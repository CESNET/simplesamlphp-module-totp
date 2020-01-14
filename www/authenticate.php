<?php

/**
 * TOTP Authenticate script
 *
 * This script displays a page to the user, which requests that they
 * submit the response from their TOTP generator.
 */

use SimpleSAML\Auth\ProcessingChain;
use SimpleSAML\Auth\State;
use SimpleSAML\Configuration;
use SimpleSAML\Error\BadRequest;
use SimpleSAML\Module;
use SimpleSAML\Module\totp\Totp;
use SimpleSAML\Utils\HTTP;
use SimpleSAML\XHTML\Template;

$totp = new Totp();

if (!isset($_REQUEST['StateId'])) {
    throw new BadRequest(
        'Missing required StateId query parameter.'
    );
}

$id = $_REQUEST['StateId'];

$sid = State::parseStateID($id);
if ($sid['url'] !== null) {
    HTTP::checkURLAllowed($sid['url']);
}

$state = State::loadState($id, 'totp:request');

$t = new Template(Configuration::getInstance(), 'totp:authenticate.php');
$t->data['formData'] = ['StateId' => $id];
$t->data['formPost'] = Module::getModuleURL('totp/authenticate.php');

if (isset($_REQUEST['code'])) {
    if ($totp->verifyCode($state['2fa_secret'], $_REQUEST['code'])) {
        ProcessingChain::resumeProcessing($state);
    } else {
        $t->data['userError'] = $t->t('{totp:totp:invalid_code}');
    }
}

$t->show();
