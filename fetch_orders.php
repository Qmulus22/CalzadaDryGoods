<?php
require 'config.php';

$status = $_GET['status'] ?? 'Pending'; // Default to 'Pending' if not set

$sql = "SELECT o.order_id, s.supplier_name, s.contact_person, o.order_date, o.status
        FROM Orders o
        JOIN Suppliers s ON o.supplier_id = s.supplier_id
        WHERE o.status = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param('s', $status);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    echo '<table class="table">';
    echo '<tr>';
    echo '<th>Order ID</th>';
    echo '<th>Supplier Name</th>';
    echo '<th>Contact Person</th>';
    echo '<th>Order Date</th>';
    echo '<th>Status</th>';
    echo '<th>Actions</th>';
    echo '</tr>';
    
    while ($row = $result->fetch_assoc()) {
        echo '<tr>';
        echo '<td>' . $row['order_id'] . '</td>';
        echo '<td>' . $row['supplier_name'] . '</td>';
        echo '<td>' . $row['contact_person'] . '</td>';
        echo '<td>' . $row['order_date'] . '</td>';
        echo '<td>' . $row['status'] . '</td>';
        echo '<td>
                <button class="action-button view-button" onclick="viewOrder(' . $row['order_id'] . ')">
                    <img src="icons/view.ico" alt="View" style="width: 20px; height: 20px;">
                    View
                </button>';
        
        // Case-insensitive comparison to hide the download button for "Cancelled" status
        if (strcasecmp($row['status'], 'Cancelled') !== 0) {
            echo '<button class="action-button download-button" onclick="downloadOrder(' . $row['order_id'] . ')">
                    <img src="icons/download.ico" alt="Download" style="width: 20px; height: 20px;">
                    Download
                  </button>';
        }

        echo '</td>';
        echo '</tr>';
    }
    
    echo '</table>';
} else {
    echo 'No orders found.';
}

?>
