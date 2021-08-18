<?php

declare(strict_types=1);

use SimpleSAML\Configuration;
use SimpleSAML\Module\totp\Totp;
use SimpleSAML\XHTML\Template;

$totp = new Totp();
$userId = $totp->getUserId();
$secret = $totp->createSecret();

$totp->storeSecret($userId, $secret);

$t = new Template(Configuration::getInstance(), 'totp:generate.php');
$t->data['qrcode'] = $totp->getQRCodeImageAsDataUri($userId, $secret);
$t->data['userId'] = $userId;
$t->show();
