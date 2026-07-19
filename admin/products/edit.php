<?php
session_start();

require_once '../../config/constant.php';
require_once '../../config/db.php';

if (!isset($_SESSION['admin_id'])) {
    header("Location: " . ADMIN_URL . "auth/login.php");
    exit;
}

$page_title = "Edit Product";

$error = "";
$success = "";

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: index.php");
    exit;
}

$id = (int)$_GET['id'];

/*----------------------------------
Fetch Product
-----------------------------------*/
$product = $database->get("products", "*", [
    "id" => $id
]);

if (!$product) {
    header("Location: index.php");
    exit;
}

/*----------------------------------
Fetch Categories
-----------------------------------*/
$categories = $database->select("categories", "*", [
    "status" => "Active",
    "ORDER" => [
        "category_name" => "ASC"
    ]
]);

/*----------------------------------
Fetch Gallery Images
-----------------------------------*/
$galleryImages = $database->select("product_images", "*", [
    "product_id" => $id,
    "ORDER" => [
        "id" => "ASC"
    ]
]);

/*----------------------------------
Update Product
-----------------------------------*/
if (isset($_POST['update_product'])) {

    $category_id = (int)$_POST['category_id'];
    $product_name = trim($_POST['product_name']);
    $description = trim($_POST['description']);
    $selling_price = $_POST['selling_price'];
    $discount_price = $_POST['discount_price'];
    $stock = $_POST['stock'];
    $status = $_POST['status'];
    $is_best_selling = isset($_POST['is_best_selling']) ? 1 : 0;
    $is_new_arrival = isset($_POST['is_new_arrival']) ? 1 : 0;
    $is_trending = isset($_POST['is_trending']) ? 1 : 0;
    $is_customer_favorite = isset($_POST['is_customer_favorite']) ? 1 : 0;

    if (empty($category_id)) {
        $error = "Please Select Category.";
    } elseif (empty($product_name)) {
        $error = "Please Enter Product Name.";
    } elseif (empty($selling_price)) {
        $error = "Please Enter Selling Price.";
    } else {

        // Fix: Use 'main_image' instead of 'image'
        $main_image = $product['main_image']; // Changed from $product['image']
        $uploadDir = "../../assets/uploads/products/";

        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        /*---------------------------
        Main Image Upload
        ---------------------------*/
        // Fix: Use 'main_image' as file input name
        if (!empty($_FILES['main_image']['name'])) { // Changed from 'image' to 'main_image'
            $extension = strtolower(pathinfo($_FILES['main_image']['name'], PATHINFO_EXTENSION));
            $allowed = ['jpg', 'jpeg', 'png', 'webp', 'gif'];

            if (in_array($extension, $allowed)) {
                // Fix: Use 'main_image' for checking existing image
                if (!empty($product['main_image']) && file_exists($uploadDir . $product['main_image'])) {
                    unlink($uploadDir . $product['main_image']);
                }

                $main_image = time() . rand(1000, 9999) . "_main." . $extension;
                move_uploaded_file(
                    $_FILES['main_image']['tmp_name'], // Changed from 'image' to 'main_image'
                    $uploadDir . $main_image
                );
            } else {
                $error = "Only JPG, JPEG, PNG, WEBP and GIF images are allowed.";
            }
        }

        /*---------------------------
        Delete Gallery Images
        ---------------------------*/
        if (isset($_POST['delete_images']) && is_array($_POST['delete_images'])) {
            foreach ($_POST['delete_images'] as $image_id) {
                $image_id = (int)$image_id;
                $gallery_image = $database->get("product_images", "*", [
                    "id" => $image_id,
                    "product_id" => $id
                ]);

                if ($gallery_image) {
                    if (file_exists($uploadDir . $gallery_image['image'])) {
                        unlink($uploadDir . $gallery_image['image']);
                    }
                    $database->delete("product_images", [
                        "id" => $image_id
                    ]);
                }
            }
        }

        if (empty($error)) {
            /*---------------------------
            Update Product
            ---------------------------*/
            // Fix: Use 'main_image' instead of 'image'
            $database->update("products", [
                "category_id" => $category_id,
                "product_name" => $product_name,
                "description" => $description,
                "main_image" => $main_image, // Changed from 'image' to 'main_image'
                "selling_price" => $selling_price,
                "discount_price" => $discount_price,
                "stock" => $stock,
                "is_best_selling" => $is_best_selling,
                "is_new_arrival" => $is_new_arrival,
                "is_trending" => $is_trending,
                "is_customer_favorite" => $is_customer_favorite,
                "status" => $status,
                "updated_at" => date("Y-m-d H:i:s")
            ], [
                "id" => $id
            ]);

            /*---------------------------
            Upload New Gallery Images
            ---------------------------*/
            if (!empty($_FILES['gallery_images']['name'][0])) {
                foreach ($_FILES['gallery_images']['name'] as $key => $name) {
                    if ($_FILES['gallery_images']['error'][$key] == 0) {
                        $extension = strtolower(pathinfo($name, PATHINFO_EXTENSION));
                        $allowed = ['jpg', 'jpeg', 'png', 'webp', 'gif'];

                        if (in_array($extension, $allowed)) {
                            $fileName = time() . rand(1000, 9999) . "_gallery_" . $key . "." . $extension;
                            move_uploaded_file(
                                $_FILES['gallery_images']['tmp_name'][$key],
                                $uploadDir . $fileName
                            );

                            $database->insert("product_images", [
                                "product_id" => $id,
                                "image" => $fileName,
                                "created_at" => date("Y-m-d H:i:s")
                            ]);
                        }
                    }
                }
            }

            header("Location: index.php?success=updated");
            exit;
        }
    }
}

include '../includes/header.php';
include '../includes/sidebar.php';
?>

<div class="lg:ml-64 min-h-screen bg-gray-100 p-4 md:p-6 lg:p-8">
    
    <div class="bg-white rounded-xl shadow max-w-4xl mx-auto">
        
        <!-- Header -->
        <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between border-b px-4 md:px-6 py-4 gap-3">
            <div>
                <h2 class="text-xl md:text-2xl font-bold text-gray-800 flex items-center">
                    <i class="fas fa-edit text-blue-600 mr-2"></i>
                    Edit Product
                </h2>
                <p class="text-sm text-gray-500 mt-1">
                    <i class="fas fa-info-circle mr-1"></i>
                    Update Product Details
                </p>
            </div>
            <a href="index.php"
                class="bg-gray-700 hover:bg-gray-800 text-white px-4 py-2 md:px-5 md:py-2 rounded-lg text-sm md:text-base transition duration-200 w-full sm:w-auto text-center">
                <i class="fas fa-arrow-left mr-2"></i> Back
            </a>
        </div>

        <div class="p-4 md:p-6">
            
            <?php if (!empty($error)) : ?>
                <div class="mb-5 bg-red-100 border border-red-300 text-red-700 px-4 py-3 rounded-lg">
                    <i class="fas fa-exclamation-circle mr-2"></i>
                    <?= htmlspecialchars($error); ?>
                </div>
            <?php endif; ?>

            <?php if (!empty($success)) : ?>
                <div class="mb-5 bg-green-100 border border-green-300 text-green-700 px-4 py-3 rounded-lg">
                    <i class="fas fa-check-circle mr-2"></i>
                    <?= htmlspecialchars($success); ?>
                </div>
            <?php endif; ?>

            <form method="POST" enctype="multipart/form-data" id="editProductForm">

                <!-- Category -->
                <div class="mb-5">
                    <label class="block mb-2 font-medium text-gray-700">
                        <i class="fas fa-folder-open text-blue-600 mr-2"></i>
                        Category <span class="text-red-500">*</span>
                    </label>
                    <select
                        name="category_id"
                        class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none transition duration-200 appearance-none"
                        required>
                        <option value="">Select Category</option>
                        <?php foreach ($categories as $cat) : ?>
                            <option
                                value="<?= $cat['id']; ?>"
                                <?= ($product['category_id'] == $cat['id']) ? 'selected' : ''; ?>>
                                <?= htmlspecialchars($cat['category_name']); ?>
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
                    <input
                        type="text"
                        name="product_name"
                        value="<?= htmlspecialchars($product['product_name']); ?>"
                        class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none transition duration-200"
                        placeholder="Enter product name"
                        required>
                </div>

                <!-- Main Product Image -->
                <div class="mb-5">
                    <label class="block mb-2 font-medium text-gray-700">
                        <i class="fas fa-image text-indigo-600 mr-2"></i>
                        Main Product Image
                    </label>
                    <?php if (!empty($product['main_image'])) : ?>
                        <div class="mb-3">
                            <div class="relative inline-block">
                                <img
                                    src="<?= BASE_URL ?>assets/uploads/products/<?= htmlspecialchars($product['main_image']); ?>"
                                    class="w-32 h-32 rounded-lg border-2 border-gray-200 object-cover"
                                    alt="<?= htmlspecialchars($product['product_name']); ?>">
                                <span class="absolute -top-2 -right-2 bg-blue-500 text-white text-xs px-2 py-1 rounded-full">
                                    <i class="fas fa-check mr-1"></i> Main
                                </span>
                            </div>
                        </div>
                    <?php else : ?>
                        <p class="text-gray-500 mb-3">
                            <i class="fas fa-image text-gray-400 mr-1"></i>
                            No main image uploaded
                        </p>
                    <?php endif; ?>
                    
                    <input
                        type="file"
                        name="main_image"
                        id="mainImage"
                        accept="image/*"
                        class="w-full border border-gray-300 rounded-lg px-4 py-3 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 cursor-pointer transition duration-200">
                    <p class="text-xs text-gray-500 mt-1">
                        <i class="fas fa-info-circle mr-1"></i>
                        Leave empty to keep current image
                    </p>
                </div>

                <!-- Existing Gallery Images -->
                <?php if (!empty($galleryImages)) : ?>
                    <div class="mb-6">
                        <label class="block mb-3 font-medium text-gray-700">
                            <i class="fas fa-images text-pink-600 mr-2"></i>
                            Product Gallery
                            <span class="text-sm font-normal text-gray-500">(<?= count($galleryImages); ?> images)</span>
                        </label>
                        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-3">
                            <?php $counter = 1; ?>
                            <?php foreach ($galleryImages as $gallery) : ?>
                                <div class="border rounded-lg p-2 bg-gray-50 group relative">
                                    <img
                                        src="<?= BASE_URL ?>assets/uploads/products/<?= htmlspecialchars($gallery['image']); ?>"
                                        class="w-full h-24 object-cover rounded"
                                        alt="Gallery Image <?= $counter; ?>">
                                    <label class="absolute top-1 right-1 bg-red-500 hover:bg-red-600 text-white p-1 rounded cursor-pointer opacity-0 group-hover:opacity-100 transition duration-200">
                                        <input
                                            type="checkbox"
                                            name="delete_images[]"
                                            value="<?= $gallery['id']; ?>"
                                            class="hidden"
                                            onclick="this.closest('label').style.opacity='1'">
                                        <i class="fas fa-times text-xs"></i>
                                    </label>
                                    <p class="text-xs text-center mt-1 text-gray-500 truncate">
                                        <i class="far fa-image mr-1"></i>
                                        Image <?= $counter++; ?>
                                    </p>
                                </div>
                            <?php endforeach; ?>
                        </div>
                        <p class="text-xs text-red-500 mt-2">
                            <i class="fas fa-info-circle mr-1"></i>
                            Hover over an image and click the <i class="fas fa-times text-xs text-red-500"></i> icon to delete it
                        </p>
                    </div>
                <?php else : ?>
                    <div class="mb-6 p-4 bg-gray-50 rounded-lg border border-gray-200 text-center">
                        <i class="fas fa-images text-3xl text-gray-300 mb-2 block"></i>
                        <p class="text-gray-500">No gallery images uploaded</p>
                    </div>
                <?php endif; ?>

                <!-- Add More Images -->
                <div class="mb-6">
                    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3 mb-3">
                        <label class="font-medium text-gray-700">
                            <i class="fas fa-plus-circle text-blue-600 mr-2"></i>
                            Add More Images
                        </label>
                        <button
                            type="button"
                            id="addImageBtn"
                            class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg transition duration-200 w-full sm:w-auto flex items-center justify-center">
                            <i class="fas fa-plus mr-2"></i> Add Image
                        </button>
                    </div>
                    <div id="additionalImagesContainer">
                        <!-- Dynamic image inputs will appear here -->
                    </div>
                    <p class="text-xs text-gray-500 mt-2">
                        <i class="fas fa-info-circle mr-1"></i>
                        Click "Add Image" to add multiple gallery images
                    </p>
                </div>

                <!-- Description -->
                <div class="mb-5">
                    <label class="block mb-2 font-medium text-gray-700">
                        <i class="fas fa-align-left text-purple-600 mr-2"></i>
                        Description
                    </label>
                    <textarea
                        name="description"
                        rows="5"
                        class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none transition duration-200 resize-y"
                        placeholder="Enter product description"><?= htmlspecialchars($product['description']); ?></textarea>
                </div>

                <!-- Price & Stock Row -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 md:gap-5 mb-5">
                    <div>
                        <label class="block mb-2 font-medium text-gray-700">
                            <i class="fas fa-dollar-sign text-yellow-600 mr-2"></i>
                            Selling Price <span class="text-red-500">*</span>
                        </label>
                        <input
                            type="number"
                            step="0.01"
                            name="selling_price"
                            value="<?= htmlspecialchars($product['selling_price']); ?>"
                            class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none transition duration-200"
                            placeholder="0.00"
                            required>
                    </div>

                    <div>
                        <label class="block mb-2 font-medium text-gray-700">
                            <i class="fas fa-percent text-red-600 mr-2"></i>
                            Discount Price
                        </label>
                        <input
                            type="number"
                            step="0.01"
                            name="discount_price"
                            value="<?= htmlspecialchars($product['discount_price']); ?>"
                            class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none transition duration-200"
                            placeholder="0.00">
                    </div>

                    <div>
                        <label class="block mb-2 font-medium text-gray-700">
                            <i class="fas fa-boxes text-orange-600 mr-2"></i>
                            Stock <span class="text-red-500">*</span>
                        </label>
                        <input
                            type="number"
                            name="stock"
                            value="<?= htmlspecialchars($product['stock']); ?>"
                            class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none transition duration-200"
                            placeholder="Quantity"
                            required>
                    </div>
                </div>

                <!-- Product Type -->
                <div class="mb-6 p-4 bg-gray-50 rounded-lg border border-gray-200">
                    <label class="block mb-3 font-medium text-gray-700">
                        <i class="fas fa-trophy text-yellow-500 mr-2"></i>
                        Product Type
                    </label>
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">
                        <label class="flex items-center gap-3 cursor-pointer group p-2 rounded-lg hover:bg-white transition duration-200">
                            <input
                                type="checkbox"
                                name="is_best_selling"
                                <?= $product['is_best_selling'] ? 'checked' : ''; ?>
                                class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500 cursor-pointer">
                            <span class="text-gray-700 group-hover:text-gray-900">
                                <i class="fas fa-fire text-red-500 mr-2"></i>
                                Best Selling
                            </span>
                        </label>

                        <label class="flex items-center gap-3 cursor-pointer group p-2 rounded-lg hover:bg-white transition duration-200">
                            <input
                                type="checkbox"
                                name="is_new_arrival"
                                <?= $product['is_new_arrival'] ? 'checked' : ''; ?>
                                class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500 cursor-pointer">
                            <span class="text-gray-700 group-hover:text-gray-900">
                                <i class="fas fa-star text-blue-500 mr-2"></i>
                                New Arrival
                            </span>
                        </label>

                        <label class="flex items-center gap-3 cursor-pointer group p-2 rounded-lg hover:bg-white transition duration-200">
                            <input
                                type="checkbox"
                                name="is_trending"
                                <?= $product['is_trending'] ? 'checked' : ''; ?>
                                class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500 cursor-pointer">
                            <span class="text-gray-700 group-hover:text-gray-900">
                                <i class="fas fa-chart-line text-green-500 mr-2"></i>
                                Trending
                            </span>
                        </label>

                        <label class="flex items-center gap-3 cursor-pointer group p-2 rounded-lg hover:bg-white transition duration-200">
                            <input
                                type="checkbox"
                                name="is_customer_favorite"
                                <?= $product['is_customer_favorite'] ? 'checked' : ''; ?>
                                class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500 cursor-pointer">
                            <span class="text-gray-700 group-hover:text-gray-900">
                                <i class="fas fa-heart text-pink-500 mr-2"></i>
                                Customer Favorite
                            </span>
                        </label>
                    </div>
                </div>

                <!-- Status -->
                <div class="mb-6 p-4 bg-gray-50 rounded-lg border border-gray-200">
                    <label class="block mb-3 font-medium text-gray-700">
                        <i class="fas fa-toggle-on text-green-600 mr-2"></i>
                        Status
                    </label>
                    <div class="flex flex-col sm:flex-row gap-4">
                        <label class="flex items-center gap-3 cursor-pointer">
                            <input
                                type="radio"
                                name="status"
                                value="Active"
                                <?= $product['status'] == 'Active' ? 'checked' : ''; ?>
                                class="w-4 h-4 text-blue-600 border-gray-300 focus:ring-blue-500 cursor-pointer">
                            <span class="text-gray-700">
                                <i class="fas fa-check-circle text-green-500 mr-1"></i>
                                Active
                            </span>
                        </label>
                        <label class="flex items-center gap-3 cursor-pointer">
                            <input
                                type="radio"
                                name="status"
                                value="Inactive"
                                <?= $product['status'] == 'Inactive' ? 'checked' : ''; ?>
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
                    <button
                        type="submit"
                        name="update_product"
                        class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 md:px-8 md:py-3 rounded-lg font-medium transition duration-200 flex items-center justify-center w-full sm:w-auto">
                        <i class="fas fa-save mr-2"></i>
                        Update Product
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

<script>
let imageCount = 0;

document.getElementById("addImageBtn").addEventListener("click", function() {
    imageCount++;
    
    let html = `
        <div class="flex flex-col sm:flex-row items-center gap-3 mb-3 imageRow p-3 bg-gray-50 rounded-lg border border-gray-200">
            <div class="flex-1 w-full">
                <input
                    type="file"
                    name="gallery_images[]"
                    accept="image/*"
                    class="w-full border border-gray-300 rounded-lg px-4 py-2 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 cursor-pointer transition duration-200">
            </div>
            <button
                type="button"
                class="removeImage bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg transition duration-200 w-full sm:w-auto flex items-center justify-center">
                <i class="fas fa-times mr-2"></i> Remove
            </button>
        </div>
    `;
    
    document
        .getElementById("additionalImagesContainer")
        .insertAdjacentHTML("beforeend", html);
});

document.addEventListener("click", function(e) {
    if (e.target.classList.contains("removeImage") || e.target.closest(".removeImage")) {
        const button = e.target.closest(".removeImage");
        if (button) {
            button.closest(".imageRow").remove();
        }
    }
});

// Preview main image when selected
document.getElementById('mainImage').addEventListener('change', function(e) {
    const file = this.files[0];
    if (file) {
        const reader = new FileReader();
        reader.onload = function(e) {
            const existingImg = document.querySelector('img[alt*="product"]');
            if (existingImg) {
                existingImg.src = e.target.result;
            } else {
                // If no existing image, show preview
                const previewDiv = document.createElement('div');
                previewDiv.className = 'mb-3';
                previewDiv.innerHTML = `
                    <div class="relative inline-block">
                        <img src="${e.target.result}" class="w-32 h-32 rounded-lg border-2 border-blue-500 object-cover">
                        <span class="absolute -top-2 -right-2 bg-green-500 text-white text-xs px-2 py-1 rounded-full">
                            <i class="fas fa-plus mr-1"></i> New
                        </span>
                    </div>
                `;
                document.querySelector('input[name="main_image"]').parentNode.insertBefore(
                    previewDiv,
                    document.querySelector('input[name="main_image"]')
                );
            }
        };
        reader.readAsDataURL(file);
    }
});

// Confirm before deleting gallery images
document.querySelectorAll('input[name="delete_images[]"]').forEach(function(checkbox) {
    checkbox.addEventListener('change', function() {
        if (this.checked) {
            if (!confirm('Are you sure you want to delete this image?')) {
                this.checked = false;
            }
        }
    });
});
</script>

<?php include '../includes/footer.php'; ?>