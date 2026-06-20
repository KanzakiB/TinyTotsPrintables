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

$sql = "SELECT * FROM customer WHERE customer_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $customer_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $user = $result->fetch_assoc();

    $profilePic = $user['profile_pic']; 
    $firstName = htmlspecialchars($user['first_name']);
    $lastName = htmlspecialchars($user['last_name']);
    $email = htmlspecialchars($user['email']);
    $phone = htmlspecialchars($user['contact_number']);
    $birthdate = htmlspecialchars($user['birthdate']);
    $age = htmlspecialchars($user['age']);
} else {
    echo "User not found!";
    exit();
}
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
    <link rel="stylesheet" type="text/css" href="http://localhost/TinyTotsPrintables/frontend/css/profile.css">


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
                    <!-- Link to cart.php for the cart icon -->
                    <a href="profile.php" title="profile" style="margin-left: 20px;">
                    <?php
                if ($profilePic) {
                    echo '<img id="profilenav" src="data:image/jpeg;base64,' . base64_encode($profilePic) . '" alt="profile-picture" style="max-width: 40px; max-height: 40px; border-radius: 50%;">';
                } else {
                    echo '<img id="profilenav" src="images/default-profile.png" alt="profile-picture" style="max-width: 40px; max-height: 40px; border-radius: 50%;">';
                }
            ?>                    </a>
                </div>
            </div>
        </nav>

<div class="main-container">
    <div class="profile-nav-container">
        <div class="profilepic-con">
            <?php
                if ($profilePic) {
                    echo '<img id="profilenav" src="data:image/jpeg;base64,' . base64_encode($profilePic) . '" alt="profile-picture">';
                } else {
                    echo '<img id="profilenav" src="images/default-profile.png" alt="profile-picture">';
                }
            ?>
            <p id="customername"><?php echo $firstName; ?></p>
        </div>
        <hr class="linebr">
        <div class="profnav-con">
            <div class="nava active">
                <i class="fa-solid fa-user"></i>
                <a href="profile.php">Profile</a> 
            </div>
            <div class="nava">
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
            <h3>My Profile</h3>
            <p>Manage and protect  your account</p>
        </div>
        <hr class="linebr">
        <div class="profilephoto-display">
            <div class="firstdiv">
                <img id="profilephoto" src="data:image/jpeg;base64,<?php echo base64_encode($profilePic); ?>" alt="profile-picture">
                <div class="customer-info-container">
                    <p><?php echo $firstName . ' ' . $lastName; ?></p>
                    <p><?php echo $email; ?></p>
                </div>
            </div>
            <div class="button-upload-container" id="uploadButtonContainer">
                <form action="upload_profile_pic.php" method="post" enctype="multipart/form-data">
                        <div class="file-upload">
                            <input type="file" id="fileInput" name="profile_pic" accept="image/*">
                            <label for="fileInput">Choose File</label>                                
                        </div> 
                    <br><br>
                    <button class="upload-btn" type="submit">Update </button>
                </form>
            </div>
        </div>
        <div class="personal-info-container">
            <form id="customerinfo" method="post" action="update_info.php">
                <div class="rowinfo">
                    <div>
                        <label for="firstname">Firstname</label> 
                        <input type="text" name="fname" id="customer-fname" value="<?php echo $firstName; ?>">  
                    </div>
                    <div>
                        <label for="lastname">Lastname</label>
                        <input type="text" name="lname" id="customer-lname" value="<?php echo $lastName; ?>"> 
                    </div>
                    <div>
                        <label for="phonenum">Phone Number</label>  
                        <input type="number" name="phone_no" id="customer-phone" value="<?php echo $phone; ?>"> 
                    </div>
                </div>
                <label for="email">Email</label> 
                <input type="email" name="email" id="customer-email" value="<?php echo $email; ?>" readonly>  

                <div class="rowinfo">
                    <div>
                        <label for="birthdate">Birthdate</label> 
                        <input type="date" name="birthdate" id="customer-birthdate" value="<?php echo $birthdate; ?>">  
                    </div> <div>
                        <label for="age">Age</label> 
                        <input type="number" name="age" id="customer-age" value="<?php echo $age; ?>" readonly>  
                    </div>
                </div>
                <div class="btn-cont">
                    <button type="submit" id="saveinfo">Save</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!--modal for profile picture-->
<div id="updateModal" class="modal">
    <div class="modal-content">
        <p id="modal-message"></p>
        <div class="modalbtn">
            <button onclick="closeModal()" id="okbtn">OK</button>
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
    function closeModal() {
        document.getElementById('updateModal').style.display = 'none';
    }

    document.addEventListener('DOMContentLoaded', () => {
        const params = new URLSearchParams(window.location.search);
        const status = params.get('status');

        if (status) {
            const modal = document.getElementById('updateModal');
            const message = document.getElementById('modal-message');

            if (status === 'success') {
                message.textContent = 'Profile Picture Updated Successfully!';
            } else if (status === 'error') {
                message.textContent = 'Failed to update profile picture.';
            } else if (status === 'invalid_file') {
                message.textContent = 'Invalid file type. Please upload an image.';
            } else if (status === 'no_file') {
                message.textContent = 'No file selected. Please choose a file to upload.';
            } else if (status === 'file_too_big') {
                message.textContent = 'The file is too big. Please upload an image smaller than 2MB.';
            }

            modal.style.display = 'flex';

            window.history.replaceState({}, document.title, window.location.pathname);
        }

        const infoUpdateStatus = params.get('info_update_status');

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

    function calculateAge() {
        var birthdateInput = document.getElementById("customer-birthdate");
        var ageInput = document.getElementById("customer-age");
        
        var birthdate = new Date(birthdateInput.value);
        
        if (!isNaN(birthdate)) {
            var today = new Date();
            var age = today.getFullYear() - birthdate.getFullYear();
            var m = today.getMonth() - birthdate.getMonth();
            
            if (m < 0 || (m === 0 && today.getDate() < birthdate.getDate())) {
                age--;
            }
            
            ageInput.value = age;
        }
    }

    document.getElementById("customer-birthdate").addEventListener("input", calculateAge);

    window.onload = function() {
        calculateAge();
    };
</script>

</body>
</html>
