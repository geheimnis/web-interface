<?php
$_CONFIGS['security'] = array(
    'local_login_auth'=>'',
    'cookie'=>array(
        'sign_key'=>'TEST-ONLY-COOKIE-SIGN-KEY',
        'HMAC_algorithm'=>'whirlpool',
        'life'=>86400,
    ),
    'session'=>array(
        'id_hash_algorithm'=>'sha1',
        'sign_key'=>'TEST-ONLY-SESSION-SIGN-KEY',
        'life'=>1800,
    ),
);
