<header>
    <nav>
        <div class="logo">
            <a href="/PHPLearning/NovaBank/public/index.php">
                <img src="/PHPLearning/NovaBank/public/assets/images/logo.png" alt="Nova Bank Logo">
                <span class="bank-name">Nova Bank</span>
            </a>
        </div>
        <ul class="nav-links">
            <li><a href="/PHPLearning/NovaBank/public/index.php" class="<?php echo (basename($_SERVER['PHP_SELF']) == 'index.php') ? 'active' : ''; ?>">Home</a></li>
            <li><a href="/PHPLearning/NovaBank/public/login.php" class="<?php echo (basename($_SERVER['PHP_SELF']) == 'login.php') ? 'active' : ''; ?>">Login</a></li>
        </ul>
    </nav>
</header>