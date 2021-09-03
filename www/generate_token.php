<?php

declare(strict_types=1);

use SimpleSAML\Configuration;
use SimpleSAML\Module\totp\Totp;
use SimpleSAML\XHTML\Template;

session_start();

if (! isset($_SESSION['qrcode']) && ! isset($_SESSION['userId'])) {
    $totp = new Totp();
    $userId = $totp->getUserId();
    $secret = $totp->createSecret();
    $totp->storeSecret($userId, $secret);
    $_SESSION['qrcode'] = $totp->getQRCodeImageAsDataUri($userId, $secret);
    $_SESSION['userId'] = $userId;
}

$t = new Template(Configuration::getInstance(), 'totp:generate.php');
$t->data['qrcode'] = $_SESSION['qrcode'];
$t->data['userId'] = $_SESSION['userId'];
$t->show();
