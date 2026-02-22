<?php
defined('_JEXEC') or die;

use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Router\Route;

HTMLHelper::_('behavior.keepalive');

// Ensure Bootstrap dropdown JS is loaded for the login button
HTMLHelper::_('bootstrap.dropdown');
?>
<div class="header-login-dropdown dropdown">
    <!-- Dropdown Trigger Button -->
    <button class="btn btn-outline-secondary account-dropdown-toggle d-flex align-items-center gap-2" type="button"
        id="loginDropdownBtn-<?php echo $module->id; ?>" data-bs-toggle="dropdown" data-bs-display="static"
        aria-expanded="false" data-purpose="login-trigger">
        <span class="icon-user" aria-hidden="true"></span>
        <span class="d-none d-lg-inline"><?php echo Text::_('JLOGIN'); ?></span>
    </button>

    <!-- Dropdown Menu / Login Card -->
    <div class="dropdown-menu dropdown-menu-end p-4 shadow-xl border-0 login-card-dropdown"
        aria-labelledby="loginDropdownBtn-<?php echo $module->id; ?>">
        <form id="login-form-<?php echo $module->id; ?>" class="mod-login"
            action="<?php echo Route::_('index.php', true); ?>" method="post">

            <h5 class="mb-3 font-serif font-bold text-resort-dark"><?php echo Text::_('JLOGIN'); ?></h5>

            <!-- Username -->
            <div class="mb-3">
                <label for="modlgn-username-<?php echo $module->id; ?>"
                    class="form-label small fw-bold text-muted uppercase tracking-wider">
                    <?php echo Text::_('MOD_LOGIN_VALUE_USERNAME'); ?>
                </label>
                <div class="input-group">
                    <span class="input-group-text bg-light border-end-0">
                        <span class="icon-user" aria-hidden="true"></span>
                    </span>
                    <input id="modlgn-username-<?php echo $module->id; ?>" type="text" name="username"
                        class="form-control border-start-0" autocomplete="username" placeholder="Имя пользователя">
                </div>
            </div>

            <!-- Password -->
            <div class="mb-3">
                <label for="modlgn-passwd-<?php echo $module->id; ?>"
                    class="form-label small fw-bold text-muted uppercase tracking-wider">
                    <?php echo Text::_('JGLOBAL_PASSWORD'); ?>
                </label>
                <div class="input-group">
                    <span class="input-group-text bg-light border-end-0">
                        <span class="icon-lock" aria-hidden="true"></span>
                    </span>
                    <input id="modlgn-passwd-<?php echo $module->id; ?>" type="password" name="password"
                        class="form-control border-start-0" autocomplete="current-password" placeholder="********">
                </div>
            </div>

            <!-- Remember Me -->
            <?php if (Joomla\CMS\Plugin\PluginHelper::isEnabled('system', 'remember')): ?>
                <div class="mb-3 form-check">
                    <input id="modlgn-remember-<?php echo $module->id; ?>" type="checkbox" name="remember"
                        class="form-check-input" value="yes">
                    <label for="modlgn-remember-<?php echo $module->id; ?>" class="form-check-label small text-muted">
                        <?php echo Text::_('MOD_LOGIN_COLUMN_REMEMBER'); ?>
                    </label>
                </div>
            <?php endif; ?>

            <!-- Login Button -->
            <div class="d-grid gap-2 mb-3">
                <button type="submit" name="Submit" class="btn btn-primary">
                    <?php echo Text::_('JLOGIN'); ?>
                </button>
            </div>

            <div class="text-center">
                <a href="<?php echo Route::_('index.php?option=com_users&view=registration'); ?>"
                    class="small text-resort-green hover-underline">
                    <?php echo Text::_('JREGISTER'); ?>
                </a>
                <span class="mx-1 text-muted">|</span>
                <a href="<?php echo Route::_('index.php?option=com_users&view=remind'); ?>"
                    class="small text-muted hover-underline">
                    Забыли имя пользователя?
                </a>
            </div>

            <input type="hidden" name="option" value="com_users">
            <input type="hidden" name="task" value="user.login">
            <input type="hidden" name="return" value="<?php echo $return; ?>">
            <?php echo HTMLHelper::_('form.token'); ?>
        </form>
    </div>
</div>