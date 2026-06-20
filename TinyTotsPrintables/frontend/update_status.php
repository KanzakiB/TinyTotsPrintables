<?php
session_start();
include ('C:\XAMPP\htdocs\TinyTotsPrintables\database\dbconnection.php');

if (isset($_POST['order_id']) && isset($_POST['status_id'])) {
    $order_id = $_POST['order_id'];
    $status_id = $_POST['status_id'];  
    
    $query = "UPDATE order_list SET status_id = ? WHERE order_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ii", $status_id, $order_id);
    $stmt->execute();
    $stmt->close();
    
    echo "Status updated successfully";
} else {
    echo "Invalid request";
}
?>
