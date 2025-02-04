<?php
include '../db.php';
session_start();

// Check if the user is an admin
if ($_SESSION['role_id'] != 1) {
    header('Location: ../login.php');
    exit();
}

if (isset($_GET['id'])) {
    $user_id = $_GET['id'];

    // Optionally, delete all transactions related to the user first (if you want to remove the data entirely)
    $delete_transactions_sql = "DELETE FROM transactions WHERE borrower_id = :user_id";
    $delete_stmt = $conn->prepare($delete_transactions_sql);
    $delete_stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
    $delete_stmt->execute();

    // Proceed with deleting the user
    $sql = "DELETE FROM users WHERE user_id = :user_id";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);

    if ($stmt->execute()) {
        header('Location: manage_users.php?message=UserDeleted');
        exit();
    } else {
        header('Location: manage_users.php?error=DeleteFailed');
        exit();
    }
} else {
    header('Location: manage_users.php?error=NoID');
    exit();
}
?>
