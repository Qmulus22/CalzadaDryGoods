<?php
require 'config.php';

if (isset($_GET['category_id'])) {
    $category_id = intval($_GET['category_id']);
    $stmt = $conn->prepare("SELECT * FROM Categories WHERE category_id = ?");
    $stmt->bind_param('i', $category_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $category = $result->fetch_assoc();

    if ($category) {
        echo json_encode($category);
    } else {
        echo json_encode(['error' => 'Category not found.']);
    }
} else {
    echo json_encode(['error' => 'Invalid category ID.']);
}
?>
