<?php
session_start();
require_once '../../config/constant.php';
require_once '../../config/db.php';

if (!isset($_SESSION['admin_id'])) {
    header("Location: " . ADMIN_URL . "auth/login.php");
    exit;
}

$page_title = "Manage Banners";

// Fetch all banners
$banners = $database->select("banners", "*", [
    "ORDER" => ["id" => "DESC"]
]);

include '../includes/header.php';
include '../includes/sidebar.php';
?>

<div class="lg:ml-64 min-h-screen bg-gray-100 p-4 md:p-6 lg:p-8">

    <div class="bg-white rounded-xl shadow">

        <!-- Header -->
        <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between border-b px-4 md:px-6 py-4 gap-3">
            <h2 class="text-xl md:text-2xl font-bold flex items-center">
                <i class="fas fa-images text-blue-600 mr-2"></i>
                Manage Banners
            </h2>
            <a href="add.php"
                class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 md:px-5 md:py-2 rounded-lg text-sm md:text-base transition duration-200 w-full sm:w-auto text-center">
                <i class="fas fa-plus mr-2"></i> Add New Banner
            </a>
        </div>

        <div class="p-4 md:p-6">

            <!-- Success Message -->
            <?php if (isset($_GET['success'])): ?>
                <div class="mb-5 rounded-lg bg-green-100 border border-green-300 text-green-700 px-4 py-3">
                    <i class="fas fa-check-circle mr-2"></i>
                    <?php
                    if ($_GET['success'] == 'added') echo "Banner added successfully!";
                    elseif ($_GET['success'] == 'updated') echo "Banner updated successfully!";
                    elseif ($_GET['success'] == 'deleted') echo "Banner deleted successfully!";
                    ?>
                </div>
            <?php endif; ?>

            <?php if (empty($banners)): ?>
                <div class="text-center py-12">
                    <i class="fas fa-images text-6xl text-gray-300 mb-4"></i>
                    <h3 class="text-xl font-semibold text-gray-600">No Banners Found</h3>
                    <p class="text-gray-500 mt-2">Click "Add New Banner" to create your first banner.</p>
                </div>
            <?php else: ?>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="border-b bg-gray-50">
                                <th class="px-4 py-3 text-left font-semibold text-gray-600">#</th>
                                <th class="px-4 py-3 text-left font-semibold text-gray-600">Image</th>
                                <th class="px-4 py-3 text-left font-semibold text-gray-600">Title</th>
                                <th class="px-4 py-3 text-left font-semibold text-gray-600">Subtitle</th>
                                <th class="px-4 py-3 text-left font-semibold text-gray-600">Button Text</th>
                                <th class="px-4 py-3 text-left font-semibold text-gray-600">Status</th>
                                <th class="px-4 py-3 text-left font-semibold text-gray-600">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($banners as $index => $banner): ?>
                                <tr class="border-b hover:bg-gray-50 transition">
                                    <td class="px-4 py-3"><?= $index + 1; ?></td>
                                    <td class="px-4 py-3">
                                        <img src="<?= BASE_URL ?>assets/uploads/banners/<?= htmlspecialchars($banner['image']); ?>"
                                            alt="<?= htmlspecialchars($banner['title']); ?>"
                                            class="w-16 h-16 object-cover rounded-lg">
                                    </td>
                                    <td class="px-4 py-3 font-medium"><?= htmlspecialchars($banner['title']); ?></td>
                                    <td class="px-4 py-3"><?= htmlspecialchars($banner['subtitle']); ?></td>
                                    <td class="px-4 py-3"><?= htmlspecialchars($banner['button_text']); ?></td>
                                    <td class="px-4 py-3">
                                        <span class="px-2 py-1 rounded-full text-xs font-semibold
                                            <?= $banner['status'] == 'Active' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700'; ?>">
                                            <?= $banner['status']; ?>
                                        </span>
                                    </td>
                                    <td class="px-4 py-3">
                                        <div class="flex gap-2">
                                            <a href="edit.php?id=<?= $banner['id']; ?>"
                                                class="bg-blue-500 hover:bg-blue-600 text-white px-3 py-1 rounded-lg text-xs transition">
                                                <i class="fas fa-edit"></i> Edit
                                            </a>
                                            <a href="delete.php?id=<?= $banner['id']; ?>"
                                                onclick="return confirm('Are you sure you want to delete this banner?')"
                                                class="bg-red-500 hover:bg-red-600 text-white px-3 py-1 rounded-lg text-xs transition">
                                                <i class="fas fa-trash"></i> Delete
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>

        </div>

    </div>

</div>

<?php include '../includes/footer.php'; ?>