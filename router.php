<?php
$path=parse_url($_SERVER['REQUEST_URI'],PHP_URL_PATH) ?: '/';
$file=__DIR__.$path;
if($path!=='/' && is_file($file)) return false;
if(str_starts_with($path,'/admin')) { require __DIR__.'/admin/index.php'; return true; }
if($path==='/robots.txt') { require __DIR__.'/robots.php'; return true; }
if($path==='/sitemap.xml') { require __DIR__.'/sitemap.php'; return true; }
require __DIR__.'/index.php';
