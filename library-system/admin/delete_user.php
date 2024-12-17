<?php
include '../db.php';
session_start();

if ($_SESSION['role_id'] != 1) {
    header('Location: ../login.php');
    exit();
}

if (isset($_GET['id'])) {
    $user_id = $_GET['id'];

    // Check if user has active references in transactions
    $check_sql = "SELECT COUNT(*) FROM transactions WHERE borrower_id = :user_id";
    $check_stmt = $conn->prepare($check_sql);
    $check_stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
    $check_stmt->execute();
    $count = $check_stmt->fetchColumn();

    if ($count > 0) {
        // User has references; cannot delete
        header('Location: manage_users.php?error=CannotDeleteUserHasTransactions');
        exit();
    }

    // Proceed with deletion if no references
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
