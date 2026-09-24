<?php
// Ensure session is started only once
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require 'config.php';

// Fetch all active employee information
$stmt = $conn->prepare("SELECT * FROM Users WHERE user_type = 'cashier' AND status = 'active'");
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Staff Management - Calzada Dry Goods Trading</title>
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
        #staff-table, #staff-table th, #staff-table td {
         border: 2px solid black; 
        }

        #staff-table th:nth-child(5), 
        #staff-table td:nth-child(5) {
            width: 100px; /* Set a maximum width */
            white-space: nowrap; /* Prevent wrapping */
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

        .btn-view-logs {
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

        .modal-content input[type="text"],
        .modal-content input[type="password"] {
            width: 100%;
            padding: 8px;
            margin-bottom: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
            /* Disable autocomplete */
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

        .popup-content h3 {
            margin-bottom: 20px;
        }

        .popup-content table {
            width: 100%;
            border-collapse: collapse;
        }

        .popup-content th, .popup-content td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }

        .popup-content th {
            background-color: #f2f2f2;
        }

        .popup-content .close {
            background-color: #f44336; /* Red */
            color: white;
            border: none;
            padding: 10px;
            cursor: pointer;
            margin-top: 10px;
        }
    </style>
</head>
<body>
<div class="container">
    <h1>Staff Management</h1>
    <div class="add-button-container">
        <button class="btn btn-add" onclick="openModal()">
            <img src="icons/add_staff.ico" alt="Add" style="width:16px; height:16px;"> <!-- Add Icon -->Add Cashier
        </button>
    </div>
    <br><br>
    <table id="staff-table">
        <thead>
            <tr>
                <th>Name</th>
                <th>Contact</th>
                <th>Username</th>
                <th>Password</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = $result->fetch_assoc()): ?>
            <tr id="employee-<?php echo $row['user_id']; ?>">
                <td><?php echo htmlspecialchars($row['full_name']); ?></td>
                <td><?php echo htmlspecialchars($row['contact']); ?></td>
                <td><?php echo htmlspecialchars($row['username']); ?></td>
                <td><?php echo substr(htmlspecialchars($row['password']), 0, 1) . str_repeat('.', strlen(htmlspecialchars($row['password'])) - 2) . substr(htmlspecialchars($row['password']), -1); ?></td>
                <td>
                    <button class="btn btn-view-logs" onclick="viewLogs(<?php echo $row['user_id']; ?>)">
                        <img src="icons/view.ico" alt="View Logs" style="width:16px; height:16px;"> <!-- View Logs Icon -->
                    </button>
                    <button class="btn btn-update" onclick="updateEmployee(<?php echo $row['user_id']; ?>)">
                        <img src="icons/edit.ico" alt="Update" style="width:16px; height:16px;"> <!-- Update Icon -->
                    </button>
                    <button class="btn btn-archive" onclick="openArchiveModal(<?php echo $row['user_id']; ?>)">
                        <img src="icons/trash.ico" alt="Archive" style="width:16px; height:16px;"> <!-- Archive Icon -->
                    </button>
                </td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>

    <!-- Add Employee Modal -->
    <div id="modal" class="modal">
        <div class="modal-content">
            <h3>Add New Employee</h3>
            <form id="add-employee-form" action="add_employee.php" method="POST">
                <label for="name">Name:</label>
                <input type="text" id="name" name="name" autocomplete="off" required>
                
                <label for="contact">Contact:</label>
                <input type="text" id="contact" name="contact" autocomplete="off" required>
                
                <label for="username">Username:</label>
                <input type="text" id="username" name="username" autocomplete="off" required>
                
                <label for="password">Password:</label>
                <input type="password" id="password" name="password" autocomplete="off" required>
                
                <input type="hidden" name="user_type" value="cashier">

                <button type="submit">Add Employee</button>
                <button type="button" class="cancel" onclick="closeModal()">Cancel</button>
            </form>
            <div id="error-message" class="error-message"></div>
            <div id="success-message" class="success-message"></div>
        </div>
    </div>
    <!-- Update Employee Modal -->
    <div id="update-modal" class="modal">
        <div class="modal-content">
            <h3>Update Employee</h3>
            <form id="update-employee-form" action="update_employee.php" method="POST">
                <input type="hidden" id="update-user-id" name="user_id">

                <label for="update-name">Name:</label>
                <input type="text" id="update-name" name="full_name" autocomplete="off" required>

                <label for="update-contact">Contact:</label>
                <input type="text" id="update-contact" name="contact" autocomplete="off" required>

                <label for="update-username">Username:</label>
                <input type="text" id="update-username" name="username" autocomplete="off" required>

                <label for="update-password">Password:</label>
                <input type="password" id="update-password" name="password" autocomplete="off" required>

                <button type="submit">Update</button>
                <button type="button" class="cancel" onclick="closeUpdateModal()">Cancel</button>
            </form>
            <div id="update-error-message" class="error-message"></div>
            <div id="update-success-message" class="success-message"></div>
        </div>
    </div>

    <!-- Archive Employee Modal -->
    <div id="archive-modal" class="modal">
        <div class="modal-content">
            <h3>Archive Employee</h3>
            <p>Are you sure you want to archive this employee?</p>
            <button id="confirm-archive" class="btn btn-archive" onclick="confirmArchive()">Confirm</button>
            <button class="cancel" onclick="closeArchiveModal()">Cancel</button>
        </div>
    </div>
            <!-- View Logs Popup -->
        <div id="logs-popup" class="popup">
            <div class="popup-content">
                <h3>Employee Login Logs</h3>
                <div id="logs-table-container"></div>
                <button class="close" onclick="closeLogsPopup()">Close</button>
            </div>
        </div>
        

        <script>

            let employeeIdToUpdate = null;

            function updateEmployee(id) {
                employeeIdToUpdate = id;
                fetch('get_employee_details.php?user_id=' + id)
                    .then(response => response.json())
                    .then(data => {
                        if (data.error) {
                            alert(data.error);
                            return;
                        }
                        document.getElementById('update-user-id').value = data.user_id;
                        document.getElementById('update-name').value = data.full_name;
                        document.getElementById('update-contact').value = data.contact;
                        document.getElementById('update-username').value = data.username;
                        document.getElementById('update-password').value = data.password; // Populate current password
                        document.getElementById('update-modal').style.display = 'flex';
                    });
            }

            function closeUpdateModal() {
                document.getElementById('update-modal').style.display = 'none';
            }

            document.getElementById('update-employee-form').addEventListener('submit', function(e) {
                e.preventDefault();
                let formData = new FormData(this);

                fetch('update_employee.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        document.getElementById('update-success-message').textContent = 'Employee updated successfully!';
                        setTimeout(() => {
                            document.getElementById('update-success-message').textContent = '';
                            closeUpdateModal();
                            location.reload(); // Refresh the page to show updated information
                        }, 2000);
                    } else {
                        document.getElementById('update-error-message').textContent = data.error || 'An error occurred.';
                    }
                });
            });
            let employeeIdToArchive = null;

            function openModal() {
                document.getElementById('modal').style.display = 'flex';
            }

            function closeModal() {
                document.getElementById('modal').style.display = 'none';
            }

            function openArchiveModal(id) {
                employeeIdToArchive = id;
                document.getElementById('archive-modal').style.display = 'flex';
            }

            function closeArchiveModal() {
                document.getElementById('archive-modal').style.display = 'none';
            }

            function confirmArchive() {
                if (employeeIdToArchive) {
                    fetch('archive_employee.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded',
                        },
                        body: `user_id=${employeeIdToArchive}`
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            document.getElementById(`employee-${employeeIdToArchive}`).remove();
                            closeArchiveModal();
                        } else {
                            alert('Failed to archive employee.');
                        }
                    });
                }
            }

            function viewLogs(id) {
                fetch('fetch_employee_logs.php?user_id=' + id)
                    .then(response => response.json())
                    .then(data => {
                        let tableHtml = '<table><thead><tr><th>Login Time</th><th>Logout Time</th></tr></thead><tbody>';
                        data.logs.forEach(log => {
                            tableHtml += `<tr><td>${log.login_time}</td><td>${log.logout_time || 'N/A'}</td></tr>`;
                        });
                        tableHtml += '</tbody></table>';
                        document.getElementById('logs-table-container').innerHTML = tableHtml;
                        document.getElementById('logs-popup').style.display = 'flex';
                    });
            }

            function closeLogsPopup() {
                document.getElementById('logs-popup').style.display = 'none';
            }
            

            // Display success message after adding employee
            document.getElementById('add-employee-form').addEventListener('submit', function(e) {
                e.preventDefault();
                let formData = new FormData(this);

                fetch('add_employee.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        document.getElementById('success-message').textContent = 'Employee added successfully!';
                        setTimeout(() => {
                            document.getElementById('success-message').textContent = '';
                            closeModal();
                            location.reload(); // Refresh the page to show the newly added employee
                        }, 2000);
                    } else {
                        document.getElementById('error-message').textContent = data.error || 'An error occurred.';
                    }
                });
            });
            
        </script>
    </body>
</html>
