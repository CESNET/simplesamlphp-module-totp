<?php

declare(strict_types=1);

use SimpleSAML\Configuration;
use SimpleSAML\XHTML\Template;

$globalConfig = Configuration::getInstance();
$t = new Template($globalConfig, 'totp:not_configured.php');
$t->show();
