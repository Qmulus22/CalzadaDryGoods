<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Orders</title>
    <style>
        /* Basic styling */
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
        }

        .new-order-button {
            background-color: #4CAF50; /* Green */
            padding: 10px 12px; /* Smaller padding */
            border-radius: 15px; /* More subtle rounding */
            color: white; /* Changed text color for better contrast */
            border: none;
            cursor: pointer;
            float: right; /* Align to the right */
            margin-right: 70px; /* Margin from the right */
            font-size: 16px; /* Adjust font size */
        }

        .button-container {
            text-align: center;
            margin-top: 100px; /* Move buttons down */
        }

        .button {
            background: transparent;
            border: none;
            margin: 0 60px; /* Space between buttons */
            cursor: pointer;
            text-align: center;
        }

        .button img {
            width: 40px; /* Size of the icons */
            height: 40px;
        }

        .button-text {
            display: block;
            margin-top: 15px; /* Space between icon and text */
        }

        .table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        table, th, td {
            border: 2px solid;
        }

        .table th, .table td {
            border: 3px solid #ddd;
            padding: 8px;
            text-align: left;
        }

        .table th {
            background-color: beige;
        }

        .action-button {
            padding: 5px 10px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            display: inline-flex; /* Ensure horizontal alignment */
            align-items: center;
            margin-right: 5px; /* Space between action buttons */
        }

        hr {
            margin-right: -130px;
        }

        .view-button {
            background-color: #cccccc;
            color: black;
        }

        .download-button {
            background-color: #79f6fc;
            color: black;
        }

        .container {
            padding: 20px;
        }

        .active-button {
            color: red; /* Color for active status */
            font-weight: bold;
        }

        /* Modal Styles */
        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            padding-top: 50px;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.4); /* Black background with opacity */
        }

        .modal-content {
            background-color: #fefefe;
            margin: auto;
            padding: 20px;
            border: 1px solid #888;
            width: 50%;
            border-radius: 10px;
            position: relative;
        }

        .modal h3 {
            border-bottom: 2px solid #ccc;
            padding-bottom: 10px;
            margin-bottom: 15px;
        }

        .modal-actions {
            margin-top: 20px;
            display: flex;
            justify-content: flex-end;
            gap: 10px;
        }

        .btn.green {
            background-color: #4CAF50;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
        }

        .btn.red {
            background-color: #f44336;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
        }

        .close {
            position: absolute;
            top: 10px;
            right: 20px;
            font-size: 25px;
            font-weight: bold;
            cursor: pointer;
        }

        .close:hover, .close:focus {
            color: red;
            cursor: pointer;
        }
    </style>
</head>
<body>
<div class="container">
    <div>
        <h1>My Orders</h1>
        <button class="new-order-button" onclick="window.location.href='admin_menu.php?page=new_order'">
            <img src="icons/cart.ico" alt="New Order Icon" style="width: 20px; height: 20px;">
            Create New Order
        </button>
    </div>
    <div class="button-container">
        <hr><br>
        <button class="button" onclick="loadOrders('Pending', this)">
            <img src="icons/truck.ico" alt="Pending Icon">
            <span class="button-text">Pending</span>
        </button>
        <button class="button" onclick="loadOrders('Received', this)">
            <img src="icons/complete.ico" alt="Complete Icon">
            <span class="button-text">Complete</span>
        </button>
        <button class="button" onclick="loadOrders('Cancelled', this)">
            <img src="icons/cancel.ico" alt="Cancel Icon">
            <span class="button-text">Cancel</span>
        </button>
    </div>

    <div id="orders-table">
        <!-- Orders table content loaded dynamically -->
    </div>
</div>

<!-- Modal Structure -->
<div id="orderModal" class="modal">
    <div class="modal-content">
        <span class="close" onclick="closeModal()">&times;</span> <!-- Close button (X) -->
        
        <!-- Order Details Section -->
        <h3>Order Details</h3>
        <p><strong>Order ID:</strong> <span id="orderId"></span></p>
        <p><strong>Order Date:</strong> <span id="orderDate"></span></p>
        <p><strong>Status:</strong> <span id="orderStatus"></span></p>
        
        <!-- Supplier Details Section -->
        <h3>Supplier Details</h3>
        <p><strong>Supplier Name:</strong> <span id="supplierName"></span></p>
        <p><strong>Contact Person:</strong> <span id="contactPerson"></span></p>
        <p><strong>Contact Number:</strong> <span id="contactNumber"></span></p>
        
        <!-- Product List Section -->
        <h3>Products</h3>
        <ul id="productList"></ul>
        
        <!-- Action Buttons (Visible only if status is "Pending") -->
        <div id="actionButtons" class="modal-actions">
            <button id="receiveButton" class="btn green" onclick="receiveOrder()">Receive Order</button>
            <button id="cancelButton" class="btn red" onclick="cancelOrder()">Cancel Order</button>
        </div>
    </div>
</div>

<script>
    function loadOrders(status, button) {
        const xhr = new XMLHttpRequest();
        xhr.open('GET', 'fetch_orders.php?status=' + status, true);
        xhr.onload = function() {
            if (this.status == 200) {
                document.getElementById('orders-table').innerHTML = this.responseText;
                resetButtonColors(); // Reset all button colors
                button.querySelector('.button-text').classList.add('active-button'); // Highlight active button
            } else {
                document.getElementById('orders-table').innerHTML = 'Error loading orders.';
            }
        }
        xhr.send();
    }

    function resetButtonColors() {
        const buttons = document.querySelectorAll('.button-text');
        buttons.forEach(button => {
            button.classList.remove('active-button');
        });
    }

    // Load pending orders by default on page load
    window.onload = function() {
        const pendingButton = document.querySelector('.button-container button:first-child'); // Select the first button (Pending)
        loadOrders('Pending', pendingButton); // Load pending orders by default
    }

    function viewOrder(orderId) {
        // Send an AJAX request to fetch order details
        fetch('get_order_details.php?order_id=' + orderId)
            .then(response => response.json())
            .then(data => {
                if (data.error) {
                    alert(data.error);
                    return;
                }

                // Insert order details into the modal
                document.getElementById('orderId').textContent = data.order_id;
                document.getElementById('orderDate').textContent = data.order_date;
                document.getElementById('orderStatus').textContent = data.status;
                document.getElementById('supplierName').textContent = data.supplier_name;
                document.getElementById('contactPerson').textContent = data.contact_person;
                document.getElementById('contactNumber').textContent = data.contact_number;

                // Display the product list
                const productList = document.getElementById('productList');
                productList.innerHTML = ''; // Clear existing list
                data.products.forEach(product => {
                    const listItem = document.createElement('li');
                    listItem.textContent = `${product.product_name} (Quantity: ${product.requested_quantity})`;
                    productList.appendChild(listItem);
                });

                // Show or hide the "Receive" and "Cancel" buttons based on the order status
                if (data.status.toLowerCase() === 'pending') {
                    document.getElementById('receiveButton').style.display = 'inline-block';
                    document.getElementById('cancelButton').style.display = 'inline-block';
                } else {
                    document.getElementById('receiveButton').style.display = 'none';
                    document.getElementById('cancelButton').style.display = 'none';
                }

                // Open the modal
                document.getElementById('orderModal').style.display = 'block';
            })
            .catch(error => {
                console.error('Error fetching order details:', error);
            });
    }


    function receiveOrder() {
        const orderId = document.getElementById('orderId').textContent;

        if (confirm('Are you sure you want to mark this order as received?')) {
            fetch('mark_as_received.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded'
                },
                body: 'order_id=' + orderId
            })
            .then(response => response.text())
            .then(result => {
                alert(result);
                closeModal();
            })
            .catch(error => {
                console.error('Error:', error);
            });
        }
    }
    function cancelOrder() {
        const orderId = document.getElementById('orderId').textContent;

        if (confirm('Are you sure you want to cancel this order?')) {
            fetch('cancel_order.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded'
                },
                body: 'order_id=' + orderId
            })
            .then(response => response.text())
            .then(result => {
                alert(result);
                closeModal();
            })
            .catch(error => {
                console.error('Error:', error);
            });
        }
    }


    function closeModal() {
        document.getElementById('orderModal').style.display = 'none';
    }

    // Close the modal when the user clicks outside of it
    window.onclick = function(event) {
        if (event.target == document.getElementById('orderModal')) {
            closeModal();
        }
    }

    function downloadOrder(orderId) {
        if (confirm("Are you sure you want to download this order as a PDF?")) {
            window.location.href = 'generateNewOrderPdf.php?orderId=' + orderId;
        }
    }
</script>
</body>
</html>
