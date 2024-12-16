<?php
include '../db.php';
session_start();

if ($_SESSION['role_id'] != 3) {
    header('Location: ../login.php');
    exit();
}

if (isset($_GET['book_id'])) {
    $book_id = $_GET['book_id'];
    $user_id = $_SESSION['user_id'];

    $sql = "INSERT INTO transactions (book_id, borrower_id, status) VALUES (:book_id, :borrower_id, 'Borrowed')";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':book_id', $book_id);
    $stmt->bindParam(':borrower_id', $user_id);
    $stmt->execute();

    $update_sql = "UPDATE books SET is_available = FALSE WHERE book_id = :book_id";
    $update_stmt = $conn->prepare($update_sql);
    $update_stmt->bindParam(':book_id', $book_id);
    $update_stmt->execute();

    header('Location: view_books.php');
    exit();
}
?>
