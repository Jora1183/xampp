<?php
/**
 * Property content sub-template
 * Contains the main property information and room listings
 */

defined('_JEXEC') or die;

use Joomla\CMS\Language\Text;

// This file should contain the actual Solidres output
// For now, we'll create a structure that wraps the existing Solidres content
?>

<!-- Property Gallery/Media -->
<div class="property-gallery mb-4 rounded overflow-hidden">
    <?php if (!empty($this->item->media)) : ?>
        <?php echo $this->item->media; ?>
    <?php else : ?>
        <div class="bg-secondary text-white d-flex align-items-center justify-content-center" style="height: 400px;">
            <span><i class="fas fa-camera fa-3x"></i></span>
        </div>
    <?php endif; ?>
</div>

<!-- Property Title -->
<?php if (!empty($this->item->name)) : ?>
    <h1 class="property-title"><?php echo $this->escape($this->item->name); ?></h1>
<?php endif; ?>

<!-- Property Rating/Reviews (if available) -->
<?php if (!empty($this->item->rating)) : ?>
    <div class="property-rating mb-3">
        <?php for ($i = 1; $i <= 5; $i++) : ?>
            <i class="fas fa-star<?php echo $i <= $this->item->rating ? '' : '-o'; ?> text-warning"></i>
        <?php endfor; ?>
        <?php if (!empty($this->item->review_count)) : ?>
            <span class="text-muted small ms-2">(<?php echo $this->item->review_count; ?> <?php echo Text::_('SR_REVIEWS'); ?>)</span>
        <?php endif; ?>
    </div>
<?php endif; ?>

<!-- Property Facilities -->
<?php if (!empty($this->item->facilities)) : ?>
    <div class="property-facilities mb-4">
        <?php foreach ($this->item->facilities as $facility) : ?>
            <span>
                <?php if (!empty($facility->icon)) : ?>
                    <i class="<?php echo $facility->icon; ?>"></i>
                <?php endif; ?>
                <?php echo $this->escape($facility->name); ?>
            </span>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<!-- Property Description -->
<?php if (!empty($this->item->description)) : ?>
    <div class="property-description mb-5">
        <h4><?php echo Text::_('SR_ABOUT_THIS_PROPERTY'); ?></h4>
        <div><?php echo $this->item->description; ?></div>
    </div>
<?php endif; ?>

<!-- Room Types / Booking Section -->
<h3 class="mb-4" id="book-form"><?php echo Text::_('SR_AVAILABLE_ROOMS'); ?></h3>

<?php if (!empty($this->roomtypes)) : ?>
    <?php foreach ($this->roomtypes as $roomtype) : ?>
        <div class="sr-room-card" id="srt_<?php echo $roomtype->id; ?>">
            <div class="row g-0">
                <!-- Room Image -->
                <div class="col-md-4">
                    <?php if (!empty($roomtype->main_image)) : ?>
                        <img src="<?php echo $roomtype->main_image; ?>" class="sr-room-img" alt="<?php echo $this->escape($roomtype->name); ?>">
                    <?php else : ?>
                        <div class="sr-room-img bg-light d-flex align-items-center justify-content-center">
                            <i class="fas fa-bed fa-3x text-muted"></i>
                        </div>
                    <?php endif; ?>
                </div>
                
                <!-- Room Details -->
                <div class="col-md-8">
                    <div class="card-body h-100 d-flex flex-column justify-content-between p-4">
                        <div>
                            <h5 class="card-title"><?php echo $this->escape($roomtype->name); ?></h5>
                            
                            <!-- Room Info -->
                            <p class="card-text small text-muted mb-2">
                                <?php if (!empty($roomtype->occupancy_adult)) : ?>
                                    <i class="fas fa-user me-1"></i> 
                                    <?php echo Text::_('SR_MAX'); ?> <?php echo $roomtype->occupancy_adult; ?> 
                                    <?php echo Text::_('SR_ADULTS'); ?>
                                <?php endif; ?>
                                <?php if (!empty($roomtype->occupancy_child)) : ?>
                                    • <?php echo $roomtype->occupancy_child; ?> <?php echo Text::_('SR_CHILDREN'); ?>
                                <?php endif; ?>
                                <?php if (!empty($roomtype->room_size)) : ?>
                                    • <i class="fas fa-ruler-combined me-1"></i> <?php echo $roomtype->room_size; ?>m²
                                <?php endif; ?>
                            </p>
                            
                            <!-- Room Description (Short) -->
                            <?php if (!empty($roomtype->short_description)) : ?>
                                <p class="card-text small">
                                    <?php echo $roomtype->short_description; ?>
                                </p>
                            <?php endif; ?>
                            
                            <!-- Room Facilities -->
                            <?php if (!empty($roomtype->facilities)) : ?>
                                <div class="room-facilities small text-muted mt-2">
                                    <?php foreach (array_slice($roomtype->facilities, 0, 4) as $facility) : ?>
                                        <span class="me-2">
                                            <?php if (!empty($facility->icon)) : ?>
                                                <i class="<?php echo $facility->icon; ?>"></i>
                                            <?php endif; ?>
                                            <?php echo $facility->name; ?>
                                        </span>
                                    <?php endforeach; ?>
                                </div>
                            <?php endif; ?>
                        </div>
                        
                        <!-- Price and Booking -->
                        <div class="d-flex justify-content-between align-items-end mt-3 pt-3 border-top">
                            <div>
                                <?php if (!empty($roomtype->w_tax_from_price_original) && $roomtype->w_tax_from_price_original > $roomtype->w_tax_from_price) : ?>
                                    <div class="text-decoration-line-through text-muted small">
                                        <?php echo $roomtype->w_tax_from_price_original_format; ?>
                                    </div>
                                <?php endif; ?>
                                <div class="sr-price-tag">
                                    <?php echo $roomtype->w_tax_from_price_format ?? '$0'; ?>
                                    <small class="fs-6 text-muted fw-normal">
                                        / <?php echo Text::_('SR_NIGHT'); ?>
                                    </small>
                                </div>
                                <?php if (!empty($roomtype->tax_label)) : ?>
                                    <div class="small text-muted"><?php echo $roomtype->tax_label; ?></div>
                                <?php endif; ?>
                            </div>
                            
                            <!-- Booking Button/Form -->
                            <div>
                                <?php echo $roomtype->booking_form ?? '<button class="btn btn-success">' . Text::_('SR_BOOK_NOW') . '</button>'; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
<?php else : ?>
    <div class="alert alert-info">
        <i class="fas fa-info-circle me-2"></i>
        <?php echo Text::_('SR_NO_ROOMS_AVAILABLE'); ?>
    </div>
<?php endif; ?>
