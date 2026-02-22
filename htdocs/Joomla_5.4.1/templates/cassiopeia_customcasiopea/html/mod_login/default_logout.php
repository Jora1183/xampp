<?php
defined('_JEXEC') or die;

use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Router\Route;

HTMLHelper::_('behavior.keepalive');

// Ensure Bootstrap dropdown JS is loaded for the account dropdown
HTMLHelper::_('bootstrap.dropdown');
?>
<?php if ($params->get('greeting', 1)): ?>
    <div class="dropdown account-dropdown">
        <button class="btn btn-light border account-dropdown-toggle d-flex align-items-center gap-2" type="button"
            id="userMenuDropdown" data-bs-toggle="dropdown" aria-expanded="false">
            <span class="account-icon-wrapper rounded-circle d-flex align-items-center justify-content-center">
                <span class="icon-user text-resort-green"></span>
            </span>
            <span class="fw-bold text-dark d-none d-sm-inline">
                <?php if (!$params->get('name', 0)): ?>
                    <?php echo htmlspecialchars($user->name, ENT_COMPAT, 'UTF-8'); ?>
                <?php else: ?>
                    <?php echo htmlspecialchars($user->username, ENT_COMPAT, 'UTF-8'); ?>
                <?php endif; ?>
            </span>
            <span class="icon-arrow-down-3 text-muted small ms-1"></span>
        </button>
        <ul class="dropdown-menu dropdown-menu-end shadow-lg border-0 mt-2 p-2 rounded-3" aria-labelledby="userMenuDropdown"
            style="min-width: 220px;">
            <li>
                <div class="dropdown-header text-uppercase fw-bold text-muted small py-2 px-3">
                    <?php echo Text::sprintf('MOD_LOGIN_HINAME', htmlspecialchars($user->name, ENT_COMPAT, 'UTF-8')); ?>
                </div>
            </li>
            <li>
                <a class="dropdown-item gap-3 d-flex align-items-center py-2 px-3 rounded-2"
                    href="<?php echo Route::_('index.php?option=com_users&view=profile'); ?>">
                    <span class="icon-user text-muted"></span>
                    <span class="flex-grow-1"><?php echo Text::_('MOD_LOGIN_PROFILE'); ?></span>
                </a>
            </li>
            <li>
                <a class="dropdown-item gap-3 d-flex align-items-center py-2 px-3 rounded-2"
                    href="<?php echo Route::_('index.php?option=com_solidres&view=reservation&layout=history'); ?>">
                    <span class="icon-calendar text-muted"></span>
                    <span class="flex-grow-1">Мои бронирования</span>
                </a>
            </li>
            <li>
                <hr class="dropdown-divider my-2 opacity-50">
            </li>
            <li>
                <form action="<?php echo Route::_('index.php', true); ?>" method="post" style="margin:0;">
                    <input type="hidden" name="option" value="com_users">
                    <input type="hidden" name="task" value="user.logout">
                    <input type="hidden" name="return" value="<?php echo $return; ?>">
                    <?php echo HTMLHelper::_('form.token'); ?>
                    <button type="submit"
                        class="dropdown-item text-danger gap-3 d-flex align-items-center py-2 px-3 rounded-2">
                        <span class="icon-power"></span>
                        <span class="flex-grow-1"><?php echo Text::_('JLOGOUT'); ?></span>
                    </button>
                </form>
            </li>
        </ul>
    </div>
<?php else: ?>
    <form class="mod-login-logout" action="<?php echo Route::_('index.php', true); ?>" method="post">
        <button type="submit" name="Submit" class="btn btn-primary d-flex align-items-center justify-content-center gap-2"
            style="height: 48px;">
            <span class="icon-power-off" aria-hidden="true"></span> <?php echo Text::_('JLOGOUT'); ?>
        </button>
        <input type="hidden" name="option" value="com_users">
        <input type="hidden" name="task" value="user.logout">
        <input type="hidden" name="return" value="<?php echo $return; ?>">
        <?php echo HTMLHelper::_('form.token'); ?>
    </form>
<?php endif; ?>