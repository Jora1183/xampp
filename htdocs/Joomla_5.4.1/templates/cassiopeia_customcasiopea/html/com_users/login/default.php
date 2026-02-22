<?php
defined('_JEXEC') or die;

use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Router\Route;

HTMLHelper::_('behavior.keepalive');
HTMLHelper::_('behavior.formvalidator');

// Get the return URL
$return = $this->form->getValue('return', '', $this->params->get('login_redirect_url', $this->params->get('menu-meta_description')));
?>
<div class="auth-container d-flex align-items-center justify-content-center min-vh-75 py-5 bg-light">
    <div class="auth-card p-4 p-md-5 shadow-lg bg-white rounded-3">
        <div class="text-center mb-4">
            <h2 class="auth-title font-serif text-resort-dark display-6 fw-bold mb-2">
                <?php echo Text::_('JLOGIN'); ?>
            </h2>
            <p class="text-muted small text-uppercase tracking-wider">С возвращением</p>
        </div>

        <form action="<?php echo Route::_('index.php?option=com_users&task=user.login'); ?>" method="post"
            class="form-validate">

            <div class="mb-3">
                <label for="username" class="form-label text-uppercase fw-bold text-muted small"
                    style="font-size: 0.75rem;">
                    <?php echo Text::_('JGLOBAL_USERNAME'); ?>
                </label>
                <div class="input-group">
                    <span class="input-group-text bg-white border-end-0 text-muted"><span
                            class="icon-user"></span></span>
                    <input type="text" name="username" id="username"
                        class="form-control border-start-0 ps-0 py-2 shadow-none" required autofocus
                        placeholder="<?php echo Text::_('JGLOBAL_USERNAME'); ?>">
                </div>
            </div>

            <div class="mb-4">
                <label for="password" class="form-label text-uppercase fw-bold text-muted small"
                    style="font-size: 0.75rem;">
                    <?php echo Text::_('JGLOBAL_PASSWORD'); ?>
                </label>
                <div class="input-group">
                    <span class="input-group-text bg-white border-end-0 text-muted"><span
                            class="icon-lock"></span></span>
                    <input type="password" name="password" id="password"
                        class="form-control border-start-0 ps-0 py-2 shadow-none" required placeholder="********">
                </div>
            </div>

            <div class="d-flex justify-content-between align-items-center mb-4">
                <div class="form-check">
                    <input type="checkbox" name="remember" class="form-check-input" id="remember" value="yes">
                    <label class="form-check-label text-muted small" for="remember">
                        <?php echo Text::_('JGLOBAL_REMEMBER_ME'); ?>
                    </label>
                </div>
                <div class="text-end">
                    <a href="<?php echo Route::_('index.php?option=com_users&view=reset'); ?>"
                        class="small text-resort-green text-decoration-none fw-bold d-block">
                        <?php echo Text::_('COM_USERS_LOGIN_RESET'); ?>
                    </a>
                    <a href="<?php echo Route::_('index.php?option=com_users&view=remind'); ?>"
                        class="small text-muted text-decoration-none d-block mt-1">
                        <?php echo Text::_('COM_USERS_LOGIN_REMIND'); ?>
                    </a>
                </div>
            </div>

            <div class="d-grid mb-4">
                <button type="submit" class="btn btn-primary py-3 fw-bold text-uppercase rounded-2 login-btn">
                    <?php echo Text::_('JLOGIN'); ?>
                </button>
            </div>

            <div class="text-center border-top pt-4">
                <a href="<?php echo Route::_('index.php?option=com_users&view=registration'); ?>"
                    class="btn btn-outline-secondary btn-sm w-100 fw-bold">
                    <?php echo Text::_('COM_USERS_LOGIN_REGISTER'); ?>
                </a>
            </div>

            <input type="hidden" name="return" value="<?php echo base64_encode($return); ?>" />
            <?php echo HTMLHelper::_('form.token'); ?>
        </form>
    </div>
</div>