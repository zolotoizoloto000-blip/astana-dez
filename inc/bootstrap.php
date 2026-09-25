<?php
declare(strict_types=1);
if (session_status() !== PHP_SESSION_ACTIVE) {
 $secure=(!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off');
 session_set_cookie_params(['lifetime'=>0,'path'=>'/','secure'=>$secure,'httponly'=>true,'samesite'=>'Lax']);
 session_start();
}
require_once __DIR__.'/helpers.php';
$dbDir=dirname(__DIR__).'/data';
if(!is_dir($dbDir)) mkdir($dbDir,0775,true);
$db=new PDO('sqlite:'.$dbDir.'/site.sqlite',null,null,[PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION,PDO::ATTR_DEFAULT_FETCH_MODE=>PDO::FETCH_ASSOC]);
$db->exec('PRAGMA journal_mode=WAL; PRAGMA foreign_keys=ON;');
$db->exec("CREATE TABLE IF NOT EXISTS settings (`key` TEXT PRIMARY KEY,value_ru TEXT NOT NULL,value_kk TEXT NOT NULL);
CREATE TABLE IF NOT EXISTS slides (id INTEGER PRIMARY KEY AUTOINCREMENT,title_ru TEXT NOT NULL,title_kk TEXT NOT NULL,text_ru TEXT NOT NULL,text_kk TEXT NOT NULL,image_path TEXT NOT NULL,sort_order INTEGER NOT NULL DEFAULT 0,active INTEGER NOT NULL DEFAULT 1);
CREATE TABLE IF NOT EXISTS services (id INTEGER PRIMARY KEY AUTOINCREMENT,slug TEXT NOT NULL UNIQUE,title_ru TEXT NOT NULL,title_kk TEXT NOT NULL,summary_ru TEXT NOT NULL,summary_kk TEXT NOT NULL,body_ru TEXT NOT NULL,body_kk TEXT NOT NULL,image_path TEXT NOT NULL,sort_order INTEGER NOT NULL DEFAULT 0,active INTEGER NOT NULL DEFAULT 1);
CREATE TABLE IF NOT EXISTS faqs (id INTEGER PRIMARY KEY AUTOINCREMENT,question_ru TEXT NOT NULL,question_kk TEXT NOT NULL,answer_ru TEXT NOT NULL,answer_kk TEXT NOT NULL,sort_order INTEGER NOT NULL DEFAULT 0,active INTEGER NOT NULL DEFAULT 1);
CREATE TABLE IF NOT EXISTS certificates (id INTEGER PRIMARY KEY AUTOINCREMENT,title_ru TEXT NOT NULL,title_kk TEXT NOT NULL,description_ru TEXT NOT NULL DEFAULT '',description_kk TEXT NOT NULL DEFAULT '',image_path TEXT NOT NULL DEFAULT '',sort_order INTEGER NOT NULL DEFAULT 0,active INTEGER NOT NULL DEFAULT 1);
CREATE TABLE IF NOT EXISTS admins (id INTEGER PRIMARY KEY AUTOINCREMENT,username TEXT NOT NULL UNIQUE,password_hash TEXT NOT NULL,created_at TEXT NOT NULL,last_login TEXT NULL);
CREATE TABLE IF NOT EXISTS leads (id INTEGER PRIMARY KEY AUTOINCREMENT,name TEXT NOT NULL,phone TEXT NOT NULL,service TEXT NOT NULL,object_type TEXT NOT NULL DEFAULT '',area TEXT NOT NULL DEFAULT '',message TEXT NOT NULL,created_at TEXT NOT NULL);");
if((int)$db->query('SELECT COUNT(*) FROM admins')->fetchColumn()===0){
 $seed=require dirname(__DIR__).'/seed.php';
 $st=$db->prepare('INSERT INTO settings (`key`,value_ru,value_kk) VALUES (?,?,?)'); foreach($seed['settings'] as $k=>$v)$st->execute([$k,$v['ru'],$v['kk']]);
 $st=$db->prepare('INSERT INTO slides(title_ru,title_kk,text_ru,text_kk,image_path,sort_order,active) VALUES(?,?,?,?,?,?,1)'); foreach($seed['slides'] as $i=>$v)$st->execute([$v['title']['ru'],$v['title']['kk'],$v['text']['ru'],$v['text']['kk'],$v['image'],$i+1]);
 $st=$db->prepare('INSERT INTO services(slug,title_ru,title_kk,summary_ru,summary_kk,body_ru,body_kk,image_path,sort_order,active) VALUES(?,?,?,?,?,?,?,?,?,1)'); foreach($seed['services'] as $i=>$v)$st->execute([$v['slug'],$v['title']['ru'],$v['title']['kk'],$v['summary']['ru'],$v['summary']['kk'],$v['body']['ru'],$v['body']['kk'],$v['image'],$i+1]);
 $st=$db->prepare('INSERT INTO faqs(question_ru,question_kk,answer_ru,answer_kk,sort_order,active) VALUES(?,?,?,?,?,1)'); foreach($seed['faqs'] as $i=>$v)$st->execute([$v['q']['ru'],$v['q']['kk'],$v['a']['ru'],$v['a']['kk'],$i+1]);
 $user=getenv('ADMIN_USER') ?: 'admin'; $pass=getenv('ADMIN_PASSWORD') ?: 'admin';
 $db->prepare("INSERT INTO admins(username,password_hash,created_at) VALUES(?,?,datetime('now'))")->execute([$user,password_hash($pass,PASSWORD_DEFAULT)]);
}
header('X-Content-Type-Options: nosniff');header('Referrer-Policy: strict-origin-when-cross-origin');header('X-Frame-Options: SAMEORIGIN');
