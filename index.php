<?php
$page_title = 'Home';
require_once 'includes/db.php';
include 'includes/header.php';

// Fetch inventory (will use DB if available, fallback otherwise)
$inventory = get_inventory(isset($pdo) ? $pdo : null);
$featured_homes = [];

if (is_array($inventory) && !empty($inventory)) {
    // Filter for featured homes
    $featured_homes = array_filter($inventory, function ($home) {
        return isset($home['status']) && $home['status'] === 'Featured';
    });
    // Get top 3
    $featured_homes = array_slice($featured_homes, 0, 3);
}
?>

<!-- Hero Section -->
<section class="hero">
    <div class="container hero-content">
        <span class="hero-kicker">Welcome Home</span>
        <h1>Your Path to Affordable Homeownership</h1>
        <p>Discover top-quality new and pre-owned manufactured homes, tiny homes, and modular living spaces. We provide
            full-service delivery, setup, and flexible financing.</p>
        <div class="hero-actions">
            <a href="inventory.php" class="btn btn-primary">Browse Inventory</a>
            <a href="financing.php" class="btn btn-accent">Get Prequalified</a>
        </div>
    </div>
</section>

<!-- Featured Inventory -->
<section class="section section-bg-gray">
    <div class="container">
        <div class="section-header">
            <h2>Featured Homes</h2>
            <p>Hand-picked models available for immediate delivery.</p>
        </div>

        <div class="property-grid">
            <?php foreach ($featured_homes as $home): ?>
                <div class="property-card">
                    <div class="property-image">
                        <img src="<?php echo htmlspecialchars($home['image']); ?>"
                            alt="<?php echo htmlspecialchars($home['title']); ?>">
                        <span class="property-badge">
                            <?php echo htmlspecialchars($home['status']); ?>
                        </span>
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
                        <a href="property.php?id=<?php echo $home['id']; ?>" class="btn btn-outline btn-full">View
                            Details</a>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <div class="text-center mt-3">
            <a href="inventory.php" class="btn btn-outline">View All Inventory</a>
        </div>
    </div>
</section>

<!-- Value Proposition -->
<section class="section">
    <div class="container">
        <div class="section-header">
            <h2>More Than Just a Dealership</h2>
            <p>We handle the heavy lifting so you can focus on moving in. From finding the perfect lot to securing your
                loan, we're with you every step.</p>
        </div>

        <div class="features-grid">
            <div class="feature-card">
                <div class="feature-icon">💸</div>
                <h3>Flexible Financing</h3>
                <p>We work with a network of lenders to offer competitive rates, seller financing, and lease-to-own
                    programs for all credit scores.</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon">🚚</div>
                <h3>Delivery & Setup</h3>
                <p>Our logistics team handles transport, blocking, leveling, and utility connections across the region.
                </p>
            </div>
            <div class="feature-card">
                <div class="feature-icon">🤝</div>
                <h3>We Buy Homes</h3>
                <p>Looking to upgrade or just need cash fast? We offer fair market value cash buyouts for your current
                    mobile home.</p>
            </div>
        </div>
    </div>
</section>

<!-- Testimonials -->
<section class="section section-bg-gray">
    <div class="container">
        <div class="section-header">
            <h2>What Our Homeowners Say</h2>
        </div>
        <div class="testimonials-grid">
            <div class="testimonial-card">
                <div class="stars">★★★★★</div>
                <p>"We never thought we could afford a home, but the financing team made it happen. The setup was
                    seamless, and we love our new space!"</p>
                <div class="author">— Sarah & Mark T.</div>
            </div>
            <div class="testimonial-card">
                <div class="stars">★★★★★</div>
                <p>"They bought our old single-wide for cash and had us set up in a beautiful new modular home in record
                    time. Incredible, stress-free service."</p>
                <div class="author">— David R.</div>
            </div>
            <div class="testimonial-card">
                <div class="stars">★★★★★</div>
                <p>"From browsing the inventory online to the final walkthrough, everything was transparent and easy.
                    Highly recommend!"</p>
                <div class="author">— Jessica M.</div>
            </div>
        </div>
    </div>
</section>

<!-- Final CTA -->
<section class="section hero py-4">
    <div class="container hero-content">
        <h2>Ready to Step Into Your New Home?</h2>
        <p class="mb-4">Don't wait. Browse our current inventory or see what financing
            programs you qualify for today.</p>
        <div class="hero-actions">
            <a href="inventory.php" class="btn btn-primary">Shop All Homes</a>
            <a href="financing.php" class="btn btn-accent">Get Prequalified Now</a>
        </div>
    </div>
</section>

<?php include 'includes/footer.php'; ?>