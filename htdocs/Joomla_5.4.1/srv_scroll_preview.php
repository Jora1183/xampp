<?php
$y = (int)($_GET['y'] ?? 0);
?><!DOCTYPE html>
<html lang="ru"><head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<script src="https://cdn.tailwindcss.com?plugins=forms"></script>
<script>tailwind.config={theme:{extend:{colors:{'resort-green':'#5c7c3b','resort-green-hover':'#4a632f','resort-light-gray':'#f9f9f9','resort-dark':'#1a1a1a'},fontFamily:{sans:['Lato','sans-serif'],serif:['Playfair Display','serif']}}}}</script>
<link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;700&family=Lato:wght@300;400;700&display=swap" rel="stylesheet">
<link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet">
<style>html{scroll-behavior:smooth}body{font-family:'Lato',sans-serif;margin:0;padding:0;background:#f0f0f0;}
.outer{width:1280px;height:900px;overflow:hidden;position:relative;}
.inner{position:absolute;top:-<?php echo $y ?>px;left:0;width:1280px;background:white;}
</style></head><body>
<div class="outer"><div class="inner"><?php include __DIR__.'/services_content.html'; ?></div></div>
</body></html>
