<?php
require_once __DIR__.'/../../app/bootstrap.php';
header('Content-Type: application/json; charset=utf-8');
if ($_SERVER['REQUEST_METHOD'] !== 'POST') { http_response_code(405); echo json_encode(['ok'=>false,'error'=>'POST required']); exit; }
$provided = $_SERVER['HTTP_X_CRM_TOKEN'] ?? ($_POST['token'] ?? '');
if (!$provided || !hash_equals((string)$config['webhook_token'], (string)$provided)) { http_response_code(401); echo json_encode(['ok'=>false,'error'=>'Unauthorized']); exit; }
$raw=file_get_contents('php://input'); $data=json_decode($raw,true);
if (!is_array($data)) $data=$_POST;
$name=trim((string)($data['full_name'] ?? $data['name'] ?? ''));
$phone=preg_replace('/[^0-9+]/','',(string)($data['phone'] ?? $data['phone_number'] ?? ''));
if ($name==='' || strlen($phone)<8) { http_response_code(422); echo json_encode(['ok'=>false,'error'=>'full_name and valid phone are required']); exit; }
$source=(string)($data['source'] ?? 'API'); if (!in_array($source,['Meta Ads','Website','API'],true)) $source='API';
$external=trim((string)($data['external_lead_id'] ?? $data['lead_id'] ?? '')) ?: null;
if ($external) { $q=$pdo->prepare('SELECT id,lead_code FROM leads WHERE source=? AND external_lead_id=? LIMIT 1');$q->execute([$source,$external]);$existing=$q->fetch();if($existing){echo json_encode(['ok'=>true,'duplicate'=>true,'lead_id'=>(int)$existing['id'],'lead_code'=>$existing['lead_code']]);exit;} }
$projectId=(int)($data['project_id']??0) ?: null; $owner=$projectId ? project_owner($pdo,$projectId) : null;
$code='RE'.date('ym').strtoupper(bin2hex(random_bytes(3)));
$q=$pdo->prepare('INSERT INTO leads(lead_code,full_name,phone,email,project_id,assigned_to,source,campaign_name,adset_name,ad_name,form_name,budget,preferred_location,property_type,purpose,status,remarks,external_lead_id) VALUES(?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)');
$q->execute([$code,$name,$phone,trim((string)($data['email']??''))?:null,$projectId,$owner,$source,trim((string)($data['campaign_name']??''))?:null,trim((string)($data['adset_name']??''))?:null,trim((string)($data['ad_name']??''))?:null,trim((string)($data['form_name']??''))?:null,trim((string)($data['budget']??''))?:null,trim((string)($data['preferred_location']??''))?:null,trim((string)($data['property_type']??''))?:null,trim((string)($data['purpose']??''))?:null,'New',trim((string)($data['remarks']??''))?:null,$external]);
$id=(int)$pdo->lastInsertId();$a=$pdo->prepare('INSERT INTO activities(lead_id,user_id,type,note) VALUES(?,NULL,?,?)');$a->execute([$id,'Lead received via '.$source,'Imported automatically']);
echo json_encode(['ok'=>true,'duplicate'=>false,'lead_id'=>$id,'lead_code'=>$code,'assigned_user_id'=>$owner]);
