<?php
$mysqli = new mysqli('localhost', 'root', '', 'joomla_db');
$res = $mysqli->query("SELECT m.id, m.title, m.link, m.alias, m.path FROM r4g29_menu m WHERE m.link LIKE '%solidres%' AND m.published=1 ORDER BY m.id");
while($row = $res->fetch_assoc()) {
  echo $row['id'] . ' | ' . $row['title'] . ' | ' . $row['link'] . ' | path=' . $row['path'] . "\n";
}
