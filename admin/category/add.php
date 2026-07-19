<?php
session_start();

require_once '../../config/constant.php';
require_once '../../config/db.php';

if (!isset($_SESSION['admin_id'])) {
    header("Location: " . ADMIN_URL . "auth/login.php");
    exit;
}

$page_title = "Add Category";

$error = "";
$success = "";

if (isset($_POST['save_category'])) {
    
    $category_name = trim($_POST['category_name']);
    $description = trim($_POST['description']);
    $status = $_POST['status'];
    $is_featured = isset($_POST['is_featured']) ? 1 : 0;
    
    // Validation
    if (empty($category_name)) {
        $error = "Category name is required.";
    } else {
        
        // Check if category already exists
        $exists = $database->has("categories", [
            "category_name" => $category_name
        ]);
        
        if ($exists) {
            $error = "Category already exists. Please use a different name.";
        } else {
            
            // Handle Image Upload
            $image = '';
            $uploadDir = "../../assets/uploads/categories/";
            
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }
            
            if (!empty($_FILES['image']['name'])) {
                $extension = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
                $allowed = ['jpg', 'jpeg', 'png', 'webp', 'gif'];
                
                if (in_array($extension, $allowed)) {
                    $image = time() . rand(1000, 9999) . "." . $extension;
                    move_uploaded_file(
                        $_FILES['image']['tmp_name'],
                        $uploadDir . $image
                    );
                } else {
                    $error = "Only JPG, JPEG, PNG, WEBP and GIF images are allowed.";
                }
            }
            
            if (empty($error)) {
                // Insert category
                $database->insert("categories", [
                    "category_name" => $category_name,
                    "description" => $description,
                    "image" => $image,
                    "is_featured" => $is_featured,
                    "status" => $status,
                    "created_at" => date("Y-m-d H:i:s")
                ]);
                
                header("Location: index.php?success=added");
                exit;
            }
        }
    }
}

include '../includes/header.php';
include '../includes/sidebar.php';
?>

<div class="lg:ml-64 min-h-screen bg-gray-100 p-4 md:p-6 lg:p-8">
    
    <div class="bg-white rounded-xl shadow max-w-3xl mx-auto">
        
        <!-- Page Header -->
        <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between border-b px-4 md:px-6 py-4 gap-3">
            <div>
                <h2 class="text-xl md:text-2xl font-bold text-gray-800 flex items-center">
                    <i class="fas fa-plus-circle text-green-600 mr-2"></i>
                    Add Category
                </h2>
                <p class="text-sm text-gray-500 mt-1">
                    <i class="fas fa-info-circle mr-1"></i>
                    Create a new product category
                </p>
            </div>
            <a href="index.php" 
               class="bg-gray-700 hover:bg-gray-800 text-white px-4 py-2 md:px-5 md:py-2 rounded-lg text-sm md:text-base transition duration-200 w-full sm:w-auto text-center">
                <i class="fas fa-arrow-left mr-2"></i> Back
            </a>
        </div>
        
        <div class="p-4 md:p-6">
            
            <!-- Error/Success Messages -->
            <?php if (!empty($error)) : ?>
                <div class="mb-5 rounded-lg bg-red-100 border border-red-300 text-red-700 px-4 py-3">
                    <i class="fas fa-exclamation-circle mr-2"></i>
                    <?= htmlspecialchars($error); ?>
                </div>
            <?php endif; ?>
            
            <?php if (!empty($success)) : ?>
                <div class="mb-5 rounded-lg bg-green-100 border border-green-300 text-green-700 px-4 py-3">
                    <i class="fas fa-check-circle mr-2"></i>
                    <?= htmlspecialchars($success); ?>
                </div>
            <?php endif; ?>
            
            <form method="POST" enctype="multipart/form-data">
                
                <!-- Category Name -->
                <div class="mb-5">
                    <label class="block mb-2 font-medium text-gray-700">
                        <i class="fas fa-tag text-blue-600 mr-2"></i>
                        Category Name <span class="text-red-500">*</span>
                    </label>
                    <input type="text" 
                           name="category_name" 
                           value="<?= isset($_POST['category_name']) ? htmlspecialchars($_POST['category_name']) : ''; ?>"
                           class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none transition duration-200"
                           placeholder="Enter category name"
                           required>
                </div>
                
                <!-- Category Image -->
                <div class="mb-5">
                    <label class="block mb-2 font-medium text-gray-700">
                        <i class="fas fa-image text-purple-600 mr-2"></i>
                        Category Image
                    </label>
                    <div class="relative">
                        <input type="file" 
                               name="image" 
                               id="categoryImage"
                               accept="image/*"
                               class="w-full border border-gray-300 rounded-lg px-4 py-3 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 cursor-pointer transition duration-200">
                        <p class="text-xs text-gray-500 mt-1">
                            <i class="fas fa-info-circle mr-1"></i>
                            Recommended: 300x300px (JPG, JPEG, PNG, WEBP, GIF)
                        </p>
                    </div>
                    
                    <!-- Image Preview -->
                    <div id="imagePreview" class="mt-3 hidden">
                        <div class="relative inline-block">
                            <img id="previewImg" src="#" alt="Category Image Preview" 
                                 class="w-32 h-32 object-cover rounded-lg border-2 border-blue-500">
                            <button type="button" onclick="removeImage()" 
                                    class="absolute -top-2 -right-2 bg-red-500 hover:bg-red-600 text-white rounded-full w-6 h-6 flex items-center justify-center transition duration-200">
                                <i class="fas fa-times text-xs"></i>
                            </button>
                        </div>
                    </div>
                </div>
                
                <!-- Description -->
                <div class="mb-5">
                    <label class="block mb-2 font-medium text-gray-700">
                        <i class="fas fa-align-left text-indigo-600 mr-2"></i>
                        Description
                    </label>
                    <textarea name="description" 
                              rows="4" 
                              class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none transition duration-200 resize-y"
                              placeholder="Enter category description (optional)"><?= isset($_POST['description']) ? htmlspecialchars($_POST['description']) : ''; ?></textarea>
                </div>
                
                <!-- Featured & Status Row -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 md:gap-6">
                    
                    <!-- Featured -->
                    <div class="mb-5">
                        <label class="flex items-center gap-3 cursor-pointer group p-3 bg-gray-50 rounded-lg border border-gray-200 hover:bg-gray-100 transition duration-200">
                            <input type="checkbox" 
                                   name="is_featured" 
                                   value="1"
                                   <?= isset($_POST['is_featured']) ? 'checked' : ''; ?>
                                   class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500 cursor-pointer">
                            <span class="font-medium text-gray-700 group-hover:text-gray-900">
                                <i class="fas fa-star text-yellow-500 mr-2"></i>
                                Featured Category
                            </span>
                        </label>
                        <p class="text-xs text-gray-500 mt-1 ml-1">
                            <i class="fas fa-info-circle mr-1"></i>
                            Featured categories appear on the homepage
                        </p>
                    </div>
                    
                    <!-- Status -->
                    <div class="mb-5">
                        <label class="block mb-2 font-medium text-gray-700">
                            <i class="fas fa-toggle-on text-green-600 mr-2"></i>
                            Status
                        </label>
                        <div class="flex flex-col sm:flex-row gap-3 p-3 bg-gray-50 rounded-lg border border-gray-200">
                            <label class="flex items-center gap-3 cursor-pointer">
                                <input type="radio" 
                                       name="status" 
                                       value="Active"
                                       <?= (!isset($_POST['status']) || $_POST['status'] == 'Active') ? 'checked' : ''; ?>
                                       class="w-4 h-4 text-blue-600 border-gray-300 focus:ring-blue-500 cursor-pointer">
                                <span class="text-gray-700">
                                    <i class="fas fa-check-circle text-green-500 mr-1"></i>
                                    Active
                                </span>
                            </label>
                            <label class="flex items-center gap-3 cursor-pointer">
                                <input type="radio" 
                                       name="status" 
                                       value="Inactive"
                                       <?= (isset($_POST['status']) && $_POST['status'] == 'Inactive') ? 'checked' : ''; ?>
                                       class="w-4 h-4 text-blue-600 border-gray-300 focus:ring-blue-500 cursor-pointer">
                                <span class="text-gray-700">
                                    <i class="fas fa-times-circle text-red-500 mr-1"></i>
                                    Inactive
                                </span>
                            </label>
                        </div>
                    </div>
                    
                </div>
                
                <!-- Form Actions -->
                <div class="flex flex-col sm:flex-row gap-3 pt-4 border-t mt-2">
                    
                    <button type="submit" 
                            name="save_category"
                            class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 md:px-8 md:py-3 rounded-lg font-medium transition duration-200 flex items-center justify-center w-full sm:w-auto">
                        <i class="fas fa-save mr-2"></i>
                        Save Category
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
// Image Preview
document.getElementById('categoryImage').addEventListener('change', function(e) {
    const file = this.files[0];
    if (file) {
        const reader = new FileReader();
        reader.onload = function(e) {
            const preview = document.getElementById('imagePreview');
            const img = document.getElementById('previewImg');
            img.src = e.target.result;
            preview.classList.remove('hidden');
        };
        reader.readAsDataURL(file);
    }
});

function removeImage() {
    document.getElementById('categoryImage').value = '';
    document.getElementById('imagePreview').classList.add('hidden');
    document.getElementById('previewImg').src = '#';
}

// Form validation - prevent empty category name
document.querySelector('form').addEventListener('submit', function(e) {
    const categoryName = document.querySelector('input[name="category_name"]').value.trim();
    if (!categoryName) {
        e.preventDefault();
        alert('Please enter a category name.');
        document.querySelector('input[name="category_name"]').focus();
    }
});
</script>

<?php include '../includes/footer.php'; ?>