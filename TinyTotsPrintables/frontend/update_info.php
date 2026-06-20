<?php
session_start();
include ('C:\XAMPP\htdocs\TinyTotsPrintables\database\dbconnection.php');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $firstName = $_POST['fname'] ?? null;
    $lastName = $_POST['lname'] ?? null;
    $phone = $_POST['phone_no'] ?? null;
    $birthdate = $_POST['birthdate'] ?? null;
    $age = $_POST['age'] ?? null;
    $customerID = $_SESSION['customer_id'];

    $query = "UPDATE customer SET first_name = ?, last_name = ?, contact_number = ?, birthdate = ?, age = ? WHERE customer_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ssssii", $firstName, $lastName, $phone, $birthdate, $age, $customerID);

    if ($stmt->execute()) {
        header("Location: profile.php?info_update_status=success");
    } else {
        header("Location: profile.php?info_update_status=error");
    }

    $stmt->close();
    $conn->close();
    exit();
}
?>
