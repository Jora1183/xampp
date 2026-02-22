<?php
$url = 'http://localhost/Joomla_5.4.1/ru';
// Suppress warnings specifically for file_get_contents to handle errors manually
$html = @file_get_contents($url);

if ($html === false) {
    $error = error_get_last();
    echo "Failed to fetch URL: " . ($error['message'] ?? 'Unknown error') . "\n";
    exit(1);
}

$checks = [
    'Hero Container (container-hero)' => 'container-hero',
    'Footer Container (container-footer)' => 'container-footer',
    'Main Component (container-component)' => 'container-component',
    'Footer Content (ParkSide)' => 'ParkSide',
    'Header (header-inner)' => 'header-inner'
];

echo "Verification Results for $url:\n";
$all_ok = true;
foreach ($checks as $name => $needle) {
    if (strpos($html, $needle) !== false) {
        echo "[OK] $name found.\n";
    } else {
        echo "[FAIL] $name NOT found.\n";
        $all_ok = false;
    }
}

if ($all_ok) {
    echo "\nSUCCESS: All layout blocks validated.\n";
} else {
    echo "\nWARNING: Some layout blocks are missing.\n";
}
?>