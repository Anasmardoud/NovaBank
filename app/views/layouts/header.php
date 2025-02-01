<?php
// Default to 'home' if $currentPage is not set
$currentPage = $currentPage ?? 'home';
?>
<header>
    <nav>
        <div class="logo">
            <a href="/PHPLearning/NovaBank/public/">
                <img src="/PHPLearning/NovaBank/public/assets/images/logo.png" alt="Nova Bank Logo">
                <span class="bank-name">Nova Bank</span>
            </a>
        </div>
        <ul class="nav-links">
            <li>
                <a href="/PHPLearning/NovaBank/public/" class="<?php echo $currentPage === 'home' ? 'active' : ''; ?>">Home</a>
            </li>
            <li>
                <a href="/PHPLearning/NovaBank/public/login_page" class="<?php echo $currentPage === 'login' ? 'active' : ''; ?>">Login</a>
            </li>
        </ul>
    </nav>
</header>