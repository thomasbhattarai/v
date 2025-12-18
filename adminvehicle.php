<?php
    require_once('connection.php');
    session_start();
    
    // Check if admin is logged in
    if(!isset($_SESSION['admin_id'])) {
        header("location: adminlogin.php");
        exit();
    }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vehicle Management - VeloRent Admin</title>
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
            display: flex;
            flex-direction: column;
        }

        /* Main Wrapper */
        .wrapper {
            flex: 1;
            display: flex;
            flex-direction: column;
        }

        /* Navbar Styles - Fixed at top */
        .navbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 15px 5%;
            background: rgba(0, 0, 0, 0.95);
            backdrop-filter: blur(10px);
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.4);
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            z-index: 1000;
            height: 80px;
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
            gap: 30px;
            align-items: center;
        }

        .menu a {
            color: #fff;
            text-decoration: none;
            font-weight: 500;
            font-size: 1rem;
            padding: 10px 20px;
            border-radius: 8px;
            transition: all 0.3s ease;
            position: relative;
        }

        .menu a:not(.button)::after {
            content: '';
            position: absolute;
            width: 0;
            height: 2px;
            bottom: 0;
            left: 0;
            background-color: #ff7b00;
            transition: width 0.3s;
        }

        .menu a:not(.button):hover {
            color: #ff7b00;
        }

        .menu a:not(.button):hover::after {
            width: 100%;
        }

        .menu a.button {
            background: linear-gradient(45deg, #ff0000, #ff7b00);
            color: white;
            font-weight: 600;
            transition: transform 0.3s, box-shadow 0.3s;
        }

        .menu a.button:hover {
            transform: translateY(-3px);
            box-shadow: 0 5px 15px rgba(255, 123, 0, 0.4);
        }

        /* Highlight current page */
        .menu a[href="adminvehicle.php"] {
            color: #ff7b00;
            font-weight: 600;
        }

        /* Hamburger Menu for Mobile */
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

        /* Main Content */
        .main-content {
            padding: 120px 5% 40px;
            flex: 1;
            animation: fadeIn 0.5s ease;
            min-height: calc(100vh - 180px);
        }

        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }

        /* Header Container */
        .header-container {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 40px;
            flex-wrap: wrap;
            gap: 20px;
        }

        .header {
            font-size: 2.5rem;
            background: linear-gradient(45deg, #ff7b00, #ffaa00);
            -webkit-background-clip: text;
            background-clip: text;
            color: transparent;
            position: relative;
            display: inline-block;
        }

        .header::after {
            content: '';
            position: absolute;
            width: 100px;
            height: 4px;
            background: #ff7b00;
            bottom: -10px;
            left: 0;
            border-radius: 2px;
        }

        /* Add Vehicle Button */
        .add {
            display: inline-block;
            background: linear-gradient(45deg, #00ff00, #00cc00);
            color: white;
            padding: 12px 30px;
            border-radius: 50px;
            text-decoration: none;
            font-weight: 600;
            font-size: 1.1rem;
            transition: transform 0.3s, box-shadow 0.3s;
            box-shadow: 0 5px 15px rgba(0, 255, 0, 0.3);
        }

        .add:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 20px rgba(0, 255, 0, 0.4);
            background: linear-gradient(45deg, #00cc00, #00ff00);
        }

        /* Table Styles */
        .content-table {
            width: 100%;
            border-collapse: collapse;
            margin: 25px 0;
            font-size: 0.9em;
            min-width: 1000px;
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
            background: rgba(255, 255, 255, 0.05);
            backdrop-filter: blur(10px);
        }

        .content-table thead tr {
            background: linear-gradient(45deg, #ff7b00, #ff5500);
            color: #ffffff;
            text-align: left;
            font-weight: bold;
        }

        .content-table th,
        .content-table td {
            padding: 18px 20px;
            text-align: center;
        }

        .content-table tbody tr {
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            transition: all 0.3s ease;
        }

        .content-table tbody tr:nth-of-type(even) {
            background: rgba(255, 255, 255, 0.03);
        }

        .content-table tbody tr:last-of-type {
            border-bottom: 2px solid #ff7b00;
        }

        .content-table tbody tr:hover {
            background: rgba(255, 255, 255, 0.08);
            transform: translateY(-2px);
        }

        .content-table td {
            color: #ddd;
        }

        .content-table td:first-child {
            font-weight: 500;
            color: #fff;
        }

        /* Available Status */
        .content-table td:nth-child(7) {
            font-weight: 600;
        }

        .content-table td:nth-child(7):contains("YES") {
            color: #00ff00;
        }

        .content-table td:nth-child(7):contains("NO") {
            color: #ff0000;
        }

        /* Button Styles */
        .content-table .button {
            display: inline-block;
            background: linear-gradient(45deg, #ff0000, #ff7b00);
            color: white;
            padding: 10px 20px;
            border-radius: 8px;
            text-decoration: none;
            font-weight: 600;
            transition: transform 0.3s, box-shadow 0.3s;
            font-size: 0.9rem;
            border: none;
            cursor: pointer;
            width: 100%;
        }

        .content-table .button:hover {
            transform: translateY(-3px);
            box-shadow: 0 5px 15px rgba(255, 123, 0, 0.4);
        }

        .content-table .button a {
            color: white;
            text-decoration: none;
            display: block;
            width: 100%;
            height: 100%;
        }

        /* Admin Stats */
        .admin-stats {
            display: flex;
            justify-content: center;
            gap: 30px;
            margin-bottom: 40px;
            flex-wrap: wrap;
        }

        .stat-card {
            background: rgba(255, 255, 255, 0.05);
            backdrop-filter: blur(10px);
            padding: 25px;
            border-radius: 15px;
            min-width: 200px;
            text-align: center;
            border: 1px solid rgba(255, 255, 255, 0.1);
            transition: transform 0.3s;
        }

        .stat-card:hover {
            transform: translateY(-5px);
            background: rgba(255, 255, 255, 0.08);
        }

        .stat-card h3 {
            color: #ff7b00;
            margin-bottom: 10px;
            font-size: 1.2rem;
        }

        .stat-card p {
            font-size: 2rem;
            font-weight: 700;
            color: #fff;
        }

        /* Table Container for Horizontal Scroll */
        .table-container {
            overflow-x: auto;
            border-radius: 15px;
            margin: 30px 0;
            background: rgba(255, 255, 255, 0.02);
            padding: 20px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
        }

        /* Footer */
        footer {
            background: rgba(0, 0, 0, 0.95);
            padding: 30px 5%;
            text-align: center;
            margin-top: auto;
            width: 100%;
            box-shadow: 0 -5px 20px rgba(0, 0, 0, 0.4);
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
        @media (max-width: 1200px) {
            .content-table {
                min-width: 900px;
            }
        }

        @media (max-width: 992px) {
            .navbar {
                padding: 15px 5%;
            }

            .menu ul {
                gap: 20px;
            }

            .header {
                font-size: 2.2rem;
            }

            .content-table {
                min-width: 800px;
            }

            .content-table th,
            .content-table td {
                padding: 15px;
                font-size: 0.85rem;
            }

            .add {
                padding: 10px 25px;
                font-size: 1rem;
            }
        }

        @media (max-width: 768px) {
            .navbar {
                padding: 15px 5%;
                height: 70px;
            }
            
            .main-content {
                padding: 100px 20px 40px;
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
                background: rgba(0, 0, 0, 0.98);
                backdrop-filter: blur(10px);
                flex-direction: column;
                justify-content: center;
                align-items: center;
                transition: right 0.5s ease;
                z-index: 999;
                box-shadow: -5px 0 20px rgba(0, 0, 0, 0.5);
            }

            .menu.active {
                right: 0;
            }

            .menu ul {
                flex-direction: column;
                align-items: center;
                gap: 30px;
                width: 100%;
                padding: 20px;
            }

            .menu a {
                width: 100%;
                text-align: center;
                padding: 15px;
                font-size: 1.1rem;
            }

            .header {
                font-size: 2rem;
                margin-top: 10px;
            }

            .content-table {
                min-width: 700px;
            }

            .content-table th,
            .content-table td {
                padding: 12px 10px;
                font-size: 0.8rem;
            }

            .content-table .button {
                padding: 8px 15px;
                font-size: 0.8rem;
            }

            .admin-stats {
                flex-direction: column;
                align-items: center;
            }

            .stat-card {
                width: 100%;
                max-width: 300px;
            }
            
            .header-container {
                flex-direction: column;
                align-items: flex-start;
                gap: 15px;
            }
            
            footer {
                padding: 25px 20px;
            }
        }

        @media (max-width: 576px) {
            .navbar {
                height: 65px;
            }
            
            .main-content {
                padding: 90px 15px 30px;
            }
            
            .header {
                font-size: 1.8rem;
            }

            .content-table {
                min-width: 600px;
            }

            .content-table th,
            .content-table td {
                padding: 10px 8px;
                font-size: 0.75rem;
            }

            .navbar img {
                height: 40px;
            }

            .add {
                padding: 8px 20px;
                font-size: 0.9rem;
            }

            footer {
                padding: 20px 15px;
            }
            
            .socials a {
                font-size: 1.3rem;
            }
        }

        @media (max-width: 480px) {
            .navbar {
                padding: 12px 4%;
                height: 60px;
            }
            
            .main-content {
                padding: 80px 12px 25px;
            }
            
            .content-table {
                min-width: 500px;
            }

            .content-table th,
            .content-table td {
                padding: 8px 6px;
                font-size: 0.7rem;
            }

            .header {
                font-size: 1.6rem;
                margin-bottom: 30px;
            }
            
            .stat-card {
                padding: 20px;
            }
            
            .stat-card h3 {
                font-size: 1.1rem;
            }
            
            .stat-card p {
                font-size: 1.8rem;
            }
            
            .add {
                padding: 7px 15px;
                font-size: 0.85rem;
            }
        }
    </style>
</head>
<body>

<div class="wrapper">
    <!-- Navigation Bar - Fixed at top -->
    <nav class="navbar">
        <img style="height: 50px;" src="images/icon.png" alt="VeloRent Logo">
        
        <!-- Hamburger Menu -->
        <div class="hamburger" id="hamburger">
            <span></span>
            <span></span>
            <span></span>
        </div>
        
        <div class="menu" id="menu">
            <ul>
                <li><a href="adminvehicle.php">VEHICLE MANAGEMENT</a></li>
                <li><a href="adminusers.php">USERS</a></li>
                <li><a href="adminbook.php">BOOKING REQUEST</a></li>
                <li><a href="index.php" class="button">LOGOUT</a></li>
            </ul>
        </div>
    </nav>

    <!-- Main Content with proper spacing -->
    <div class="main-content">
        <!-- Admin Stats -->
        <div class="admin-stats">
            <?php
            // Get vehicle stats
            $total_query = "SELECT COUNT(*) as total FROM vehicles";
            $total_result = mysqli_query($con, $total_query);
            $total_row = mysqli_fetch_assoc($total_result);
            $total_vehicles = $total_row['total'];
            
            $available_query = "SELECT COUNT(*) as available FROM vehicles WHERE AVAILABLE = 'Y'";
            $available_result = mysqli_query($con, $available_query);
            $available_row = mysqli_fetch_assoc($available_result);
            $available_vehicles = $available_row['available'];
            ?>
            <div class="stat-card">
                <h3>Total Vehicles</h3>
                <p><?php echo $total_vehicles; ?></p>
            </div>
            <div class="stat-card">
                <h3>Available Now</h3>
                <p><?php echo $available_vehicles; ?></p>
            </div>
        </div>

        <!-- Header with Add Button -->
        <div class="header-container">
            <h1 class="header">VEHICLE MANAGEMENT</h1>
            <a href="addvehicle.php" class="add">+ ADD VEHICLE</a>
        </div>

        <!-- Table Container for Horizontal Scroll -->
        <div class="table-container">
            <table class="content-table">
                <thead>
                    <tr>
                        <th>VEHICLE ID</th>
                        <th>VEHICLE NAME</th>
                        <th>VEHICLE TYPE</th>
                        <th>FUEL TYPE</th>
                        <th>CAPACITY</th>
                        <th>PRICE</th>
                        <th>AVAILABLE</th>
                        <th>ACTION</th>
                    </tr>
                </thead>
                <tbody>
                <?php
                    require_once('connection.php');
                    $query="SELECT * FROM vehicles";    
                    $queryy=mysqli_query($con,$query);
                    if($queryy) {
                        while($res=mysqli_fetch_array($queryy)) {
                            $available_text = $res['AVAILABLE'] == 'Y' ? 'YES' : 'NO';
                            $available_color = $res['AVAILABLE'] == 'Y' ? '#00ff00' : '#ff0000';
                ?>
                    <tr>
                        <td><?php echo htmlspecialchars($res['VEHICLE_ID']); ?></td>
                        <td><?php echo htmlspecialchars($res['VEHICLE_NAME']); ?></td>
                        <td><?php echo htmlspecialchars($res['VEHICLE_TYPE']); ?></td>
                        <td><?php echo htmlspecialchars($res['FUEL_TYPE']); ?></td>
                        <td><?php echo htmlspecialchars($res['CAPACITY']); ?></td>
                        <td>$<?php echo htmlspecialchars($res['PRICE']); ?>/day</td>
                        <td style="color: <?php echo $available_color; ?>; font-weight: 600;"><?php echo $available_text; ?></td>
                        <td>
                            <button class="button">
                                <a href="#" onclick="confirmDelete(<?php echo urlencode($res['VEHICLE_ID']); ?>); return false;">DELETE</a>
                            </button>
                        </td>
                    </tr>
                <?php 
                        }
                    } else {
                        echo '<tr><td colspan="8" style="text-align: center; color: #ff6b6b;">Error loading vehicle data</td></tr>';
                    }
                ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Footer - Sticky at bottom -->
    <footer>
        <p>&copy; 2024 VeloRent. All Rights Reserved.</p>
        <div class="socials">
            <a href="https://www.facebook.com/thomasbhattrai" target="_blank"><ion-icon name="logo-facebook"></ion-icon></a>
            <a href="https://x.com/thomashbhattarai" target="_blank"><ion-icon name="logo-twitter"></ion-icon></a>
            <a href="https://www.instagram.com/swostimakaju/" target="_blank"><ion-icon name="logo-instagram"></ion-icon></a>
        </div>
    </footer>
</div>

<script src="https://unpkg.com/ionicons@5.4.0/dist/ionicons.js"></script>

<!-- Embedded JavaScript -->
<script>
    // Hamburger menu toggle
    const hamburger = document.getElementById('hamburger');
    const menu = document.getElementById('menu');

    if (hamburger && menu) {
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
    }

    // Confirmation for delete actions
    function confirmDelete(vehicleId) {
        showDialog('Are you sure you want to delete this vehicle? This action cannot be undone.', function() {
            window.location.href = 'deletevehicle.php?id=' + vehicleId;
        }, true);
        return false;
    }

    // Add hover effect to table rows
    const tableRows = document.querySelectorAll('.content-table tbody tr');
    tableRows.forEach(row => {
        row.addEventListener('mouseenter', function() {
            this.style.cursor = 'pointer';
        });
    });

    // Simple page load animation
    document.addEventListener('DOMContentLoaded', function() {
        document.body.style.opacity = '0';
        document.body.style.transition = 'opacity 0.5s ease';
        
        setTimeout(function() {
            document.body.style.opacity = '1';
        }, 100);
    });
</script>

</body>
</html>