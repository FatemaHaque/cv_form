<?php
session_start();

if (!isset($_SESSION['username'])) {
    header("Location: index.php");
    exit();
}

require_once 'db_connect.php'; 
$username = $_SESSION['username'];
$upload_dir = "cv_uploads/";

// Check if form was submitted
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_FILES["cv_file"])) {
    $file_name = basename($_FILES["cv_file"]["name"]);
    $target_file = $upload_dir . $file_name;
    $fileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

    // Check if file already exists
    if (file_exists($target_file)) {
        $_SESSION['upload_message'] = "Sorry, file already exists!";
        header("Location: home.php");
        exit();
    }

    // Check file size (limit it to 5MB)
    if ($_FILES["cv_file"]["size"] > 5000000) {
        $_SESSION['upload_message'] = "Sorry, your file is too large!";
        header("Location: home.php");
        exit();
    }

    // Allow only PDF files
    if ($fileType != "pdf") {
        $_SESSION['upload_message'] = "Sorry, only PDF files are allowed!";
        header("Location: home.php");
        exit();
    }

    // If all checks passed, attempt to move the file
    if (move_uploaded_file($_FILES["cv_file"]["tmp_name"], $target_file)) {
        // Insert the file details into the database
        $cv_name = $_FILES["cv_file"]["name"];
        $cv_path = $target_file;

        $sql = "INSERT INTO cv_uploads (Username, cv_name, cv_path) VALUES (?, ?, ?)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$username, $cv_name, $cv_path]);

        // Set success message in session
        $_SESSION['upload_message'] = "The file has been uploaded!";
        $_SESSION['cv_uploaded'] = true;
        header("Location: home.php");
        exit();
    } else {
        // Display error message if file upload failed
        $_SESSION['upload_message'] = "Sorry, there was an error uploading your file!";
        header("Location: home.php");
        exit();
    }
}
?>