<?php
require_once '../includes/db.php';
include 'includes/header.php';

$msg = '';
$settings = get_settings($pdo);

// Fetch current admin username
$admin_user = ['username' => 'admin']; // fallback
if ($pdo) {
    try {
        $stmt = $pdo->query("SELECT username FROM admin_users LIMIT 1");
        $admin_user = $stmt->fetch();
    } catch (PDOException $e) {
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $updated_settings = [
        'site_name' => $_POST['site_name'],
        'contact_phone' => $_POST['contact_phone'],
        'contact_email' => $_POST['contact_email'],
        'contact_address' => $_POST['contact_address'],
        'contact_hours' => $_POST['contact_hours'],
        'about_text' => $_POST['about_text']
    ];

    // Handle Logo Upload
    if (!empty($_FILES['site_logo_file']['name'])) {
        $upload_dir = '../uploads/';
        $file_ext = strtolower(pathinfo($_FILES['site_logo_file']['name'], PATHINFO_EXTENSION));
        $allowed_exts = ['jpg', 'jpeg', 'png', 'webp', 'gif', 'svg'];

        if (in_array($file_ext, $allowed_exts)) {
            $new_filename = 'logo_' . uniqid() . '.' . $file_ext;
            $dest_path = $upload_dir . $new_filename;

            if (move_uploaded_file($_FILES['site_logo_file']['tmp_name'], $dest_path)) {
                // Delete old logo if it exists
                if (!empty($settings['site_logo']) && strpos($settings['site_logo'], '/uploads/') === 0) {
                    $old_path = '../' . ltrim($settings['site_logo'], '/');
                    if (file_exists($old_path)) {
                        unlink($old_path);
                    }
                }
                $updated_settings['site_logo'] = 'uploads/' . $new_filename;
            }
        }
    }

    // Handle Favicon Upload
    if (!empty($_FILES['site_favicon_file']['name'])) {
        $upload_dir = '../uploads/';
        $file_ext = strtolower(pathinfo($_FILES['site_favicon_file']['name'], PATHINFO_EXTENSION));
        $allowed_exts = ['ico', 'png', 'jpg', 'jpeg', 'webp', 'svg'];

        if (in_array($file_ext, $allowed_exts)) {
            $new_filename = 'favicon_' . uniqid() . '.' . $file_ext;
            $dest_path = $upload_dir . $new_filename;

            if (move_uploaded_file($_FILES['site_favicon_file']['tmp_name'], $dest_path)) {
                // Delete old favicon if it exists
                if (!empty($settings['site_favicon']) && strpos($settings['site_favicon'], '/uploads/') === 0) {
                    $old_path = '../' . ltrim($settings['site_favicon'], '/');
                    if (file_exists($old_path)) {
                        unlink($old_path);
                    }
                }
                $updated_settings['site_favicon'] = 'uploads/' . $new_filename;
            }
        }
    }

    if ($pdo) {
        try {
            foreach ($updated_settings as $key => $value) {
                $stmt = $pdo->prepare("UPDATE settings SET setting_value = ? WHERE setting_key = ?");
                $stmt->execute([$value, $key]);
            }

            // Handle Admin Account Updates
            $new_username = trim($_POST['admin_username'] ?? '');
            $new_password = $_POST['admin_password'] ?? '';

            if (!empty($new_username)) {
                $stmt = $pdo->prepare("UPDATE admin_users SET username = ?");
                $stmt->execute([$new_username]);
            }

            if (!empty($new_password)) {
                $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
                $stmt = $pdo->prepare("UPDATE admin_users SET password_hash = ?");
                $stmt->execute([$hashed_password]);
            }

            $msg = "Settings updated successfully.";
            // Refresh settings array
            $settings = get_settings($pdo);
            // Refresh admin user
            $stmt = $pdo->query("SELECT username FROM admin_users LIMIT 1");
            $admin_user = $stmt->fetch();
        } catch (PDOException $e) {
            $error = "Update failed: " . $e->getMessage();
        }
    }
}
?>

<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
    <h2>Website Settings</h2>
</div>

<?php if ($msg): ?>
    <div
        style="background-color: #D1FAE5; color: #065F46; padding: 1rem; border-radius: 6px; margin-bottom: 2rem; font-weight: 500;">
        ✅
        <?php echo htmlspecialchars($msg); ?>
    </div>
<?php endif; ?>

<?php if (isset($error)): ?>
    <div
        style="background-color: #FEE2E2; color: #991B1B; padding: 1rem; border-radius: 6px; margin-bottom: 2rem; font-weight: 500;">
        ⚠️
        <?php echo htmlspecialchars($error); ?>
    </div>
<?php endif; ?>

<div class="card">
    <form method="POST" enctype="multipart/form-data">
        <div style="display: grid; grid-template-columns: 1fr; gap: 1.5rem;">

            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 1.5rem;">
                <div>
                    <label style="display:block; margin-bottom: 0.5rem; font-weight: 500;">Website Name</label>
                    <input type="text" name="site_name" value="<?php echo htmlspecialchars($settings['site_name']); ?>"
                        required
                        style="width: 100%; padding: 0.75rem; border: 1px solid #E2E8F0; border-radius: 6px; font-family: inherit;">
                </div>
                <div>
                    <label style="display:block; margin-bottom: 0.5rem; font-weight: 500;">Contact Phone</label>
                    <input type="text" name="contact_phone"
                        value="<?php echo htmlspecialchars($settings['contact_phone']); ?>" required
                        style="width: 100%; padding: 0.75rem; border: 1px solid #E2E8F0; border-radius: 6px; font-family: inherit;">
                </div>
            </div>

            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 1.5rem;">
                <div>
                    <label style="display:block; margin-bottom: 0.5rem; font-weight: 500;">Contact Email</label>
                    <input type="email" name="contact_email"
                        value="<?php echo htmlspecialchars($settings['contact_email']); ?>" required
                        style="width: 100%; padding: 0.75rem; border: 1px solid #E2E8F0; border-radius: 6px; font-family: inherit;">
                </div>
                <div>
                    <label style="display:block; margin-bottom: 0.5rem; font-weight: 500;">Business Hours</label>
                    <input type="text" name="contact_hours"
                        value="<?php echo htmlspecialchars($settings['contact_hours']); ?>" required
                        style="width: 100%; padding: 0.75rem; border: 1px solid #E2E8F0; border-radius: 6px; font-family: inherit;">
                </div>
            </div>

            <div>
                <label style="display:block; margin-bottom: 0.5rem; font-weight: 500;">Office Address</label>
                <input type="text" name="contact_address"
                    value="<?php echo htmlspecialchars($settings['contact_address']); ?>" required
                    style="width: 100%; padding: 0.75rem; border: 1px solid #E2E8F0; border-radius: 6px; font-family: inherit;">
            </div>

            <div>
                <label style="display:block; margin-bottom: 0.5rem; font-weight: 500;">Footer "About"
                    Description</label>
                <textarea name="about_text" rows="3" required
                    style="width: 100%; padding: 0.75rem; border: 1px solid #E2E8F0; border-radius: 6px; font-family: inherit;"><?php echo htmlspecialchars($settings['about_text']); ?></textarea>
            </div>

            <div style="background-color: #F8FAFC; padding: 1.5rem; border-radius: 6px; border: 1px dashed #CBD5E1;">
                <label style="display:block; margin-bottom: 1rem; font-weight: 500;">Website Logo</label>
                <div style="display: flex; align-items: center; gap: 2rem; flex-wrap: wrap;">
                    <div
                        style="flex-shrink: 0; background: white; padding: 1rem; border-radius: 6px; border: 1px solid #E2E8F0; min-width: 150px; text-align: center;">
                        <?php if (!empty($settings['site_logo'])): ?>
                            <img src="<?php echo get_image_url($settings['site_logo'], true); ?>" alt="Current Logo"
                                style="max-height: 80px; max-width: 100%; display: block; margin: 0 auto 0.5rem;">
                            <span style="font-size: 0.75rem; color: var(--text-muted);">Current Logo</span>
                        <?php else: ?>
                            <div
                                style="height: 60px; display: flex; align-items: center; justify-content: center; color: var(--text-muted); font-style: italic; font-size: 0.875rem;">
                                No Image Logo
                            </div>
                        <?php endif; ?>
                    </div>
                    <div style="flex-grow: 1;">
                        <p style="font-size: 0.875rem; color: var(--text-muted); margin-bottom: 0.75rem;">Upload a new
                            logo image (PNG, JPG, WebP, or SVG). Recommended height: 60-80px. Clear background PNG or
                            SVG works best.</p>
                        <input type="file" name="site_logo_file" accept="image/*"
                            style="width: 100%; padding: 0.5rem; background: white; border: 1px solid #E2E8F0; border-radius: 6px;">
                    </div>
                </div>
            </div>

            <div
                style="background-color: #F8FAFC; padding: 1.5rem; border-radius: 6px; border: 1px dashed #CBD5E1; margin-top: 1rem;">
                <label style="display:block; margin-bottom: 1rem; font-weight: 500;">Browser Favicon (Tab Icon)</label>
                <div style="display: flex; align-items: center; gap: 2rem; flex-wrap: wrap;">
                    <div
                        style="flex-shrink: 0; background: white; padding: 0.75rem; border-radius: 6px; border: 1px solid #E2E8F0; min-width: 80px; text-align: center;">
                        <?php if (!empty($settings['site_favicon'])): ?>
                            <img src="<?php echo get_image_url($settings['site_favicon'], true); ?>" alt="Current Favicon"
                                style="max-height: 32px; max-width: 32px; display: block; margin: 0 auto 0.25rem;">
                            <span style="font-size: 0.65rem; color: var(--text-muted); display: block;">Current Icon</span>
                        <?php else: ?>
                            <div
                                style="height: 32px; display: flex; align-items: center; justify-content: center; color: var(--text-muted); font-style: italic; font-size: 0.75rem;">
                                default
                            </div>
                        <?php endif; ?>
                    </div>
                    <div style="flex-grow: 1;">
                        <p style="font-size: 0.875rem; color: var(--text-muted); margin-bottom: 0.75rem;">Upload a
                            favicon (PNG, ICO, or SVG). Recommended size: 32x32px. This will appear in browser tabs.</p>
                        <input type="file" name="site_favicon_file" accept=".ico,.png,.jpg,.jpeg,.svg,.webp"
                            style="width: 100%; padding: 0.4rem; background: white; border: 1px solid #E2E8F0; border-radius: 6px; font-size: 0.875rem;">
                    </div>
                </div>
            </div>

            <div id="account-security"
                style="background-color: #FFFBEB; padding: 1.5rem; border-radius: 6px; border: 1px solid #FEF3C7; margin-top: 1rem;">
                <label style="display:block; margin-bottom: 1rem; font-weight: 600; color: #92400E;">🔒 Admin Account
                    Security</label>
                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 1.5rem;">
                    <div>
                        <label
                            style="display:block; margin-bottom: 0.5rem; font-size: 0.875rem; font-weight: 500;">Admin
                            Username</label>
                        <input type="text" name="admin_username"
                            value="<?php echo htmlspecialchars($admin_user['username'] ?? 'admin'); ?>"
                            style="width: 100%; padding: 0.6rem; border: 1px solid #FCD34D; border-radius: 6px; font-family: inherit;">
                    </div>
                    <div>
                        <label style="display:block; margin-bottom: 0.5rem; font-size: 0.875rem; font-weight: 500;">New
                            Password (leave blank to keep current)</label>
                        <input type="password" name="admin_password" placeholder="••••••••"
                            style="width: 100%; padding: 0.6rem; border: 1px solid #FCD34D; border-radius: 6px; font-family: inherit;">
                    </div>
                </div>
                <p style="font-size: 0.75rem; color: #92400E; margin-top: 0.75rem;">⚠️ Only change these if you want to
                    update your login credentials. Security is important!</p>
            </div>

            <div style="margin-top: 1.5rem; padding-top: 1.5rem; border-top: 1px solid #E2E8F0;">

                <button type="submit" class="btn btn-primary" style="padding: 0.75rem 2rem; font-size: 1rem;">Save All
                    Settings</button>
            </div>
        </div>
    </form>
</div>

</div><!-- End content-area -->
</main>
</body>

</html>