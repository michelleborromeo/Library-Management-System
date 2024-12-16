<?php
include 'db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = password_hash($_POST['password'], PASSWORD_BCRYPT);
    $role_id = $_POST['role_id'];

    $sql = "INSERT INTO users (username, password, role_id) VALUES (:username, :password, :role_id)";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':username', $username);
    $stmt->bindParam(':password', $password);
    $stmt->bindParam(':role_id', $role_id);
    $stmt->execute();

    $success = "User registered successfully!";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/styles.css">
</head>
<body>
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card shadow">
                    <div class="card-body">
                        <h2 class="text-center text-primary">Register</h2>
                        <?php if (isset($success)) { echo "<div class='alert alert-success'>$success</div>"; } ?>
                        <form method="POST">
                            <div class="mb-3">
                                <label for="username" class="form-label">Username</label>
                                <input type="text" name="username" class="form-control" required>
                            </div>
                            <div class="mb-3">
                                <label for="password" class="form-label">Password</label>
                                <input type="password" name="password" class="form-control" required>
                            </div>
                            <div class="mb-3">
                                <label for="role_id" class="form-label">Role</label>
                                <select name="role_id" class="form-select">
                                    <option value="1">Admin</option>
                                    <option value="2">Librarian</option>
                                    <option value="3">Borrower</option>
                                </select>
                            </div>
                            <button type="submit" class="btn btn-primary w-100">Register</button>
                        </form>
                        <div class="mt-3 text-center">
                            <a href="login.php" class="text-decoration-none">Already have an account? Login here.</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
