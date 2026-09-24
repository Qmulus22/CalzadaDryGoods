<?php
session_start();
require 'config.php'; // Ensure you include your database connection

if (isset($_SESSION['user_id'])) {
    $userId = $_SESSION['user_id'];
    $logoutTime = date('Y-m-d H:i:s'); // Current logout time

    // Now fetch the latest login time for the user
    $sql = "SELECT login_time FROM LoginTrackRecord WHERE user_id = ? ORDER BY login_time DESC LIMIT 1";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($row = $result->fetch_assoc()) {
        $loginTime = $row['login_time'];
        $logoutTime = date('Y-m-d H:i:s'); // Update logout time

        // Calculate session duration in seconds
        $sessionDurationSeconds = strtotime($logoutTime) - strtotime($loginTime);

        // Convert seconds to minutes
        $sessionDurationMinutes = (int)($sessionDurationSeconds / 60);

        // Update the logout record with the current logout time and session duration
        $updateSql = "UPDATE LoginTrackRecord SET logout_time = ?, session_duration = ? WHERE user_id = ? AND login_time = ?";
        $updateStmt = $conn->prepare($updateSql);
        $updateStmt->bind_param("siis", $logoutTime, $sessionDurationMinutes, $userId, $loginTime);
        $updateStmt->execute();
    }

    // Destroy all session data
    session_destroy();  
}

// Redirect to login page
header("Location: login.php");  
exit;
?>
