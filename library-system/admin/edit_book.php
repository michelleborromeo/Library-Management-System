<?php
include '../db.php';
session_start();

// Ensure the user is a Librarian
if ($_SESSION['role_id'] != 1) {
    header('Location: ../login.php');
    exit();
}

// Edit Book functionality
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $book_id = $_POST['id'];
    $title = $_POST['title'];
    $author = $_POST['author'];
    $published_year = $_POST['published_year'];

    // Correct the placeholder to :id (or :book_id)
    $sql = "UPDATE books SET title = :title, author = :author, published_year = :published_year WHERE book_id = :id";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':title', $title);
    $stmt->bindParam(':author', $author);
    $stmt->bindParam(':published_year', $published_year);
    $stmt->bindParam(':id', $book_id);  // This must match the placeholder in SQL query
    $stmt->execute();

    $success = "Book updated successfully!";
}

// Fetch the book to edit
if (isset($_GET['id'])) {
    $book_id = $_GET['id'];

    $sql = "SELECT * FROM books WHERE book_id = :id";  // Ensure the column name matches
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':id', $book_id);  // Ensure the placeholder matches
    $stmt->execute();
    $book = $stmt->fetch(PDO::FETCH_ASSOC);
} else {
    header('Location: manage_books.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Book</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h1>Edit Book</h1>
        
        <?php if (isset($success)) echo "<div class='alert alert-success'>$success</div>"; ?>

        <!-- Edit Book Form -->
        <form method="POST">
            <input type="hidden" name="id" value="<?php echo $book['book_id']; ?>">
            <div class="mb-3">
                <label for="title" class="form-label">Book Title</label>
                <input type="text" name="title" class="form-control" value="<?php echo htmlspecialchars($book['title']); ?>" required>
            </div>
            <div class="mb-3">
                <label for="author" class="form-label">Author</label>
                <input type="text" name="author" class="form-control" value="<?php echo htmlspecialchars($book['author']); ?>" required>
            </div>
            <div class="mb-3">
                <label for="published_year" class="form-label">Published Year</label>
                <input type="number" name="published_year" class="form-control" value="<?php echo htmlspecialchars($book['published_year']); ?>" required>
            </div>
            <button type="submit" class="btn btn-primary">Update Book</button>
        </form>
    </div>
</body>
</html>
