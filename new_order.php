<?php
require 'config.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create New Order</title>
    <style>
        .container {
            max-width: 900px;
            margin: 20px auto;
            padding: 20px;
            border: 1px solid #ccc;
            border-radius: 8px;
            background-color: #f9f9f9;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }
        .form-group {
            margin-bottom: 20px;
        }
        label {
            font-weight: bold;
        }
        select, input {
            width: 100%;
            padding: 10px;
            border-radius: 4px;
            border: 1px solid #ccc;
            margin-top: 5px;
        }
        .btn {
            padding: 10px 15px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-weight: bold;
        }
        .btn-add-product {
            background-color: #FFA500; /* Orange */
            color: white;
            float: right; /* Align to the right */
            margin-left: 10px; /* Space between buttons */
        }
        .btn-add-products {
            background-color: #FFA500; /* Orange */
            color: white;
            float: center; /* Align to the right */
            margin-left: 10px; /* Space between buttons */
        }
        .btn-submit {
            background-color: #28a745; /* Green */
            color: white;
        }
        .btn-remove {
            background-color: #dc3545; /* Red */
            color: white;
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
        }
        .close {
            float: right;
            cursor: pointer;
            font-size: 24px;
            color: #aaa;
        }
        #success-message, #error-message {
            color: green;
            margin-top: 10px;
        }
        #error-message {
            color: red;
        }
    </style>
</head>
<body>

<div class="container">
    <h1>Create New Order</h1><br><br>

    <form id="order-form">
        <div class="form-group">
            <label for="supplier_id">Select Supplier:</label>
            <select name="supplier_id" id="supplier_id" required>
                <option value="">Select Supplier</option>
                <?php
                $sql_suppliers = "SELECT supplier_id, supplier_name, contact_person, address, contact_number FROM suppliers WHERE status = 'active'";
                $result_suppliers = $conn->query($sql_suppliers);

                while ($row = $result_suppliers->fetch_assoc()) {
                    echo "<option value='" . $row['supplier_id'] . "'>" . $row['supplier_name'] . " - " . $row['contact_person'] . " (" . $row['address'] . ", " . $row['contact_number'] . ")</option>";
                }
                ?>
            </select>
        </div>

        <div class="form-group">
            <label>Add Products</label>
            <button type="button" class="btn btn-add-product" onclick="openProductModal()">Select Products</button>
        </div>

        <div id="product-list"></div>

        <br>
        <button type="button" class="btn btn-submit" onclick="confirmAndSubmit()">Submit Order</button>
    </form>
    <div id="success-message"></div>
    <div id="error-message"></div>
</div>

<!-- Product Selection Modal -->
<div id="productModal" class="modal">
    <div class="modal-content">
        <span class="close" onclick="closeProductModal()">&times;</span>
        <h2>Add Products to The List!</h2><br><br>
        <label for="productSelect">Select Product:</label>
        <select id="productSelect">
            <option value="">Select Product</option>
            <?php
            $sql_products = "SELECT product_id, product_name, brand FROM products WHERE status = 'active'";
            $result_products = $conn->query($sql_products);

            while ($row = $result_products->fetch_assoc()) {
                echo "<option value='" . $row['product_id'] . "'>" . $row['product_name'] . " (" . $row['brand'] . ")</option>";
            }
            ?>
        </select>
        <br><br><br>
        <label for="quantity">Enter Quantity:</label>
        <input type="number" id="quantity" min="1" placeholder="Quantity">
        <br><br><br>
        <button type="button" class="btn btn-add-products" onclick="addProduct()">Add to List</button><br><br>
    </div>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<script>
    function openProductModal() {
        document.getElementById('productModal').style.display = 'block';
    }

    function closeProductModal() {
        document.getElementById('productModal').style.display = 'none';
    }

    function addProduct() {
        const productSelect = document.getElementById('productSelect');
        const productId = productSelect.value;
        const quantity = document.getElementById('quantity').value;

        if (!productId || !quantity) {
            alert("Please select a product and provide a quantity.");
            return;
        }

        const productList = document.getElementById('product-list');
        const productRow = `
            <div class="form-group">
                <input type="hidden" name="products[]" value="${productId}">
                <label>Product: ${productSelect.options[productSelect.selectedIndex].text} (Quantity: ${quantity})</label>
                <input type="hidden" name="quantities[]" value="${quantity}">
                <button type="button" class="btn btn-remove" onclick="removeProduct(this)">Remove</button>
            </div>
        `;
        productList.insertAdjacentHTML('beforeend', productRow);
        closeProductModal();
    }

    function removeProduct(button) {
        button.parentElement.remove();
    }

    function confirmAndSubmit() {
        const confirmation = confirm("Are you sure you want to submit the order?");
        if (confirmation) {
            const form = document.getElementById('order-form');
            const formData = new FormData(form);

            fetch('add_orders.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    document.getElementById('success-message').textContent = data.message;
                    
                    // Prompt for downloading the PDF
                    const downloadConfirmation = confirm("Order submitted successfully! Do you want to download the PDF?");
                    if (downloadConfirmation) {
                        window.location.href = `generateNewOrderPdf.php?orderId=${data.orderId}`;
                    }
                    
                    setTimeout(() => {
                        document.getElementById('success-message').textContent = '';
                        location.reload(); // Refresh the page to show the newly added order
                    }, 2000);
                } else {
                    document.getElementById('error-message').textContent = data.message;
                }
            })
            .catch(error => {
                document.getElementById('error-message').textContent = 'An error occurred. Please try again.';
                console.error('Error:', error);
            });
        }
    }
</script>

</body>
</html>
