<?php
require_once '../includes/db.php';
include 'includes/header.php';

// Handle deletion
$msg = '';
if (isset($_POST['delete_id']) && isset($pdo)) {
    try {
        $stmt = $pdo->prepare("DELETE FROM leads WHERE id = ?");
        $stmt->execute([$_POST['delete_id']]);
        $msg = "Lead deleted successfully.";
    } catch (PDOException $e) {
        $msg = "Error deleting lead: " . $e->getMessage();
    }
}

// Handle Mark as Read
if (isset($_POST['read_id']) && isset($pdo)) {
    try {
        $stmt = $pdo->prepare("UPDATE leads SET is_read = 1 WHERE id = ?");
        $stmt->execute([$_POST['read_id']]);
    } catch (PDOException $e) {
    }
}

// Fetch leads
$leads = [];
if (isset($pdo)) {
    try {
        $stmt = $pdo->query("SELECT * FROM leads ORDER BY created_at DESC");
        $leads = $stmt->fetchAll();
    } catch (PDOException $e) {
        $error = "Database Error: " . $e->getMessage();
    }
} else {
    $error = "Not connected to the database. Cannot fetch leads.";
}
?>

<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
    <h2>Customer Requests & Leads</h2>
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
    <?php if (empty($leads)): ?>
        <p style="text-align: center; color: var(--text-muted); padding: 2rem;">No leads or requests found yet. They will
            appear here when customers submit forms.</p>
    <?php else: ?>
        <div style="overflow-x: auto;">
            <table>
                <thead>
                    <tr>
                        <th>Status</th>
                        <th>Date</th>
                        <th>Type</th>
                        <th>Customer</th>
                        <th>Details</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($leads as $lead): ?>
                        <tr style="<?php echo $lead['is_read'] ? 'opacity: 0.7;' : 'background-color: #F8FAFC;'; ?>">
                            <td>
                                <?php if (!$lead['is_read']): ?>
                                    <span
                                        style="display: inline-block; width: 10px; height: 10px; border-radius: 50%; background: var(--secondary);"></span>
                                <?php else: ?>
                                    <span style="color: var(--text-muted); font-size: 0.75rem;">Read</span>
                                <?php endif; ?>
                            </td>
                            <td style="color: var(--text-muted); font-size: 0.875rem;">
                                <?php echo date('M d, Y h:ia', strtotime($lead['created_at'])); ?>
                            </td>
                            <td>
                                <?php
                                $badgeClass = 'badge-blue';
                                if ($lead['type'] == 'Sell')
                                    $badgeClass = 'badge-yellow';
                                if ($lead['type'] == 'Financing')
                                    $badgeClass = 'badge-green';
                                ?>
                                <span class="badge <?php echo $badgeClass; ?>">
                                    <?php echo htmlspecialchars($lead['type']); ?>
                                </span>
                            </td>
                            <td>
                                <div style="font-weight: 500; color: var(--primary);">
                                    <?php echo htmlspecialchars($lead['name']); ?>
                                </div>
                                <div style="font-size: 0.875rem;">
                                    📞 <a href="tel:<?php echo htmlspecialchars($lead['phone']); ?>">
                                        <?php echo htmlspecialchars($lead['phone']); ?>
                                    </a><br>
                                    <?php if ($lead['email']): ?>
                                        ✉️ <a href="mailto:<?php echo htmlspecialchars($lead['email']); ?>">
                                            <?php echo htmlspecialchars($lead['email']); ?>
                                        </a>
                                    <?php endif; ?>
                                </div>
                            </td>
                            <td style="max-width: 300px; font-size: 0.875rem; color: var(--text-main);">
                                <?php
                                // Simple display of details
                                $details = json_decode($lead['details'], true);
                                if (is_array($details)) {
                                    foreach ($details as $key => $val) {
                                        echo "<strong>" . htmlspecialchars(ucfirst($key)) . ":</strong> " . htmlspecialchars($val) . "<br>";
                                    }
                                } else {
                                    echo htmlspecialchars($lead['details']);
                                }
                                ?>
                            </td>
                            <td>
                                <div style="display: flex; gap: 0.5rem; flex-direction: column;">
                                    <?php if (!$lead['is_read']): ?>
                                        <form method="POST">
                                            <input type="hidden" name="read_id" value="<?php echo $lead['id']; ?>">
                                            <button type="submit" class="btn"
                                                style="background: var(--primary-light); color: white; padding: 0.25rem 0.5rem; font-size: 0.75rem; width: 100%;">Mark
                                                Read</button>
                                        </form>
                                    <?php endif; ?>
                                    <form method="POST"
                                        onsubmit="return confirm('Are you sure you want to delete this lead?');">
                                        <input type="hidden" name="delete_id" value="<?php echo $lead['id']; ?>">
                                        <button type="submit" class="btn btn-danger"
                                            style="padding: 0.25rem 0.5rem; font-size: 0.75rem; width: 100%;">Delete</button>
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