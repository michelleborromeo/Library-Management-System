<?php
include '../db.php';
session_start();

// Ensure the user is a Librarian
if ($_SESSION['role_id'] != 2) {
    header('Location: ../login.php');
    exit();
}

// Add Book functionality
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $title = $_POST['title'];
    $author = $_POST['author'];
    $published_year = $_POST['published_year'];

    $sql = "INSERT INTO books (title, author, published_year) VALUES (:title, :author, :published_year)";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':title', $title);
    $stmt->bindParam(':author', $author);
    $stmt->bindParam(':published_year', $published_year);
    $stmt->execute();

    $success = "Book added successfully!";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Book</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        /* Sidebar styles */
        .sidebar {
            height: 100%;
            position: fixed;
            top: 0;
            left: 0;
            z-index: 100;
            background-color: #343a40;
            padding-top: 20px;
            width: 250px;
        }
        .sidebar a {
            color: white;
            padding: 10px 15px;
            text-decoration: none;
            font-size: 18px;
            display: block;
        }
        .sidebar a:hover {
            background-color: #575d63;
        }
        /* Adjusting content area */
        .content {
            margin-left: 250px;
            padding: 20px;
        }
        /* Make the sidebar responsive */
        @media (max-width: 768px) {
            .sidebar {
                position: absolute;
                top: 0;
                left: -250px;
                width: 250px;
                transition: 0.3s;
            }
            .sidebar.active {
                left: 0;
            }
            .content {
                margin-left: 0;
            }
            .sidebar a {
                font-size: 16px;
            }
            .toggle-btn {
                display: block;
                font-size: 18px;
                color: white;
                background: #343a40;
                padding: 10px;
                cursor: pointer;
            }
        }
    </style>
</head>
<body>

    <!-- Sidebar -->
    <div class="sidebar">
        <h2 class="text-white text-center">Librarian Dashboard</h2>
        <a href="add_book.php">Add Book</a>
        <a href="edit_book.php">Edit Book</a>
        <a href="delete_book.php">Delete Book</a>
        <a href="approve_requests.php">Approve Requests</a>
        <a href="reports.php">Reports</a>
        <a href="borrower_records.php">Borrower Records</a>
        <a href="/php-108/logout.php" class="btn btn-danger">Logout</a>
    </div>

    <!-- Toggle Button for Sidebar (on mobile) -->
    <div class="toggle-btn d-none d-md-block">
        <span class="text-white" id="sidebar-toggle">&#9776; Menu</span>
    </div>

    <!-- Main Content -->
    <div class="content">
        <h1>Add Book</h1>
        <?php if (isset($success)) echo "<div class='alert alert-success'>$success</div>"; ?>
        <form method="POST">
            <div class="mb-3">
                <label for="title" class="form-label">Book Title</label>
                <input type="text" id="title" name="title" class="form-control" required>
            </div>
            <div class="mb-3">
                <label for="author" class="form-label">Author</label>
                <input type="text" id="author" name="author" class="form-control" required>
            </div>
            <div class="mb-3">
                <label for="published_year" class="form-label">Published Year</label>
                <input type="number" id="published_year" name="published_year" class="form-control" required>
            </div>
            <button type="submit" class="btn btn-primary">Add Book</button>
        </form>
    </div>

    <!-- Bootstrap JS and Popper.js (necessary for Bootstrap components) -->
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.min.js"></script>

    <script>
        // Toggle sidebar visibility on mobile screens
        const sidebar = document.querySelector('.sidebar');
        const toggleBtn = document.querySelector('#sidebar-toggle');
        toggleBtn.addEventListener('click', function() {
            sidebar.classList.toggle('active');
        });
    </script>
</body>
</html>
