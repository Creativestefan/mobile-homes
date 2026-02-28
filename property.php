<?php
require_once 'includes/db.php';

// Get property ID from URL
$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
$property = null;

// Find property
if (isset($pdo)) {
    try {
        $stmt = $pdo->prepare("SELECT * FROM inventory WHERE id = ?");
        $stmt->execute([$id]);
        $property = $stmt->fetch();
    } catch (PDOException $e) {
    }
}

// Redirect if not found
if (!$property) {
    header("Location: inventory.php");
    exit();
}

$message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $inquiry_text = trim($_POST['message'] ?? '');

    $details = json_encode([
        'property_id' => $id,
        'property_title' => $property['title'],
        'message' => $inquiry_text
    ]);

    if (isset($pdo)) {
        try {
            $stmt = $pdo->prepare("INSERT INTO leads (type, name, email, phone, details) VALUES ('Inquiry', ?, ?, ?, ?)");
            $stmt->execute([$name, $email, $phone, $details]);
            $message = "Message sent! A representative will contact you shortly about the " . htmlspecialchars($property['title']) . ".";
        } catch (PDOException $e) {
            $message = "Error submitting request. Please try again later.";
        }
    } else {
        $message = "Message sent! (Static demo mode: No database connected)";
    }
}

$page_title = $property['title'];
include 'includes/header.php';
?>

<div style="background-color: var(--bg-gray); padding: 3rem 0;">
    <div class="container">
        <!-- Breadcrumb -->
        <div style="margin-bottom: 2rem; font-size: 0.875rem;">
            <a href="index.php" style="color: var(--text-muted);">Home</a> &gt;
            <a href="inventory.php" style="color: var(--text-muted);">Inventory</a> &gt;
            <span style="color: var(--primary); font-weight: 500;">
                <?php echo htmlspecialchars($property['title']); ?>
            </span>
        </div>

        <div class="property-detail-grid">
            <!-- Left: Images & Info -->
            <div class="property-main">
                <?php
                $images = array_filter(array_map('trim', explode("\n", $property['image'])));
                if (count($images) > 1):
                    ?>
                    <div class="property-gallery"
                        style="margin-bottom: 2rem; border-radius: var(--radius-lg); overflow: hidden; box-shadow: var(--shadow); position: relative;">
                        <div class="carousel-container"
                            style="position: relative; width: 100%; height: 500px; display: flex; align-items: center; justify-content: center; background: #000;">
                            <?php foreach ($images as $index => $img): ?>
                                <img class="carousel-slide <?php echo $index === 0 ? 'active' : ''; ?>"
                                    src="<?php echo htmlspecialchars($img); ?>" alt="Property Image <?php echo $index + 1; ?>"
                                    style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; object-fit: cover; opacity: <?php echo $index === 0 ? '1' : '0'; ?>; transition: opacity 0.4s ease-in-out;">
                            <?php endforeach; ?>

                            <!-- Controls -->
                            <button onclick="changeSlide(-1)"
                                style="position: absolute; top: 50%; left: 1rem; transform: translateY(-50%); background: rgba(0,0,0,0.5); color: white; border: none; width: 40px; height: 40px; border-radius: 50%; font-size: 1.5rem; cursor: pointer; display: flex; align-items: center; justify-content: center; z-index: 10;">&#10094;</button>
                            <button onclick="changeSlide(1)"
                                style="position: absolute; top: 50%; right: 1rem; transform: translateY(-50%); background: rgba(0,0,0,0.5); color: white; border: none; width: 40px; height: 40px; border-radius: 50%; font-size: 1.5rem; cursor: pointer; display: flex; align-items: center; justify-content: center; z-index: 10;">&#10095;</button>

                            <!-- Indicators -->
                            <div
                                style="position: absolute; bottom: 1rem; left: 50%; transform: translateX(-50%); display: flex; gap: 0.5rem; z-index: 10;">
                                <?php foreach ($images as $index => $img): ?>
                                    <div class="carousel-dot <?php echo $index === 0 ? 'active' : ''; ?>"
                                        onclick="goToSlide(<?php echo $index; ?>)"
                                        style="width: 10px; height: 10px; border-radius: 50%; background: <?php echo $index === 0 ? 'white' : 'rgba(255,255,255,0.5)'; ?>; cursor: pointer; transition: background 0.3s;">
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>

                    <script>
                        let currentSlide = 0;
                        const slides = document.querySelectorAll('.carousel-slide');
                        const dots = document.querySelectorAll('.carousel-dot');

                        function showSlide(n) {
                            slides[currentSlide].style.opacity = '0';
                            dots[currentSlide].style.background = 'rgba(255,255,255,0.5)';

                            currentSlide = (n + slides.length) % slides.length;

                            slides[currentSlide].style.opacity = '1';
                            dots[currentSlide].style.background = 'white';
                        }

                        function changeSlide(step) {
                            showSlide(currentSlide + step);
                        }

                        function goToSlide(n) {
                            showSlide(n);
                        }
                    </script>
                <?php else: ?>
                    <div class="property-gallery"
                        style="margin-bottom: 2rem; border-radius: var(--radius-lg); overflow: hidden; box-shadow: var(--shadow);">
                        <img src="<?php echo htmlspecialchars($images[0] ?? $property['image']); ?>"
                            alt="<?php echo htmlspecialchars($property['title']); ?>"
                            style="width: 100%; height: 500px; object-fit: cover;">
                    </div>
                <?php endif; ?>

                <div class="property-info"
                    style="background: white; padding: 2rem; border-radius: var(--radius-lg); box-shadow: var(--shadow);">
                    <div
                        style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 1rem; flex-wrap: wrap; gap: 1rem;">
                        <div>
                            <span class="property-type" style="margin-right: 0.5rem;">
                                <?php echo htmlspecialchars($property['category'] ?? 'Pre-Owned'); ?>
                            </span>
                            <span class="property-type">
                                <?php echo htmlspecialchars($property['type']); ?>
                            </span>
                            <h1 style="margin-bottom: 0.5rem; margin-top: 0.5rem;">
                                <?php echo htmlspecialchars($property['title']); ?>
                            </h1>
                        </div>
                        <div style="text-align: right;">
                            <div style="font-size: 2.5rem; font-weight: 800; color: var(--secondary);">$
                                <?php echo number_format($property['price']); ?>
                            </div>
                            <div style="color: var(--text-muted);">Est. $
                                <?php echo number_format($property['price'] * 0.008); ?>/mo
                            </div>
                        </div>
                    </div>

                    <?php if (!empty($property['address'])): ?>
                        <div
                            style="color: var(--text-muted); font-size: 1.125rem; margin-bottom: 1.5rem; display: flex; align-items: center; gap: 0.5rem;">
                            📍 <?php echo htmlspecialchars($property['address']); ?>
                        </div>
                    <?php endif; ?>

                    <div
                        style="display: grid; grid-template-columns: repeat(auto-fit, minmax(120px, 1fr)); gap: 1rem; background: var(--bg-gray); padding: 1.5rem; border-radius: var(--radius); margin-bottom: 2rem;">
                        <div>
                            <div
                                style="color: var(--text-muted); font-size: 0.875rem; text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 0.25rem;">
                                Year Built</div>
                            <div style="font-weight: 600; font-size: 1.125rem;">📅
                                <?php echo htmlspecialchars($property['year_built'] ?? 'N/A'); ?>
                            </div>
                        </div>
                        <div>
                            <div
                                style="color: var(--text-muted); font-size: 0.875rem; text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 0.25rem;">
                                Bedrooms</div>
                            <div style="font-weight: 600; font-size: 1.125rem;">🛏️ <?php echo $property['beds']; ?>
                            </div>
                        </div>
                        <div>
                            <div
                                style="color: var(--text-muted); font-size: 0.875rem; text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 0.25rem;">
                                Bathrooms</div>
                            <div style="font-weight: 600; font-size: 1.125rem;">🛁 <?php echo $property['baths']; ?>
                            </div>
                        </div>
                        <div>
                            <div
                                style="color: var(--text-muted); font-size: 0.875rem; text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 0.25rem;">
                                Area</div>
                            <div style="font-weight: 600; font-size: 1.125rem;">📐
                                <?php echo number_format($property['sqft']); ?> sqft
                            </div>
                        </div>
                    </div>

                    <h2 style="font-size: 1.5rem; margin-bottom: 1rem;">Description</h2>
                    <p style="color: var(--text-main); line-height: 1.8; margin-bottom: 2rem;">
                        <?php echo nl2br(htmlspecialchars($property['description'] ?? '')); ?>
                    </p>

                    <?php if (!empty($property['features'])): ?>
                        <h2 style="font-size: 1.5rem; margin-bottom: 1rem;">Features</h2>
                        <ul style="color: var(--text-main); line-height: 1.8; margin-left: 1.5rem; margin-bottom: 2rem;">
                            <?php
                            $features = explode("\n", $property['features']);
                            foreach ($features as $feature) {
                                $f = trim(str_replace('-', '', $feature));
                                if (!empty($f))
                                    echo "<li>" . htmlspecialchars($f) . "</li>";
                            }
                            ?>
                        </ul>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Right: Sidebar Action -->
            <div class="property-sidebar">
                <div
                    style="background: white; padding: 2rem; border-radius: var(--radius-lg); box-shadow: var(--shadow); position: sticky; top: 100px;">
                    <h3 style="margin-bottom: 1.5rem; text-align: center;">Interested in this home?</h3>

                    <a href="financing.php" class="btn btn-primary"
                        style="width: 100%; margin-bottom: 1rem; text-align: center;">Get Prequalified Now</a>
                    <a href="tel:5551234567" class="btn btn-outline"
                        style="width: 100%; margin-bottom: 2rem; text-align: center;">Call (555) 123-4567</a>

                    <hr style="border: 0; border-top: 1px solid var(--border-color); margin-bottom: 1.5rem;">

                    <h4 style="font-size: 1rem; margin-bottom: 1rem;">Ask a Question</h4>

                    <?php if ($message): ?>
                        <div
                            style="background-color: #ECFDF5; color: #065F46; padding: 1rem; border-radius: var(--radius); margin-bottom: 1.5rem; font-size: 0.875rem;">
                            <?php echo $message; ?>
                        </div>
                    <?php else: ?>
                        <form method="POST">
                            <div class="form-group" style="margin-bottom: 1rem;">
                                <input type="text" name="name" class="form-control" placeholder="Your Name" required>
                            </div>
                            <div class="form-group" style="margin-bottom: 1rem;">
                                <input type="email" name="email" class="form-control" placeholder="Email Address" required>
                            </div>
                            <div class="form-group" style="margin-bottom: 1rem;">
                                <input type="tel" name="phone" class="form-control" placeholder="Phone Number" required>
                            </div>
                            <div class="form-group" style="margin-bottom: 1rem;">
                                <textarea name="message" class="form-control"
                                    placeholder="I'm interested in the <?php echo htmlspecialchars($property['title']); ?>..."
                                    rows="4" required style="resize: vertical;"></textarea>
                            </div>
                            <button type="submit" class="btn btn-secondary"
                                style="width: 100%; background: var(--primary); color: white;">Send Message</button>
                        </form>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .property-detail-grid {
        display: grid;
        grid-template-columns: 1fr;
        gap: 2rem;
    }

    @media (min-width: 992px) {
        .property-detail-grid {
            grid-template-columns: 2fr 1fr;
            align-items: start;
        }
    }
</style>

<?php include 'includes/footer.php'; ?>