<?php
$page_title = 'Financing';
require_once 'includes/db.php';
include 'includes/header.php';

$message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $first_name = trim($_POST['first_name'] ?? '');
    $last_name = trim($_POST['last_name'] ?? '');
    $name = $first_name . ' ' . $last_name;
    $email = trim($_POST['email'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $credit = trim($_POST['credit'] ?? '');

    $details = json_encode(['credit_score' => $credit]);

    if (isset($pdo)) {
        try {
            $stmt = $pdo->prepare("INSERT INTO leads (type, name, email, phone, details) VALUES ('Financing', ?, ?, ?, ?)");
            $stmt->execute([$name, $email, $phone, $details]);
            $message = "Thank you, " . htmlspecialchars($first_name) . "! Your prequalification request has been securely submitted. A finance specialist will contact you shortly.";
        } catch (PDOException $e) {
            $message = "Error submitting request. Please try again later.";
        }
    } else {
        // Fallback demo mode
        $message = "Thank you, " . htmlspecialchars($first_name) . "! Your prequalification request has been securely submitted. (Static demo mode: No database connected)";
    }
}
?>

<div class="section">
    <div class="container">
        <div class="financing-grid" style="display: grid; grid-template-columns: 1fr; gap: 4rem; align-items: start;">

            <!-- Left Column: Copy -->
            <div>
                <span class="hero-kicker" style="color: var(--secondary);">Finance Your Dream</span>
                <h1 style="font-size: 3rem; margin-bottom: 1.5rem;">Homeownership is within your reach.</h1>
                <p style="font-size: 1.125rem; margin-bottom: 2rem; color: var(--text-muted);">We believe everyone
                    deserves a place to call home. With over 20 years of experience, our finance team has built
                    relationships with top national lenders and local credit unions to get you approved.</p>

                <div style="margin-top: 3rem;">
                    <div style="margin-bottom: 2rem; display: flex; gap: 1rem;">
                        <div style="font-size: 1.5rem;">🏠</div>
                        <div>
                            <h3 style="margin-bottom: 0.5rem;">Chattel (Home-Only) Loans</h3>
                            <p style="color: var(--text-muted);">Perfect if you are placing the home in a rented
                                community or park.</p>
                        </div>
                    </div>

                    <div style="margin-bottom: 2rem; display: flex; gap: 1rem;">
                        <div style="font-size: 1.5rem;">🌳</div>
                        <div>
                            <h3 style="margin-bottom: 0.5rem;">Land & Home Packages</h3>
                            <p style="color: var(--text-muted);">Bundle the cost of the home, the land, and the setup
                                into one single monthly payment.</p>
                        </div>
                    </div>

                    <div style="margin-bottom: 2rem; display: flex; gap: 1rem;">
                        <div style="font-size: 1.5rem;">🤝</div>
                        <div>
                            <h3 style="margin-bottom: 0.5rem;">Bad Credit / No Credit Programs</h3>
                            <p style="color: var(--text-muted);">Ask about our in-house seller financing and
                                lease-to-own options. We work with all backgrounds.</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Column: Form -->
            <div
                style="background-color: var(--bg-gray); padding: 3rem; border-radius: var(--radius-lg); border: 1px solid var(--border-color); box-shadow: var(--shadow-lg);">
                <h2 style="margin-bottom: 1.5rem;">Prequalify Today</h2>

                <?php if ($message): ?>
                    <div
                        style="background-color: #D1FAE5; color: #065F46; padding: 1.5rem; border-radius: var(--radius); margin-bottom: 2rem; border-left: 4px solid #10B981;">
                        <?php echo $message; ?>
                    </div>
                <?php else: ?>
                    <form method="POST" action="financing.php">
                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                            <div class="form-group">
                                <label class="form-label" for="first_name">First Name</label>
                                <input type="text" id="first_name" name="first_name" class="form-control" required>
                            </div>
                            <div class="form-group">
                                <label class="form-label" for="last_name">Last Name</label>
                                <input type="text" id="last_name" name="last_name" class="form-control" required>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="form-label" for="email">Email Address</label>
                            <input type="email" id="email" name="email" class="form-control" required>
                        </div>

                        <div class="form-group">
                            <label class="form-label" for="phone">Phone Number</label>
                            <input type="tel" id="phone" name="phone" class="form-control" required>
                        </div>

                        <div class="form-group">
                            <label class="form-label" for="credit">Estimated Credit Score</label>
                            <select id="credit" name="credit" class="form-control" required style="appearance: auto;">
                                <option value="" disabled selected>Select an option</option>
                                <option value="excellent">Excellent 720+</option>
                                <option value="good">Good 680-719</option>
                                <option value="fair">Fair 640-679</option>
                                <option value="needs_work">Needs Work &lt;640</option>
                                <option value="unknown">I don't know</option>
                            </select>
                        </div>

                        <button type="submit" class="btn btn-primary"
                            style="width: 100%; display: flex; justify-content: center; align-items: center; gap: 0.5rem; margin-top: 1rem;">
                            <span>🔒</span> Submit Application Securely
                        </button>

                        <p style="font-size: 0.75rem; color: var(--text-muted); margin-top: 1.5rem; text-align: center;">By
                            submitting, you agree to our terms. This will not affect your credit score.</p>
                    </form>
                <?php endif; ?>
            </div>

        </div>
    </div>
</div>

<style>
    @media (min-width: 1024px) {
        .financing-grid {
            grid-template-columns: 1fr 1fr !important;
        }
    }
</style>

<?php include 'includes/footer.php'; ?>