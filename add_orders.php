<?php
require 'config.php';

$response = ['success' => false, 'message' => '', 'orderId' => null]; // Initialize orderId

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $supplier_id = $_POST['supplier_id'];
    $status = 'pending'; // Set initial order status
    $products = $_POST['products']; // Array of product IDs
    $quantities = $_POST['quantities']; // Array of quantities

    // Begin transaction
    $conn->begin_transaction();

    try {
        // Insert into orders table
        $sql_order = "INSERT INTO orders (supplier_id, order_date, status) VALUES (?, NOW(), ?)";
        $stmt_order = $conn->prepare($sql_order);
        $stmt_order->bind_param("is", $supplier_id, $status);

        if ($stmt_order->execute()) {
            $order_id = $conn->insert_id;
            $response['orderId'] = $order_id; // Add this line to capture orderId

            foreach ($products as $index => $product_id) {
                $requested_quantity = $quantities[$index];

                // Insert into orderdetails
                $sql_orderdetails = "INSERT INTO orderdetails (order_id, product_id, requested_quantity) VALUES (?, ?, ?)";
                $stmt_orderdetails = $conn->prepare($sql_orderdetails);
                $stmt_orderdetails->bind_param("iii", $order_id, $product_id, $requested_quantity);
                
                if (!$stmt_orderdetails->execute()) {
                    throw new Exception("Error inserting order details: " . $stmt_orderdetails->error);
                }
            }

            // Commit transaction
            $conn->commit();
            $response['success'] = true;
            $response['message'] = "Order and details inserted successfully!";
        } else {
            throw new Exception("Error inserting order: " . $stmt_order->error);
        }
    } catch (Exception $e) {
        // Rollback transaction
        $conn->rollback();
        $response['message'] = $e->getMessage();
    }

    echo json_encode($response);
}
