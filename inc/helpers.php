<?php
declare(strict_types=1);
function h(?string $value): string { return htmlspecialchars((string)$value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8'); }
function lang(): string { return preg_match('~^/kk(?:/|$)~', parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/') ? 'kk' : 'ru'; }
function t(array $row, string $key, ?string $locale = null, string $fallback = ''): string { $locale ??= lang(); return (string)($row[$key . '_' . $locale] ?? $row[$key . '_ru'] ?? $fallback); }
function site_path(string $path = '', ?string $locale = null): string { $locale ??= lang(); $path = trim($path, '/'); return ($locale === 'kk' ? '/kk' : '') . ($path !== '' ? '/' . $path : '/'); }
function csrf_token(): string { if (empty($_SESSION['csrf'])) $_SESSION['csrf'] = bin2hex(random_bytes(32)); return $_SESSION['csrf']; }
function csrf_check(): void { $token=(string)($_SESSION['csrf']??''); if ($token==='' || !hash_equals($token, (string)($_POST['csrf'] ?? ''))) { http_response_code(419); exit('Форма устарела. Обновите страницу и попробуйте снова.'); } }
function setting(PDO $db, string $key): array { $stmt=$db->prepare('SELECT value_ru,value_kk FROM settings WHERE `key`=?');$stmt->execute([$key]);return $stmt->fetch(PDO::FETCH_ASSOC) ?: ['value_ru'=>'','value_kk'=>'']; }
function settings_all(PDO $db): array { $out=[];foreach($db->query('SELECT `key`,value_ru,value_kk FROM settings') as $row)$out[$row['key']]=$row;return $out; }
function admin_logged_in(): bool { return isset($_SESSION['admin_id']) && is_int($_SESSION['admin_id']); }
function require_admin(): void { if (!admin_logged_in()) { header('Location: /admin/'); exit; } }
function upload_image(array $file, string $old = ''): string {
 if (($file['error'] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_NO_FILE) return $old;
 if (($file['error'] ?? UPLOAD_ERR_OK) !== UPLOAD_ERR_OK || ($file['size'] ?? 0) > 6000000) throw new RuntimeException('Фото не загружено: выберите файл до 6 МБ.');
 $finfo=new finfo(FILEINFO_MIME_TYPE);$mime=$finfo->file($file['tmp_name']);$extensions=['image/jpeg'=>'jpg','image/png'=>'png','image/webp'=>'webp'];
 if(!isset($extensions[$mime])) throw new RuntimeException('Поддерживаются JPG, PNG и WebP.');
 $name=bin2hex(random_bytes(18)).'.'.$extensions[$mime];$dir=dirname(__DIR__).'/uploads';if(!is_dir($dir)&&!mkdir($dir,0755,true))throw new RuntimeException('Не удалось создать папку фото.');
 if(!move_uploaded_file($file['tmp_name'],$dir.'/'.$name))throw new RuntimeException('Не удалось сохранить фото.');
 return '/uploads/'.$name;
}
function wa_url(string $number, string $message): string { return 'https://wa.me/'.preg_replace('/\D+/', '', $number).'?text='.rawurlencode($message); }
function safe_external_url(string $url): string { return preg_match('~^https?://~i', trim($url)) ? trim($url) : '#'; }
function current_path(): string { $path=parse_url($_SERVER['REQUEST_URI'] ?? '/',PHP_URL_PATH) ?: '/'; return preg_replace('~^/kk(?=/|$)~','',$path) ?: '/'; }
