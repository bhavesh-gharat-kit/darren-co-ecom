<?php
session_start();

require_once '../../config/constant.php';
require_once '../../config/db.php';

$page_title = "Products";

// Authentication Check
if (!isset($_SESSION['admin_id'])) {
    header("Location: " . ADMIN_URL . "auth/login.php");
    exit;
}

// ============================================
// INCLUDE FILES
// ============================================

include_once '../includes/pagination.php';
include_once '../includes/search-bar.php';

// ============================================
// GET SEARCH TERM
// ============================================

$search = isset($_GET['search']) ? trim($_GET['search']) : '';

// ============================================
// GET ITEMS PER PAGE
// ============================================

$items_per_page = isset($_GET['limit']) && is_numeric($_GET['limit']) 
                  ? (int)$_GET['limit'] 
                  : 12;

// ============================================
// GET VIEW MODE (Grid or List)
// ============================================

$view_mode = isset($_GET['view']) && $_GET['view'] == 'list' ? 'list' : 'grid';

// ============================================
// BUILD WHERE CONDITIONS
// ============================================

$where = [];

if (!empty($search)) {
    $where["OR"] = [
        "product_name[~]" => $search,
        "description[~]" => $search
    ];
}

// ============================================
// GET TOTAL COUNT
// ============================================

if (!empty($where)) {
    $total_products = $database->count("products", $where);
} else {
    $total_products = $database->count("products");
}

// ============================================
// GET PAGINATION DATA
// ============================================

$pagination = getPagination($total_products, $items_per_page);
$offset = $pagination['offset'];

$query_params = $where;

$query_params["ORDER"] = [
    "id" => "DESC"
];

$query_params["LIMIT"] = [
    $offset,
    $items_per_page
];

// ============================================
// FETCH PRODUCTS WITH JOIN
// ============================================

$products = $database->select(
    "products",
    [
        "[>]categories" => ["category_id" => "id"]
    ],
    [
        "products.id",
        "products.product_name",
        "products.description",
        "products.main_image",
        // "products.gallery_images",
        "products.selling_price",
        "products.discount_price",
        "products.stock",
        "products.is_best_selling",
        "products.is_new_arrival",
        "products.is_trending",
        "products.is_customer_favorite",
        "products.status",
        "products.created_at",
        "categories.category_name"
    ],
    $query_params
);

// ============================================
// INCLUDE HEADER AND SIDEBAR
// ============================================

include '../includes/header.php';
include '../includes/sidebar.php';

?>

<div class="lg:ml-64 min-h-screen bg-gray-100 p-4 md:p-6 lg:p-8">

    <!-- Page Header -->
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between mb-6 gap-4">
        <div class="w-full sm:w-auto">
            <h1 class="text-2xl md:text-3xl font-bold text-gray-800 flex items-center">
                <i class="fas fa-boxes text-blue-600 mr-2"></i>
                Product Management
            </h1>
            <p class="text-sm md:text-base text-gray-500 mt-1">
                Manage all products in your store
                <?php if (!empty($search)): ?>
                    <span class="bg-blue-100 text-blue-700 px-2 py-1 rounded ml-2 inline-block mt-1 sm:mt-0">
                        <i class="fas fa-search mr-1"></i> Results for: "<?= htmlspecialchars($search) ?>"
                    </span>
                <?php endif; ?>
            </p>
            <p class="text-xs md:text-sm text-gray-400 mt-1">
                <i class="fas fa-layer-group mr-1"></i>
                Showing <?= $pagination['start_number'] ?> to <?= $pagination['end_number'] ?> 
                of <?= $total_products ?> products
            </p>
        </div>
        
        <div class="flex flex-wrap gap-2 w-full sm:w-auto">
            <a href="add.php" 
               class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 md:px-5 md:py-3 rounded-lg font-medium text-sm md:text-base transition duration-200 flex items-center justify-center w-full sm:w-auto">
                <i class="fas fa-plus mr-2"></i> Add Product
            </a>
        </div>
    </div>

    <!-- Search and View Controls -->
    <div class="flex flex-col sm:flex-row gap-4 mb-5">
        <div class="flex-1">
            <?= renderSearchBar([
                'search' => $search,
                'placeholder' => 'Search products by name or description...',
                'action' => 'index.php'
            ]); ?>
        </div>
        
        <div class="flex gap-2 items-center">
            <!-- View Toggle -->
            <div class="bg-white rounded-lg shadow p-1 flex">
                <a href="?<?= http_build_query(array_merge($_GET, ['view' => 'grid'])) ?>" 
                   class="px-3 py-2 rounded <?= $view_mode == 'grid' ? 'bg-blue-600 text-white' : 'text-gray-600 hover:bg-gray-100' ?> transition duration-200">
                    <i class="fas fa-th"></i>
                </a>
                <a href="?<?= http_build_query(array_merge($_GET, ['view' => 'list'])) ?>" 
                   class="px-3 py-2 rounded <?= $view_mode == 'list' ? 'bg-blue-600 text-white' : 'text-gray-600 hover:bg-gray-100' ?> transition duration-200">
                    <i class="fas fa-list"></i>
                </a>
            </div>
            
            <!-- Items Per Page -->
            <div class="bg-white rounded-lg shadow">
                <select onchange="window.location.href='?' + new URLSearchParams({...Object.fromEntries(new URLSearchParams(window.location.search)), limit: this.value}).toString()"
                        class="px-3 py-2 rounded-lg border-0 focus:ring-2 focus:ring-blue-500 outline-none text-sm">
                    <option value="12" <?= $items_per_page == 12 ? 'selected' : '' ?>>12</option>
                    <option value="24" <?= $items_per_page == 24 ? 'selected' : '' ?>>24</option>
                    <option value="48" <?= $items_per_page == 48 ? 'selected' : '' ?>>48</option>
                    <option value="96" <?= $items_per_page == 96 ? 'selected' : '' ?>>96</option>
                </select>
            </div>
        </div>
    </div>

    <!-- Success Messages -->
    <?php if (isset($_GET['success'])) : ?>
        <div class="mb-5">
            <?php if ($_GET['success'] == "added") : ?>
                <div class="bg-green-100 border border-green-300 text-green-700 px-4 py-3 rounded-lg">
                    <i class="fas fa-check-circle mr-2"></i> Product Added Successfully.
                </div>
            <?php elseif ($_GET['success'] == "updated") : ?>
                <div class="bg-blue-100 border border-blue-300 text-blue-700 px-4 py-3 rounded-lg">
                    <i class="fas fa-check-circle mr-2"></i> Product Updated Successfully.
                </div>
            <?php elseif ($_GET['success'] == "deleted") : ?>
                <div class="bg-red-100 border border-red-300 text-red-700 px-4 py-3 rounded-lg">
                    <i class="fas fa-trash-alt mr-2"></i> Product Deleted Successfully.
                </div>
            <?php endif; ?>
        </div>
    <?php endif; ?>

    <?php if (!empty($products)) : ?>
        
        <?php if ($view_mode == 'grid') : ?>
            
            <!-- Grid View -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4 md:gap-6">
                <?php foreach ($products as $product) : ?>
                    <div class="bg-white rounded-xl shadow hover:shadow-lg transition duration-300 overflow-hidden group">
                        
                        <!-- Product Image -->
                        <div class="relative overflow-hidden bg-gray-100">
                            <?php if (!empty($product['main_image'])) : ?>
                                <img src="<?= BASE_URL ?>assets/uploads/products/<?= htmlspecialchars($product['main_image']); ?>" 
                                     alt="<?= htmlspecialchars($product['product_name']); ?>"
                                     class="w-full h-48 object-cover group-hover:scale-105 transition duration-300">
                            <?php else : ?>
                                <div class="w-full h-48 flex items-center justify-center bg-gray-200">
                                    <i class="fas fa-image text-4xl text-gray-400"></i>
                                </div>
                            <?php endif; ?>
                            
                            <!-- Badges -->
                            <div class="absolute top-2 left-2 flex flex-wrap gap-1">
                                <?php if ($product['is_best_selling'] == 1) : ?>
                                    <span class="bg-red-500 text-white text-xs px-2 py-1 rounded-full">
                                        <i class="fas fa-fire mr-1"></i> Best Seller
                                    </span>
                                <?php endif; ?>
                                <?php if ($product['is_new_arrival'] == 1) : ?>
                                    <span class="bg-blue-500 text-white text-xs px-2 py-1 rounded-full">
                                        <i class="fas fa-star mr-1"></i> New
                                    </span>
                                <?php endif; ?>
                                <?php if ($product['is_trending'] == 1) : ?>
                                    <span class="bg-green-500 text-white text-xs px-2 py-1 rounded-full">
                                        <i class="fas fa-chart-line mr-1"></i> Trending
                                    </span>
                                <?php endif; ?>
                                <?php if ($product['is_customer_favorite'] == 1) : ?>
                                    <span class="bg-pink-500 text-white text-xs px-2 py-1 rounded-full">
                                        <i class="fas fa-heart mr-1"></i> Favorite
                                    </span>
                                <?php endif; ?>
                            </div>
                            
                            <!-- Status Badge -->
                            <div class="absolute top-2 right-2">
                                <?php if ($product['status'] == "Active") : ?>
                                    <span class="bg-green-500 text-white text-xs px-2 py-1 rounded-full">
                                        <i class="fas fa-circle text-xs mr-1"></i> Active
                                    </span>
                                <?php else : ?>
                                    <span class="bg-red-500 text-white text-xs px-2 py-1 rounded-full">
                                        <i class="fas fa-circle text-xs mr-1"></i> Inactive
                                    </span>
                                <?php endif; ?>
                            </div>
                            
                            <!-- Stock Badge -->
                            <div class="absolute bottom-2 right-2">
                                <?php if ($product['stock'] > 10) : ?>
                                    <span class="bg-green-600 text-white text-xs px-2 py-1 rounded-full">
                                        <i class="fas fa-box mr-1"></i> <?= $product['stock']; ?> in stock
                                    </span>
                                <?php elseif ($product['stock'] > 0) : ?>
                                    <span class="bg-yellow-500 text-white text-xs px-2 py-1 rounded-full">
                                        <i class="fas fa-box mr-1"></i> <?= $product['stock']; ?> left
                                    </span>
                                <?php else : ?>
                                    <span class="bg-red-600 text-white text-xs px-2 py-1 rounded-full">
                                        <i class="fas fa-box mr-1"></i> Out of Stock
                                    </span>
                                <?php endif; ?>
                            </div>
                        </div>
                        
                        <!-- Product Info -->
                        <div class="p-4">
                            <div class="mb-2">
                                <span class="text-xs text-blue-600 font-medium">
                                    <i class="fas fa-folder-open mr-1"></i>
                                    <?= htmlspecialchars($product['category_name'] ?? 'Uncategorized'); ?>
                                </span>
                            </div>
                            
                            <h3 class="font-semibold text-gray-800 text-lg mb-1 truncate">
                                <?= htmlspecialchars($product['product_name']); ?>
                            </h3>
                            
                            <p class="text-sm text-gray-600 mb-3 line-clamp-2">
                                <?= htmlspecialchars(substr($product['description'] ?? '', 0, 80)); ?>
                                <?= (strlen($product['description'] ?? '') > 80) ? '...' : '' ?>
                            </p>
                            
                            <div class="flex items-center justify-between mb-3">
                                <div>
                                    <?php if (!empty($product['discount_price']) && $product['discount_price'] > 0) : ?>
                                        <span class="text-xl font-bold text-green-600">
                                            $<?= number_format($product['discount_price'], 2); ?>
                                        </span>
                                        <span class="text-sm text-gray-400 line-through ml-2">
                                            $<?= number_format($product['selling_price'], 2); ?>
                                        </span>
                                        <?php 
                                            $discount_percent = round((($product['selling_price'] - $product['discount_price']) / $product['selling_price']) * 100);
                                        ?>
                                        <span class="text-xs text-red-500 font-medium ml-1">
                                            -<?= $discount_percent; ?>%
                                        </span>
                                    <?php else : ?>
                                        <span class="text-xl font-bold text-gray-800">
                                            $<?= number_format($product['selling_price'], 2); ?>
                                        </span>
                                    <?php endif; ?>
                                </div>
                            </div>
                            
                            <!-- Action Buttons -->
                            <div class="flex gap-2">
                                <a href="edit.php?id=<?= $product['id']; ?>" 
                                   class="flex-1 bg-yellow-500 hover:bg-yellow-600 text-white px-3 py-2 rounded-lg text-sm text-center transition duration-200">
                                    <i class="fas fa-edit mr-1"></i> Edit
                                </a>
                                <a href="delete.php?id=<?= $product['id']; ?>" 
                                   onclick="return confirm('Are you sure you want to delete this product?');"
                                   class="flex-1 bg-red-600 hover:bg-red-700 text-white px-3 py-2 rounded-lg text-sm text-center transition duration-200">
                                    <i class="fas fa-trash mr-1"></i> Delete
                                </a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
            
        <?php else : ?>
            
            <!-- List View -->
            <div class="bg-white rounded-xl shadow overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full min-w-[768px]">
                        <thead class="bg-gray-100">
                            <tr>
                                <th class="px-4 md:px-6 py-3 md:py-4 text-left text-sm font-semibold text-gray-700">#</th>
                                <th class="px-4 md:px-6 py-3 md:py-4 text-left text-sm font-semibold text-gray-700">Image</th>
                                <th class="px-4 md:px-6 py-3 md:py-4 text-left text-sm font-semibold text-gray-700">Product</th>
                                <th class="px-4 md:px-6 py-3 md:py-4 text-left text-sm font-semibold text-gray-700 hidden md:table-cell">Category</th>
                                <th class="px-4 md:px-6 py-3 md:py-4 text-left text-sm font-semibold text-gray-700">Price</th>
                                <th class="px-4 md:px-6 py-3 md:py-4 text-left text-sm font-semibold text-gray-700 hidden lg:table-cell">Stock</th>
                                <th class="px-4 md:px-6 py-3 md:py-4 text-left text-sm font-semibold text-gray-700 hidden xl:table-cell">Status</th>
                                <th class="px-4 md:px-6 py-3 md:py-4 text-center text-sm font-semibold text-gray-700">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($products as $key => $product) : ?>
                                <tr class="border-t hover:bg-gray-50 transition duration-150">
                                    <td class="px-4 md:px-6 py-3 md:py-4 text-sm">
                                        <?= $pagination['start_number'] + $key; ?>
                                    </td>
                                    <td class="px-4 md:px-6 py-3 md:py-4">
                                        <?php if (!empty($product['main_image'])) : ?>
                                            <img src="<?= BASE_URL ?>assets/uploads/products/<?= htmlspecialchars($product['main_image']); ?>" 
                                                 class="w-12 h-12 md:w-16 md:h-16 rounded object-cover"
                                                 alt="<?= htmlspecialchars($product['product_name']); ?>">
                                        <?php else : ?>
                                            <div class="w-12 h-12 md:w-16 md:h-16 rounded bg-gray-200 flex items-center justify-center">
                                                <i class="fas fa-image text-gray-400"></i>
                                            </div>
                                        <?php endif; ?>
                                    </td>
                                    <td class="px-4 md:px-6 py-3 md:py-4">
                                        <div class="font-medium text-sm">
                                            <?= htmlspecialchars($product['product_name']); ?>
                                        </div>
                                        <div class="text-xs text-gray-500 truncate max-w-[150px]">
                                            <?= htmlspecialchars(substr($product['description'] ?? '', 0, 50)); ?>
                                        </div>
                                    </td>
                                    <td class="px-4 md:px-6 py-3 md:py-4 text-sm hidden md:table-cell">
                                        <?= htmlspecialchars($product['category_name'] ?? 'Uncategorized'); ?>
                                    </td>
                                    <td class="px-4 md:px-6 py-3 md:py-4">
                                        <?php if (!empty($product['discount_price']) && $product['discount_price'] > 0) : ?>
                                            <div class="text-sm">
                                                <span class="font-bold text-green-600">
                                                    $<?= number_format($product['discount_price'], 2); ?>
                                                </span>
                                                <span class="text-xs text-gray-400 line-through block">
                                                    $<?= number_format($product['selling_price'], 2); ?>
                                                </span>
                                            </div>
                                        <?php else : ?>
                                            <span class="text-sm font-bold text-gray-800">
                                                $<?= number_format($product['selling_price'], 2); ?>
                                            </span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="px-4 md:px-6 py-3 md:py-4 hidden lg:table-cell">
                                        <?php if ($product['stock'] > 10) : ?>
                                            <span class="text-green-600 text-sm">
                                                <i class="fas fa-check-circle mr-1"></i> <?= $product['stock']; ?>
                                            </span>
                                        <?php elseif ($product['stock'] > 0) : ?>
                                            <span class="text-yellow-600 text-sm">
                                                <i class="fas fa-exclamation-circle mr-1"></i> <?= $product['stock']; ?>
                                            </span>
                                        <?php else : ?>
                                            <span class="text-red-600 text-sm">
                                                <i class="fas fa-times-circle mr-1"></i> Out of Stock
                                            </span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="px-4 md:px-6 py-3 md:py-4 hidden xl:table-cell">
                                        <?php if ($product['status'] == "Active") : ?>
                                            <span class="bg-green-100 text-green-700 px-2 py-1 rounded-full text-xs">
                                                <i class="fas fa-circle text-xs mr-1"></i> Active
                                            </span>
                                        <?php else : ?>
                                            <span class="bg-red-100 text-red-700 px-2 py-1 rounded-full text-xs">
                                                <i class="fas fa-circle text-xs mr-1"></i> Inactive
                                            </span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="px-4 md:px-6 py-3 md:py-4">
                                        <div class="flex flex-col sm:flex-row justify-center gap-1 sm:gap-2">
                                            <a href="edit.php?id=<?= $product['id']; ?>" 
                                               class="bg-yellow-500 hover:bg-yellow-600 text-white px-2 py-1 md:px-3 md:py-1.5 rounded text-xs md:text-sm text-center transition duration-200">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <a href="delete.php?id=<?= $product['id']; ?>" 
                                               onclick="return confirm('Are you sure you want to delete this product?');"
                                               class="bg-red-600 hover:bg-red-700 text-white px-2 py-1 md:px-3 md:py-1.5 rounded text-xs md:text-sm text-center transition duration-200">
                                                <i class="fas fa-trash"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
            
        <?php endif; ?>
        
        <!-- Pagination -->
        <div class="mt-5">
            <?= renderPagination($pagination, [
                'show_items_per_page' => true,
                'show_first_last' => true,
                'items_per_page_options' => [12, 24, 48, 96]
            ]); ?>
        </div>
        
    <?php else : ?>
        
        <!-- Empty State -->
        <div class="bg-white rounded-xl shadow p-12 text-center">
            <i class="fas fa-boxes text-6xl text-gray-300 mb-4"></i>
            <?php if (!empty($search)): ?>
                <h3 class="text-xl font-semibold text-gray-700 mb-2">No products found</h3>
                <p class="text-gray-500 mb-4">No products matching "<strong><?= htmlspecialchars($search) ?></strong>"</p>
                <a href="index.php" class="text-blue-600 hover:underline">
                    <i class="fas fa-times mr-1"></i> Clear search
                </a>
            <?php else: ?>
                <h3 class="text-xl font-semibold text-gray-700 mb-2">No products yet</h3>
                <p class="text-gray-500 mb-4">Start adding products to your store</p>
                <a href="add.php" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-lg font-medium inline-block transition duration-200">
                    <i class="fas fa-plus mr-2"></i> Add Your First Product
                </a>
            <?php endif; ?>
        </div>
        
    <?php endif; ?>
    
</div>

<?php include '../includes/footer.php'; ?>