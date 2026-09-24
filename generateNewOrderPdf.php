<?php
require('fpdf/fpdf.php');
require 'config.php';

if (isset($_GET['orderId'])) {
    $orderId = intval($_GET['orderId']); // Ensure it's an integer

    // Fetch order details and status
    $sql_order = "SELECT o.*, s.supplier_name, s.contact_person, s.address, s.contact_number 
                   FROM orders o 
                   JOIN suppliers s ON o.supplier_id = s.supplier_id 
                   WHERE o.order_id = ?";
    $stmt_order = $conn->prepare($sql_order);
    $stmt_order->bind_param("i", $orderId);
    $stmt_order->execute();
    $result_order = $stmt_order->get_result();
    $order = $result_order->fetch_assoc();

    // Check if the order exists
    if ($order) {
        // Fetch the status explicitly to ensure it is retrieved correctly
        $sql_status = "SELECT status FROM orders WHERE order_id = ?";
        $stmt_status = $conn->prepare($sql_status);
        $stmt_status->bind_param("i", $orderId);
        $stmt_status->execute();
        $result_status = $stmt_status->get_result();
        $statusRow = $result_status->fetch_assoc();

        // Check if the order status is "Received"
        $statusReceived = (trim($statusRow['status']) === 'received');

        // Fetch order details for products
        $sql_details = "SELECT od.*, p.product_name 
                        FROM orderdetails od 
                        JOIN products p ON od.product_id = p.product_id 
                        WHERE od.order_id = ?";
        $stmt_details = $conn->prepare($sql_details);
        $stmt_details->bind_param("i", $orderId);
        $stmt_details->execute();
        $result_details = $stmt_details->get_result();

        // Create PDF
        $pdf = new FPDF();
        $pdf->AddPage();
        
        // Logo (centered and larger)
        $pdf->Image('icons/calzada.png', 70, 10, 60); // Center the image with width 60
        $pdf->Ln(55); // Add space after the logo

        // Set font for order details
        $pdf->SetFont('Arial', 'B', 16);
        
        // Left-aligned text for order details
        if ($statusReceived) {
            $pdf->SetTextColor(255, 0, 0); // Red color for the status
            $pdf->Cell(0, 10, 'RECEIVED', 0, 1, 'L'); // Left-aligned status indicator
            $pdf->SetTextColor(0); // Reset text color
            $pdf->Ln(5); // Add space after the status
        }

        // Set smaller font for details
        $pdf->SetFont('Arial', '', 12);
        $pdf->Cell(0, 10, 'Order ID: ' . $orderId, 0, 1, 'L');
        $pdf->Cell(0, 10, 'Supplier Name: ' . $order['supplier_name'], 0, 1, 'L');
        $pdf->Cell(0, 10, 'Contact Person: ' . $order['contact_person'], 0, 1, 'L');
        $pdf->Cell(0, 10, 'Address: ' . $order['address'], 0, 1, 'L');
        $pdf->Cell(0, 10, 'Contact Number: ' . $order['contact_number'], 0, 1, 'L');
        $pdf->Cell(0, 10, 'Order Date: ' . $order['order_date'], 0, 1, 'L');
        $pdf->Ln(10); // Add some space before the table

        // Table Header
        $pdf->SetFont('Arial', 'B', 12);
        $pdf->Cell(80, 10, 'Product Name', 1, 0, 'C');
        $pdf->Cell(40, 10, 'Quantity', 1, 1, 'C');

        // Table Body
        $pdf->SetFont('Arial', '', 12); // Regular font for values
        while ($row = $result_details->fetch_assoc()) {
            $pdf->Cell(80, 10, $row['product_name'], 1, 0, 'C');
            $pdf->Cell(40, 10, $row['requested_quantity'], 1, 1, 'C');
        }

        // Output PDF as a download
        header('Content-Type: application/pdf');
        header('Content-Disposition: attachment; filename="order_' . $orderId . '.pdf"');
        header('Cache-Control: no-cache, no-store, must-revalidate'); // HTTP 1.1.
        header('Pragma: no-cache'); // HTTP 1.0.
        header('Expires: 0'); // Proxies.

        $pdf->Output('I'); // Output to browser
        exit();
    } else {
        echo "Order not found.";
    }
} else {
    echo "Order ID not specified.";
}
?>
