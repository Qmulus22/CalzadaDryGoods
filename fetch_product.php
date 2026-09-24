PHP
<?php
include 'config.php';

$barcode = $_GET['barcode'];

// Prepare the SQL query to fetch product details based on the barcode
$sql = "SELECT * FROM products WHERE barcode = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $barcode);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    // Product found
    $row = $result->fetch_assoc();
    $product = array(
        'barcode' => $row['barcode'],
        'name' => $row['product_name'],
        'brand' => $row['brand'],
        'product_code' => $row['product_code'],
        'price' => $row['selling_price']
    );
    echo json_encode($product);
} else {
    // Product not found
    echo json_encode(null);
}

$stmt->close();
$conn->close();
?>