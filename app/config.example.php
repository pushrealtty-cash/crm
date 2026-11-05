<?php
// Copy to config.php and fill in your hosting/database details.
return [
  'db' => [
    'host' => 'localhost',
    'name' => 'YOUR_DATABASE',
    'user' => 'YOUR_DB_USER',
    'pass' => 'YOUR_DB_PASSWORD',
    'charset' => 'utf8mb4',
  ],
  'app_url' => 'https://crm.yourdomain.com',
  // Generate a long random value (at least 32 characters). Used by website and automation webhooks.
  'webhook_token' => 'CHANGE_THIS_TO_A_LONG_RANDOM_SECRET',
  'timezone' => 'Asia/Kolkata',
];
