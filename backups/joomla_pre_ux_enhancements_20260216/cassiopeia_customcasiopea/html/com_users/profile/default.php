<?php
defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Router\Route;

HTMLHelper::_('behavior.keepalive');

$user = Factory::getUser();
?>
<div class="container-component py-5">
    <div class="row">
        <!-- Sidebar -->
        <div class="col-lg-4 mb-4">
            <div class="profile-sidebar shadow-sm">
                <div class="p-4 text-center border-bottom bg-light">
                    <div class="avatar-circle bg-white shadow-sm d-inline-flex align-items-center justify-content-center mb-3 rounded-circle" style="width: 80px; height: 80px;">
                        <span class="icon-user display-6 text-muted"></span>
                    </div>
                    <h5 class="mb-1 fw-bold"><?php echo $this->escape($user->name); ?></h5>
                    <p class="text-muted small mb-0"><?php echo $this->escape($user->username); ?></p>
                </div>
                <nav class="profile-menu nav flex-column">
                    <a class="nav-link active" href="<?php echo Route::_('index.php?option=com_users&view=profile'); ?>">
                        <span class="icon-user" aria-hidden="true"></span> <?php echo Text::_('COM_USERS_PROFILE_CORE_LEGEND'); ?>
                    </a>
                    <a class="nav-link" href="<?php echo Route::_('index.php?option=com_users&view=profile&layout=edit'); ?>">
                        <span class="icon-pencil" aria-hidden="true"></span> <?php echo Text::_('COM_USERS_EDIT_PROFILE'); ?>
                    </a>
                    <a class="nav-link" href="<?php echo Route::_('index.php?option=com_solidres&view=reservation&layout=history'); ?>">
                        <span class="icon-calendar" aria-hidden="true"></span> Мои бронирования
                    </a>
                    <a class="nav-link text-danger" href="<?php echo Route::_('index.php?option=com_users&task=user.logout&' . JSession::getFormToken() . '=1'); ?>">
                        <span class="icon-power" aria-hidden="true"></span> <?php echo Text::_('JLOGOUT'); ?>
                    </a>
                </nav>
            </div>
        </div>

        <!-- Main Content -->
        <div class="col-lg-8">
            <div class="profile-card shadow-sm">
                <div class="d-flex justify-content-between align-items-center mb-4 pb-3 border-bottom">
                    <h3 class="font-serif fw-bold mb-0 text-resort-dark">Мой профиль</h3>
                    <a href="<?php echo Route::_('index.php?option=com_users&view=profile&layout=edit'); ?>" class="btn btn-outline-primary btn-sm rounded-pill px-3">
                        Редактировать
                    </a>
                </div>

                <!-- User Info Grid -->
                <?php if ($this->data) : ?>
                   <div class="row g-4">
                        <div class="col-md-6">
                            <div class="p-3 bg-light rounded-3 h-100">
                                <label class="text-uppercase text-muted extra-small fw-bold mb-1" style="font-size:0.75rem">Имя</label>
                                <div class="fw-bold fs-5 text-dark"><?php echo $this->escape($this->data->name); ?></div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="p-3 bg-light rounded-3 h-100">
                                <label class="text-uppercase text-muted extra-small fw-bold mb-1" style="font-size:0.75rem">Логин</label>
                                <div class="fw-bold fs-5 text-dark"><?php echo $this->escape($this->data->username); ?></div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="p-3 bg-light rounded-3 h-100">
                                <label class="text-uppercase text-muted extra-small fw-bold mb-1" style="font-size:0.75rem">Эл. почта</label>
                                <div class="fw-bold fs-5 text-dark"><?php echo $this->escape($this->data->email); ?></div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="p-3 bg-light rounded-3 h-100">
                                <label class="text-uppercase text-muted extra-small fw-bold mb-1" style="font-size:0.75rem">Дата регистрации</label>
                                <div class="fw-bold fs-5 text-dark"><?php echo HTMLHelper::_('date', $this->data->registerDate, Text::_('DATE_FORMAT_LC1')); ?></div>
                            </div>
                        </div>
                   </div>
                <?php endif; ?>
                
                <div class="mt-5">
                    <h5 class="fw-bold mb-3 border-bottom pb-2">Последние действия</h5>
                    <div class="alert alert-light border border-dashed text-center py-4">
                        <p class="mb-2 text-muted">Просматривайте историю бронирований и управляйте своими заказами.</p>
                        <a href="<?php echo Route::_('index.php?option=com_solidres&view=reservation&layout=history'); ?>" class="btn btn-sm btn-primary">
                            Мои бронирования
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
