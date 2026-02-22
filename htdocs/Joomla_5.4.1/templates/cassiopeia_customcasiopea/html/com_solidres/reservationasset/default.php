<?php
/**
 * Custom template override for Solidres reservationasset view.
 * Showcase layout: full-width, no sidebar, with page header + anchor nav.
 *
 * Override path (correct):
 * templates/cassiopeia_customcasiopea/html/com_solidres/reservationasset/default.php
 */

defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Uri\Uri;

$app    = Factory::getApplication();
$menuId = '&Itemid=' . $app->input->get('Itemid', '', 'uint');

SRHtml::_('venobox');

// Load custom CSS
$doc = Factory::getDocument();
$doc->addStyleSheet(Uri::root() . 'templates/cassiopeia_customcasiopea/css/solidres-custom.css');
?>

<style>
  /* ============================================================
     Showcase page styles — scoped to this component view
  ============================================================ */

  /* Remove default Solidres/Bootstrap container padding */
  #solidres.reservation_asset_default { padding: 0; }
  #solidres .reservation_asset_item { padding: 0; }

  /* Gallery panel */
  .sr-showcase-gallery { overflow: hidden; }
  .sr-showcase-gallery img {
    width: 100%; height: 100%; object-fit: cover;
    transition: opacity 0.2s ease;
  }

  /* Thumbnail strip */
  .sr-thumb-strip {
    display: flex; gap: 4px; overflow-x: auto;
    scroll-snap-type: x mandatory;
    scrollbar-width: thin; scrollbar-color: #5c7c3b transparent;
    padding: 6px; background: #f3f4f6;
  }
  .sr-thumb-strip::-webkit-scrollbar { height: 4px; }
  .sr-thumb-strip::-webkit-scrollbar-thumb { background: #5c7c3b; border-radius: 2px; }
  .sr-thumb-btn {
    flex: 0 0 80px; height: 60px; overflow: hidden; border-radius: 4px;
    scroll-snap-align: start; cursor: pointer; border: 2px solid transparent;
    transition: border-color 0.2s; padding: 0; background: none;
  }
  .sr-thumb-btn img { width: 100%; height: 100%; object-fit: cover; display: block; }
  .sr-thumb-btn.active, .sr-thumb-btn:hover { border-color: #5c7c3b; }

  /* Anchor nav */
  .sr-anchor-nav {
    position: sticky; top: 70px; z-index: 40;
    background: white; border-bottom: 1px solid #f0f0f0;
    box-shadow: 0 1px 4px rgba(0,0,0,0.06);
  }
  .sr-anchor-nav a {
    display: inline-block; padding: 0.4rem 1rem; border-radius: 9999px;
    font-size: 0.82rem; font-weight: 700; color: #6b7280;
    white-space: nowrap; transition: background 0.2s, color 0.2s;
    text-decoration: none;
  }
  .sr-anchor-nav a:hover { background: #5c7c3b; color: white; }

  /* Per-house showcase block */
  .sr-house-block { scroll-margin-top: 130px; }

  /* Amenity item */
  .sr-amenity {
    display: flex; align-items: center; gap: 0.5rem;
    font-size: 0.875rem; color: #4b5563;
  }
  .sr-amenity .material-symbols-outlined { font-size: 20px; color: #5c7c3b; flex-shrink: 0; }

  /* Featured badge */
  .sr-rec-badge {
    position: absolute; top: 1rem; right: 1rem; z-index: 10;
    background: #5c7c3b; color: white; padding: 0.25rem 0.75rem;
    border-radius: 9999px; font-size: 0.7rem; font-weight: 700;
    text-transform: uppercase; letter-spacing: 0.08em;
    box-shadow: 0 2px 8px rgba(0,0,0,0.2);
  }

  /* Booking section collapse */
  .sr-booking-section { border-top: 1px solid #e5e7eb; margin-top: 1.5rem; padding-top: 1.25rem; }
  .sr-booking-toggle {
    width: 100%; display: flex; align-items: center; justify-content: space-between;
    padding: 0.75rem 1rem; border: 2px solid #5c7c3b; border-radius: 0.5rem;
    background: white; color: #5c7c3b; font-weight: 700; cursor: pointer;
    transition: background 0.2s, color 0.2s; font-size: 0.95rem;
  }
  .sr-booking-toggle:hover { background: #5c7c3b; color: white; }
  .sr-booking-toggle .toggle-icon { font-size: 1.2rem; transition: transform 0.3s; }
  .sr-booking-toggle.open .toggle-icon { transform: rotate(180deg); }
  .sr-booking-body { display: none; margin-top: 1rem; }
  .sr-booking-body.open { display: block; }

  /* Remove redundant Solidres heading inside room card */
  .sr-house-block .sr-reservation-form-heading { display: none; }

  /* Divider */
  .sr-house-divider { display: flex; align-items: center; gap: 1rem; padding: 0.75rem 2rem; max-width: 80rem; margin: 0 auto; }
  .sr-house-divider .line { flex: 1; height: 1px; background: #e5e7eb; }

  @media (min-width: 1024px) {
    .sr-house-block { min-height: 520px; }
    .sr-house-block .sr-gallery-col { display: flex; flex-direction: column; }
    .sr-showcase-gallery { flex: 1; min-height: 0; }
  }
</style>

<div id="solidres" class="<?php echo SR_UI ?> reservation_asset_default <?php echo SR_LAYOUT_STYLE ?>">

  <?php if (!empty($this->checkin) && !empty($this->checkout)):
    echo SRLayoutHelper::render('asset.booking_summary', [
      'checkinFormatted'  => $this->checkinFormatted,
      'checkoutFormatted' => $this->checkoutFormatted,
      'property'          => $this->item,
    ]);
  endif ?>

  <div class="reservation_asset_item">

    <!-- ====== PAGE HEADER BAND ====== -->
    <div style="background: linear-gradient(180deg, #e8efe0 0%, #f7f8f5 240px); padding: 4rem 1rem 3rem; margin: 0 -1rem;">
      <div style="max-width: 80rem; margin: 0 auto; text-align: center;">
        <p style="font-size: 0.7rem; font-weight: 700; letter-spacing: 0.2em; text-transform: uppercase; color: #5c7c3b; margin-bottom: 0.75rem;">Размещения</p>
        <h1 style="font-family: 'Playfair Display', serif; font-weight: 700; color: #1a1a1a; font-size: 2.75rem; line-height: 1.1; margin-bottom: 1rem;">
          <?php echo $this->escape($this->item->name); ?>
        </h1>
        <div style="height: 3px; width: 56px; background: #5c7c3b; border-radius: 2px; margin: 0 auto 1.25rem;"></div>
        <p style="color: #6c757d; font-size: 1.05rem; max-width: 560px; margin: 0 auto; line-height: 1.7;">
          Выберите идеальный вариант — от уютного домика у озера до роскошной виллы с сауной и бильярдом.
        </p>
      </div>
    </div>

    <!-- ====== ANCHOR NAV ====== -->
    <?php if (!empty($this->item->roomTypes)): ?>
    <nav class="sr-anchor-nav" style="margin: 0 -1rem;">
      <div style="max-width: 80rem; margin: 0 auto; padding: 0.6rem 1rem; display: flex; gap: 0.35rem; overflow-x: auto; -webkit-overflow-scrolling: touch;">
        <?php foreach ($this->item->roomTypes as $rt): ?>
          <a href="#sr-house-<?php echo $rt->id ?>"><?php echo $this->escape($rt->name) ?></a>
        <?php endforeach; ?>
      </div>
    </nav>
    <?php endif; ?>

    <?php echo $this->events->beforeDisplayAssetForm; ?>

    <!-- ====== HOUSE SHOWCASE BLOCKS ====== -->
    <?php echo $this->loadTemplate('roomtype'); ?>

    <!-- ====== CTA BAND ====== -->
    <div style="background: linear-gradient(135deg, #4a632f 0%, #5c7c3b 100%); padding: 4rem 1rem; text-align: center; margin: 0 -1rem;">
      <div style="max-width: 36rem; margin: 0 auto;">
        <h3 style="font-family: 'Playfair Display', serif; font-weight: 700; color: white; font-size: 1.9rem; margin-bottom: 1rem;">Готовы к незабываемому отдыху?</h3>
        <p style="color: #c8e6a0; font-size: 1.05rem; margin-bottom: 2rem;">Выберите дом и проверьте доступность дат прямо на этой странице</p>
      </div>
    </div>

    <?php if (!$this->isAmending): ?>
      <?php echo $this->loadTemplate('information'); ?>
    <?php endif; ?>

    <?php echo $this->events->afterDisplayAssetForm; ?>

  </div><!-- /reservation_asset_item -->

</div><!-- /solidres -->

<script>
function srSwapImage(houseId, newSrc, clickedThumb) {
  var img = document.getElementById('sr-main-img-' + houseId);
  if (!img) return;
  img.style.opacity = '0';
  setTimeout(function() { img.src = newSrc; img.style.opacity = '1'; }, 200);
  var strip = document.getElementById('sr-strip-' + houseId);
  if (strip) {
    strip.querySelectorAll('.sr-thumb-btn').forEach(function(b) { b.classList.remove('active'); });
  }
  if (clickedThumb) clickedThumb.classList.add('active');
}

function srToggleBooking(id) {
  var btn  = document.getElementById('sr-toggle-' + id);
  var body = document.getElementById('sr-booking-' + id);
  if (!btn || !body) return;
  var isOpen = body.classList.contains('open');
  btn.classList.toggle('open', !isOpen);
  body.classList.toggle('open', !isOpen);
}
</script>
