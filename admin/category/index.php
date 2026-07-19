<?php
session_start();

require_once '../../config/constant.php';
require_once '../../config/db.php';

$page_title = "Categories";

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
                  : 10;

// ============================================
// BUILD WHERE CONDITIONS
// ============================================

$where = [];

if (!empty($search)) {
    $where["OR"] = [
        "category_name[~]" => $search,
        "description[~]" => $search
    ];
}

// ============================================
// GET TOTAL COUNT
// ============================================

if (!empty($where)) {
    $total_categories = $database->count("categories", $where);
} else {
    $total_categories = $database->count("categories");
}

// ============================================
// GET PAGINATION DATA
// ============================================

$pagination = getPagination($total_categories, $items_per_page);
$offset = $pagination['offset'];

$query_params = $where;

$query_params["ORDER"] = [
    "id" => "DESC"
];

$query_params["LIMIT"] = [
    $offset,
    $items_per_page
];

$categories = $database->select(
    "categories",
    "*",
    $query_params
);

// ============================================
// FETCH DATA
// ============================================

$categories = $database->select("categories", "*", $query_params);

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
            <h1 class="text-2xl md:text-3xl font-bold text-gray-800">
                Category Management
            </h1>
            <p class="text-sm md:text-base text-gray-500 mt-1">
                Manage all product categories
                <?php if (!empty($search)): ?>
                    <span class="bg-blue-100 text-blue-700 px-2 py-1 rounded ml-2 inline-block mt-1 sm:mt-0">
                        <i class="fas fa-search mr-1"></i> Results for: "<?= htmlspecialchars($search) ?>"
                    </span>
                <?php endif; ?>
            </p>
            <p class="text-xs md:text-sm text-gray-400 mt-1">
                Showing <?= $pagination['start_number'] ?> to <?= $pagination['end_number'] ?> 
                of <?= $total_categories ?> categories
            </p>
        </div>
        
        <a href="add.php" 
           class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 md:px-5 md:py-3 rounded-lg font-medium text-sm md:text-base w-full sm:w-auto text-center transition duration-200 whitespace-nowrap">
            <i class="fas fa-plus mr-2"></i> Add Category
        </a>
    </div>

    <!-- Search Bar -->
    <div class="mb-5">
        <?= renderSearchBar([
            'search' => $search,
            'placeholder' => 'Search categories by name or description...',
            'action' => 'index.php'
        ]); ?>
    </div>

    <!-- Success Messages -->
    <?php if (isset($_GET['success'])) : ?>
        <div class="mb-5">
            <?php if ($_GET['success'] == "added") : ?>
                <div class="bg-green-100 border border-green-300 text-green-700 px-4 py-3 rounded-lg">
                    <i class="fas fa-check-circle mr-2"></i> Category Added Successfully.
                </div>
            <?php elseif ($_GET['success'] == "updated") : ?>
                <div class="bg-blue-100 border border-blue-300 text-blue-700 px-4 py-3 rounded-lg">
                    <i class="fas fa-check-circle mr-2"></i> Category Updated Successfully.
                </div>
            <?php elseif ($_GET['success'] == "deleted") : ?>
                <div class="bg-red-100 border border-red-300 text-red-700 px-4 py-3 rounded-lg">
                    <i class="fas fa-trash-alt mr-2"></i> Category Deleted Successfully.
                </div>
            <?php endif; ?>
        </div>
    <?php endif; ?>

    <!-- Table -->
    <div class="bg-white rounded-xl shadow overflow-hidden">
        
        <div class="overflow-x-auto">
            <table class="w-full min-w-[768px]">
                
                <thead class="bg-gray-100">
                    <tr>
                        <th class="px-4 md:px-6 py-3 md:py-4 text-left text-sm font-semibold text-gray-700">#</th>
                        <th class="px-4 md:px-6 py-3 md:py-4 text-left text-sm font-semibold text-gray-700">Image</th>
                        <th class="px-4 md:px-6 py-3 md:py-4 text-left text-sm font-semibold text-gray-700">Category Name</th>
                        <th class="px-4 md:px-6 py-3 md:py-4 text-left text-sm font-semibold text-gray-700 hidden sm:table-cell">Description</th>
                        <th class="px-4 md:px-6 py-3 md:py-4 text-left text-sm font-semibold text-gray-700">Featured</th>
                        <th class="px-4 md:px-6 py-3 md:py-4 text-left text-sm font-semibold text-gray-700 hidden md:table-cell">Status</th>
                        <th class="px-4 md:px-6 py-3 md:py-4 text-center text-sm font-semibold text-gray-700">Action</th>
                    </tr>
                </thead>
                
                <tbody>
                    
                    <?php if (!empty($categories)) : ?>
                        <?php foreach ($categories as $key => $category) : ?>
                            <tr class="border-t hover:bg-gray-50 transition duration-150">
                                <td class="px-4 md:px-6 py-3 md:py-4 text-sm">
                                    <?= $pagination['start_number'] + $key; ?>
                                </td>
                                
                                <td class="px-4 md:px-6 py-3 md:py-4">
                                    <?php if (!empty($category['image'])) : ?>
                                        <img src="../../assets/uploads/categories/<?= htmlspecialchars($category['image']); ?>"
                                             class="w-12 h-12 md:w-16 md:h-16 rounded object-cover"
                                             alt="<?= htmlspecialchars($category['category_name']); ?>">
                                    <?php else : ?>
                                        <img src="../../assets/uploads/categories/no-image.png"
                                             class="w-12 h-12 md:w-16 md:h-16 rounded object-cover"
                                             alt="No Image">
                                    <?php endif; ?>
                                </td>
                                
                                <td class="px-4 md:px-6 py-3 md:py-4 font-medium text-sm">
                                    <?= htmlspecialchars($category['category_name']); ?>
                                </td>
                                
                                <td class="px-4 md:px-6 py-3 md:py-4 text-sm text-gray-600 hidden sm:table-cell">
                                    <?= htmlspecialchars(substr($category['description'] ?? '', 0, 50)) ?>
                                    <?= (strlen($category['description'] ?? '') > 50) ? '...' : '' ?>
                                </td>
                                
                                <td class="px-4 md:px-6 py-3 md:py-4">
                                    <?php if ($category['is_featured'] == 1) : ?>
                                        <span class="bg-green-100 text-green-700 px-2 py-1 md:px-3 md:py-1 rounded-full text-xs md:text-sm whitespace-nowrap">
                                            <i class="fas fa-star mr-1"></i> Yes
                                        </span>
                                    <?php else : ?>
                                        <span class="bg-gray-100 text-gray-700 px-2 py-1 md:px-3 md:py-1 rounded-full text-xs md:text-sm whitespace-nowrap">
                                            <i class="far fa-star mr-1"></i> No
                                        </span>
                                    <?php endif; ?>
                                </td>
                                
                                <td class="px-4 md:px-6 py-3 md:py-4 hidden md:table-cell">
                                    <?php if ($category['status'] == "Active") : ?>
                                        <span class="bg-blue-100 text-blue-700 px-2 py-1 md:px-3 md:py-1 rounded-full text-xs md:text-sm whitespace-nowrap">
                                            <i class="fas fa-circle text-xs mr-1"></i> Active
                                        </span>
                                    <?php else : ?>
                                        <span class="bg-red-100 text-red-700 px-2 py-1 md:px-3 md:py-1 rounded-full text-xs md:text-sm whitespace-nowrap">
                                            <i class="fas fa-circle text-xs mr-1"></i> Inactive
                                        </span>
                                    <?php endif; ?>
                                </td>
                                
                                <td class="px-4 md:px-6 py-3 md:py-4">
                                    <div class="flex flex-col sm:flex-row justify-center gap-2 sm:gap-3">
                                        <a href="edit.php?id=<?= $category['id']; ?>"
                                           class="bg-yellow-500 hover:bg-yellow-600 text-white px-3 py-1.5 md:px-4 md:py-2 rounded text-xs md:text-sm text-center transition duration-200 whitespace-nowrap">
                                            <i class="fas fa-edit mr-1"></i> Edit
                                        </a>
                                        <a href="delete.php?id=<?= $category['id']; ?>"
                                           onclick="return confirm('Are you sure you want to delete this category?');"
                                           class="bg-red-600 hover:bg-red-700 text-white px-3 py-1.5 md:px-4 md:py-2 rounded text-xs md:text-sm text-center transition duration-200 whitespace-nowrap">
                                            <i class="fas fa-trash mr-1"></i> Delete
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        
                    <?php else : ?>
                        <tr>
                            <td colspan="7" class="text-center py-10 text-gray-500">
                                <?php if (!empty($search)): ?>
                                    <i class="fas fa-search text-3xl mb-3 block"></i>
                                    No categories found matching "<strong><?= htmlspecialchars($search) ?></strong>"
                                    <br>
                                    <a href="index.php" class="text-blue-600 hover:underline inline-block mt-2">
                                        <i class="fas fa-times mr-1"></i> Clear search
                                    </a>
                                <?php else: ?>
                                    <i class="fas fa-folder-open text-3xl mb-3 block"></i>
                                    No Categories Found.
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endif; ?>
                    
                </tbody>
                
            </table>
        </div>
        
        <!-- Pagination -->
        <div class="px-4 md:px-6 py-3 md:py-4 border-t">
            <?= renderPagination($pagination, [
                'show_items_per_page' => true,
                'show_first_last' => true,
                'items_per_page_options' => [5, 10, 25, 50]
            ]); ?>
        </div>
        
    </div>
    
</div>

<?php include '../includes/footer.php'; ?>