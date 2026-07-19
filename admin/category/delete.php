<?php

session_start();

require_once '../../config/constant.php';
require_once '../../config/db.php';

if (!isset($_SESSION['admin_id'])) {
    header("Location: " . ADMIN_URL . "auth/login.php");
    exit;
}

// Check ID
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {

    header("Location: index.php");
    exit;

}

$id = (int) $_GET['id'];

// Get Category
$category = $database->get("categories", "*", [
    "id" => $id
]);

// Category Not Found
if (!$category) {

    header("Location: index.php");
    exit;

}

// Delete Image
if (!empty($category['image'])) {

    $imagePath = "../../assets/uploads/categories/" . $category['image'];

    if (file_exists($imagePath)) {

        unlink($imagePath);

    }

}

// Delete Category
$database->delete("categories", [
    "id" => $id
]);

header("Location: index.php?success=deleted");
exit;

?>