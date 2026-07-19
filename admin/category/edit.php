<?php

session_start();

require_once '../../config/constant.php';
require_once '../../config/db.php';

if (!isset($_SESSION['admin_id'])) {
    header("Location: " . ADMIN_URL . "auth/login.php");
    exit;
}

$page_title = "Edit Category";

$error = "";

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: index.php");
    exit;
}

$id = (int)$_GET['id'];

$category = $database->get("categories", "*", [
    "id" => $id
]);

if (!$category) {
    header("Location: index.php");
    exit;
}

if (isset($_POST['update_category'])) {

    $category_name = trim($_POST['category_name']);
    $description   = trim($_POST['description']);
    $status        = $_POST['status'];
    $is_featured   = isset($_POST['is_featured']) ? 1 : 0;

    if (empty($category_name)) {

        $error = "Category Name is required.";

    } else {

        // Duplicate Check
        $exists = $database->has("categories", [
            "AND" => [
                "category_name" => $category_name,
                "id[!]" => $id
            ]
        ]);

        if ($exists) {

            $error = "Category already exists.";

        } else {

            $image = $category['image'];

            // Upload New Image
            if (!empty($_FILES['image']['name'])) {

                $extension = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));

                $allowed = ['jpg', 'jpeg', 'png', 'webp'];

                if (in_array($extension, $allowed)) {

                    // Delete Old Image
                    if (!empty($category['image']) && file_exists("../../assets/uploads/categories/" . $category['image'])) {

                        unlink("../../assets/uploads/categories/" . $category['image']);

                    }

                    $image = time() . rand(1000,9999) . "." . $extension;

                    $uploadDir = "../../assets/uploads/categories/";

                    if (!is_dir($uploadDir)) {
                        mkdir($uploadDir, 0777, true);
                    }

                    move_uploaded_file(
                        $_FILES['image']['tmp_name'],
                        $uploadDir . $image
                    );

                } else {

                    $error = "Only JPG, JPEG, PNG and WEBP images are allowed.";

                }

            }

            if (empty($error)) {

                $database->update("categories", [

                    "category_name" => $category_name,
                    "image"         => $image,
                    "description"   => $description,
                    "is_featured"   => $is_featured,
                    "status"        => $status

                ], [
                    "id" => $id
                ]);

                header("Location: index.php?success=updated");
                exit;

            }

        }

    }

}

include '../includes/header.php';
include '../includes/sidebar.php';

?>

<div class="lg:ml-64 min-h-screen bg-gray-100 p-4 md:p-6 lg:p-8">

    <div class="bg-white rounded-xl shadow max-w-4xl mx-auto">

        <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between border-b px-4 md:px-6 py-4 gap-3">

            <h2 class="text-xl md:text-2xl font-bold flex items-center">
                <i class="fas fa-edit text-blue-600 mr-2"></i>
                Edit Category
            </h2>

            <a href="index.php"
                class="bg-gray-700 hover:bg-gray-800 text-white px-4 py-2 md:px-5 md:py-2 rounded-lg text-sm md:text-base transition duration-200 w-full sm:w-auto text-center">
                <i class="fas fa-arrow-left mr-2"></i> Back
            </a>

        </div>

        <div class="p-4 md:p-6">

            <?php if (!empty($error)) : ?>

                <div class="mb-5 rounded-lg bg-red-100 border border-red-300 text-red-700 px-4 py-3">
                    <i class="fas fa-exclamation-circle mr-2"></i>
                    <?= $error; ?>
                </div>

            <?php endif; ?>

            <form method="POST" enctype="multipart/form-data">

                <!-- Category Name -->
                <div class="mb-5">

                    <label class="block mb-2 font-medium text-gray-700">
                        <i class="fas fa-tag text-blue-600 mr-2"></i>
                        Category Name <span class="text-red-500">*</span>
                    </label>

                    <input
                        type="text"
                        name="category_name"
                        value="<?= htmlspecialchars($category['category_name']); ?>"
                        class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none transition duration-200"
                        placeholder="Enter category name"
                        required>

                </div>

                <!-- Current Image -->
                <div class="mb-5">

                    <label class="block mb-2 font-medium text-gray-700">
                        <i class="fas fa-image text-green-600 mr-2"></i>
                        Current Image
                    </label>

                    <?php if (!empty($category['image'])) : ?>

                        <div class="relative inline-block">
                            <img
                                src="<?= BASE_URL ?>assets/uploads/categories/<?= htmlspecialchars($category['image']); ?>"
                                class="w-24 h-24 md:w-28 md:h-28 rounded-lg border-2 border-gray-200 object-cover"
                                alt="<?= htmlspecialchars($category['category_name']); ?>">
                        </div>

                    <?php else : ?>

                        <p class="text-gray-500">
                            <i class="fas fa-image text-gray-400 mr-2"></i>
                            No Image
                        </p>

                    <?php endif; ?>

                </div>

                <!-- New Image -->
                <div class="mb-5">

                    <label class="block mb-2 font-medium text-gray-700">
                        <i class="fas fa-upload text-purple-600 mr-2"></i>
                        Change Image
                    </label>

                    <div class="relative">
                        <input
                            type="file"
                            name="image"
                            id="imageInput"
                            class="w-full border border-gray-300 rounded-lg px-4 py-3 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 cursor-pointer transition duration-200"
                            accept="image/*">
                        <p class="text-xs text-gray-500 mt-1">
                            <i class="fas fa-info-circle mr-1"></i>
                            Allowed: JPG, JPEG, PNG, WEBP (Max size: 2MB)
                        </p>
                    </div>

                </div>

                <!-- Description -->
                <div class="mb-5">

                    <label class="block mb-2 font-medium text-gray-700">
                        <i class="fas fa-align-left text-indigo-600 mr-2"></i>
                        Description
                    </label>

                    <textarea
                        name="description"
                        rows="5"
                        class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none transition duration-200 resize-y"
                        placeholder="Enter category description"><?= htmlspecialchars($category['description']); ?></textarea>

                </div>

                <!-- Featured -->
                <div class="mb-5">

                    <label class="flex items-center gap-3 cursor-pointer group">

                        <input
                            type="checkbox"
                            name="is_featured"
                            value="1"
                            <?= $category['is_featured'] == 1 ? 'checked' : ''; ?>
                            class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500 cursor-pointer">

                        <span class="font-medium text-gray-700 group-hover:text-gray-900 transition duration-200">
                            <i class="fas fa-star text-yellow-500 mr-2"></i>
                            Featured Collection
                        </span>

                    </label>

                </div>

                <!-- Status -->
                <div class="mb-6">

                    <label class="block mb-2 font-medium text-gray-700">
                        <i class="fas fa-toggle-on text-green-600 mr-2"></i>
                        Status
                    </label>

                    <select
                        name="status"
                        class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none transition duration-200 appearance-none">

                        <option value="Active"
                            <?= $category['status'] == 'Active' ? 'selected' : ''; ?>>
                            <i class="fas fa-check-circle text-green-600"></i> Active
                        </option>

                        <option value="Inactive"
                            <?= $category['status'] == 'Inactive' ? 'selected' : ''; ?>>
                            <i class="fas fa-times-circle text-red-600"></i> Inactive
                        </option>

                    </select>

                </div>

                <!-- Form Actions -->
                <div class="flex flex-col sm:flex-row gap-3 pt-4 border-t">
                    
                    <button
                        type="submit"
                        name="update_category"
                        class="bg-green-600 hover:bg-green-700 text-white px-6 py-3 md:px-8 md:py-3 rounded-lg font-medium transition duration-200 flex items-center justify-center w-full sm:w-auto">
                        <i class="fas fa-save mr-2"></i>
                        Update Category
                    </button>

                    <a href="index.php"
                        class="bg-gray-500 hover:bg-gray-600 text-white px-6 py-3 md:px-8 md:py-3 rounded-lg font-medium transition duration-200 flex items-center justify-center w-full sm:w-auto text-center">
                        <i class="fas fa-times mr-2"></i>
                        Cancel
                    </a>

                </div>

            </form>

        </div>

    </div>

</div>

<?php include '../includes/footer.php'; ?>s