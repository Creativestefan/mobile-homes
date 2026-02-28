<?php
$page_title = 'Inventory';
include 'includes/header.php';
require_once 'includes/db.php';

// Fetch inventory
$inventory = get_inventory(isset($pdo) ? $pdo : null);

// Check if a filter is set
$filter = isset($_GET['type']) ? $_GET['type'] : 'All';

$filtered_inventory = $inventory;
if ($filter !== 'All') {
    $filtered_inventory = array_filter($inventory, function ($home) use ($filter) {
        // Simple string parsing since types include "Single Wide", "Tiny Home", etc.
        return stripos($home['type'], $filter) !== false;
    });
}
?>

<div style="background-color: var(--primary); padding: 4rem 0;">
    <div class="container text-center" style="color: white; max-width: 700px; margin: 0 auto; text-align: center;">
        <h1 style="color: white;">Current Inventory</h1>
        <p style="font-size: 1.125rem; color: #E2E8F0;">Browse our wide selection of ready-to-move homes. Use the
            filters below to find the perfect size for your family.</p>
    </div>
</div>

<div class="section section-bg-gray">
    <div class="container">
        <!-- Filter Bar -->
        <div class="filter-bar"
            style="margin-bottom: 3rem; text-align: center; display: flex; flex-wrap: wrap; gap: 0.5rem; justify-content: center;">
            <?php
            $filters = ['All', 'Single Wide', 'Double Wide', 'Tiny Home', 'Triple Wide'];
            foreach ($filters as $f):
                $activeClass = $filter === $f ? 'btn-primary' : 'btn-outline';
                ?>
                <a href="?type=<?php echo urlencode($f); ?>" class="btn <?php echo $activeClass; ?>">
                    <?php echo htmlspecialchars($f); ?>
                </a>
            <?php endforeach; ?>
        </div>

        <div class="property-grid">
            <?php if (count($filtered_inventory) > 0): ?>
                <?php foreach ($filtered_inventory as $home): ?>
                    <div class="property-card">
                        <div class="property-image">
                            <img src="<?php echo htmlspecialchars($home['image']); ?>"
                                alt="<?php echo htmlspecialchars($home['title']); ?>">
                            <?php if ($home['status'] === 'Featured'): ?>
                                <span class="property-badge">
                                    <?php echo htmlspecialchars($home['status']); ?>
                                </span>
                            <?php endif; ?>
                        </div>
                        <div class="property-content">
                            <span class="property-type">
                                <?php echo htmlspecialchars($home['type']); ?>
                            </span>
                            <h3 class="property-title">
                                <?php echo htmlspecialchars($home['title']); ?>
                            </h3>
                            <div class="property-price">$
                                <?php echo number_format($home['price']); ?>
                            </div>
                            <div class="property-payment">Est. $
                                <?php echo number_format($home['price'] * 0.008); ?>/mo
                            </div>
                            <div class="property-specs">
                                <span>🛏️
                                    <?php echo $home['beds']; ?> Beds
                                </span>
                                <span>🛁
                                    <?php echo $home['baths']; ?> Baths
                                </span>
                                <span>📐
                                    <?php echo number_format($home['sqft']); ?> sqft
                                </span>
                            </div>
                            <a href="property.php?id=<?php echo $home['id']; ?>" class="btn btn-outline"
                                style="width: 100%; display: block;">View
                                Details</a>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div style="grid-column: 1 / -1; text-align: center; padding: 4rem 1rem;">
                    <h3>No homes found in this category.</h3>
                    <p>Please try a different filter or check back later.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>