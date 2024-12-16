<?php
include '../db.php';
session_start();

if ($_SESSION['role_id'] != 1) {
    header('Location: ../login.php');
    exit();
}

if (isset($_GET['book_id'])) {
    $book_id = $_GET['book_id'];

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $title = $_POST['title'];
        $author = $_POST['author'];
        $published_year = $_POST['published_year'];

        $sql = "UPDATE books SET title = :title, author = :author, published_year = :published_year WHERE book_id = :book_id";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':title', $title);
        $stmt->bindParam(':author', $author);
        $stmt->bindParam(':published_year', $published_year);
        $stmt->bindParam(':book_id', $book_id);
        $stmt->execute();

        header('Location: dashboard.php');
        exit();
    }

    $sql = "SELECT * FROM books WHERE book_id = :book_id";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':book_id', $book_id);
    $stmt->execute();
    $book = $stmt->fetch(PDO::FETCH_ASSOC);
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
        <form method="POST">
            <div class="mb-3">
                <label for="title" class="form-label">Title</label>
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
            <button type="submit" class="btn btn-primary">Save Changes</button>
        </form>
    </div>
</body>
</html>
