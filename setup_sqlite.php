<?php
// setup_sqlite.php
// This script creates a local SQLite database for testing the admin panel without needing a full MySQL server.

$db_file = __DIR__ . '/data/mobile_homes_db.sqlite';

try {
    // Create (connect to) SQLite database in file
    $pdo = new PDO('sqlite:' . $db_file);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Create tables
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS inventory (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            title TEXT NOT NULL,
            type TEXT NOT NULL,
            price REAL NOT NULL,
            beds INTEGER NOT NULL,
            baths REAL NOT NULL,
            sqft INTEGER NOT NULL,
            status TEXT DEFAULT 'Standard',
            year_built INTEGER,
            category TEXT,
            address TEXT,
            features TEXT,
            image TEXT NOT NULL,
            description TEXT
        )
    ");

    $pdo->exec("
        CREATE TABLE IF NOT EXISTS leads (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            type TEXT NOT NULL,
            name TEXT NOT NULL,
            email TEXT,
            phone TEXT NOT NULL,
            details TEXT,
            is_read INTEGER DEFAULT 0,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP
        )
    ");

    $pdo->exec("
        CREATE TABLE IF NOT EXISTS admin_users (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            username TEXT NOT NULL UNIQUE,
            password_hash TEXT NOT NULL
        )
    ");

    // Insert demo config admin
    $stmt = $pdo->prepare("INSERT OR IGNORE INTO admin_users (username, password_hash) VALUES ('admin', ?)");
    $stmt->execute(['$2y$10$8/XGxkH5Jd.x1Z1E2sXXueT2/1U7T5O5WQKv.f6qU1A/iA1V5k1j2']);

    // Check if inventory is empty before inserting demo data
    $count = $pdo->query("SELECT COUNT(*) FROM inventory")->fetchColumn();

    if ($count == 0) {
        $img1 = "https://images.unsplash.com/photo-1549517045-bc93de075e53?auto=format&fit=crop&q=80&w=800\nhttps://images.unsplash.com/photo-1512917774080-9991f1c4c750?auto=format&fit=crop&q=80&w=800\nhttps://images.unsplash.com/photo-1502005229762-cf1b2da7c5d6?auto=format&fit=crop&q=80&w=800";
        $img2 = "https://images.unsplash.com/photo-1518780664697-55e3ad937233?auto=format&fit=crop&q=80&w=800\nhttps://images.unsplash.com/photo-1588880331179-bc9b9c4aadcb?auto=format&fit=crop&q=80&w=800";

        $inventory_data = [
            ['The Southern Charm', 'Double Wide', 85900.00, 3, 2.0, 1450, 'Featured', 2024, 'New', '123 Meadow Lane, Austin, TX 78701', "- Energy Star Certified\n- Open Floor Plan\n- Kitchen Island", $img1, 'Discover comfortable living in this beautiful Double Wide model.'],
            ['The Heritage', 'Single Wide', 54500.00, 2, 2.0, 900, 'Featured', 2022, 'Pre-Owned', '456 Oak View Drive, Round Rock, TX 78664', "- Vinyl Siding\n- Shingle Roof\n- Corner Lot Setup", $img2, 'Discover comfortable living in this beautiful Single Wide model.'],
            ['Cozy Cabin Retreat', 'Tiny Home', 42000.00, 1, 1.0, 399, 'Featured', 2024, 'New', '789 Pine Tree Rd, Georgetown, TX 78626', "- Loft Area\n- Custom Wood Cabinets\n- Porch Included", 'https://images.unsplash.com/photo-1449844908441-8829872d2607?auto=format&fit=crop&q=80&w=800', 'Discover comfortable living in this beautiful Tiny Home model.'],
            ['The Grand Estate', 'Modular', 135000.00, 4, 3.0, 2200, 'Standard', 2023, 'New', '101 Horizon Path, Cedar Park, TX 78613', "- Walk-in Closets\n- High Ceilings\n- Luxury Bath", 'https://images.unsplash.com/photo-1600585154340-be6161a56a0c?auto=format&fit=crop&q=80&w=800', 'Discover comfortable living in this beautiful Modular model.'],
            ['The Starter', 'Single Wide', 48900.00, 2, 1.0, 750, 'Standard', 2020, 'Pre-Owned', '202 Willow Bend, Leander, TX 78641', "- Fully Furnished\n- Move-in Ready", 'https://images.unsplash.com/photo-1570129477492-45c003edd2be?auto=format&fit=crop&q=80&w=800', 'Discover comfortable living in this beautiful Single Wide model.'],
            ['Modern Minimalist', 'Tiny Home', 55000.00, 1, 1.0, 450, 'Standard', 2024, 'New', '303 Tiny Lane, Austin, TX 78758', "- Smart Home Tech\n- Off-grid Capable", 'https://images.unsplash.com/photo-1523217582562-09d0def993a6?auto=format&fit=crop&q=80&w=800', 'Discover comfortable living in this beautiful Tiny Home model.']
        ];

        $stmt = $pdo->prepare("INSERT INTO inventory (title, type, price, beds, baths, sqft, status, year_built, category, address, features, image, description) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

        foreach ($inventory_data as $item) {
            $stmt->execute($item);
        }
    }

    echo "Local SQLite database created successfully at data/mobile_homes_db.sqlite";

} catch (PDOException $e) {
    echo "Error creating local database: " . $e->getMessage();
}
?>