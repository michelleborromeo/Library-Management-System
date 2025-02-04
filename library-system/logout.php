<?php
session_start();
session_destroy(); // Destroy the session

// Correct absolute path to index.php
header('Location:/library-system/index.php');
exit();
?>
