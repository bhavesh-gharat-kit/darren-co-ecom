<?php
session_start();

require_once '../../config/constant.php';
require_once '../../config/db.php';

if (!isset($_SESSION['admin_id'])) {
    header("Location: " . ADMIN_URL . "auth/login.php");
    exit;
}

$page_title = "Add Product";

$error = "";
$success = "";

// Fetch categories for dropdown
$categories = $database->select("categories", "*", [
    "status" => "Active",
    "ORDER" => ["category_name" => "ASC"]
]);

// Handle form submission
if (isset($_POST['save_product'])) {

    $category_id = (int) $_POST['category_id'];
    $product_name = trim($_POST['product_name']);
    $description = trim($_POST['description']);
    $selling_price = (float) $_POST['selling_price'];
    $discount_price = !empty($_POST['discount_price']) ? (float) $_POST['discount_price'] : 0;
    $stock = (int) $_POST['stock'];
    $status = $_POST['status'];

    // Product Type Checkboxes
    $is_best_selling = isset($_POST['is_best_selling']) ? 1 : 0;
    $is_new_arrival = isset($_POST['is_new_arrival']) ? 1 : 0;
    $is_trending = isset($_POST['is_trending']) ? 1 : 0;
    $is_customer_favorite = isset($_POST['is_customer_favorite']) ? 1 : 0;

    // Main Image Upload
    $main_image = '';
    if (!empty($_FILES['main_image']['name'])) {
        $uploadDir = "../../assets/uploads/products/";

        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        $extension = strtolower(pathinfo($_FILES['main_image']['name'], PATHINFO_EXTENSION));
        $allowed = ['jpg', 'jpeg', 'png', 'webp', 'gif'];

        if (in_array($extension, $allowed)) {
            $main_image = time() . rand(1000, 9999) . "_main." . $extension;
            move_uploaded_file($_FILES['main_image']['tmp_name'], $uploadDir . $main_image);
        } else {
            $error = "Only JPG, JPEG, PNG, WEBP and GIF images are allowed for main image.";
        }
    } else {
        $error = "Main product image is required.";
    }

    // Validation
    if (empty($error)) {
        if (empty($category_id)) {
            $error = "Please select a category.";
        } elseif (empty($product_name)) {
            $error = "Product name is required.";
        } elseif (empty($description)) {
            $error = "Description is required.";
        } elseif (empty($selling_price) || $selling_price <= 0) {
            $error = "Please enter a valid selling price.";
        } elseif ($stock < 0) {
            $error = "Stock cannot be negative.";
        }
    }

    if (empty($error)) {
        try {
            // Insert product - Using 'main_image' as the column name
            $database->insert("products", [
                "category_id" => $category_id,
                "product_name" => $product_name,
                "description" => $description,
                "main_image" => $main_image,  // Fixed: Using 'main_image' instead of 'image'
                "selling_price" => $selling_price,
                "discount_price" => $discount_price,
                "stock" => $stock,
                "is_best_selling" => $is_best_selling,
                "is_new_arrival" => $is_new_arrival,
                "is_trending" => $is_trending,
                "is_customer_favorite" => $is_customer_favorite,
                "status" => $status,
                "created_at" => date("Y-m-d H:i:s")
            ]);

            $product_id = $database->id();

            // Handle Additional Images (product_images table)
            if (!empty($_FILES['product_images']['name'][0])) {
                foreach ($_FILES['product_images']['name'] as $key => $name) {
                    if ($_FILES['product_images']['error'][$key] == 0) {
                        $extension = strtolower(pathinfo($name, PATHINFO_EXTENSION));
                        $allowed = ['jpg', 'jpeg', 'png', 'webp', 'gif'];

                        if (in_array($extension, $allowed)) {
                            $fileName = time() . rand(1000, 9999) . "_gallery_" . $key . "." . $extension;
                            move_uploaded_file(
                                $_FILES['product_images']['tmp_name'][$key],
                                $uploadDir . $fileName
                            );

                            $database->insert("product_images", [
                                "product_id" => $product_id,
                                "image" => $fileName,
                                "created_at" => date("Y-m-d H:i:s")
                            ]);
                        }
                    }
                }
            }

            header("Location: index.php?success=added");
            exit;

        } catch (Exception $e) {
            $error = "Failed to save product: " . $e->getMessage();
        }
    }
}

include '../includes/header.php';
include '../includes/sidebar.php';
?>

<div class="lg:ml-64 min-h-screen bg-gray-100 p-4 md:p-6 lg:p-8">

    <div class="bg-white rounded-xl shadow max-w-4xl mx-auto">

        <!-- Page Header -->
        <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between border-b px-4 md:px-6 py-4 gap-3">
            <h2 class="text-xl md:text-2xl font-bold flex items-center">
                <i class="fas fa-plus-circle text-blue-600 mr-2"></i>
                Add Product
            </h2>
            <a href="index.php"
                class="bg-gray-700 hover:bg-gray-800 text-white px-4 py-2 md:px-5 md:py-2 rounded-lg text-sm md:text-base transition duration-200 w-full sm:w-auto text-center">
                <i class="fas fa-arrow-left mr-2"></i> Back
            </a>
        </div>

        <div class="p-4 md:p-6">

            <!-- Error/Success Messages -->
            <?php if (!empty($error)): ?>
                <div class="mb-5 rounded-lg bg-red-100 border border-red-300 text-red-700 px-4 py-3">
                    <i class="fas fa-exclamation-circle mr-2"></i>
                    <?= htmlspecialchars($error); ?>
                </div>
            <?php endif; ?>

            <form method="POST" enctype="multipart/form-data" id="productForm">

                <!-- Category -->
                <div class="mb-5">
                    <label class="block mb-2 font-medium text-gray-700">
                        <i class="fas fa-folder-open text-blue-600 mr-2"></i>
                        Category <span class="text-red-500">*</span>
                    </label>
                    <select name="category_id"
                        class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none transition duration-200 appearance-none"
                        required>
                        <option value="">Select Category</option>
                        <?php foreach ($categories as $category): ?>
                            <option value="<?= $category['id']; ?>">
                                <?= htmlspecialchars($category['category_name']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <!-- Product Name -->
                <div class="mb-5">
                    <label class="block mb-2 font-medium text-gray-700">
                        <i class="fas fa-tag text-green-600 mr-2"></i>
                        Product Name <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="product_name"
                        value="<?= isset($_POST['product_name']) ? htmlspecialchars($_POST['product_name']) : ''; ?>"
                        class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none transition duration-200"
                        placeholder="Enter product name" required>
                </div>

                <!-- Description -->
                <div class="mb-5">
                    <label class="block mb-2 font-medium text-gray-700">
                        <i class="fas fa-align-left text-purple-600 mr-2"></i>
                        Description <span class="text-red-500">*</span>
                    </label>
                    <textarea name="description" rows="5"
                        class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none transition duration-200 resize-y"
                        placeholder="Enter product description"
                        required><?= isset($_POST['description']) ? htmlspecialchars($_POST['description']) : ''; ?></textarea>
                </div>

                <!-- Main Product Image -->
                <div class="mb-5">
                    <label class="block mb-2 font-medium text-gray-700">
                        <i class="fas fa-image text-indigo-600 mr-2"></i>
                        Main Product Image <span class="text-red-500">*</span>
                    </label>
                    <div class="relative">
                        <input type="file" name="main_image" id="mainImage" accept="image/*"
                            class="w-full border border-gray-300 rounded-lg px-4 py-3 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 cursor-pointer transition duration-200"
                            required>
                        <p class="text-xs text-gray-500 mt-1">
                            <i class="fas fa-info-circle mr-1"></i>
                            Main product image (JPG, JPEG, PNG, WEBP, GIF)
                        </p>
                    </div>

                    <!-- Main Image Preview -->
                    <div id="mainImagePreview" class="mt-3 hidden">
                        <div class="relative inline-block">
                            <img id="mainImagePreviewImg" src="#" alt="Main Image Preview"
                                class="w-32 h-32 object-cover rounded-lg border-2 border-blue-500">
                            <button type="button" onclick="removeMainImage()"
                                class="absolute -top-2 -right-2 bg-red-500 text-white rounded-full w-6 h-6 flex items-center justify-center hover:bg-red-600">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Additional Images -->
                <div class="mb-5">
                    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3 mb-3">
                        <label class="font-medium text-gray-700">
                            <i class="fas fa-images text-pink-600 mr-2"></i>
                            Additional Product Images
                        </label>
                        <button type="button" id="addImageBtn"
                            class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg transition duration-200 w-full sm:w-auto flex items-center justify-center">
                            <i class="fas fa-plus mr-2"></i> Add Image
                        </button>
                    </div>
                    <div id="additionalImagesContainer">
                        <!-- Dynamic image inputs will appear here -->
                    </div>
                    <p class="text-xs text-gray-500 mt-1">
                        <i class="fas fa-info-circle mr-1"></i>
                        Click "Add Image" to upload multiple product images
                    </p>
                </div>

                <!-- Price & Stock Row -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 md:gap-6">

                    <!-- Selling Price -->
                    <div class="mb-5">
                        <label class="block mb-2 font-medium text-gray-700">
                            <i class="fas fa-dollar-sign text-yellow-600 mr-2"></i>
                            Selling Price <span class="text-red-500">*</span>
                        </label>
                        <input type="number" name="selling_price" step="0.01"
                            value="<?= isset($_POST['selling_price']) ? htmlspecialchars($_POST['selling_price']) : ''; ?>"
                            class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none transition duration-200"
                            placeholder="0.00" required>
                    </div>

                    <!-- Discount Price -->
                    <div class="mb-5">
                        <label class="block mb-2 font-medium text-gray-700">
                            <i class="fas fa-percent text-red-600 mr-2"></i>
                            Discount Price
                        </label>
                        <input type="number" name="discount_price" step="0.01"
                            value="<?= isset($_POST['discount_price']) ? htmlspecialchars($_POST['discount_price']) : ''; ?>"
                            class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none transition duration-200"
                            placeholder="0.00">
                    </div>

                </div>

                <!-- Stock -->
                <div class="mb-5">
                    <label class="block mb-2 font-medium text-gray-700">
                        <i class="fas fa-boxes text-orange-600 mr-2"></i>
                        Stock <span class="text-red-500">*</span>
                    </label>
                    <input type="number" name="stock"
                        value="<?= isset($_POST['stock']) ? htmlspecialchars($_POST['stock']) : 0; ?>"
                        class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none transition duration-200"
                        placeholder="Quantity in stock" required>
                </div>

                <!-- Product Type -->
                <div class="mb-5 p-4 bg-gray-50 rounded-lg border border-gray-200">
                    <label class="block mb-3 font-medium text-gray-700">
                        <i class="fas fa-trophy text-yellow-500 mr-2"></i>
                        Product Type
                    </label>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">

                        <label class="flex items-center gap-3 cursor-pointer group p-2 rounded-lg hover:bg-white transition duration-200">
                            <input type="checkbox" name="is_best_selling" value="1"
                                class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500 cursor-pointer">
                            <span class="text-gray-700 group-hover:text-gray-900">
                                <i class="fas fa-fire text-red-500 mr-2"></i>
                                Best Selling
                            </span>
                        </label>

                        <label class="flex items-center gap-3 cursor-pointer group p-2 rounded-lg hover:bg-white transition duration-200">
                            <input type="checkbox" name="is_new_arrival" value="1"
                                class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500 cursor-pointer">
                            <span class="text-gray-700 group-hover:text-gray-900">
                                <i class="fas fa-star text-blue-500 mr-2"></i>
                                New Arrival
                            </span>
                        </label>

                        <label class="flex items-center gap-3 cursor-pointer group p-2 rounded-lg hover:bg-white transition duration-200">
                            <input type="checkbox" name="is_trending" value="1"
                                class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500 cursor-pointer">
                            <span class="text-gray-700 group-hover:text-gray-900">
                                <i class="fas fa-chart-line text-green-500 mr-2"></i>
                                Trending
                            </span>
                        </label>

                        <label class="flex items-center gap-3 cursor-pointer group p-2 rounded-lg hover:bg-white transition duration-200">
                            <input type="checkbox" name="is_customer_favorite" value="1"
                                class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500 cursor-pointer">
                            <span class="text-gray-700 group-hover:text-gray-900">
                                <i class="fas fa-heart text-pink-500 mr-2"></i>
                                Customer Favorite
                            </span>
                        </label>

                    </div>
                </div>

                <!-- Status -->
                <div class="mb-5 p-4 bg-gray-50 rounded-lg border border-gray-200">
                    <label class="block mb-3 font-medium text-gray-700">
                        <i class="fas fa-toggle-on text-green-600 mr-2"></i>
                        Status
                    </label>
                    <div class="flex flex-col sm:flex-row gap-4">
                        <label class="flex items-center gap-3 cursor-pointer">
                            <input type="radio" name="status" value="Active" checked
                                class="w-4 h-4 text-blue-600 border-gray-300 focus:ring-blue-500 cursor-pointer">
                            <span class="text-gray-700">
                                <i class="fas fa-check-circle text-green-500 mr-1"></i>
                                Active
                            </span>
                        </label>
                        <label class="flex items-center gap-3 cursor-pointer">
                            <input type="radio" name="status" value="Inactive"
                                class="w-4 h-4 text-blue-600 border-gray-300 focus:ring-blue-500 cursor-pointer">
                            <span class="text-gray-700">
                                <i class="fas fa-times-circle text-red-500 mr-1"></i>
                                Inactive
                            </span>
                        </label>
                    </div>
                </div>

                <!-- Form Actions -->
                <div class="flex flex-col sm:flex-row gap-3 pt-4 border-t">

                    <button type="submit" name="save_product"
                        class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 md:px-8 md:py-3 rounded-lg font-medium transition duration-200 flex items-center justify-center w-full sm:w-auto">
                        <i class="fas fa-save mr-2"></i>
                        Save Product
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

<!-- JavaScript for Image Preview -->
<script>
    // Main Image Preview
    document.getElementById('mainImage').addEventListener('change', function (e) {
        const file = this.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function (e) {
                const preview = document.getElementById('mainImagePreview');
                const img = document.getElementById('mainImagePreviewImg');
                img.src = e.target.result;
                preview.classList.remove('hidden');
            };
            reader.readAsDataURL(file);
        }
    });

    function removeMainImage() {
        document.getElementById('mainImage').value = '';
        document.getElementById('mainImagePreview').classList.add('hidden');
        document.getElementById('mainImagePreviewImg').src = '#';
    }

    let imageCount = 0;

    document.getElementById("addImageBtn").addEventListener("click", function () {
        imageCount++;

        let html = `
        <div class="flex flex-col sm:flex-row items-center gap-3 mb-3 image-row p-3 bg-gray-50 rounded-lg border border-gray-200">
            <div class="flex-1 w-full">
                <input
                    type="file"
                    name="product_images[]"
                    accept="image/*"
                    class="w-full border border-gray-300 rounded-lg px-4 py-2 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 cursor-pointer transition duration-200">
            </div>
            <button
                type="button"
                class="removeBtn bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg transition duration-200 w-full sm:w-auto flex items-center justify-center">
                <i class="fas fa-times mr-2"></i> Remove
            </button>
        </div>
        `;

        document
            .getElementById("additionalImagesContainer")
            .insertAdjacentHTML("beforeend", html);
    });

    document.addEventListener("click", function (e) {
        if (e.target.classList.contains("removeBtn") || e.target.closest(".removeBtn")) {
            const button = e.target.closest(".removeBtn");
            if (button) {
                button.closest(".image-row").remove();
            }
        }
    });
</script>

<?php include '../includes/footer.php'; ?>