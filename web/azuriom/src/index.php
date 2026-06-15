<?php
$date = new DateTimeImmutable('now', new DateTimeZone('Europe/London'));
$phpVersion = PHP_VERSION;
$serverName = $_SERVER['SERVER_NAME'] ?? 'unknown';
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>PHP + Nginx Demo</title>
  <style>
    body { font-family: system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif; margin: 0; background: #f7f7fb; color: #1f2937; }
    main { max-width: 760px; margin: 6rem auto; background: white; padding: 2rem 2.25rem; border-radius: 1rem; box-shadow: 0 10px 30px rgba(0,0,0,.08); }
    code { background: #eef2ff; padding: 0.15rem 0.35rem; border-radius: 0.35rem; }
    h1 { margin-top: 0; }
    .meta { margin-top: 1rem; line-height: 1.7; }
  </style>
</head>
<body>
  <main>
    <h1>Hello from PHP running behind Nginx</h1>
    <p>This page is served by Nginx and rendered by PHP-FPM in Docker Compose.</p>
    <div class="meta">
      <div><strong>PHP version:</strong> <code><?= htmlspecialchars($phpVersion, ENT_QUOTES, 'UTF-8') ?></code></div>
      <div><strong>Server name:</strong> <code><?= htmlspecialchars($serverName, ENT_QUOTES, 'UTF-8') ?></code></div>
      <div><strong>London time:</strong> <code><?= htmlspecialchars($date->format(DATE_ATOM), ENT_QUOTES, 'UTF-8') ?></code></div>
    </div>
  </main>
</body>
</html>
