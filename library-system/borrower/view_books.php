<?php
include '../db.php';
session_start();

// Ensure the user is logged in and is a Borrower
if (!isset($_SESSION['role_id']) || $_SESSION['role_id'] != 3) {
    header('Location: ../login.php');
    exit();
}

// Fetch the logged-in user's status
$user_id = $_SESSION['user_id'];  // Assuming user_id is stored in session
$sql_user = "SELECT status FROM users WHERE user_id = :user_id";
$stmt_user = $conn->prepare($sql_user);
$stmt_user->bindParam(':user_id', $user_id, PDO::PARAM_INT);
$stmt_user->execute();
$user = $stmt_user->fetch(PDO::FETCH_ASSOC);

// Check if the user's status is 'blocked'
if ($user && $user['status'] == 'blocked') {
    $_SESSION['error_message'] = 'Your account has been blocked. You cannot borrow books.';
    header('Location: ../borrow_book.php');
    exit();
}

// Handle search query and category filter
$search = isset($_POST['search']) ? $_POST['search'] : '';
$category = isset($_POST['category']) ? $_POST['category'] : '';

// SQL Query for available books with dynamic filtering based on search and category
$sql_books = "SELECT * FROM books WHERE is_available = TRUE";

// Add conditions based on search and category
$conditions = [];
if ($search) {
    $conditions[] = "(title LIKE :search OR author LIKE :search)";
}
if ($category) {
    $conditions[] = "category = :category";
}

// If there are conditions, append them to the SQL query
if (count($conditions) > 0) {
    $sql_books .= " AND " . implode(" AND ", $conditions);
}

$stmt_books = $conn->prepare($sql_books);

// Bind parameters if needed
if ($search) {
    $stmt_books->bindValue(':search', '%' . $search . '%');
}
if ($category) {
    $stmt_books->bindValue(':category', $category);
}

$stmt_books->execute();
$books = $stmt_books->fetchAll(PDO::FETCH_ASSOC);

// Get categories for the dropdown
$sql_categories = "SELECT DISTINCT category FROM books";
$stmt_categories = $conn->prepare($sql_categories);
$stmt_categories->execute();
$categories = $stmt_categories->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Books</title>
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
            transition: 0.3s;
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

        /* Adjust content when sidebar is visible */
        .content {
            margin-left: 250px;
            padding: 50px;
            transition: margin-left 0.3s;
        }

        /* Navbar Styles */
        .navbar {
            background-color: #343a40;
            position: fixed;
            top: 0;
            width: 100%;
            z-index: 1000;
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

        /* Adjust main content to not be hidden by navbar */
        .content {
            margin-top: 80px; /* Adjust the content position below the fixed navbar */
        }

        /* Image size adjustment */
        .card-img-top {
            width: 100%;
            height: auto;
            max-height: 200px; /* Optional: Limit the maximum height of the image */
            object-fit: contain;
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
    </style>
</head>
<body>

    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark">
        <div class="container-fluid">
            <a class="navbar-brand" href="#">Borrower Dashboard</a>
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
        <a href="view_books.php" class="nav-item"><i class="bi bi-book"></i> Books</a>
        <a href="borrow_book.php" class="nav-item"><i class="bi bi-cash-stack"></i> Borrow Records</a>
    </nav>
    </div>

    <!-- Main Content -->
    <div class="content">
        <h1>Available Books</h1>

        <!-- Search and Category Filter -->
        <form method="POST" class="mb-4" id="searchForm">
            <div class="d-flex gap-3">
                <input type="text" name="search" id="search" class="form-control" placeholder="Search by title or author" value="<?= htmlspecialchars($search) ?>">
                <select name="category" id="category" class="form-control">
                    <option value="">All Categories</option>
                    <?php foreach ($categories as $cat): ?>
                        <option value="<?= htmlspecialchars($cat['category']) ?>" <?= $category == $cat['category'] ? 'selected' : '' ?>><?= htmlspecialchars($cat['category']) ?></option>
                    <?php endforeach; ?>
                </select>
                <button type="submit" class="btn btn-primary">Filter</button>
            </div>
        </form>

        <!-- Display Books -->
        <div class="row">
            <?php if (count($books) > 0): ?>
                <?php foreach ($books as $book): ?>
                    <div class="col-md-3 mb-4">
                        <div class="card">
                        <img src="../<?= htmlspecialchars($book['image_path']) ?>" class="card-img-top" alt="Book Image">
                            <div class="card-body">
                                <h5 class="card-title"><?= htmlspecialchars($book['title']) ?></h5>
                                <p class="card-text">Author: <?= htmlspecialchars($book['author']) ?></p>
                                <p class="card-text">Year: <?= htmlspecialchars($book['published_year']) ?></p>
                                <p class="card-text">Category: <?= htmlspecialchars($book['category']) ?></p>
                                 <!-- Borrow Button opens the modal -->
                        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#borrowModal<?php echo $book['book_id']; ?>">Borrow</button>
                            </div>
                        </div>
                    </div>

                     <!-- Modal for Borrow Form -->
            <div class="modal fade" id="borrowModal<?php echo $book['book_id']; ?>" tabindex="-1" aria-labelledby="borrowModalLabel" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <form action="borrow_book.php" method="POST">
                            <div class="modal-header">
                                <h5 class="modal-title" id="borrowModalLabel">Borrow Book</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <input type="hidden" name="book_id" value="<?php echo $book['book_id']; ?>">
                                <div class="mb-3">
                                    <label for="borrow_date" class="form-label">Borrow Date</label>
                                    <input type="date" name="borrow_date" class="form-control" required>
                                </div>
                                <div class="mb-3">
                                    <label for="return_date" class="form-label">Return Date</label>
                                    <input type="date" name="return_date" class="form-control" required>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="submit" class="btn btn-primary">Submit</button>
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p>No books available for your search criteria.</p>
            <?php endif; ?>
        </div>
    </div>

    <!-- Footer -->
    <div class="footer">
        <p>&copy; 2024 Bookshelf. All rights reserved.</p>
    </div>

    <!-- Bootstrap JS and Popper.js (necessary for Bootstrap components) -->
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.min.js"></script>

    <!-- Auto-submit search form when user types or selects a category -->
    <script>
        document.getElementById('search').addEventListener('input', function() {
            document.getElementById('searchForm').submit();
        });
        
        document.getElementById('category').addEventListener('change', function() {
            document.getElementById('searchForm').submit();
        });
    </script>

</body>
</html>
