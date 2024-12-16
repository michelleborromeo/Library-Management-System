<?php
session_start();
if ($_SESSION['role_id'] != 1) {
    header('Location: ../login.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container mt-5">
        <h1>Admin Dashboard</h1>
        <a href="add_book.php" class="btn btn-primary">Add Book</a>
        <a href="logs.php" class="btn btn-secondary">View Logs</a>
    </div>
</body>
</html>
