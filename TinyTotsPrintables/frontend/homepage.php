<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TinyTots Printables</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/aos@2.3.4/dist/aos.css">
    <link href="https://fonts.googleapis.com/css2?family=Fredoka" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css">
  <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Genty+Sans&display=swap">
</head>

            <style>
                body {
                    font-family: "Fredoka", sans-serif;
                }
                * {
                    margin: 0;
                    padding: 0;
                    }

                .sticky-header {
                    position: fixed;
                    top: 0;
                    left: 0;
                    width: 100%;
                    z-index: 1000; /* Ensures navbar stays on top */
                    background-color: white; /* Optional, ensures navbar background is visible */
                    box-shadow: 0 4px 2px -2px gray; /* Optional, gives shadow effect */
                }

                .hero-section {
            min-height: 700px;  /* Ensures it doesn't shrink below 600px in height */
            background-image: url('http://localhost/TinyTotsPrintables/frontend/images/mainbg.png');
            background-size: cover;
            background-repeat: no-repeat;
            background-position: center;
            color: #4c4c4c;
            padding: 6rem 0;
            position: relative;
        }



        .hero-section h1 {
            font-weight: bold;
            color: #000;
            font-size: 3rem; /* Larger font size */
            padding: 10px 20px; /* Adjust padding for text */
            margin-top: -50px; /* Adjust the position to bring the text closer */
        }

        @media (max-width: 768px) {
            .hero-section h1 {
                font-size: 2.5rem; /* Adjust for smaller screens */
                margin-top: -40px;
                
            }
        }

        @media (max-width: 480px) {
            .hero-section h1 {
                font-size: 2rem; /* Even smaller font size for very small screens */
                margin-top: -30px; /* Adjust margin for small screens */
            }
        }


                    .carousel {
                border: 5px solid #a86dff; /* Adds a purple border */
                border-radius: 10px; /* Matches the rounded corners of the images */
                padding: 10px; /* Adds some space inside the border */
                background-color: #ffffff; /* Adds a white background */
                box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1); /* Adds a subtle shadow for better visual appeal */
            }

                .carousel img {
                    width: 100%;
                    height: 300px; /* Fixed height for images */
                    object-fit: cover; /* Ensures the images fit well inside the defined height */
                    border-radius: 10px;
                }

                .carousel-caption h5 {
                    color: #a86dff; /* Sets the color of h5 to violet */
                    font-weight: bold; /* Optional: Makes the text bold */
                }

                .carousel-caption p {
                    color: #a86dff; /* Sets the color of p to violet */
                }
                .sticky-header {
                    position: sticky;
                    top: 0;
                    z-index: 1020;
                    background-color: #ffffff; /* Solid fallback color */
                    box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
                    border-bottom: 5px solid #8900c9; /* Increased border thickness to 4px */
            }


                .nav-link {
                    color:rgb(0, 0, 0);
                    font-weight: bold;
                    margin: 0 10px;
                }
                .nav-link:hover {
                    color: #a86dff;
                }
                .btn-primary {
            background-color: #a86dff;
            border: 5px solid #8900c9; /* Original border */
            animation: pulse 1.5s infinite;
            font-size: 1rem; /* Increase font size */
            border-radius: 8px; /* Optional: Round the corners */
            text-align: center; /* Ensure the text is centered */
            width: auto; /* Optional: You can use this if you want the width to adjust based on the content */
            max-width: 300px; /* Optional: Set a maximum width */
            transition: transform 0.3s ease, box-shadow 0.3s ease; /* Smooth transition for movement */
        }

        .btn-primary:hover {
            background-color: #8c55cc;
            border-color: #8c55cc; /* Change border color on hover */
            transform: translateX(20px) scale(1.05); /* Move to the right and increase size */
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2); /* Optional: Adds shadow on hover */
        }

        /* Bounce effect */
        @keyframes bounce {
            0%, 100% {
                transform: translateY(0); /* No movement at start and end */
            }
            50% {
                transform: translateY(-10px); /* Bounce up */
            }
        }

        .btn-primary:active {
            animation: bounce 0.5s ease; /* Bounce effect when the button is clicked */
        }

        /* Services Section */
        .services-section {
            background-color: #f4eeff;
            height: 88vh;  /* Keep the height of the section to cover the viewport */
            padding: 50px 0;
            text-align: center;
            display: flex;
            justify-content: flex-start; /* Align content to the top */
            align-items: center;
            flex-direction: column;
        }

        /* Services Section Heading */
        .services-section h2 {
            font-size: 3rem;  /* Larger font size */
            margin-top: 25px;  /* Reduced top margin to bring it closer to the top */
            margin-bottom: 20px;  /* Space below the heading */
            font-weight: bold;
            color: #333;
        }

            /* Service Box */
            .service-box {
                background: linear-gradient(to bottom, #9f43ff, #d34cff);
                border-radius: 15px;
                box-shadow: 0 8px 15px rgba(0, 0, 0, 0.2);
                color: white;
                padding: 30px;  /* Increased padding for larger box */
                margin: 15px auto;
                transition: transform 0.3s ease;
                height: 300px;  /* Increased height */
                display: flex;
                flex-direction: column;
                justify-content: space-between;
                align-items: center; /* Center the content horizontally and vertically */
            }

            .service-box i {
                font-size: 3rem;
                margin-bottom: 15px;
            }

            .service-box h4 {
                font-size: 1.5rem;
                margin-bottom: 15px;
            }

            .service-box p {
                font-size: 1rem;
            }

            .service-box:hover {
                transform: translateY(-10px);
                cursor: pointer;
            }
            .service-icon {
            width: 200px; /* Adjust the size as needed */
            height: 200px; /* Maintain aspect ratio */
            align-items: center;
        }
/* Learning Benefits Section */
.benefits-section {
    background-image: url('http://localhost/TinyTotsPrintables/frontend/images/bgbenefits.png');
    background-size: cover;  /* Make the background image cover the entire section */
    height: 70vh;  /* Increased height for a banner-like section */
    width: 1519.5px;
    padding: 20px 0;  /* Reduced padding to make it more compact */
    text-align: center;
    display: flex;
    justify-content: center;  /* Center content horizontally */
    align-items: center;  /* Center content vertically */
    flex-direction: column;
}

/* Learning Benefits Section Heading */
.benefits-section h2 {
    font-size: 3.5rem;  /* Larger font size to make it more prominent */
    margin-top: 0;  /* Remove top margin */
    margin-bottom: 20px;  /* Space below the heading */
    font-weight: bold;
    color: black;  /* White text color for better visibility */
    text-shadow: 3px 3px 5px rgba(255, 255, 255, 0.6)  /* Text shadow to make the text stand out */
}

/* Row for benefits boxes */
.benefits-section .row {
    display: flex;
    justify-content: center;  /* Center columns horizontally */
    align-items: center;  /* Vertically align columns */
    flex-wrap: wrap;  /* Allow wrapping of columns if necessary */
    gap: 20px;  /* Space between boxes */
}

/* Service Box */
.benefits-box {
    background: linear-gradient(to bottom, #1e90ff, #00bfff); /* Blue Gradient */
    border-radius: 15px;
    box-shadow: 0 8px 15px rgba(0, 0, 0, 0.2);
    color: white;
    padding: 20px;  /* Increased padding for larger box */
    margin: 15px auto;  /* Adjusted margin */
    transition: transform 0.3s ease;
    height: 25vh;  /* Adjusted height for a smaller box */
    display: flex;
    flex-direction: column;
    justify-content: center;  /* Center content vertically */
    align-items: center;  /* Center content horizontally */
    text-align: center;
    width: 100vh;  /* Adjust width for better fit */
    max-width:300px;  /* Limit max width to prevent large boxes */
}

/* Ensure icons inside benefits boxes are centered */
.benefits-box i {
    font-size: 4rem;  /* Increased icon size */
    margin-bottom: 15px;
}

/* Icon size adjustments */
.benefits-icon {
    width: 150px;  /* Adjust the size of icons */
    height: 150px;
    align-items: center;
}

/* Styling for box text */
.benefits-box h4 {
    font-size: 1.5rem;
    margin-bottom: 15px;
}

.benefits-box p {
    font-size: 1rem;
}

.benefits-box:hover {
    transform: translateY(-10px);
    cursor: pointer;
}


            /* Smooth scroll behavior */
            html {
                scroll-behavior: smooth;
            }

            /* Navbar Styles */
            .navbar {
                position: fixed;
                top: 0;
                width: 100%;
                z-index: 9999;
            }

            .nav-link {
                cursor: pointer;
            }

            /* Hide sections initially */
            .hidden {
                display: none;
            }
            html {
            scroll-behavior: smooth;
            scroll-padding-top: 80px; /* Adjust this value depending on your header height */
        }
        /* Add colors for each point */
.color-1 {
    color: #FF5733; /* Bright Orange */
    transition: color 0.5s ease-in-out;
}

.color-2 {
    color: #33FF57; /* Bright Green */
    transition: color 0.5s ease-in-out;
}

.color-3 {
    color: #3357FF; /* Bright Blue */
    transition: color 0.5s ease-in-out;
}

.color-4 {
    color: #FF33A1; /* Bright Pink */
    transition: color 0.5s ease-in-out;
}

/* Add animation effects for when the content becomes visible */
[data-aos="fade-up"] {
    opacity: 0;
    transform: translateY(50px);
    transition: opacity 0.6s ease, transform 0.6s ease;
}

[data-aos="fade-up"].aos-animate {
    opacity: 1;
    transform: translateY(0);
}

/* Optional: Change color on hover to enhance the interactive feel */
.color-1:hover {
    color: #FF4500; /* Darker Orange */
}

.color-2:hover {
    color: #2E8B57; /* Darker Green */
}

.color-3:hover {
    color: #1E3A8A; /* Darker Blue */
}

.color-4:hover {
    color: #D5006D; /* Darker Pink */
}
.why-us-section {
  
    background-image: url('http://localhost/TinyTotsPrintables/frontend/images/gridbg.png'); /* Optional image */
    width: 1519.5px;
    background-size: cover;
    background-position: center center;
    background-attachment: fixed;
    padding: 60px 0;
}
.footer {
    background-color: #a86dff; /* Violet background for the footer */
    width: 1519.5px;
    color: white;
    text-align: center;
    font-size: 1rem;
    margin-top: 50px;
}

.footer a {
    color: #ffffff;
    text-decoration: none;
    font-weight: bold;
}

.footer a:hover {
    color: #17e2e7; /* Hover effect for the link */
}
.footer-dark-violet {
    background-color: #5D2E92;
    width: 100%;
    position: relative;
    left: 0;
}

</style>

</head>
<body>
    
    <!-- Header -->
    <header class="sticky-header py-2">
        <div class="container d-flex justify-content-between align-items-center">
            <h1 class="logo" onclick="window.location.href='homepage.php';" style="cursor: pointer;">
                <img src="http://localhost/TinyTotsPrintables/frontend/images/navbarlogo.png" alt="TinyTots Printables Logo" class="img-fluid" style="max-height: 50px;">
            </h1>
            <nav>
                <ul class="nav">
                    <li class="nav-item"><a href="#home" class="nav-link">Home</a></li>
                    <li class="nav-item"><a href="#services" class="nav-link">Services</a></li>
                    <li class="nav-item"><a href="#learningbenefits" class="nav-link">Learning Benefits</a></li>
                    <li class="nav-item"><a href="#whyus" class="nav-link">Why Us</a></li>
                    <li class="nav-item"><a href="#contactus" class="nav-link">Contact Us</a></li>
                </ul>
            </nav>
        </div>
    </header>

    <!-- Hero Section -->
    <section class="hero-section text-center" id="home">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-md-6 text-start">
                    <h1 data-aos="fade-up">Empowering early learning through interactive and printable resources</h1>
                    <p class="mb-4" data-aos="fade-right"><i>TinyTots Printables offers interactive, printable educational resources designed to support preschool and kindergarten children's cognitive and motor development.</i></p>
                    <div class="col-md-6 d-flex justify-content-end">
                        <a href="signup.php" class="btn btn-primary btn-lg">Sign Up Now</a>
                    </div>
                </div>
                <div class="col-md-6">
                    <!-- Carousel -->
                    <div id="heroCarousel" class="carousel slide" data-bs-ride="carousel" data-aos="fade-up">
                        <div class="carousel-inner">
                            <div class="carousel-item active">
                                <img src="http://localhost/TinyTotsPrintables/frontend/images/first.png" alt="Slide 1">
                                
                            </div>
                            <div class="carousel-item">
                                <img src="http://localhost/TinyTotsPrintables/frontend/images/second.png" alt="Slide 2">
                                
                            </div>
                            <div class="carousel-item">
                                <img src="http://localhost/TinyTotsPrintables/frontend/images/third.png" alt="Slide 2">
                               
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="services-section" id="services">
    <div class="container">
        <h2>Our Services</h2>
        <br>
        <br>
        <div class="row">
            <div class="col-md-3">
            <div class="service-box">
                <img src="http://localhost/TinyTotsPrintables/frontend/images/worksheet.png" alt="Paint Brush Icon" class="service-icon">
                <h4>WORKSHEETS</h4>
            </div>
            </div>
            <div class="col-md-3">
                <div class="service-box">
                <img src="http://localhost/TinyTotsPrintables/frontend/images/coloring.png" alt="Paint Brush Icon" class="service-icon">
                    <h4>COLORING PAGES</h4>
                </div>
            </div>
            <div class="col-md-3">
                <div class="service-box">
                <img src="http://localhost/TinyTotsPrintables/frontend/images/flashcards.png" alt="Paint Brush Icon" class="service-icon">
                    <h4>FLASHCARDS</h4>
                </div>
            </div>
            <div class="col-md-3">
                <div class="service-box">
                <img src="http://localhost/TinyTotsPrintables/frontend/images/activitysheets.png" alt="Paint Brush Icon" class="service-icon">
                    <h4>ACTIVITY SHEETS</h4>
                 
                </div>
            </div>
        </div>
    </div>

    <br><br><br><br><br>
    <section class="benefits-section" id="learningbenefits">
    <div class="container">
        <h2>Learning Benefits</h2>
        <div class="row">
            <div class="col-md-3">
                <div class="benefits-box">
                    <i class="fas fa-lightbulb benefits-icon"></i> <!-- Light Bulb Icon for Creativity -->
                    <h4>Improves<br>Thinking<br>Skills<br></h4>
                </div>
            </div>
            <div class="col-md-3">
                <div class="benefits-box">
                    <i class="fas fa-brain benefits-icon"></i> <!-- Brain Icon for Brain Development -->
                    <h4>Encourages Creativity<br></h4>
                </div>
            </div>
            <div class="col-md-3">
                <div class="benefits-box">
                    <i class="fas fa-trophy benefits-icon"></i> <!-- Trophy Icon for Top Student -->
                    <h4>Promotes<br>Independent<br>Learning<br></h4>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Why Us Section -->
<section class="why-us-section" id="whyus">
    <div class="container">
        <h2 data-aos="fade-up">Why Us?</h2>
        <p class="mb-4" data-aos="fade-up" data-aos-delay="200">
            TinyTots Printables stands out as a trusted resource for preschool and kindergarten educators and parents.
            We offer high-quality, engaging, and educational printables that foster learning and development in young children.
        </p>
        <div class="row">
            <div class="col-md-3" data-aos="fade-up" data-aos-delay="300">
                <h3 class="color-1">Affordable</h3>
                <p class="color-1">We offer budget-friendly resources that ensure quality education for every child without breaking the bank.</p>
            </div>
            <div class="col-md-3" data-aos="fade-up" data-aos-delay="400">
                <h3 class="color-2">Accessible</h3>
                <p class="color-2">Our printable resources are easily accessible online, so you can download and start using them immediately.</p>
            </div>
            <div class="col-md-3" data-aos="fade-up" data-aos-delay="500">
                <h3 class="color-3">Customizable Options</h3>
                <p class="color-3">Our resources are flexible, allowing you to tailor them to meet the specific learning needs of your child or classroom.</p>
            </div>
            <div class="col-md-3" data-aos="fade-up" data-aos-delay="600">
                <h3 class="color-4">Designed for Early Childhood Education</h3>
                <p class="color-4">Each resource is specifically designed to support the cognitive, motor, and social development of young children.</p>
            </div>
        </div>
    </div>
</section>
<!-- Footer -->
<footer class="text-white py-5" style="background-color: #5D2E92; width: 100%; left: 0; position: relative;"id="contactus">
    <div class="container">
        <div class="row">
            <div class="col-md-6">
                <h5>Contact Us</h5>
                <p>If you have any questions or need support, feel free to reach out!</p>
                <ul class="list-unstyled">
                    <li><i class="fas fa-phone-alt"></i> <a href="tel:63+ 9683405955" class="text-white">09683405955</a></li>
                    <li><i class="fas fa-envelope"></i> <a href="mailto:info@tinytotsprintables.com" class="text-white">info@tinytotsprintables.com</a></li>
                    <li><i class="fas fa-map-marker-alt"></i> 123 TinyTots Street, Education City</li>
                </ul>
            </div>
            <div class="col-md-6">
                <h5>Follow Us</h5>
                <ul class="list-unstyled d-flex">
                    <li><a href="#" class="text-white me-3"><i class="fab fa-facebook-f"></i></a></li>
                    <li><a href="#" class="text-white me-3"><i class="fab fa-instagram"></i></a></li>
                    <li><a href="#" class="text-white me-3"><i class="fab fa-tiktok"></i></a></li>
                </ul>
            </div>
        </div>
        <div class="text-center mt-4">
            <p>&copy; 2024 TinyTots Printables. All Rights Reserved.</p>
        </div>
    </div>
</footer>






    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/aos@2.3.4/dist/aos.js"></script>
    <script>
        // Initialize AOS (Animate On Scroll)
        AOS.init();

        // Enable smooth scrolling behavior
        document.querySelector('.nav-link[href="#services"]').addEventListener('click', function(e) {
            e.preventDefault(); // Prevent default anchor click behavior
            document.querySelector('#services').scrollIntoView({
                behavior: 'smooth'
            });
        });

        // Optional: Scroll to Home section when clicking on "Home" nav link
        document.querySelector('.nav-link[href="#home"]').addEventListener('click', function(e) {
            e.preventDefault();
            document.querySelector('#home').scrollIntoView({
                behavior: 'smooth'
            });
        });
    </script>
</body>

</html>
