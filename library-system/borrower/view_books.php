<?php
include '../db.php';
session_start();

// Ensure the user is a Borrower
if (!isset($_SESSION['role_id']) || $_SESSION['role_id'] != 3) {
    header('Location: ../login.php');
    exit();
}

// Fetch all available books
$sql_books = "SELECT * FROM books WHERE is_available = TRUE";
$stmt_books = $conn->query($sql_books);
$books = $stmt_books->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Books</title>
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
            .toggle-btn {
                display: block;
                font-size: 18px;
                color: white;
                background: #343a40;
                padding: 10px;
                cursor: pointer;
                position: fixed;
                top: 10px;
                left: 10px;
                z-index: 101;
            }
        }
    </style>
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-light bg-primary">
        <div class="container">
            <div class="col">
            <a class="navbar-brand text-white" href="#">Library Management</a>
            <div class="toggle-btn d-md-none" id="sidebar-toggle">
            &#9776;
            </div>
        </div>
    </div>
    </nav>
    <!-- Sidebar -->
    <div class="sidebar">
        <h2 class="text-white text-center">Borrower</h2>
        <a href="view_books.php">Books</a>
        <a href="borrow_book.php">Borrowed Books</a>
        <a href="../logout.php" class="btn btn-danger">Logout</a>
    </div>

    <!-- Toggle Button for Sidebar (on mobile)
    <div class="toggle-btn d-md-none" id="sidebar-toggle">
        &#9776; Menu
    </div> -->

    <!-- Main Content -->
    <div class="content">
        <h1>Available Books</h1>
        <div class="row">
            <?php foreach ($books as $book): ?>
            <div class="col-md-4">
                <div class="card mb-4">
                    <div class="card-body">
                        <h5 class="card-title"><?php echo htmlspecialchars($book['title']); ?></h5>
                        <p class="card-text">Author: <?php echo htmlspecialchars($book['author']); ?></p>
                        <p class="card-text">Year: <?php echo htmlspecialchars($book['published_year']); ?></p>
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
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
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
