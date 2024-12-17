<?php
include '../db.php';
session_start();

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Check if the user is logged in and has the correct role
if ($_SESSION['role_id'] != 1) {
    header('Location: ../login.php');
    exit();
}

if (isset($_GET['book_id'])) {
    $book_id = $_GET['book_id'];

    // Check if book_id is valid and not empty
    if (empty($book_id)) {
        echo "Invalid book ID.";
        exit();
    }

    // Prepare and execute the delete query
    $sql = "DELETE FROM books WHERE book_id = :book_id";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':book_id', $book_id);

    // Execute the query and check if it was successful
    if ($stmt->execute()) {
        header('Location: librarian_dashboard.php');
        exit();
    } else {
        echo "Error deleting the book.";
        exit();
    }
} else {
    echo "No book ID provided.";
    exit();
}
?>
