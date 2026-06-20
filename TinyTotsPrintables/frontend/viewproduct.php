<?php
session_start();
include ('C:\XAMPP\htdocs\TinyTotsPrintables\database\dbconnection.php');

$searchQuery = "";
$priceMin = 0;
$priceMax = 1000;
$categoryFilter = 'all';  

if (isset($_GET['search'])) {
    $searchQuery = $_GET['search'];
}

if (!isset($_SESSION['customer_id'])) {
    header('Location: login.php'); 
    exit();
}

if (isset($_GET['prod_id']) && is_numeric($_GET['prod_id'])) {
    $prod_id = $_GET['prod_id'];
} else {
    die("Invalid product ID.");
}

// Prepare SQL query to fetch product details
$sql = "SELECT pl.prod_id, pl.prod_name, pl.prod_price, pl.prod_pages, pi.image_url, c.cat_name 
        FROM product_list pl 
        LEFT JOIN product_images pi ON pl.prod_id = pi.prod_id
        LEFT JOIN category_list c ON pl.cat_id = c.cat_id
        WHERE pl.prod_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $prod_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result && $result->num_rows > 0) {
    $product = $result->fetch_assoc(); 
} else {
    die("Product not found.");
}

// Fetch customer profile picture
$customer_id = $_SESSION['customer_id'];
$query = "SELECT c.profile_pic FROM customer c WHERE c.customer_id = ?";
$stmtProfile = $conn->prepare($query);
$stmtProfile->bind_param("i", $customer_id);
$stmtProfile->execute();
$stmtProfile->store_result();
$stmtProfile->bind_result($profilePic);
$stmtProfile->fetch();
$stmtProfile->close();

$profilePicBase64 = base64_encode($profilePic);

// Fetch similar products based on category
$similarProductsQuery = "
    SELECT pl.prod_id, pl.prod_name, pi.image_url 
    FROM product_list pl
    LEFT JOIN product_images pi ON pl.prod_id = pi.prod_id
    WHERE pl.cat_id = ?
    AND pl.prod_id != ? LIMIT 4";
$stmtSimilar = $conn->prepare($similarProductsQuery);
$stmtSimilar->bind_param("ii", $product['cat_id'], $prod_id);
$stmtSimilar->execute();
$similarResult = $stmtSimilar->get_result();

?>

<!DOCTYPE HTML>
<html lang="en-US">
<head>
    <title><?php echo htmlspecialchars($product["prod_name"]); ?> - TinyTots Printables</title>
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
        }

        .navbar-custom {
            background-color: #ffffff;
            padding: 10px;
            border-bottom: 5px solid #8900c9;
            height: 82px;
        }

        .navbar-custom .navbar-brand {
            color: black;
            font-weight: bold;
        }

        .navbar-custom .nav-link {
            color: black;
            margin-right: 15px;
        }

        .navbar-custom .bold-link {
            font-weight: bold;
        }

        .navbar-custom .nav-icons i {
            font-size: 1.5rem;
            color: black;
            margin-left: 15px;
            cursor: pointer;
        }

        .product-detail-container {
        display: flex;
        justify-content: center;
        margin-top: 50px;
        padding: 0 15px;
    }

    .product-container {
        background-color: white;
        box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.1);
        padding: 30px;
        border-radius: 8px;
        display: flex;
        max-width: 1000px;
        width: 100%;
        height:500px;
    }

    .product-image {
        flex: 0 0 40%;
        margin-right: 30px;
        display: flex;
        justify-content: center;
        align-items: center;
    }

    .product-image img {
        max-width: 100%;
        max-height: 450px;
        object-fit: contain;
    }

    .product-info {
        margin-top: 22px;
        flex: 0 0 60%;
        padding: 0 20px;
    }

    .similar-products-section {
        margin-top: 50px;
        padding: 0 15px;
    }

    .product-grid {
    display: flex;
    flex-wrap: wrap;
    gap: 0.7rem;
    justify-content: flex-start;
    margin-top: 20px;
}

    .card {
        margin-bottom: 1.5rem;
        max-width: 100%;
        border-radius: 8px;
        box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.1);
    }

    .card-body {
        padding: 15px;
    }

    .btn-custom {
        background-color: #8900c9;
        color: white;
        border: none;
        padding: 5px;
        width: 50%;
        font-size: 20px;
    }

    .btn-custom:hover {
        background-color: #6b21a8;
    }
</style>
</head>
<body>

    <nav class="navbar navbar-expand-lg navbar-custom sticky" id="myHeader">
        <div class="container-fluid">
            <a class="navbar-brand" href="shopnow.php">
                <img src="http://localhost/TinyTotsPrintables/frontend/images/navbarlogo.png" alt="TinyTots Logo" class="img-fluid" style="max-height: 50px; margin-left: 87px;">
            </a>
            <div class="nav-icons d-flex align-items-center ms-auto">
                <a href="profile.php" title="profile" style="margin-left: 20px;">
                    <img src="data:image/jpeg;base64,<?php echo $profilePicBase64; ?>" alt="Profile Picture" style="max-width: 40px; max-height: 40px; border-radius: 50%;">
                </a>
            </div>
        </div>
    </nav>

    <div class="container product-detail-container">
        <!-- Product container with white background and shadow -->
        <div class="product-container">
            <div class="product-image">
            <img src="../backend/<?php echo htmlspecialchars($product['image_url']); ?>" alt="<?php echo htmlspecialchars($product['prod_name']); ?>">
            </div>
            <div class="product-info">
                <h1><?php echo htmlspecialchars($product['prod_name']); ?></h1><br><br>
                <p><strong>Price:</strong> ₱<?php echo number_format($product['prod_price'], 2); ?></p><br>
                <p><strong>Category:</strong> <?php echo htmlspecialchars($product['cat_name']); ?></p><br>
                <p><strong>Pages:</strong> <?php echo htmlspecialchars($product['prod_pages']); ?></p><br><br>
                <a href="cart.php?prod_id=<?php echo $product['prod_id']; ?>" class="btn btn-custom">Add to Cart</a>
            </div>
        </div>
    </div>

    <!-- Add this after the product-detail-container div -->
    <?php if ($similarResult->num_rows > 0): ?>
        <div class="container mt-4">
            <h3 class="mb-4">Similar Products</h3>
            <div class="row product-grid" id="similarProductGrid">
                <?php while ($similarProduct = $similarResult->fetch_assoc()): ?>
                    <div class="col-md-3 col-sm-6 col-12">
                        <div class="card">
                            <img src="../backend/<?php echo htmlspecialchars($similarProduct['image_url']); ?>" 
                                class="card-img-top" 
                                alt="<?php echo htmlspecialchars($similarProduct['prod_name']); ?>">
                            <div class="card-body text-center">
                                <h5 class="card-title"><?php echo htmlspecialchars($similarProduct['prod_name']); ?></h5>
                                <a href="viewproduct.php?prod_id=<?php echo $similarProduct['prod_id']; ?>" 
                                class="btn btn-custom">View</a>
                            </div>
                        </div>
                    </div>
                <?php endwhile; ?>
            </div>
        </div>
    <?php endif; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
