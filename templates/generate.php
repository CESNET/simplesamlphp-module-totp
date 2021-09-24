<?php declare(strict_types=1);

/**
 * Template for device registration.
 */

use SimpleSAML\Module;

$this->data['head'] = '<link rel="stylesheet" type="text/css" href="'
    . Module::getModuleURL('totp/style.css') . '" />' . "\n";

$this->includeAtTemplateBase('includes/header.php');
?>

<h1><?php echo $this->t('{totp:totp:totp_setup}'); ?></h1>
<p><?php echo $this->t('{totp:totp:scan_qr_code}'); ?></p>
<img src="<?php echo $this->data['qrcode']; ?>">
<p><?php echo $this->t('{totp:totp:user}', [
    '!userId' => $this->data['userId'],
]); ?></p>
<p><?php echo $this->t('{totp:totp:manual_code}'); ?></p>
<input type="text" readonly autocomplete="off" value="<?php echo $this->data['secret']; ?>" size="<?php echo strlen($this->data['secret']); ?>">

<?php
$this->includeAtTemplateBase('includes/footer.php');
