<?php
require 'config.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $order_id = $_POST['order_id'] ?? null;

    if ($order_id) {
        // Prepare and execute the update statement
        $sql = "UPDATE Orders SET status = ? WHERE order_id = ?";
        $stmt = $conn->prepare($sql);
        $new_status = 'Cancelled';
        $stmt->bind_param('si', $new_status, $order_id);

        if ($stmt->execute()) {
            echo "Order status updated to Cancelled.";
        } else {
            echo "Error updating order status: " . $stmt->error;
        }

        $stmt->close();
    } else {
        echo "Order ID is required.";
    }
} else {
    echo "Invalid request method.";
}

$conn->close();
?>
