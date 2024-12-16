<?php
include '../db.php';
session_start();

if ($_SESSION['role_id'] != 1) {
    header('Location: ../login.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $max_books = $_POST['max_books'];
    $penalty_per_day = $_POST['penalty_per_day'];

    $sql = "UPDATE settings SET max_books = :max_books, penalty_per_day = :penalty_per_day WHERE id = 1";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':max_books', $max_books);
    $stmt->bindParam(':penalty_per_day', $penalty_per_day);
    $stmt->execute();

    $success = "Settings updated successfully!";
}

$sql = "SELECT * FROM settings WHERE id = 1";
$stmt = $conn->query($sql);
$settings = $stmt->fetch(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>System Configuration</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h1>System Configuration</h1>
        <?php if (isset($success)) echo "<div class='alert alert-success'>$success</div>"; ?>
        <form method="POST">
            <div class="mb-3">
                <label for="max_books" class="form-label">Max Books Per Borrower</label>
                <input type="number" name="max_books" class="form-control" value="<?php echo $settings['max_books']; ?>" required>
            </div>
            <div class="mb-3">
                <label for="penalty_per_day" class="form-label">Penalty Per Day (in $)</label>
                <input type="number" name="penalty_per_day" class="form-control" value="<?php echo $settings['penalty_per_day']; ?>" required>
            </div>
            <button type="submit" class="btn btn-primary">Save Settings</button>
        </form>
    </div>
</body>
</html>
