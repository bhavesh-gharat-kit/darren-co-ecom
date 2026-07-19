<?php
// Make sure constants are loaded
if (!defined('ADMIN_URL')) {
    require_once __DIR__ . '/../../config/constant.php';
}
?>

<!-- Mobile Hamburger Button -->
<button id="menuToggle" class="fixed top-4 left-4 z-50 lg:hidden bg-slate-800 text-white p-3 rounded-lg shadow-lg hover:bg-slate-700 transition">
    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
    </svg>
</button>

<!-- Overlay -->
<div id="overlay" class="fixed inset-0 bg-black bg-opacity-50 z-40 hidden lg:hidden"></div>

<!-- Sidebar -->
<aside id="sidebar" class="fixed left-0 top-0 h-full w-64 bg-slate-800 text-white flex flex-col z-50 transition-transform duration-300 -translate-x-full lg:translate-x-0">
    
    <!-- Close button for mobile -->
    <button id="closeMenu" class="absolute top-4 right-4 lg:hidden text-white hover:text-gray-300">
        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
        </svg>
    </button>

    <!-- Logo -->
    <div class="p-6 border-b border-slate-700">
        <h2 class="text-2xl font-bold">Admin Panel</h2>
        <p class="text-sm text-slate-400">Welcome, <?= $_SESSION['admin_name'] ?? 'Admin' ?></p>
    </div>

    <!-- Navigation -->
    <nav class="flex-1 p-4 space-y-2 overflow-y-auto">
        <a href="<?= ADMIN_URL ?>dashboard.php" 
            class="flex items-center space-x-3 rounded-lg px-4 py-3 hover:bg-slate-700 transition <?= basename($_SERVER['PHP_SELF']) == 'dashboard.php' ? 'bg-slate-700' : '' ?>">
            <span>📊</span>
            <span>Dashboard</span>
        </a>
        
        <a href="<?= ADMIN_URL ?>products/index.php" 
            class="flex items-center space-x-3 rounded-lg px-4 py-3 hover:bg-slate-700 transition <?= strpos($_SERVER['PHP_SELF'], 'products') !== false ? 'bg-slate-700' : '' ?>">
            <span>📦</span>
            <span>Products</span>
        </a>
        
        <a href="<?= ADMIN_URL ?>category/index.php" 
            class="flex items-center space-x-3 rounded-lg px-4 py-3 hover:bg-slate-700 transition <?= strpos($_SERVER['PHP_SELF'], 'categories') !== false ? 'bg-slate-700' : '' ?>">
            <span>📂</span>
            <span>Categories</span>
        </a>
        
        <a href="<?= ADMIN_URL ?>orders/index.php" 
            class="flex items-center space-x-3 rounded-lg px-4 py-3 hover:bg-slate-700 transition <?= strpos($_SERVER['PHP_SELF'], 'orders') !== false ? 'bg-slate-700' : '' ?>">
            <span>🛒</span>
            <span>Orders</span>
        </a>
        
        <a href="<?= ADMIN_URL ?>users/index.php" 
            class="flex items-center space-x-3 rounded-lg px-4 py-3 hover:bg-slate-700 transition <?= strpos($_SERVER['PHP_SELF'], 'users') !== false ? 'bg-slate-700' : '' ?>">
            <span>👥</span>
            <span>Customers</span>
        </a>

          <a href="<?= ADMIN_URL ?>banners/index.php" 
            class="flex items-center space-x-3 rounded-lg px-4 py-3 hover:bg-slate-700 transition <?= strpos($_SERVER['PHP_SELF'], 'users') !== false ? 'bg-slate-700' : '' ?>">
            <span>👥</span>
            <span>Banner</span>
        </a>
        
        <!-- Settings Link -->
        <a href="<?= ADMIN_URL ?>settings/index.php" 
            class="flex items-center space-x-3 rounded-lg px-4 py-3 hover:bg-slate-700 transition <?= strpos($_SERVER['PHP_SELF'], 'settings') !== false ? 'bg-slate-700' : '' ?>">
            <span>⚙️</span>
            <span>Settings</span>
        </a>
    </nav>

    <!-- Logout Button -->
    <div class="border-t border-slate-700 p-4">
        <a href="<?= ADMIN_URL ?>auth/logout.php"
            class="flex items-center justify-center rounded-lg bg-red-600 py-3 font-semibold hover:bg-red-700 transition">
            Logout
        </a>
    </div>

</aside>

<!-- JavaScript for Hamburger Menu -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    const sidebar = document.getElementById('sidebar');
    const overlay = document.getElementById('overlay');
    const menuToggle = document.getElementById('menuToggle');
    const closeMenu = document.getElementById('closeMenu');

    // Function to open sidebar
    function openSidebar() {
        sidebar.classList.remove('-translate-x-full');
        overlay.classList.remove('hidden');
        document.body.style.overflow = 'hidden';
    }

    // Function to close sidebar
    function closeSidebar() {
        sidebar.classList.add('-translate-x-full');
        overlay.classList.add('hidden');
        document.body.style.overflow = '';
    }

    // Toggle sidebar on hamburger click
    menuToggle.addEventListener('click', function() {
        if (sidebar.classList.contains('-translate-x-full')) {
            openSidebar();
        } else {
            closeSidebar();
        }
    });

    // Close sidebar on close button click
    closeMenu.addEventListener('click', closeSidebar);

    // Close sidebar on overlay click
    overlay.addEventListener('click', closeSidebar);

    // Close sidebar on escape key
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape' && !sidebar.classList.contains('-translate-x-full')) {
            closeSidebar();
        }
    });

    // Auto-close sidebar on window resize to desktop
    window.addEventListener('resize', function() {
        if (window.innerWidth >= 1024) {
            closeSidebar();
            sidebar.classList.remove('-translate-x-full');
        }
    });
});
</script>