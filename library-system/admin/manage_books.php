<?php
include '../db.php';
session_start();

// Ensure the user is a Librarian
if ($_SESSION['role_id'] != 1) {
    header('Location: ../login.php');
    exit();
}

// Fetch all books from the database
$sql = "SELECT * FROM books";
$stmt = $conn->query($sql);
$books = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Delete Book functionality
if (isset($_GET['delete'])) {
    $book_id = $_GET['delete'];

    $sql = "DELETE FROM books WHERE book_id = :book_id";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':book_id', $book_id);
    $stmt->execute();

    $success = "Book deleted successfully!";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Books</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h1>Manage Books</h1>

        <?php if (isset($success)) echo "<div class='alert alert-success'>$success</div>"; ?>

        <!-- Add Book Button (Only visible in Manage Books) -->
        <div class="d-flex justify-content-start mb-3">
            <a href="add_book.php" class="btn btn-primary">Add New Book</a>
        </div>


        <hr>

        <!-- List of Books -->
        <h3>Books List</h3>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Title</th>
                    <th>Author</th>
                    <th>Published Year</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($books as $book): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($book['title']); ?></td>
                        <td><?php echo htmlspecialchars($book['author']); ?></td>
                        <td><?php echo htmlspecialchars($book['published_year']); ?></td>
                        <td>
                            <a href="edit_book.php?id=<?php echo $book['book_id']; ?>" class="btn btn-warning">Edit</a>
                            <a href="?delete=<?php echo $book['book_id']; ?>" class="btn btn-danger" onclick="return confirm('Are you sure you want to delete this book?');">Delete</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</body>
</html>
