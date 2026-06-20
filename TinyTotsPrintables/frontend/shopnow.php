<?php
session_start();
include ('C:\xampp\htdocs\TinyTotsPrintables\database\dbconnection.php');

if (!isset($_SESSION['customer_id'])) {
    header('Location: login.php'); 
    exit();
}

$searchQuery = "";
$priceMin = 0;
$priceMax = 1000;
$categoryFilter = 'all';  

if (isset($_GET['search'])) {
    $searchQuery = $_GET['search'];
}

if (isset($_GET['price_min']) && isset($_GET['price_max'])) {
    $priceMin = $_GET['price_min'];
    $priceMax = $_GET['price_max'];
}

if (isset($_GET['category'])) {
    $categoryFilter = $_GET['category'];
}

$categoryQuery = "SELECT * FROM category_list";
$categoryResult = $conn->query($categoryQuery);

$sql = "SELECT 
            pl.prod_id, 
            pl.prod_name, 
            pl.prod_price, 
            pi.image_url 
        FROM 
            product_list pl 
        LEFT JOIN 
            product_images pi 
        ON 
            pl.prod_id = pi.prod_id
        WHERE 
            pl.prod_name LIKE '%$searchQuery%' 
            AND pl.prod_price BETWEEN $priceMin AND $priceMax";

if ($categoryFilter != 'all') {
    $sql .= " AND pl.cat_id = '$categoryFilter'";
}

$sql .= " GROUP BY pl.prod_id"; 

$result = $conn->query($sql);
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
    <title>TinyTots Printables</title>
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

        /* Custom styles */
        .product-grid {
            display: flex;
            flex-wrap: wrap;
            gap: .7 rem;
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

        .search-bar {
            display: flex;
            align-items: center;
        }

        .search-bar input {
            width: 250px;
        }

        .modal-dialog {
            position: fixed;
            top: 10%;
            right: 10px;
            width: 300px;
            margin-right: 10px;
        }

        .modal-content {
            width: 100%;
        }

        .btn-custom {
            background-color: #8900c9;
            color: white;
            border: none;
            padding: 5px;
            width: 100%;
        }

        .btn-custom:hover {
            background-color: #6b21a8;
        }

        .modal-title {
            color: #8900c9;
        }

        .btn-close {
            color: #8900c9;
        }

        .form-label {
            font-weight: bold;
        }

        input[type="radio"] + label {
            color: black;
            cursor: pointer;
            transition: color 0.3s ease-in-out;
        }

        input[type="radio"]:checked + label {
            color: #8900c9;
            font-weight: bold;
        }

        .modal-footer {
            display: flex;
            justify-content: center; 
            padding: 10px 0;
        }

        .modal-footer .btn {
            width: auto;
        }

        .price-range-container {
            display: flex;
            justify-content: space-between;
        }

        .price-range-container input {
            width: 45%;
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
                    <form class="search-bar" method="GET" action="shopnow.php">
                        <input type="text" class="form-control" id="searchInput" placeholder="Search for Products" name="search" value="<?php echo htmlspecialchars($searchQuery); ?>">
                        <button type="submit" class="btn btn-link ms-2" id="searchBtn">
                            <i class="bi bi-search" style="font-size: 1.5rem;"></i>
                        </button>
                    </form>
                    <!-- Link to cart.php for the cart icon -->
                    <a href="cart.php" title="Cart">
                        <i class="bi bi-cart" style="font-size: 1.5rem;"></i>
                    </a>
                    <!-- Filter icon, triggers modal -->
                    <i class="bi bi-funnel" title="Filters" data-bs-toggle="modal" data-bs-target="#filterModal"></i>
                    <!-- Link to cart.php for the cart icon -->
                    <a href="profile.php" title="profile" style="margin-left: 20px;">
                        <img src="data:image/jpeg;base64,<?php echo $profilePicBase64; ?>" alt="Profile Picture" style="max-width: 40px; max-height: 40px; border-radius: 50%;">
                    </a>
                </div>
            </div>
        </nav>

        <div class="container mt-4">
            <div class="row product-grid" id="productGrid">
            <?php 
                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        $imagePath = htmlspecialchars($row["image_url"]); 
                        echo '<div class="col-md-3 col-sm-6 col-12">';
                        echo '    <div class="card">';
                        echo '        <img src="../backend/' . $imagePath . '" class="card-img-top" alt="' . htmlspecialchars($row["prod_name"]) . '">';
                        echo '        <div class="card-body text-center">';
                        echo '            <h5 class="card-title">' . htmlspecialchars($row["prod_name"]) . '</h5>';
                        echo '            <p class="card-text">₱' . number_format($row["prod_price"], 2) . '</p>';
                        echo '            <a href="viewproduct.php?prod_id=' . $row["prod_id"] . '" class="btn btn-custom">View</a>';
                        echo '        </div>';
                        echo '    </div>';
                        echo '</div>';
                    }
                } else {
                    echo '<p>No products found</p>';
                }
                ?>
            </div>
        </div>
    </div>

    <!-- Filter Modal -->
    <div class="modal fade" id="filterModal" tabindex="-1" aria-labelledby="filterModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="filterModalLabel">Filter Products</h5>
                </div>
                <div class="modal-body">
                    <form method="GET" action="shopnow.php">
                        <div class="form-group">
                            <label for="price_min" class="form-label">Price Range:</label>
                            <div class="price-range-container">
                                <input type="number" class="form-control" name="price_min" min="0" max="1000" value="<?php echo $priceMin; ?>" />
                                <input type="number" class="form-control" name="price_max" min="0" max="1000" value="<?php echo $priceMax; ?>" />
                            </div>
                        </div>
                        <div class="form-group mt-3">
                            <label for="category">Category</label>
                            <select class="form-control" name="category" id="category">
                                <option value="all" <?php echo $categoryFilter == 'all' ? 'selected' : ''; ?>>All Categories</option>
                                <?php 
                                if ($categoryResult->num_rows > 0) {
                                    while ($category = $categoryResult->fetch_assoc()) {
                                        echo '<option value="' . $category["cat_id"] . '" ' . ($category["cat_id"] == $categoryFilter ? 'selected' : '') . '>' . $category["cat_name"] . '</option>';
                                    }
                                }
                                ?>
                            </select>
                        </div>

                        <div class="modal-footer">
                            <button type="submit" class="btn btn-custom">Apply Filters</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>