<?php
/**
 * Custom Booking Confirmation Page for Solidres
 * 
 * Override for: /components/com_solidres/views/reservation/tmpl/final.php
 * Template: cassiopeia_customcasiopea
 * Design: Modern Park-Hotel style
 * 
 * @version 1.0.0
 */

use Joomla\CMS\Language\Text;
use Joomla\CMS\Uri\Uri;
use Joomla\CMS\Router\Route;
use Joomla\CMS\HTML\HTMLHelper;

defined('_JEXEC') or die;

// Load the currency class
JLoader::register('SRCurrency', SRPATH_LIBRARY . '/currency/currency.php');

// Get reservation data from user state
$reservationCodeUserState = $this->app->getUserState($this->context . '.code', '');
$isNew = $this->app->getUserState($this->context . '.is_new', true);
$paymentMethodMessage = $this->app->getUserState($this->context . '.payment_method_message');
$bookingRequireApproval = $this->app->getUserState($this->context . '.booking_require_approval');
$customerFirstName = $this->app->getUserState($this->context . '.customer_firstname');
$customerLastName = $this->app->getUserState($this->context . '.customer_lastname', '');
$customerEmail = $this->app->getUserState($this->context . '.customeremail');
$assetName = $this->app->getUserState($this->context . '.reservation_asset_name');

// Check if we have reservation data
$hasReservationObject = isset($this->reservation);

// Get currency if reservation exists
if ($hasReservationObject) {
    $baseCurrency = new SRCurrency($this->reservation->total_price_tax_incl - $this->reservation->total_discount, $this->reservation->currency_id);
    $lengthOfStay = $this->lengthOfStay ?? 1;
}

// Date formatting
$dateFormat = 'D, M d';
$timeCheckin = '3:00 PM';
$timeCheckout = '11:00 AM';

?>

<!DOCTYPE html>
<html lang="<?php echo $this->document->getLanguage(); ?>">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://fonts.googleapis.com/css2?family=Be+Vietnam+Pro:wght@400;500;600;700;800&display=swap"
        rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap"
        rel="stylesheet">
    <style>
        :root {
            --primary: #2a6e54;
            --primary-dark: #1e4f3b;
            --primary-light: #e9f2ef;
            --background-light: #f8faf9;
            --surface-light: #ffffff;
            --text-main: #111718;
            --text-sub: #5a7175;
            --border-light: #e5e7eb;
            --success-bg: #dcfce7;
            --success: #16a34a;
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        .sr-confirmation {
            font-family: 'Be Vietnam Pro', sans-serif;
            background: var(--background-light);
            color: var(--text-main);
            min-height: 100vh;
            padding: 2rem 1rem;
        }

        .sr-confirmation-container {
            max-width: 1200px;
            margin: 0 auto;
        }

        /* Header Success Section */
        .sr-success-header {
            text-align: center;
            margin-bottom: 3rem;
            max-width: 700px;
            margin-left: auto;
            margin-right: auto;
        }

        .sr-success-icon {
            width: 80px;
            height: 80px;
            background: var(--success-bg);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1.5rem;
            box-shadow: 0 0 0 8px rgba(34, 197, 94, 0.1);
        }

        .sr-success-icon .material-symbols-outlined {
            font-size: 40px;
            color: var(--success);
        }

        .sr-success-title {
            font-size: 2.5rem;
            font-weight: 700;
            margin-bottom: 1rem;
            letter-spacing: -0.02em;
        }

        .sr-success-subtitle {
            color: var(--text-sub);
            font-size: 1.1rem;
            line-height: 1.7;
            margin-bottom: 2rem;
        }

        /* Reservation ID Box */
        .sr-reservation-id-box {
            display: inline-flex;
            flex-wrap: wrap;
            align-items: center;
            justify-content: center;
            gap: 1rem;
            background: var(--surface-light);
            padding: 0.75rem 0.75rem 0.75rem 1.5rem;
            border-radius: 1rem;
            box-shadow: 0 4px 20px -2px rgba(0, 0, 0, 0.05);
            border: 1px solid var(--border-light);
        }

        .sr-reservation-id-label {
            text-transform: uppercase;
            font-size: 0.65rem;
            font-weight: 700;
            letter-spacing: 0.1em;
            color: var(--text-sub);
        }

        .sr-reservation-id-value {
            font-size: 1.5rem;
            font-weight: 700;
            font-family: monospace;
            letter-spacing: 0.1em;
        }

        .sr-copy-btn {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            background: rgba(42, 110, 84, 0.05);
            color: var(--primary);
            padding: 0.75rem 1.25rem;
            border-radius: 0.75rem;
            border: none;
            cursor: pointer;
            font-weight: 600;
            font-size: 0.875rem;
            transition: background 0.2s;
        }

        .sr-copy-btn:hover {
            background: rgba(42, 110, 84, 0.1);
        }

        /* Grid Layout */
        .sr-grid {
            display: grid;
            grid-template-columns: 1fr;
            gap: 1.5rem;
        }

        @media (min-width: 1024px) {
            .sr-grid {
                grid-template-columns: 2fr 1fr;
                align-items: start;
            }
        }

        /* Cards */
        .sr-card {
            background: var(--surface-light);
            border-radius: 1.5rem;
            box-shadow: 0 0 0 1px rgba(0, 0, 0, 0.03), 0 2px 8px rgba(0, 0, 0, 0.04);
            padding: 1.5rem;
            margin-bottom: 1.5rem;
        }

        .sr-card-header {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            margin-bottom: 1.5rem;
        }

        .sr-card-title {
            font-size: 1.25rem;
            font-weight: 700;
        }

        .sr-card-title .material-symbols-outlined {
            color: var(--primary);
        }

        /* Accommodation Card */
        .sr-accommodation-card {
            display: flex;
            flex-direction: column;
            padding: 0;
            overflow: hidden;
        }

        @media (min-width: 768px) {
            .sr-accommodation-card {
                flex-direction: row;
            }
        }

        .sr-accommodation-image {
            width: 100%;
            height: 200px;
            background: linear-gradient(135deg, var(--primary-light) 0%, var(--primary) 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
        }

        @media (min-width: 768px) {
            .sr-accommodation-image {
                width: 40%;
                height: auto;
                min-height: 280px;
            }
        }

        .sr-accommodation-image .material-symbols-outlined {
            font-size: 64px;
            color: white;
            opacity: 0.8;
        }

        .sr-accommodation-badge {
            position: absolute;
            bottom: 1rem;
            left: 1rem;
            background: white;
            padding: 0.5rem 1rem;
            border-radius: 0.5rem;
            font-size: 0.75rem;
            font-weight: 700;
            color: var(--primary);
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .sr-accommodation-content {
            padding: 1.5rem;
            flex: 1;
        }

        .sr-accommodation-label {
            display: inline-block;
            background: rgba(42, 110, 84, 0.1);
            color: var(--primary);
            font-size: 0.7rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            padding: 0.25rem 0.5rem;
            border-radius: 0.25rem;
            margin-bottom: 0.5rem;
        }

        .sr-accommodation-name {
            font-size: 1.5rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
        }

        .sr-accommodation-location {
            color: var(--text-sub);
            font-size: 0.875rem;
            display: flex;
            align-items: center;
            gap: 0.25rem;
            margin-bottom: 1.5rem;
        }

        /* Date Cards */
        .sr-date-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1rem;
            margin-bottom: 1.5rem;
        }

        .sr-date-card {
            background: var(--background-light);
            padding: 1rem;
            border-radius: 1rem;
            border: 1px solid var(--border-light);
        }

        .sr-date-card-header {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            color: var(--text-sub);
            margin-bottom: 0.5rem;
        }

        .sr-date-card-header span {
            font-size: 0.65rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }

        .sr-date-card-header .material-symbols-outlined {
            font-size: 18px;
        }

        .sr-date-value {
            font-size: 1.1rem;
            font-weight: 700;
        }

        .sr-date-time {
            font-size: 0.875rem;
            color: var(--text-sub);
        }

        /* Tags */
        .sr-tags {
            display: flex;
            flex-wrap: wrap;
            gap: 0.5rem;
            padding-top: 1.5rem;
            border-top: 1px solid var(--border-light);
        }

        .sr-tag {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            background: rgba(0, 0, 0, 0.02);
            padding: 0.5rem 1rem;
            border-radius: 2rem;
            font-size: 0.875rem;
            font-weight: 500;
            color: var(--text-sub);
            border: 1px solid var(--border-light);
        }

        .sr-tag .material-symbols-outlined {
            font-size: 18px;
        }

        /* Guest Details */
        .sr-guest-grid {
            background: var(--background-light);
            padding: 1.5rem;
            border-radius: 1rem;
            border: 1px solid var(--border-light);
        }

        .sr-guest-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1.5rem 3rem;
        }

        .sr-guest-label {
            font-size: 0.65rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.1em;
            color: var(--text-sub);
            margin-bottom: 0.25rem;
        }

        .sr-guest-value {
            font-weight: 600;
            font-size: 1rem;
        }

        /* Payment Summary */
        .sr-payment-card {
            position: sticky;
            top: 1.5rem;
        }

        .sr-payment-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 1.5rem;
        }

        .sr-payment-title {
            font-size: 1.1rem;
            font-weight: 700;
        }

        .sr-status-badge {
            display: inline-flex;
            align-items: center;
            gap: 0.25rem;
            background: var(--success);
            color: white;
            font-size: 0.6rem;
            font-weight: 700;
            padding: 0.375rem 0.75rem;
            border-radius: 2rem;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }

        .sr-status-badge .material-symbols-outlined {
            font-size: 12px;
        }

        .sr-payment-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 0.5rem 0;
        }

        .sr-payment-label {
            color: var(--text-sub);
            font-size: 0.875rem;
        }

        .sr-payment-value {
            font-weight: 500;
        }

        .sr-payment-total-row {
            border-top: 2px dashed var(--border-light);
            margin-top: 1rem;
            padding-top: 1.25rem;
        }

        .sr-payment-total-label {
            font-weight: 700;
        }

        .sr-payment-total-value {
            font-size: 1.75rem;
            font-weight: 800;
            color: var(--primary);
        }

        /* Buttons */
        .sr-btn-group {
            margin-top: 2rem;
            display: flex;
            flex-direction: column;
            gap: 0.75rem;
        }

        .sr-btn {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            padding: 0.875rem 1.5rem;
            border-radius: 0.75rem;
            font-weight: 700;
            font-size: 0.875rem;
            cursor: pointer;
            border: none;
            text-decoration: none;
            transition: all 0.2s;
        }

        .sr-btn-primary {
            background: var(--primary);
            color: white;
            box-shadow: 0 4px 12px rgba(42, 110, 84, 0.25);
        }

        .sr-btn-primary:hover {
            background: var(--primary-dark);
            transform: translateY(-2px);
        }

        .sr-btn-secondary {
            background: white;
            color: var(--text-main);
            border: 1px solid var(--border-light);
        }

        .sr-btn-secondary:hover {
            background: var(--background-light);
        }

        /* Concierge Box */
        .sr-concierge-box {
            background: linear-gradient(135deg, rgba(42, 110, 84, 0.05), rgba(42, 110, 84, 0.1));
            border-radius: 1.5rem;
            padding: 1.5rem;
            border: 1px solid rgba(42, 110, 84, 0.1);
            display: flex;
            gap: 1rem;
            margin-top: 1.5rem;
        }

        .sr-concierge-icon {
            width: 40px;
            height: 40px;
            background: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
        }

        .sr-concierge-icon .material-symbols-outlined {
            color: var(--primary);
        }

        .sr-concierge-title {
            font-weight: 700;
            margin-bottom: 0.25rem;
        }

        .sr-concierge-text {
            font-size: 0.75rem;
            color: var(--text-sub);
            line-height: 1.5;
            margin-bottom: 0.75rem;
        }

        .sr-concierge-link {
            font-size: 0.75rem;
            font-weight: 700;
            color: var(--primary);
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 0.25rem;
        }

        .sr-concierge-link:hover {
            color: var(--primary-dark);
        }

        /* Back link */
        .sr-back-link {
            text-align: center;
            margin-top: 1.5rem;
        }

        .sr-back-link a {
            color: var(--text-sub);
            text-decoration: none;
            font-size: 0.875rem;
            font-weight: 500;
        }

        .sr-back-link a:hover {
            color: var(--primary);
        }

        /* Approval Message */
        .sr-approval-message {
            background: #fef3c7;
            border: 1px solid #fcd34d;
            border-radius: 1rem;
            padding: 1.5rem;
            margin-bottom: 2rem;
            text-align: center;
        }

        .sr-approval-message .material-symbols-outlined {
            font-size: 40px;
            color: #d97706;
            margin-bottom: 0.5rem;
        }

        @media print {
            .no-print {
                display: none !important;
            }

            .sr-confirmation {
                background: white;
                padding: 0;
            }

            .sr-card {
                box-shadow: none;
                border: 1px solid #ddd;
            }
        }
    </style>
</head>

<body>
    <div class="sr-confirmation" id="solidres">
        <div class="sr-confirmation-container">

            <?php if (!$hasReservationObject && !empty($reservationCodeUserState)): ?>
                <?php if (!empty($paymentMethodMessage) || $bookingRequireApproval): ?>
                    <!-- Approval Required or Payment Message -->
                    <div class="sr-success-header">
                        <div class="sr-success-icon"
                            style="background: #fef3c7; box-shadow: 0 0 0 8px rgba(251, 191, 36, 0.1);">
                            <span class="material-symbols-outlined" style="color: #d97706;">schedule</span>
                        </div>
                        <h1 class="sr-success-title"><?php echo Text::_('SR_BOOKING_PENDING'); ?></h1>
                        <div class="sr-approval-message">
                            <?php echo $paymentMethodMessage; ?>
                        </div>
                    </div>
                <?php else: ?>
                    <!-- Success Header -->
                    <div class="sr-success-header">
                        <div class="sr-success-icon">
                            <span class="material-symbols-outlined"
                                style="font-variation-settings: 'FILL' 1;">check_circle</span>
                        </div>
                        <h1 class="sr-success-title"><?php echo Text::_('SR_BOOKING_CONFIRMED'); ?></h1>
                        <p class="sr-success-subtitle">
                            <?php echo Text::sprintf(
                                'SR_BOOKING_CONFIRMED_MESSAGE',
                                $customerFirstName,
                                '<strong>' . $assetName . '</strong>',
                                '<strong>' . $customerEmail . '</strong>'
                            ); ?>
                        </p>

                        <!-- Reservation ID Box -->
                        <div class="sr-reservation-id-box">
                            <div style="text-align: left;">
                                <div class="sr-reservation-id-label"><?php echo Text::_('SR_RESERVATION_ID'); ?></div>
                                <div class="sr-reservation-id-value"><?php echo $reservationCodeUserState; ?></div>
                            </div>
                            <button class="sr-copy-btn no-print"
                                onclick="navigator.clipboard.writeText('<?php echo $reservationCodeUserState; ?>')">
                                <span class="material-symbols-outlined">content_copy</span>
                                <span><?php echo Text::_('SR_COPY'); ?></span>
                            </button>
                        </div>
                    </div>

                    <!-- Action Buttons for Simple View -->
                    <div style="text-align: center; margin-bottom: 2rem;" class="no-print">
                        <a href="<?php echo Uri::root(); ?>" class="sr-btn sr-btn-primary" style="display: inline-flex;">
                            <span class="material-symbols-outlined">home</span>
                            <span><?php echo Text::_('SR_BACK_TO_HOME'); ?></span>
                        </a>
                    </div>
                <?php endif; ?>

                <?php
                // Clear session data
                $this->app->setUserState($this->context . '.payment_method_message', null);
                $this->app->setUserState($this->context . '.payment_method_custom_email_content', null);
                ?>

            <?php elseif ($hasReservationObject): ?>
                <!-- Full Confirmation with Reservation Details -->

                <!-- Success Header -->
                <div class="sr-success-header">
                    <div class="sr-success-icon">
                        <span class="material-symbols-outlined"
                            style="font-variation-settings: 'FILL' 1;">check_circle</span>
                    </div>
                    <h1 class="sr-success-title"><?php echo Text::_('SR_BOOKING_CONFIRMED'); ?></h1>
                    <p class="sr-success-subtitle">
                        <?php echo Text::sprintf(
                            'SR_BOOKING_CONFIRMED_FULL_MESSAGE',
                            '<strong>' . $this->asset->name . '</strong>',
                            '<strong>' . $this->reservation->customer_email . '</strong>'
                        ); ?>
                    </p>

                    <!-- Reservation ID Box -->
                    <div class="sr-reservation-id-box">
                        <div style="text-align: left;">
                            <div class="sr-reservation-id-label"><?php echo Text::_('SR_RESERVATION_ID'); ?></div>
                            <div class="sr-reservation-id-value"><?php echo $this->reservation->code; ?></div>
                        </div>
                        <button class="sr-copy-btn no-print"
                            onclick="navigator.clipboard.writeText('<?php echo $this->reservation->code; ?>')">
                            <span class="material-symbols-outlined">content_copy</span>
                            <span><?php echo Text::_('SR_COPY'); ?></span>
                        </button>
                    </div>
                </div>

                <div class="sr-grid">
                    <!-- Left Column -->
                    <div>
                        <!-- Accommodation Card -->
                        <div class="sr-card sr-accommodation-card">
                            <div class="sr-accommodation-image">
                                <span class="material-symbols-outlined">hotel</span>
                                <div class="sr-accommodation-badge">
                                    <span class="material-symbols-outlined"
                                        style="font-size: 14px; font-variation-settings: 'FILL' 1;">star</span>
                                    <?php echo Text::_('SR_CONFIRMED'); ?>
                                </div>
                            </div>
                            <div class="sr-accommodation-content">
                                <span class="sr-accommodation-label"><?php echo Text::_('SR_ACCOMMODATION'); ?></span>
                                <h2 class="sr-accommodation-name"><?php echo $this->asset->name; ?></h2>
                                <div class="sr-accommodation-location">
                                    <span class="material-symbols-outlined">location_on</span>
                                    <?php echo $this->asset->address_1; ?>
                                    <?php if (!empty($this->asset->city))
                                        echo ', ' . $this->asset->city; ?>
                                    <?php if (!empty($this->asset->country_name))
                                        echo ', ' . $this->asset->country_name; ?>
                                </div>

                                <!-- Check-in/out Date Cards -->
                                <div class="sr-date-grid">
                                    <div class="sr-date-card">
                                        <div class="sr-date-card-header">
                                            <span class="material-symbols-outlined">login</span>
                                            <span><?php echo Text::_('SR_CHECKIN'); ?></span>
                                        </div>
                                        <div class="sr-date-value">
                                            <?php echo HTMLHelper::_('date', $this->reservation->checkin, $dateFormat); ?>
                                        </div>
                                        <div class="sr-date-time"><?php echo Text::_('SR_FROM'); ?>
                                            <?php echo $timeCheckin; ?></div>
                                    </div>
                                    <div class="sr-date-card">
                                        <div class="sr-date-card-header">
                                            <span class="material-symbols-outlined">logout</span>
                                            <span><?php echo Text::_('SR_CHECKOUT'); ?></span>
                                        </div>
                                        <div class="sr-date-value">
                                            <?php echo HTMLHelper::_('date', $this->reservation->checkout, $dateFormat); ?>
                                        </div>
                                        <div class="sr-date-time"><?php echo Text::_('SR_BY'); ?>
                                            <?php echo $timeCheckout; ?></div>
                                    </div>
                                </div>

                                <!-- Tags -->
                                <div class="sr-tags">
                                    <?php
                                    $totalGuests = 0;
                                    $totalRooms = count($this->reservation->reserved_room_details);
                                    foreach ($this->reservation->reserved_room_details as $room) {
                                        $totalGuests += $room->adults_number + $room->children_number;
                                    }
                                    ?>
                                    <span class="sr-tag">
                                        <span class="material-symbols-outlined">group</span>
                                        <?php echo Text::plural('SR_GUESTS', $totalGuests); ?>
                                    </span>
                                    <span class="sr-tag">
                                        <span class="material-symbols-outlined">bed</span>
                                        <?php echo Text::plural('SR_ROOMS', $totalRooms); ?>
                                    </span>
                                    <span class="sr-tag">
                                        <span class="material-symbols-outlined">dark_mode</span>
                                        <?php
                                        if ($this->reservation->booking_type == 0) {
                                            echo Text::plural('SR_NIGHTS', $lengthOfStay);
                                        } else {
                                            echo Text::plural('SR_DAYS', $lengthOfStay + 1);
                                        }
                                        ?>
                                    </span>
                                </div>
                            </div>
                        </div>

                        <!-- Guest Details Card -->
                        <div class="sr-card">
                            <div class="sr-card-header">
                                <span class="material-symbols-outlined">person</span>
                                <h3 class="sr-card-title"><?php echo Text::_('SR_GUEST_DETAILS'); ?></h3>
                            </div>
                            <div class="sr-guest-grid">
                                <div class="sr-guest-row">
                                    <div>
                                        <div class="sr-guest-label"><?php echo Text::_('SR_PRIMARY_GUEST'); ?></div>
                                        <div class="sr-guest-value">
                                            <?php echo $this->reservation->customer_firstname . ' ' . $this->reservation->customer_lastname; ?>
                                        </div>
                                    </div>
                                    <div>
                                        <div class="sr-guest-label"><?php echo Text::_('JGLOBAL_EMAIL'); ?></div>
                                        <div class="sr-guest-value"><?php echo $this->reservation->customer_email; ?></div>
                                    </div>
                                    <?php if (!empty($this->reservation->customer_phonenumber)): ?>
                                        <div>
                                            <div class="sr-guest-label"><?php echo Text::_('SR_PHONE'); ?></div>
                                            <div class="sr-guest-value"><?php echo $this->reservation->customer_phonenumber; ?>
                                            </div>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>

                        <!-- Room Details Card -->
                        <div class="sr-card">
                            <div class="sr-card-header">
                                <span class="material-symbols-outlined">meeting_room</span>
                                <h3 class="sr-card-title"><?php echo Text::_('SR_ROOM_DETAILS'); ?></h3>
                            </div>
                            <?php foreach ($this->reservation->reserved_room_details as $room): ?>
                                <div class="sr-guest-grid" style="margin-bottom: 1rem;">
                                    <div class="sr-guest-row">
                                        <div>
                                            <div class="sr-guest-label"><?php echo Text::_('SR_ROOM_TYPE'); ?></div>
                                            <div class="sr-guest-value"><?php echo $room->room_type_name; ?></div>
                                        </div>
                                        <div>
                                            <div class="sr-guest-label"><?php echo Text::_('SR_GUESTS'); ?></div>
                                            <div class="sr-guest-value">
                                                <?php echo Text::plural('SR_ADULTS', $room->adults_number); ?>
                                                <?php if ($room->children_number > 0): ?>
                                                    , <?php echo Text::plural('SR_CHILDREN', $room->children_number); ?>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                        <?php if (!empty($room->guest_fullname)): ?>
                                            <div>
                                                <div class="sr-guest-label"><?php echo Text::_('SR_GUEST_NAME'); ?></div>
                                                <div class="sr-guest-value"><?php echo $room->guest_fullname; ?></div>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <!-- Right Column - Payment Summary -->
                    <div>
                        <div class="sr-card sr-payment-card">
                            <div class="sr-payment-header">
                                <h3 class="sr-payment-title"><?php echo Text::_('SR_PAYMENT_SUMMARY'); ?></h3>
                                <span class="sr-status-badge">
                                    <span class="material-symbols-outlined">check</span>
                                    <?php echo Text::_('SR_CONFIRMED'); ?>
                                </span>
                            </div>

                            <div class="sr-payment-row">
                                <span class="sr-payment-label">
                                    <?php echo Text::plural('SR_NIGHTS', $lengthOfStay); ?>
                                </span>
                                <span class="sr-payment-value">
                                    <?php
                                    $subtotal = clone $baseCurrency;
                                    $subtotal->setValue($this->reservation->total_price_tax_excl);
                                    echo $subtotal->format();
                                    ?>
                                </span>
                            </div>

                            <?php if ($this->reservation->tax_amount > 0): ?>
                                <div class="sr-payment-row">
                                    <span class="sr-payment-label"><?php echo Text::_('SR_TAX'); ?></span>
                                    <span class="sr-payment-value">
                                        <?php
                                        $taxCurrency = clone $baseCurrency;
                                        $taxCurrency->setValue($this->reservation->tax_amount);
                                        echo $taxCurrency->format();
                                        ?>
                                    </span>
                                </div>
                            <?php endif; ?>

                            <?php if ($this->reservation->total_discount > 0): ?>
                                <div class="sr-payment-row">
                                    <span class="sr-payment-label"
                                        style="color: var(--success);"><?php echo Text::_('SR_DISCOUNT'); ?></span>
                                    <span class="sr-payment-value" style="color: var(--success);">
                                        -<?php
                                        $discountCurrency = clone $baseCurrency;
                                        $discountCurrency->setValue($this->reservation->total_discount);
                                        echo $discountCurrency->format();
                                        ?>
                                    </span>
                                </div>
                            <?php endif; ?>

                            <div class="sr-payment-row sr-payment-total-row">
                                <span class="sr-payment-total-label"><?php echo Text::_('SR_TOTAL'); ?></span>
                                <span class="sr-payment-total-value"><?php echo $baseCurrency->format(); ?></span>
                            </div>

                            <!-- Action Buttons -->
                            <div class="sr-btn-group no-print">
                                <button class="sr-btn sr-btn-primary" onclick="window.print()">
                                    <span class="material-symbols-outlined">print</span>
                                    <span><?php echo Text::_('SR_PRINT_CONFIRMATION'); ?></span>
                                </button>
                                <a href="<?php echo Uri::root(); ?>" class="sr-btn sr-btn-secondary">
                                    <span class="material-symbols-outlined">home</span>
                                    <span><?php echo Text::_('SR_BACK_TO_HOME'); ?></span>
                                </a>
                            </div>
                        </div>

                        <!-- Concierge Box -->
                        <div class="sr-concierge-box no-print">
                            <div class="sr-concierge-icon">
                                <span class="material-symbols-outlined">support_agent</span>
                            </div>
                            <div>
                                <div class="sr-concierge-title"><?php echo Text::_('SR_NEED_HELP'); ?></div>
                                <div class="sr-concierge-text"><?php echo Text::_('SR_CONCIERGE_TEXT'); ?></div>
                                <?php if (!empty($this->asset->email)): ?>
                                    <a href="mailto:<?php echo $this->asset->email; ?>" class="sr-concierge-link">
                                        <?php echo Text::_('SR_CONTACT_US'); ?>
                                        <span class="material-symbols-outlined" style="font-size: 14px;">arrow_forward</span>
                                    </a>
                                <?php endif; ?>
                            </div>
                        </div>

                        <!-- Back Link -->
                        <div class="sr-back-link no-print">
                            <a href="<?php echo Uri::root(); ?>"><?php echo Text::_('SR_BACK_TO_HOME'); ?></a>
                        </div>
                    </div>
                </div>

            <?php endif; ?>

        </div>
    </div>
</body>

</html>