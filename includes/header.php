<?php
$current_page = basename($_SERVER['PHP_SELF']);
$settings = get_settings($pdo);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>
        <?php echo isset($page_title) ? $page_title . ' | ' : ''; ?><?php echo htmlspecialchars($settings['site_name']); ?>
    </title>

    <!-- Google Fonts: Inter -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <?php if (!empty($settings['site_favicon'])): ?>
        <link rel="icon" href="<?php echo htmlspecialchars($settings['site_favicon']); ?>"
            type="image/<?php echo pathinfo($settings['site_favicon'], PATHINFO_EXTENSION); ?>">
    <?php endif; ?>

    <!-- Custom CSS -->
    <link rel="stylesheet" href="css/style.css?v=1.1">
</head>

<body>

    <header class="site-header">
        <div class="top-bar">
            <div class="container top-bar-inner">
                <div class="contact-info">
                    📞 <a
                        href="tel:<?php echo preg_replace('/[^0-9]/', '', $settings['contact_phone']); ?>"><?php echo htmlspecialchars($settings['contact_phone']); ?></a>
                    | 📍 <?php echo htmlspecialchars($settings['contact_address']); ?>
                </div>
                <div class="trust-badges hidden-mobile">
                    🔒 Secure Application | ⭐️⭐️⭐️⭐️⭐️ 5-Star Service
                </div>
            </div>
        </div>

        <nav class="main-nav">
            <div class="container nav-inner">
                <div class="logo">
                    <a href="index.php">
                        <?php if (!empty($settings['site_logo'])): ?>
                            <img src="<?php echo htmlspecialchars($settings['site_logo']); ?>"
                                alt="<?php echo htmlspecialchars($settings['site_name']); ?>" class="logo-img"
                                style="max-height: 50px; width: auto; display: block;">
                        <?php else: ?>
                            <span class="logo-text">
                                <?php
                                $name_parts = explode(' ', $settings['site_name'], 2);
                                echo htmlspecialchars($name_parts[0]);
                                if (isset($name_parts[1])) {
                                    echo '<span class="text-accent">' . htmlspecialchars($name_parts[1]) . '</span>';
                                }
                                ?>
                            </span>
                        <?php endif; ?>
                    </a>
                </div>

                <button class="mobile-menu-toggle" aria-label="Toggle menu" id="mobile-menu-btn">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                        stroke-linecap="round" stroke-linejoin="round">
                        <line x1="3" y1="12" x2="21" y2="12"></line>
                        <line x1="3" y1="6" x2="21" y2="6"></line>
                        <line x1="3" y1="18" x2="21" y2="18"></line>
                    </svg>
                </button>

                <div class="nav-links" id="nav-links">
                    <a href="index.php" class="<?php echo $current_page == 'index.php' ? 'active' : ''; ?>">Home</a>
                    <a href="inventory.php" class="<?php echo $current_page == 'inventory.php' ? 'active' : ''; ?>">Shop
                        Homes</a>
                    <a href="financing.php"
                        class="<?php echo $current_page == 'financing.php' ? 'active' : ''; ?>">Financing</a>
                    <a href="sell.php" class="<?php echo $current_page == 'sell.php' ? 'active' : ''; ?>">Sell Your
                        Home</a>
                    <a href="tel:<?php echo preg_replace('/[^0-9]/', '', $settings['contact_phone']); ?>"
                        class="btn btn-primary d-mobile-only" style="margin-top: 1rem; text-align: center;">Call Now</a>
                </div>
            </div>
        </nav>
    </header>
    <main class="site-main">