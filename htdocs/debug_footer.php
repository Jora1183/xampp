<?php
$url = 'http://localhost/Joomla_5.4.1/ru';
$html = file_get_contents($url);

if (strpos($html, '&lt;footer') !== false) {
    echo "DETECTED: Footer HTML is escaped (&lt;footer).\n";
    echo "Cause: User likely pasted code into Visual Editor.\n";
} elseif (strpos($html, '<footer') !== false) {
    echo "DETECTED: Footer HTML is NOT escaped (<footer).\n";
    echo "Cause: Rendering seems correct in source. CSS might be missing or broken.\n";
} else {
    echo "DETECTED: Footer tag not found.\n";
}

// Print a snippet to be sure
$start = strpos($html, 'footer');
if ($start !== false) {
    echo "\nSnippet:\n" . substr($html, $start - 20, 100) . "\n";
}
?>