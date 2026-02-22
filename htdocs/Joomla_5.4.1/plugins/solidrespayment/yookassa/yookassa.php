<?php
/**
 * YooKassa Payment Plugin for Solidres
 * 
 * @author    Custom Development
 * @copyright Copyright (C) 2026. All Rights Reserved.
 * @license   GNU General Public License version 3, or later
 */

defined('_JEXEC') or die;

JLoader::register('SRPayment', SRPATH_LIBRARY . '/payment/payment.php');

use Joomla\CMS\Factory as CMSFactory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Log\Log;
use Joomla\CMS\Uri\Uri;

class PlgSolidrespaymentYookassa extends SRPayment
{
    /**
     * YooKassa API endpoint
     */
    const API_URL = 'https://api.yookassa.ru/v3/';

    /**
     * Payment statuses from YooKassa
     */
    const STATUS_PENDING = 'pending';
    const STATUS_WAITING_FOR_CAPTURE = 'waiting_for_capture';
    const STATUS_SUCCEEDED = 'succeeded';
    const STATUS_CANCELED = 'canceled';

    /**
     * Initialize payment - creates YooKassa payment and redirects user
     *
     * @param object $reservationData The reservation data
     * @return void
     */
    public function onSolidresPaymentNew($reservationData)
    {
        parent::onSolidresPaymentNew($reservationData);

        $this->log('Starting YooKassa payment for reservation ID: ' . $reservationData->id);

        // Get configuration
        $shopId = $this->dataConfig['yookassa_shop_id'] ?? '';
        $secretKey = $this->dataConfig['yookassa_secret_key'] ?? '';
        $testMode = (int) ($this->dataConfig['yookassa_test_mode'] ?? 0);

        if (empty($shopId) || empty($secretKey)) {
            $this->log('YooKassa configuration error: Shop ID or Secret Key not set', Log::ERROR);
            CMSFactory::getApplication()->enqueueMessage(Text::_('SR_YOOKASSA_CONFIG_ERROR'), 'error');
            return;
        }

        // Prepare payment data
        $description = sprintf(
            Text::_('SR_YOOKASSA_PAYMENT_DESCRIPTION'),
            $reservationData->code,
            $reservationData->reservation_asset_name
        );

        // Amount must be in format "123.45"
        $amount = number_format($this->amountToBePaid, 2, '.', '');

        // Get currency code (default to RUB)
        $currencyCode = $this->getCurrencyCode($reservationData->currency_id);

        $paymentData = [
            'amount' => [
                'value' => $amount,
                'currency' => $currencyCode,
            ],
            'confirmation' => [
                'type' => 'redirect',
                'return_url' => $this->getReturnUrl($reservationData->id),
            ],
            'capture' => true,
            'description' => mb_substr($description, 0, 128), // YooKassa limit
            'metadata' => [
                'reservation_id' => (int) $reservationData->id,
                'reservation_code' => $reservationData->code,
                'asset_id' => (int) $reservationData->reservation_asset_id,
            ],
        ];

        // Add customer email if available
        if (!empty($reservationData->customer_email)) {
            $paymentData['receipt'] = [
                'customer' => [
                    'email' => $reservationData->customer_email,
                ],
                'items' => [
                    [
                        'description' => mb_substr($description, 0, 128),
                        'quantity' => 1,
                        'amount' => [
                            'value' => $amount,
                            'currency' => $currencyCode,
                        ],
                        'vat_code' => 1, // Without VAT
                    ],
                ],
            ];
        }

        $this->log('Creating YooKassa payment with data: ' . json_encode($paymentData));

        // Create payment via API
        $response = $this->apiRequest('payments', 'POST', $paymentData, $shopId, $secretKey);

        if ($response === false || isset($response['error'])) {
            $errorMsg = isset($response['error']) ? $response['error']['description'] : 'Unknown error';
            $this->log('YooKassa API error: ' . $errorMsg, Log::ERROR);
            CMSFactory::getApplication()->enqueueMessage(
                Text::sprintf('SR_YOOKASSA_PAYMENT_ERROR', $errorMsg),
                'error'
            );
            return;
        }

        // Store payment ID in reservation
        if (isset($response['id'])) {
            $this->updateReservationPaymentId($reservationData->id, $response['id']);
            $this->log('YooKassa payment created with ID: ' . $response['id']);
        }

        // Redirect to YooKassa payment page
        if (isset($response['confirmation']['confirmation_url'])) {
            $this->log('Redirecting to YooKassa: ' . $response['confirmation']['confirmation_url']);
            CMSFactory::getApplication()->redirect($response['confirmation']['confirmation_url']);
        } else {
            $this->log('No confirmation URL in response', Log::ERROR);
            CMSFactory::getApplication()->enqueueMessage(Text::_('SR_YOOKASSA_NO_REDIRECT_URL'), 'error');
        }
    }

    /**
     * Handle payment callback/webhook from YooKassa
     *
     * @param string $paymentMethodId The payment method identifier
     * @param array  $callbackData    The callback data
     * @return bool
     */
    public function onSolidresPaymentCallback($paymentMethodId, $callbackData)
    {
        if (!$this->isValidPaymentMethod($paymentMethodId)) {
            return false;
        }

        $this->log('Received YooKassa callback');

        // Get raw POST data for webhook
        $rawInput = file_get_contents('php://input');
        $notification = json_decode($rawInput, true);

        if (empty($notification)) {
            $this->log('Empty or invalid webhook data', Log::WARNING);
            return false;
        }

        $this->log('Webhook data: ' . $rawInput);

        // Verify this is a payment notification
        if (!isset($notification['event']) || !isset($notification['object'])) {
            $this->log('Invalid webhook structure', Log::WARNING);
            return false;
        }

        $event = $notification['event'];
        $payment = $notification['object'];

        // Get reservation ID from metadata
        $reservationId = $payment['metadata']['reservation_id'] ?? null;

        if (!$reservationId) {
            $this->log('No reservation ID in webhook metadata', Log::WARNING);
            return false;
        }

        // Load reservation
        JTable::addIncludePath(JPATH_ADMINISTRATOR . '/components/com_solidres/tables');
        $reservationTable = JTable::getInstance('Reservation', 'SolidresTable');

        if (!$reservationTable->load($reservationId)) {
            $this->log('Could not load reservation: ' . $reservationId, Log::ERROR);
            return false;
        }

        // Load config for this asset
        $this->dataConfig = $this->loadFormData((int) $reservationTable->reservation_asset_id);

        // Handle different events
        switch ($event) {
            case 'payment.succeeded':
                $this->handlePaymentSucceeded($reservationTable, $payment);
                break;

            case 'payment.canceled':
                $this->handlePaymentCanceled($reservationTable, $payment);
                break;

            case 'payment.waiting_for_capture':
                // For two-stage payments - auto-capture
                $this->capturePayment($payment['id'], $payment['amount']);
                break;

            default:
                $this->log('Unhandled event type: ' . $event);
        }

        return true;
    }

    /**
     * Handle successful payment
     *
     * @param JTable $reservationTable
     * @param array  $payment
     * @return void
     */
    protected function handlePaymentSucceeded($reservationTable, $payment)
    {
        $this->log('Payment succeeded for reservation: ' . $reservationTable->id);

        // Update reservation status
        $reservationTable->state = $this->confirmationState;
        $reservationTable->payment_status = $this->confirmationPaymentState;
        $reservationTable->payment_method_txn_id = $payment['id'];
        $reservationTable->total_paid = $payment['amount']['value'];
        $reservationTable->payment_data = json_encode($payment);

        if ($reservationTable->store()) {
            $this->log('Reservation updated successfully');

            // Add payment history
            $this->addPaymentHistory(
                $reservationTable,
                Text::sprintf('SR_YOOKASSA_PAYMENT_RECEIVED', $payment['id']),
                Text::_('SR_YOOKASSA_PAYMENT_CONFIRMED'),
                $payment['amount']['value']
            );
        } else {
            $this->log('Failed to update reservation: ' . $reservationTable->getError(), Log::ERROR);
        }
    }

    /**
     * Handle canceled payment
     *
     * @param JTable $reservationTable
     * @param array  $payment
     * @return void
     */
    protected function handlePaymentCanceled($reservationTable, $payment)
    {
        $this->log('Payment canceled for reservation: ' . $reservationTable->id);

        $reservationTable->state = $this->cancellationState;
        $reservationTable->payment_status = $this->cancellationPaymentState;
        $reservationTable->payment_data = json_encode($payment);

        if ($reservationTable->store()) {
            $this->log('Reservation marked as canceled');
        }
    }

    /**
     * Check if reservation should be confirmed
     *
     * @param string  $context
     * @param JTable  $tableReservation
     * @param bool    $isConfirmed
     * @return void
     */
    public function onReservationCheckConfirmed($context, $tableReservation, &$isConfirmed)
    {
        if ($tableReservation->payment_method_id !== 'yookassa') {
            return;
        }

        // If we have a transaction ID, verify with YooKassa
        if (!empty($tableReservation->payment_method_txn_id)) {
            $this->dataConfig = $this->loadFormData((int) $tableReservation->reservation_asset_id);

            $shopId = $this->dataConfig['yookassa_shop_id'] ?? '';
            $secretKey = $this->dataConfig['yookassa_secret_key'] ?? '';

            if (!empty($shopId) && !empty($secretKey)) {
                $payment = $this->apiRequest(
                    'payments/' . $tableReservation->payment_method_txn_id,
                    'GET',
                    null,
                    $shopId,
                    $secretKey
                );

                if ($payment && isset($payment['status']) && $payment['status'] === self::STATUS_SUCCEEDED) {
                    $isConfirmed = true;
                    $this->log('Payment verified as succeeded: ' . $tableReservation->payment_method_txn_id);
                }
            }
        }
    }

    /**
     * Finalize reservation after return from YooKassa
     *
     * @param string $context
     * @param int    $reservationId
     * @return bool
     */
    public function onReservationFinalize($context, &$reservationId)
    {
        if (!parent::onReservationFinalize($context, $reservationId)) {
            return false;
        }

        $this->log('Finalizing YooKassa payment for reservation: ' . $reservationId);

        // Load reservation to check payment status
        JTable::addIncludePath(JPATH_ADMINISTRATOR . '/components/com_solidres/tables');
        $reservationTable = JTable::getInstance('Reservation', 'SolidresTable');

        if (!$reservationTable->load($reservationId)) {
            return false;
        }

        // If payment ID exists, verify status
        if (!empty($reservationTable->payment_method_txn_id)) {
            $this->dataConfig = $this->loadFormData((int) $reservationTable->reservation_asset_id);

            $shopId = $this->dataConfig['yookassa_shop_id'] ?? '';
            $secretKey = $this->dataConfig['yookassa_secret_key'] ?? '';

            $payment = $this->apiRequest(
                'payments/' . $reservationTable->payment_method_txn_id,
                'GET',
                null,
                $shopId,
                $secretKey
            );

            if ($payment && $payment['status'] === self::STATUS_SUCCEEDED) {
                $this->handlePaymentSucceeded($reservationTable, $payment);
            }
        }

        return true;
    }

    /**
     * Make API request to YooKassa
     *
     * @param string      $endpoint
     * @param string      $method
     * @param array|null  $data
     * @param string      $shopId
     * @param string      $secretKey
     * @return array|false
     */
    protected function apiRequest($endpoint, $method = 'GET', $data = null, $shopId = '', $secretKey = '')
    {
        $url = self::API_URL . $endpoint;

        $headers = [
            'Content-Type: application/json',
            'Idempotence-Key: ' . uniqid('sr_', true),
        ];

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_USERPWD, $shopId . ':' . $secretKey);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);

        if ($method === 'POST') {
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        }

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);
        curl_close($ch);

        if ($error) {
            $this->log('cURL error: ' . $error, Log::ERROR);
            return false;
        }

        $this->log('API Response (' . $httpCode . '): ' . $response);

        $result = json_decode($response, true);

        if ($httpCode >= 400) {
            $this->log('API error response: ' . $response, Log::ERROR);
            return $result ?: false;
        }

        return $result;
    }

    /**
     * Capture a waiting payment
     *
     * @param string $paymentId
     * @param array  $amount
     * @return array|false
     */
    protected function capturePayment($paymentId, $amount)
    {
        $shopId = $this->dataConfig['yookassa_shop_id'] ?? '';
        $secretKey = $this->dataConfig['yookassa_secret_key'] ?? '';

        return $this->apiRequest(
            'payments/' . $paymentId . '/capture',
            'POST',
            ['amount' => $amount],
            $shopId,
            $secretKey
        );
    }

    /**
     * Update reservation with YooKassa payment ID
     *
     * @param int    $reservationId
     * @param string $paymentId
     * @return void
     */
    protected function updateReservationPaymentId($reservationId, $paymentId)
    {
        $db = CMSFactory::getDbo();
        $query = $db->getQuery(true)
            ->update($db->qn('#__sr_reservations'))
            ->set($db->qn('payment_method_txn_id') . ' = ' . $db->q($paymentId))
            ->where($db->qn('id') . ' = ' . (int) $reservationId);
        $db->setQuery($query);
        $db->execute();
    }

    /**
     * Get currency code for reservation
     *
     * @param int $currencyId
     * @return string
     */
    protected function getCurrencyCode($currencyId)
    {
        if (empty($currencyId)) {
            return 'RUB';
        }

        $db = CMSFactory::getDbo();
        $query = $db->getQuery(true)
            ->select($db->qn('currency_code'))
            ->from($db->qn('#__sr_currencies'))
            ->where($db->qn('id') . ' = ' . (int) $currencyId);
        $db->setQuery($query);
        $code = $db->loadResult();

        return $code ?: 'RUB';
    }
}
