<?php
require_once '../includes/db.php';
include 'includes/header.php';

// Quick stats
$total_inventory = 0;
$total_leads = 0;
$recent_leads = [];

if (isset($pdo)) {
    try {
        $total_inventory = $pdo->query("SELECT COUNT(*) FROM inventory")->fetchColumn();
        $total_leads = $pdo->query("SELECT COUNT(*) FROM leads")->fetchColumn();
        $recent_leads = $pdo->query("SELECT * FROM leads ORDER BY created_at DESC LIMIT 5")->fetchAll();
    } catch (PDOException $e) {
        $error = "Database Error: " . $e->getMessage();
    }
} else {
    // static fallback
    include_once '../data/inventory.php';
    $total_inventory = count($inventory);
}
?>

<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
    <h2>Dashboard Overview</h2>
    <?php if (isset($error)): ?>
        <span
            style="color: #DC2626; background: #FEE2E2; padding: 0.5rem 1rem; border-radius: 6px; font-weight: 500; font-size: 0.875rem;">
            ⚠️
            <?php echo htmlspecialchars($error); ?>
        </span>
    <?php endif; ?>
</div>

<div
    style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 1.5rem; margin-bottom: 2rem;">
    <div class="card" style="margin-bottom: 0;">
        <h3 style="color: var(--text-muted); font-size: 0.875rem; text-transform: uppercase; letter-spacing: 0.05em;">
            Total Inventory</h3>
        <div style="font-size: 2.5rem; font-weight: 700; color: var(--primary); margin-top: 0.5rem;">
            <?php echo $total_inventory; ?>
        </div>
        <a href="inventory.php"
            style="display: inline-block; margin-top: 1rem; color: var(--secondary); font-size: 0.875rem; font-weight: 500;">Manage
            Homes &rarr;</a>
    </div>

    <div class="card" style="margin-bottom: 0;">
        <h3 style="color: var(--text-muted); font-size: 0.875rem; text-transform: uppercase; letter-spacing: 0.05em;">
            Total Leads</h3>
        <div style="font-size: 2.5rem; font-weight: 700; color: var(--primary); margin-top: 0.5rem;">
            <?php echo $total_leads; ?>
        </div>
        <a href="leads.php"
            style="display: inline-block; margin-top: 1rem; color: var(--secondary); font-size: 0.875rem; font-weight: 500;">View
            Requests &rarr;</a>
    </div>
</div>

<div class="card">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
        <h3>Recent Leads</h3>
        <a href="leads.php" class="btn btn-primary" style="font-size: 0.75rem; padding: 0.25rem 0.5rem;">View All</a>
    </div>

    <?php if (empty($recent_leads)): ?>
        <p style="color: var(--text-muted); padding: 2rem 0; text-align: center;">No leads received yet.</p>
    <?php else: ?>
        <div style="overflow-x: auto;">
            <table>
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Type</th>
                        <th>Name</th>
                        <th>Contact</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($recent_leads as $lead): ?>
                        <tr>
                            <td style="color: var(--text-muted); font-size: 0.875rem;">
                                <?php echo date('M d, Y', strtotime($lead['created_at'])); ?>
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
                            <td style="font-weight: 500; color: var(--primary);">
                                <?php echo htmlspecialchars($lead['name']); ?>
                            </td>
                            <td style="font-size: 0.875rem;">
                                <?php echo htmlspecialchars($lead['phone']); ?><br>
                                <span style="color: var(--text-muted);">
                                    <?php echo htmlspecialchars($lead['email']); ?>
                                </span>
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