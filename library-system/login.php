<?php
include 'db.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    $sql = "SELECT * FROM users WHERE username = :username";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':username', $username);
    $stmt->execute();
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['user_id'];
        $_SESSION['role_id'] = $user['role_id'];

        if ($user['role_id'] == 1) {
            header('Location: admin/dashboard.php');
            exit();
        } elseif ($user['role_id'] == 2) {
            header('Location: librarian/librarian_dashboard.php');
            exit();
        } elseif ($user['role_id'] == 3) {
            header('Location: borrower/borrow_book.php');
            exit();
        }
    } else {
        $error = "Invalid credentials!";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card shadow">
                    <div class="card-body">
                        <h2 class="text-center text-primary">Login</h2>

                        <!-- Display error message if login fails -->
                        <?php if (isset($error)): ?>
                            <div class="alert alert-danger"><?php echo $error; ?></div>
                        <?php endif; ?>

                        <form method="POST">
                            <div class="mb-3">
                                <label for="username" class="form-label">Username</label>
                                <input type="text" name="username" class="form-control" required>
                            </div>
                            <div class="mb-3">
                                <label for="password" class="form-label">Password</label>
                                <input type="password" name="password" class="form-control" required>
                            </div>
                            <button type="submit" class="btn btn-primary w-100">Login</button>
                        </form>
                         <!-- Forgot Password Link -->
                         <!-- <div class="mt-2">
                            <a href="forgot_password.php" class="text-decoration-none">Forgot Password?</a>
                        </div> -->
                        <div class="mt-3 text-center">
                            <a href="register.php" class="text-decoration-none">Don't have an account? Register here.</a>
                        </div>
                       
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
