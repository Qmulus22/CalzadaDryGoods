<?php
// Ensure session is started only once
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require 'config.php';

// Fetch all active supplier information
$stmt = $conn->prepare("SELECT * FROM Suppliers WHERE status = 'active'");
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Supplier Management - Calzada Dry Goods Trading</title>
    <link rel="stylesheet" href="styles.css"> <!-- Link to your CSS file -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

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
            width: calc(100% - 30px); /* Adjust table width */
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
            display: inline-block;
            min-width: 100px;
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
            autocomplete: off;
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

        /* Success message */
        .success-message {
            color: green;
            margin-top: 10px;
        }

        /* View Logs Popup Styles */
        .popup {
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

        .popup-content {
            background: white;
            padding: 20px;
            border-radius: 5px;
            width: 600px;
            max-height: 80%;
            overflow-y: auto; /* Add scroll if content overflows */
        }
    </style>
    </style>
   

    </style>
</head>
<body>
<div class="container">
    <h1>Supplier Management</h1>
    <div class="add-button-container">
        <button class="btn btn-add" onclick="openAddSupplierModal()">
            <img src="icons/add_staff.ico" alt="Add" style="width:16px; height:16px;"> <!-- Add Icon -->Add Supplier
        </button>
    </div>
    <br><br>
    <table id="supplier-table">
        <thead>
            <tr>
                <th>Supplier Name</th>
                <th>Contact Person</th>
                <th>Contact Number</th>
                <th>Address</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = $result->fetch_assoc()): ?>
            <tr id="supplier-<?php echo $row['supplier_id']; ?>">
                <td><?php echo htmlspecialchars($row['supplier_name']); ?></td>
                <td><?php echo htmlspecialchars($row['contact_person']); ?></td>
                <td><?php echo htmlspecialchars($row['contact_number']); ?></td>
                <td><?php echo htmlspecialchars($row['address']); ?></td>
                <td>
                     <button class="btn btn-view" onclick="window.location.href='admin_menu.php?page=SupplierDetails&supplier_id=<?php echo $row['supplier_id']; ?>'">
                        <img src="icons/view.ico" alt="View" style="width:16px; height:16px;"> <!-- View Icon -->
                    </button>

                    <button class="btn btn-update" onclick="updateSupplier(<?php echo $row['supplier_id']; ?>)">
                        <img src="icons/edit.ico" alt="Update" style="width:16px; height:16px;"> <!-- Update Icon -->
                    </button>
                    <button class="btn btn-archive" onclick="openArchiveModal(<?php echo $row['supplier_id']; ?>)">
                        <img src="icons/trash.ico" alt="Archive" style="width:16px; height:16px;"> <!-- Archive Icon -->
                    </button>
                </td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>

<!-- Add Supplier Modal -->
<div id="add-supplier-modal" class="modal">
    <div class="modal-content">
        <h3>Add Supplier</h3>
        <form id="add-supplier-form">
            <div class="form-group">
                <label for="supplier_name">Supplier Name:</label>
                <input type="text" id="supplier_name" required>
            </div>
            <div class="form-group">
                <label for="contact_person">Contact Person:</label>
                <input type="text" id="contact_person" required>
            </div>
            <div class="form-group">
                <label for="contact_number">Contact Number:</label>
                <input type="text" id="contact_number" required>
            </div>
            <div class="form-group">
                <label for="address">Address:</label>
                <input type="text" id="address" required>
            </div>
            <button type="submit" class="btn btn-add">Add Supplier</button>
            <button type="button" class="cancel" onclick="closeAddSupplierModal()">Cancel</button>
        </form>
        <br><br>
        <div id="success-message" style="display:none; color: green;">Supplier added successfully!</div>
    </div>
</div>



<!-- Update Supplier Modal -->
<div id="update-modal" class="modal" style="display:none;">
    <div class="modal-content">
        <h3>Update Supplier</h3>
        <form id="update-supplier-form">
            <input type="hidden" id="update-supplier-id">
            <div class="form-group">
                <label for="update-supplier-name">Supplier Name:</label>
                <input type="text" id="update-supplier-name" required>
            </div>
            <div class="form-group">
                <label for="update-contact-person">Contact Person:</label>
                <input type="text" id="update-contact-person" required>
            </div>
            <div class="form-group">
                <label for="update-contact-number">Contact Number:</label>
                <input type="text" id="update-contact-number" required>
            </div>
            <div class="form-group">
                <label for="update-address">Address:</label>
                <input type="text" id="update-address" required>
            </div>
            <button type="submit" class="btn btn-update">Update Supplier</button>
            <button type="button" class="cancel" onclick="closeUpdateModal()">Cancel</button>
        </form>
        <br><br>
        <div id="update-success-message" style="display:none; color: green;">Supplier updated successfully!</div>
        <div id="update-error-message" style="display:none; color: red;"></div>
    </div>
</div>




<!-- Archive Supplier Modal -->
<div id="archive-modal" class="modal" style="display: none;">
    <div class="modal-content">
        <h3>Archive Employee</h3>
        <p>Are you sure you want to archive this employee?</p>
        <button id="confirm-archive" class="btn btn-archive" onclick="confirmArchive()">Confirm</button>
        <button class="cancel" onclick="closeArchiveModal()">Cancel</button>
    </div>
</div>

<script>
    let supplierIdToArchive = null;

    function openArchiveModal(id) {
        supplierIdToArchive = id;
        document.getElementById('archive-modal').style.display = 'flex';
    }
    function closeArchiveModal() {
        document.getElementById('archive-modal').style.display = 'none';
        location.reload();
    }

    function confirmArchive() {
        if (supplierIdToArchive) {
            fetch('supp_archive.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `supplier_id=${supplierIdToArchive}`
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    document.getElementById(`supplier-${supplierIdToArchive}`).remove();
                    closeArchiveModal();
                } else {
                    alert('Failed to archive supplier.');
                }
            });
        }
    }
    /////////////////////////////////////////////////////////////////////////////////////////

    function openAddSupplierModal() {
        document.getElementById('add-supplier-modal').style.display = 'flex';
    }

    function closeAddSupplierModal() {
        document.getElementById('add-supplier-modal').style.display = 'none';
        location.reload();
    }

    document.getElementById('add-supplier-form').addEventListener('submit', function(event) {
        event.preventDefault();
        
        const supplierData = {
            supplier_name: document.getElementById('supplier_name').value,
            contact_person: document.getElementById('contact_person').value,
            contact_number: document.getElementById('contact_number').value,
            address: document.getElementById('address').value,
            status: 'active' // Assuming default status is active
        };

        fetch('supp_add.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify(supplierData)
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Show success message
                const successMessage = document.getElementById('success-message');
                successMessage.style.display = 'block';

                // Optionally hide it after a few seconds
                setTimeout(() => {
                    successMessage.style.display = 'none';
                    closeAddSupplierModal();
                    location.reload(); // Refresh the page
                }, 2000);
            } else {
                alert('Failed to add supplier: ' + (data.error || 'Unknown error.'));
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred: ' + error.message);
        });
    });
    ////////////////////////////////////////////////////////////////////////////////////////////
    let supplierIdToUpdate = null;

    function updateSupplier(id) {
        supplierIdToUpdate = id;
        fetch('get_supplier_details.php?supplier_id=' + id)
            .then(response => response.json())
            .then(data => {
                if (data.error) {
                    alert(data.error);
                    return;
                }
                document.getElementById('update-supplier-id').value = data.supplier_id;
                document.getElementById('update-supplier-name').value = data.supplier_name;
                document.getElementById('update-contact-person').value = data.contact_person;
                document.getElementById('update-contact-number').value = data.contact_number;
                document.getElementById('update-address').value = data.address;
                document.getElementById('update-modal').style.display = 'flex'; // Show modal
            });
    }

    function closeUpdateModal() {
        document.getElementById('update-modal').style.display = 'none';
        location.reload();
    }

    document.getElementById('update-supplier-form').addEventListener('submit', function(e) {
        e.preventDefault();
        
        const supplierId = document.getElementById('update-supplier-id').value;
        const supplierData = {
            supplier_id: supplierId,
            supplier_name: document.getElementById('update-supplier-name').value,
            contact_person: document.getElementById('update-contact-person').value,
            contact_number: document.getElementById('update-contact-number').value,
            address: document.getElementById('update-address').value,
        };

        fetch('supp_update.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify(supplierData)
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                document.getElementById('update-success-message').style.display = 'block';
                document.getElementById('update-success-message').textContent = 'Supplier updated successfully!';
                setTimeout(() => {
                    closeUpdateModal();
                    location.reload(); // Refresh the page to show updated information
                }, 2000);
            } else {
                document.getElementById('update-error-message').style.display = 'block';
                document.getElementById('update-error-message').textContent = data.message || 'An error occurred.';
            }
        })
        .catch(error => {
            console.error('Error:', error);
            document.getElementById('update-error-message').style.display = 'block';
            document.getElementById('update-error-message').textContent = 'Failed to update supplier.';
        });
    });




</script>

</body>
</html>
