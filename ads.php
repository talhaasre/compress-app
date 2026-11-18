<?php
// Served as /ads.txt (see .htaccess). Lists the AdSense publisher once it is set in inc/config.php.
$config = require __DIR__ . '/inc/config.php';
header('Content-Type: text/plain; charset=utf-8');
$client = trim($config['adsense_client']);
if ($client === '') {
    http_response_code(404);
    exit;
}
echo 'google.com, ' . preg_replace('/^ca-/', '', $client) . ", DIRECT, f08c47fec0942fa0\n";
