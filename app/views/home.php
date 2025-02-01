<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nova Bank - Home</title>
    <link rel="stylesheet" href="/PHPLearning/NovaBank/public/assets/css/global.css">
    <link rel="stylesheet" href="/PHPLearning/NovaBank/public/assets/css/home.css">
    <!-- Include Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>

<body>
    <?php $currentPage = 'home';
    include __DIR__ . '/layouts/header.php'; ?>

    <main>
        <!-- Hero Section -->
        <section class="hero">
            <div class="hero-container">
                <!-- Left Section: Branding and Buttons -->
                <div class="hero-left">
                    <h1>Nova Bank</h1>
                    <p class="tagline">Your trusted partner in modern banking.</p>
                    <ul class="features-list">
                        <li><i class="fas fa-check-circle"></i> Secure Transactions</li>
                        <li><i class="fas fa-check-circle"></i> 24/7 Customer Support</li>
                        <li><i class="fas fa-check-circle"></i> Easy Account Management</li>
                        <li><i class="fas fa-check-circle"></i> Fast Loan Approvals</li>
                    </ul>
                    <p class="cta-text">Join thousands of satisfied customers today!</p>
                    <a href="/PHPLearning/NovaBank/public/login_page" class="cta-button">Get Started</a>
                </div>

                <!-- Right Section: Image -->
                <div class="hero-right">
                    <img src="/PHPLearning/NovaBank/public/assets/images/bank-left-image.jpg" alt="Bank Image">
                </div>
            </div>
        </section>
    </main>

    <!-- Footer Section -->
    <?php include __DIR__ . '/layouts/footer.php'; ?>

    <script src="/PHPLearning/NovaBank/public/assets/js/global.js"></script>
    <script src="/PHPLearning/NovaBank/public/assets/js/home.js"></script>
</body>

</html>