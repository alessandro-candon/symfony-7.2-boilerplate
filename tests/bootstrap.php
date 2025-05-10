<?php

use Symfony\Component\Dotenv\Dotenv;

require dirname(__DIR__) . '/vendor/autoload.php';

/** @phpstan-ignore function.alreadyNarrowedType */
if (method_exists(Dotenv::class, 'bootEnv')) {
    (new Dotenv())->bootEnv(dirname(__DIR__) . '/.env');
}

if ($_SERVER['APP_DEBUG']) {
    umask(0000);
}

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, 'http://fake-gcs-server:4443/storage/v1/b');
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode(['name' => 'bucket-test']));
curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_exec($ch);
curl_setopt($ch, CURLOPT_URL, 'http://localhost:4443/storage/v1/b');
curl_exec($ch);

// if this file not exists create it : /tmp/application_default_credentials.json
if (!file_exists('/tmp/application_default_credentials.json')) {
    file_put_contents('/tmp/application_default_credentials.json', '');
}
