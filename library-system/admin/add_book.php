<?php
include '../db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $title = $_POST['title'];
    $author = $_POST['author'];
    $published_year = $_POST['published_year'];

    $sql = "INSERT INTO books (title, author, published_year) VALUES (:title, :author, :published_year)";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':title', $title);
    $stmt->bindParam(':author', $author);
    $stmt->bindParam(':published_year', $published_year);
    $stmt->execute();

    echo "Book added successfully!";
}
?>

<form method="POST">
    Title: <input type="text" name="title" required><br>
    Author: <input type="text" name="author" required><br>
    Year: <input type="number" name="published_year" required><br>
    <button type="submit">Add Book</button>
</form>
