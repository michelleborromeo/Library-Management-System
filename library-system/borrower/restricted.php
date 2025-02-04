<?php
// Start the session
session_start();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Access Restricted</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
            font-family: Arial, sans-serif;
        }

        .container {
            margin-top: 100px;
            text-align: center;
        }

        .card {
            background-color: #ffffff;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            max-width: 500px;
            margin: auto;
        }

        .message {
            font-size: 24px;
            color: #dc3545;
            font-weight: bold;
            margin-bottom: 20px;
        }

        .btn-home {
            margin-top: 20px;
            padding: 10px 20px;
            font-size: 16px;
            background-color: #007bff;
            color: white;
            border: none;
            border-radius: 5px;
            text-decoration: none;
        }

        .btn-home:hover {
            background-color: #0056b3;
            color: white;
            text-decoration: none;
        }

        .card-header {
            background-color: #dc3545;
            color: white;
            font-size: 18px;
            font-weight: bold;
            border-radius: 10px 10px 0 0;
            padding: 10px;
        }
    </style>
</head>
<body>

<div class="container">
    <div class="card">
        <div class="card-header">
            Account Access Restricted
        </div>
        <div class="message">
            <p>Your account has been blocked.<br> You are not authorized to access this page.</p>
            <p>Make sure to return any overdue books to regain access to this page and borrow books again.</p>
        </div>
        <a href="../login.php" class="btn btn-primary btn-home">Go to Login</a>
    </div>
</div>

</body>
</html>
