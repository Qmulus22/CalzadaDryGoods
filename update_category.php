<?php
// Ensure session is started only once
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Include database configuration
require 'config.php';

// Initialize response array
$response = array('success' => false, 'message' => '');

// Check if the request method is POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Retrieve and sanitize input data
    $category_id = isset($_POST['category_id']) ? intval($_POST['category_id']) : 0;
    $category_name = isset($_POST['category_name']) ? trim($_POST['category_name']) : '';
    $description = isset($_POST['description']) ? trim($_POST['description']) : '';

    // Validate input data
    if (empty($category_name) || empty($description)) {
        $response['message'] = 'Category name and description are required.';
    } else {
        // Prepare SQL statement to update category
        $stmt = $conn->prepare("UPDATE Categories SET category_name = ?, description = ? WHERE category_id = ?");
        if ($stmt) {
            // Bind parameters and execute the statement
            $stmt->bind_param('ssi', $category_name, $description, $category_id);
            if ($stmt->execute()) {
                $response['success'] = true;
                $response['message'] = 'Category updated successfully!';
            } else {
                $response['message'] = 'Failed to update category. Please try again.';
            }
            // Close statement
            $stmt->close();
        } else {
            $response['message'] = 'Failed to prepare the SQL statement.';
        }
    }
} else {
    $response['message'] = 'Invalid request method.';
}

// Close database connection
$conn->close();

// Return response as JSON
header('Content-Type: application/json');
echo json_encode($response);
?>
