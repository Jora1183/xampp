<?php // Standalone preview — no Joomla context needed ?>
<!DOCTYPE html>
<html lang="ru">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Preview: Услуги — Дивная Усадьба</title>

  <!-- Tailwind CSS (same CDN as main template) -->
  <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
  <script>
    tailwind.config = {
      theme: {
        extend: {
          colors: {
            'resort-green': '#5c7c3b',
            'resort-green-hover': '#4a632f',
            'resort-light-gray': '#f9f9f9',
            'resort-dark': '#1a1a1a',
          },
          fontFamily: {
            sans: ['Lato', 'sans-serif'],
            serif: ['Playfair Display', 'serif'],
          }
        }
      }
    }
  </script>

  <!-- Google Fonts -->
  <link href="https://fonts.googleapis.com" rel="preconnect">
  <link crossorigin href="https://fonts.gstatic.com" rel="preconnect">
  <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;700&family=Lato:wght@300;400;700&display=swap" rel="stylesheet">

  <!-- Material Symbols Outlined -->
  <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet">

  <style>
    html { scroll-behavior: smooth; }
    body { font-family: 'Lato', sans-serif; margin: 0; padding: 0; background: #f0f0f0; }
    .preview-wrap { max-width: 1280px; margin: 0 auto; background: white; overflow: hidden; }
    /* Simulate the 1rem padding the Joomla component wrapper adds */
    .preview-inner { padding: 0 1rem; }
  </style>
</head>
<body>
  <div class="preview-wrap">
    <div class="preview-inner">
      <?php include __DIR__ . '/services_content.html'; ?>
    </div>
  </div>
</body>
</html>
