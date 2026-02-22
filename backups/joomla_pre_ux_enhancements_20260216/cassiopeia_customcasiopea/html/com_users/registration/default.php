<?php
defined('_JEXEC') or die;

use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Router\Route;

HTMLHelper::_('behavior.keepalive');
HTMLHelper::_('behavior.formvalidator');
?>
<div class="auth-container d-flex align-items-center justify-content-center min-vh-75 py-5 bg-light">
    <div class="auth-card p-4 p-md-5 shadow-lg bg-white rounded-3 w-100" style="max-width: 550px;">
        <div class="text-center mb-4">
            <h2 class="auth-title font-serif text-resort-dark display-6 fw-bold mb-2">
                <?php echo Text::_('COM_USERS_REGISTRATION'); ?>
            </h2>
            <p class="text-muted small text-uppercase tracking-wider">Join Divine Estate</p>
        </div>

        <form id="member-registration"
            action="<?php echo Route::_('index.php?option=com_users&task=registration.register'); ?>" method="post"
            class="form-validate form-horizontal" enctype="multipart/form-data">

            <?php foreach ($this->form->getFieldsets() as $fieldset): // Iterate through fieldsets ?>
                <?php $fields = $this->form->getFieldset($fieldset->name); ?>
                <?php if (count($fields)): ?>
                    <div class="fieldset-wrapper mb-3">
                        <?php if (isset($fieldset->label) && ($fieldset->label != '')): ?>
                            <h5 class="mb-3 text-resort-green border-bottom pb-2">
                                <?php echo Text::_($fieldset->label); ?>
                            </h5>
                        <?php endif; ?>

                        <?php foreach ($fields as $field): // Iterate through fields ?>
                            <?php if ($field->hidden):  // If the field is hidden, just render the input. ?>
                                <?php echo $field->input; ?>
                            <?php else: ?>
                                <div class="mb-3 row">
                                    <div class="col-12">
                                        <?php echo str_replace('class="', 'class="form-label text-uppercase fw-bold text-muted small fs-7 ', $field->label); ?>
                                    </div>
                                    <div class="col-12">
                                        <!-- Add custom classes to input by targeting standard Joomla render output if simpler, otherwise just render -->
                                        <?php echo str_replace('class="', 'class="form-control py-2 shadow-none ', $field->input); ?>
                                        <?php if (!$field->required && $field->type != 'Spacer'): ?>
                                            <span class="optional small text-muted">
                                                <?php echo Text::_('COM_USERS_OPTIONAL'); ?>
                                            </span>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            <?php endforeach; ?>

            <div class="d-grid mb-4 pt-2">
                <button type="submit" class="btn btn-primary py-3 fw-bold text-uppercase rounded-2 register-btn">
                    <?php echo Text::_('JREGISTER'); ?>
                </button>
            </div>

            <div class="text-center pt-2">
                <p class="mb-0 text-muted small">
                    Already have an account?
                    <a href="<?php echo Route::_('index.php?option=com_users&view=login'); ?>"
                        class="fw-bold text-resort-green text-decoration-none">
                        <?php echo Text::_('JLOGIN'); ?>
                    </a>
                </p>
            </div>

            <?php echo HTMLHelper::_('form.token'); ?>
        </form>
    </div>
</div>