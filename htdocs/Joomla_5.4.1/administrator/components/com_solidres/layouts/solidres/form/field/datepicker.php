<?php

/**
 ------------------------------------------------------------------------
 SOLIDRES - Accommodation booking extension for Joomla
 ------------------------------------------------------------------------
 * @author    Solidres Team <contact@solidres.com>
 * @website   https://www.solidres.com
 * @copyright Copyright (C) 2013 Solidres. All Rights Reserved.
 * @license   GNU General Public License version 3, or later
 ------------------------------------------------------------------------
 */

/*
 * This layout file can be overridden by copying to:
 *
 * /templates/TEMPLATENAME/html/layouts/com_solidres/experience/field/datepicker.php
 *
 * However, occasionally we will need to update template/layout related files and it is the template developers'
 * responsibility to update the overridden files (if any) to maintain full compatibility with Solidres.
 *
 * We do not provide support if any of the overridden files are out of date and are not compatible with Solidres.
 *
 * @version 3.2.3
 */

defined('_JEXEC') or die;

use Joomla\CMS\Language\Text;
use Joomla\CMS\HTML\HTMLHelper;

extract($displayData);
$tariffValue = !empty($tariffs) ? htmlspecialchars(json_encode($tariffs), ENT_COMPAT, 'UTF-8') : '{}';
$fromId      = $field->id . '-from';
$toId        = $field->id . '-to';
?>
<div class="inline-available-date">

    <div id="datepicker-<?php echo $field->id; ?>" class="sr-datepicker"></div>

    <input type="hidden" name="<?php echo $field->name; ?>" id="<?php echo $field->id; ?>"
           value="<?php echo htmlspecialchars($field->value, ENT_COMPAT, 'UTF-8'); ?>"/>
</div>
