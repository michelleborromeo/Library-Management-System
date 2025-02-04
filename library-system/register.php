<?php
// Include database connection
include 'db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get username and password from the form
    $username = $_POST['username'];
    $password = password_hash($_POST['password'], PASSWORD_BCRYPT); // Hash the password
    $role_id = 3; // Automatically assign the Borrower role

    try {
        // Insert user into the database
        $sql = "INSERT INTO users (username, password, role_id) VALUES (:username, :password, :role_id)";
        $stmt = $conn->prepare($sql);

        // Bind parameters
        $stmt->bindParam(':username', $username);
        $stmt->bindParam(':password', $password);
        $stmt->bindParam(':role_id', $role_id);

        // Execute the query
        $stmt->execute();
        $success = "User registered successfully as a Borrower!";
    } catch (PDOException $e) {
        // Catch duplicate username error
        if ($e->getCode() == 23000) {
            $error = "Username already exists. Please choose another.";
        } else {
            $error = "An error occurred: " . $e->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card shadow">
                    <div class="card-body">
                        <h2 class="text-center text-primary">Register</h2>
                        <!-- Display success or error messages -->
                        <?php if (isset($success)) { ?>
                            <div class="alert alert-success"><?php echo $success; ?></div>
                        <?php } ?>
                        <?php if (isset($error)) { ?>
                            <div class="alert alert-danger"><?php echo $error; ?></div>
                        <?php } ?>
                        <!-- Registration Form -->
                        <form method="POST">
                            <div class="mb-3">
                                <label for="username" class="form-label">Username</label>
                                <input type="text" name="username" class="form-control" required>
                            </div>
                            <div class="mb-3">
                                <label for="password" class="form-label">Password</label>
                                <input type="password" name="password" class="form-control" required>
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
