<?php
function e($v): string { return htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8'); }
function redirect(string $url): never { header('Location: '.$url); exit; }
function csrf_token(): string { if (empty($_SESSION['_csrf'])) $_SESSION['_csrf'] = bin2hex(random_bytes(32)); return $_SESSION['_csrf']; }
function verify_csrf(): void { if (!hash_equals($_SESSION['_csrf'] ?? '', $_POST['_csrf'] ?? '')) { http_response_code(419); exit('Session expired. Go back and try again.'); } }
function user(): ?array { return $_SESSION['user'] ?? null; }
function require_login(): void { if (!user()) redirect('?page=login'); }
function is_admin(): bool { return (user()['role'] ?? '') === 'admin'; }
function flash(string $msg, string $type='success'): void { $_SESSION['_flash']=['msg'=>$msg,'type'=>$type]; }
function take_flash(): ?array { $f=$_SESSION['_flash']??null; unset($_SESSION['_flash']); return $f; }
function log_activity(PDO $pdo, int $leadId, string $type, string $note=''): void { $s=$pdo->prepare('INSERT INTO activities (lead_id,user_id,type,note) VALUES (?,?,?,?)'); $s->execute([$leadId,user()['id']??null,$type,$note]); }
function project_owner(PDO $pdo, int $projectId): ?int { $s=$pdo->prepare('SELECT owner_user_id FROM projects WHERE id=? AND is_active=1'); $s->execute([$projectId]); $v=$s->fetchColumn(); return $v ? (int)$v : null; }
function status_options(): array { return ['New','Attempted Contact','Contacted','Interested','Follow-up Required','Site Visit Scheduled','Site Visit Completed','Negotiation','Booking Confirmed','Lost / Not Interested']; }
function badge_class(string $s): string { return match($s) { 'New'=>'blue','Interested','Booking Confirmed'=>'green','Follow-up Required','Attempted Contact'=>'amber','Lost / Not Interested'=>'red','Site Visit Scheduled','Site Visit Completed'=>'purple','Negotiation'=>'orange',default=>'gray' }; }
function app_url(array $config): string { return rtrim($config['app_url'] ?? '', '/'); }
