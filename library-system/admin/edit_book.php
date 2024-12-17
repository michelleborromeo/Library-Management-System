<?php
include '../db.php';
session_start();

// Check if the user is logged in and has the correct role
if (!isset($_SESSION['role_id']) || $_SESSION['role_id'] != 2) {
    header('Location: ../login.php');
    exit();
}

// Check if 'book_id' is passed in the URL
if (isset($_GET['book_id'])) {
    $book_id = $_GET['book_id'];

    // Handle form submission (POST request)
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $title = $_POST['title'];
        $author = $_POST['author'];
        $published_year = $_POST['published_year'];

        // Update the book in the database
        $sql = "UPDATE books SET title = :title, author = :author, published_year = :published_year WHERE book_id = :book_id";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':title', $title);
        $stmt->bindParam(':author', $author);
        $stmt->bindParam(':published_year', $published_year);
        $stmt->bindParam(':book_id', $book_id);
        $stmt->execute();

        // Redirect back to the librarian dashboard
        header('Location: librarian_dashboard.php');
        exit();
    }

    // Fetch the book details for editing
    $sql = "SELECT * FROM books WHERE book_id = :book_id";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':book_id', $book_id);
    $stmt->execute();
    $book = $stmt->fetch(PDO::FETCH_ASSOC);

    // Check if the book exists
    if (!$book) {
        echo "Book not found.";
        exit();
    }
} else {
    echo "No book selected for editing.";
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Book</title>
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
        <a href="../logout.php" class="btn btn-danger">Logout</a>
    </div>

    <!-- Toggle Button for Sidebar (on mobile) -->
    <div class="toggle-btn d-none d-md-block">
        <span class="text-white" id="sidebar-toggle">&#9776; Menu</span>
    </div>

    <!-- Main Content -->
    <div class="content">
        <h1>Edit Book</h1>
        <form method="POST">
            <div class="mb-3">
                <label for="title" class="form-label">Book Title</label>
                <input type="text" name="title" class="form-control" 
                       value="<?php echo htmlspecialchars($book['title']); ?>" required>
            </div>
            <div class="mb-3">
                <label for="author" class="form-label">Author</label>
                <input type="text" name="author" class="form-control" 
                       value="<?php echo htmlspecialchars($book['author']); ?>" required>
            </div>
            <div class="mb-3">
                <label for="published_year" class="form-label">Published Year</label>
                <input type="number" name="published_year" class="form-control" 
                       value="<?php echo htmlspecialchars($book['published_year']); ?>" required>
            </div>
            <button type="submit" class="btn btn-primary">Save Changes</button>
            <a href="librarian_dashboard.php" class="btn btn-secondary">Cancel</a>
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
