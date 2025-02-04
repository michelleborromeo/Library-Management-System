<?php
include '../db.php';
session_start();

// Ensure the user is a Librarian
if ($_SESSION['role_id'] != 1) {
    header('Location: ../login.php');
    exit();
}

// Add Book functionality
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $title = $_POST['title'];
    $author = $_POST['author'];
    $published_year = $_POST['published_year'];
    $category = $_POST['category'];
    $added_by = $_SESSION['user_id']; // Get the logged-in user's ID

    // Check if the directory exists, if not, create it
    $imageDir = '../assets/images';
    if (!file_exists($imageDir)) {
        mkdir($imageDir, 0777, true);
    }

    // Handle image upload
    $imagePath = '';
    if (isset($_FILES['book_image']) && $_FILES['book_image']['error'] == 0) {
        $fileExt = pathinfo($_FILES['book_image']['name'], PATHINFO_EXTENSION);
        $uniqueFileName = 'book_' . uniqid() . '.' . $fileExt;

        if (move_uploaded_file($_FILES['book_image']['tmp_name'], $imageDir . '/' . $uniqueFileName)) {
            $imagePath = 'assets/images/' . $uniqueFileName;
        } else {
            $error = "Error uploading image.";
        }
    }

    // Insert book details into the database
    $sql = "INSERT INTO books (title, author, published_year, category, added_by, image_path) 
            VALUES (:title, :author, :published_year, :category, :added_by, :image_path)";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':title', $title);
    $stmt->bindParam(':author', $author);
    $stmt->bindParam(':published_year', $published_year);
    $stmt->bindParam(':category', $category);
    $stmt->bindParam(':added_by', $added_by);
    $stmt->bindParam(':image_path', $imagePath);
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
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">

    <style>
        /* Sidebar styles */
        .sidebar {
            height: 100%;
            position: fixed;
            top: 50px; /* Adjust this value to move the sidebar lower */
            left: 0;
            background-color: #343a40;
            width: 250px;
            padding-top: 20px;
        }

        .sidebar a {
            color: white;
            padding: 10px 15px;
            font-size: 18px;
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .sidebar a:hover {
            background-color: #495057;
        }

        .sidebar .nav-item {
            margin: 5px 0;
        }

        .content {
            margin-left: 250px;
            padding: 20px;
        }

        .navbar {
            background-color: #343a40;
        }

        .navbar .dropdown-menu {
            background-color: #343a40;
        }

        .navbar .dropdown-item {
            color: white;
        }

        .navbar .dropdown-item:hover {
            background-color: #495057;
        }

        .footer {
            background-color: #343a40;
            color: white;
            text-align: center;
            padding: 10px;
            position: fixed;
            bottom: 0;
            width: 100%;
        }

        .card {
            border: none;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            margin-top: 20px; 
        }

        @media (max-width: 768px) {
            .sidebar {
                left: -250px;
                transition: 0.3s;
            }

            .sidebar.active {
                left: 0;
            }

            .content {
                margin-left: 0;
            }

            .toggle-btn {
                display: block;
                position: fixed;
                top: 10px;
                left: 10px;
                z-index: 1050;
                background-color: #343a40;
                color: white;
                border: none;
                padding: 10px 15px;
                cursor: pointer;
            }
        }
    </style>
</head>
<body>

    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark">
        <div class="container-fluid">
        <a class="navbar-brand" href="#">Admin</a>
            <div class="ms-auto">
                <div class="dropdown">
                    <button class="btn btn-secondary dropdown-toggle" type="button" id="dropdownMenuButton" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="bi bi-person-circle"></i>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="dropdownMenuButton">
                        <li><a class="dropdown-item text-white" href="../logout.php">Logout</a></li>
                    </ul>
                </div>
            </div>
        </div>
    </nav>

    <!-- Sidebar -->
    <div class="sidebar">
        <nav>
            <a href="dashboard.php" class="nav-item"><i class="bi bi-house-door"></i> Dashboard</a>
            <a href="manage_books.php" class="nav-item"><i class="bi bi-book"></i> Manage Books</a>
            <a href="activity_logs.php" class="nav-item"><i class="bi bi-file-earmark-text"></i> View Logs</a>
            <a href="reports.php" class="nav-item"><i class="bi bi-bar-chart-line"></i> Reports</a>
        </nav>
    </div>

    <!-- Toggle Button -->
    <button class="toggle-btn d-lg-none" id="sidebar-toggle">&#9776;</button>

    <!-- Main Content -->
    <div class="content">
        <h1>Add Book</h1>
        <?php if (isset($success)) echo "<div class='alert alert-success'>$success</div>"; ?>
        <?php if (isset($error)) echo "<div class='alert alert-danger'>$error</div>"; ?>
        <form method="POST" enctype="multipart/form-data">
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
            <div class="mb-3">
                <label for="category" class="form-label">Category</label>
                <select id="category" name="category" class="form-control" required>
                    <option value="Engineering">Engineering</option>
                    <option value="Environmental Science">Environmental Science</option>
                    <option value="Social Science">Social Science</option>
                    <option value="Natural Science">Natural Science</option>
                    <option value="Education">Education</option>
                    <option value="Programming">Programming</option>
                </select>
            </div>
            <div class="mb-3">
                <label for="book_image" class="form-label">Book Image</label>
                <input type="file" id="book_image" name="book_image" class="form-control">
            </div>
            <button type="submit" class="btn btn-primary">Add Book</button>
        </form>
    </div>

    <!-- Bootstrap JS and Popper.js (necessary for Bootstrap components) -->
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.min.js"></script>

  <!-- Footer -->
  <div class="footer">
        <p>&copy; 2024 Bookshelf. All rights reserved.</p>
    </div>
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
