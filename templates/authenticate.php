<?php declare(strict_types=1);

/**
 * Template form for submitting 2fa codes.
 */

use SimpleSAML\Module;

assert(is_array($this->data['formData']));
assert(is_string($this->data['formPost']));

$this->data['head'] = '<link rel="stylesheet" type="text/css" href="'
    . Module::getModuleURL('totp/style.css') . '" />' . "\n";

$this->includeAtTemplateBase('includes/header.php');
?>

<h1><?php echo $this->t('{totp:totp:2fa_required}'); ?></h1>
<p><?php echo $this->t('{totp:totp:2fa_required_description}'); ?></p>
<p><?php echo $this->t('{totp:totp:ask_helpdesk}'); ?></p>
<?php
if (!empty($this->data['userError'])) {
    ?>
    <div class="totp-error">
        <img src="/<?php echo $this->data['baseurlpath']; ?>resources/icons/experience/gtk-dialog-error.48x48.png"
        class="float-l erroricon" />
        <h2><?php echo $this->t('{totp:totp:authentication_error}'); ?></h2>
        <p><?php echo htmlspecialchars($this->data['userError']); ?> </p>
    </div>
    <?php
}
?>
<form action="<?php echo htmlspecialchars($this->data['formPost']); ?>">

    <?php
    // Embed hidden fields...
    foreach ($this->data['formData'] as $name => $value) {
        echo '<input type="hidden" name="' . htmlspecialchars($name) . '" value="' . htmlspecialchars($value) . '" />';
    }
    ?>

    <label for="code"><?php echo $this->t('{totp:totp:totp_code}'); ?>:</label>
    <input name="code" id="code" autocomplete="one-time-code" type="text" inputmode="numeric" pattern="[0-9]{6,}" autofocus />
    <input type="submit" value="<?php echo htmlspecialchars($this->t('{totp:totp:verify}')); ?>" />
    <?php
    if (!empty($this->data['skipRedirectUrl'])) {
        ?>
        <br>
        <label for="skip"><?php echo $this->t('{totp:totp:skip_totp}'); ?></label>
        <input type="submit" name="skip" value="<?php echo $this->t('{totp:totp:skip}'); ?>"/>
        <?php
    }
    ?>
</form>
<?php
$this->includeAtTemplateBase('includes/footer.php');
