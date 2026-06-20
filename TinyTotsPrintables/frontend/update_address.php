<?php

session_start();
include ('C:\XAMPP\htdocs\TinyTotsPrintables\database\dbconnection.php');

if (!isset($_SESSION['customer_id'])) {
    header('Location: login.php');
    exit();
}

$customer_id = $_SESSION['customer_id'];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Fetch form values
    $house_no = $_POST['house_no'] ?? null;
    $street_name = $_POST['street_name'] ?? null;
    $barangay = $_POST['barangay'] ?? null;
    $city = $_POST['city'] ?? null;
    $postal_code = $_POST['postal_code'] ?? null;

    // Check if address already exists for this customer
    $query = "SELECT * FROM customer_address WHERE customer_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $customer_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // If address already exists, update it
        $query = "UPDATE customer_address SET house_no = ?, street_name = ?, barangay = ?, city = ?, postal_code = ? WHERE customer_id = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("sssssi", $house_no, $street_name, $barangay, $city, $postal_code, $customer_id);

        if ($stmt->execute()) {
            header("Location: address.php?info_update_status=success");
        } else {
            header("Location: address.php?info_update_status=error");
        }
    } else {
        // If no address found, insert new address
        $query = "INSERT INTO customer_address (customer_id, house_no, street_name, barangay, city, postal_code) VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("isssss", $customer_id, $house_no, $street_name, $barangay, $city, $postal_code);

        if ($stmt->execute()) {
            header("Location: address.php?info_update_status=success");
        } else {
            header("Location: address.php?info_update_status=error");
        }
    }

    $stmt->close();
    $conn->close();
    exit();
}
?>