<?php
require 'config.php';

$supplier_id = isset($_GET['supplier_id']) ? intval($_GET['supplier_id']) : 0;

if ($supplier_id > 0) {
    // Fetch supplier details
    $stmt = $conn->prepare("SELECT * FROM Suppliers WHERE supplier_id = ?");
    $stmt->bind_param('i', $supplier_id);
    $stmt->execute();
    $supplier = $stmt->get_result()->fetch_assoc();

    // Fetch related products using the productsuppliers table
    $stmt = $conn->prepare("
    SELECT ps.product_supplier_id, p.product_name, p.brand, c.category_name
    FROM productsuppliers ps
    JOIN Products p ON ps.product_id = p.product_id
    JOIN Categories c ON p.category_id = c.category_id
    WHERE ps.supplier_id = ? AND ps.status = 'active'
    ");
    $stmt->bind_param('i', $supplier_id);
    $stmt->execute();
    $products = $stmt->get_result();
} else {
    // Redirect or handle error if no supplier_id is provided
    header("Location: admin_menu.php?page=suppliers");
    exit();
}
?>

<style>
    .supplier-details {
        max-width: 900px;
        margin: 20px auto;
        padding: 20px;
        border: 1px solid #ccc;
        border-radius: 8px;
        background-color: #f9f9f9;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
    }

    .supplier-info {
        display: flex;
        justify-content: space-between;
        margin-bottom: 20px;
    }

    .supplier-info div {
        width: 48%;
    }

    .btn {
        padding: 10px 15px;
        border: none;
        border-radius: 5px;
        cursor: pointer;
        font-weight: bold;
    }

    .btn-relate-products {
        background-color: #28a745;
        color: white;
    }

    .separator {
        border-top: 1px solid #ccc;
        margin: 20px 0;
    }

    table {
        width: 100%;
        border-collapse: collapse;
    }

    th, td {
        padding: 10px;
        text-align: left;
        border-bottom: 1px solid #ddd;
    }

    th {
        background-color: #f2f2f2;
    }

    .btn-archive {
        background-color: #dc3545;
        color: white;
        border: none;
    }

    .btn-archive img {
        width: 16px;
        height: 16px;
    }

    .back-link {
        display: flex;
        align-items: center;
        margin-bottom: 15px;
        text-decoration: none;
        color: #007bff;
    }

    .back-link img {
        width: 16px;
        height: 16px;
        margin-right: 5px;
    }

    .related-products-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .modal {
        display: none;
        position: fixed;
        z-index: 1000;
        left: 0;
        top: 0;
        width: 100%;
        height: 100%;
        overflow: auto;
        background-color: rgba(0, 0, 0, 0.5);
    }

    .modal-content {
        background-color: #fefefe;
        margin: 10% auto; 
        padding: 20px;
        border: 1px solid #888;
        width: 500px; 
        max-width: 90%; 
        border-radius: 8px;
        text-align: center;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.2); 
    }

    .modal-header, .modal-footer {
        padding: 10px;
    }
    .modal-header {

        padding: 15px; 
        border-top-left-radius: 8px;
        border-top-right-radius: 8px; 
    }

    .modal-body {
        margin: 20px 0; 
    }

    .modal-footer {
        margin-top: 20px;
        display: flex;
        justify-content: space-between;
    }
    .close {
        color: #aaa;
        float: right;
        font-size: 28px;
        font-weight: bold;
    }

    .modal-footer button {
        margin-left: 10px; 
        padding: 10px 15px; 
        border: none;
        border-radius: 5px; 
        font-weight: bold; 
        margin-top: 10px; 
        width: 100%;
    }


    .modal-footer button.archive {
        background-color: #dc3545; 
        color: white; 
    }

 
    .modal-footer button.cancel {
        background-color: #28a745;
        color: white; 
    }


    .btn-relate {
        background-color: #28a745;
        color: white; 
}
</style>

<div class="supplier-details">
    <a href="admin_menu.php?page=suppliers" class="back-link">
        <img src="icons/arrow.ico" alt="Back"> Back
    </a>
    
    <h2>Supplier Information</h2><br><br>
    <div class="supplier-info">
        <div>
            <p><strong>Name:</strong> <?php echo htmlspecialchars($supplier['supplier_name']); ?></p><br>
            <p><strong>Contact Person:</strong> <?php echo htmlspecialchars($supplier['contact_person']); ?></p>
        </div>
        <div>
            <p><strong>Contact Number:</strong> <?php echo htmlspecialchars($supplier['contact_number']); ?></p><br>
            <p><strong>Address:</strong> <?php echo htmlspecialchars($supplier['address']); ?></p>
        </div>
    </div>
    
    <hr>
    <br><br>
    <div class="related-products-header">
        <h3>Related Products</h3>
        <button class="btn btn-relate-products" onclick="openRelateProductsModal()">
            <img src="icons/link.ico" alt="Link" style="width: 16px; height: 16px; margin-right: 5px;"> Relate Products
        </button>
    </div>
    <br><br><hr>

    <table>
        <thead>
            <tr>
                <th>Product Name</th>
                <th>Brand</th>
                <th>Category</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($product = $products->fetch_assoc()): ?>
                <tr>
                    <td><?php echo htmlspecialchars($product['product_name']); ?></td>
                    <td><?php echo htmlspecialchars($product['brand']); ?></td>
                    <td><?php echo htmlspecialchars($product['category_name']); ?></td>
                    <td>
                        <button class="btn btn-archive" onclick="showArchiveModal(<?php echo $product['product_supplier_id']; ?>)">
                            <img src="icons/x.ico" alt="Archive"> Remove <!-- Archive Icon -->
                        </button>
                    </td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>

<!-- Archive Confirmation Modal -->
<div id="archiveModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h4>Confirm Archive</h4>
        </div>
        <div class="modal-body">
            <p>Are you sure you want to archive this product?</p>
        </div>
        <div class="modal-footer">
            <button class="archive" onclick="archiveProduct()">Yes</button>
            <button class="cancel" onclick="closeModal()">No</button>
        </div>
    </div>
</div>

<!-- Relate Product Modal -->
<div id="relateProductsModal" class="modal">
    <div class="modal-content">
        <span class="close" onclick="closeModal()" style="float: right; cursor: pointer; font-size: 24px; color: #aaa;">&times;</span>
        <div class="modal-header">
            <h2>Relate Products to Supplier</h2>
        </div>
        <div class="modal-body">
            <label for="productSelect">Select Product:</label>
            <select id="productSelect" style="width: 100%; padding: 10px; border-radius: 4px; border: 1px solid #ccc;">
                <!-- Options will be populated dynamically -->
            </select>
        </div>
        <div class="modal-footer">
            <button class="btn btn-relate" onclick="relateProducts()">Relate</button>
        </div>
    </div>
</div>

<script>
    let productSupplierIdToArchive;

    function showArchiveModal(productSupplierId) {
        productSupplierIdToArchive = productSupplierId;
        document.getElementById("archiveModal").style.display = "block";
    }

    function closeModal() {
        document.getElementById("archiveModal").style.display = "none";
        document.getElementById("relateProductsModal").style.display = "none";
    }

    function archiveProduct() {
        const xhr = new XMLHttpRequest();
        xhr.open("POST", "archive_supplier_relation.php", true);
        xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
        xhr.onreadystatechange = function () {
            if (xhr.readyState === XMLHttpRequest.DONE) {
                if (xhr.status === 200) {
                    const response = JSON.parse(xhr.responseText);
                    if (response.success) {
                        alert("Supplier relationship archived successfully.");
                        location.reload();
                    } else {
                        alert(response.message);
                    }
                } else {
                    alert("Failed to archive supplier relationship.");
                }
            }
        };
        xhr.send("product_supplier_id=" + productSupplierIdToArchive);
        closeModal();
    }
///////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    function openRelateProductsModal() {
        document.getElementById('relateProductsModal').style.display = 'block';
        populateProductSelect();
    }

    function populateProductSelect() {
        const select = document.getElementById('productSelect');
        select.innerHTML = ''; // Clear existing options
        <?php
        // Fetch all active products to populate the dropdown
        $stmt = $conn->prepare("SELECT product_id, product_name, brand FROM Products WHERE status = 'active'");
        $stmt->execute();
        $result = $stmt->get_result();
        while ($product = $result->fetch_assoc()) {
            echo "select.innerHTML += '<option value=\"{$product['product_id']}\">{$product['product_name']} ({$product['brand']})</option>';"; 
        }
        ?>
    }

    function relateProducts() {
        const productId = document.getElementById('productSelect').value;
        const supplierId = <?php echo $supplier_id; ?>; // Get supplier ID from PHP

        const xhr = new XMLHttpRequest();
        xhr.open("POST", "relate_product.php", true);
        xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
        xhr.onreadystatechange = function () {
            if (xhr.readyState === XMLHttpRequest.DONE) {
                if (xhr.status === 200) {
                    alert("Product related successfully.");
                    location.reload(); // Reload to reflect changes
                } else {
                    alert("Failed to relate product.");
                }
            }
        };
        xhr.send("product_id=" + productId + "&supplier_id=" + supplierId);
    }
</script>