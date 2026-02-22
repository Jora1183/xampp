<?php
defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Router\Route;
use Joomla\CMS\MVC\Model\BaseDatabaseModel;

// 1. Security Check: Ensure User is Logged In
$user = Factory::getUser();
if ($user->guest) {
    $app = Factory::getApplication();
    $app->enqueueMessage(Text::_('JERROR_ALERTNOAUTHOR'), 'warning');
    $app->redirect(Route::_('index.php?option=com_users&view=login', false));
    return;
}

// 2. Load Solidres Reservation Model (Admin Model)
if (!defined('JPATH_SOLIDRES_ADMIN')) {
    define('JPATH_SOLIDRES_ADMIN', JPATH_ADMINISTRATOR . '/components/com_solidres');
}

BaseDatabaseModel::addIncludePath(JPATH_SOLIDRES_ADMIN . '/models', 'SolidresModel');
$model = BaseDatabaseModel::getInstance('Reservations', 'SolidresModel', ['ignore_request' => true]);

// 3. Resolve Solidres Customer ID from Joomla User ID
$db = Factory::getDbo();
$query = $db->getQuery(true);
$query->select('id')
    ->from($db->quoteName('#__sr_customers'))
    ->where($db->quoteName('user_id') . ' = ' . (int) $user->id);
$db->setQuery($query);
$customerId = $db->loadResult();

// Setup Filters
$model->setState('filter.is_customer_dashboard', 1); // Crucial for frontend viewing
$model->setState('list.limit', 20); // Show last 20 bookings
$model->setState('list.ordering', 'r.id');
$model->setState('list.direction', 'DESC');

// Filter by Customer ID if found, otherwise by User ID (fallback logic in model)
if ($customerId) {
    $model->setState('filter.customer_id', $customerId);
} else {
    // If no customer record exists, try filtering by user ID directly if model supports it, 
    // or return empty to be safe.
    // For now, let's assume if no SR customer record, they have no SR bookings.
}

$items = $customerId ? $model->getItems() : [];

?>

<div class="my-bookings-page py-12 bg-gray-50 min-h-screen">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

        <!-- Page Header -->
        <div class="mb-8 border-b border-gray-200 pb-4">
            <h1 class="text-3xl font-serif font-bold text-resort-dark">Мои бронирования</h1>
            <p class="text-gray-500 mt-2">История ваших поездок и текущие бронирования</p>
        </div>

        <?php if (empty($items)): ?>

            <!-- Empty State -->
            <div class="bg-white rounded-xl shadow-sm p-12 text-center border border-gray-100">
                <div
                    class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-gray-50 mb-4 text-resort-green">
                    <span class="icon-calendar text-3xl"></span>
                </div>
                <h3 class="text-xl font-bold text-gray-900 mb-2">Бронирований пока нет</h3>
                <p class="text-gray-500 mb-6 max-w-md mx-auto">Вы еще не совершали бронирований. Начните планировать свой
                    идеальный отдых прямо сейчас.</p>
                <a href="<?php echo Route::_('index.php?Itemid=122'); ?>"
                    class="btn btn-primary px-6 py-3 rounded-lg font-semibold bg-resort-green border-resort-green text-white hover:bg-resort-green-hover transition-colors">
                    Найти доступные даты
                </a>
            </div>

        <?php else: ?>

            <!-- Bookings List -->
            <div class="space-y-6">
                <?php foreach ($items as $item): ?>
                    <?php
                    // Determine Status Badge Color
                    // 1=Confirmed, 2=Pending, 3=Cancelled (Example - adjust based on actual Solidres constants)
                    $statusClass = 'bg-gray-100 text-gray-800';
                    $statusLabel = 'Unknown';

                    switch ($item->state) {
                        case 1:
                            $statusClass = 'bg-green-100 text-green-800 border-green-200';
                            $statusLabel = 'Подтверждено';
                            break;
                        case 0:
                            $statusClass = 'bg-yellow-100 text-yellow-800 border-yellow-200';
                            $statusLabel = 'Ожидает оплаты';
                            break;
                        case 2: // Cancelled
                            $statusClass = 'bg-red-100 text-red-800 border-red-200';
                            $statusLabel = 'Отменено';
                            break;
                        default:
                            $statusClass = 'bg-gray-100 text-gray-800 border-gray-200';
                            $statusLabel = 'В обработке';
                    }
                    ?>

                    <div
                        class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden hover:shadow-md transition-shadow duration-200">
                        <div class="p-6">
                            <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 mb-4">
                                <div>
                                    <div class="flex items-center gap-3 mb-1">
                                        <span class="font-mono text-sm font-bold text-gray-400">#
                                            <?php echo $item->code; ?>
                                        </span>
                                        <span
                                            class="px-3 py-1 rounded-full text-xs font-semibold border <?php echo $statusClass; ?>">
                                            <?php echo $statusLabel; ?>
                                        </span>
                                    </div>
                                    <h3 class="text-xl font-bold text-gray-900">
                                        <?php echo htmlspecialchars($item->reservation_asset_name); ?>
                                    </h3>
                                </div>
                                <div class="text-right">
                                    <span class="block text-2xl font-bold text-resort-green">
                                        <?php echo SRUtilities::formatMoney($item->total_amount, $item->currency_id); ?>
                                    </span>
                                    <span class="text-sm text-gray-500">
                                        <?php echo $item->length_of_stay; ?> ночей
                                    </span>
                                </div>
                            </div>

                            <hr class="border-gray-100 my-4">

                            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 text-sm">
                                <div>
                                    <span
                                        class="block text-gray-400 text-xs uppercase font-bold tracking-wider mb-1">Заезд</span>
                                    <div class="flex items-center gap-2 text-gray-700">
                                        <span class="icon-calendar"></span>
                                        <span class="font-semibold">
                                            <?php echo HTMLHelper::_('date', $item->checkin, Text::_('DATE_FORMAT_LC1')); ?>
                                        </span>
                                    </div>
                                </div>
                                <div>
                                    <span
                                        class="block text-gray-400 text-xs uppercase font-bold tracking-wider mb-1">Выезд</span>
                                    <div class="flex items-center gap-2 text-gray-700">
                                        <span class="icon-calendar"></span>
                                        <span class="font-semibold">
                                            <?php echo HTMLHelper::_('date', $item->checkout, Text::_('DATE_FORMAT_LC1')); ?>
                                        </span>
                                    </div>
                                </div>
                                <div class="md:text-right flex items-center md:justify-end">
                                    <a href="<?php echo Route::_('index.php?option=com_solidres&view=reservation&layout=default&id=' . $item->id . '&code=' . $item->code); ?>"
                                        class="inline-flex items-center gap-2 text-resort-green font-semibold hover:text-resort-green-hover transition-colors group">
                                        Детали бронирования
                                        <span
                                            class="icon-arrow-right text-sm transition-transform group-hover:translate-x-1"></span>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</div>