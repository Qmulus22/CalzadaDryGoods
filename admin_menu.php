<?php
session_start();
require 'config.php';

// Fetch the user's information from the session
$user_id = $_SESSION['user_id'];
$user_type = $_SESSION['user_type'];

// Query to fetch the user's full name
$stmt = $conn->prepare("SELECT full_name FROM Users WHERE user_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$stmt->bind_result($full_name);
$stmt->fetch();
$stmt->close();

// Determine which content to display
$page = isset($_GET['page']) ? $_GET['page'] : 'dashboard'; // Default to 'dashboard' if no page specified
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Calzada Dry Goods Trading - Admin Menu</title>
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

        /* Top Navbar */
        .navbar {
            
            background-color: #bd8b65;
            height: 50px;
            color: black;
            padding: 15px;
            text-align: center;
            position: relative;
        }

        .navbar h1 {
            margin: 0;
            font-size: 24px;
            position: absolute;
        }

        .navbar .icons {
            position: absolute;
            right: 130px;
            top: 15px;
        }

        .navbar .icons a {
            color: black;
            margin-left: 10px;
            text-decoration: none;
        }

        .navbar .icons img {
            width: 30px;
            height: 30px;
        }

        /* Side Panel */
        .side-panel {
            background-color: #e7d4ab;
            color: black;
            width: 260px;
            height: 100vh;
            position: fixed;
            top: 0;
            left: 0;
            padding: 20px;
        }

      

        .side-panel .user-info img {
            width: 220px;
            height: 180px;
            border-radius: 40%;
            margin-bottom: 10px;
        }

        .user-info p {
            font-size: 18px;
            text-align: center;
            font-weight: bold;
            position: relative;
            margin: 5px 0;
        }

        /* Side Panel Links */
        .side-panel a {
            display: block;
            padding: 9px;
            color: black;
            text-decoration: none;
            margin-bottom: 10px;
            position: relative;
            background-color: transparent;
        }

        .side-panel a:hover,
        .side-panel a.active {
            background-color: #cba271;
        }

       
        /* .side-panel a::after {
            content: '▶';
            position: absolute;
            right: 10px;
            font-size: 12px;
            color: black;
            top: 50%;
            transform: translateY(-50%) rotate(90deg);
            transition: transform 0.3s;
        } */
        

        .side-panel a:hover::after,
        .side-panel a.active::after {
            transform: translateY(-50%) rotate(0deg);
        }

        /* Main content */
        .main-content {
            margin-left: 270px;
            padding: 20px;
            background-color: #f9f9f9;
        }

        .settings-dropdown {
            position: relative;
            display: inline-block;
        }

        .dropdown-content {
            display: none; /* Hide the dropdown by default */
            position: absolute;
            background-color: #f9f9f9;
            min-width: 160px;
            box-shadow: 0px 8px 16px 0px rgba(0,0,0,0.2);
            z-index: 1;
        }

        .dropdown-content a {
            color: black;
            padding: 12px 16px;
            text-decoration: none;
            display: block;
        }

        .dropdown-content a:hover {
            background-color: #cba271;
        }
        .system-management, .order-management {
            margin-bottom: 10px;
        }

        .system-management a, .order-management a {
            display: block;
            padding: 10px;
            color: black;
            text-decoration: none;
            background-color: transparent;
        }
        

        .system-links a , .order-links a{
            padding-left: 20px; /* Indent the submenu items */
            display: block; /* Make them block elements */
        }
        .system-links, .order-links  {
            padding-left: 20px; /* Indent submenu */
        }

        .system-links a, .order-links a {
            padding: 10px;
            color: black;
            text-decoration: none;
            background-color: transparent;
        }

    </style>
</head>
<body>
    <!-- Top Navbar -->
    <div class="navbar">
        <div class="user-info">

         <p><?php echo $full_name,"/",$user_type; ?></p>
         
         </div>

        <!--<h1>Calzada Dry Goods Trading</h1>-->
        <div class="icons">
            <a href="#"><img src="icons/notif.png" alt="Notifications" /></a>
            <div class="settings-dropdown">
                <a href="#" onclick="toggleDropdown(event)">
                    <img src="icons/settings.png" alt="Settings" />
                </a>
                <div class="dropdown-content">
                    <a href="view_profile.php">View Profile</a>
                    <a href="logout.php">Log Out</a>
                </div>
            </div>
        </div>
    </div>

    <!-- Side Panel -->
    <div class="side-panel">
        <div class="user-info">
            <img src="icons/calzada.png" alt="User Icon">
        </div>

        <a href="?page=dashboard" class="<?php echo ($page === 'dashboard') ? 'active' : ''; ?>">
        <img src="icons/dashboard.ico" alt="Dashboard Icon" style="width:20px; height:20px; vertical-align:middle; margin-right:5px;">Dashboard</a>
        <a href="?page=sales_reports" class="<?php echo ($page === 'sales_reports') ? 'active' : ''; ?>">
        <img src="icons/salesreport.ico" alt="Sales Report Icon" style="width:20px; height:20px; vertical-align:middle; margin-right:5px;">Sales Reports</a>
        <a href="?page=staff" class="<?php echo ($page === 'staff') ? 'active' : ''; ?>">
        <img src="icons/cashier.ico" alt="cashier Icon" style="width:20px; height:20px; vertical-align:middle; margin-right:5px;">Manage Cashiers</a>

                <!-- Order Management Section -->
        <div class="order-management">
            <a href="javascript:void(0);" onclick="toggleOrderManagement()">
            <img src="icons/shipment.ico" alt="shipment Icon" style="width:20px; height:20px; vertical-align:middle; margin-right:5px;">Order Management</a>
            <div class="order-links" style="display: none;">
                <a href="?page=new_order" class="<?php echo ($page === 'new_order') ? 'active' : ''; ?>">
                <img src="icons/orderlist.ico" alt="orderlist Icon" style="width:20px; height:20px; vertical-align:middle; margin-right:5px;">Make Orders</a>
                <a href="?page=orders" class="<?php echo ($page === 'orders') ? 'active' : ''; ?>">
                <img src="icons/order.ico" alt="manage order Icon" style="width:20px; height:20px; vertical-align:middle; margin-right:5px;">Manage Orders</a>
            </div>
        </div>
        <!-- System Management Section -->
        <div class="system-management">
            <a href="javascript:void(0);" onclick="toggleSystemManagement()">
            <img src="icons/system_management.ico" alt="System Management Icon" style="width:20px; height:20px; vertical-align:middle; margin-right:5px;">System Management</a>
            <div class="system-links" style="display: none;">
                <a href="?page=product_details" class="<?php echo ($page === 'product_details') ? 'active' : ''; ?>">
                <img src="icons/products.ico" alt="product Icon" style="width:20px; height:20px; vertical-align:middle; margin-right:5px;">Products</a>
                <a href="?page=categories" class="<?php echo ($page === 'categories') ? 'active' : ''; ?>">
                <img src="icons/category.ico" alt="category Icon" style="width:20px; height:20px; vertical-align:middle; margin-right:5px;">Categories</a>
                <a href="?page=suppliers" class="<?php echo ($page === 'suppliers') ? 'active' : ''; ?>">
                <img src="icons/supplier.ico" alt="supplier Icon" style="width:20px; height:20px; vertical-align:middle; margin-right:5px;">Suppliers</a>
            </div>
        </div>

        <a href="?page=POS" class="<?php echo ($page === 'POS') ? 'active' : ''; ?>">
        <img src="icons/registrar.ico" alt="Registrar Icon" style="width:20px; height:20px; vertical-align:middle; margin-right:5px;">Registrar</a>
        <a href="?page=inventory_transactions" class="<?php echo ($page === 'inventory_transactions') ? 'active' : ''; ?>">
        <img src="icons/inventory2.ico" alt="Inventory Icon" style="width:20px; height:20px; vertical-align:middle; margin-right:5px;">Inventory Logs</a>
        <a href="?page=replacements" class="<?php echo ($page === 'replacements') ? 'active' : ''; ?>">
        <img src="icons/replacement.ico" alt="replacement Icon" style="width:20px; height:20px; vertical-align:middle; margin-right:5px;">Replacements</a>
    </div>


    <!-- Main Content -->
    <div class="main-content">
        <?php
        // Include the relevant page content based on the URL parameter
        $allowed_pages = ['dashboard', 'staff', 'product_details', 'categories', 'sales_reports', 'suppliers','POS', 'inventory_transactions', 'replacements', 'SupplierDetails','orders','new_order'];

        if (in_array($page, $allowed_pages)) {
            include $page . '.php'; // Make sure these files exist and are secure
        } else {
            echo "<h2>Page not found</h2>";
        }
        ?>
    </div>

    <script>
        function toggleOrderManagement() {
            const links = document.querySelector('.order-links');
            const isOpen = links.style.display === 'block';

            // Toggle display
            links.style.display = isOpen ? 'none' : 'block';

            // Save state in localStorage
            localStorage.setItem('orderManagementOpen', !isOpen);
        }

        function toggleSystemManagement() {
            const links = document.querySelector('.system-links');
            const isOpen = links.style.display === 'block';

            // Toggle display
            links.style.display = isOpen ? 'none' : 'block';

            // Save state in localStorage
            localStorage.setItem('systemManagementOpen', !isOpen);
        }

        window.onload = function() {
            const orderLinks = document.querySelector('.order-links');
            const orderIsOpen = localStorage.getItem('orderManagementOpen') === 'true';
            orderLinks.style.display = orderIsOpen ? 'block' : 'none';

            const systemLinks = document.querySelector('.system-links');
            const systemIsOpen = localStorage.getItem('systemManagementOpen') === 'true';
            systemLinks.style.display = systemIsOpen ? 'block' : 'none';
        };

        // Close the dropdowns if clicking outside
        window.onclick = function(event) {
            // Close order management if clicking outside
            if (!event.target.closest('.order-management')) {
                const dropdowns = document.getElementsByClassName("order-links");
                for (let i = 0; i < dropdowns.length; i++) {
                    dropdowns[i].style.display = "none";
                }
            }

            // Close system management if clicking outside
            if (!event.target.closest('.system-management')) {
                const dropdowns = document.getElementsByClassName("system-links");
                for (let i = 0; i < dropdowns.length; i++) {
                    dropdowns[i].style.display = "none";
                }
            }

            // Close settings dropdown if clicking outside
            if (!event.target.closest('.settings-dropdown')) {
                const dropdowns = document.getElementsByClassName("dropdown-content");
                for (let i = 0; i < dropdowns.length; i++) {
                    dropdowns[i].style.display = "none";
                }
            }
        }

        function toggleDropdown(event) {
            event.preventDefault(); // Prevent default anchor behavior
            const dropdown = document.querySelector('.dropdown-content');
            dropdown.style.display = dropdown.style.display === 'block' ? 'none' : 'block';
        }

    </script>
</body>
</html>
