<?php

use Phalcon\Loader;
use Phalcon\Mvc\Micro;
use Phalcon\Di\FactoryDefault;
use Phalcon\Security\JWT\Builder;
use Phalcon\Security\JWT\Signer\Hmac;
use Phalcon\Security\JWT\Token\Parser;

session_start();

$loader = new Loader();
$loader->registerNamespaces(
    [
        'MyApp\Models' => __DIR__ . '/models/',
    ]
);

$loader->register();

$container = new FactoryDefault();

$app = new Micro($container);

$app->get(
    '/get/token',
    function () {
        $strresult = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz';
        $_SESSION['token'] = substr(str_shuffle($strresult), 0, 10);
        $signer  = new Hmac();
        $builder = new Builder($signer);
        $passphrase = 'QcMpZ&b&mo3TPsPk668J6QH8JA$&U&m2';
        $builder
            ->setSubject($_SESSION['token'])
            ->setPassphrase($passphrase);
        $token = $builder->getToken();
        echo $token->getToken();
    }
);

$app->get(
    '/get/auth',
    function () {
        $token = $_GET['token'];
        $parser = new Parser();
        $tokenObject = $parser->parse($token);
        $role = $tokenObject->getclaims()->getpayload()['sub'];
        if ($role === $_SESSION['token']) {
            echo "<h3>Verified User</h3>";
        } else {
            echo "Unauthorised User Detected";
        }
    }
);


$app->handle(
    $_SERVER["REQUEST_URI"]
);
