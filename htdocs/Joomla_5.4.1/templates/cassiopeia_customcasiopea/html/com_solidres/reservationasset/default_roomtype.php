<?php
/**
 * Custom template override: per-house showcase blocks for Solidres reservationasset.
 *
 * Override path (correct):
 * templates/cassiopeia_customcasiopea/html/com_solidres/reservationasset/default_roomtype.php
 */

defined('_JEXEC') or die;

use Joomla\CMS\Uri\Uri;
use Joomla\CMS\Language\Text;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Factory;
use Joomla\CMS\Date\Date;
use Joomla\CMS\Router\Route;
use Solidres\Media\ImageUploaderHelper;

$doc = Factory::getDocument();
$doc->addStyleSheet(Uri::root() . 'templates/cassiopeia_customcasiopea/css/solidres-custom.css', ['version' => 'auto']);

$layout           = SRLayoutHelper::getInstance();
$showInquiryForm  = !empty($this->item->params['show_inquiry_form']);
$inquiryRoomType  = $showInquiryForm && !empty($this->item->params['show_inquiry_form_scope']);

if ($showInquiryForm):
    echo $this->loadTemplate('inquiry_form');
endif;

// ============================================================
// Hardcoded per-house showcase data (descriptions & amenities)
// Keyed by pattern in the room type name (Cyrillic or number).
// ============================================================
$houseData = [
    '1' => [
        'eyebrow'     => 'Вид на озеро',
        'capacity'    => 'до 4 человек',
        'description' => 'Уютный одноэтажный дом с просторной верандой, раскинувшийся у самой кромки озера. Большие окна открывают живописные виды на водную гладь — идеальный фон для неспешного утреннего кофе. Интерьер выполнен в тёплых натуральных тонах: деревянные акценты, мягкий текстиль и всё необходимое для по-настоящему домашнего отдыха. Идеально подходит как для романтического уикенда, так и для семейного отдыха с детьми.',
        'amenities'   => [
            ['water',         'Вид на озеро'],
            ['bed',           '2 спальни'],
            ['shower',        'Душевая'],
            ['deck',          'Просторная веранда'],
            ['outdoor_grill', 'Мангал'],
            ['kitchen',       'Кухня'],
            ['wifi',          'Wi-Fi'],
            ['local_parking', 'Парковка'],
        ],
    ],
    '2' => [
        'eyebrow'     => 'Двухэтажный коттедж',
        'capacity'    => 'до 6 человек',
        'description' => 'Величественный двухэтажный коттедж с открытым балконом, с которого открываются панорамные виды на лесные дали. На первом этаже просторная гостиная с камином — место, где семья собирается вечером. Второй этаж с отдельными спальнями обеспечивает уединение для каждого. Дом создан для тех, кто ценит пространство, покой и настоящий семейный уют вдали от городской суеты.',
        'amenities'   => [
            ['fireplace',     'Камин'],
            ['balcony',       'Балкон с панорамой'],
            ['bed',           '3 спальни'],
            ['shower',        '2 санузла'],
            ['kitchen',       'Полная кухня'],
            ['deck',          'Терраса'],
            ['wifi',          'Wi-Fi'],
            ['local_parking', 'Парковка'],
        ],
    ],
    '3' => [
        'eyebrow'     => 'Панорамный современный дом',
        'capacity'    => 'до 8 человек',
        'description' => 'Современный загородный дом с панорамным остеклением, наполненный светом с раннего утра до заката. Просторная открытая терраса площадью более 30 м² становится любимым местом встречи всей компании. Лаконичный интерьер гармонично сочетается с природными материалами: камень, дерево, натуральный текстиль. Идеальный выбор для большой компании, ищущей комфорт без компромиссов.',
        'amenities'   => [
            ['panorama',      'Панорамные окна'],
            ['deck',          'Терраса 30+ м²'],
            ['bed',           '4 спальни'],
            ['shower',        '2 санузла'],
            ['kitchen',       'Оборудованная кухня'],
            ['outdoor_grill', 'Зона барбекю'],
            ['wifi',          'Wi-Fi'],
            ['local_parking', 'Парковка'],
        ],
    ],
    '4' => [
        'eyebrow'     => 'Уединённый лесной дом',
        'capacity'    => 'до 4 человек',
        'description' => 'Утопающий в зелени лесного массива стильный дом — идеальное убежище для тех, кто мечтает о полном слиянии с природой. Густой лес подходит вплотную к стенам, создавая ощущение полного уединения: лишь шум ветра в кронах и пение птиц. Современная отделка и панорамные окна делают пребывание по-настоящему комфортным. Минимализм и природа — главная философия этого пространства.',
        'amenities'   => [
            ['forest',        'Лесное окружение'],
            ['panorama',      'Панорамные окна'],
            ['bed',           '2 спальни'],
            ['shower',        'Душевая'],
            ['kitchen',       'Кухня'],
            ['outdoor_grill', 'Мангал'],
            ['wifi',          'Wi-Fi'],
            ['local_parking', 'Парковка'],
        ],
    ],
    '5' => [
        'eyebrow'     => 'Премиум-вилла',
        'capacity'    => 'до 12 человек',
        'description' => 'Роскошная вилла — флагманское предложение усадьбы — создана для тех, кто привык к лучшему. Просторные залы, вместительные спальни и полный спектр развлечений: бильярдная, собственная сауна, оборудованный кинозал. Ухоженная территория с открытой террасой позволяет принять большую компанию с истинным размахом. Уединение, роскошь и природа — всё воплощено в этой вилле.',
        'amenities'   => [
            ['sauna',         'Сауна'],
            ['sports_esports','Бильярдная'],
            ['bed',           '5+ спален'],
            ['shower',        '3 санузла'],
            ['kitchen',       'Полная кухня'],
            ['deck',          'Большая терраса'],
            ['outdoor_grill', 'Барбекю-зона'],
            ['wifi',          'Wi-Fi'],
            ['local_parking', 'Парковка'],
            ['movie',         'Кинозал'],
        ],
    ],
    '6' => [
        'eyebrow'     => 'Деревянный сруб',
        'capacity'    => 'до 4 человек',
        'description' => 'Классический русский деревянный сруб — воплощение тепла и уюта, которых так не хватает в мире бетона и стекла. Ароматная натуральная древесина, потрескивающий огонь в камине, тяжёлые льняные шторы окутывают гостей особой атмосферой. Здесь время замедляется, а душа находит долгожданный покой. Дом №6 — это не просто жильё, это возвращение к истокам.',
        'amenities'   => [
            ['cabin',         'Сруб из дерева'],
            ['fireplace',     'Камин'],
            ['bed',           '2 спальни'],
            ['shower',        'Санузел'],
            ['kitchen',       'Кухня'],
            ['deck',          'Крыльцо'],
            ['wifi',          'Wi-Fi'],
            ['local_parking', 'Парковка'],
        ],
    ],
    'tents' => [
        'eyebrow'     => 'Glamping под звёздами',
        'capacity'    => '2 человека / палатка',
        'description' => 'Для тех, кто слышит зов природы. Наши кемпинговые палатки установлены в самом живописном уголке усадьбы — в окружении деревьев, вдали от городского шума. Засыпать под настоящим звёздным небом, просыпаться от пения птиц, дышать чистейшим лесным воздухом — это опыт, который невозможно забыть. Всё снаряжение предоставляется.',
        'amenities'   => [
            ['camping',             'Снаряжение включено'],
            ['star',                'Звёздное небо'],
            ['local_fire_department','Кострище'],
            ['outdoor_grill',       'Мангал'],
            ['wc',                  'Санузел на территории'],
            ['water_drop',          'Питьевая вода'],
            ['hiking',              'Пешие маршруты'],
            ['nature',              'Дикая природа'],
        ],
    ],
];

/**
 * Match a room type name to our house data key.
 * Looks for "№1"…"№6" pattern, or "палатк" for tents.
 */
function srMatchHouseKey($name) {
    if (preg_match('/палатк/ui', $name))     return 'tents';
    if (preg_match('/кемпинг/ui', $name))    return 'tents';
    if (preg_match('/tent/i', $name))        return 'tents';
    if (preg_match('/[№#]\s*(\d)/u', $name, $m)) return $m[1];
    if (preg_match('/\b(\d)\b/', $name, $m)) return $m[1];
    return null;
}

// ── Per-room date picker setup ───────────────────────────────────────────────
$rtMinCheckoutDate = $this->minDaysBookInAdvance + $this->minLengthOfStay;
$rtAvailableDates  = (!empty($this->item->params['available_dates'])) ? $this->item->params['available_dates'] : '[]';

$rtDateCheckIn = Date::getInstance();
$rtDateCheckIn->add(new DateInterval('P' . $this->minDaysBookInAdvance . 'D'))->setTimezone($this->timezone);
$rtDateCheckOut = Date::getInstance();
$rtDateCheckOut->add(new DateInterval('P' . $rtMinCheckoutDate . 'D'))->setTimezone($this->timezone);

if (!empty($this->item->params['available_dates'])) {
    $rtDefaultLOS      = $this->item->params['inline_default_los'] ?? $this->minLengthOfStay;
    $rtFirstAvail      = json_decode($rtAvailableDates)[0];
    $rtCheckinDisplay  = $rtDateCheckIn->format($this->dateFormat, true);
    $rtCheckinValue    = $rtFirstAvail;
    $rtCheckoutValue   = Date::getInstance($rtFirstAvail, $this->timezone)->add(new DateInterval("P{$rtDefaultLOS}D"))->format('Y-m-d', true);
    $rtCheckoutDisplay = Date::getInstance($rtCheckoutValue, $this->timezone)->format($this->dateFormat, true);
} else {
    $rtCheckinDisplay  = $rtDateCheckIn->format($this->dateFormat, true);
    $rtCheckinValue    = $rtDateCheckIn->format('Y-m-d', true);
    $rtCheckoutDisplay = $rtDateCheckOut->format($this->dateFormat, true);
    $rtCheckoutValue   = $rtDateCheckOut->format('Y-m-d', true);
}

if ($this->isFresh) {
    // Load Flatpickr assets for per-room date pickers (deduplicated by Joomla)
    $wa = $this->getDocument()->getWebAssetManager();
    $wa->getRegistry()->addExtensionRegistryFile('com_solidres');
    $wa->useStyle('flatpickr')->useScript('flatpickr')->useScript('flatpickr.ru');
    HTMLHelper::_('script', 'templates/' . $this->app->getTemplate() . '/js/solidres-datepicker-adapter.js', ['version' => 'auto']);
}

$rtItemId  = $this->itemid ?: '';
$rtBaseUrl = Route::_(
    'index.php?option=com_solidres&view=reservationasset&id=' . $this->item->id
    . ($rtItemId ? '&Itemid=' . $rtItemId : ''),
    false
);
?>

<a id="book-form"></a>

<?php if ($this->isFresh): ?>
<script>
function srBookRoom(containerId) {
    var c = document.getElementById(containerId);
    if (!c) return;
    var ci = c.querySelector('input[name="checkin"]');
    var co = c.querySelector('input[name="checkout"]');
    if (!ci || !ci.value || !co || !co.value) {
        var f = c.querySelector('.checkin_module');
        if (f) { f.click(); f.focus(); }
        return;
    }
    var url = c.getAttribute('data-base-url');
    var sep = url.indexOf('?') !== -1 ? '&' : '?';
    url += sep
        + 'checkin='      + encodeURIComponent(ci.value)
        + '&checkout='    + encodeURIComponent(co.value)
        + '&room_type_id=' + c.getAttribute('data-room-type-id');
    <?php if ($this->enableAutoScroll): ?>url += '#book-form';<?php endif; ?>
    window.location.href = url;
}
</script>
<?php endif; ?>

<?php
// ── Check-availability form at top ──────────────────────────
if (
    isset($this->item->params['show_inline_checkavailability_form'])
    && $this->item->params['show_inline_checkavailability_form'] == 1
    && !$this->disableOnlineBooking
    && !$this->isAmending
): ?>
    <div id="asset-checkavailability-form" style="max-width: 80rem; margin: 2rem auto; padding: 0 1rem;">
        <div style="background: white; border-radius: 0.75rem; box-shadow: 0 4px 24px rgba(0,0,0,0.08); padding: 1.5rem;">
            <p style="font-size: 0.75rem; font-weight: 700; letter-spacing: 0.15em; text-transform: uppercase; color: #5c7c3b; margin-bottom: 0.5rem;">Проверить доступность</p>
            <h3 style="font-family: 'Playfair Display', serif; font-size: 1.4rem; color: #1a1a1a; margin-bottom: 1rem;">Выберите даты заезда и выезда</h3>
            <?php echo $this->loadTemplate('checkavailability'); ?>
        </div>
    </div>
<?php endif ?>

<?php if ($this->isAmending): ?>
    <h2><?php echo Text::_('SR_AMENDING_HEADING') ?></h2>
<?php endif ?>

<?php
// ── Booking wizard shell ─────────────────────────────────────
if (!$this->disableOnlineBooking): ?>
<style>
/* Wizard step bar */
.sr-wizard-steps { display: flex; list-style: none; margin: 0; padding: 0; max-width: 80rem; margin: 0 auto 0; }
.sr-wizard-steps li {
    flex: 1; display: flex; align-items: center; justify-content: center;
    padding: 0.65rem 0.5rem; background: #f5f5f5; color: #9ca3af;
    font-size: 0.82rem; font-weight: 700; position: relative; gap: 0.4rem;
    margin: 0; border-bottom: 2px solid #e5e7eb;
}
.sr-wizard-steps li.active { background: white; color: #5c7c3b; border-bottom-color: #5c7c3b; }
.sr-wizard-steps li .badge { background: #d1d5db; color: #4b5563; border-radius: 50%; width: 20px; height: 20px; display: inline-flex; align-items: center; justify-content: center; font-size: 0.7rem; }
.sr-wizard-steps li.active .badge { background: #5c7c3b; color: white; }
</style>

<?php if (!$this->isFresh): ?>
<ul class="sr-wizard-steps <?php echo SR_UI_GRID_CONTAINER ?> steps list-inline">
    <li data-target="#step1" class="list-inline-item active reservation-tab reservation-tab-room flex-fill text-center p-2 position-relative">
        <span class="badge bg-light text-dark">1</span>
        <?php echo Text::_('SR_STEP_ROOM_AND_RATE') ?><span class="chevron"></span>
    </li>
    <li data-target="#step2" class="list-inline-item reservation-tab reservation-tab-guestinfo flex-fill text-center p-2 position-relative">
        <span class="badge bg-secondary">2</span>
        <?php echo Text::_('SR_STEP_GUEST_INFO_AND_PAYMENT') ?><span class="chevron"></span>
    </li>
    <li data-target="#step3" class="list-inline-item reservation-tab reservation-tab-confirmation flex-fill text-center p-2 position-relative">
        <span class="badge bg-secondary">3</span>
        <?php echo Text::_('SR_STEP_CONFIRMATION') ?>
    </li>
</ul>
<?php endif; // !isFresh ?>
<?php endif; // !disableOnlineBooking ?>

<div class="step-content">
<div class="step-pane active" id="step1">
<div class="reservation-single-step-holder room room-default">

<?php
if ($this->prioritizingRoomTypeId == 0):
    echo $this->loadTemplate('searchinfo');
endif;
?>

<form enctype="multipart/form-data" id="sr-reservation-form-room" class="sr-reservation-form"
    action="<?php echo \Joomla\CMS\Uri\Uri::base() ?>index.php?option=com_solidres&task=reservation.process&step=room&format=json"
    method="POST">

<?php
$roomTypeCount = count($this->item->roomTypes);
$roomTypeDisplayData = [
    'isFresh'      => $this->isFresh,
    'roomTypeCount' => $roomTypeCount,
];

if ($roomTypeCount > 0):

    $blockIndex = 0; // for alternating layout
    $countNotPrioritizing = 0;

    // Pre-compute name so it's available regardless of room type order in the loop
    $prioritizingRoomTypeName = '';
    if ($this->prioritizingRoomTypeId > 0):
        $countNotPrioritizing = $roomTypeCount - 1;
        foreach ($this->item->roomTypes as $_rt):
            if ($_rt->id == $this->prioritizingRoomTypeId):
                $prioritizingRoomTypeName = $_rt->name;
                break;
            endif;
        endforeach;
    endif;

    foreach ($this->item->roomTypes as $roomType):

        // ── Match showcase data ───────────────────────────────
        $houseKey  = srMatchHouseKey($roomType->name);
        $showcase  = isset($houseKey, $houseData[$houseKey]) ? $houseData[$houseKey] : null;
        $isEven    = ($blockIndex % 2 === 1); // alternate layout direction
        $bgStyle   = $isEven ? 'background:#f9f9f9;' : 'background:white;';
        $blockIndex++;

        $rowCSSClass   = [];
        $rowCSSClass[] = ($blockIndex % 2) ? 'even' : 'odd';
        $rowCSSClass[] = $roomType->featured == 1 ? 'featured' : '';
        $rowCSSClass[] = 'room_type_row';

        if (!is_array($roomType->params)):
            $roomType->params = json_decode($roomType->params, true);
        endif;

        $roomTypeDisplayData = array_merge($roomTypeDisplayData, [
            'Itemid'                   => $this->itemid,
            'bookingType'              => $this->item->booking_type,
            'checkinFormatted'         => $this->checkinFormatted ?? null,
            'checkoutFormatted'        => $this->checkoutFormatted ?? null,
            'config'                   => $this->config,
            'dayMapping'               => $this->dayMapping,
            'defaultTariffVisibility'  => $this->defaultTariffVisibility,
            'disableOnlineBooking'     => $this->disableOnlineBooking,
            'enableAutoScroll'         => $this->enableAutoScroll,
            'inquiryRoomType'          => $inquiryRoomType,
            'isExclusive'              => (bool) ($roomType->params['is_exclusive'] ?? false),
            'isSingular'               => $this->isSingular,
            'item'                     => $this->item,
            'roomType'                 => $roomType,
            'selectedRoomTypes'        => $this->selectedRoomTypes,
            'showRemainingRooms'       => (bool) ($roomType->params['show_number_remaining_rooms'] ?? true),
            'showTariffs'              => $this->showTariffs,
            'showTaxIncl'              => $this->showTaxIncl,
            'skipRoomForm'             => (bool) ($roomType->params['skip_room_form'] ?? false),
            'stayLength'               => $this->stayLength,
            'tariffNetOrGross'         => $this->tariffNetOrGross,
        ]);

        echo SRLayoutHelper::render('roomtype.rateplan_breakdown', $roomTypeDisplayData);

        // Prepare description text
        $intro = $full = '';
        $regex = '#<hr(.*)id="system-readmore"(.*)\/>#iU';
        if (preg_match($regex, $roomType->description)) {
            [$intro, $full] = preg_split($regex, $roomType->description, 2);
            $roomType->text = $intro;
        } else {
            $roomType->text = $roomType->description;
        }
        Factory::getApplication()->triggerEvent('onContentPrepare', ['com_solidres.roomtype', &$roomType, &$roomType->params, 0]);

        $isPrioritizingRoomType = false;
        if ($this->prioritizingRoomTypeId == $roomType->id):
            $isPrioritizingRoomType = true;
            $rowCSSClass[] = 'prioritizing';
        endif;

        if ($this->prioritizingRoomTypeId > 0 && $blockIndex == 2):
            echo '<div class="prioritizing-roomtype-notice" style="margin:1rem 0 0.5rem;">'
                . '<a href="' . $this->escape($rtBaseUrl) . '" '
                . 'style="display:inline-flex;align-items:center;gap:0.4rem;color:#5c7c3b;font-size:0.9rem;font-weight:700;text-decoration:none;">'
                . '<span class="material-symbols-outlined" style="font-size:18px;">arrow_back</span>'
                . 'Назад ко всем вариантам'
                . '</a></div>';
        endif;

        // ── Collect images ────────────────────────────────────
        $mediaItems  = [];
        $mainImgSrc  = '';
        if (!empty($roomType->media)):
            foreach ($roomType->media as $med):
                $imgSrc = is_array($med) ? ($med['image'] ?? '') : $med;
                if (!preg_match('/^\/|https?/', $imgSrc)):
                    $imgSrc = ImageUploaderHelper::getImage($imgSrc);
                endif;
                $thumbSrc = is_array($med) ? ($med['thumb'] ?? $imgSrc) : $imgSrc;
                if (!preg_match('/^\/|https?/', $thumbSrc)):
                    $thumbSrc = ImageUploaderHelper::getImage($thumbSrc, 'full');
                endif;
                $mediaItems[] = ['img' => $imgSrc, 'thumb' => $thumbSrc];
            endforeach;
            $mainImgSrc = $mediaItems[0]['img'] ?? '';
        endif;
        $hasMedia    = !empty($mediaItems);
        $mediaCount  = count($mediaItems);
        $houseBlockId = 'sr-' . $roomType->id;
?>

        <!-- Divider (skip for first block) -->
        <?php if ($blockIndex > 1): ?>
        <div class="sr-house-divider" style="margin: 0 -1rem;">
            <div class="line"></div>
            <span class="material-symbols-outlined" style="color:#d1d5db; font-size:22px;">spa</span>
            <div class="line"></div>
        </div>
        <?php endif; ?>

        <!-- ======================================================
             HOUSE SHOWCASE BLOCK
        ====================================================== -->
        <div class="<?php echo implode(' ', $rowCSSClass) ?>"
            id="room_type_row_<?php echo $roomType->id ?>"
            <?php echo ($this->prioritizingRoomTypeId > 0 && !$isPrioritizingRoomType) ? 'style="display:none"' : '' ?>>

        <article id="sr-house-<?php echo $roomType->id ?>"
            class="sr-house-block"
            style="display: grid; grid-template-columns: 1fr; <?php echo $bgStyle ?>">

            <style>
            @media (min-width: 1024px) {
                #sr-house-<?php echo $roomType->id ?> {
                    grid-template-columns: 1fr 1fr;
                    min-height: 520px;
                }
                <?php if ($isEven): ?>
                #sr-house-<?php echo $roomType->id ?> .sr-gallery-col { order: 2; }
                #sr-house-<?php echo $roomType->id ?> .sr-info-col    { order: 1; }
                <?php endif; ?>
            }
            </style>

            <!-- GALLERY COLUMN -->
            <div class="sr-gallery-col" style="display: flex; flex-direction: column; position: relative;">

                <?php if ($roomType->featured == 1): ?>
                    <div class="sr-rec-badge">Рекомендуем</div>
                <?php endif; ?>

                <?php if ($hasMedia): ?>
                    <!-- Main image -->
                    <div class="sr-showcase-gallery" style="flex: 1; min-height: 280px;">
                        <img id="sr-main-img-<?php echo $roomType->id ?>"
                            src="<?php echo htmlspecialchars($mainImgSrc, ENT_QUOTES) ?>"
                            alt="<?php echo $this->escape($roomType->name) ?>"
                            style="width:100%; height:100%; object-fit:cover; transition: opacity 0.2s ease;">
                    </div>
                    <!-- Thumbnail strip -->
                    <?php if ($mediaCount > 1): ?>
                    <div id="sr-strip-<?php echo $roomType->id ?>" class="sr-thumb-strip">
                        <?php foreach ($mediaItems as $mi => $med): ?>
                        <button class="sr-thumb-btn <?php echo $mi === 0 ? 'active' : '' ?>"
                            onclick="srSwapImage('<?php echo $roomType->id ?>','<?php echo htmlspecialchars($med['img'], ENT_QUOTES) ?>',this)">
                            <img src="<?php echo htmlspecialchars($med['thumb'], ENT_QUOTES) ?>"
                                alt="фото <?php echo $mi + 1 ?>" loading="lazy">
                        </button>
                        <?php endforeach; ?>
                    </div>
                    <?php endif; ?>

                <?php elseif ($houseKey === 'tents'): ?>
                    <!-- Tents: no photos — night-sky gradient -->
                    <div style="flex:1; min-height:280px; display:flex; flex-direction:column; align-items:center; justify-content:center; background: linear-gradient(180deg, #0f1a0f 0%, #1e3d1a 40%, #2d5a27 70%, #4a7c3b 100%); position:relative; overflow:hidden;">
                        <span class="material-symbols-outlined" style="font-size:80px; color:#a8d878; opacity:0.9; position:relative; z-index:1;">camping</span>
                        <p style="font-family:'Playfair Display',serif; font-weight:700; color:#d4f0b0; font-size:1.2rem; margin-top:1rem; position:relative; z-index:1;">Отдых под открытым небом</p>
                        <p style="color:#8fc870; font-size:0.85rem; opacity:0.8; position:relative; z-index:1;">Glamping в окружении природы</p>
                    </div>
                <?php else: ?>
                    <!-- Other houses: no photos yet — green gradient placeholder -->
                    <div style="flex:1; min-height:280px; display:flex; flex-direction:column; align-items:center; justify-content:center; background: linear-gradient(135deg, #e8efe0 0%, #c5d9a5 50%, #a8c87a 100%);">
                        <span class="material-symbols-outlined" style="font-size:80px; color:#5c7c3b; opacity:0.85;">cabin</span>
                        <p style="font-family:'Playfair Display',serif; font-weight:700; color:#3a5a24; font-size:1.2rem; margin-top:1rem;">
                            <?php echo $this->escape($roomType->name) ?>
                        </p>
                        <p style="color:#4a6a34; font-size:0.85rem; opacity:0.8;">Фотографии скоро появятся</p>
                    </div>
                <?php endif; ?>

            </div><!-- /sr-gallery-col -->

            <!-- INFO COLUMN -->
            <div class="sr-info-col" style="padding: 2rem 1.5rem; display: flex; flex-direction: column; justify-content: center;">

                <!-- Eyebrow -->
                <p style="font-size: 0.7rem; font-weight: 700; letter-spacing: 0.15em; text-transform: uppercase; color: #5c7c3b; margin-bottom: 0.5rem;">
                    <?php echo $showcase ? htmlspecialchars($showcase['eyebrow'], ENT_QUOTES) : '' ?>
                </p>

                <!-- House name -->
                <h3 style="font-family: 'Playfair Display', serif; font-size: 1.9rem; font-weight: 700; color: #1a1a1a; margin-bottom: 0.5rem;">
                    <?php echo $this->escape($roomType->name) ?>
                    <?php if ($roomType->featured == 1): ?>
                        <span class="badge bg-warning text-dark ms-1" style="font-size: 0.65rem; vertical-align: middle;"><?php echo Text::_('SR_FEATURED_ROOM_TYPE') ?></span>
                    <?php endif ?>
                </h3>

                <!-- Capacity -->
                <div style="display: flex; align-items: center; gap: 0.4rem; color: #6b7280; font-size: 0.9rem; margin-bottom: 0.75rem;">
                    <span class="material-symbols-outlined" style="font-size:18px;">group</span>
                    <?php
                    if ($showcase):
                        echo htmlspecialchars($showcase['capacity'], ENT_QUOTES);
                    else:
                        $cap = $roomType->occupancy_max > 0 ? $roomType->occupancy_max : ((int)$roomType->occupancy_adult + (int)$roomType->occupancy_child);
                        echo 'до ' . $cap . ' человек';
                    endif;
                    ?>
                </div>

                <!-- Green divider -->
                <div style="height:2px; width:3.5rem; background:#5c7c3b; border-radius:1px; margin-bottom:1.25rem;"></div>

                <!-- Description -->
                <div style="color: #4b5563; line-height: 1.75; font-size: 0.97rem; margin-bottom: 1.25rem;">
                    <?php if ($showcase): ?>
                        <p style="margin: 0;"><?php echo htmlspecialchars($showcase['description'], ENT_QUOTES) ?></p>
                    <?php elseif (!empty($roomType->text)): ?>
                        <?php echo $roomType->text ?>
                    <?php endif; ?>
                </div>

                <!-- Amenities -->
                <?php
                $amenities = [];
                if ($showcase):
                    $amenities = $showcase['amenities'];
                elseif (!empty($roomType->facilities)):
                    foreach ($roomType->facilities as $fac):
                        $amenities[] = [null, $fac->name];
                    endforeach;
                endif;
                ?>
                <?php if (!empty($amenities)): ?>
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 0.6rem 1rem; margin-bottom: 1.5rem;">
                    <?php foreach ($amenities as [$icon, $label]): ?>
                    <div class="sr-amenity">
                        <?php if ($icon): ?>
                            <span class="material-symbols-outlined"><?php echo htmlspecialchars($icon, ENT_QUOTES) ?></span>
                        <?php elseif (!empty($fac->icon)): ?>
                            <i class="<?php echo htmlspecialchars($fac->icon ?? '', ENT_QUOTES) ?>"></i>
                        <?php endif; ?>
                        <?php echo htmlspecialchars($label, ENT_QUOTES) ?>
                    </div>
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>

                <!-- Booking section -->
                <div class="sr-booking-section">

                <?php if ($this->isFresh): ?>
                    <!-- Per-room date picker: JS navigates to this room + dates -->
                    <div id="sr-room-form-<?php echo $roomType->id ?>"
                         class="sr-room-date-form"
                         data-base-url="<?php echo $this->escape($rtBaseUrl) ?>"
                         data-room-type-id="<?php echo $roomType->id ?>">

                        <div class="<?php echo SR_UI_GRID_CONTAINER ?>" style="margin-bottom:0.75rem;">
                            <div class="<?php echo SR_UI_GRID_COL_6 ?>">
                                <?php echo SRLayoutHelper::render('field.datepicker', [
                                    'fieldLabel'            => 'SR_SEARCH_CHECKIN_DATE',
                                    'fieldName'             => 'checkin',
                                    'fieldClass'            => 'checkin_module',
                                    'datePickerInlineClass' => 'checkin_datepicker_inline_module',
                                    'dateUserFormat'        => $rtCheckinDisplay,
                                    'dateDefaultFormat'     => $rtCheckinValue,
                                ]); ?>
                            </div>
                            <div class="<?php echo SR_UI_GRID_COL_6 ?>">
                                <?php echo SRLayoutHelper::render('field.datepicker', [
                                    'fieldLabel'            => 'SR_SEARCH_CHECKOUT_DATE',
                                    'fieldName'             => 'checkout',
                                    'fieldClass'            => 'checkout_module',
                                    'datePickerInlineClass' => 'checkout_datepicker_inline_module',
                                    'dateUserFormat'        => $rtCheckoutDisplay,
                                    'dateDefaultFormat'     => $rtCheckoutValue,
                                ]); ?>
                            </div>
                        </div>

                        <div class="d-grid">
                            <button type="button"
                                    class="btn <?php echo SR_UI_BTN_DEFAULT ?>"
                                    style="display:flex; align-items:center; justify-content:center; gap:0.4rem;"
                                    onclick="srBookRoom('sr-room-form-<?php echo $roomType->id ?>')">
                                <span class="material-symbols-outlined" style="font-size:18px;">calendar_month</span>
                                Проверить и забронировать
                            </button>
                        </div>
                    </div>

                    <script>
                    Solidres.jQuery(function($) {
                        Solidres.initDatePickers(
                            'sr-room-form-<?php echo $roomType->id ?>',
                            <?php echo (int)$rtMinCheckoutDate ?>,
                            '<?php echo $rtCheckinValue ?>',
                            '<?php echo $rtCheckoutValue ?>',
                            '', '', false, [],
                            <?php echo $rtAvailableDates ?>
                        );
                    });
                    </script>

                <?php else: ?>
                    <!-- Dates selected: show availability status and utility buttons -->
                    <div class="sr-booking-body open">
                        <?php
                        echo SRLayoutHelper::render('roomtype.available_room_msg', $roomTypeDisplayData);
                        echo SRLayoutHelper::render('roomtype.buttons', $roomTypeDisplayData);
                        ?>
                    </div>
                <?php endif; ?>

                    <!-- More description (always present, toggled by "Show more info" button) -->
                    <div class="unstyled more_desc" id="more_desc_<?php echo $roomType->id ?>" style="display: none">
                        <?php echo SRLayoutHelper::render('roomtype.customfields', $roomTypeDisplayData); ?>
                    </div>

                </div><!-- /sr-booking-section -->

            </div><!-- /sr-info-col -->

        </article>

        <?php if ($this->config->get('availability_calendar_enable', 1)): ?>
        <div class="availability-calendar" id="availability-calendar-<?php echo $roomType->id ?>" style="display: none;"></div>
        <?php endif; ?>

        <?php
        if (SRPlugin::isEnabled('flexsearch')):
            $layout->addIncludePath(SRPlugin::getLayoutPath('flexsearch'));
            echo $layout->render('roomtype.flexsearch', $roomTypeDisplayData);
        endif;

        if (!$this->isFresh):
            echo $layout->render('asset.rateplans', $roomTypeDisplayData);
        endif;
        ?>

        </div><!-- /room_type_row -->

        <?php
    endforeach; // roomTypes

else: // no room types
    ?>
    <div class="alert alert-warning" style="max-width: 80rem; margin: 2rem auto; padding: 0 1rem;">
        <?php
        if ($this->isFresh):
            echo Text::_('SR_NO_ROOM_TYPES');
        else:
            echo Text::sprintf(
                'SR_NO_ROOM_TYPES_MATCHED_SEARCH_CONDITIONS',
                $this->checkinFormatted,
                $this->checkoutFormatted
            ) . ' <a href="' . $this->resetLink . '"><i class="fa fa-sync"></i> ' . Text::_('SR_SEARCH_RESET') . '</a>';
        endif;
        ?>
    </div>
    <?php
endif; // roomTypeCount

?>

    <input type="hidden" name="jform[raid]"      value="<?php echo $this->item->id ?>" />
    <input type="hidden" name="jform[next_step]" value="guestinfo" />
    <input type="hidden" name="jform[return]"    value="<?php echo base64_encode(\Joomla\CMS\Uri\Uri::getInstance()->toString()); ?>" />
    <?php echo HTMLHelper::_('form.token'); ?>

</form>

</div><!-- /reservation-single-step-holder -->
</div><!-- /step-pane #step1 -->

<div class="step-pane" id="step2">
    <div class="reservation-single-step-holder guestinfo nodisplay" id="guestinfo"></div>
</div>

<div class="step-pane" id="step3">
    <div class="reservation-single-step-holder confirmation nodisplay"></div>
</div>


</div><!-- /step-content -->
