<?php
require 'config.php';

$order_id = $_GET['order_id'] ?? null;

if ($order_id) {
    // Fetch the order details
    $sql = "SELECT o.order_id, o.order_date, o.status, s.supplier_name, s.contact_person, s.contact_number
            FROM Orders o
            JOIN Suppliers s ON o.supplier_id = s.supplier_id
            WHERE o.order_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('i', $order_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $order = $result->fetch_assoc();

        // Prepare order details for JSON response
        $order_details = [
            'order_id' => $order['order_id'],
            'order_date' => $order['order_date'],
            'status' => $order['status'],
            'supplier_name' => $order['supplier_name'],
            'contact_person' => $order['contact_person'],
            'contact_number' => $order['contact_number'],
            'products' => []
        ];

        // Fetch the products in the order
        $sql_products = "SELECT p.product_name, od.requested_quantity
                         FROM OrderDetails od
                         JOIN Products p ON od.product_id = p.product_id
                         WHERE od.order_id = ?";
        $stmt_products = $conn->prepare($sql_products);
        $stmt_products->bind_param('i', $order_id);
        $stmt_products->execute();
        $result_products = $stmt_products->get_result();

        if ($result_products->num_rows > 0) {
            while ($product = $result_products->fetch_assoc()) {
                $order_details['products'][] = [
                    'product_name' => $product['product_name'],
                    'requested_quantity' => $product['requested_quantity']
                ];
            }
        }

        // Return JSON response
        echo json_encode($order_details);
    } else {
        echo json_encode(['error' => 'Order not found.']);
    }
} else {
    echo json_encode(['error' => 'Invalid order ID.']);
}
?>
