<?php
session_start();
include ('C:\XAMPP\htdocs\TinyTotsPrintables\database\dbconnection.php');

if (!isset($_SESSION['customer_id'])) {
    header('Location: login.php'); 
    exit();
}

// Function to fetch product details
function fetchProductDetails($conn, $prod_id) {
    $stmt = $conn->prepare("SELECT prod_id, prod_name, prod_price, prod_pages FROM product_list WHERE prod_id = ?");
    $stmt->bind_param("i", $prod_id);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->fetch_assoc();
}

function fetchProductImage($conn, $prod_id) {
    $stmt = $conn->prepare("SELECT image_url FROM product_images WHERE prod_id = ? LIMIT 1");
    $stmt->bind_param("i", $prod_id);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        return $row['image_url'];
    }
    return 'default.jpg'; 
}


// Check if prod_id is passed and update the cart
if (isset($_GET['prod_id'])) {
    $prod_id = intval($_GET['prod_id']);
    $action = $_GET['action'] ?? '';

    if (isset($_SESSION['cart'][$prod_id])) {
        if ($action === 'add') {
            $_SESSION['cart'][$prod_id]['quantity']++;
        } elseif ($action === 'subtract' && $_SESSION['cart'][$prod_id]['quantity'] > 1) {
            $_SESSION['cart'][$prod_id]['quantity']--;
        } elseif ($action === 'delete') {
            unset($_SESSION['cart'][$prod_id]);
        }
    } else {
        // Add product to cart if it doesn't exist
        $product = fetchProductDetails($conn, $prod_id);
        if ($product) {
            $_SESSION['cart'][$prod_id] = [
                'prod_name' => $product['prod_name'],
                'prod_price' => $product['prod_price'],
                'prod_pages' => $product['prod_pages'],
                'quantity' => 1,
            ];
        }
    }
}

// Check out one product at a time
if (isset($_GET['checkout'])) {
    $prod_id = intval($_GET['checkout']);
    unset($_SESSION['cart'][$prod_id]);
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

<!DOCTYPE HTML>
<html lang="en-US">
<head>
    <title>Cart - TinyTots Printables</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Fredoka" rel="stylesheet">

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: "Fredoka", sans-serif;
        }
    body {
    background-image: url('images/background.png');
    background-size: cover;
    background-repeat: no-repeat;
    background-attachment: fixed;
    margin: 0;
    padding: 0;
    height: 100vh;
    overflow-x: hidden;
    padding-bottom: 15px;
}

.container {
    padding-bottom: 15px; 
}


    .navbar-custom {
        background-color: #ffffff;
        padding: 10px;
        border-bottom: 5px solid #8900c9;
        height: 80px;
    }

    .navbar-custom .navbar-brand {
        color: black;
        font-weight: bold;
    }

    .navbar-custom .nav-link {
        color: black !important;
        font-weight: bold;
    }

    .navbar-custom .nav-link:hover {
        color: black !important;
        text-decoration: none;
    }

    .navbar-custom .nav-icons i {
        font-size: 1.5rem;
        color: black;
        margin-left: 15px;
    }

    .cart-item {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 20px;
        padding: 10px;
        background-color: white;
        border-radius: 8px;
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        position: relative;
    }

    .cart-item img {
        width: 150px;  
        height: 150px;
        object-fit: contain;
        border-radius: 8px;
    }

    .cart-item-details {
        margin-left: 20px;
        flex-grow: 1;
        padding: 0 15px;
    }

    .btn-custom {
        background-color: #8900c9;
        color: white;
        border: none;
        padding: 10px 20px;
        width: 100%;
    }

    .btn-custom:hover {
        background-color: #6b21a8;
    }

    .quantity-btn {
        width: 35px; 
        height: 35px; 
        text-align: center;
        font-size: 20px; 
        border: 2px solid #8900c9; 
        background-color: #fff;
        cursor: pointer;
        border-radius: 5px;
        transition: background-color 0.3s ease, color 0.3s ease;
    }

    .quantity-btn:hover {
        background-color: #8900c9;
        color: #fff;
    }

    .quantity-display {
        font-size: 18px;
        font-weight: bold;
        color: #333;
        margin: 0 15px;
    }

    .cart-item-details div {
        display: flex;
        align-items: center;
    }

    .cart-actions {
        display: flex;
        flex-direction: column;
        align-items: flex-end;
    }

    .cart-actions .btn {
        margin-top: 5px;
    }

    .remove-btn {
        position: absolute;
        top: 10px; 
        right: 10px; 
        background: transparent;
        border: none;
        font-size: 20px;
        cursor: pointer;
    }
    .remove-btn i {
        color: #8900c9;  
    }

    .checkout-btn {
        background-color: #8900c9;
        color: white;
        border: none;
        border-radius: 15px;
        padding: 10px 20px;
        width: auto;
        text-align: center;
        text-decoration: none; 
        position: fixed; bottom: 15px; right: 15px;
    }

    .checkout-btn:hover {
        background-color: #6b21a8;
    }

    .total-price {
        font-size: 25px;
        font-weight: bold;
        color: #8900c9;
        margin-top: 10px;
    }

    .total-label {
        font-size: 16px;
        font-weight: bold;
        color: #333;
        margin-right: 10px;
    }

    .total-price-container {
        display: flex;
        justify-content: flex-end;
        align-items: center;
        margin-top: 10px;
        margin-right: 30px;
    }

    .btn-primary{
        background-color: #8900c9;
        border: 1px solid #8900c9;
    }
    
    .btn-primary:hover{
        background-color: #6b21a8;
        border: 1px solid #6b21a8;
    }

    .total-price-container .total-label,
    .total-price-container .total-price {
        display: inline-block;
    }
</style>
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
                    <a href="profile.php" title="profile" style="margin-left: 20px;">
                        <img src="data:image/jpeg;base64,<?php echo $profilePicBase64; ?>" alt="Profile Picture" style="max-width: 40px; max-height: 40px; border-radius: 50%;">
                    </a>
                </div>
            </div>
        </nav>

        <div class="container mt-5">
            <h3>Your Shopping Cart</h3>
            <?php if (isset($_SESSION['cart']) && !empty($_SESSION['cart'])): ?>
                <div class="cart-items">
                    <?php foreach ($_SESSION['cart'] as $prod_id => $product): ?>
                        <?php $image_url = fetchProductImage($conn, $prod_id); ?>
                        <div class="cart-item">
                        <img src="../backend/<?php echo htmlspecialchars($image_url); ?>" alt="product image">
                        <div class="cart-item-details">
                                <h5><?php echo htmlspecialchars($product['prod_name']); ?></h5>
                                <p><strong>Pages:</strong> <?php echo htmlspecialchars($product['prod_pages']); ?> pages</p>
                                <p><strong>Price:</strong> ₱<?php echo number_format($product['prod_price'], 2); ?></p>
                                <div class="quantity-container">
                                    <button class="quantity-btn" onclick="window.location.href='cart.php?prod_id=<?php echo $prod_id; ?>&action=subtract'">-</button>
                                    <span class="quantity-display"><?php echo $product['quantity']; ?></span>
                                    <button class="quantity-btn" onclick="window.location.href='cart.php?prod_id=<?php echo $prod_id; ?>&action=add'">+</button>
                                </div>
                            </div>
                            <div class="cart-actions">
                                <button class="remove-btn" onclick="window.location.href='cart.php?prod_id=<?php echo $prod_id; ?>&action=delete'">
                                    <i class="fa fa-trash"></i>
                                </button>
                            </div>
                            <div class="total-price-container">
                                <span class="total-label">Total Price:</span>
                                <p class="total-price">₱<?php echo number_format($product['prod_price'] * $product['quantity'], 2); ?></p>
                            </div>
                            <div class="checkout-container">
                                <a href="checkout.php?prod_id=<?php echo $prod_id; ?>" class="btn btn-primary">Checkout</a>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <p>Your cart is empty.</p>
            <?php endif; ?>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

<?php $conn->close(); ?>
