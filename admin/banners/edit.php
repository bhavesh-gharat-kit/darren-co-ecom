<?php
session_start();
require_once '../../config/constant.php';
require_once '../../config/db.php';

if (!isset($_SESSION['admin_id'])) {
    header("Location: " . ADMIN_URL . "auth/login.php");
    exit;
}

$page_title = "Edit Banner";

$error = "";
$success = "";

$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;

if ($id <= 0) {
    header("Location: index.php");
    exit;
}

// Fetch banner data
$banner = $database->get("banners", "*", [
    "id" => $id
]);

if (!$banner) {
    header("Location: index.php");
    exit;
}

if (isset($_POST['update_banner'])) {

    $title = trim($_POST['title']);
    $subtitle = trim($_POST['subtitle']);
    $button_text = trim($_POST['button_text']);
    $button_link = trim($_POST['button_link']);
    $status = $_POST['status'];

    // Image Upload
    $image = $banner['image'];
    if (!empty($_FILES['image']['name'])) {
        $uploadDir = "../../assets/uploads/banners/";

        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        $extension = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
        $allowed = ['jpg', 'jpeg', 'png', 'webp', 'gif'];

        if (in_array($extension, $allowed)) {
            // Delete old image
            if (!empty($banner['image']) && file_exists($uploadDir . $banner['image'])) {
                unlink($uploadDir . $banner['image']);
            }

            $image = time() . rand(1000, 9999) . "." . $extension;
            move_uploaded_file($_FILES['image']['tmp_name'], $uploadDir . $image);
        } else {
            $error = "Only JPG, JPEG, PNG, WEBP and GIF images are allowed.";
        }
    }

    // Validation
    if (empty($error)) {
        if (empty($title)) {
            $error = "Title is required.";
        } elseif (empty($button_text)) {
            $error = "Button text is required.";
        } elseif (empty($button_link)) {
            $error = "Button link is required.";
        }
    }

    if (empty($error)) {
        try {
            $database->update("banners", [
                "title" => $title,
                "subtitle" => $subtitle,
                "image" => $image,
                "button_text" => $button_text,
                "button_link" => $button_link,
                "status" => $status
            ], [
                "id" => $id
            ]);

            header("Location: index.php?success=updated");
            exit;

        } catch (Exception $e) {
            $error = "Failed to update banner: " . $e->getMessage();
        }
    }
}

include '../includes/header.php';
include '../includes/sidebar.php';
?>

<div class="lg:ml-64 min-h-screen bg-gray-100 p-4 md:p-6 lg:p-8">

    <div class="bg-white rounded-xl shadow max-w-3xl mx-auto">

        <!-- Header -->
        <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between border-b px-4 md:px-6 py-4 gap-3">
            <h2 class="text-xl md:text-2xl font-bold flex items-center">
                <i class="fas fa-edit text-blue-600 mr-2"></i>
                Edit Banner
            </h2>
            <a href="index.php"
                class="bg-gray-700 hover:bg-gray-800 text-white px-4 py-2 rounded-lg text-sm transition w-full sm:w-auto text-center">
                <i class="fas fa-arrow-left mr-2"></i> Back
            </a>
        </div>

        <div class="p-4 md:p-6">

            <?php if (!empty($error)): ?>
                <div class="mb-5 rounded-lg bg-red-100 border border-red-300 text-red-700 px-4 py-3">
                    <i class="fas fa-exclamation-circle mr-2"></i>
                    <?= htmlspecialchars($error); ?>
                </div>
            <?php endif; ?>

            <form method="POST" enctype="multipart/form-data">

                <!-- Title -->
                <div class="mb-5">
                    <label class="block mb-2 font-medium text-gray-700">
                        <i class="fas fa-heading text-blue-600 mr-2"></i>
                        Title <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="title"
                        value="<?= htmlspecialchars($banner['title']); ?>"
                        class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none"
                        placeholder="Enter banner title" required>
                </div>

                <!-- Subtitle -->
                <div class="mb-5">
                    <label class="block mb-2 font-medium text-gray-700">
                        <i class="fas fa-subscript text-purple-600 mr-2"></i>
                        Subtitle
                    </label>
                    <input type="text" name="subtitle"
                        value="<?= htmlspecialchars($banner['subtitle']); ?>"
                        class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none"
                        placeholder="Enter banner subtitle">
                </div>

                <!-- Image -->
                <div class="mb-5">
                    <label class="block mb-2 font-medium text-gray-700">
                        <i class="fas fa-image text-indigo-600 mr-2"></i>
                        Banner Image
                    </label>
                    <?php if (!empty($banner['image'])): ?>
                        <div class="mb-3">
                            <img src="<?= BASE_URL ?>assets/uploads/banners/<?= htmlspecialchars($banner['image']); ?>"
                                alt="<?= htmlspecialchars($banner['title']); ?>"
                                class="w-48 h-32 object-cover rounded-lg border-2 border-gray-200">
                            <p class="text-xs text-gray-500 mt-1">Current Image</p>
                        </div>
                    <?php endif; ?>
                    <input type="file" name="image" accept="image/*"
                        class="w-full border border-gray-300 rounded-lg px-4 py-3 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 cursor-pointer">
                    <p class="text-xs text-gray-500 mt-1">
                        <i class="fas fa-info-circle mr-1"></i>
                        Leave empty to keep current image. Recommended size: 1920x1080px
                    </p>
                    <!-- Image Preview -->
                    <div id="imagePreview" class="mt-3 hidden">
                        <img id="previewImg" src="#" alt="Preview" class="w-48 h-32 object-cover rounded-lg border-2 border-blue-500">
                    </div>
                </div>

                <!-- Button Text -->
                <div class="mb-5">
                    <label class="block mb-2 font-medium text-gray-700">
                        <i class="fas fa-link text-green-600 mr-2"></i>
                        Button Text <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="button_text"
                        value="<?= htmlspecialchars($banner['button_text']); ?>"
                        class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none"
                        placeholder="e.g. Shop Now" required>
                </div>

                <!-- Button Link -->
                <div class="mb-5">
                    <label class="block mb-2 font-medium text-gray-700">
                        <i class="fas fa-external-link-alt text-yellow-600 mr-2"></i>
                        Button Link <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="button_link"
                        value="<?= htmlspecialchars($banner['button_link']); ?>"
                        class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none"
                        placeholder="e.g. /shop.php" required>
                    <p class="text-xs text-gray-500 mt-1">
                        <i class="fas fa-info-circle mr-1"></i>
                        Enter full URL or relative path (e.g., /shop.php)
                    </p>
                </div>

                <!-- Status -->
                <div class="mb-5">
                    <label class="block mb-2 font-medium text-gray-700">
                        <i class="fas fa-toggle-on text-green-600 mr-2"></i>
                        Status
                    </label>
                    <div class="flex gap-6">
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="radio" name="status" value="Active"
                                <?= $banner['status'] == 'Active' ? 'checked' : ''; ?>
                                class="w-4 h-4 text-blue-600">
                            <span>Active</span>
                        </label>
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="radio" name="status" value="Inactive"
                                <?= $banner['status'] == 'Inactive' ? 'checked' : ''; ?>
                                class="w-4 h-4 text-blue-600">
                            <span>Inactive</span>
                        </label>
                    </div>
                </div>

                <!-- Buttons -->
                <div class="flex flex-col sm:flex-row gap-3 pt-4 border-t">
                    <button type="submit" name="update_banner"
                        class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-lg font-medium transition w-full sm:w-auto">
                        <i class="fas fa-save mr-2"></i> Update Banner
                    </button>
                    <a href="index.php"
                        class="bg-gray-500 hover:bg-gray-600 text-white px-6 py-3 rounded-lg font-medium transition w-full sm:w-auto text-center">
                        <i class="fas fa-times mr-2"></i> Cancel
                    </a>
                </div>

            </form>

        </div>

    </div>

</div>

<script>
    // Image Preview
    document.querySelector('input[name="image"]').addEventListener('change', function(e) {
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
</script>

<?php include '../includes/footer.php'; ?>