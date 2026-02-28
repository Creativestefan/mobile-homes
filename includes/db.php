<?php
// includes/db.php

// Flag to indicate if we are in local testing mode
$is_local_testing = true;

if ($is_local_testing) {
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
} else {
    // ---------------------------------------------------------
    // ---------------------------------------------------------
    // CPANEL MYSQL CONFIGURATION (From .env)
    // ---------------------------------------------------------
    // Simple .env parser for lightweight PHP apps without Composer
    $env_path = __DIR__ . '/../.env';
    $env_vars = [];
    if (file_exists($env_path)) {
        $lines = file($env_path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        foreach ($lines as $line) {
            if (strpos(trim($line), '#') === 0)
                continue;
            list($name, $value) = explode('=', $line, 2);
            $env_vars[trim($name)] = trim($value);
        }
    }

    $db_host = $env_vars['DB_HOST'] ?? 'localhost';
    $db_name = $env_vars['DB_NAME'] ?? '';
    $db_user = $env_vars['DB_USER'] ?? '';
    $db_pass = $env_vars['DB_PASS'] ?? '';

    try {
        $pdo = new PDO("mysql:host=$db_host;dbname=$db_name;charset=utf8mb4", $db_user, $db_pass);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        $db_connection_error = "MySQL connection failed: " . $e->getMessage();
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
?>