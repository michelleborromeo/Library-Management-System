<?php
include '../db.php';
session_start();

// Ensure the user is a Librarian
if ($_SESSION['role_id'] != 1) {
    header('Location: ../login.php');
    exit();
}

// Edit Book functionality
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $book_id = $_POST['id'];
    $title = $_POST['title'];
    $author = $_POST['author'];
    $published_year = $_POST['published_year'];
    $category = $_POST['category']; // Get the category from the form
    $added_by = $_SESSION['user_id']; // Get the logged-in user's ID
    $image_path = null;

    // Handle image upload
    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $uploads_dir = '../assets/images'; // Directory where the image will be uploaded
        
        // Check if uploads directory exists, if not create it
        if (!is_dir($uploads_dir)) {
            mkdir($uploads_dir, 0777, true); // Create the directory with full permissions
        }

        $image_name = $_FILES['image']['name'];
        $image_tmp_name = $_FILES['image']['tmp_name'];
        $image_ext = pathinfo($image_name, PATHINFO_EXTENSION);
        $image_new_name = uniqid('book_', true) . '.' . $image_ext;
        $image_path = 'assets/images/' . $image_new_name;
        
        // Move the uploaded image to the 'assets/images' directory
        if (move_uploaded_file($image_tmp_name, $uploads_dir . '/' . $image_new_name)) {
            // Image uploaded successfully
        } else {
            $error = "Image upload failed!";
        }
    } else {
        // If no image was uploaded, use the current image
        $sql = "SELECT image_path FROM books WHERE book_id = :id";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':id', $book_id);
        $stmt->execute();
        $book = $stmt->fetch(PDO::FETCH_ASSOC);
        $image_path = $book['image_path'];  // Keep the existing image if no new image is uploaded
    }

    // Update book details including the category and image
    $sql = "UPDATE books SET title = :title, author = :author, published_year = :published_year, category = :category, added_by = :added_by, image_path = :image WHERE book_id = :id";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':title', $title);
    $stmt->bindParam(':author', $author);
    $stmt->bindParam(':published_year', $published_year);
    $stmt->bindParam(':category', $category);
    $stmt->bindParam(':added_by', $added_by);
    $stmt->bindParam(':image', $image_path);
    $stmt->bindParam(':id', $book_id);
    $stmt->execute();

    $success = "Book updated successfully!";
}

// Fetch the book to edit
if (isset($_GET['id'])) {
    $book_id = $_GET['id'];

    $sql = "SELECT * FROM books WHERE book_id = :id";  
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':id', $book_id);  
    $stmt->execute();
    $book = $stmt->fetch(PDO::FETCH_ASSOC);
} else {
    header('Location: manage_books.php');
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
</head>
<body>
    <div class="container mt-5">
        <h1>Edit Book</h1>
        
        <?php if (isset($success)) echo "<div class='alert alert-success'>$success</div>"; ?>
        <?php if (isset($error)) echo "<div class='alert alert-danger'>$error</div>"; ?>

        <!-- Edit Book Form -->
        <form method="POST" enctype="multipart/form-data">
            <input type="hidden" name="id" value="<?= $book['book_id'] ?>">

            <div class="mb-3">
                <label for="title" class="form-label">Book Title</label>
                <input type="text" id="title" name="title" class="form-control" value="<?= $book['title'] ?>" required>
            </div>
            <div class="mb-3">
                <label for="author" class="form-label">Author</label>
                <input type="text" id="author" name="author" class="form-control" value="<?= $book['author'] ?>" required>
            </div>
            <div class="mb-3">
                <label for="published_year" class="form-label">Published Year</label>
                <input type="number" id="published_year" name="published_year" class="form-control" value="<?= $book['published_year'] ?>" required>
            </div>
            <div class="mb-3">
                <label for="category" class="form-label">Category</label>
                <select id="category" name="category" class="form-control" required>
                    <option value="Engineering" <?= $book['category'] == 'Engineering' ? 'selected' : '' ?>>Engineering</option>
                    <option value="Environmental Science" <?= $book['category'] == 'Environmental Science' ? 'selected' : '' ?>>Environmental Science</option>
                    <option value="Social Science" <?= $book['category'] == 'Social Science' ? 'selected' : '' ?>>Social Science</option>
                    <option value="Natural Science" <?= $book['category'] == 'Natural Science' ? 'selected' : '' ?>>Natural Science</option>
                    <option value="Education" <?= $book['category'] == 'Education' ? 'selected' : '' ?>>Education</option>
                    <option value="Programming" <?= $book['category'] == 'Programming' ? 'selected' : '' ?>>Programming</option>
                </select>
            </div>
            <div class="mb-3">
                <label for="image" class="form-label">Book Image</label>
                <input type="file" id="image" name="image" class="form-control">
                <?php if ($book['image_path']): ?>
                    <img src="<?= '../' . $book['image_path'] ?>" alt="Book Image" class="mt-2" style="width: 100px; height: auto;">
                <?php endif; ?>
            </div>
            <button type="submit" class="btn btn-primary">Update Book</button>
        </form>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.min.js"></script>
</body>
</html>
