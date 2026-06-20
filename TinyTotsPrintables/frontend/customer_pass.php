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
   SELECT c.first_name, c.profile_pic, c.passw, a.house_no, a.street_name, a.barangay, a.city, a.postal_code
    FROM customer c
    LEFT JOIN customer_address a ON c.customer_id = a.customer_id
    WHERE c.customer_id = ?

";

$stmt = $conn->prepare($query);
$stmt->bind_param("i", $customer_id);
$stmt->execute();
$stmt->store_result();
$stmt->bind_result($firstName, $profilePic, $passw, $houseNo, $streetName, $barangay, $city, $postalCode);
$stmt->fetch();
$stmt->close();
$profilePicBase64 = base64_encode($profilePic);


if (isset($_POST['check_current_password'])) {
    $currentPass = $_POST['check_current_password'];

    if (password_verify($currentPass, $passw)) {
        echo 'valid';
    } else {
        echo 'invalid';
    }
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $currentPass = $_POST['passw'];
    $newPass = $_POST['new_passw'];
    $confirmPass = $_POST['confirm_passw'];

    $errors = [];

    if (!password_verify($currentPass, $passw)) {
        $errors['current_pass'] = 'Current password is incorrect.';
    }

    if (strlen($newPass) < 8) {
        $errors['new_pass'] = 'Password must be at least 8 characters long.';
    }
    if (!preg_match('/[A-Z]/', $newPass)) {
        $errors['new_pass'] .= ' Password must contain at least one uppercase letter.';
    }
    if (!preg_match('/[0-9]/', $newPass)) {
        $errors['new_pass'] .= ' Password must contain at least one number.';
    }
    if (!preg_match('/[\W_]/', $newPass)) {
        $errors['new_pass'] .= ' Password must contain at least one special character.';
    }

    if ($newPass !== $confirmPass) {
        $errors['confirm_pass'] = 'New password and confirmation do not match.';
    }

    if (!empty($errors)) {
        echo json_encode(['status' => 'error', 'errors' => $errors]);
        exit();
    }

    $hashedNewPass = password_hash($newPass, PASSWORD_DEFAULT);
    $updateQuery = "UPDATE customer SET passw = ? WHERE customer_id = ?";
    $updateStmt = $conn->prepare($updateQuery);
    $updateStmt->bind_param("si", $hashedNewPass, $customer_id);
    $updateStmt->execute();

    if ($updateStmt->affected_rows > 0) {
        $_SESSION['password_updated'] = true;
        exit();
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Failed to update password.']);
    }

    $updateStmt->close();
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Password Modification</title>
  <link rel="stylesheet" type="text/css" href="http://localhost/TinyTotsPrintables/frontend/css/customerpass.css">
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css">
  <link href="https://fonts.googleapis.com/css2?family=Fredoka" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" crossorigin="anonymous" referrerpolicy="no-referrer" />
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
            <div class="nava">
                <i class="fa-solid fa-location-dot"></i>
                <a href="address.php">Address</a>
            </div>
            <div class="nava">
                <i class="fa-solid fa-envelope"></i>
                <a href="email_security.php">Change Email</a> 
            </div>
            <div class="nava active">
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
            <h3>My Password</h3>
            <p>Manage your password for security</p>
        </div>
        <hr class="linebr">
        <div class="pass-change-container">
            <form method="post" id="newPasswordContainer">
                <div class="first-container">
                <label for="current-pass">Current Password</label>
                    <div class="current-container">
                        <input type="password" name="passw" id="currentPass"  required>
                        <span class="current-icon" style="display: none;"><i class="fas fa-eye"></i></span>
                    </div>
                    <p class="error-message1" id="error-message1"></p>  <!-- This show an error message if current password is wrong  -->


                <label for="new-pass">New Password</label>
                    <div class="new-container">
                        <input type="password" name="new_passw" id="newPass"  onfocus="showRequirements()" onkeyup="validatePassword()">
                        <span class="new-icon" style="display: none;"><i class="fas fa-eye"></i></span>
                    </div>
                    <p class="error-message2" id="error-message2"></p>  <!--This show an error message if the requirements are not met  -->


                <label for="confirm-pass">Confirm New Password</label>
                    <div class="confirm-container">
                        <input type="password" name="confirm_passw" id="confirmPass" >
                        <span class="cfw-icon" style="display: none;"><i class="fas fa-eye"></i></span>
                    </div>
                    <p class="error-message3" id="error-message3"></p>  <!--This show an error message if new password and confirm password do not match -->

                    
                <button type="submit" id="saveNewPass">Save</button>
                </div>
                <div class="sec-container">
                    <!-- Password requirements -->
                    <ul id="requirements" class="requirements" style="display: none;">
                        <li id="length"> Must be at least 8 characters long</li>
                        <li id="capital"> Must have at least one capital letter</li>
                        <li id="number"> Must have at least one number</li>
                        <li id="special"> Must have at least one special character</li>
                    </ul>
                </div>
                
            </form>
        </div>
    </div>
</div>

<!-- Success Modal -->
<div id="success-modal" class="modal" style="display: none;">
    <div class="modal-content">
        <p>Password Updated successfully!</p>
        <div class="modalbtn">
            <button id="modal-ok-button">OK</button>
        </div>
    </div>
</div>


<script>
document.addEventListener("DOMContentLoaded", function () {
    const currentPasswordField = document.getElementById("currentPass");
    const newPasswordField = document.getElementById("newPass");
    const confirmPasswordField = document.getElementById("confirmPass");

    const currentPasswordToggleIconContainer = document.querySelector(".current-container .current-icon");
    const newPasswordToggleIconContainer = document.querySelector(".new-container .new-icon");
    const confirmPasswordToggleIconContainer = document.querySelector(".confirm-container .cfw-icon");

    currentPasswordField.addEventListener("input", function () {
        toggleIcon(currentPasswordField, currentPasswordToggleIconContainer);
        validateCurrentPassword();
    });

    newPasswordField.addEventListener("input", function () {
        toggleIcon(newPasswordField, newPasswordToggleIconContainer);
        validatePassword();
        validateConfirmPassword();
    });

    confirmPasswordField.addEventListener("input", function () {
        toggleIcon(confirmPasswordField, confirmPasswordToggleIconContainer);
        validateConfirmPassword();
    });

    function toggleIcon(passwordField, iconContainer) {
        if (passwordField.value.length > 0) {
            iconContainer.style.display = "inline";
        } else {
            iconContainer.style.display = "none";
        }
    }

    function validateCurrentPassword() {
        const currentPassword = currentPasswordField.value;
        const errorMessage1 = document.getElementById("error-message1");
        errorMessage1.textContent = ''; // Clear previous error messages

        if (currentPassword.length > 0) {
            // Send an AJAX request to check the current password
            $.ajax({
                url: '', // The current PHP file
                type: 'POST',
                data: { check_current_password: currentPassword },
                success: function(response) {
                    if (response === 'invalid') {
                        errorMessage1.textContent = "Current password is incorrect.";
                    } else {
                        errorMessage1.textContent = ''; // Clear error message if valid
                    }
                }
            });
        }
    }

    function validatePassword() {
        const password = newPasswordField.value;
        const errorMessage2 = document.getElementById("error-message2");
        errorMessage2.textContent = '';

        if (password.length < 8 || !/[A-Z]/.test(password) || !/\d/.test(password) || !/[!@#$%^&*(),.?":{}|<>]/.test(password)) {
            errorMessage2.textContent = "Password requirements not met";
        }
    }

    function validateConfirmPassword() {
        const newPassword = newPasswordField.value;
        const confirmPassword = confirmPasswordField.value;
        const errorMessage3 = document.getElementById("error-message3");
        errorMessage3.textContent = ''; // Clear previous error messages

        if (confirmPassword !== newPassword) {
            errorMessage3.textContent = "Passwords do not match.";
        }
    }
});

document.getElementById("newPass").addEventListener("focus", function () {
    document.getElementById("requirements").style.display = "block"; 
});


document.addEventListener("DOMContentLoaded", function () {
    const currentPasswordField = document.getElementById("currentPass");
    const newPasswordField = document.getElementById("newPass");
    const confirmPasswordField = document.getElementById("confirmPass");

    const currentPasswordToggleIconContainer = document.querySelector(".current-container .current-icon");
    const newPasswordToggleIconContainer = document.querySelector(".new-container .new-icon");
    const confirmPasswordToggleIconContainer = document.querySelector(".confirm-container .cfw-icon");

    currentPasswordField.addEventListener("input", function () {
        if (currentPasswordField.value.length > 0) {
            currentPasswordToggleIconContainer.style.display = "inline";
        } else {
            currentPasswordToggleIconContainer.style.display = "none";
        }
    });

    newPasswordField.addEventListener("input", function () {
        if (newPasswordField.value.length > 0) {
            newPasswordToggleIconContainer.style.display = "inline";
        } else {
            newPasswordToggleIconContainer.style.display = "none";
        }
    });

    confirmPasswordField.addEventListener("input", function () {
        if (confirmPasswordField.value.length > 0) {
            confirmPasswordToggleIconContainer.style.display = "inline";
        } else {
            confirmPasswordToggleIconContainer.style.display = "none";
        }
    });

    // Password field 
    currentPasswordToggleIconContainer.addEventListener("click", function () {
        const icon = currentPasswordToggleIconContainer.querySelector("i");
        if (currentPasswordField.type === "password") {
            currentPasswordField.type = "text";
            icon.classList.remove("fa-eye");
            icon.classList.add("fa-eye-slash");
        } else {
            currentPasswordField.type = "password";
            icon.classList.remove("fa-eye-slash");
            icon.classList.add("fa-eye");
        }
    });

    // New Password field 
    newPasswordToggleIconContainer.addEventListener("click", function () {
        const icon = newPasswordToggleIconContainer.querySelector("i");
        if (newPasswordField.type === "password") {
            newPasswordField.type = "text";
            icon.classList.remove("fa-eye");
            icon.classList.add("fa-eye-slash");
        } else {
            newPasswordField.type = "password";
            icon.classList.remove("fa-eye-slash");
            icon.classList.add("fa-eye");
        }
    });

    // Confirm New Password 
    confirmPasswordToggleIconContainer.addEventListener("click", function () {
        const icon = confirmPasswordToggleIconContainer.querySelector("i");
        if (confirmPasswordField.type === "password") {
            confirmPasswordField.type = "text";
            icon.classList.remove("fa-eye");
            icon.classList.add("fa-eye-slash");
        } else {
            confirmPasswordField.type = "password";
            icon.classList.remove("fa-eye-slash");
            icon.classList.add("fa-eye");
        }
    });
});

document.getElementById("newPass").addEventListener("focus", function () {
    document.getElementById("requirements").style.display = "block";  
});

function validatePassword() {
    const password = document.getElementById("newPass").value;
    const requirements = document.querySelectorAll("#requirements li");

    // Length requirement
    if (password.length >= 8) {
        document.getElementById("length").style.color = "green";
    } else {
        document.getElementById("length").style.color = "red";
    }

    // Capital letter requirement
    if (/[A-Z]/.test(password)) {
        document.getElementById("capital").style.color = "green";
    } else {
        document.getElementById("capital").style.color = "red";
    }

    // Number requirement
    if (/\d/.test(password)) {
        document.getElementById("number").style.color = "green";
    } else {
        document.getElementById("number").style.color = "red";
    }

    // Special character requirement
    if (/[!@#$%^&*(),.?":{}|<>]/.test(password)) {
        document.getElementById("special").style.color = "green";
    } else {
        document.getElementById("special").style.color = "red";
    }
}
document.addEventListener("DOMContentLoaded", function () {
    <?php if (isset($_SESSION['password_updated']) && $_SESSION['password_updated'] === true) : ?>
        const modal = document.getElementById('success-modal');
        modal.style.display = 'block';  

        const okButton = document.getElementById('modal-ok-button');
        okButton.addEventListener('click', function () {
            window.location.href = 'profile.php';  
        });

        <?php unset($_SESSION['password_updated']); ?>
    <?php endif; ?>
});

document.addEventListener("DOMContentLoaded", function () {
    const form = document.getElementById("newPasswordContainer");

    form.addEventListener("submit", function (event) {
        event.preventDefault(); // Prevent the form from submitting normally

        // Collect the form data
        const formData = new FormData(form);

        // Send an AJAX request to submit the form
        $.ajax({
            url: '', // The current page (this ensures the form submits to the same page)
            type: 'POST',
            data: formData,
            processData: false, // Do not process the data
            contentType: false, // Do not set content-type header
            success: function(response) {
                    window.location.reload();                
                    if (response === "Password Updated successfully!") {
                    const modal = document.getElementById('success-modal');
                    modal.style.display = 'block';  // Show the modal

                    // Optionally, add functionality for the OK button to close the modal
                    const okButton = document.getElementById('modal-ok-button');
                    okButton.addEventListener('click', function () {
                        window.location.href = 'profile.php';  // Redirect to profile page or any page after clicking OK
                    });
                }
            },
            error: function() {
                console.log('An error occurred while updating the password.');
            }
        });
    });
});



</script>


</body>
</html>
