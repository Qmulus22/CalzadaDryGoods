<?php
session_start();
require 'config.php'; // Include the database configuration

$loginError = '';

if (!empty($_POST['username']) && !empty($_POST['password'])) {
    // Collect and sanitize user inputs
    $username = htmlspecialchars($_POST['username']);
    $password = htmlspecialchars($_POST['password']);

    // Prepare and execute SQL query to check user credentials
    $stmt = $conn->prepare("SELECT user_id, user_type, password FROM Users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $stmt->store_result();
    $stmt->bind_result($user_id, $user_type, $stored_password);
    
    if ($stmt->num_rows > 0) {
        $stmt->fetch();
        
        // Verify password
        if ($password === $stored_password) {
            $_SESSION['user_id'] = $user_id;
            $_SESSION['user_type'] = $user_type;

            // Insert a record into the LoginTrackRecord table
            $login_time = date('Y-m-d H:i:s'); // Get current datetime for login time
            $stmt_insert = $conn->prepare("INSERT INTO LoginTrackRecord (user_id, login_time) VALUES (?, ?)");
            $stmt_insert->bind_param("is", $user_id, $login_time);
            $stmt_insert->execute();
            $stmt_insert->close();

            // Redirect based on user type
            if ($user_type == 'admin') {
                header("Location: admin_menu.php");
            } elseif ($user_type == 'cashier') {
                header("Location: cashier_menu.php");
            }
            exit();
        } else {
            $loginError = "Invalid username or password!";
        }
    } else {
        $loginError = "Invalid username or password!";
    }

    $stmt->close();
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #e6e2d3; /* Softer beige background color */
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }
        .background-wrapper {
            width: 100%;
            height: 100%;
            display: flex;
            justify-content: center;
            align-items: center;
            background-color: #d4cfc0; /* Slightly darker beige for contrast */
        }
        .container {
            background: #fafafa; /* Very light beige for container */
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            max-width: 400px;
            width: 100%;
            padding: 20px;
            box-sizing: border-box;
            text-align: center;
        }
        h2 {
            margin: 0 0 20px;
            color: #4a4a4a; /* Darker text for better readability */
        }
        label {
            display: block;
            margin-bottom: 8px;
            font-weight: bold;
            color: #333; /* Darker label text */
        }
        input[type="text"], input[type="password"] {
            width: calc(100% - 22px);
            padding: 10px;
            margin-bottom: 20px;
            border: 1px solid #bbb; /* Subtle border for inputs */
            border-radius: 4px;
            box-sizing: border-box;
            background-color: #fff; /* White background for inputs */
        }
        input[type="submit"] {
            background-color: #8a7f72; /* Muted brownish beige for the button */
            color: #fff;
            border: none;
            padding: 10px 20px;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
        }
        input[type="submit"]:hover {
            background-color: #736b5b; /* Slightly darker brownish beige on hover */
        }
        .error {
            color: #d9534f;
            margin: 10px 0;
        }
    </style>
</head>
<body>
    <div class="background-wrapper">
        <div class="container">
            <h2>Login</h2>
            <?php if (!empty($loginError)) { ?>
                <div class="error"><?php echo $loginError; ?></div>
            <?php } ?>
            <form action="" method="post">
                <label for="username">Username:</label>
                <input type="text" id="username" name="username" required>
                <label for="password">Password:</label>
                <input type="password" id="password" name="password" required>
                <input type="submit" value="Login">
            </form>
        </div>
    </div>
</body>
</html>
