<?php
include '../db.php';
session_start();

if ($_SESSION['role_id'] != 1) {
    header('Location: ../login.php');
    exit();
}

$sql = "SELECT logs.user_id, users.username, logs.action, logs.table_name, logs.timestamp 
        FROM logs 
        JOIN users ON logs.user_id = users.id 
        ORDER BY logs.timestamp DESC";
$stmt = $conn->query($sql);
$logs = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Activity Logs</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h1>Activity Logs</h1>
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>User</th>
                    <th>Action</th>
                    <th>Table</th>
                    <th>Timestamp</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($logs as $log): ?>
                <tr>
                    <td><?php echo htmlspecialchars($log['username']); ?></td>
                    <td><?php echo htmlspecialchars($log['action']); ?></td>
                    <td><?php echo htmlspecialchars($log['table_name']); ?></td>
                    <td><?php echo htmlspecialchars($log['timestamp']); ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</body>
</html>
