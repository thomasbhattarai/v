<?php
    require_once('connection.php');
    session_start(); // Move session_start to very beginning
    
    if(isset($_POST['login']))
    {
        $email=$_POST['email'];
        $pass=$_POST['pass'];

        if(empty($email) || empty($pass))
        {
            echo '<script>showDialog("Please fill the blanks")</script>';
        }
        else{
            $query="SELECT * FROM users WHERE EMAIL='$email'";
            $res=mysqli_query($con,$query);
            if($row=mysqli_fetch_assoc($res)){
                $db_password = $row['PASSWORD'];
                if(md5($pass) == $db_password)
                {
                    $_SESSION['email'] = $email;
                    header("location: vehiclesdetails.php");
                    exit(); // Always call exit after header redirect
                }
                else{
                    $error_message = "Enter a proper password";
                }
            }
            else{
                $error_message = "Enter a proper email";
            }
        }
    }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>VeloRent - Premium Vehicle Rental Service</title>
    <script src="main.js" defer></script>
    <!-- Embedded CSS -->
    <style>
        /* Reset and Base Styles */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Poppins', sans-serif;
        }

        body {
            background: linear-gradient(135deg, #0c0c0c, #1a1a2e);
            color: #fff;
            min-height: 100vh;
            overflow-x: hidden;
            position: relative;
        }

        /* Loading Animation */
        .loading {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: #0c0c0c;
            display: flex;
            justify-content: center;
            align-items: center;
            z-index: 9999;
            transition: opacity 0.5s ease, visibility 0.5s ease;
        }

        .loading.hidden {
            opacity: 0;
            visibility: hidden;
        }

        .loader {
            width: 60px;
            height: 60px;
            border: 5px solid rgba(255, 255, 255, 0.1);
            border-radius: 50%;
            border-top-color: #ff7b00;
            animation: spin 1s ease-in-out infinite;
        }

        @keyframes spin {
            100% { transform: rotate(360deg); }
        }

        /* Navbar Styles */
        .navbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 20px 5%;
            background: rgba(0, 0, 0, 0.8);
            backdrop-filter: blur(10px);
            position: fixed;
            top: 0;
            width: 100%;
            z-index: 1000;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.3);
        }

        .navbar img {
            height: 50px;
            transition: transform 0.3s;
        }

        .navbar img:hover {
            transform: scale(1.05);
        }

        .menu ul {
            display: flex;
            list-style: none;
        }

        .menu li {
            margin-left: 40px;
        }

        .menu a {
            color: #fff;
            text-decoration: none;
            font-weight: 500;
            font-size: 1rem;
            transition: color 0.3s, border-bottom 0.3s;
            padding-bottom: 5px;
            position: relative;
        }

        .menu a::after {
            content: '';
            position: absolute;
            width: 0;
            height: 2px;
            bottom: 0;
            left: 0;
            background-color: #ff7b00;
            transition: width 0.3s;
        }

        .menu a:hover {
            color: #ff7b00;
        }

        .menu a:hover::after {
            width: 100%;
        }

        /* Hamburger Menu */
        .hamburger {
            display: none;
            flex-direction: column;
            cursor: pointer;
            z-index: 1001;
        }

        .hamburger span {
            width: 30px;
            height: 3px;
            background: #fff;
            margin: 4px 0;
            transition: 0.3s;
            border-radius: 2px;
        }

        .hamburger.active span:nth-child(1) {
            transform: rotate(45deg) translate(5px, 5px);
        }

        .hamburger.active span:nth-child(2) {
            opacity: 0;
        }

        .hamburger.active span:nth-child(3) {
            transform: rotate(-45deg) translate(7px, -6px);
        }

        /* Main Content Area */
        .hai {
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        /* Main Content Layout */
        .content {
            display: flex;
            flex-wrap: wrap;
            justify-content: space-between;
            align-items: center;
            padding: 150px 5% 80px;
            flex: 1;
            gap: 40px;
        }

        /* Hero Content */
        .hero-content {
            flex: 1;
            min-width: 300px;
            padding-right: 50px;
            animation: fadeInLeft 1s ease;
        }

        @keyframes fadeInLeft {
            from { opacity: 0; transform: translateX(-50px); }
            to { opacity: 1; transform: translateX(0); }
        }

        .hero-content h1 {
            font-size: 3.5rem;
            font-weight: 700;
            line-height: 1.2;
            margin-bottom: 20px;
            background: linear-gradient(45deg, #ff7b00, #ffaa00);
            -webkit-background-clip: text;
            background-clip: text;
            color: transparent;
        }

        .hero-content span {
            color: #fff;
            text-shadow: 0 0 10px rgba(255, 123, 0, 0.5);
        }

        .par {
            font-size: 1.1rem;
            line-height: 1.6;
            margin-bottom: 30px;
            color: #ccc;
            max-width: 600px;
        }

        .cn {
            background: linear-gradient(45deg, #ff7b00, #ff5500);
            border: none;
            padding: 15px 30px;
            border-radius: 50px;
            font-size: 1.1rem;
            font-weight: 600;
            cursor: pointer;
            transition: transform 0.3s, box-shadow 0.3s;
            box-shadow: 0 5px 15px rgba(255, 123, 0, 0.3);
        }

        .cn a {
            color: #fff;
            text-decoration: none;
        }

        .cn:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(255, 123, 0, 0.4);
        }

        /* Form Container */
        .form-container {
            flex: 1;
            min-width: 300px;
            max-width: 450px;
            animation: fadeInRight 1s ease;
        }

        @keyframes fadeInRight {
            from { opacity: 0; transform: translateX(50px); }
            to { opacity: 1; transform: translateX(0); }
        }

        .form {
            background: rgba(255, 255, 255, 0.05);
            backdrop-filter: blur(10px);
            padding: 40px;
            border-radius: 20px;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.5);
            border: 1px solid rgba(255, 255, 255, 0.1);
        }

        .form h2 {
            font-size: 2rem;
            margin-bottom: 30px;
            text-align: center;
            color: #ff7b00;
        }

        .form input[type="email"],
        .form input[type="password"],
        .form input[type="text"] {
            width: 100%;
            padding: 15px;
            margin-bottom: 20px;
            border: none;
            border-radius: 10px;
            background: rgba(255, 255, 255, 0.1);
            color: #fff;
            font-size: 1rem;
            outline: none;
            transition: background 0.3s, box-shadow 0.3s;
        }

        .form input:focus {
            background: rgba(255, 255, 255, 0.15);
            box-shadow: 0 0 10px rgba(255, 123, 0, 0.5);
        }

        .form input::placeholder {
            color: rgba(255, 255, 255, 0.6);
        }

        .btnn {
            background: linear-gradient(45deg, #ff7b00, #ff5500);
            color: #fff;
            font-weight: 600;
            cursor: pointer;
            transition: transform 0.3s, box-shadow 0.3s;
            width: 100%;
            padding: 15px;
            border: none;
            border-radius: 10px;
            font-size: 1.1rem;
        }

        .btnn:hover {
            transform: translateY(-3px);
            box-shadow: 0 5px 15px rgba(255, 123, 0, 0.4);
        }

        .link {
            text-align: center;
            margin-top: 20px;
            color: #ccc;
        }

        .link a {
            color: #ff7b00;
            text-decoration: none;
            font-weight: 600;
            transition: color 0.3s;
        }

        .link a:hover {
            color: #ffaa00;
            text-decoration: underline;
        }

        /* Error Message Styling */
        .error-message {
            background: rgba(255, 0, 0, 0.1);
            color: #ff6b6b;
            padding: 12px;
            border-radius: 8px;
            margin-bottom: 20px;
            text-align: center;
            border-left: 4px solid #ff0000;
            display: <?php echo isset($error_message) ? 'block' : 'none'; ?>;
        }

        /* Footer */
        footer {
            background: rgba(0, 0, 0, 0.9);
            padding: 40px 5%;
            text-align: center;
            margin-top: 50px;
        }

        footer p {
            margin-bottom: 20px;
            color: #aaa;
        }

        .socials {
            display: flex;
            justify-content: center;
            gap: 20px;
        }

        .socials a {
            color: #fff;
            font-size: 1.5rem;
            transition: color 0.3s, transform 0.3s;
        }

        .socials a:hover {
            color: #ff7b00;
            transform: translateY(-5px);
        }

        /* Responsive Design */
        @media (max-width: 992px) {
            .content {
                flex-direction: column;
                text-align: center;
                padding-top: 150px;
            }

            .hero-content {
                padding-right: 0;
                margin-bottom: 50px;
            }

            .form-container {
                width: 100%;
                max-width: 500px;
            }
        }

        @media (max-width: 768px) {
            .navbar {
                padding: 15px 5%;
            }

            .hamburger {
                display: flex;
            }

            .menu {
                position: fixed;
                top: 0;
                right: -100%;
                width: 250px;
                height: 100vh;
                background: rgba(0, 0, 0, 0.95);
                backdrop-filter: blur(10px);
                flex-direction: column;
                justify-content: center;
                align-items: center;
                transition: right 0.5s ease;
                z-index: 1000;
                box-shadow: -5px 0 20px rgba(0, 0, 0, 0.5);
            }

            .menu.active {
                right: 0;
            }

            .menu ul {
                flex-direction: column;
                align-items: center;
            }

            .menu li {
                margin: 20px 0;
            }

            .hero-content h1 {
                font-size: 2.5rem;
            }

            .par {
                font-size: 1rem;
            }
        }

        @media (max-width: 480px) {
            .hero-content h1 {
                font-size: 2rem;
            }

            .form {
                padding: 30px 20px;
            }

            .form h2 {
                font-size: 1.5rem;
            }

            .content {
                padding: 120px 20px 60px;
            }
            
            footer {
                padding: 30px 20px;
            }
        }
    </style>
    
    <!-- Prevent back button -->
    <script type="text/javascript">
        window.history.forward();
        function noBack() {
            window.history.forward();
        }
    </script>
</head>
<body onload="noBack();" onpageshow="if(event.persisted) noBack();" onunload="">

    <!-- Loading Animation -->
    <div class="loading">
        <div class="loader"></div>
    </div>

    <div class="hai">
        <!-- Navigation Bar -->
        <nav class="navbar">
            <a href="index.php"><img style="height: 50px;" src="images/icon.png" alt="VeloRent Logo"></a>
            
            <!-- Hamburger Menu -->
            <div class="hamburger" id="hamburger">
                <span></span>
                <span></span>
                <span></span>
            </div>
            
            <div class="menu" id="menu">
                <ul>
                    <li><a href="index.php">Home</a></li>
                    <li><a href="aboutus.html">About Us</a></li>
                    <li><a href="services.html">Services</a></li>
                    <li><a href="adminlogin.php">Admin</a></li>
                </ul>
            </div>
        </nav>

        <!-- Main Content -->
        <div class="content">
            <!-- Left Side - Hero Content -->
            <div class="hero-content">
                <h1>Rent Your <br><span>Dream Vehicle</span></h1>
                <p class="par">
                    Live the life of Luxury.<br>
                    Rent a vehicle of your wish from our vast collection.<br>
                    Enjoy every moment with your family.<br>
                    Join us to make this family vast.
                </p>
                <button class="cn"><a href="register.php">Join Us</a></button>
            </div>
            
            <!-- Right Side - Login Form -->
            <div class="form-container">
                <div class="form">
                    <h2>Login Here</h2>
                    
                    <!-- Error Message Display -->
                    <?php if(isset($error_message)): ?>
                        <div class="error-message">
                            <?php echo htmlspecialchars($error_message); ?>
                        </div>
                    <?php endif; ?>
                    
                    <form method="POST">
                        <input type="email" name="email" placeholder="Enter Email Here" required>
                        <input type="password" name="pass" placeholder="Enter Password Here" required>
                        <input class="btnn" type="submit" value="Login" name="login">
                    </form>
                    <p class="link" style="margin-top: 15px;">
                        <a href="forgotpassword.php">Forgot Password?</a>
                    </p>
                    <p class="link">Don't have an account?<br>
                        <a href="register.php">Sign up</a> here
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer>
        <p>&copy; 2024 VeloRent. All Rights Reserved.</p>
        <div class="socials">
            <a href="https://www.facebook.com/thomasbhattrai"><ion-icon name="logo-facebook"></ion-icon></a>
            <a href="https://x.com/thomashbhattarai"><ion-icon name="logo-twitter"></ion-icon></a>
            <a href="https://www.instagram.com/swostimakaju/"><ion-icon name="logo-instagram"></ion-icon></a>
        </div>
    </footer>

    <!-- Scripts -->
    <script src="https://unpkg.com/ionicons@5.4.0/dist/ionicons.js"></script>
    
    <!-- Embedded JavaScript -->
    <script>
        // Loading animation
        window.addEventListener('load', function() {
            const loading = document.querySelector('.loading');
            setTimeout(() => {
                loading.classList.add('hidden');
            }, 1000);
        });

        // Hamburger menu toggle
        const hamburger = document.getElementById('hamburger');
        const menu = document.getElementById('menu');

        hamburger.addEventListener('click', function() {
            hamburger.classList.toggle('active');
            menu.classList.toggle('active');
            
            // Prevent scrolling when menu is open
            if(menu.classList.contains('active')) {
                document.body.style.overflow = 'hidden';
            } else {
                document.body.style.overflow = 'auto';
            }
        });

        // Close menu when clicking on a link
        const menuLinks = document.querySelectorAll('.menu a');
        menuLinks.forEach(link => {
            link.addEventListener('click', function() {
                hamburger.classList.remove('active');
                menu.classList.remove('active');
                document.body.style.overflow = 'auto';
            });
        });

        // Close menu when clicking outside
        document.addEventListener('click', function(event) {
            if (!menu.contains(event.target) && !hamburger.contains(event.target)) {
                hamburger.classList.remove('active');
                menu.classList.remove('active');
                document.body.style.overflow = 'auto';
            }
        });

        // Form validation
        const loginForm = document.querySelector('.form form');
        const emailInput = document.querySelector('input[name="email"]');
        const passInput = document.querySelector('input[name="pass"]');

        loginForm.addEventListener('submit', function(e) {
            let isValid = true;
            
            // Clear previous error styles
            emailInput.style.borderColor = '';
            passInput.style.borderColor = '';
            
            // Validate Email
            const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!emailPattern.test(emailInput.value.trim())) {
                emailInput.style.borderColor = '#ff0000';
                emailInput.focus();
                isValid = false;
            }
            
            // Validate Password
            if (passInput.value.trim().length < 6) {
                passInput.style.borderColor = '#ff0000';
                if (isValid) passInput.focus();
                isValid = false;
            }
            
            if (!isValid) {
                e.preventDefault();
                showDialog('Please enter valid email and password (minimum 6 characters).');
            }
        });

        // Prevent back button
        function noBack() {
            window.history.forward();
        }
    </script>
</body>
</html>