<html>
<head>
    <title>Dashboard</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f8f9fa;
            margin: 0;
            padding: 20px;
        }
        .container {
            max-width: 1200px;
            margin: 0 auto;
        }
        .header {
            font-size: 24px;
            font-weight: bold;
            margin-bottom: 20px;
        }
        .card {
            display: inline-block;
            width: 200px;
            padding: 20px;
            margin: 10px;
            border-radius: 5px;
            color: white;
            position: relative;
        }
        .card i {
            position: absolute;
            top: 10px;
            right: 10px;
            font-size: 24px;
        }
        .card.green {
            background-color: #28a745;
        }
        .card.blue {
            background-color: #17a2b8;
        }
        .card.red {
            background-color: #dc3545;
        }
        .card.yellow {
            background-color: #ffc107;
        }
        .card.teal {
            background-color: #20c997;
        }
        .card.orange {
            background-color: #fd7e14;
        }
        .card h2 {
            margin: 0;
            font-size: 36px;
        }
        .card p {
            margin: 5px 0 0;
        }
        .card a {
            color: white;
            text-decoration: none;
            font-size: 14px;
            display: block;
            margin-top: 10px;
        }
        .table-container {
            display: flex;
            justify-content: space-between;
            margin-top: 20px;
        }
        .table {
            width: 48%;
            background-color: white;
            border-radius: 5px;
            padding: 20px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        .table h3 {
            margin-top: 0;
            font-size: 18px;
            font-weight: bold;
        }
        .table table {
            width: 100%;
            border-collapse: collapse;
        }
        .table table th, .table table td {
            padding: 10px;
            text-align: left;
        }
        .table table th {
            background-color: #f1f1f1;
        }
        .table table tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        .status-paid {
            color: green;
        }
        .status-unpaid {
            color: red;
        }
        .status-critical {
            color: red;
        }
        .card.wide {
            width: 420px;
        }
        .card-container {
            display: flex;
            flex-wrap: wrap;
            justify-content: space-between;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">Dashboard</div>
        <div class="card-container">
            <div class="card blue">
                <h2>8</h2>
                <p>Total Category</p>
                <i class="fas fa-list"></i>
                <a href="#">More Info</a>
            </div>
            <div class="card green">
                <h2>8</h2>
                <p>Total Products</p>
                <i class="fas fa-tags"></i>
                <a href="#">More Info</a>
            </div>
            <div class="card red">
                <h2>1</h2>
                <p>Pending Orders</p>
                <i class="fas fa-hourglass-half"></i>
                <a href="#">More Info</a>
            </div>
            <div class="card green wide">
                <h2>8225.77</h2>
                <p>Total Sales</p>
                <i class="fas fa-chart-line"></i>
                <a href="#">More Info</a>
            </div>
            <div class="card yellow">
                <h2>2</h2>
                <p>Total Members</p>
                <i class="fas fa-users"></i>
                <a href="#">More Info</a>
            </div>
            <div class="card blue">
                <h2>150</h2>
                <p>Sales Report</p>
                <i class="fas fa-chart-bar"></i>
                <a href="#">More Info</a>
            </div>
            <div class="card yellow">
                <h2>3</h2>
                <p>Low Stock Products</p>
                <i class="fas fa-exclamation-triangle"></i>
                <a href="#">More Info</a>
            </div>
            <div class="card red">
                <h2>0</h2>
                <p>Out of Stock Products</p>
                <i class="fas fa-times-circle"></i>
                <a href="#">More Info</a>
            </div>
            <div class="card teal">
                <h2>4</h2>
                <p>Suppliers</p>
                <i class="fas fa-truck"></i>
                <a href="#">More Info</a>
            </div>
           
        </div>
        <div class="table-container">
            <div class="table">
                <h3>Critical Stock Products</h3>
                <table>
                    <tr>
                        <th>Product Name</th>
                        <th>Stock</th>
                        <th>Status</th>
                    </tr>
                    <tr>
                        <td>Product 1</td>
                        <td>5</td>
                        <td class="status-critical">Critical</td>
                    </tr>
                    <tr>
                        <td>Product 2</td>
                        <td>3</td>
                        <td class="status-critical">Critical</td>
                    </tr>
                    <tr>
                        <td>Product 3</td>
                        <td>2</td>
                        <td class="status-critical">Critical</td>
                    </tr>
                </table>
            </div>
            <div class="table">
                <h3>Recent Orders</h3>
                <table>
                    <tr>
                        <th>Order ID</th>
                        <th>Customer</th>
                        <th>Status</th>
                    </tr>
                    <tr>
                        <td>#12345</td>
                        <td>John Doe</td>
                        <td class="status-paid">Paid</td>
                    </tr>
                    <tr>
                        <td>#12346</td>
                        <td>Jane Smith</td>
                        <td class="status-unpaid">Unpaid</td>
                    </tr>
                    <tr>
                        <td>#12347</td>
                        <td>Bob Johnson</td>
                        <td class="status-paid">Paid</td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
</body>
</html>