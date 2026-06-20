<?php
session_start();
include ('C:\XAMPP\htdocs\TinyTotsPrintables\database\dbconnection.php');


if (empty($_SESSION['cart'])) {
    header("Location: cart.php");
    exit();
}

if (!isset($_GET['prod_id']) || !isset($_SESSION['cart'][$_GET['prod_id']])) {
    header("Location: cart.php");
    exit();
}

$prod_id = $_GET['prod_id'];
$product = $_SESSION['cart'][$prod_id];

$customer_id = $_SESSION['customer_id'];
$customer_sql = "SELECT * FROM customer WHERE customer_id = $customer_id";
$customer_result = $conn->query($customer_sql);
$customer = $customer_result->fetch_assoc();

$address_sql = "SELECT * FROM customer_address WHERE customer_id = $customer_id";
$address_result = $conn->query($address_sql);
$address = $address_result->fetch_assoc();

$image_sql = "SELECT image_url FROM product_images WHERE prod_id = $prod_id";
$image_result = $conn->query($image_sql);
$image = $image_result->fetch_assoc();
$image_url = $image ? $image['image_url'] : 'images/default.jpg';

// Calculate printing fee based on number of pages
$printing_fee = 0;
if ($product['prod_pages'] >= 5) {
    $printing_fee = 40;
} elseif ($product['prod_pages'] >= 10) {
    $printing_fee = 80;
} elseif ($product['prod_pages'] >= 15) {
    $printing_fee = 130;
}
$printing_fee *= $product['quantity'];

$total_price = $product['prod_price'] * $product['quantity'];

$order_message = '';

if (isset($_POST['confirm_order'])) {
    $order_type = $_POST['order_type'];
    $_SESSION['order_type'] = $order_type;
    $payment_method = $_POST['payment_method'];
    $gcash_number = isset($_POST['gcash_number']) ? $_POST['gcash_number'] : '';
    $email = isset($_POST['email']) ? $_POST['email'] : '';  
    $shipping_fee = $order_type === 'Product and Deliver' ? 60 : 0; 

    
    if ($payment_method === 'Gcash') {
         $_SESSION['order_quantity'] = $product['quantity'];
        header("Location: gcash_payment.php?order_type=" . urlencode($order_type));
        exit();
    }

    if ($payment_method !== 'Gcash') {
        $type_id = 0;
        if ($order_type === 'Product Only') {
            $type_id = 1;
        } elseif ($order_type === 'Product and Deliver') {
            $type_id = 2;
        } elseif ($order_type === 'Product and Pickup') {
            $type_id = 3;
        }

        $status_id = 1;

        $stmt = $conn->prepare("
            INSERT INTO order_list (order_date, status_id, type_id, customer_id, shipping_fee, prod_id, quantity) 
            VALUES (NOW(), ?, ?, ?, ?, ?, ?)
        ");
        $stmt->bind_param(
            "iiidii", 
            $status_id, 
            $type_id, 
            $customer_id, 
            $shipping_fee, 
            $prod_id,
            $product['quantity'] 
        );

        
        if ($stmt->execute()) {
            unset($_SESSION['cart'][$prod_id]);
            $order_message = "Order placed successfully!";
        } else {
            $order_message = "Failed to place the order. Please try again.";
        }

        $stmt->close();
    }

    if ($order_type == 'Product Only') {
        if (empty($email)) {
            $order_message = "Please input your email to receive the product access link.";
        } else {
            $order_message = "Email received. Your product access link will be sent to: $email";
        }
    } elseif ($order_type == 'Product and Deliver') {
        $order_message = "Your order will be delivered by " . date('l, F j, Y', strtotime('+2 days')) . ". Thank you for ordering!";
    } elseif ($order_type == 'Product and Pickup') {
        $order_message = "Your order can be picked up by " . date('l, F j, Y', strtotime('+2 days')) . ". Thank you for ordering!";
    }
}



$customer_id = $_SESSION['customer_id'];

$query = "
    SELECT c.profile_pic
    FROM customer c
    WHERE c.customer_id = ?
";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $customer_id);
$stmt->execute();
$stmt->store_result();
$stmt->bind_result($profilePic);
$stmt->fetch();
$stmt->close();

$profilePicBase64 = base64_encode($profilePic);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Fredoka" rel="stylesheet">
    <link rel="stylesheet" type="text/css" href="http://localhost/TinyTotsPrintables/frontend/css/checkout.css">

    <style>
        body {
            background-image: url('images/background.png');
            background-size: cover;
            background-repeat: no-repeat;
            margin: 0;
            padding: 0;
            height: 100vh;
            overflow-x: hidden;
            padding-bottom: 15px; 
        }
    </style>
    <script>
        function updateFees() {
            const orderType = document.getElementById("order_type").value;
            const shippingFeeDisplay = document.getElementById("shipping_fee");
            const totalDisplay = document.getElementById("total_price");
            const basePrice = <?php echo $total_price + $printing_fee; ?>;
            let shippingFee = 0;

            if (orderType === "Product and Deliver") {
                shippingFee = 60;
            }

            shippingFeeDisplay.textContent = `₱${shippingFee.toFixed(2)}`;
            totalDisplay.textContent = `₱${(basePrice + shippingFee).toFixed(2)}`;
        }

        function toggleGcashField() {
            const paymentMethod = document.getElementById("payment_method").value;
            const gcashField = document.getElementById("gcash_field");
            const cashOnDeliveryOption = document.getElementById("cash_on_delivery_option");
            if (paymentMethod === "Gcash") {
                gcashField.style.display = "block";
                cashOnDeliveryOption.disabled = true;
            } else {
                gcashField.style.display = "none";
                cashOnDeliveryOption.disabled = false;
            }
        }

        function restrictPaymentMethods() {
            const orderType = document.getElementById("order_type").value;
            const paymentMethodSelect = document.getElementById("payment_method");
            const cashOnDeliveryOption = document.getElementById("cash_on_delivery_option");

            if (orderType === "Product Only") {
                paymentMethodSelect.value = "Gcash";
                cashOnDeliveryOption.disabled = true;
                document.getElementById("email_field").style.display = "block"; 
            } else {
                cashOnDeliveryOption.disabled = false;
                document.getElementById("email_field").style.display = "none"; 
            }
        }

        <?php if ($order_message): ?>
        window.onload = function() {
            var myModal = new bootstrap.Modal(document.getElementById('orderModal'));
            document.getElementById('orderMessage').innerHTML = "<?php echo $order_message; ?>";
            myModal.show();
        };
        <?php endif; ?>
    </script>
</head>
<body>

<div class="wrap">
        <nav class="navbar navbar-expand-lg navbar-custom sticky" id="myHeader">
            <div class="container-fluid">
                <a class="navbar-brand" href="shopnow.php">
                    <img src="http://localhost/TinyTotsPrintables/frontend/images/navbarlogo.png" alt="TinyTots Logo" class="img-fluid" style="max-height: 50px; margin-left: 87px;">
                </a>

                <div class="nav-icons d-flex align-items-center ms-auto">
                    <a href="cart.php" title="Cart"><i class="bi bi-cart"></i></a>
                </div>
                <a href="profile.php" title="profile" style="margin-left: 20px;">
                        <img src="data:image/jpeg;base64,<?php echo $profilePicBase64; ?>" alt="Profile Picture" style="max-width: 40px; max-height: 40px; border-radius: 50%;">
                    </a>
            </div>
        </nav>
<div class="container mt-5">
    <div class="checkout-container">
        <h3>Checkout</h3>

        <!-- Customer Details -->
        <div class="customer-details mb-3">
            <h4>Customer Information</h4>
            <p>Name: <?php echo htmlspecialchars($customer['first_name'] . ' ' . $customer['last_name']); ?></p>
            <p>Email: <?php echo htmlspecialchars($customer['email']); ?></p>
            <p>Phone Number: <?php echo htmlspecialchars($customer['contact_number']); ?></p>
            <p>Address: <?php echo htmlspecialchars($address['house_no'] . ', ' . $address['street_name'] . ', ' . $address['barangay'] . ', ' . $address['city'] . ', ' . $address['postal_code']); ?></p>
        </div>

        <!-- Order Details -->
        <form method="POST">
            <h4>Order Details</h4>
            <div class="checkout-item">
                <img src="../backend/<?php echo htmlspecialchars($image_url); ?>" alt="Product Image" style="width: 120px; height: 120px;">
                <div>
                    <h5><?php echo htmlspecialchars($product['prod_name']); ?></h5>
                    <p>Quantity: <?php echo $product['quantity']; ?></p>
                    <p>Pages: <?php echo $product['prod_pages']; ?> pages</p>
                    <p>Subtotal: ₱<?php echo number_format($total_price, 2); ?></p>
                </div>
            </div>

            <!-- Order Type -->
            <label>Order Type:</label>
            <select name="order_type" id="order_type" class="form-select" onchange="updateFees(); restrictPaymentMethods();">
                <option value="Select Order Type">Select Order Type</option>
                <option value="Product Only">Product Only</option>
                <option value="Product and Deliver">Product and Deliver</option>
                <option value="Product and Pickup">Product and Pickup</option>
            </select>

            <!-- Fees -->
            <p>Shipping Fee: <span id="shipping_fee">₱0.00</span></p>
            <p>Printing Fee: ₱<?php echo number_format($printing_fee, 2); ?></p>
            <p>Total: <span id="total_price">₱<?php echo number_format($total_price + $printing_fee, 2); ?></span></p>

            <!-- Payment Method -->
            <label>Payment Method:</label>
            <select name="payment_method" id="payment_method" class="form-select" onchange="toggleGcashField();">
                <option value="Select">Select Payment</option>
                <option value="Gcash">Gcash</option>
                <option value="Cash on Delivery" id="cash_on_delivery_option">Cash on Delivery</option>
            </select>

            <!-- Gcash Field -->
            <div id="gcash_field">
                <label>Gcash Number:</label>
                <input type="text" name="gcash_number" class="form-control" placeholder="Enter Gcash Number">
            </div>
        
            <button type="submit" name="confirm_order" class="btn btn-custom mt-3" id="confirmOrderButton">Confirm Order</button>
        </form>
    </div>
    

    <!-- Order Message Modal -->
<div class="modal fade" id="orderModal" tabindex="-1" aria-labelledby="orderModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="orderModalLabel">Order Status</h5>
            </div>
            <div class="modal-body" id="orderMessage"></div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" onclick="window.location.href='shopnow.php';">Okay</button>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
