<?php
session_start();
require_once '../../config/constant.php';
require_once '../../config/db.php';

if (!isset($_SESSION['admin_id'])) {
    header("Location: " . ADMIN_URL . "auth/login.php");
    exit;
}

$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;

if ($id > 0) {
    // Get banner image
    $banner = $database->get("banners", "image", ["id" => $id]);
    
    if ($banner) {
        // Delete image file
        $uploadDir = "../../assets/uploads/banners/";
        if (!empty($banner['image']) && file_exists($uploadDir . $banner['image'])) {
            unlink($uploadDir . $banner['image']);
        }
        
        // Delete from database
        $database->delete("banners", ["id" => $id]);
    }
}

header("Location: index.php?success=deleted");
exit;
?>