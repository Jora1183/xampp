# XAMPP / Joomla Project

## Environment

- **Stack**: XAMPP on Windows 11 (Apache + MySQL + PHP)
- **Joomla**: 5.4.1 at `http://localhost/Joomla_5.4.1/`
- **Admin panel**: `http://localhost/Joomla_5.4.1/administrator/`
- **Custom template**: `cassiopeia_customcasiopea` (in `htdocs/Joomla_5.4.1/templates/`)
- **SolidRes** booking plugin installed

## Playwright MCP Testing

Use the Playwright MCP tool (already configured) to automate browser testing.

### After every code change — always verify with Playwright
1. Navigate to the affected page
2. Take a screenshot and describe what you see
3. Check for layout breaks, missing elements, or JS errors (`browser_console_messages`)
4. If forms are involved, fill and submit them to confirm they still work

### Common test targets
- Homepage / room listing: `http://localhost/Joomla_5.4.1/`
- Single room / booking flow: navigate from homepage
- Admin panel: `http://localhost/Joomla_5.4.1/administrator/`

### Viewports to check
- Desktop: 1280×900 (default)
- Mobile: 375×812

### Key things to verify for this project
- SolidRes room cards render correctly (`default_roomtype.php`)
- Check-in/check-out date picker works
- Availability form submits without errors
- No horizontal scroll on mobile
