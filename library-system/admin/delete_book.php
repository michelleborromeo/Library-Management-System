<?php
include '../db.php';
session_start();

if ($_SESSION['role_id'] != 1) {
    header('Location: ../login.php');
    exit();
}

if (isset($_GET['book_id'])) {
    $book_id = $_GET['book_id'];

    $sql = "DELETE FROM books WHERE book_id = :book_id";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':book_id', $book_id);
    $stmt->execute();

    header('Location: dashboard.php');
    exit();
}
?>
