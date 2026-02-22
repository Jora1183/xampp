<?php
$mysqli = new mysqli("localhost", "root", "", "joomla_db");
if ($mysqli->connect_errno) {
    die("Failed to connect to MySQL: " . $mysqli->connect_error);
}

// Read HTML content
$html_path = 'C:/Users/vyugo/.gemini/antigravity/brain/ae417ede-03f7-437c-854c-964caf9d5d20/footer_module_code.html';
$content = file_get_contents($html_path);
if (!$content) {
    die("Could not read HTML file at $html_path");
}

// Prepare update
$stmt = $mysqli->prepare("UPDATE r4g29_modules SET content = ? WHERE title = 'Footer' AND client_id = 0");
$stmt->bind_param("s", $content);
$stmt->execute();

echo "Rows updated: " . $stmt->affected_rows . "\n";
if ($stmt->affected_rows === 0) {
    // Check if module exists at all
    $res = $mysqli->query("SELECT id FROM r4g29_modules WHERE title = 'Footer'");
    if ($res->num_rows == 0) {
        echo "ERROR: Could not find a module named 'Footer'. Please ensure you created it with that exact title.\n";
    } else {
        echo "Info: Module found but content was identical (no changes made).\n";
    }
} else {
    echo "SUCCESS: Footer module content updated directly in database.\n";
}
?>