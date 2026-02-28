<?php
require_once '../includes/db.php';
include 'includes/header.php';

$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
$is_edit = $id > 0;
$msg = '';

// Default values
$home = [
    'title' => '',
    'type' => '',
    'price' => '',
    'beds' => '',
    'baths' => '',
    'sqft' => '',
    'status' => 'Standard',
    'year_built' => '',
    'category' => '',
    'address' => '',
    'features' => '',
    'image' => '',
    'description' => ''
];

// Provide fake db error handling for static fallback
$db_mode = isset($pdo);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Handle image uploads
    $uploaded_images = [];
    if (!empty($_FILES['image_files']['name'][0])) {
        $upload_dir = '../uploads/';
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0755, true);
        }

        foreach ($_FILES['image_files']['tmp_name'] as $key => $tmp_name) {
            if ($_FILES['image_files']['error'][$key] === UPLOAD_ERR_OK) {
                // Generate secure filename
                $file_ext = strtolower(pathinfo($_FILES['image_files']['name'][$key], PATHINFO_EXTENSION));
                $allowed_exts = ['jpg', 'jpeg', 'png', 'webp', 'gif'];

                if (in_array($file_ext, $allowed_exts)) {
                    $new_filename = uniqid('img_') . '.' . $file_ext;
                    $dest_path = $upload_dir . $new_filename;

                    if (move_uploaded_file($tmp_name, $dest_path)) {
                        $uploaded_images[] = '/uploads/' . $new_filename;
                    }
                }
            }
        }
    }

    // Combine pasted URLs and new uploaded URLs
    $final_image_string = trim($_POST['image'] ?? '');
    if (!empty($uploaded_images)) {
        $uploaded_string = implode("\n", $uploaded_images);
        if (empty($final_image_string)) {
            $final_image_string = $uploaded_string;
        } else {
            $final_image_string .= "\n" . $uploaded_string;
        }
    }

    if ($db_mode) {
        try {
            if ($is_edit) {
                // Update
                $stmt = $pdo->prepare("UPDATE inventory SET title=?, type=?, price=?, beds=?, baths=?, sqft=?, status=?, year_built=?, category=?, address=?, features=?, image=?, description=? WHERE id=?");
                $stmt->execute([
                    $_POST['title'],
                    $_POST['type'],
                    $_POST['price'],
                    $_POST['beds'],
                    $_POST['baths'],
                    $_POST['sqft'],
                    $_POST['status'],
                    $_POST['year_built'],
                    $_POST['category'],
                    $_POST['address'],
                    $_POST['features'],
                    $final_image_string,
                    $_POST['description'],
                    $id
                ]);
                $msg = "Property updated successfully.";
            } else {
                // Insert
                $stmt = $pdo->prepare("INSERT INTO inventory (title, type, price, beds, baths, sqft, status, year_built, category, address, features, image, description) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
                $stmt->execute([
                    $_POST['title'],
                    $_POST['type'],
                    $_POST['price'],
                    $_POST['beds'],
                    $_POST['baths'],
                    $_POST['sqft'],
                    $_POST['status'],
                    $_POST['year_built'],
                    $_POST['category'],
                    $_POST['address'],
                    $_POST['features'],
                    $final_image_string,
                    $_POST['description']
                ]);
                $id = $pdo->lastInsertId();
                $is_edit = true;
                $msg = "Property added successfully.";
            }
        } catch (PDOException $e) {
            $msg = "Database Error: " . $e->getMessage();
        }
    } else {
        $msg = "Cannot save: Database is not connected yet. Running in static array fallback mode.";
    }
}

// Fetch existing property
if ($is_edit && $db_mode) {
    try {
        $stmt = $pdo->prepare("SELECT * FROM inventory WHERE id = ?");
        $stmt->execute([$id]);
        $row = $stmt->fetch();
        if ($row)
            $home = $row;
    } catch (PDOException $e) {
    }
}

?>

<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
    <h2>
        <?php echo $is_edit ? 'Edit Property: ' . htmlspecialchars($home['title']) : 'Add New Property'; ?>
    </h2>
    <a href="inventory.php" class="btn" style="background: #E2E8F0; color: var(--primary);">&larr; Back to Inventory</a>
</div>

<?php if ($msg): ?>
    <div style="background: <?php echo strpos($msg, 'Error') !== false || strpos($msg, 'Cannot') !== false ? '#FEE2E2' : '#D1FAE5'; ?>; 
                color: <?php echo strpos($msg, 'Error') !== false || strpos($msg, 'Cannot') !== false ? '#991B1B' : '#065F46'; ?>; 
                padding: 1rem; border-radius: 6px; margin-bottom: 2rem;">
        <?php echo htmlspecialchars($msg); ?>
    </div>
<?php endif; ?>

<div class="card">
    <form method="POST" enctype="multipart/form-data">
        <div style="display: grid; grid-template-columns: 1fr; gap: 1.5rem;">

            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 1.5rem;">
                <div>
                    <label style="display:block; margin-bottom: 0.5rem; font-weight: 500;">Property Title</label>
                    <input type="text" name="title" required value="<?php echo htmlspecialchars($home['title']); ?>"
                        style="width: 100%; padding: 0.75rem; border: 1px solid #E2E8F0; border-radius: 6px;">
                </div>
                <div>
                    <label style="display:block; margin-bottom: 0.5rem; font-weight: 500;">Home Type</label>
                    <select name="type" required
                        style="width: 100%; padding: 0.75rem; border: 1px solid #E2E8F0; border-radius: 6px; background: white;">
                        <option value="Single Wide" <?php echo $home['type'] == 'Single Wide' ? 'selected' : ''; ?>>Single
                            Wide</option>
                        <option value="Double Wide" <?php echo $home['type'] == 'Double Wide' ? 'selected' : ''; ?>>Double
                            Wide</option>
                        <option value="Triple Wide" <?php echo $home['type'] == 'Triple Wide' ? 'selected' : ''; ?>>Triple
                            Wide</option>
                        <option value="Modular" <?php echo $home['type'] == 'Modular' ? 'selected' : ''; ?>>Modular
                        </option>
                        <option value="Tiny Home" <?php echo $home['type'] == 'Tiny Home' ? 'selected' : ''; ?>>Tiny Home
                        </option>
                    </select>
                </div>
            </div>

            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(150px, 1fr)); gap: 1.5rem;">
                <div>
                    <label style="display:block; margin-bottom: 0.5rem; font-weight: 500;">Price ($)</label>
                    <input type="number" name="price" required value="<?php echo htmlspecialchars($home['price']); ?>"
                        style="width: 100%; padding: 0.75rem; border: 1px solid #E2E8F0; border-radius: 6px;">
                </div>
                <div>
                    <label style="display:block; margin-bottom: 0.5rem; font-weight: 500;">Bedrooms</label>
                    <input type="number" name="beds" required value="<?php echo htmlspecialchars($home['beds']); ?>"
                        style="width: 100%; padding: 0.75rem; border: 1px solid #E2E8F0; border-radius: 6px;">
                </div>
                <div>
                    <label style="display:block; margin-bottom: 0.5rem; font-weight: 500;">Bathrooms</label>
                    <input type="number" step="0.5" name="baths" required
                        value="<?php echo htmlspecialchars($home['baths']); ?>"
                        style="width: 100%; padding: 0.75rem; border: 1px solid #E2E8F0; border-radius: 6px;">
                </div>
                <div>
                    <label style="display:block; margin-bottom: 0.5rem; font-weight: 500;">Square Feet</label>
                    <input type="number" name="sqft" required value="<?php echo htmlspecialchars($home['sqft']); ?>"
                        style="width: 100%; padding: 0.75rem; border: 1px solid #E2E8F0; border-radius: 6px;">
                </div>
            </div>

            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1.5rem;">
                <div>
                    <label style="display:block; margin-bottom: 0.5rem; font-weight: 500;">Status</label>
                    <select name="status"
                        style="width: 100%; padding: 0.75rem; border: 1px solid #E2E8F0; border-radius: 6px; background: white;">
                        <option value="Standard" <?php echo $home['status'] == 'Standard' ? 'selected' : ''; ?>>Standard
                            (Normal Listing)</option>
                        <option value="Featured" <?php echo $home['status'] == 'Featured' ? 'selected' : ''; ?>>Featured
                            (Shows on Homepage)</option>
                        <option value="Sold" <?php echo $home['status'] == 'Sold' ? 'selected' : ''; ?>>Sold</option>
                    </select>
                </div>
                <div>
                    <label style="display:block; margin-bottom: 0.5rem; font-weight: 500;">Year Built</label>
                    <input type="number" name="year_built" required
                        value="<?php echo htmlspecialchars($home['year_built'] ?? ''); ?>"
                        style="width: 100%; padding: 0.75rem; border: 1px solid #E2E8F0; border-radius: 6px;"
                        placeholder="e.g. 2024">
                </div>
                <div>
                    <label style="display:block; margin-bottom: 0.5rem; font-weight: 500;">Category</label>
                    <select name="category"
                        style="width: 100%; padding: 0.75rem; border: 1px solid #E2E8F0; border-radius: 6px; background: white;">
                        <option value="New" <?php echo ($home['category'] ?? '') == 'New' ? 'selected' : ''; ?>>New
                        </option>
                        <option value="Pre-Owned" <?php echo ($home['category'] ?? '') == 'Pre-Owned' ? 'selected' : ''; ?>>Pre-Owned</option>
                        <option value="Used" <?php echo ($home['category'] ?? '') == 'Used' ? 'selected' : ''; ?>>Used
                        </option>
                    </select>
                </div>
            </div>

            <div>
                <label style="display:block; margin-bottom: 0.5rem; font-weight: 500;">Address / Location</label>
                <input type="text" name="address" required
                    value="<?php echo htmlspecialchars($home['address'] ?? ''); ?>"
                    placeholder="e.g. 123 Pine St, Austin, TX"
                    style="width: 100%; padding: 0.75rem; border: 1px solid #E2E8F0; border-radius: 6px;">
            </div>

            <div>
                <label style="display:block; margin-bottom: 0.5rem; font-weight: 500;">Image URLs (One per line)</label>
                <textarea name="image" required rows="3" placeholder="https://..."
                    style="width: 100%; padding: 0.75rem; border: 1px solid #E2E8F0; border-radius: 6px; font-family: inherit; resize: vertical;"><?php echo htmlspecialchars($home['image'] ?? ''); ?></textarea>
            </div>

            <div
                style="background-color: #F8FAFC; padding: 1.5rem; border-radius: var(--radius); border: 1px dashed #CBD5E1;">
                <label
                    style="display:block; margin-bottom: 0.5rem; font-weight: 500; display: flex; align-items: center; gap: 0.5rem;">
                    <span>📁</span>
                    Upload Local Images
                </label>
                <p style="font-size: 0.875rem; color: var(--text-muted); margin-bottom: 1rem;">Select one or multiple
                    images from your device. They will be uploaded and automatically added to the image gallery.</p>
                <input type="file" name="image_files[]" multiple accept="image/*"
                    style="width: 100%; padding: 0.5rem; background: white; border: 1px solid #E2E8F0; border-radius: 6px;">
            </div>

            <div>
                <label style="display:block; margin-bottom: 0.5rem; font-weight: 500;">Property Description</label>
                <textarea name="description" rows="5" required
                    style="width: 100%; padding: 0.75rem; border: 1px solid #E2E8F0; border-radius: 6px; font-family: inherit; resize: vertical;"><?php echo htmlspecialchars($home['description'] ?? ''); ?></textarea>
            </div>

            <div>
                <label style="display:block; margin-bottom: 0.5rem; font-weight: 500;">Features (One per line)</label>
                <textarea name="features" rows="4"
                    placeholder="- Open Floor Plan&#10;- Kitchen Island&#10;- Energy Star Certified"
                    style="width: 100%; padding: 0.75rem; border: 1px solid #E2E8F0; border-radius: 6px; font-family: inherit; resize: vertical;"><?php echo htmlspecialchars($home['features'] ?? ''); ?></textarea>
            </div>

            <div style="border-top: 1px solid #E2E8F0; padding-top: 1.5rem; display: flex; justify-content: flex-end;">
                <button type="submit" class="btn btn-primary" style="font-size: 1rem; padding: 0.75rem 2rem;">Save
                    Property</button>
            </div>

        </div>
    </form>
</div>

</div><!-- End content-area -->
</main>
</body>

</html>