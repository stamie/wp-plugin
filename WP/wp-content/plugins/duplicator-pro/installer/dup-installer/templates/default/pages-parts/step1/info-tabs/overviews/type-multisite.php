<?php
/**
 *
 * @package templates/default
 *
 */
defined('ABSPATH') || defined('DUPXABSPATH') || exit;

if (DUPX_InstallerState::instTypeAvaiable(DUPX_InstallerState::INSTALL_MULTISITE_SUBDOMAIN)) {
    $instTypeClass = 'install-type-' . DUPX_InstallerState::INSTALL_MULTISITE_SUBDOMAIN;
    $title         = 'Install subdomain multisite';
} elseif (DUPX_InstallerState::instTypeAvaiable(DUPX_InstallerState::INSTALL_MULTISITE_SUBFOLDER)) {
    $instTypeClass = 'install-type-' . DUPX_InstallerState::INSTALL_MULTISITE_SUBFOLDER;
    $title         = 'Install subfolder multisite';
} else {
    return;
}

$overwriteMode = (DUPX_InstallerState::getInstance()->getMode() === DUPX_InstallerState::MODE_OVR_INSTALL);
$display       = DUPX_InstallerState::getInstance()->isInstType(
    array(
        DUPX_InstallerState::INSTALL_MULTISITE_SUBDOMAIN,
        DUPX_InstallerState::INSTALL_MULTISITE_SUBFOLDER
    )
);
$display       = (!DUPX_InstallerState::isRestoreBackup() && $display);
?>
<div class="overview-description <?php echo $instTypeClass . ($display ? '' : ' no-display'); ?>">
    <h2><?php echo $title; ?></h2>
    <p>
        Multisite installation, all sites in the netowork will be extracted and installed.
        <?php if ($overwriteMode) { ?>
            <br>
            By running this installation all the site data will be lost and the current package will be installed.
        </p>
        <p>
            <b>Continuing, it will no longer be possible to go back.</b>
        <?php } ?>
    </p>
</div>