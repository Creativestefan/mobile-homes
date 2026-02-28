<?php
$page_title = 'Sell Your Home';
require_once 'includes/db.php';
include 'includes/header.php';

$message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $make = trim($_POST['make'] ?? '');
    $year = trim($_POST['year'] ?? '');
    $size = trim($_POST['size'] ?? '');
    $location = trim($_POST['location'] ?? '');
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $phone = trim($_POST['phone'] ?? '');

    $details = json_encode([
        'make' => $make,
        'year' => $year,
        'size' => $size,
        'location' => $location
    ]);

    if (isset($pdo)) {
        try {
            $stmt = $pdo->prepare("INSERT INTO leads (type, name, email, phone, details) VALUES ('Sell', ?, ?, ?, ?)");
            $stmt->execute([$name, $email, $phone, $details]);
            $message = "Thank you! We've received your information about your " . htmlspecialchars($year) . " " . htmlspecialchars($make) . ". Our purchasing team will reach out with a cash offer within 48 hours.";
        } catch (PDOException $e) {
            $message = "Error submitting request. Please try again later.";
        }
    } else {
        // Fallback demo mode
        $message = "Thank you! We've received your information about your " . htmlspecialchars($year) . " " . htmlspecialchars($make) . ". (Static demo mode: No database connected)";
    }
}
?>

<!-- High contrast dark hero section -->
<div
    style="background-color: var(--primary); color: white; padding: 5rem 0; min-height: 80vh; display: flex; align-items: center;">
    <div class="container">
        <div class="sell-grid" style="display: grid; grid-template-columns: 1fr; gap: 4rem; align-items: center;">

            <!-- Left Copy -->
            <div>
                <span class="hero-kicker" style="color: var(--accent);">Cash Buyout Program</span>
                <h1 style="margin-bottom: 1.5rem; color: white;" class="text-center-mobile">Need to sell your mobile
                    home?<br>We
                    buy them for cash.</h1>
                <p style="font-size: 1.25rem; margin-bottom: 2.5rem; color: #E2E8F0;">Skip the real estate agents, the
                    repairs, and the waiting. We buy manufactured homes in any condition, handle the tear-down and
                    transport, and put cash in your hands fast.</p>

                <div style="margin-bottom: 2rem; display: flex; align-items: flex-start; gap: 1rem;">
                    <div
                        style="background-color: var(--secondary); color: white; width: 40px; height: 40px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 1.25rem; font-weight: bold; flex-shrink: 0;">
                        1</div>
                    <div>
                        <h3 style="color: white; margin-bottom: 0.25rem;">Fast Cash Offers</h3>
                        <p style="color: #94A3B8;">Get an evaluation and an offer within 48 hours.</p>
                    </div>
                </div>

                <div style="display: flex; align-items: flex-start; gap: 1rem;">
                    <div
                        style="background-color: var(--secondary); color: white; width: 40px; height: 40px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 1.25rem; font-weight: bold; flex-shrink: 0;">
                        2</div>
                    <div>
                        <h3 style="color: white; margin-bottom: 0.25rem;">We Handle Moving</h3>
                        <p style="color: #94A3B8;">Don't worry about logistics; our fleet handles removal from your lot.
                        </p>
                    </div>
                </div>
            </div>

            <!-- Right Form -->
            <div
                style="background-color: white; padding: 2.5rem; border-radius: var(--radius-lg); color: var(--text-main); box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.2);">
                <h2 style="margin-bottom: 1.5rem; text-align: center;">Get Your Fair Cash Offer</h2>

                <?php if ($message): ?>
                    <div
                        style="background-color: #ECFDF5; color: #065F46; padding: 1.5rem; border-radius: var(--radius); margin-bottom: 2rem; border-left: 4px solid #10B981; font-weight: 500;">
                        <?php echo $message; ?>
                    </div>
                <?php else: ?>
                    <form method="POST" action="sell.php">
                        <div class="form-group">
                            <label class="form-label" for="make">Make / Builder (e.g. Clayton)</label>
                            <input type="text" id="make" name="make" class="form-control" required
                                placeholder="Who built your home?">
                        </div>

                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                            <div class="form-group">
                                <label class="form-label" for="year">Year Built</label>
                                <input type="number" id="year" name="year" class="form-control" required placeholder="YYYY"
                                    min="1950" max="<?php echo date('Y'); ?>">
                            </div>
                            <div class="form-group">
                                <label class="form-label" for="size">Size Type</label>
                                <select id="size" name="size" class="form-control" required style="appearance: auto;">
                                    <option value="" disabled selected>Select size</option>
                                    <option value="Single Wide">Single Wide</option>
                                    <option value="Double Wide">Double Wide</option>
                                    <option value="Modular">Modular</option>
                                    <option value="RV/Park Model">RV / Park Model</option>
                                </select>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="form-label" for="location">Current Location</label>
                            <input type="text" id="location" name="location" class="form-control" required
                                placeholder="City, State">
                        </div>

                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                            <div class="form-group">
                                <label class="form-label" for="name">Your Name</label>
                                <input type="text" id="name" name="name" class="form-control" required>
                            </div>
                            <div class="form-group">
                                <label class="form-label" for="email">Email Address</label>
                                <input type="email" id="email" name="email" class="form-control" required>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="form-label" for="phone">Your Phone Number</label>
                            <input type="tel" id="phone" name="phone" class="form-control" required>
                        </div>

                        <button type="submit" class="btn btn-accent"
                            style="width: 100%; font-size: 1.125rem; margin-top: 1rem;">
                            Get My Cash Offer
                        </button>
                    </form>
                <?php endif; ?>
            </div>

        </div>
    </div>
</div>

<style>
    @media (min-width: 1024px) {
        .sell-grid {
            grid-template-columns: 1.2fr 1fr !important;
        }
    }
</style>

<?php include 'includes/footer.php'; ?>