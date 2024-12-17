<?php
session_start();
session_destroy(); // Destroy the session

// Correct absolute path to index.php
header('Location: /php-108/library-system/index.php');
exit();
?>
