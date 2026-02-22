<?php
defined('_JEXEC') or die;

use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Router\Route;

HTMLHelper::_('behavior.keepalive');
HTMLHelper::_('behavior.formvalidator');
?>
<div class="auth-container d-flex align-items-center justify-content-center min-vh-75 py-5 bg-light">
    <div class="auth-card p-4 p-md-5 shadow-lg bg-white rounded-3 w-100" style="max-width: 500px;">
        <div class="text-center mb-4">
            <h2 class="auth-title font-serif text-resort-dark display-6 fw-bold mb-2">
                <?php echo Text::_('COM_USERS_REMIND'); ?>
            </h2>
            <p class="text-muted small text-uppercase tracking-wider">Напомнить логин</p>
        </div>

        <form action="<?php echo Route::_('index.php?option=com_users&task=remind.remind'); ?>" method="post"
            class="form-validate">

            <div class="mb-4">
                <p class="text-muted mb-4">
                    <?php echo Text::_('COM_USERS_REMIND_DEFAULT_LABEL'); ?>
                </p>
                <label for="email" class="form-label text-uppercase fw-bold text-muted small"
                    style="font-size: 0.75rem;">
                    <?php echo Text::_('JGLOBAL_EMAIL'); ?>
                </label>
                <div class="input-group">
                    <span class="input-group-text bg-white border-end-0 text-muted"><span
                            class="icon-envelope"></span></span>
                    <input type="email" name="email" id="email"
                        class="form-control border-start-0 ps-0 py-2 shadow-none" required autofocus
                        placeholder="example@mail.com">
                </div>
            </div>

            <div class="d-grid mb-4 pt-2">
                <button type="submit" class="btn btn-primary py-3 fw-bold text-uppercase rounded-2">
                    <?php echo Text::_('JSUBMIT'); ?>
                </button>
            </div>

            <div class="text-center border-top pt-4">
                <a href="<?php echo Route::_('index.php?option=com_users&view=login'); ?>"
                    class="small text-resort-green text-decoration-none fw-bold">
                    <span class="icon-arrow-left small"></span>
                    <?php echo Text::_('JLOGIN'); ?>
                </a>
            </div>

            <?php echo HTMLHelper::_('form.token'); ?>
        </form>
    </div>
</div>