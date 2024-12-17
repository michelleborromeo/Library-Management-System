<?php
include '../db.php';
session_start();

if ($_SESSION['role_id'] != 1) {
    header('Location: ../login.php');
    exit();
}

if (isset($_GET['id'])) {
    $user_id = $_GET['id'];

    // Update the user's status to 'active'
    $sql = "UPDATE users SET status = 'active' WHERE user_id = :user_id";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);

    if ($stmt->execute()) {
        header('Location: manage_users.php?message=User unblocked successfully');
        exit();
    } else {
        echo "Error unblocking the user.";
    }
} else {
    header('Location: manage_users.php');
    exit();
}
?>
