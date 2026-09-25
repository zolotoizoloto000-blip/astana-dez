<?php
$base=(isset($_SERVER['HTTPS'])&&$_SERVER['HTTPS']!=='off'?'https':'http').'://'.($_SERVER['HTTP_HOST']??'localhost');header('Content-Type: text/plain; charset=utf-8');echo "User-agent: *\nAllow: /\nDisallow: /admin/\nDisallow: /install.php\nSitemap: {$base}/sitemap.xml\n";
