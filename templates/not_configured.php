<?php

/**
 * Template for error message users receive when they do not have a TOTP
 *  value when 2fa is enforced.
 */

use SimpleSAML\Module;

$this->data['head'] = '<link rel="stylesheet" type="text/css" href="'
    . Module::getModuleURL('totp/style.css') . '" />' . "\n";

$this->includeAtTemplateBase('includes/header.php');
?>

<h1><?php echo $this->t('{totp:totp:2fa_required}'); ?></h1>
<p><?php echo $this->t('{totp:totp:totp_not_configured}'); ?></p>
<p><?php echo $this->t('{totp:totp:ask_helpdesk}'); ?></p>

<?php
$this->includeAtTemplateBase('includes/footer.php');
