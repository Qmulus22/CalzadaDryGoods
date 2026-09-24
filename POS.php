<?php
require 'config.php'; // Include the database connection

// Function to fetch product by barcode
function fetchProductByBarcode($conn, $barcode) {
    $stmt = $conn->prepare("SELECT * FROM products WHERE barcode = ?");
    $stmt->bind_param("s", $barcode);
    $stmt->execute();
    return $stmt->get_result()->fetch_assoc();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['barcode'])) {
    $barcode = $_POST['barcode'];
    $product = fetchProductByBarcode($conn, $barcode);
    echo json_encode($product);
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>POS System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
            height: 1080px;
            margin: 0;
            display: flex;
            flex-direction: column;
        }
        .row {
            flex: 1;
        }
        .product-container {
            border: 3px solid #ccc;
            border-radius: 10px;
            background-color: #ffffff;
            height: 600px;
            overflow-y: auto;
            margin-right: 20px;
            padding: 15px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }
        .product-item {
            display: flex;
            justify-content: space-between;
            padding: 10px;
            border-bottom: 1px solid #ddd;
            transition: background-color 0.3s;
        }
        .product-item:hover {
            background-color: #f1f1f1;
        }
        .summary-container {
            border: 3px solid #ccc;
            border-radius: 10px;
            background-color: #ffffff;
            height: 500px;
            width: 450px;
            padding: 20px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }
        .total-section {
            background-color: #ffc107;
            font-size: 2rem;
            border-radius: 5px;
            margin-bottom: 1rem;
            padding: 15px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }
        .gcash-label:hover {
            text-decoration: underline;
            cursor: pointer;
        }
        #gcashContainer {
            background-color: #e7f1ff;
            padding: 10px;
            border-radius: 5px;
            margin-top: 10px;
        }
        footer {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            background-color: orange;
            color: black;
            text-align: center;
            padding: 8px 0;
            box-shadow: 0 -2px 5px rgba(0, 0, 0, 0.2);
        }
        .form-control {
            border-radius: 5px;
            margin-bottom: 10px;
        }
        #toggle-icon {
            cursor: pointer;
            width: 30px;
            transition: transform 0.3s;
        }
        .btn-add {
            margin-left: 10px;
            background-color: #4CAF50;
            border: none;
            padding: 5px 10px;
            color: white;
            border-radius: 5px;
        }
        .btn-remove {
            background-color: #f44336;
            color: white;
            padding: 10px 20px;
            width: 100%;
            font-size: 1.2rem;
            margin-bottom: 10px;
            display: flex;
            align-items: center;
        }
        .btn-clear {
            background-color: #ff9800;
            color: white;
            padding: 10px 20px;
            width: 100%;
            font-size: 1.2rem;
            display: flex;
            align-items: center;
        }
        .btn-remove img, .btn-clear img {
            margin-right: 10px;
            width: 24px;
            height: 24px;
        }
        .product-item {
            padding: 10px;
            border: 1px solid #ccc;
            margin: 5px 0;
            border-radius: 5px;
            cursor: pointer; /* Indicate that the product item is clickable */
        }

        .product-item.highlighted {
            background-color: lightblue;
        }
    </style>
</head>
<body>
<div class="d-flex align-items-center mb-3">
    <a id="toggle-link" href="admin_menu.php?page=POS" onclick="toggleIcon(event)">
        <img id="toggle-icon" src="icons/expand.ico" alt="Expand">
    </a>
</div>

<div class="container-fluid mt-4">
    <div class="row">
        <div class="col-md-7" style="margin-left: 140px;">
            <div>
                <input type="text" class="form-control" id="searchBar" placeholder="Search products..." onkeyup="searchProducts()" />
            </div>
            <div id="searchResults"></div>
            <hr>
            <div class="d-flex align-items-center">
                <input type="text" class="form-control" id="barcodeInput" placeholder="Enter Barcode">
                <button class="btn-add" onclick="addProductByBarcode()">
                    <img src="icons/plus.ico" alt="Add" style="width:16px; height:16px;"> Add
                </button>
            </div>
        </div>
    </div>
</div>

<div class="container-fluid mt-4">
    <div class="row">
        <div class="col-md-7" style="margin-left: 20px;">
            <div class="product-container">
                <!-- Product items will be placed here -->
            </div>
        </div>
        <div class="col-md-4" style="margin-left: -20px;">
            <div class="summary-container">
             <div class="total-section bg-warning text-center p-3 mb-3">
                    <h4>Total: $<span id="totalAmount">0.00</span></h4>
                    <h4>Change: $<span id="changeAmount">0.00</span></h4> 
                </div>
                <div id="paymentContainer">
                    <label for="paymentInput">Enter Payment Amount</label>
                    <input type="number" class="form-control mb-3" id="paymentInput" placeholder="Enter Payment Amount">
                </div>
                <br>
                <div id="gcashContainer" style="display:none;">
                    <label for="gcashRefCode">Enter GCash Reference Code</label>
                    <input type="text" class="form-control mb-3" id="gcashRefCode" placeholder="GCash Reference Number">
                </div>
                <button class="btn btn-success w-100 mb-3" onclick="checkout()">Checkout</button>
                <label class="gcash-label text-primary" style="cursor:pointer;" onclick="toggleGcash()">Pay via GCash</label>
            </div>
            <div class="mt-4">
                <button class="btn btn-remove" onclick="confirmAndRemoveHighlighted()">
                    <img src="icons/remove.ico" alt="Remove"> REMOVE
                </button>
                <button class="btn btn-clear" onclick="clearCart()">
                    <img src="icons/clear.ico" alt="Clear"> CLEAR
                </button>
            </div>
        </div>
    </div>
</div>

<script>
    let cart = [];

    function toggleIcon(event) {
        event.preventDefault();
        const icon = document.getElementById('toggle-icon');
        const link = document.getElementById('toggle-link');
        // Logic to toggle icon
    }

    function toggleGcash() {
        const gcashContainer = document.getElementById('gcashContainer');
        const paymentContainer = document.getElementById('paymentContainer');
        const gcashLabel = document.querySelector('.gcash-label');
        if (gcashContainer.style.display === 'none') {
            gcashContainer.style.display = 'block';
            paymentContainer.style.display = 'none';
            gcashLabel.innerText = 'Pay with Cash';
        } else {
            gcashContainer.style.display = 'none';
            paymentContainer.style.display = 'block';
            gcashLabel.innerText = 'Pay via GCash';
        }
    }

    function addProductByBarcode() {
        const barcodeInput = document.getElementById('barcodeInput').value;
        if (barcodeInput.trim() === '') {
            alert('Please enter a barcode.');
            return;
        }
        fetch('POS.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: 'barcode=' + barcodeInput
        })
        .then(response => response.json())
        .then(product => {
            if (product) {
                const existingProduct = cart.find(item => item.product_id === product.product_id);
                if (existingProduct) {
                    existingProduct.quantity++;
                } else {
                    cart.push({ ...product, quantity: 1 });
                }
                updateCartDisplay();
                updateTotal();
                document.getElementById('barcodeInput').value = '';
            } else {
                alert('Product not found!');
            }
        });
    }

    let highlightedProductId = null; // Variable to store the highlighted product ID

    function updateCartDisplay() {
        const productContainer = document.querySelector('.product-container');
        productContainer.innerHTML = '';
        cart.forEach(item => {
            const productItem = document.createElement('div');
            productItem.classList.add('product-item');
            productItem.innerHTML = `
                <div style="display: flex; align-items: center;">
                    <button style="font-size: 20px; margin-right: 10px;" onclick="changeQuantity(${item.product_id}, -1)">−</button>
                    <span>${item.quantity}</span>
                    <button style="font-size: 20px; margin-left: 10px;" onclick="changeQuantity(${item.product_id}, 1)">+</button>
                    <span style="margin-left: 15px;">${item.unit} - ${item.product_name} - ${item.brand} (${item.product_code})</span>
                    <span style="margin-left: 5px;">${'.'.repeat(5)}</span>
                    <span>$${(item.selling_price * item.quantity).toFixed(2)}</span>
                </div>
            `;
            // Add click event for highlighting
            productItem.onclick = () => highlightProduct(productItem, item.product_id);
            productContainer.appendChild(productItem);
        });
    }

    function changeQuantity(productId, change) {
        const product = cart.find(item => item.product_id === productId);
        if (product) {
            product.quantity += change;

            if (product.quantity < 0) {
                product.quantity = 0; // Prevent negative quantity
            } else if (product.quantity === 1 && change === -1) {
                // Automatically remove the product if quantity is 1 and minus is clicked
                removeProduct(productId);
                return;
            }

            updateCartDisplay();
            updateTotal();
        }
    }

    function removeProduct(productId) {
        cart = cart.filter(item => item.product_id !== productId);
        highlightedProductId = null; // Clear highlighted product after removal
        updateCartDisplay();
        updateTotal();
    }

    function highlightProduct(productElement, productId) {
        const previouslyHighlighted = document.querySelector('.highlighted');
        if (previouslyHighlighted) {
            previouslyHighlighted.classList.remove('highlighted');
        }
        productElement.classList.add('highlighted');
        highlightedProductId = productId; // Store the highlighted product ID
    }

    function confirmAndRemoveHighlighted() {
        if (highlightedProductId) {
            removeProduct(highlightedProductId); // Remove the highlighted product
        }
    }

    function updateTotal() {
        const totalAmount = cart.reduce((total, item) => total + (item.selling_price * item.quantity), 0);
        document.getElementById('totalAmount').innerText = totalAmount.toFixed(2);
        document.getElementById('changeAmount').innerText = '0.00'; // Reset change amount until checkout
    }

    function clearCart() {
        cart = [];
        updateCartDisplay();
        updateTotal();
        location.reload(); // Refresh the page
    }

    function searchProducts() {
        const query = document.getElementById('searchBar').value.trim();
        if (query.length === 0) {
            document.getElementById('searchResults').innerHTML = '';
            document.getElementById('searchResults').style.display = 'none';
            return;
        }
        const xhr = new XMLHttpRequest();
        xhr.open('GET', 'search.php?query=' + encodeURIComponent(query), true);
        xhr.onload = function() {
            if (xhr.status === 200) {
                document.getElementById('searchResults').style.display = 'block';
                document.getElementById('searchResults').innerHTML = xhr.responseText;
            }
        };
        xhr.send();
    }

    // Checkout function
    function checkout() {
        const paymentAmount = parseFloat(document.getElementById('paymentInput').value);
        const referenceNumber = document.getElementById('gcashRefCode').value; // Assuming GCash reference number is optional

        if (isNaN(paymentAmount) || paymentAmount <= 0) {
            alert('Please enter a valid payment amount.');
            return;
        }

        const totalAmount = cart.reduce((total, item) => total + (item.selling_price * item.quantity), 0);
        const changeAmount = paymentAmount - totalAmount; // Calculate change amount

        if (changeAmount < 0) {
            alert('Payment amount is insufficient!');
            return;
        }

        // Prepare data for checkout
        const checkoutData = {
            payment_method: document.querySelector('input[name="paymentMethod"]:checked').value, // Get selected payment method
            total_amount: totalAmount,
            payment_amount: paymentAmount,
            change_amount: changeAmount,
            reference_number: referenceNumber || null,
            user_id: 1 // Replace with actual user ID from your session or login system
        };

        fetch('checkout.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(checkoutData)
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Checkout successful!');
                clearCart();
                document.getElementById('paymentInput').value = '';
                document.getElementById('gcashRefCode').value = '';
            } else {
                alert(data.message || 'Checkout failed.');
            }
        });
    }
</script>

<!-- <footer>
    <p>&copy; 2024 Calzada Dry Goods. All rights reserved.</p>
</footer> -->
</body>
</html>
