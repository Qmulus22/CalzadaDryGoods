<?php
require 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents("php://input"), true);
    $cart = json_decode($data['cart'], true);
    $paymentMethod = $data['paymentMethod'];
    $totalAmount = $data['totalAmount'];
    $paymentAmount = $data['paymentAmount'] ?? null; // Can be null if GCash
    $gcashRefCode = $data['gcashRefCode'] ?? null; // Can be null if cash

    // Prepare the sale entry
    $stmt = $conn->prepare("INSERT INTO sales (sale_date, total_amount, payment_amount, change_amount, user_id) VALUES (NOW(), ?, ?, ?, ?)");
    $userId = 1; // Assuming a user ID of 1 for this example; you should set this dynamically based on your session.
    $changeAmount = $paymentAmount - $totalAmount;

    // Execute sale entry
    if ($stmt->execute([$totalAmount, $paymentAmount, $changeAmount, $userId])) {
        $saleId = $stmt->insert_id; // Get the newly created sale ID

        // Prepare and execute sale details entries
        $stmtDetail = $conn->prepare("INSERT INTO saledetails (sale_id, product_id, quantity, unit_price, total_price) VALUES (?, ?, ?, ?, ?)");
        
        foreach ($cart as $item) {
            $totalPrice = $item['selling_price'] * $item['quantity'];
            $stmtDetail->execute([$saleId, $item['product_id'], $item['quantity'], $item['selling_price'], $totalPrice]);
        }

        // Optionally, handle GCash reference code if needed
        if ($paymentMethod === 'gcash') {
            // Save GCash reference code logic here if needed
        }

        echo json_encode(['success' => true, 'orderId' => $saleId]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to record sale.']);
    }
}
?>
