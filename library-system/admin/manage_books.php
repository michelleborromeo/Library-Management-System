<?php
include '../db.php';
session_start();

// Ensure the user is a Librarian
if ($_SESSION['role_id'] != 1) {
    header('Location: ../login.php');
    exit();
}

// Fetch all books from the database
$sql = "SELECT * FROM books";
$stmt = $conn->query($sql);
$books = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Delete Book functionality
if (isset($_GET['delete'])) {
    $book_id = $_GET['delete'];

    // First, delete related transactions that reference this book
    $deleteTransactions = "DELETE FROM transactions WHERE book_id = :book_id";
    $stmt = $conn->prepare($deleteTransactions);
    $stmt->bindParam(':book_id', $book_id);
    $stmt->execute();

    // Then, delete the book from the books table
    $sql = "DELETE FROM books WHERE book_id = :book_id";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':book_id', $book_id);
    $stmt->execute();

    $success = "Book and related transactions deleted successfully!";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Books</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        /* Sidebar styles */
        .sidebar {
            height: 100%;
            position: fixed;
            top: 0;
            left: 0;
            background-color: #343a40;
            width: 250px;
            padding-top: 60px; /* Adjust for sticky navbar */
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

        .table {
            border-collapse: collapse;
            width: 100%;
            background-color: #fff;
        }

        .table th,
        .table td {
            text-align: center;
            padding: 12px;
        }

        .table th {
            background-color: #343a40;
            color: white;
            font-weight: bold;
        }

        .table-hover tbody tr:hover {
            background-color: #f1f1f1;
        }

        .table-striped tbody tr:nth-of-type(odd) {
            background-color: #f9f9f9;
        }
        /* Scrollable Table Wrapper */
        .table-wrapper {
            max-height: 400px; /* Adjust height as needed */
            overflow-y: auto;
            border: 1px solid #ddd;
        }

        .search-bar {
            margin-bottom: 20px;
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
    <nav class="navbar navbar-expand-lg navbar-dark sticky-top">
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

    <div class="content">
        <h1>Manage Books</h1>

        <?php if (isset($success)) echo "<div class='alert alert-success'>$success</div>"; ?>

        <!-- Search Bar -->
        <div class="search-bar">
            <input type="text" class="form-control" id="searchInput" placeholder="Search books by title, author, or year...">
        </div>

        <!-- Add Book Button -->
        <div class="d-flex justify-content-start mb-3">
            <a href="add_book.php" class="btn btn-primary">Add New Book</a>
        </div>

        <!-- List of Books -->
        <h3>Books List</h3>
        <div class="table-wrapper">
            <table class="table table-bordered table-striped table-hover">
                <thead>
                    <tr>
                        <th>Title</th>
                        <th>Author</th>
                        <th>Published Year</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody id="booksTable">
                    <?php foreach ($books as $book): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($book['title']); ?></td>
                            <td><?php echo htmlspecialchars($book['author']); ?></td>
                            <td><?php echo htmlspecialchars($book['published_year']); ?></td>
                            <td>
                                <a href="edit_book.php?id=<?php echo $book['book_id']; ?>" class="btn btn-warning">Edit</a>
                                <a href="?delete=<?php echo $book['book_id']; ?>" class="btn btn-danger" onclick="return confirm('Are you sure you want to delete this book?');">Delete</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Footer -->
    <div class="footer">
        <p>&copy; 2024 Bookshelf. All rights reserved.</p>
    </div>

    <!-- Bootstrap JS and Popper.js -->
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.min.js"></script>

    <!-- Sidebar Toggle Script -->
    <script>
        const sidebar = document.querySelector('.sidebar');
        const toggleBtn = document.querySelector('#sidebar-toggle');
        toggleBtn.addEventListener('click', function () {
            sidebar.classList.toggle('active');
        });

        // Filter Books Script
        document.getElementById('searchInput').addEventListener('input', function () {
            const filter = this.value.toLowerCase();
            const rows = document.querySelectorAll('#booksTable tr');
            rows.forEach(row => {
                const title = row.children[0].textContent.toLowerCase();
                const author = row.children[1].textContent.toLowerCase();
                const year = row.children[2].textContent.toLowerCase();
                if (title.includes(filter) || author.includes(filter) || year.includes(filter)) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        });
    </script>
</body>
</html>
