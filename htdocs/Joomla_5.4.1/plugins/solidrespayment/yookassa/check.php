<?php
/**
 * YooKassa Plugin Diagnostic Tool
 * 
 * This script checks if YooKassa plugin is properly installed and configured
 * Access: http://localhost/Joomla_5.4.1/plugins/solidrespayment/yookassa/check.php
 */

define('_JEXEC', 1);
define('JPATH_BASE', realpath(dirname(__FILE__) . '/../../../'));

require_once JPATH_BASE . '/configuration.php';

$config = new JConfig();
$db = new mysqli($config->host, $config->user, $config->password, $config->db);

if ($db->connect_error) {
    die('<h2>❌ Database Connection Failed</h2>');
}

$db->set_charset('utf8mb4');
$prefix = $config->dbprefix;

echo '<!DOCTYPE html>
<html>
<head>
    <title>YooKassa Diagnostic</title>
    <style>
        body { font-family: Arial, sans-serif; max-width: 900px; margin: 50px auto; padding: 20px; }
        h2 { color: #333; border-bottom: 2px solid #007bff; padding-bottom: 10px; }
        .ok { color: green; font-weight: bold; }
        .error { color: red; font-weight: bold; }
        .warning { color: orange; font-weight: bold; }
        table { width: 100%; border-collapse: collapse; margin: 20px 0; }
        th, td { border: 1px solid #ddd; padding: 12px; text-align: left; }
        th { background-color: #f5f5f5; font-weight: bold; }
        code { background: #f4f4f4; padding: 2px 6px; border-radius: 3px; }
        pre { background: #f4f4f4; padding: 15px; border-radius: 5px; overflow-x: auto; }
        .section { margin: 30px 0; }
    </style>
</head>
<body>';

echo '<h1>🔍 YooKassa Plugin Diagnostic</h1>';

// Check 1: Is plugin registered in database?
echo '<div class="section">';
echo '<h2>1. Plugin Registration</h2>';
$query = "SELECT extension_id, name, enabled, element, folder FROM `{$prefix}extensions` 
          WHERE element = 'yookassa' AND folder = 'solidrespayment'";
$result = $db->query($query);

if ($result && $row = $result->fetch_assoc()) {
    echo '<p class="ok">✅ YooKassa plugin IS registered in database</p>';
    echo '<table>';
    echo '<tr><th>Extension ID</th><td>' . $row['extension_id'] . '</td></tr>';
    echo '<tr><th>Name</th><td>' . $row['name'] . '</td></tr>';
    echo '<tr><th>Enabled</th><td>' . ($row['enabled'] ? '<span class="ok">Yes</span>' : '<span class="error">NO - Plugin is DISABLED!</span>') . '</td></tr>';
    echo '<tr><th>Element</th><td>' . $row['element'] . '</td></tr>';
    echo '<tr><th>Folder</th><td>' . $row['folder'] . '</td></tr>';
    echo '</table>';

    if (!$row['enabled']) {
        echo '<p class="error">⚠️ <strong>ACTION REQUIRED:</strong> Go to Joomla Admin → System → Plugins → Find "YooKassa" → Enable it</p>';
    }
} else {
    echo '<p class="error">❌ YooKassa plugin is NOT registered in database</p>';
    echo '<p>Please run the installation script: <a href="install.php">install.php</a></p>';
}
echo '</div>';

// Check 2: Are plugin files present?
echo '<div class="section">';
echo '<h2>2. Plugin Files</h2>';
$files = [
    'yookassa.php' => 'Main plugin file',
    'yookassa.xml' => 'Plugin manifest',
    'form/yookassa.xml' => 'Configuration form',
    'language/en-GB/en-GB.plg_solidrespayment_yookassa.ini' => 'English language file',
    'language/ru-RU/ru-RU.plg_solidrespayment_yookassa.ini' => 'Russian language file',
];

echo '<table>';
echo '<tr><th>File</th><th>Status</th></tr>';
foreach ($files as $file => $desc) {
    $exists = file_exists(__DIR__ . '/' . $file);
    $status = $exists ? '<span class="ok">✅ Exists</span>' : '<span class="error">❌ Missing</span>';
    echo '<tr><td>' . $desc . '<br><code>' . $file . '</code></td><td>' . $status . '</td></tr>';
}
echo '</table>';
echo '</div>';

// Check 3: All Solidres payment plugins
echo '<div class="section">';
echo '<h2>3. All Solidres Payment Plugins</h2>';
$query = "SELECT extension_id, name, element, enabled FROM `{$prefix}extensions` 
          WHERE folder = 'solidrespayment' AND type = 'plugin' 
          ORDER BY element";
$result = $db->query($query);

if ($result && $result->num_rows > 0) {
    echo '<table>';
    echo '<tr><th>ID</th><th>Plugin Name</th><th>Element</th><th>Status</th></tr>';
    while ($row = $result->fetch_assoc()) {
        $status = $row['enabled'] ? '<span class="ok">Enabled</span>' : '<span class="warning">Disabled</span>';
        $highlight = ($row['element'] == 'yookassa') ? ' style="background-color: #ffffcc;"' : '';
        echo '<tr' . $highlight . '>';
        echo '<td>' . $row['extension_id'] . '</td>';
        echo '<td>' . $row['name'] . '</td>';
        echo '<td><code>' . $row['element'] . '</code></td>';
        echo '<td>' . $status . '</td>';
        echo '</tr>';
    }
    echo '</table>';
} else {
    echo '<p class="warning">⚠️ No Solidres payment plugins found</p>';
}
echo '</div>';

// Check 4: YooKassa configuration for assets
echo '<div class="section">';
echo '<h2>4. YooKassa Configuration (Per Asset)</h2>';
$query = "SELECT scope_id, data_key, data_value FROM `{$prefix}sr_config_data` 
          WHERE data_key LIKE 'payments/yookassa/%' 
          ORDER BY scope_id, data_key";
$result = $db->query($query);

if ($result && $result->num_rows > 0) {
    echo '<p class="ok">✅ YooKassa configuration found</p>';
    echo '<table>';
    echo '<tr><th>Asset ID</th><th>Setting</th><th>Value</th></tr>';
    while ($row = $result->fetch_assoc()) {
        $key = str_replace('payments/yookassa/', '', $row['data_key']);
        // Hide sensitive data
        $value = ($key == 'yookassa_secret_key') ? '***HIDDEN***' : $row['data_value'];
        $highlight = ($key == 'yookassa_enabled' && $row['data_value'] == '1') ? ' class="ok"' : '';
        echo '<tr' . $highlight . '>';
        echo '<td>' . $row['scope_id'] . '</td>';
        echo '<td><code>' . $key . '</code></td>';
        echo '<td>' . $value . '</td>';
        echo '</tr>';
    }
    echo '</table>';
} else {
    echo '<p class="error">❌ No YooKassa configuration found for any assets</p>';
    echo '<p><strong>ACTION REQUIRED:</strong> Go to Solidres → Assets → Edit → Payments tab → Configure YooKassa</p>';
}
echo '</div>';

// Summary
echo '<div class="section">';
echo '<h2>📋 Summary & Next Steps</h2>';

$issues = [];
if (!isset($row) || !$row) {
    $issues[] = 'Plugin is not registered - run <a href="install.php">install.php</a>';
} elseif (!$row['enabled']) {
    $issues[] = 'Plugin is disabled - enable it in Plugins manager';
}

$configResult = $db->query("SELECT COUNT(*) as count FROM `{$prefix}sr_config_data` WHERE data_key = 'payments/yookassa/yookassa_enabled' AND data_value = '1'");
$configRow = $configResult->fetch_assoc();
if ($configRow['count'] == 0) {
    $issues[] = 'YooKassa is not enabled for any assets - configure it in Solidres';
}

if (empty($issues)) {
    echo '<p class="ok" style="font-size: 18px;">✅ Everything looks good! YooKassa should be working.</p>';
    echo '<p>If it still doesn\'t show on the booking page, try:</p>';
    echo '<ol>';
    echo '<li>Clear Joomla cache (System → Clear Cache)</li>';
    echo '<li>Make sure you\'re editing the correct asset</li>';
    echo '<li>Check that visibility is set to "Frontend and Backend" or "Frontend Only"</li>';
    echo '</ol>';
} else {
    echo '<p class="error">⚠️ Issues found:</p>';
    echo '<ol>';
    foreach ($issues as $issue) {
        echo '<li>' . $issue . '</li>';
    }
    echo '</ol>';
}
echo '</div>';

$db->close();

echo '<hr>';
echo '<p style="text-align: center; color: #666;">Delete this file (check.php) after troubleshooting</p>';
echo '</body></html>';
?>