# Copilot Instructions - XAMPP Joomla Development Environment

## Project Overview

This is a **local XAMPP development environment** hosting a **Joomla 5.4.1 CMS** website (divnayausadba) with **Solidres** accommodation booking extension. The site uses the **Cassiopeia template** with custom modifications for a booking/hotel business.

**Project Paths:**
- Root: `c:\xampp\htdocs\`
- Joomla: `c:\xampp\htdocs\Joomla_5.4.1\`
- Access URLs:
  - Frontend: `http://localhost/Joomla_5.4.1/`
  - Admin: `http://localhost/Joomla_5.4.1/administrator`

## Architecture & Components

### Core Stack
- **CMS**: Joomla 5.4.1 (PHP-based CMS using MVC pattern)
- **Server**: Apache (via XAMPP on Windows)
- **Database**: MySQL (`joomla_db`, prefix: `r4g29_`)
- **Template**: Cassiopeia (Joomla's default, customized)
- **Languages**: Multilingual setup (Russian `ru-RU` + English `en-GB`)

### Key Extensions
- **Solidres** (`com_solidres`): Accommodation booking system with modules:
  - `mod_sr_checkavailability`: Availability search/booking form
  - `mod_sr_currency`: Currency selector
  - `mod_sr_summary`: Booking summary
- **Custom modules**: Hero section with booking widget

### Template Structure
- **Active template**: `templates/cassiopeia/`
- **Custom CSS**: `templates/cassiopeia/css/user.css` (hero module styling with background images)
- **Template positions**: topbar, menu, banner, main-top, sidebar-left/right, footer, etc.
- **Override strategy**: Template overrides go in `templates/cassiopeia/html/[component|module]/`

## Development Workflows

### Configuration Management
- **Main config**: `Joomla_5.4.1/configuration.php` (JConfig class with public properties)
- **Backups**: Automated backups to `configuration_backup.php` and timestamped versions
- **Language switching**: Modify `$language` property in configuration.php or use batch scripts

### Language Setup
- **Installation**: Language packs in `language/[lang-CODE]/`
- **Admin translations**: `administrator/language/[lang-CODE]/`
- **Automation**: Use `set_russian_language.bat` for quick language switching (Windows batch script)

### Module Customization Pattern
1. Find module in admin (`Extensions → Modules`)
2. Add module class suffix in "Advanced" tab (e.g., `hero-module`)
3. Define CSS for that class in `templates/cassiopeia/css/user.css`
4. For hero backgrounds: Use class selector + `background-image: url('../images/filename.jpg')`
5. Clear Joomla cache: `System → Clear Cache` after changes

### Debugging
- **Debug mode**: Enabled in configuration.php (`public $debug = true`)
- **Error reporting**: Set to 'maximum'
- **Debug language constants**: `debug_lang_const = true`
- Always clear cache after code/config changes

## Joomla-Specific Conventions

### File Organization
- **Components**: `components/com_[name]/` (frontend) + `administrator/components/com_[name]/` (backend)
- **Modules**: `modules/mod_[name]/` (site) or `administrator/modules/mod_[name]/` (admin)
- **Plugins**: `plugins/[group]/[name]/`
- **Templates**: `templates/[name]/` (site) or `administrator/templates/[name]/` (admin)

### Naming Conventions
- Database prefix in queries: Always use `$db->getPrefix()` or `#__` placeholder
- Module names: `mod_[descriptive_name]`
- Component names: `com_[name]`
- Class namespacing: `Joomla\CMS\[Category]\ClassName` (PSR-4 autoloading)

### Extension Entry Points
- **Module**: Main file is `mod_[name].php`, requires `defined('_JEXEC') or die;` security check
- **Component**: Entry via router, uses Model-View-Controller pattern
- **Always use Joomla APIs**: Factory, Language\Text, HTMLHelper, WebAssetManager, etc.

### Asset Management
- **WebAssetManager**: Use `$wa = Factory::getApplication()->getDocument()->getWebAssetManager()`
- Scripts/styles: Register in `joomla.asset.json` or via `$wa->useScript()` / `$wa->useStyle()`
- Example: Solidres loads jQuery UI via `$wa->useScript('com_solidres.jquery-ui')`

## Common Tasks

### Adding Hero Background Images
1. Place image in `Joomla_5.4.1/images/hero-background.jpg`
2. Target module with class `.moduletable.hero-module` in user.css
3. Apply: `background-image: url('../images/hero-background.jpg')` with cover/center positioning
4. See: `templates/cassiopeia/css/user.css` lines 1-42 for reference implementation

### Creating Template Overrides
1. Navigate to: `System → Templates → Site Templates → Cassiopeia Details`
2. Click "Create Overrides" tab
3. Select component/module to override
4. Edit override in `templates/cassiopeia/html/[extension]/`

### Database Queries
- Use Joomla's DatabaseDriver: `$db = Factory::getContainer()->get('DatabaseDriver')`
- Prepared statements: `$query = $db->getQuery(true)->select(...)->from($db->quoteName('#__table'))`
- Table prefix: `#__` auto-replaces with `r4g29_`

### Cache Clearing
**Critical after any changes:**
- Admin panel: `System → Clear Cache → Check All → Delete`
- Or delete files in `administrator/cache/` and `cache/` directories

## Project-Specific Patterns

### Solidres Booking Flow (Detailed)

**Architecture Overview:**
- **Context**: `com_solidres.reservation.process` - Central state management key
- **Controller**: `SolidresControllerReservation` extends `SolidresControllerReservationBase`
- **Entry Point**: `components/com_solidres/solidres.php` dispatches to controller
- **State Storage**: Session-based via `$app->getUserState()` / `setUserState()`

**Booking Process Steps:**

1. **Search/Availability Check** (`mod_sr_checkavailability`)
   - User inputs: checkin, checkout, room occupancy (adults/children per room)
   - State keys set:
     - `$context.checkin` / `$context.checkout`
     - `$context.room_opt` (array of occupancy options per room)
     - `$context.prioritizing_room_type_id` (optional specific room type)
   - Date validation: min/max days in advance, allowed checkin days, min length of stay
   - Module IDs: 110, 113 (with hero background styling)

2. **Room Selection** (`view=reservationasset`)
   - Queries `SolidresModelReservationAsset` for available room types
   - Calculates pricing based on:
     - Tariffs (pricing rules with date ranges)
     - Occupancy (adult/child counts, age-based pricing)
     - Tax inclusion/exclusion (`show_price_with_tax`)
     - Currency conversion (`SRCurrency` helper)
   - State updated: `$context.room.room_types`, `$context.room.raid` (reservation asset ID)

3. **Guest Information** (`task=reservation.step2`)
   - Collects: customer name, email, phone, special requests
   - Optional extras: per-room or per-booking additional items
   - State keys: `$context.guest.*`, `$context.room.total_extra_price_*`
   - Login requirement check: `$propertyParams['require_user_login']`

4. **Confirmation Review** (`task=reservation.step3`)
   - Aggregates all session data via `prepareSavingData()`
   - Merges states: room, guest, cost, discount, coupon, deposit
   - Calculates final totals with tax and surcharges
   - Displays via `SRLayoutHelper` layout system

5. **Payment & Finalization** (`task=reservation.save` → `task=reservation.finalize`)
   - Saves to `#__sr_reservations` table via `SolidresModelReservation`
   - Payment plugin integration: `plg_solidrespayment_*`
   - Email notifications (customer + admin) if `$sendOutgoingEmails = true`
   - State keys: `$context.state` (reservation status), `$context.payment_status`
   - Booking confirmation state: `$solidresConfig->get('confirm_state', 5)`

**Key State Variables:**
```php
$context = 'com_solidres.reservation.process';
// Core booking data
$app->getUserState($context . '.checkin');           // Y-m-d format
$app->getUserState($context . '.checkout');          // Y-m-d format
$app->getUserState($context . '.room_opt');          // Array: [room_num => ['adults' => N, 'children' => N, 'children_ages' => []]]
$app->getUserState($context . '.room.raid');         // Reservation Asset ID
$app->getUserState($context . '.room.room_types');   // Selected room types with quantities
$app->getUserState($context . '.cost');              // Price breakdown object
$app->getUserState($context . '.guest');             // Customer info array
$app->getUserState($context . '.coupon');            // Applied coupon code
$app->getUserState($context . '.discount');          // Discount details
$app->getUserState($context . '.deposit');           // Deposit amount
$app->getUserState($context . '.id');                // Reservation ID (editing mode)
```

**Database Schema:**
- **Reservations**: `#__sr_reservations` (main booking record)
- **Room Details**: `#__sr_reservation_room_xref` (booked room types)
- **Assets**: `#__sr_reservation_assets` (properties/hotels)
- **Room Types**: `#__sr_room_types` (room categories)
- **Tariffs**: `#__sr_tariffs` (pricing rules)

**Common Patterns:**
- **WebAsset Loading**: `$wa->getRegistry()->addExtensionRegistryFile('com_solidres')`
- **Form Validation**: jQuery Validate plugin (`com_solidres.jquery-validate`)
- **Layout Rendering**: `SRLayoutHelper::getInstance()` for modular template parts
- **Currency Handling**: `SRCurrency` class for multi-currency support
- **Tax Calculations**: Pre-tax vs post-tax discounts via `discount_pre_tax` config
- **Date Operations**: Joomla's `Date` class with timezone support
- **Plugin Triggers**: `onSolidresBeforeDisplayConfirmationForm` for customization

**Debugging Booking Flow:**
- Check session state: `$app->getUserState('com_solidres.reservation.process')`
- Verify room availability queries in `models/reservationasset.php`
- Inspect cost calculations in `controllers/reservationbase.php::prepareSavingData()`
- Language constants: `components/com_solidres/language/[lang]/[lang].com_solidres.ini`

### Multilingual Support
- Site configured for Russian primary (`ru-RU`), English secondary
- Offline message in Russian: "Сайт закрыт на техническое обслуживание"
- Language files follow pattern: `language/ru-RU/[extension].ini`

### Custom Styling Strategy
- **Never modify core template files**
- All customizations go in `templates/cassiopeia/css/user.css`
- Module-specific styles via class suffixes (e.g., `.hero-module`, `#sr-checkavailability-form-110`)
- Overlay patterns for text readability on backgrounds (see user.css lines 14-28)

## Critical Files
- **[configuration.php](Joomla_5.4.1/configuration.php)**: All site settings, database connection, debug mode
- **[user.css](Joomla_5.4.1/templates/cassiopeia/css/user.css)**: Custom template styling (hero module backgrounds)
- **[mod_sr_checkavailability.php](Joomla_5.4.1/modules/mod_sr_checkavailability/mod_sr_checkavailability.php)**: Booking form module logic
- **[templateDetails.xml](Joomla_5.4.1/templates/cassiopeia/templateDetails.xml)**: Template manifest, positions, files

## Constraints & Best Practices
- **Windows environment**: Use backslashes in file paths for OS operations, forward slashes for URLs
- **XAMPP context**: Database host is always `localhost` with no password (dev only!)
- **Security**: Never commit `configuration.php` or expose database credentials
- **Read-only protection**: Check file properties before editing configuration.php
- **Joomla updates**: Use admin panel, not manual file replacement
- **Extension compatibility**: Always verify Joomla 5.x compatibility before installing extensions
