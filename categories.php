<?php
// Ensure session is started only once
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require 'config.php';

// Fetch all categories
$stmt = $conn->prepare("SELECT * FROM Categories");
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Categories Management - Calzada Dry Goods Trading</title>
    <link rel="stylesheet" href="styles.css"> <!-- Link to your CSS file -->
    <style>
        /* Basic reset */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: Arial, sans-serif;
        }

        /* Container */
        .container {
            padding: 20px;
        }

        /* Table Styles */
        table {
            width: calc(100% - 30px); /* Adjust table width to be 30px away from the sidebar */
            margin-left: 30px;
            border-collapse: collapse;
        }

        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }

        th {
            background-color: #f2f2f2;
        }

        /* Buttons */
        .btn {
            padding: 10px 20px; /* Increased padding for better visibility */
            margin: 5px;
            border: none;
            cursor: pointer;
            border-radius: 4px;
            font-size: 16px; /* Larger text for better readability */
            color: white; /* White text for all buttons */
        }

        .btn-update {
            background-color: orange; /* Green */
        }

        .btn-add {
            background-color: #008CBA; /* Blue */
        }

        /* Add Button Container */
        .add-button-container {
            text-align: right; /* Align the add button to the right */
            margin-top: 20px; /* Add space above the button */
        }

        .add-button-container button {
            display: inline-block;
        }

        /* Modal Styles */
        .modal {
            display: none; /* Hidden by default */
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            justify-content: center;
            align-items: center;
        }

        .modal-content {
            background: white;
            padding: 20px;
            border-radius: 5px;
            width: 400px;
        }

        .modal-content h3 {
            margin-bottom: 20px;
        }

        .modal-content label {
            display: block;
            margin-bottom: 10px;
        }

        .modal-content input[type="text"] {
            width: 100%;
            padding: 8px;
            margin-bottom: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
            /* Disable autocomplete */
            /* autocomplete: off; */
        }

        .modal-content button {
            padding: 10px;
            background-color: #4CAF50; /* Green */
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            margin-right: 10px;
        }

        .modal-content button:hover {
            background-color: #45a049;
        }

        .modal-content .cancel {
            background-color: #f44336; /* Red */
        }

        /* Error message */
        .error-message {
            color: red;
            margin-top: 10px;
        }

        /* Success message */
        .success-message {
            color: green;
            margin-top: 10px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Categories Management</h1>
        <div class="add-button-container">
            <button class="btn btn-add" onclick="openAddModal()">Add New Category</button><br><br>
        </div>
        <table id="categories-table">
            <thead>
                <tr>
                    <th>Category Name</th>
                    <th>Description</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result->fetch_assoc()): ?>
                <tr id="category-<?php echo $row['category_id']; ?>">
                    <td><?php echo htmlspecialchars($row['category_name']); ?></td>
                    <td><?php echo htmlspecialchars($row['description']); ?></td>
                    <td>
                        <button class="btn btn-update" onclick="openUpdateModal(<?php echo $row['category_id']; ?>)">
                        <img src="icons/edit.ico" alt="Archive" style="width:16px; height:16px;">
                        Update</button>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>

    <!-- Add Category Modal -->
    <div id="add-modal" class="modal">
        <div class="modal-content">
            <h3>Add New Category</h3>
            <form id="add-category-form" action="add_category.php" method="POST">
                <label for="add-category-name">Category Name:</label>
                <input type="text" id="add-category-name" name="category_name" autocomplete="off" required>
                
                <label for="add-description">Description:</label>
                <input type="text" id="add-description" name="description" autocomplete="off" required>

                <button type="submit">Add Category</button>
                <button type="button" class="cancel" onclick="closeAddModal()">Cancel</button>
            </form>
            <div id="add-error-message" class="error-message"></div>
            <div id="add-success-message" class="success-message"></div>
        </div>
    </div>

    <!-- Update Category Modal -->
    <div id="update-modal" class="modal">
        <div class="modal-content">
            <h3>Update Category</h3>
            <form id="update-category-form" action="update_category.php" method="POST">
                <input type="hidden" id="update-category-id" name="category_id">

                <label for="update-category-name">Category Name:</label>
                <input type="text" id="update-category-name" name="category_name" autocomplete="off" required>
                
                <label for="update-description">Description:</label>
                <input type="text" id="update-description" name="description" autocomplete="off" required>

                <button type="submit">Update Category</button>
                <button type="button" class="cancel" onclick="closeUpdateModal()">
                    Cancel
                </button>
            </form>
            <div id="update-error-message" class="error-message"></div>
            <div id="update-success-message" class="success-message"></div>
        </div>
    </div>

    <script>
        function openAddModal() {
            document.getElementById('add-modal').style.display = 'flex';
        }

        function closeAddModal() {
            document.getElementById('add-modal').style.display = 'none';
        }

        // Add category form submission
        document.getElementById('add-category-form').addEventListener('submit', function(e) {
            e.preventDefault();
            let formData = new FormData(this);

            fetch('add_category.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    document.getElementById('add-success-message').textContent = 'Category added successfully!';
                    setTimeout(() => {
                        document.getElementById('add-success-message').textContent = '';
                        closeAddModal();
                        location.reload(); // Refresh the page to show the newly added category
                    }, 2000);
                } else {
                    document.getElementById('add-error-message').textContent = data.error || 'An error occurred.';
                }
            })
            .catch(error => {
                console.error('Error:', error);
                document.getElementById('add-error-message').textContent = 'An error occurred.';
            });
        });

        let categoryIdToUpdate = null;

        // Open Update Category Modal
        function openUpdateModal(id) {
            categoryIdToUpdate = id;
            fetch('get_category_details.php?category_id=' + id)
                .then(response => response.json())
                .then(data => {
                    if (data.error) {
                        alert(data.error);
                        return;
                    }

                    // Debug: Check if data is being received correctly
                    console.log('Category Data:', data);

                    // Populate the modal fields with the fetched data
                    document.getElementById('update-category-id').value = data.category_id;
                    document.getElementById('update-category-name').value = data.category_name;
                    document.getElementById('update-description').value = data.description;

                    // Show the modal by setting its display to flex
                    document.getElementById('update-modal').style.display = 'flex';
                })
                .catch(error => {
                    console.error('Error fetching category details:', error);
                });
        }


        function closeUpdateModal() {
            document.getElementById('update-modal').style.display = 'none';
        }

        // Update category form submission
        document.getElementById('update-category-form').addEventListener('submit', function(e) {
                    e.preventDefault();
            let formData = new FormData(this);

            fetch('update_category.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    document.getElementById('update-success-message').textContent = data.message;
                    setTimeout(() => {
                        document.getElementById('update-success-message').textContent = '';
                        closeUpdateModal();
                        location.reload(); // Refresh the page to show the updated category
                    }, 2000);
                } else {
                    document.getElementById('update-error-message').textContent = data.message || 'An error occurred.';
                }
            })
            .catch(error => {
                console.error('Error:', error);
                document.getElementById('update-error-message').textContent = 'An error occurred.';
            });
        });
    </script>
</body>
</html>
