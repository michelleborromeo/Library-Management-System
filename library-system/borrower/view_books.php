<?php
include '../db.php';
session_start();

if ($_SESSION['role_id'] != 3) {
    header('Location: ../login.php');
    exit();
}

$sql = "SELECT * FROM books WHERE is_available = TRUE";
$stmt = $conn->query($sql);
$books = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Books</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h1>Available Books</h1>
        <div class="row">
            <?php foreach ($books as $book): ?>
            <div class="col-md-4">
                <div class="card mb-4">
                    <div class="card-body">
                        <h5 class="card-title"><?php echo htmlspecialchars($book['title']); ?></h5>
                        <p class="card-text">Author: <?php echo htmlspecialchars($book['author']); ?></p>
                        <p class="card-text">Year: <?php echo htmlspecialchars($book['published_year']); ?></p>
                        <a href="borrow_book.php?book_id=<?php echo $book['book_id']; ?>" class="btn btn-primary">Borrow</a>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</body>
</html>
