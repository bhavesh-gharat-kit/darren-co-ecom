<?php
session_start();

require_once '../config/constant.php';
require_once '../config/db.php';

if (!isset($_SESSION['admin_id'])) {
    header("Location: " . ADMIN_URL . "auth/login.php");
    exit;
}

// ============================================
// FETCH DYNAMIC COUNTS (Only Products & Categories)
// ============================================

// Total Products
$total_products = $database->count("products");

// Total Categories
$total_categories = $database->count("categories");

// ============================================
// GET LOW STOCK PRODUCTS
// ============================================

$low_stock_products = $database->select("products", "*", [
    "stock[<=]" => 5,
    "ORDER" => ["stock" => "ASC"],
    "LIMIT" => 5
]);

include 'includes/header.php';
?>

<!-- Main Content with responsive margin -->
<div class="flex min-h-screen">
    <!-- Sidebar is included here -->
    <?php include 'includes/sidebar.php'; ?>
    
    <!-- Main content area -->
    <main class="flex-1 p-4 lg:p-8 pt-16 lg:pt-8 lg:ml-64">
        
        <!-- Page Header -->
        <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between mb-6 lg:mb-8">
            <div>
                <h1 class="text-2xl lg:text-3xl font-bold text-slate-800 flex items-center">
                    <i class="fas fa-chart-pie text-blue-600 mr-3"></i>
                    Dashboard
                </h1>
                <p class="text-sm text-gray-500 mt-1">
                    <i class="far fa-calendar-alt mr-1"></i>
                    Welcome back, <?= htmlspecialchars($_SESSION['admin_name'] ?? 'Admin'); ?>!
                </p>
            </div>
            <div class="mt-3 sm:mt-0">
                <span class="text-sm text-gray-500">
                    <i class="far fa-clock mr-1"></i>
                    Last updated: <?= date('d M Y, h:i A'); ?>
                </span>
            </div>
        </div>

        <!-- Stats Grid - Responsive -->
        <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-4 lg:gap-6 mb-6 lg:mb-8">
            
            <!-- Total Products (Dynamic) -->
             
            <a href="<?= ADMIN_URL ?>products/index.php" class="block">
            <div class="bg-white rounded-xl shadow-md p-4 lg:p-6 border-l-4 border-blue-500 hover:shadow-lg transition duration-300 group">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-500 text-sm font-medium">
                            <i class="fas fa-box text-blue-500 mr-1"></i>
                            Total Products
                        </p>
                        <h2 class="text-2xl lg:text-3xl font-bold text-slate-800 mt-2">
                            <?= number_format($total_products); ?>
                        </h2>
                        <p class="text-xs text-gray-400 mt-1">
                            <i class="fas fa-database mr-1"></i>
                            In your catalog
                        </p>
                    </div>
                    <div class="w-12 h-12 lg:w-14 lg:h-14 rounded-full bg-blue-100 flex items-center justify-center text-xl lg:text-2xl group-hover:scale-110 transition duration-300">
                        <i class="fas fa-boxes text-blue-600"></i>
                    </div>
                </div>
            </div>
            </a>

            <!-- Total Categories (Dynamic + Clickable) -->
            <a href="<?= ADMIN_URL ?>category/index.php" class="block">
                <div class="bg-white rounded-xl shadow-md p-4 lg:p-6 border-l-4 border-green-500 hover:shadow-lg hover:scale-[1.02] transition-all duration-300 cursor-pointer group">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-gray-500 text-sm font-medium">
                                <i class="fas fa-folder-open text-green-500 mr-1"></i>
                                Total Categories
                            </p>
                            <h2 class="text-2xl lg:text-3xl font-bold text-slate-800 mt-2">
                                <?= number_format($total_categories); ?>
                            </h2>
                            <p class="text-xs text-gray-400 mt-1">
                                <i class="fas fa-arrow-right mr-1"></i>
                                Click to manage
                            </p>
                        </div>
                        <div class="w-12 h-12 lg:w-14 lg:h-14 rounded-full bg-green-100 flex items-center justify-center text-xl lg:text-2xl group-hover:scale-110 transition duration-300">
                            <i class="fas fa-folder-tree text-green-600"></i>
                        </div>
                    </div>
                </div>
            </a>

            <!-- Total Categories (Dynamic + Clickable) -->
            <!-- <a href="<?= ADMIN_URL ?>banners/index.php" class="block">
                <div class="bg-white rounded-xl shadow-md p-4 lg:p-6 border-l-4 border-green-500 hover:shadow-lg hover:scale-[1.02] transition-all duration-300 cursor-pointer group">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-gray-500 text-sm font-medium">
                                <i class="fas fa-folder-open text-green-500 mr-1"></i>
                                Total Banners
                            </p>
                            <h2 class="text-2xl lg:text-3xl font-bold text-slate-800 mt-2">
                                <?= number_format($total_banners); ?>
                            </h2>
                            <p class="text-xs text-gray-400 mt-1">
                                <i class="fas fa-arrow-right mr-1"></i>
                                Click to manage
                            </p>
                        </div>
                        <div class="w-12 h-12 lg:w-14 lg:h-14 rounded-full bg-green-100 flex items-center justify-center text-xl lg:text-2xl group-hover:scale-110 transition duration-300">
                            <i class="fas fa-folder-tree text-green-600"></i>
                        </div>
                    </div>
                </div>
            </a> -->

            <!-- Total Customers (Static) -->
            <div class="bg-white rounded-xl shadow-md p-4 lg:p-6 border-l-4 border-yellow-500 hover:shadow-lg transition duration-300 group">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-500 text-sm font-medium">
                            <i class="fas fa-users text-yellow-500 mr-1"></i>
                            Total Customers
                        </p>
                        <h2 class="text-2xl lg:text-3xl font-bold text-slate-800 mt-2">
                            0
                        </h2>
                        <p class="text-xs text-gray-400 mt-1">
                            <i class="fas fa-user-plus mr-1"></i>
                            Registered users
                        </p>
                    </div>
                    <div class="w-12 h-12 lg:w-14 lg:h-14 rounded-full bg-yellow-100 flex items-center justify-center text-xl lg:text-2xl group-hover:scale-110 transition duration-300">
                        <i class="fas fa-user-circle text-yellow-600"></i>
                    </div>
                </div>
            </div>

            <!-- Total Orders (Static) -->
            <div class="bg-white rounded-xl shadow-md p-4 lg:p-6 border-l-4 border-purple-500 hover:shadow-lg transition duration-300 group">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-500 text-sm font-medium">
                            <i class="fas fa-shopping-cart text-purple-500 mr-1"></i>
                            Total Orders
                        </p>
                        <h2 class="text-2xl lg:text-3xl font-bold text-slate-800 mt-2">
                            0
                        </h2>
                        <p class="text-xs text-gray-400 mt-1">
                            <i class="fas fa-history mr-1"></i>
                            All time orders
                        </p>
                    </div>
                    <div class="w-12 h-12 lg:w-14 lg:h-14 rounded-full bg-purple-100 flex items-center justify-center text-xl lg:text-2xl group-hover:scale-110 transition duration-300">
                        <i class="fas fa-shopping-bag text-purple-600"></i>
                    </div>
                </div>
            </div>

            <!-- Revenue (Static) -->
            <div class="bg-white rounded-xl shadow-md p-4 lg:p-6 border-l-4 border-emerald-500 hover:shadow-lg transition duration-300 group">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-500 text-sm font-medium">
                            <i class="fas fa-coins text-emerald-500 mr-1"></i>
                            Revenue
                        </p>
                        <h2 class="text-2xl lg:text-3xl font-bold text-slate-800 mt-2">
                            ₹0.00
                        </h2>
                        <p class="text-xs text-gray-400 mt-1">
                            <i class="fas fa-check-circle mr-1"></i>
                            Completed orders
                        </p>
                    </div>
                    <div class="w-12 h-12 lg:w-14 lg:h-14 rounded-full bg-emerald-100 flex items-center justify-center text-xl lg:text-2xl group-hover:scale-110 transition duration-300">
                        <i class="fas fa-money-bill-wave text-emerald-600"></i>
                    </div>
                </div>
            </div>

            <!-- Pending Orders (Static) -->
            <div class="bg-white rounded-xl shadow-md p-4 lg:p-6 border-l-4 border-red-500 hover:shadow-lg transition duration-300 group">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-500 text-sm font-medium">
                            <i class="fas fa-clock text-red-500 mr-1"></i>
                            Pending Orders
                        </p>
                        <h2 class="text-2xl lg:text-3xl font-bold text-slate-800 mt-2">
                            0
                        </h2>
                        <p class="text-xs text-gray-400 mt-1">
                            <i class="fas fa-hourglass-half mr-1"></i>
                            Awaiting processing
                        </p>
                    </div>
                    <div class="w-12 h-12 lg:w-14 lg:h-14 rounded-full bg-red-100 flex items-center justify-center text-xl lg:text-2xl group-hover:scale-110 transition duration-300">
                        <i class="fas fa-hourglass-start text-red-600"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Additional Sections -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 lg:gap-6">
            
            <!-- Recent Orders (Static) -->
            <div class="bg-white rounded-xl shadow-md overflow-hidden">
                <div class="px-4 lg:px-6 py-4 border-b flex items-center justify-between">
                    <h3 class="font-semibold text-slate-800 flex items-center">
                        <i class="fas fa-clock text-blue-600 mr-2"></i>
                        Recent Orders
                    </h3>
                    <a href="<?= ADMIN_URL ?>orders/index.php" class="text-sm text-blue-600 hover:text-blue-700">
                        View All <i class="fas fa-arrow-right ml-1"></i>
                    </a>
                </div>
                <div class="p-4 lg:p-6">
                    <p class="text-gray-500 text-sm text-center py-4">
                        <i class="fas fa-inbox text-2xl block mb-2"></i>
                        No recent orders
                    </p>
                </div>
            </div>

            <!-- Low Stock Products (Dynamic) -->
            <div class="bg-white rounded-xl shadow-md overflow-hidden">
                <div class="px-4 lg:px-6 py-4 border-b flex items-center justify-between">
                    <h3 class="font-semibold text-slate-800 flex items-center">
                        <i class="fas fa-exclamation-triangle text-yellow-600 mr-2"></i>
                        Low Stock Alert
                    </h3>
                    <a href="<?= ADMIN_URL ?>products/index.php" class="text-sm text-blue-600 hover:text-blue-700">
                        View All <i class="fas fa-arrow-right ml-1"></i>
                    </a>
                </div>
                <div class="p-4 lg:p-6">
                    <?php if (!empty($low_stock_products)) : ?>
                        <div class="space-y-4">
                            <?php foreach ($low_stock_products as $product) : ?>
                                <div class="flex items-center justify-between border-b pb-3 last:border-0 last:pb-0">
                                    <div>
                                        <p class="font-medium text-sm text-slate-800">
                                            <?= htmlspecialchars($product['product_name']); ?>
                                        </p>
                                        <?php if (!empty($product['main_image'])) : ?>
                                            <img src="<?= BASE_URL ?>assets/uploads/products/<?= htmlspecialchars($product['main_image']); ?>" 
                                                 class="w-10 h-10 rounded object-cover mt-1">
                                        <?php endif; ?>
                                    </div>
                                    <div class="text-right">
                                        <span class="text-sm font-bold 
                                            <?= $product['stock'] == 0 ? 'text-red-600' : 'text-yellow-600'; ?>">
                                            <?= $product['stock']; ?> left
                                        </span>
                                        <?php if ($product['stock'] == 0) : ?>
                                            <span class="text-xs bg-red-100 text-red-700 px-2 py-1 rounded-full block mt-1">
                                                <i class="fas fa-times-circle mr-1"></i> Out of Stock
                                            </span>
                                        <?php else : ?>
                                            <span class="text-xs bg-yellow-100 text-yellow-700 px-2 py-1 rounded-full block mt-1">
                                                <i class="fas fa-exclamation-circle mr-1"></i> Low Stock
                                            </span>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php else : ?>
                        <p class="text-gray-500 text-sm text-center py-4">
                            <i class="fas fa-check-circle text-2xl text-green-500 block mb-2"></i>
                            All products are well stocked!
                        </p>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="mt-6 lg:mt-8 grid grid-cols-2 md:grid-cols-4 gap-4">
            <a href="<?= ADMIN_URL ?>products/add.php" 
               class="bg-blue-600 hover:bg-blue-700 text-white p-4 rounded-xl text-center transition duration-200 group">
                <i class="fas fa-plus-circle text-2xl block mb-2 group-hover:scale-110 transition duration-200"></i>
                <span class="text-sm font-medium">Add Product</span>
            </a>
            <a href="<?= ADMIN_URL ?>category/add.php" 
               class="bg-green-600 hover:bg-green-700 text-white p-4 rounded-xl text-center transition duration-200 group">
                <i class="fas fa-folder-plus text-2xl block mb-2 group-hover:scale-110 transition duration-200"></i>
                <span class="text-sm font-medium">Add Category</span>
            </a>
            <a href="<?= ADMIN_URL ?>orders/index.php" 
               class="bg-purple-600 hover:bg-purple-700 text-white p-4 rounded-xl text-center transition duration-200 group">
                <i class="fas fa-list-ul text-2xl block mb-2 group-hover:scale-110 transition duration-200"></i>
                <span class="text-sm font-medium">View Orders</span>
            </a>
            <a href="<?= ADMIN_URL ?>customers/index.php" 
               class="bg-yellow-600 hover:bg-yellow-700 text-white p-4 rounded-xl text-center transition duration-200 group">
                <i class="fas fa-user-plus text-2xl block mb-2 group-hover:scale-110 transition duration-200"></i>
                <span class="text-sm font-medium">Manage Customers</span>
            </a>
        </div>
        
    </main>
</div>

<?php include 'includes/footer.php'; ?>