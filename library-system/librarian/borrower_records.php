<?php
include '../db.php';
session_start();

if ($_SESSION['role_id'] != 2) {
    header('Location: ../login.php');
    exit();
}

$sql = "SELECT users.name AS borrower_name, books.title AS book_title, transactions.borrow_date, transactions.return_date, transactions.status 
        FROM transactions 
        JOIN users ON transactions.borrower_id = users.user_id 
        JOIN books ON transactions.book_id = books.book_id
        ORDER BY transactions.borrow_date DESC";
$stmt = $conn->query($sql);
$records = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Borrower Records</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h1>Borrower Records</h1>
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>Borrower</th>
                    <th>Book Title</th>
                    <th>Borrow Date</th>
                    <th>Return Date</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($records as $record): ?>
                <tr>
                    <td><?php echo htmlspecialchars($record['borrower_name']); ?></td>
                    <td><?php echo htmlspecialchars($record['book_title']); ?></td>
                    <td><?php echo htmlspecialchars($record['borrow_date']); ?></td>
                    <td><?php echo htmlspecialchars($record['return_date'] ?? 'Not Returned'); ?></td>
                    <td><?php echo htmlspecialchars($record['status']); ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</body>
</html>
