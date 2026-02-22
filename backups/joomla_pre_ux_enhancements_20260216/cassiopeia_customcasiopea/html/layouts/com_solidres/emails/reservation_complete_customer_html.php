<?php
/**
 * Custom Email Template for Solidres Reservation Confirmation
 * 
 * Override for: /components/com_solidres/layouts/emails/reservation_complete_customer_html.php
 * Template: cassiopeia_customcasiopea
 * 
 * @version 1.0.0
 */

use Joomla\CMS\Language\Text;
use Solidres\Media\ImageUploaderHelper;

defined('_JEXEC') or die;

echo SRLayoutHelper::render('emails.header', $displayData);

extract($displayData);

?>

<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>

<body
    style="margin: 0; padding: 0; background-color: #f4f7fa; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;">

    <!-- Main Container -->
    <table width="100%" cellpadding="0" cellspacing="0" border="0" style="background-color: #f4f7fa; padding: 20px 0;">
        <tr>
            <td align="center">
                <table width="600" cellpadding="0" cellspacing="0" border="0"
                    style="background-color: #ffffff; border-radius: 12px; overflow: hidden; box-shadow: 0 4px 20px rgba(0,0,0,0.08);">

                    <!-- Header Section -->
                    <tr>
                        <td
                            style="background: linear-gradient(135deg, #1e3a5f 0%, #2d5a87 100%); padding: 30px 40px; text-align: center;">
                            <?php
                            $assetLogo = $asset->params['logo'] ?? '';
                            $assetId = $asset->id;
                            if ($assetLogo && is_file(ImageUploaderHelper::getUploadPath() . '/p/' . $assetId . '/' . $assetLogo)): ?>
                                <img src="<?php echo ImageUploaderHelper::getImage('p/' . $assetId . '/' . $assetLogo) ?>"
                                    alt="logo" style="max-height: 60px; margin-bottom: 15px;" />
                            <?php endif ?>
                            <h1
                                style="color: #ffffff; margin: 0; font-size: 24px; font-weight: 600; letter-spacing: 0.5px;">
                                <?php echo Text::_('SR_EMAIL_CONFIRM_RESERVATION') ?>
                            </h1>
                            <p style="color: #a8c5e8; margin: 10px 0 0 0; font-size: 16px;">
                                <?php echo Text::sprintf('SR_EMAIL_REF_ID', '<strong style="color: #ffffff;">' . $reservation->code . '</strong>') ?>
                            </p>
                        </td>
                    </tr>

                    <!-- Greeting Section -->
                    <tr>
                        <td style="padding: 40px 40px 20px 40px;">
                            <h2 style="color: #1e3a5f; margin: 0 0 15px 0; font-size: 22px;">
                                <?php echo Text::sprintf('SR_EMAIL_GREETING_NAME', $reservation->customer_firstname, $reservation->customer_middlename, $reservation->customer_lastname) ?>
                            </h2>
                            <p style="color: #5a6a7a; font-size: 15px; line-height: 1.6; margin: 0;">
                                <?php
                                if (is_array($greetingText)) {
                                    echo call_user_func_array(Text::class . '::sprintf', $greetingText);
                                } else {
                                    echo $greetingText;
                                }
                                ?>
                            </p>
                        </td>
                    </tr>

                    <!-- Check-in/Check-out Cards -->
                    <tr>
                        <td style="padding: 0 40px 30px 40px;">
                            <table width="100%" cellpadding="0" cellspacing="0" border="0">
                                <tr>
                                    <td width="48%"
                                        style="background-color: #e8f4ec; border-radius: 10px; padding: 20px; text-align: center;">
                                        <p
                                            style="color: #2d7a4a; margin: 0 0 5px 0; font-size: 12px; text-transform: uppercase; letter-spacing: 1px; font-weight: 600;">
                                            <?php echo Text::_('SR_CHECKIN') ?>
                                        </p>
                                        <p style="color: #1e3a5f; margin: 0; font-size: 18px; font-weight: 700;">
                                            <?php echo JDate::getInstance($reservation->checkin, $timezone)->format($dateFormat, true) ?>
                                        </p>
                                    </td>
                                    <td width="4%"></td>
                                    <td width="48%"
                                        style="background-color: #fef3e8; border-radius: 10px; padding: 20px; text-align: center;">
                                        <p
                                            style="color: #c45a20; margin: 0 0 5px 0; font-size: 12px; text-transform: uppercase; letter-spacing: 1px; font-weight: 600;">
                                            <?php echo Text::_('SR_CHECKOUT') ?>
                                        </p>
                                        <p style="color: #1e3a5f; margin: 0; font-size: 18px; font-weight: 700;">
                                            <?php echo JDate::getInstance($reservation->checkout, $timezone)->format($dateFormat, true) ?>
                                        </p>
                                    </td>
                                </tr>
                            </table>
                            <p style="text-align: center; color: #5a6a7a; margin: 15px 0 0 0; font-size: 14px;">
                                <?php echo Text::_('SR_EMAIL_LENGTH_OF_STAY') ?>:
                                <strong>
                                    <?php
                                    if ($reservation->booking_type == 0):
                                        echo Text::plural('SR_NIGHTS', $stayLength);
                                    else:
                                        echo Text::plural('SR_DAYS', $stayLength + 1);
                                    endif;
                                    ?>
                                </strong>
                            </p>
                        </td>
                    </tr>

                    <!-- Reservation Details Section -->
                    <tr>
                        <td style="padding: 0 40px 30px 40px;">
                            <table width="100%" cellpadding="0" cellspacing="0" border="0"
                                style="background-color: #f8fafc; border-radius: 10px; overflow: hidden;">
                                <tr>
                                    <td colspan="2" style="background-color: #1e3a5f; padding: 15px 20px;">
                                        <h3 style="color: #ffffff; margin: 0; font-size: 16px; font-weight: 600;">
                                            <?php echo Text::_("SR_GENERAL_INFO") ?>
                                        </h3>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="padding: 20px; vertical-align: top;" width="50%">
                                        <p style="margin: 0 0 10px 0; color: #5a6a7a; font-size: 14px;">
                                            <strong
                                                style="color: #1e3a5f;"><?php echo Text::_('SR_EMAIL_PAYMENT_METHOD') ?></strong><br>
                                            <?php echo Text::_($paymentMethodLabel) ?>
                                        </p>
                                        <p style="margin: 0 0 10px 0; color: #5a6a7a; font-size: 14px;">
                                            <strong
                                                style="color: #1e3a5f;"><?php echo Text::_('SR_EMAIL_EMAIL') ?></strong><br>
                                            <?php echo $reservation->customer_email ?>
                                        </p>
                                        <?php if (!empty($reservation->coupon_code)): ?>
                                            <p style="margin: 0 0 10px 0; color: #5a6a7a; font-size: 14px;">
                                                <strong
                                                    style="color: #1e3a5f;"><?php echo Text::_('SR_EMAIL_COUPON_CODE') ?></strong><br>
                                                <?php echo $reservation->coupon_code ?>
                                            </p>
                                        <?php endif ?>
                                    </td>
                                    <td style="padding: 20px; vertical-align: top;" width="50%">
                                        <p style="margin: 0 0 10px 0; color: #5a6a7a; font-size: 14px;">
                                            <strong
                                                style="color: #1e3a5f;"><?php echo Text::_('SR_EMAIL_SUB_TOTAL') ?></strong><br>
                                            <?php echo $subTotal->format() ?>
                                        </p>
                                        <?php if ($discountPreTax && !is_null($totalDiscount)): ?>
                                            <p style="margin: 0 0 10px 0; color: #2d7a4a; font-size: 14px;">
                                                <strong><?php echo Text::_('SR_EMAIL_TOTAL_DISCOUNT') ?></strong>
                                                -<?php echo $totalDiscount ?>
                                            </p>
                                        <?php endif; ?>
                                        <p style="margin: 0 0 10px 0; color: #5a6a7a; font-size: 14px;">
                                            <strong
                                                style="color: #1e3a5f;"><?php echo Text::_('SR_EMAIL_TAX') ?></strong><br>
                                            <?php echo $tax->format() ?>
                                        </p>
                                        <?php if (!$discountPreTax && !is_null($totalDiscount)): ?>
                                            <p style="margin: 0 0 10px 0; color: #2d7a4a; font-size: 14px;">
                                                <strong><?php echo Text::_('SR_EMAIL_TOTAL_DISCOUNT') ?></strong>
                                                -<?php echo $totalDiscount ?>
                                            </p>
                                        <?php endif; ?>
                                        <?php if ($enableTouristTax): ?>
                                            <p style="margin: 0 0 10px 0; color: #5a6a7a; font-size: 14px;">
                                                <strong
                                                    style="color: #1e3a5f;"><?php echo Text::_('SR_EMAIL_TOURIST_TAX') ?></strong><br>
                                                <?php echo $touristTax; ?>
                                            </p>
                                        <?php endif ?>
                                        <?php if ($reservation->total_fee > 0): ?>
                                            <p style="margin: 0 0 10px 0; color: #5a6a7a; font-size: 14px;">
                                                <strong
                                                    style="color: #1e3a5f;"><?php echo Text::_('SR_TOTAL_FEE_AMOUNT') ?></strong><br>
                                                <?php echo $totalFee->format(); ?>
                                            </p>
                                        <?php endif ?>
                                    </td>
                                </tr>
                                <!-- Grand Total Row -->
                                <tr>
                                    <td colspan="2"
                                        style="background-color: #1e3a5f; padding: 20px; text-align: center;">
                                        <p
                                            style="color: #a8c5e8; margin: 0 0 5px 0; font-size: 12px; text-transform: uppercase; letter-spacing: 1px;">
                                            <?php echo Text::_('SR_EMAIL_GRAND_TOTAL') ?>
                                        </p>
                                        <p style="color: #ffffff; margin: 0; font-size: 28px; font-weight: 700;">
                                            <?php echo $grandTotal ?>
                                        </p>
                                    </td>
                                </tr>
                                <!-- Payment Summary -->
                                <tr>
                                    <td colspan="2" style="padding: 15px 20px; background-color: #edf2f7;">
                                        <table width="100%" cellpadding="0" cellspacing="0" border="0">
                                            <tr>
                                                <td style="color: #5a6a7a; font-size: 13px;">
                                                    <?php echo Text::_('SR_EMAIL_DEPOSIT_AMOUNT') ?>
                                                </td>
                                                <td
                                                    style="text-align: right; color: #1e3a5f; font-weight: 600; font-size: 13px;">
                                                    <?php echo $depositAmount ?>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td style="color: #5a6a7a; font-size: 13px; padding-top: 8px;">
                                                    <?php echo Text::_('SR_EMAIL_TOTAL_PAID') ?>
                                                </td>
                                                <td
                                                    style="text-align: right; color: #2d7a4a; font-weight: 600; font-size: 13px; padding-top: 8px;">
                                                    <?php echo $totalPaid ?>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td style="color: #5a6a7a; font-size: 13px; padding-top: 8px;">
                                                    <?php echo Text::_('SR_EMAIL_DUE_AMOUNT') ?>
                                                </td>
                                                <td
                                                    style="text-align: right; color: #c45a20; font-weight: 600; font-size: 13px; padding-top: 8px;">
                                                    <?php echo $totalDue ?>
                                                </td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    <!-- Guest Information Section -->
                    <tr>
                        <td style="padding: 0 40px 30px 40px;">
                            <h3
                                style="color: #1e3a5f; margin: 0 0 15px 0; font-size: 18px; border-bottom: 2px solid #e2e8f0; padding-bottom: 10px;">
                                <?php echo Text::_('SR_GUEST_INFO'); ?>
                            </h3>
                            <?php echo SRLayoutHelper::render('emails.customer_fields', $displayData, false); ?>
                        </td>
                    </tr>

                    <!-- Bank Wire Instructions (if applicable) -->
                    <?php if (!empty($bankwireInstructions)): ?>
                        <tr>
                            <td style="padding: 0 40px 30px 40px;">
                                <table width="100%" cellpadding="0" cellspacing="0" border="0"
                                    style="background-color: #fff8e8; border-radius: 10px; border-left: 4px solid #f5a623;">
                                    <tr>
                                        <td style="padding: 20px;">
                                            <h4 style="color: #c45a20; margin: 0 0 10px 0; font-size: 16px;">
                                                <?php echo Text::_("SR_EMAIL_BANKWIRE_INFO") ?>
                                            </h4>
                                            <p style="color: #5a6a7a; margin: 0 0 5px 0; font-size: 14px;">
                                                <?php echo $bankwireInstructions['account_name']; ?>
                                            </p>
                                            <p style="color: #5a6a7a; margin: 0; font-size: 14px;">
                                                <?php echo $bankwireInstructions['account_details']; ?>
                                            </p>
                                        </td>
                                    </tr>
                                </table>
                            </td>
                        </tr>
                    <?php endif ?>

                    <!-- Room Details Section -->
                    <tr>
                        <td style="padding: 0 40px 30px 40px;">
                            <h3
                                style="color: #1e3a5f; margin: 0 0 15px 0; font-size: 18px; border-bottom: 2px solid #e2e8f0; padding-bottom: 10px;">
                                <?php echo Text::_("SR_ROOM_EXTRA_INFO") ?>
                            </h3>

                            <?php foreach ($reservation->reserved_room_details as $room): ?>
                                <table width="100%" cellpadding="0" cellspacing="0" border="0"
                                    style="background-color: #f8fafc; border-radius: 10px; margin-bottom: 15px;">
                                    <tr>
                                        <td
                                            style="padding: 15px 20px; background-color: #2d5a87; border-radius: 10px 10px 0 0;">
                                            <h4 style="color: #ffffff; margin: 0; font-size: 16px;">
                                                <?php echo $room->room_type_name ?>
                                            </h4>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td style="padding: 20px;">
                                            <?php if (isset($room->guest_fullname) && !empty($room->guest_fullname)): ?>
                                                <p style="margin: 0 0 10px 0; color: #5a6a7a; font-size: 14px;">
                                                    <strong
                                                        style="color: #1e3a5f;"><?php echo Text::_("SR_GUEST_FULLNAME") ?>:</strong>
                                                    <?php echo $room->guest_fullname ?>
                                                </p>
                                            <?php endif ?>
                                            <p style="margin: 0 0 10px 0; color: #5a6a7a; font-size: 14px;">
                                                <strong
                                                    style="color: #1e3a5f;"><?php echo Text::_("SR_ADULT_NUMBER") ?>:</strong>
                                                <?php echo $room->adults_number ?>
                                            </p>
                                            <?php if ($room->children_number > 0): ?>
                                                <p style="margin: 0 0 10px 0; color: #5a6a7a; font-size: 14px;">
                                                    <strong
                                                        style="color: #1e3a5f;"><?php echo Text::_("SR_CHILDREN_NUMBER") ?>:</strong>
                                                    <?php echo $room->children_number ?>
                                                </p>
                                            <?php endif ?>

                                            <?php if (isset($room->extras) && is_array($room->extras)): ?>
                                                <p
                                                    style="margin: 15px 0 10px 0; color: #1e3a5f; font-size: 14px; font-weight: 600;">
                                                    <?php echo Text::_('SR_EMAIL_EXTRAS_ITEMS') ?>
                                                </p>
                                                <?php foreach ($room->extras as $extra): ?>
                                                    <p
                                                        style="margin: 0 0 5px 0; color: #5a6a7a; font-size: 13px; padding-left: 15px;">
                                                        • <?php echo $extra->extra_name ?> (x<?php echo $extra->extra_quantity ?>) -
                                                        <?php
                                                        $roomExtraCurrency = clone $baseCurrency;
                                                        $roomExtraCurrency->setValue($extra->extra_price);
                                                        echo $roomExtraCurrency->format();
                                                        ?>
                                                    </p>
                                                <?php endforeach; ?>
                                            <?php endif; ?>

                                            <?php
                                            if (isset($roomFields[$room->id])) {
                                                echo SRLayoutHelper::render('emails.room_fields', ['roomFields' => $roomFields[$room->id], 'roomExtras' => isset($room->extras) ? $room->extras : []]);
                                            }
                                            ?>
                                        </td>
                                    </tr>
                                </table>
                            <?php endforeach; ?>
                        </td>
                    </tr>

                    <!-- Per-Booking Extras -->
                    <?php if (isset($reservation->extras) && is_array($reservation->extras) && count($reservation->extras) > 0): ?>
                        <tr>
                            <td style="padding: 0 40px 30px 40px;">
                                <h3
                                    style="color: #1e3a5f; margin: 0 0 15px 0; font-size: 18px; border-bottom: 2px solid #e2e8f0; padding-bottom: 10px;">
                                    <?php echo Text::_("SR_EMAIL_OTHER_INFO") ?>
                                </h3>
                                <table width="100%" cellpadding="0" cellspacing="0" border="0"
                                    style="background-color: #f8fafc; border-radius: 10px;">
                                    <tr>
                                        <td style="padding: 20px;">
                                            <?php foreach ($reservation->extras as $extra): ?>
                                                <p style="margin: 0 0 10px 0; color: #5a6a7a; font-size: 14px;">
                                                    <strong style="color: #1e3a5f;"><?php echo $extra->extra_name ?></strong>
                                                    (x<?php echo $extra->extra_quantity ?>) -
                                                    <?php
                                                    $bookingExtraCurrency = clone $baseCurrency;
                                                    $bookingExtraCurrency->setValue($extra->extra_price);
                                                    echo $bookingExtraCurrency->format();
                                                    ?>
                                                </p>
                                            <?php endforeach; ?>
                                        </td>
                                    </tr>
                                </table>
                            </td>
                        </tr>
                    <?php endif; ?>

                    <!-- Footer Section -->
                    <tr>
                        <td style="background-color: #1e3a5f; padding: 30px 40px;">
                            <table width="100%" cellpadding="0" cellspacing="0" border="0">
                                <tr>
                                    <td width="50%" style="vertical-align: top;">
                                        <h4 style="color: #ffffff; margin: 0 0 15px 0; font-size: 14px;">
                                            <?php echo Text::_('SR_EMAIL_CONTACT_INFO') ?>
                                        </h4>
                                        <p style="color: #a8c5e8; margin: 0 0 8px 0; font-size: 13px;">
                                            📍
                                            <?php echo $asset->address_1 . ', ' . $asset->city . ', ' . (!empty($asset->geostate_code_2) ? $asset->geostate_code_2 . ' ' : '') . $asset->postcode ?>
                                        </p>
                                        <p style="color: #a8c5e8; margin: 0 0 8px 0; font-size: 13px;">
                                            📞 <?php echo $asset->phone ?>
                                        </p>
                                        <p style="color: #a8c5e8; margin: 0; font-size: 13px;">
                                            ✉️ <a href="mailto:<?php echo $asset->email ?>"
                                                style="color: #a8c5e8;"><?php echo $asset->email ?></a>
                                        </p>
                                    </td>
                                    <td width="50%" style="vertical-align: top; text-align: right;">
                                        <h4 style="color: #ffffff; margin: 0 0 15px 0; font-size: 14px;">
                                            <?php echo Text::_('SR_EMAIL_CONNECT_WITH_US') ?>
                                        </h4>
                                        <?php if (!empty($asset->reservationasset_extra_fields['facebook_link']) && $asset->reservationasset_extra_fields['facebook_show'] == 1): ?>
                                            <a href="<?php echo $asset->reservationasset_extra_fields['facebook_link'] ?>"
                                                style="display: inline-block; background-color: #3b5998; color: #ffffff; padding: 8px 15px; border-radius: 5px; text-decoration: none; font-size: 12px; margin: 0 0 8px 8px;">
                                                Facebook
                                            </a>
                                        <?php endif; ?>
                                        <?php if (!empty($asset->reservationasset_extra_fields['twitter_link']) && $asset->reservationasset_extra_fields['twitter_show'] == 1): ?>
                                            <a href="<?php echo $asset->reservationasset_extra_fields['twitter_link'] ?>"
                                                style="display: inline-block; background-color: #1da1f2; color: #ffffff; padding: 8px 15px; border-radius: 5px; text-decoration: none; font-size: 12px; margin: 0 0 8px 8px;">
                                                Twitter
                                            </a>
                                        <?php endif; ?>
                                        <?php if (!empty($asset->reservationasset_extra_fields['youtube_link']) && $asset->reservationasset_extra_fields['youtube_show'] == 1): ?>
                                            <a href="<?php echo $asset->reservationasset_extra_fields['youtube_link'] ?>"
                                                style="display: inline-block; background-color: #ff0000; color: #ffffff; padding: 8px 15px; border-radius: 5px; text-decoration: none; font-size: 12px; margin: 0 0 8px 8px;">
                                                YouTube
                                            </a>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    <!-- Copyright -->
                    <tr>
                        <td style="background-color: #162d4a; padding: 15px 40px; text-align: center;">
                            <p style="color: #7a8fa8; margin: 0; font-size: 12px;">
                                © <?php echo date('Y'); ?> <?php echo $asset->name ?>. All rights reserved.
                            </p>
                        </td>
                    </tr>

                </table>
            </td>
        </tr>
    </table>

</body>

</html>
<?php
echo SRLayoutHelper::render('emails.footer');
