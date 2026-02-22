<?php
/**
 * YooKassa Plugin Installation Helper (Simplified)
 * 
 * This script manually registers the YooKassa payment plugin in Joomla's database.
 * 
 * USAGE:
 * 1. Access this file in your browser: http://localhost/Joomla_5.4.1/plugins/solidrespayment/yookassa/install.php
 * 2. The script will register the plugin in the database
 * 3. Delete this file after successful installation
 */

// Load Joomla configuration
define('_JEXEC', 1);
define('JPATH_BASE', realpath(dirname(__FILE__) . '/../../../'));

require_once JPATH_BASE . '/configuration.php';

// Create configuration object
$config = new JConfig();

// Connect to database
$db = new mysqli(
    $config->host,
    $config->user,
    $config->password,
    $config->db
);

// Check connection
if ($db->connect_error) {
    die('<h2>❌ Database Connection Failed</h2><p>Error: ' . $db->connect_error . '</p>');
}

$db->set_charset('utf8mb4');

// Table prefix
$prefix = $config->dbprefix;

// Check if plugin already exists
$query = "SELECT extension_id FROM `{$prefix}extensions` 
          WHERE `type` = 'plugin' 
          AND `element` = 'yookassa' 
          AND `folder` = 'solidrespayment'";

$result = $db->query($query);
$existingId = $result ? $result->fetch_row() : null;
$result->close();

if ($existingId) {
    echo '<h2>✅ Plugin Already Installed</h2>';
    echo '<p>YooKassa plugin (ID: ' . $existingId[0] . ') is already registered in the database.</p>';
    echo '<p><a href="administrator/index.php?option=com_plugins&view=plugins&filter[folder]=solidrespayment">View in Plugin Manager</a></p>';
    $db->close();
    exit;
}

// Create manifest cache
$manifestCache = json_encode([
    'name' => 'plg_solidrespayment_yookassa',
    'type' => 'plugin',
    'creationDate' => 'January 2026',
    'author' => 'Custom Development',
    'copyright' => 'Copyright (C) 2026. All Rights Reserved.',
    'authorEmail' => 'contact@example.com',
    'authorUrl' => 'https://example.com',
    'version' => '1.0.0',
    'description' => 'PLG_SOLIDRESPAYMENT_YOOKASSA_XML_DESCRIPTION',
    'group' => '',
    'filename' => 'yookassa'
]);

// Escape values
$manifestCache = $db->real_escape_string($manifestCache);

// Insert plugin record
$query = "INSERT INTO `{$prefix}extensions` (
    `package_id`,
    `name`,
    `type`,
    `element`,
    `folder`,
    `client_id`,
    `enabled`,
    `access`,
    `protected`,
    `locked`,
    `manifest_cache`,
    `params`,
    `custom_data`,
    `checked_out`,
    `checked_out_time`,
    `ordering`,
    `state`
) VALUES (
    0,
    'plg_solidrespayment_yookassa',
    'plugin',
    'yookassa',
    'solidrespayment',
    0,
    1,
    1,
    0,
    0,
    '{$manifestCache}',
    '{}',
    '',
    0,
    NULL,
    0,
    0
)";

if ($db->query($query)) {
    $extensionId = $db->insert_id;

    echo '<h2>✅ Installation Successful!</h2>';
    echo '<p>YooKassa payment plugin has been registered successfully.</p>';
    echo '<p><strong>Extension ID:</strong> ' . $extensionId . '</p>';
    echo '<hr>';
    echo '<h3>Next Steps:</h3>';
    echo '<ol>';
    echo '<li><strong>Delete this file</strong> (install.php) for security</li>';
    echo '<li>Go to <a href="administrator/index.php?option=com_plugins&view=plugins&filter[folder]=solidrespayment" target="_blank">Plugin Manager</a></li>';
    echo '<li>Find "Solidres - YooKassa Payment Plugin"</li>';
    echo '<li>Verify it is <strong>enabled</strong> (it should be enabled by default)</li>';
    echo '<li>Configure the plugin in Solidres → Assets → Edit → Payments tab</li>';
    echo '</ol>';
    echo '<hr>';
    echo '<p><a href="administrator/" class="btn btn-primary">Go to Joomla Admin</a></p>';

} else {
    echo '<h2>❌ Installation Failed</h2>';
    echo '<p><strong>Error:</strong> ' . $db->error . '</p>';
    echo '<p>Please check database permissions and try again.</p>';
}

$db->close();
?>
<!DOCTYPE html>
<html>

<head>
    <title>YooKassa Plugin Installation</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 800px;
            margin: 50px auto;
            padding: 20px;
            background: #f5f5f5;
        }

        h2 {
            color: #28a745;
        }

        h2:first-child {
            margin-top: 0;
        }

        .btn {
            display: inline-block;
            padding: 10px 20px;
            background: #007bff;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            margin-top: 20px;
        }

        .btn:hover {
            background: #0056b3;
        }

        ol,
        p {
            line-height: 1.6;
        }

        a {
            color: #007bff;
        }
    </style>
</head>

<body>
</body>

</html>