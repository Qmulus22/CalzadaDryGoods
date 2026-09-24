<?php
    // Start session if not already started
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    // Include database configuration
    require 'config.php';

    // Fetch all active products (only those that are not archived)
    $stmt = $conn->prepare("SELECT p.*, c.category_name FROM Products p LEFT JOIN Categories c ON p.category_id = c.category_id WHERE p.status = 'active'");
    $stmt->execute();
    $result = $stmt->get_result();

    // Fetch all categories for the dropdown
    $categoryStmt = $conn->prepare("SELECT category_id, category_name FROM Categories");
    $categoryStmt->execute();
    $categoryResult = $categoryStmt->get_result();
    ?>

    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <style>
                /* Basic reset */
                * {
                margin: 0;
                padding: 0;
                box-sizing: border-box;
            }
            /* Adjust container width and move table to the left */
            .container {
                width: 85%;
                margin: 20px auto;
            }

            h1 {
                text-align: center;
                margin-bottom: 20px;
            }

            /* Buttons */
            .btn {
                display: inline-block;
                min-width: 100px; /* Ensure buttons have a minimum width */
                background-color: orange;
                color: white;
                border: none;
                padding: 8px 12px;
                cursor: pointer;
                border-radius: 20px;
            }
            

            .btn-update {
                background-color: #FFA500; /* Orange */
            }

            .btn-archive {
                background-color: #f44336; /* Red */
            }

            .btn-add {
                background-color: #4CAF50; /* Green */
                padding: 15px 18px;
                border-radius: 30px;
                color: black;
            }

            .btn-view {
                background-color: #9E9E9E; /* Gray */
                color: white;
            }

            /* Add Button Container */
            .add-button-container {
                text-align: right; /* Align the add button to the right */
                margin-top: 20px; /* Add space above the button */
            }

            .add-button-container button {
                display: inline-block;
            }
            /* Table Styles */
            table {
                width: calc(100% - 30px); /* Adjust table width to be 30px away from the sidebar */
                margin-left: 30px;
                border-collapse: collapse;
            }

            #products-table, #products-table th, #products-table td {
            border: 2px solid black; 
            }

            #products-table th:nth-child(5), 
            #products-table td:nth-child(5) {
                width: 150px; /* Set a maximum width */ 
                white-space: nowrap; /* Prevent wrapping */
            }
            
            #products-table td {
            padding: 5px; /* Adjust padding as needed */
            }

            th, td {
                border: 1px solid #ddd;
                padding: 8px;
                text-align: left;
            }

            th {
                background-color: #f2f2f2;
            }

            .error-message {
                color: red;
            }

            .success-message {
                color: green;
            }
            .btn-confirm-archive {
                background-color: #d9534f; /* Red color */
                color: white;
            }

            .btn-cancel {
                background-color: #6c757d; /* Gray color */
                color: white;
            }
            .modal {
                display: none; /* Hidden by default */
                position: fixed; 
                z-index: 1; 
                left: 0;
                top: 0;
                width: 100%;
                height: 100%;
                overflow: auto; 
                background-color: rgb(0,0,0); 
                background-color: rgba(0,0,0,0.4); /* Black w/ opacity */
                justify-content: center;
                align-items: center;
            }

            .modal-content {
                background-color: white;
                padding: 20px;
                border-radius: 5px;
                width: 400px; /* You can adjust this width as needed */
                max-width: 90%; /* Ensure it doesn't exceed viewport width */
                text-align: left; /* Align text to the left */
                display: flex;
                flex-direction: column; /* Stack items vertically */
            }
            .modal-header {
                display: flex; /* Use flexbox for the header */
                justify-content: space-between; /* Space between elements */
                align-items: center; /* Center items vertically */
            }
            .modal-content p {
                margin: 15px 0; /* Add spacing between paragraphs */
            }

            .close {
                color: #aaa;
                font-size: 28px;
                font-weight: bold;
                cursor: pointer; /* Always good to have cursor pointer */
            }

            .close:hover,
            .close:focus {
                color: black;
                text-decoration: none;
            }

            .modal {
        display: none; /* Hidden by default */
        position: fixed;
        z-index: 1;
        left: 0;
        top: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(0,0,0,0.4); /* Black with opacity */
        justify-content: center;
        align-items: center;
    }

    .modal-content {
        background-color: white;
        padding: 20px;
        border-radius: 5px;
        width: 600px; /* Rectangular shape */
        max-width: 90%;
        box-shadow: 0px 0px 10px 0px #000;
    }

    .modal-content h2 {
        text-align: center;
    }

    .modal-content input,
    .modal-content select {
        width: 100%;
        padding: 10px;
        margin: 8px 0;
        border-radius: 4px;
        border: 1px solid #ccc;
    }

    .modal-content button {
        padding: 10px;
        border: none;
        border-radius: 5px;
        background-color: #4CAF50;
        color: white;
        cursor: pointer;
    }

    .modal-content button:hover {
        background-color: #45a049;
    }

    .close {
        color: #aaa;
        float: right;
        font-size: 28px;
        font-weight: bold;
    }

    .close:hover,
    .close:focus {
        color: black;
        cursor: pointer;
    }


            


        </style>
    </head>
    <body>
        <div class="container">
        <h1 style="text-align: left; margin-left: -100px;">Product Details</h1>
            <div class="add-button-container">
                <button class="btn btn-add" onclick="openAddModal()">
                    <img src="icons/add_staff.ico" alt="Add" style="width:16px; height:16px;"> <!-- Add Icon -->Add New Product
                </button>
            </div>
            <br><br>
            <table id="products-table">
                <thead>
                    <tr>
                        <th>Product Name</th>
                        <th>Product Code</th>
                        <th>Barcode</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $result->fetch_assoc()): ?>
                    <tr id="product-<?php echo $row['product_id']; ?>">
                        <td><?php echo htmlspecialchars($row['product_name']); ?></td>
                        <td><?php echo htmlspecialchars($row['product_code']); ?></td>
                        <td><?php echo htmlspecialchars($row['barcode']); ?></td>
                        <td>
                            <button class="btn btn-view" onclick="viewProductDetails(<?php echo $row['product_id']; ?>)">
                                <img src="icons/view.ico" alt="View Product Details" style="width:16px; height:16px;"> <!-- View Product Details Icon -->
                            </button>
                            <button class="btn btn-edit" onclick="getProductDetails(<?php echo $row['product_id']; ?>)">
                                <img src="icons/edit.ico" alt="Edit" style="width:16px; height:16px;"> <!-- Edit Icon -->
                            </button>
                            <button class="btn btn-archive" onclick="archiveProduct(<?php echo $row['product_id']; ?>)">
                                <img src="icons/trash.ico" alt="Archive" style="width:16px; height:16px;"> <!-- Archive Icon -->
                            </button>
                        </td>

                    </tr>
                    <?php endwhile; ?>
            </tbody>
            </table>
        </div>
   


        <!-- Modal for Viewing Product Details -->
        <div class="modal" id="viewProductModal">
        <div class="modal-content">
            <span class="close" onclick="closeModal()">&times;</span><br>
            <h2>Product Details</h2>
            <br>
            <br>
            <form id="productDetailsForm">
                <input type="hidden" id="view_product_id" name="product_id">
                <p><strong>Product Name:</strong> <span id="product_name"></span></p>
                <p><strong>Category:</strong> <span id="category_name"></span></p>
                <p><strong>Category Description:</strong> <span id="category_description"></span></p>
                <p><strong>Product Code:</strong> <span id="product_code"></span></p>
                <p><strong>Barcode:</strong> <span id="barcode"></span></p>
                <p><strong>Units:</strong> <span id="unit"></span></p>
                <p><strong>Base Price: ₱</strong> <span id="base_price"></span></p>
                <p><strong>Selling Price: ₱</strong> <span id="selling_price"></span></p>
                <p><strong>Stock Quantity:</strong> <span id="stock_quantity"></span></p>
                <p><strong>Date Added:</strong> <span id="date_added"></span></p>
                <p><strong>Last Updated:</strong> <span id="last_updated"></span></p>
            </form>
        </div>
    </div>
  


    <!-- Add New Product Modal -->
    <div class="modal" id="addProductModal">
        <div class="modal-content">
            <span class="close" onclick="closeAddModal()">&times;</span><br>
            <h2>Add New Product</h2>
            <br>
            <div id="duplicate_error" class="error-message"></div>

            <form id="addProductForm" method="POST" action="add_product.php">
                <!-- Product Name -->
                <label for="product_name">Product Name:</label>
                <input type="text" id="product_name" name="product_name" required><br>

                <!-- Product Code -->
                <label for="product_code">Product Code:</label>
                <input type="text" id="product_code" name="product_code" required><br>

                <!-- Barcode -->
                <label for="barcode">Barcode:</label>
                <input type="text" id="barcode" name="barcode"><br>

                <!-- Category Dropdown with Other Option -->
                <label for="category">Category:</label>
                <select id="category" name="category_id" required>
                    <option value="">Select Category</option>
                    <?php while ($row = $categoryResult->fetch_assoc()): ?>
                        <option value="<?php echo $row['category_id']; ?>"><?php echo htmlspecialchars($row['category_name']); ?></option>
                    <?php endwhile; ?>
                    <option value="other">Other</option>
                </select><br>

                <!-- Hidden Inputs for Custom Category -->
                <div id="customCategoryFields" style="display:none;">
                    <label for="new_category_name">New Category Name:</label>
                    <input type="text" id="new_category_name" name="new_category_name"><br>
                    
                    <label for="new_category_description">Category Description:</label>
                    <input type="text" id="new_category_description" name="new_category_description"><br>
                </div>

                <!-- Unit Input with Suggestions -->
                <label for="unit">Unit:</label>
                <input type="text" id="unit" name="unit" list="unitSuggestions" required><br>
                <datalist id="unitSuggestions">
                    <option value="Box">
                    <option value="Piece">
                    <option value="Pack">
                    <option value="Dozen">
                    <option value="Gram">
                    <option value="Kilogram">
                </datalist>

                <!-- Base Price -->
                <label for="base_price">Base Price:</label>
                <input type="number" id="base_price" name="base_price" step="0.01" required><br>

                <!-- Selling Price -->
                <label for="selling_price">Selling Price:</label>
                <input type="number" id="selling_price" name="selling_price" step="0.01" required><br>

                <!-- Stock Quantity -->
                <label for="stock_quantity">Stock Quantity:</label>
                <input type="number" id="stock_quantity" name="stock_quantity" required><br>
                <div id="duplicate_error" class="error-message"></div><br><br>

                <button type="submit">Add Product</button>
            </form>
        </div>
    </div>


  
    
        <!-- Update Product Modal -->
    <div class="modal" id="updateProductModal">
        <div class="modal-content">
            <span class="close" onclick="closeUpdateModal()">&times;</span><br>
            <h2>Update Product</h2>
            <form id="updateProductForm">
                <input type="hidden" id="update_product_id" name="product_id">

                <!-- Product Name -->
                <label for="update_product_name">Product Name:</label>
                <input type="text" id="update_product_name" name="product_name" required><br>

                <!-- Product Code -->
                <label for="update_product_code">Product Code:</label>
                <input type="text" id="update_product_code" name="product_code" required><br>

                <!-- Barcode -->
                <label for="update_barcode">Barcode:</label>
                <input type="text" id="update_barcode" name="barcode"><br>

                <!-- Category Dropdown -->
                <label for="update_category">Category:</label>
                <select id="update_category" name="category_id" required>
                    <option value="">Select Category</option>
                    <?php while ($row = $categoryResult->fetch_assoc()): ?>
                        <option value="<?php echo $row['category_id']; ?>"><?php echo htmlspecialchars($row['category_name']); ?></option>
                    <?php endwhile; ?>
                </select><br>

                <!-- Unit Input with Suggestions -->
                <label for="update_unit">Unit:</label>
                <input type="text" id="update_unit" name="unit" list="unitSuggestions" required><br>
                <datalist id="unitSuggestions">
                    <option value="Box">
                    <option value="Piece">
                    <option value="Pack">
                    <option value="Dozen">
                    <option value="Gram">
                    <option value="Kilogram">
                </datalist>

                <!-- Base Price -->
                <label for="update_base_price">Base Price:</label>
                <input type="number" id="update_base_price" name="base_price" step="0.01" required><br>

                <!-- Selling Price -->
                <label for="update_selling_price">Selling Price:</label>
                <input type="number" id="update_selling_price" name="selling_price" step="0.01" required><br>

                <!-- Stock Quantity -->
                <label for="update_stock_quantity">Stock Quantity:</label>
                <input type="number" id="update_stock_quantity" name="stock_quantity" required><br>

                <button type="submit">Update Product</button>
            </form>
        </div>
    </div>

    



        <script>
            ///////////////////////////////////////////////////////////////////////////////////
            function viewProductDetails(productId) {
                fetch(`get_product_details.php?id=${productId}`)
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            // Populate the form fields
                            document.getElementById('view_product_id').value = data.product.product_id;
                            document.getElementById('product_name').innerText = data.product.product_name;
                            document.getElementById('category_name').innerText = data.product.category_name;
                            document.getElementById('category_description').innerText = data.product.category_description;
                            document.getElementById('product_code').innerText = data.product.product_code;
                            document.getElementById('barcode').innerText = data.product.barcode;
                            document.getElementById('unit').innerText = data.product.unit;
                            document.getElementById('base_price').innerText = data.product.base_price;
                            document.getElementById('selling_price').innerText = data.product.selling_price;
                            document.getElementById('stock_quantity').innerText = data.product.stock_quantity;
                            document.getElementById('date_added').innerText = data.product.date_added;
                            document.getElementById('last_updated').innerText = data.product.last_updated;

                            // Show the modal
                            document.getElementById('viewProductModal').style.display = 'flex';
                        }
                    })
                    .catch(error => console.error('Error fetching product details:', error));
            }
            function closeModal() {
                document.getElementById('viewProductModal').style.display = 'none';     
            }

            // Close modal when clicking outside of it
            window.onclick = function(event) {
                const modal = document.getElementById('viewProductModal');
                if (event.target === modal) {
                    closeModal();
                }
            };
            ///////////////////////////////////////////////////////////////////////////////////
            function openAddModal() {
                document.getElementById('addProductModal').style.display = 'flex';
            }

            function closeAddModal() {
                document.getElementById('addProductModal').style.display = 'none';
            }

            function validateForm() {
            const productName = document.getElementById('product_name').value.trim();
            const productCode = document.getElementById('product_code').value.trim();
            const category = document.getElementById('category').value;
            const unit = document.getElementById('unit').value.trim();
            const basePrice = document.getElementById('base_price').value;
            const sellingPrice = document.getElementById('selling_price').value;
            const stockQuantity = document.getElementById('stock_quantity').value;

            if (!productName) {
                alert('Product Name is required.');
                return false;
            }
            if (!productCode) {
                alert('Product Code is required.');
                return false;
            }
            if (!category) {
                alert('Please select a Category.');
                return false;
            }
            if (!unit) {
                alert('Unit is required.');
                return false;
            }
            if (!basePrice) {
                alert('Base Price is required.');
                return false;
            }
            if (!sellingPrice) {
                alert('Selling Price is required.');
                return false;
            }
            if (!stockQuantity) {
                alert('Stock Quantity is required.');
                return false;
            }

            // If all checks pass, allow the form to submit
            return true;
        }
        document.getElementById('addProductForm').addEventListener('submit', function(event) {
            event.preventDefault(); // Prevent default form submission

            const formData = new FormData(this);
            
            fetch('add_product.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (!data.success) {
                    document.getElementById('duplicate_error').innerText = data.message; // Show error message
                } else {
                    alert(data.message); // Show success message
                    closeAddModal(); // Close modal
                    location.reload(); // Refresh to see new product
                }
            })
            .catch(error => {
                console.error('Error:', error);
            });
        });

           ///////////////////////////////////////////////////////////////////////////////////
        function getProductDetails(productId) {
            fetch('get_product_details.php?id=' + productId)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        openUpdateProductModal(data.product);
                    } else {
                        alert(data.message);
                    }
                })
                .catch(error => {
                    console.error('Error fetching product details:', error);
                });
        }

        function openUpdateProductModal(product) {
            // Populate the fields in the update modal with the current product details
            document.getElementById('update_product_id').value = product.product_id;
            document.getElementById('update_product_name').value = product.product_name;
            document.getElementById('update_product_code').value = product.product_code;
            document.getElementById('update_barcode').value = product.barcode;
            document.getElementById('update_category').value = product.category_id;
            document.getElementById('update_unit').value = product.unit;
            document.getElementById('update_base_price').value = product.base_price;
            document.getElementById('update_selling_price').value = product.selling_price;
            document.getElementById('update_stock_quantity').value = product.stock_quantity;

            // Store original stock quantity for validation
            document.getElementById('update_stock_quantity').setAttribute('data-original', product.stock_quantity);

            // Show the update modal
            document.getElementById('updateProductModal').style.display = 'flex';
        }

        function closeUpdateModal() {
            document.getElementById('updateProductModal').style.display = 'none';
        }

        // Event listener for form submission
        document.getElementById('updateProductForm').addEventListener('submit', function(event) {
            event.preventDefault(); // Prevent default form submission

            const originalStock = parseInt(document.getElementById('update_stock_quantity').getAttribute('data-original'));
            const newStock = parseInt(document.getElementById('update_stock_quantity').value);
            const productCode = document.getElementById('update_product_code').value;
            const barcode = document.getElementById('update_barcode').value;

            // Check if new stock quantity is greater than the original
            if (newStock <= originalStock) {
                alert('Stock Quantity must be greater than the original quantity.');
                return;
            }

            // Check for duplicates
            fetch('check_duplicates.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({ product_code: productCode, barcode: barcode, product_id: document.getElementById('update_product_id').value })
            })
            .then(response => response.json())
            .then(data => {
                if (!data.isUnique) {
                    alert(data.message); // Show error message if duplicates found
                    return;
                }

                const formData = new FormData(this);
                
                fetch('update_product.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (!data.success) {
                        alert(data.message); // Show error message
                    } else {
                        alert(data.message); // Show success message
                        closeUpdateModal(); // Close modal
                        location.reload(); // Refresh to see updated product
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                });
            })
            .catch(error => {
                console.error('Error checking duplicates:', error);
            });
        });

//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

        function archiveProduct(productId) {
            if (confirm('Are you sure you want to archive this product?')) {
                const formData = new FormData();
                formData.append('product_id', productId);

                fetch('archive_product.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (!data.success) {
                        alert(data.message);
                    } else {
                        alert(data.message);
                        location.reload(); // Refresh to see updated product list
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                });
            }
        }

        </script>

    </body>
    </html>
