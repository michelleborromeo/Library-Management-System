<?php
include '../db.php';
session_start();

if ($_SESSION['role_id'] != 2) {
    header('Location: ../login.php');
    exit();
}

$sql = "SELECT books.title, COUNT(transactions.book_id) AS borrow_count
        FROM transactions
        JOIN books ON transactions.book_id = books.book_id
        GROUP BY books.title
        ORDER BY borrow_count DESC";
$stmt = $conn->query($sql);
$report = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reports</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h1>Most Borrowed Books</h1>
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>Book Title</th>
                    <th>Times Borrowed</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($report as $item): ?>
                <tr>
                    <td><?php echo htmlspecialchars($item['title']); ?></td>
                    <td><?php echo htmlspecialchars($item['borrow_count']); ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</body>
</html>
