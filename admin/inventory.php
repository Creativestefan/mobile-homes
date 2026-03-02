<?php
require_once '../includes/db.php';
include 'includes/header.php';

// Handle deletion
$msg = '';
if (isset($_POST['delete_id']) && isset($pdo)) {
    try {
        $stmt = $pdo->prepare("DELETE FROM inventory WHERE id = ?");
        $stmt->execute([$_POST['delete_id']]);
        $msg = "Property deleted successfully.";
    } catch (PDOException $e) {
        $msg = "Error deleting property: " . $e->getMessage();
    }
}

// Fetch inventory
$inventory = [];
if (isset($pdo)) {
    try {
        $stmt = $pdo->query("SELECT * FROM inventory ORDER BY id DESC");
        $inventory = $stmt->fetchAll();
    } catch (PDOException $e) {
        $error = "Database Error: " . $e->getMessage();
    }
} else {
    include_once '../data/inventory.php'; // fallback
}
?>

<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
    <h2>Manage Inventory</h2>
    <a href="inventory_edit.php" class="btn btn-primary" style="display: flex; align-items: center; gap: 0.5rem;">
        <span style="font-size: 1.25rem;">+</span> Add New Home
    </a>
</div>

<?php if ($msg): ?>
    <div
        style="background: #D1FAE5; color: #065F46; padding: 1rem; border-radius: 6px; margin-bottom: 2rem; border-left: 4px solid #10B981;">
        <?php echo htmlspecialchars($msg); ?>
    </div>
<?php endif; ?>
<?php if (isset($error)): ?>
    <div
        style="background: #FEE2E2; color: #991B1B; padding: 1rem; border-radius: 6px; margin-bottom: 2rem; border-left: 4px solid #DC2626;">
        ⚠️
        <?php echo htmlspecialchars($error); ?>
    </div>
<?php endif; ?>

<div class="card">
    <?php if (empty($inventory)): ?>
        <p style="text-align: center; color: var(--text-muted); padding: 2rem;">No inventory found. Add a new home to get
            started.</p>
    <?php else: ?>
        <div style="overflow-x: auto;">
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Image</th>
                        <th>Title & Type</th>
                        <th>Price</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($inventory as $item): ?>
                        <tr>
                            <td style="color: var(--text-muted);">#
                                <?php echo $item['id']; ?>
                            </td>
                            <td>
                                <img src="<?php echo get_image_url($item['image'], true); ?>" alt="home"
                                    style="width: 60px; height: 40px; object-fit: cover; border-radius: 4px;">
                            </td>
                            <td>
                                <div style="font-weight: 500; color: var(--primary);">
                                    <?php echo htmlspecialchars($item['title']); ?>
                                </div>
                                <div style="font-size: 0.75rem; color: var(--text-muted); text-transform: uppercase;">
                                    <?php echo htmlspecialchars($item['type']); ?>
                                </div>
                            </td>
                            <td style="font-weight: 600;">$
                                <?php echo number_format($item['price']); ?>
                            </td>
                            <td>
                                <?php
                                $statusClass = 'badge-blue';
                                if ($item['status'] == 'Featured')
                                    $statusClass = 'badge-green';
                                if ($item['status'] == 'Sold')
                                    $statusClass = 'badge-yellow';
                                ?>
                                <span class="badge <?php echo $statusClass; ?>">
                                    <?php echo htmlspecialchars($item['status']); ?>
                                </span>
                            </td>
                            <td>
                                <div style="display: flex; gap: 0.5rem;">
                                    <a href="inventory_edit.php?id=<?php echo $item['id']; ?>" class="btn"
                                        style="background: #F1F5F9; color: var(--primary); padding: 0.25rem 0.5rem; font-size: 0.75rem;">Edit</a>
                                    <form method="POST"
                                        onsubmit="return confirm('Are you sure you want to delete this property?');"
                                        style="display: inline;">
                                        <input type="hidden" name="delete_id" value="<?php echo $item['id']; ?>">
                                        <button type="submit" class="btn btn-danger"
                                            style="padding: 0.25rem 0.5rem; font-size: 0.75rem;">Delete</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>

</div><!-- End content-area -->
</main>
</body>

</html>