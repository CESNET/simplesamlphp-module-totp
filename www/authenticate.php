<?php

declare(strict_types=1);

/**
 * TOTP Authenticate script
 *
 * This script displays a page to the user, which requests that they submit the response from their TOTP generator.
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

if (! isset($_REQUEST['StateId'])) {
    throw new BadRequest('Missing required StateId query parameter.');
}

$id = $_REQUEST['StateId'];
$sid = State::parseStateID($id);
if ($sid['url'] !== null) {
    HTTP::checkURLAllowed($sid['url']);
}

$state = State::loadState($id, 'totp:request');

$t = new Template(Configuration::getInstance(), 'totp:authenticate.php');
$t->data['formData'] = [
    'StateId' => $id,
];
$t->data['skipRedirectUrl'] = $state['skip_redirect_url'];
$t->data['formPost'] = Module::getModuleURL('totp/authenticate.php');

if (isset($_REQUEST['skip']) && $state['skip_redirect_url'] !== null) {
    $state['Attributes']['MFA_RESULT'] = 'UnAuthenticated';
    $id = State::saveState($state, 'authSwitcher:request');
    HTTP::redirectTrustedURL($state['skip_redirect_url'], [
        'StateId' => $id,
    ]);
} elseif (isset($_REQUEST['code'])) {
    if ($totp->verifyCode($state['2fa_secrets'], $_REQUEST['code'])) {
        if ($state['skip_redirect_url'] !== null) {
            $state['Attributes']['MFA_RESULT'] = 'Authenticated';
            $id = State::saveState($state, 'authSwitcher:request');
            HTTP::redirectTrustedURL($state['skip_redirect_url'], [
                'StateId' => $id,
            ]);
        } else {
            ProcessingChain::resumeProcessing($state);
        }
    } else {
        $t->data['userError'] = $t->t('{totp:totp:invalid_code}');
    }
}

$t->show();
