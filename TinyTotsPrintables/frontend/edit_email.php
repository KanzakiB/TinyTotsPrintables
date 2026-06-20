<?php
session_start();
include ('C:\XAMPP\htdocs\TinyTotsPrintables\database\dbconnection.php');

if (!isset($_SESSION['customer_id'])) {
    echo "error: not_logged_in";
    exit();
}

$newEmail = trim($_POST['email']);

if (empty($newEmail)) {
    echo "error: email_empty";
    exit();
}

if (!filter_var($newEmail, FILTER_VALIDATE_EMAIL)) {
    echo "error: invalid_email";
    exit();
}

$query = "SELECT * FROM customer WHERE email = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("s", $newEmail);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    echo "error: email_exists";
    exit();
}

$stmt->close();

$customer_id = $_SESSION['customer_id'];
$query = "UPDATE customer SET email = ? WHERE customer_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("si", $newEmail, $customer_id);

if ($stmt->execute()) {
    echo "success: email_updated";
} else {
    echo "error: database_error";
}

$stmt->close();
$conn->close();
?>
