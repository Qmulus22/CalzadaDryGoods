<?php
require 'config.php';

if (isset($_GET['query'])) {
    $query = trim($_GET['query']); // Trim whitespace from the input

    // Check if the query is empty
    if (empty($query)) {
        exit; // Stop further execution
    }

    // Prepare the SQL statement to prevent SQL injection
    $stmt = $conn->prepare("SELECT p.*, c.category_name 
                             FROM products p 
                             JOIN categories c ON p.category_id = c.category_id 
                             WHERE p.product_name LIKE ? 
                                OR c.category_name LIKE ? 
                                OR p.brand LIKE ? 
                                OR p.product_code LIKE ? 
                                OR p.barcode LIKE ?");

    $searchTerm = "%$query%";
    $stmt->bind_param("sssss", $searchTerm, $searchTerm, $searchTerm, $searchTerm, $searchTerm);
    
    $stmt->execute();
    $result = $stmt->get_result();

    // Check if any results were returned
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            // Customize the output as needed
            echo "<div class='product'>";
            echo "<h3>" . htmlspecialchars($row['product_name']) . "</h3>";
            echo "<p>Category: " . htmlspecialchars($row['category_name']) . "</p>";
            echo "<p>Brand: " . htmlspecialchars($row['brand']) . "</p>";
            echo "<p>Code: " . htmlspecialchars($row['product_code']) . "</p>";
            echo "<p>Barcode: " . htmlspecialchars($row['barcode']) . "</p>";
            echo "<p>Stock: " . htmlspecialchars($row['stock_quantity']) . "</p>";
            echo "<p>Price: ₱" . htmlspecialchars($row['selling_price']) . "</p>"; 
            echo "</div>";
        }
    } else {
        echo "<p>No products found.</p>";
    }

    $stmt->close();
}
$conn->close();
?>
