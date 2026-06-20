<?php
session_start();
include ('C:\XAMPP\htdocs\TinyTotsPrintables\database\dbconnection.php');

if (!isset($_SESSION['customer_id'])) {
    header('Location: login.php'); 
    exit();
}


$customer_id = $_SESSION['customer_id'];
$maxFileSize = 2 * 1024 * 1024; // 2MB

if (isset($_FILES['profile_pic'])) {
    $file = $_FILES['profile_pic'];

    if ($file['error'] !== UPLOAD_ERR_OK) {
        header("Location: profile.php?status=error");
        exit();
    }

    if ($file['size'] > $maxFileSize) {
        header("Location: profile.php?status=file_too_big");
        exit();
    }

    $fileData = file_get_contents($file['tmp_name']);

    if (empty($fileData)) {
        header("Location: profile.php?status=no_file");
        exit();
    }

    $fileType = mime_content_type($file['tmp_name']);
    $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];

    if (!in_array($fileType, $allowedTypes)) {
        header("Location: profile.php?status=invalid_file");
        exit();
    }

    $stmt = $conn->prepare("UPDATE customer SET profile_pic = ? WHERE customer_id = ?");
    $stmt->bind_param("bi", $null, $customer_id);
    $stmt->send_long_data(0, $fileData);

    if ($stmt->execute()) {

        $_SESSION['profile_pic'] = 'data:' . $fileType . ';base64,' . base64_encode($fileData);

        header("Location: profile.php?status=success");
        exit();
    } else {
        header("Location: profile.php?status=error");
        exit();
    }

    $stmt->close();
} else {
    header("Location: profile.php?status=no_file");
    exit();
}

$conn->close();
?>
