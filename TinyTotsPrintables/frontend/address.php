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

$customer_id = $_SESSION['customer_id'];


$query = "
    SELECT c.first_name, c.profile_pic, a.house_no, a.street_name, a.barangay, a.city, a.postal_code
    FROM customer c
    LEFT JOIN customer_address a ON c.customer_id = a.customer_id
    WHERE c.customer_id = ?
";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $customer_id);
$stmt->execute();
$stmt->store_result();
$stmt->bind_result($firstName, $profilePic, $houseNo, $streetName, $barangay, $city, $postalCode);
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
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Fredoka" rel="stylesheet">
    <link rel="stylesheet" type="text/css" href="http://localhost/TinyTotsPrintables/frontend/css/address.css">


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

        .search-bar {
            display: flex;
            align-items: center;
        }

        .search-bar input {
            width: 250px;
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
                    <!-- Link to cart.php for the cart icon -->
                    <a href="profile.php" title="profile" style="margin-left: 20px;">
                        <img src="data:image/jpeg;base64,<?php echo $profilePicBase64; ?>" alt="Profile Picture" style="max-width: 40px; max-height: 40px; border-radius: 50%;">
                    </a>
                </div>
            </div>
        </nav>

<div class="main-container">
    <div class="profile-nav-container">
        <div class="profilepic-con">
            <img id="profilenav" src="data:image/jpeg;base64,<?php echo $profilePicBase64; ?>" alt="profile-picture">              
            <p id="customername"><?php echo htmlspecialchars($firstName); ?></p>
        </div>
        <hr class="linebr">
        <div class="profnav-con">
            <div class="nava">
                <i class="fa-solid fa-user"></i>
                <a href="profile.php">Profile</a> 
            </div>
            <div class="nava active">
                <i class="fa-solid fa-location-dot"></i>
                <a href="address.php">Address</a>
            </div>
            <div class="nava">
                <i class="fa-solid fa-envelope"></i>
                <a href="email_security.php">Change Email</a> 
            </div>
            <div class="nava">
                <i class="fa-solid fa-lock"></i>
                <a href="password_security.php">Change Password</a> 
            </div>
            <div class="nava">
                <i class="fa-solid fa-location-dot"></i>
                <a href="purchase.php">My Purchase</a>
            </div>
            <div class="nava">
                <i class="fa-solid fa-right-from-bracket"></i>
                <a href="logout.php">Logout</a>
            </div>
        </div>
    </div>
    <div class="profile-info-container">
        <div class="titleprofile">
            <h3>My Address</h3>
            <p>Manage your address</p>
        </div>
        <hr class="linebr">

        <div class="personal-info-container">
            <form id="customerinfo" method="post" action="update_address.php">
                <div class="rowinfo">
                    <div>
                        <label for="houseno">House Number</label> 
                        <input type="text" name="house_no" id="customer-fname" value="<?php echo htmlspecialchars($houseNo); ?>">  
                    </div>
                    <div>
                        <label for="street">Street Name</label>
                        <input type="text" name="street_name" id="customer-lname" value="<?php echo htmlspecialchars($streetName); ?>"> 
                    </div>
                    
                </div>
                
                <div class="rowinfo">
                    <div>
                        <label for="barangay">Barangay</label>  
                        <input type="text" name="barangay" id="customer-phone" value="<?php echo htmlspecialchars($barangay); ?>"> 
                    </div>
                    <div>
                        <label for="city">City</label> 
                        <input type="text" name="city" id="customer-city" value="<?php echo htmlspecialchars($city); ?>" >  
                    </div>
                </div>
                <div class="rowinfo">
                    <div>
                        <label for="code">Postal Code</label> 
                        <input type="number" name="postal_code" id="customer-postal" value="<?php echo htmlspecialchars($postalCode); ?>">  
                    </div> 
                </div>
                <div class="btn-cont">
                    <button type="submit" id="saveinfo">Save</button>
                </div>
            </form>
        </div>
    </div>
</div>


<!-- Modal for Profile Information Update -->
<div id="updateModalinfo" class="modal">
    <div class="modal-content">
        <p id="modal-message-update"></p>
        <div class="modalbtn">
            <button onclick="closeinfoModal()" id="okbtn">OK</button>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        const params = new URLSearchParams(window.location.search);
        const infoUpdateStatus = params.get('info_update_status');

        //information update modal
        if (infoUpdateStatus) {
            const infoUpdateModal = document.getElementById('updateModalinfo');
            const infoUpdateMessage = document.getElementById('modal-message-update');

            if (infoUpdateStatus === 'success') {
                infoUpdateMessage.textContent = 'Information Updated successfully!';
            } else if (infoUpdateStatus === 'error') {
                infoUpdateMessage.textContent = 'Failed to update your information.';
            } else if (infoUpdateStatus === 'invalid_input') {
                infoUpdateMessage.textContent = 'Please ensure all required fields are filled correctly.';
            }

            infoUpdateModal.style.display = 'flex';
            window.history.replaceState({}, document.title, window.location.pathname);
        }
    });

    function closeinfoModal() {
        document.getElementById('updateModalinfo').style.display = 'none';
    }

</script>

</body>
</html>
