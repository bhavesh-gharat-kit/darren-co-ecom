<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= isset($page_title) ? $page_title . ' - ' : '' ?>Darren & Co | Fashion Brand</title>

    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css">
    <script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Fraunces:opsz,wght@9..144,300;9..144,450;9..144,600;9..144,700&family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <style>
    /* ======================================================
       DARREN & CO - PREMIUM HEADER CSS
    ====================================================== */

    /* ---------- Google Fonts ---------- */
    @import url('https://fonts.googleapis.com/css2?family=Cormorant+Garamond:wght@500;600;700&family=Manrope:wght@300;400;500;600;700&display=swap');

    /* ---------- Root Variables ---------- */
    :root {
        --black: #080808;
        --secondary: #141414;
        --gold: #C6A769;
        --gold-light: #E7D3A8;
        --white: #ffffff;
        --cream: #F8F6F1;
        --gray: #8A8A8A;
        --border: #ECE4D7;
        --brown: #2C1810;
        --brown-dark: #1a0e0a;
        --brown-light: #4A2C1E;
    }

    /* ---------- Reset ---------- */
    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
    }

    html {
        scroll-behavior: smooth;
    }

    body {
        font-family: 'Manrope', sans-serif;
        background: var(--cream);
        color: var(--black);
        padding-top: 0;
    }

    /* ==========================================
       ANNOUNCEMENT BAR - BROWN (TOP)
    ========================================== */
    .announcement-bar {
        background: #2C1810 !important;
        color: #C6A769 !important;
        font-size: 13px;
        position: relative;
        z-index: 1002 !important;
        display: block !important;
        border-bottom: 1px solid rgba(198, 167, 105, 0.15);
        height: 40px;
        line-height: 40px;
        width: 100%;
        top: 0;
        left: 0;
    }

    .announcement-bar .container {
        max-width: 1280px;
        margin: 0 auto;
        padding: 0 20px;
        height: 100%;
    }

    .announcement-bar .flex {
        display: flex !important;
        align-items: center !important;
        justify-content: space-between !important;
        height: 40px !important;
    }

    .announcement-bar .announcement-text {
        color: #C6A769 !important;
        font-weight: 500;
        letter-spacing: 0.3px;
    }

    .announcement-bar .announcement-icon {
        color: #C6A769 !important;
        opacity: 0.8;
    }

    /* ==========================================
       HEADER - WHITE (BELOW ANNOUNCEMENT)
    ========================================== */
    #headerMain {
        position: sticky;
        top: 0;
        left: 0;
        width: 100%;
        z-index: 1001 !important;
        transition: .4s;
        background: rgba(255, 255, 255, 0.98);
        border-bottom: 1px solid #eee;
        box-shadow: 0 2px 20px rgba(0,0,0,0.06);
    }

    #headerMain .bg-white {
        background: rgba(255, 255, 255, 0.98) !important;
        backdrop-filter: none !important;
    }

    /* Sticky */
    #headerMain.scrolled {
        background: rgba(255, 255, 255, .98);
        box-shadow: 0 10px 40px rgba(0, 0, 0, .08);
        border-bottom: 1px solid var(--border);
    }

    /* ---------- Navigation ---------- */
    .nav-link {
        position: relative;
        color: #1a1a1a;
        font-size: 13px;
        font-weight: 600;
        padding: 8px 0;
        letter-spacing: 0.5px;
        transition: .3s;
        text-transform: uppercase;
    }

    .nav-link:hover {
        color: var(--gold);
    }

    .nav-link::after {
        content: "";
        position: absolute;
        left: 0;
        bottom: -4px;
        width: 0;
        height: 2px;
        background: var(--gold);
        transition: .4s;
    }

    .nav-link:hover::after {
        width: 100%;
    }

    .nav-link.active {
        color: var(--gold);
    }

    .nav-link.active::after {
        width: 100%;
    }

    /* ---------- Logo ---------- */
    .logo img {
        transition: .45s;
        height: 65px;
        width: auto;
        object-fit: contain;
    }

    .logo img:hover {
        transform: scale(1.05);
        filter: drop-shadow(0 8px 18px rgba(198, 167, 105, .30));
    }

    /* ---------- Search ---------- */
    .search-box {
        width: 240px;
        height: 42px;
        border: 1px solid #e0e0e0;
        border-radius: 30px;
        padding: 0 45px 0 18px;
        outline: none;
        background: #f8f8f8;
        transition: .35s;
        font-size: 13px;
    }

    .search-box:focus {
        border-color: var(--gold);
        background: #fff;
        box-shadow: 0 0 15px rgba(198, 167, 105, .15);
    }

    .search-box::placeholder {
        color: #999;
    }

    /* ---------- Icons ---------- */
    .icon-btn {
        position: relative;
        width: 40px;
        height: 40px;
        border-radius: 50%;
        border: 1px solid #e8e8e8;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #1a1a1a;
        transition: .35s;
        background: transparent;
        cursor: pointer;
    }

    .icon-btn:hover {
        background: var(--gold);
        color: #fff;
        border-color: var(--gold);
        transform: translateY(-2px);
    }

    /* ---------- Badge ---------- */
    .badge {
        position: absolute;
        top: -4px;
        right: -4px;
        width: 18px;
        height: 18px;
        border-radius: 50%;
        background: #2C1810;
        color: #fff;
        font-size: 9px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 700;
    }

    /* ---------- Login Button ---------- */
    .login-btn {
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 10px 28px;
        border-radius: 30px;
        border: 2px solid var(--gold);
        color: var(--gold);
        font-weight: 700;
        font-size: 12px;
        transition: .4s;
        background: transparent;
        text-transform: uppercase;
        letter-spacing: 1px;
    }

    .login-btn:hover {
        background: var(--gold);
        color: #fff;
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(198, 167, 105, 0.3);
    }

    /* ---------- Mega Menu ---------- */
    .mega-menu {
        width: 900px;
        background: #fff;
        border-radius: 18px;
        border: 1px solid #eee;
        box-shadow: 0 25px 70px rgba(0, 0, 0, .10);
        padding: 35px;
        margin-top: 25px;
    }

    .menu-title {
        font-size: 18px;
        margin-bottom: 15px;
        color: #111;
        font-family: 'Cormorant Garamond', serif;
    }

    .mega-menu a {
        display: block;
        color: #666;
        margin-bottom: 10px;
        font-size: 14px;
    }

    .mega-menu a:hover {
        color: var(--gold);
        padding-left: 8px;
    }

    /* ---------- Dropdown ---------- */
    .dropdown-link {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 13px 20px;
        color: #333;
        transition: .3s;
        font-size: 14px;
    }

    .dropdown-link:hover {
        background: #F8F6F1;
        color: var(--gold);
        padding-left: 28px;
    }

    /* ==========================
       Mobile Sidebar
    ========================== */
    #mobileMenu {
        background: #fff;
        overflow-y: auto;
        transition: all .45s ease;
        box-shadow: 20px 0 50px rgba(0, 0, 0, .15);
        width: 320px;
        max-width: 100%;
        z-index: 9999;
        position: fixed;
        top: 0;
        left: -100%;
        height: 100vh;
    }

    #mobileMenu.active {
        left: 0 !important;
    }

    .mobile-link {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 18px 0;
        border-bottom: 1px solid #efefef;
        color: #111;
        font-weight: 600;
        transition: .35s;
        font-size: 15px;
    }

    .mobile-link:hover {
        color: var(--gold);
        padding-left: 12px;
    }

    /* ==========================
       Mobile Search
    ========================== */
    .mobile-search {
        width: 100%;
        height: 48px;
        border-radius: 30px;
        border: 1px solid #ddd;
        padding: 0 50px 0 20px;
        outline: none;
        transition: .3s;
        font-size: 14px;
        background: #f8f8f8;
    }

    .mobile-search:focus {
        border-color: var(--gold);
        box-shadow: 0 0 15px rgba(198, 167, 105, .25);
        background: #fff;
    }

    /* ==========================
       Overlay
    ========================== */
    #overlay {
        opacity: 0;
        visibility: hidden;
        transition: .4s;
        background: rgba(0, 0, 0, 0.5);
        position: fixed;
        inset: 0;
        z-index: 9998;
    }

    #overlay.show {
        opacity: 1;
        visibility: visible;
    }

    /* ==========================
       Responsive
    ========================== */
    @media(max-width:1200px) {
        .search-box {
            width: 180px;
        }
        .desktop-nav {
            gap: 18px !important;
        }
        .nav-link {
            font-size: 12px;
        }
    }

    @media(max-width:991px) {
        .desktop-nav {
            display: none !important;
        }
        .login-btn {
            display: none !important;
        }
        .search-box {
            display: none !important;
        }
        #headerMain {
            position: sticky;
            top: 0;
        }
        .announcement-bar {
            height: 36px;
            line-height: 36px;
            font-size: 11px;
        }
        .announcement-bar .flex {
            height: 36px !important;
        }
        .logo img {
            height: 50px;
        }
        #headerMain .h-24 {
            height: 70px !important;
        }
        body {
            padding-top: 0;
        }
    }

    @media(max-width:768px) {
        .icon-btn {
            width: 36px;
            height: 36px;
        }
        .icon-btn i {
            font-size: 14px !important;
        }
        .announcement-bar {
            height: 34px;
            line-height: 34px;
            font-size: 10px;
        }
        .announcement-bar .flex {
            height: 34px !important;
        }
        .logo img {
            height: 45px;
        }
        #headerMain .h-24 {
            height: 64px !important;
        }
        .announcement-bar .hide-mobile {
            display: none !important;
        }
    }

    @media(max-width:576px) {
        #mobileMenu {
            width: 100%;
            max-width: 320px;
        }
        .logo img {
            height: 40px;
        }
        #headerMain .h-24 {
            height: 58px !important;
        }
        .announcement-bar {
            height: 32px;
            line-height: 32px;
            font-size: 9px;
        }
        .announcement-bar .flex {
            height: 32px !important;
        }
        .announcement-bar .announcement-text {
            font-size: 9px;
        }
    }

    /* ==========================
       Utility Classes
    ========================== */
    .gold {
        color: var(--gold);
    }

    .bg-gold {
        background: var(--gold);
    }

    .border-gold {
        border-color: var(--gold);
    }

    .text-premium {
        font-family: 'Cormorant Garamond', serif;
        letter-spacing: .5px;
    }

    .transition {
        transition: .35s ease;
    }

    /* Mobile menu button */
    #mobileMenuBtn {
        display: flex !important;
        align-items: center;
        justify-content: center;
        background: none;
        border: none;
        cursor: pointer;
        padding: 8px;
        color: #1a1a1a;
        font-size: 22px;
    }

    @media (min-width: 992px) {
        #mobileMenuBtn {
            display: none !important;
        }
    }

    /* Desktop nav */
    .desktop-nav {
        display: flex !important;
        align-items: center !important;
        gap: 28px !important;
    }

    @media (max-width: 991px) {
        .desktop-nav {
            display: none !important;
        }
    }

    /* Header height */
    #headerMain .h-24 {
        height: 80px !important;
    }

    @media (max-width: 768px) {
        #headerMain .h-24 {
            height: 70px !important;
        }
    }

    @media (max-width: 576px) {
        #headerMain .h-24 {
            height: 64px !important;
        }
    }

    /* Search button */
    .search-btn {
        background: none;
        border: none;
        cursor: pointer;
        color: #666;
        transition: .3s;
        position: absolute;
        right: 14px;
        top: 50%;
        transform: translateY(-50%);
    }

    .search-btn:hover {
        color: var(--gold);
    }

    /* Announcement bar text color */
    .announcement-bar .gold-text {
        color: #C6A769 !important;
    }

    /* Hide on extra small */
    @media (max-width: 480px) {
        .announcement-bar .hide-xs {
            display: none !important;
        }
        .announcement-bar .text-center-xs {
            text-align: center !important;
            width: 100% !important;
            justify-content: center !important;
        }
    }

    /* Brown announcement bar original color */
    .announcement-bar-original {
        background: #2C1810 !important;
    }
    </style>
</head>
<body>

<!-- ===========================
     ANNOUNCEMENT BAR - BROWN (TOP)
=========================== -->
<div class="announcement-bar" style="background: #080808 !important;">
    <div class="container">
        <div class="flex">
            <!-- Left: Features -->
            <div class="hidden lg:flex items-center gap-6">
                <span class="flex items-center gap-2 announcement-text">
                    <i class="fa-solid fa-truck-fast announcement-icon"></i>
                    Free Shipping Above ₹999
                </span>
                <span class="flex items-center gap-2 announcement-text">
                    <i class="fa-solid fa-award announcement-icon"></i>
                    Premium Quality Products
                </span>
                <span class="flex items-center gap-2 announcement-text">
                    <i class="fa-solid fa-arrow-rotate-left announcement-icon"></i>
                    Easy Returns
                </span>
            </div>

            <!-- Center: Welcome Text -->
            <div class="announcement-text text-center mx-auto lg:mx-0 text-sm font-medium">
                Welcome to Darren & Co
            </div>

            <!-- Right: Empty for spacing -->
            <div class="hidden lg:block w-48"></div>
        </div>
    </div>
</div>

<!-- ===========================
        HEADER - WHITE (BELOW)
=========================== -->
<header id="headerMain">
    <div class="bg-white border-b border-[#ece4d7]">
        <div class="container mx-auto px-4 lg:px-6">
            <div class="flex items-center justify-between h-24 lg:h-24">

                <!-- MOBILE MENU BUTTON -->
                <button id="mobileMenuBtn" class="lg:hidden text-2xl text-black" aria-label="Toggle Menu">
                    <i class="fas fa-bars"></i>
                </button>

                <!-- LOGO -->
                <div class="logo flex items-center">
                    <a href="<?= BASE_URL ?>" class="flex items-center">
                        <img src="<?= BASE_URL ?>assets/images/logo.png" alt="Darren & Co" class="h-16 lg:h-20 w-auto object-contain transition duration-500 hover:scale-105">
                    </a>
                </div>

                <!-- DESKTOP NAVIGATION -->
                <nav class="desktop-nav hidden lg:flex items-center gap-6 xl:gap-8">
                    <a href="<?= BASE_URL ?>" class="nav-link <?= basename($_SERVER['PHP_SELF']) == 'index.php' ? 'active' : '' ?>">Home</a>

                    <!-- SHOP DROPDOWN -->
                    <div class="relative group">
                        <a href="<?= BASE_URL ?>shop.php" class="nav-link flex items-center gap-1 <?= strpos($_SERVER['PHP_SELF'], 'shop') !== false ? 'active' : '' ?>">
                            Shop
                            <i class="fas fa-chevron-down text-[10px]"></i>
                        </a>

                        <!-- Mega Menu Dropdown -->
                        <div class="mega-menu absolute left-0 top-full opacity-0 invisible group-hover:visible group-hover:opacity-100 transition duration-300">
                            <div class="grid grid-cols-4 gap-6">
                                <div>
                                    <h4 class="menu-title text-[#2C1810]">Men</h4>
                                    <a href="#">T-Shirts</a>
                                    <a href="#">Shirts</a>
                                    <a href="#">Jeans</a>
                                    <a href="#">Shoes</a>
                                    <a href="#">Accessories</a>
                                </div>
                                <div>
                                    <h4 class="menu-title text-[#2C1810]">Women</h4>
                                    <a href="#">Dresses</a>
                                    <a href="#">Handbags</a>
                                    <a href="#">Heels</a>
                                    <a href="#">Jewellery</a>
                                    <a href="#">Scarves</a>
                                </div>
                                <div>
                                    <h4 class="menu-title text-[#2C1810]">Kids</h4>
                                    <a href="#">Boys</a>
                                    <a href="#">Girls</a>
                                    <a href="#">Footwear</a>
                                    <a href="#">Accessories</a>
                                    <a href="#">Toys</a>
                                </div>
                                <div>
                                    <h4 class="menu-title text-[#2C1810]">Featured</h4>
                                    <img src="<?= BASE_URL ?>assets/images/banner.jpg" class="rounded-xl w-full h-32 object-cover" alt="Featured">
                                    <p class="text-xs text-gray-500 mt-2">New Collection 2026</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <a href="<?= BASE_URL ?>collections.php" class="nav-link <?= strpos($_SERVER['PHP_SELF'], 'collections') !== false ? 'active' : '' ?>">Collections</a>
                    <a href="<?= BASE_URL ?>new-arrivals.php" class="nav-link <?= strpos($_SERVER['PHP_SELF'], 'new-arrivals') !== false ? 'active' : '' ?>">New Arrivals</a>
                    <a href="<?= BASE_URL ?>best-sellers.php" class="nav-link <?= strpos($_SERVER['PHP_SELF'], 'best-sellers') !== false ? 'active' : '' ?>">Best Sellers</a>
                    <a href="<?= BASE_URL ?>about.php" class="nav-link <?= strpos($_SERVER['PHP_SELF'], 'about') !== false ? 'active' : '' ?>">About</a>
                    <a href="<?= BASE_URL ?>contact.php" class="nav-link <?= strpos($_SERVER['PHP_SELF'], 'contact') !== false ? 'active' : '' ?>">Contact</a>
                </nav>

                <!-- RIGHT SIDE - Icons & Search -->
                <div class="flex items-center gap-3 lg:gap-4">

                    <!-- SEARCH -->
                    <div class="hidden lg:block relative">
                        <form action="<?= BASE_URL ?>search.php" method="GET" class="relative">
                            <input type="text" name="q" placeholder="Search luxury products..." class="search-box">
                            <button type="submit" class="search-btn">
                                <i class="fas fa-search text-gray-400 hover:text-[#C6A769]"></i>
                            </button>
                        </form>
                    </div>

                    <!-- WISHLIST -->
                    <a href="<?= USER_URL ?>wishlist.php" class="icon-btn relative">
                        <i class="far fa-heart text-lg"></i>
                        <span class="badge">0</span>
                    </a>

                    <!-- CART -->
                    <a href="<?= USER_URL ?>cart.php" class="icon-btn relative">
                        <i class="fas fa-bag-shopping text-lg"></i>
                        <span id="cartCount" class="badge">0</span>
                    </a>

                    <!-- USER ACCOUNT / LOGIN -->
                    <?php if (isset($_SESSION['user_id'])): ?>
                        <div class="relative group hidden lg:block">
                            <button class="icon-btn">
                                <i class="fas fa-user-circle text-xl"></i>
                            </button>
                            <div class="absolute right-0 mt-3 w-56 bg-white rounded-xl shadow-2xl border border-gray-100 opacity-0 invisible group-hover:opacity-100 group-hover:visible transition duration-300">
                                <div class="p-4 border-b">
                                    <h4 class="font-semibold text-sm"><?= $_SESSION['user_name'] ?? 'User'; ?></h4>
                                    <p class="text-xs text-gray-500">Welcome Back</p>
                                </div>
                                <div class="py-1">
                                    <a href="<?= USER_URL ?>profile.php" class="dropdown-link text-sm">
                                        <i class="fas fa-user w-5"></i> My Profile
                                    </a>
                                    <a href="<?= USER_URL ?>orders.php" class="dropdown-link text-sm">
                                        <i class="fas fa-box w-5"></i> My Orders
                                    </a>
                                    <a href="<?= USER_URL ?>wishlist.php" class="dropdown-link text-sm">
                                        <i class="fas fa-heart w-5"></i> Wishlist
                                    </a>
                                    <a href="<?= USER_URL ?>logout.php" class="dropdown-link text-sm text-red-600">
                                        <i class="fas fa-sign-out-alt w-5"></i> Logout
                                    </a>
                                </div>
                            </div>
                        </div>
                    <?php else: ?>
                        <a href="<?= USER_URL ?>login.php" class="login-btn hidden lg:flex">Login</a>
                    <?php endif; ?>

                </div>
                <!-- Right Side End -->

            </div>
        </div>
    </div>
</header>

<!-- ===========================
      MOBILE SIDEBAR
=========================== -->
<div id="mobileMenu">
    <div class="p-6">
        <div class="flex justify-between items-center">
            <img src="<?= BASE_URL ?>assets/images/logo.png" class="h-14" alt="Logo">
            <button id="closeMenu" class="text-2xl text-black hover:text-[#C6A769] transition">
                <i class="fas fa-times"></i>
            </button>
        </div>

        <div class="mt-6">
            <form action="<?= BASE_URL ?>search.php" method="GET" class="relative">
                <input type="text" name="q" placeholder="Search products..." class="mobile-search">
                <button class="absolute right-4 top-1/2 -translate-y-1/2 text-gray-400 hover:text-[#C6A769]">
                    <i class="fas fa-search"></i>
                </button>
            </form>
        </div>

        <nav class="mt-6 flex flex-col">
            <a href="<?= BASE_URL ?>" class="mobile-link">Home</a>
            <a href="<?= BASE_URL ?>shop.php" class="mobile-link">Shop</a>
            <a href="<?= BASE_URL ?>collections.php" class="mobile-link">Collections</a>
            <a href="<?= BASE_URL ?>new-arrivals.php" class="mobile-link">New Arrivals</a>
            <a href="<?= BASE_URL ?>best-sellers.php" class="mobile-link">Best Sellers</a>
            <a href="<?= BASE_URL ?>about.php" class="mobile-link">About</a>
            <a href="<?= BASE_URL ?>contact.php" class="mobile-link">Contact</a>

            <?php if (isset($_SESSION['user_id'])): ?>
                <a href="<?= USER_URL ?>profile.php" class="mobile-link">My Profile</a>
                <a href="<?= USER_URL ?>orders.php" class="mobile-link">My Orders</a>
                <a href="<?= USER_URL ?>logout.php" class="mobile-link text-red-600">Logout</a>
            <?php else: ?>
                <a href="<?= USER_URL ?>login.php" class="mobile-link text-[#C6A769]">Login</a>
                <a href="<?= USER_URL ?>register.php" class="mobile-link">Register</a>
            <?php endif; ?>
        </nav>

        <!-- Mobile Footer -->
        <div class="mt-8 pt-6 border-t border-gray-100">
            <div class="flex flex-col gap-2 text-sm text-gray-500">
                <span><i class="fa-solid fa-truck-fast mr-2"></i> Free Shipping Above ₹999</span>
                <span><i class="fa-solid fa-award mr-2"></i> Premium Quality Products</span>
                <span><i class="fa-solid fa-arrow-rotate-left mr-2"></i> Easy Returns</span>
            </div>
        </div>
    </div>
</div>

<!-- Overlay -->
<div id="overlay"></div>

<!-- ==========================================
        AOS Library
========================================== -->
<script src="https://unpkg.com/aos@2.3.4/dist/aos.js"></script>

<script>
    /* ==========================================
            AOS Initialization
    ========================================== */
    document.addEventListener('DOMContentLoaded', function() {
        AOS.init({
            duration: 1000,
            once: true,
            offset: 120,
            easing: 'ease-in-out'
        });
    });

    /* ==========================================
            Sticky Header
    ========================================== */
    const header = document.getElementById("headerMain");

    window.addEventListener("scroll", function() {
        if (window.scrollY > 80) {
            header.classList.add("scrolled");
        } else {
            header.classList.remove("scrolled");
        }
    });

    /* ==========================================
            Mobile Menu
    ========================================== */
    const mobileMenuBtn = document.getElementById("mobileMenuBtn");
    const closeMenu = document.getElementById("closeMenu");
    const mobileMenu = document.getElementById("mobileMenu");
    const overlay = document.getElementById("overlay");

    if (mobileMenuBtn) {
        mobileMenuBtn.addEventListener("click", function() {
            mobileMenu.classList.add("active");
            mobileMenu.style.left = "0";
            overlay.classList.add("show");
            document.body.style.overflow = "hidden";
        });
    }

    if (closeMenu) {
        closeMenu.addEventListener("click", closeSidebar);
    }

    if (overlay) {
        overlay.addEventListener("click", closeSidebar);
    }

    function closeSidebar() {
        mobileMenu.classList.remove("active");
        mobileMenu.style.left = "-100%";
        overlay.classList.remove("show");
        document.body.style.overflow = "";
    }

    /* ==========================================
            ESC Key Close
    ========================================== */
    document.addEventListener("keydown", function(e) {
        if (e.key === "Escape") {
            closeSidebar();
        }
    });

    /* ==========================================
            Cart Count
    ========================================== */
    function updateCartCount() {
        fetch("<?= USER_URL ?>get-cart-count.php")
            .then(response => response.json())
            .then(data => {
                const cart = document.getElementById("cartCount");
                if (cart) {
                    cart.innerHTML = data.count ?? 0;
                }
            })
            .catch(() => {});
    }
    updateCartCount();

    /* ==========================================
            Console Branding
    ========================================== */
    console.log(
        "%c Darren & Co Premium Header Loaded",
        "background:#2C1810;color:#C6A769;padding:8px 16px;border-radius:4px;font-size:14px;font-weight:bold;"
    );

    /* ==========================================
            Close mobile menu on link click
    ========================================== */
    document.querySelectorAll('.mobile-link').forEach(link => {
        link.addEventListener('click', closeSidebar);
    });
</script>