<?php
// includes/db.php

/**
 * Lightweight .env loader for small PHP projects without Composer.
 *
 * @param string $path Absolute path to the .env file.
 * @return array<string, string>
 */
function load_env_file($path)
{
    $env_vars = [];

    if (!file_exists($path)) {
        return $env_vars;
    }

    $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        $line = trim($line);
        if ($line === '' || strpos($line, '#') === 0 || strpos($line, '=') === false) {
            continue;
        }

        list($name, $value) = explode('=', $line, 2);
        $name = trim($name);
        $value = trim($value);

        if ($name === '') {
            continue;
        }

        $value = trim($value, "\"'");
        $env_vars[$name] = $value;

        if (getenv($name) === false) {
            putenv($name . '=' . $value);
        }
    }

    return $env_vars;
}

/**
 * Detect whether the current request is running in a local development context.
 *
 * @return bool
 */
function is_local_request()
{
    $server_name = $_SERVER['HTTP_HOST'] ?? $_SERVER['SERVER_NAME'] ?? php_uname('n');
    $server_name = strtolower((string) $server_name);

    // Strip port if present
    if (strpos($server_name, ':') !== false) {
        $server_name = explode(':', $server_name)[0];
    }

    return $server_name === '' ||
        $server_name === 'localhost' ||
        $server_name === '127.0.0.1' ||
        $server_name === '::1' ||
        substr($server_name, -5) === '.test' ||
        substr($server_name, -6) === '.local';
}

$env_path = __DIR__ . '/../.env';
$env_vars = load_env_file($env_path);

$pdo = null;
$db_connection_error = null;

$configured_driver = strtolower((string) (getenv('DB_CONNECTION') ?: ($env_vars['DB_CONNECTION'] ?? '')));
$has_mysql_credentials = !empty(getenv('DB_NAME') ?: ($env_vars['DB_NAME'] ?? '')) &&
    !empty(getenv('DB_USER') ?: ($env_vars['DB_USER'] ?? ''));

if ($configured_driver === 'mysql') {
    $use_mysql = true;
} elseif ($configured_driver === 'sqlite') {
    $use_mysql = false;
} else {
    $use_mysql = !is_local_request() && $has_mysql_credentials;
}

if ($use_mysql) {
    // ---------------------------------------------------------
    // CPANEL MYSQL CONFIGURATION (From environment / .env)
    // ---------------------------------------------------------
    $db_host = getenv('DB_HOST') ?: ($env_vars['DB_HOST'] ?? 'localhost');
    $db_name = getenv('DB_NAME') ?: ($env_vars['DB_NAME'] ?? '');
    $db_user = getenv('DB_USER') ?: ($env_vars['DB_USER'] ?? '');
    $db_pass = getenv('DB_PASS') ?: ($env_vars['DB_PASS'] ?? '');

    try {
        $pdo = new PDO("mysql:host=$db_host;dbname=$db_name;charset=utf8mb4", $db_user, $db_pass);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        $db_connection_error = "MySQL connection failed: " . $e->getMessage();
    }
} else {
    // ---------------------------------------------------------
    // LOCAL SQLITE CONFIGURATION
    // ---------------------------------------------------------
    $db_file = __DIR__ . '/../data/mobile_homes_db.sqlite';
    try {
        $pdo = new PDO('sqlite:' . $db_file);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        $db_connection_error = "SQLite connection failed: " . $e->getMessage();
    }
}
/**
 * Helper function to safely fallback to static inventory if DB is down
 */
function get_inventory($pdo = null)
{
    if ($pdo) {
        try {
            $stmt = $pdo->query("SELECT * FROM inventory ORDER BY status ASC, id DESC");
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            // fallback
        }
    }

    // Fallback to static data if DB connection failed
    include_once __DIR__ . '/../data/inventory.php';
    global $inventory;
    return $inventory;
}

/**
 * Helper function to get a single property
 */
function get_property($pdo, $id)
{
    if ($pdo) {
        try {
            $stmt = $pdo->prepare("SELECT * FROM inventory WHERE id = ?");
            $stmt->execute([$id]);
            return $stmt->fetch();
        } catch (PDOException $e) {
            // fallback
        }
    }

    // Fallback
    include_once __DIR__ . '/../data/inventory.php';
    global $inventory;
    foreach ($inventory as $item) {
        if ($item['id'] == $id) {
            return $item;
        }
    }
    return null;
}

/**
 * Helper function to get all website settings
 */
function get_settings($pdo)
{
    $settings = [
        'site_name' => 'NextGen Homes',
        'contact_phone' => '(555) 123-4567',
        'contact_email' => 'info@nextgenhomes.demo',
        'contact_address' => '123 Home Blvd, Anytown, USA',
        'contact_hours' => 'Mon - Sat: 9am - 6pm',
        'about_text' => 'Your path to affordable, high-quality homeownership. We provide full-service delivery, setup, and flexible financing.'
    ];

    if ($pdo) {
        try {
            $stmt = $pdo->query("SELECT setting_key, setting_value FROM settings");
            while ($row = $stmt->fetch()) {
                $settings[$row['setting_key']] = $row['setting_value'];
            }
        } catch (PDOException $e) {
            // fallback to defaults
        }
    }

    return $settings;
}
/**
 * Extract the first usable image path from a DB field.
 *
 * Inventory records can store multiple images separated by newlines, and some
 * hosting environments may save an absolute filesystem path for uploaded files.
 *
 * @param string $path The raw image path or URL from the database.
 * @return string The normalized primary image path.
 */
function normalize_image_path($path)
{
    if (!is_string($path)) {
        return '';
    }

    $path = trim(str_replace(["\r\n", "\r"], "\n", $path));
    if ($path === '') {
        return '';
    }

    foreach (explode("\n", $path) as $candidate) {
        $candidate = trim($candidate);
        if ($candidate !== '') {
            $path = $candidate;
            break;
        }
    }

    if (preg_match('/^(https?:)?\/\//i', $path)) {
        return $path;
    }

    $path = str_replace('\\', '/', $path);

    if (preg_match('~(?:^|/)(uploads/[^?#]+(?:\?[^#]*)?(?:#.*)?)$~i', $path, $matches)) {
        return ltrim($matches[1], '/');
    }

    return ltrim($path, '/');
}

/**
 * Helper function to safely get the image URL, handles local and remote paths.
 * 
 * @param string $path The image path or URL from the database.
 * @param bool $is_admin Whether the current page is in the admin directory.
 * @return string The resolved image URL.
 */
function get_image_url($path, $is_admin = false)
{
    $resolved_path = normalize_image_path($path);

    if ($resolved_path === '') {
        return 'https://images.unsplash.com/photo-1549517045-bc93de075e53?auto=format&fit=crop&q=80&w=800'; // Default fallback
    }

    // If it is a full URL (http://, https://, or protocol-relative), return as-is
    if (preg_match('/^(https?:)?\/\//i', $resolved_path)) {
        return $resolved_path;
    }

    // Clean leading slash for consistency
    $clean_path = ltrim($resolved_path, '/');

    // If we are in admin, relative path to uploads is ../uploads/...
    // If we are in public root, relative path to uploads is uploads/...
    if ($is_admin) {
        return '../' . $clean_path;
    }

    return $clean_path;
}
?>