<?php
// Buffer output so redirects can happen even after markup starts
ob_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>VEHICLE Details</title>
    
    <!-- Using the remembered CSS from the login page (dark futuristic theme) -->
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

        /* Loading Animation (kept from original) */
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
            gap: 30px;
            align-items: center;
        }

        .menu a, .menu p {
            color: #fff;
            text-decoration: none;
            font-weight: 500;
            font-size: 1rem;
            transition: color 0.3s;
        }

        .menu a:hover {
            color: #ff7b00;
        }

        .nn {
            background: linear-gradient(45deg, #ff7b00, #ff5500);
            border: none;
            padding: 10px 20px;
            border-radius: 50px;
            cursor: pointer;
            font-weight: 600;
        }

        .nn a {
            color: #fff;
            text-decoration: none;
        }

        .nn:hover {
            transform: translateY(-3px);
            box-shadow: 0 5px 15px rgba(255, 123, 0, 0.4);
        }

        /* Search & Filter Bar */
        .search-filter {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 20px;
            margin: 100px 0 40px;
            flex-wrap: wrap;
        }

        .search-filter input {
            padding: 12px 20px;
            width: 300px;
            max-width: 100%;
            border: none;
            border-radius: 50px;
            background: rgba(255, 255, 255, 0.1);
            color: #fff;
            font-size: 1rem;
            outline: none;
            backdrop-filter: blur(5px);
        }

        .search-filter input::placeholder {
            color: rgba(255, 255, 255, 0.6);
        }

        .search-filter input:focus {
            background: rgba(255, 255, 255, 0.15);
            box-shadow: 0 0 15px rgba(255, 123, 0, 0.3);
        }

        .filter-container {
            position: relative;
        }

        .filter-button {
            background: linear-gradient(45deg, #ff7b00, #ff5500);
            color: #fff;
            border: none;
            padding: 12px 20px;
            border-radius: 50px;
            cursor: pointer;
            font-size: 1rem;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 8px;
            transition: all 0.3s;
        }

        .filter-button:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 20px rgba(255, 123, 0, 0.4);
        }

        .filter-dropdown {
            display: none;
            position: absolute;
            top: 110%;
            right: 0;
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 12px;
            min-width: 180px;
            z-index: 10;
            overflow: hidden;
        }

        .filter-dropdown.show {
            display: block;
        }

        .filter-dropdown a {
            display: block;
            padding: 12px 20px;
            color: #fff;
            text-decoration: none;
            transition: background 0.3s;
        }

        .filter-dropdown a:hover {
            background: rgba(255, 123, 0, 0.3);
        }

        /* Page Title */
        .overview {
            text-align: center;
            font-size: 2.8rem;
            margin: 40px 0;
            background: linear-gradient(45deg, #ff7b00, #ffaa00);
            -webkit-background-clip: text;
            background-clip: text;
            color: transparent;
        }

        /* Sections */
        .recommended-section h2,
        .other-vehicles-section h2 {
            text-align: center;
            font-size: 2.2rem;
            margin: 50px 0 30px;
            background: linear-gradient(45deg, #ff7b00, #ffaa00);
            -webkit-background-clip: text;
            background-clip: text;
            color: transparent;
        }

        /* Vehicle Grid */
        .de {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
            gap: 30px;
            padding: 0 5%;
            list-style: none;
        }

        .vehicle-item {
            display: none;
        }

        .vehicle-item.visible {
            display: block;
            animation: fadeInUp 0.6s ease forwards;
        }

        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(30px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .box {
            background: rgba(255, 255, 255, 0.05);
            backdrop-filter: blur(10px);
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.5);
            border: 1px solid rgba(255, 255, 255, 0.1);
            transition: transform 0.3s, box-shadow 0.3s;
        }

        .box:hover {
            transform: translateY(-10px);
            box-shadow: 0 20px 40px rgba(255, 123, 0, 0.3);
        }

        .imgBx {
            position: relative;
            height: 220px;
            overflow: hidden;
        }

        .imgBx img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.5s;
        }

        .box:hover .imgBx img {
            transform: scale(1.1);
        }

        .recommended-badge {
            position: absolute;
            top: 15px;
            right: 15px;
            background: linear-gradient(45deg, #ff7b00, #ff5500);
            color: #fff;
            padding: 8px 15px;
            border-radius: 50px;
            font-size: 0.9rem;
            font-weight: bold;
            z-index: 5;
        }

        .content {
            padding: 25px;
            text-align: center;
        }

        .content h1 {
            font-size: 1.8rem;
            margin-bottom: 15px;
            color: #ff7b00;
        }

        .content h2 {
            font-size: 1.1rem;
            margin: 10px 0;
            color: #ccc;
        }

        .content h2 a {
            color: #fff;
            font-weight: 600;
        }

        .utton {
            margin-top: 20px;
            display: inline-block;
            background: linear-gradient(45deg, #ff7b00, #ff5500);
            border: none;
            padding: 14px 30px;
            border-radius: 50px;
            cursor: pointer;
            font-size: 1.1rem;
            font-weight: 600;
            color: #fff;
            text-decoration: none;
            transition: all 0.3s;
            text-align: center;
        }

        .utton:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(255, 123, 0, 0.4);
        }

        /* Footer */
        footer {
            background: rgba(0, 0, 0, 0.9);
            padding: 40px 5%;
            text-align: center;
            margin-top: 80px;
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

        /* Responsive */
        @media (max-width: 768px) {
            .search-filter {
                flex-direction: column;
                gap: 15px;
            }

            .search-filter input {
                width: 90%;
            }

            .de {
                grid-template-columns: 1fr;
                padding: 0 5%;
            }

            .overview {
                font-size: 2.2rem;
            }
        }
    </style>
</head>
<body class="body">

    <!-- Loading Animation -->
    <div class="loading">
        <div class="loader"></div>
    </div>

<?php 
    require_once('connection.php');
    session_start();

    // Require logged-in user and load their record; redirect if missing
    $value = isset($_SESSION['email']) ? $_SESSION['email'] : null;
    if (!$value) {
        header('Location: index.php');
        exit;
    }

    $_SESSION['email'] = $value;
    $sql = "select * from users where EMAIL='$value'";
    $name = mysqli_query($con, $sql);
    $rows = ($name && ($fetched = mysqli_fetch_assoc($name))) ? $fetched : null;
    if (!$rows) {
        header('Location: index.php');
        exit;
    }
    
    // Handle sorting
    $sort = '';
    if (isset($_GET['sort'])) {
        if ($_GET['sort'] == 'price_asc') {
            $sort = ' ORDER BY PRICE ASC';
        } elseif ($_GET['sort'] == 'price_desc') {
            $sort = ' ORDER BY PRICE DESC';
        }
    }
    
    $sql2 = "select * from vehicles where AVAILABLE='Y'";
    $sql2 .= $sort;
    
    $vehicles = mysqli_query($con, $sql2);
    
    // Get user's booking history for recommendations (with error handling)
    $bookingHistory = [];
    $userId = isset($rows['USER_ID']) ? $rows['USER_ID'] : null;
    
    if ($userId) {
        // Check if bookings table exists
        $tableCheck = mysqli_query($con, "SHOW TABLES LIKE 'bookings'");
        if(mysqli_num_rows($tableCheck) > 0) {
            $historyQuery = "SELECT v.VEHICLE_TYPE, v.FUEL_TYPE FROM bookings b 
                             JOIN vehicles v ON b.VEHICLE_ID = v.VEHICLE_ID 
                             WHERE b.USER_ID = '$userId'";
            $historyResult = mysqli_query($con, $historyQuery);
            if($historyResult) {
                while($history = mysqli_fetch_assoc($historyResult)) {
                    $bookingHistory[] = $history;
                }
            }
        }
    }

    // Store vehicle data for recommendation algorithm
    $vehicleData = [];
    while($result = mysqli_fetch_array($vehicles)) {
        $vehicleData[] = $result;
    }
    
// Recommendation Algorithm
$recommendedVehicles = [];
$categories = ['Car', 'Bike', 'Scooter'];

// Function to calculate similarity between two users based on booking history
function calculateUserSimilarity($userHistory, $otherUserHistory) {
    $similarity = 0;
    foreach ($userHistory as $userBooking) {
        foreach ($otherUserHistory as $otherBooking) {
            if (strtolower($userBooking['VEHICLE_TYPE']) === strtolower($otherBooking['VEHICLE_TYPE'])) {
                $similarity += 10; // Weight for vehicle type match
            }
            if (strtolower($userBooking['FUEL_TYPE']) === strtolower($otherBooking['FUEL_TYPE'])) {
                $similarity += 5; // Weight for fuel type match
            }
        }
    }
    return $similarity;
}

// Get booking history for all users (for collaborative filtering and popularity)
$allUsersHistory = [];
$vehiclePopularity = []; // Track booking frequency per vehicle
if ($userId && mysqli_num_rows(mysqli_query($con, "SHOW TABLES LIKE 'bookings'")) > 0) {
    $allUsersQuery = "SELECT b.USER_ID, b.VEHICLE_ID, v.VEHICLE_TYPE, v.FUEL_TYPE, v.CAPACITY, v.PRICE, b.BOOKING_DATE 
                     FROM bookings b 
                     JOIN vehicles v ON b.VEHICLE_ID = v.VEHICLE_ID";
    $allUsersResult = mysqli_query($con, $allUsersQuery);
    if ($allUsersResult) {
        while ($row = mysqli_fetch_assoc($allUsersResult)) {
            $allUsersHistory[$row['USER_ID']][] = [
                'VEHICLE_ID' => $row['VEHICLE_ID'],
                'VEHICLE_TYPE' => $row['VEHICLE_TYPE'],
                'FUEL_TYPE' => $row['FUEL_TYPE'],
                'CAPACITY' => $row['CAPACITY'],
                'PRICE' => $row['PRICE'],
                'BOOKING_DATE' => $row['BOOKING_DATE']
            ];
            // Increment popularity score
            $vehiclePopularity[$row['VEHICLE_ID']] = ($vehiclePopularity[$row['VEHICLE_ID']] ?? 0) + 1;
        }
    }
}

// Score vehicles
$scoredVehicles = [];
foreach ($vehicleData as $vehicle) {
    $score = 0;

    if (!empty($bookingHistory)) {
        // Content-based scoring (based on user's booking history)
        foreach ($bookingHistory as $history) {
            if (strtolower($history['VEHICLE_TYPE']) === strtolower($vehicle['VEHICLE_TYPE'])) {
                $score += 15; // Higher weight for vehicle type match
            }
            if (strtolower($history['FUEL_TYPE']) === strtolower($vehicle['FUEL_TYPE'])) {
                $score += 8; // Weight for fuel type match
            }
            if (isset($vehicle['CAPACITY']) && isset($history['CAPACITY']) && 
                abs($vehicle['CAPACITY'] - $history['CAPACITY']) <= 2) {
                $score += 5; // Weight for similar capacity
            }
        }

        // Collaborative filtering: score based on similar users' bookings
        $similarUsers = [];
        foreach ($allUsersHistory as $otherUserId => $otherHistory) {
            if ($otherUserId != $userId) {
                $similarity = calculateUserSimilarity($bookingHistory, $otherHistory);
                if ($similarity > 0) {
                    $similarUsers[$otherUserId] = $similarity;
                }
            }
        }
        arsort($similarUsers); // Sort users by similarity descending
        $topSimilarUsers = array_slice($similarUsers, 0, 3, true); // Top 3 similar users

        foreach ($topSimilarUsers as $similarUserId => $similarity) {
            foreach ($allUsersHistory[$similarUserId] as $similarBooking) {
                if ($similarBooking['VEHICLE_ID'] == $vehicle['VEHICLE_ID']) {
                    $score += $similarity * 0.7; // Higher weight for exact vehicle match
                } elseif (strtolower($similarBooking['VEHICLE_TYPE']) === strtolower($vehicle['VEHICLE_TYPE'])) {
                    $score += $similarity * 0.5; // Weight for vehicle type match
                }
                if (strtolower($similarBooking['FUEL_TYPE']) === strtolower($vehicle['FUEL_TYPE'])) {
                    $score += $similarity * 0.3; // Weight for fuel type match
                }
            }
        }

        // Price preference (favor vehicles closer to average price of bookings)
        $avgBookingPrice = array_sum(array_column($bookingHistory, 'PRICE') ?: [0]) / (count($bookingHistory) ?: 1);
        if ($avgBookingPrice > 0 && abs($vehicle['PRICE'] - $avgBookingPrice) < 500) {
            $score += 5; // Weight for price proximity
        }

        // Recency bias (if booking date is available)
        foreach ($bookingHistory as $history) {
            if (isset($history['BOOKING_DATE']) && $history['VEHICLE_ID'] == $vehicle['VEHICLE_ID']) {
                $bookingDate = new DateTime($history['BOOKING_DATE']);
                $now = new DateTime();
                $daysDiff = $now->diff($bookingDate)->days;
                if ($daysDiff <= 30) {
                    $score += 10 / ($daysDiff + 1); // Higher score for recent bookings
                }
            }
        }
    } else {
        // For users without booking history
        // Content-based scoring: favor premium or eco-friendly vehicles
        $avgPrice = array_sum(array_column($vehicleData, 'PRICE')) / count($vehicleData);
        if ($vehicle['PRICE'] > $avgPrice) {
            $score += 5; // Favor "premium" vehicles
        }
        if (strtolower($vehicle['FUEL_TYPE']) === 'electric') {
            $score += 7; // Favor eco-friendly vehicles
        }
        if ($vehicle['CAPACITY'] >= 4) {
            $score += 3; // Favor higher capacity
        }
    }

    // Popularity-based scoring
    $popularityScore = $vehiclePopularity[$vehicle['VEHICLE_ID']] ?? 0;
    $score += $popularityScore * (!empty($bookingHistory) ? 3 : 5); // Higher weight for new users

    $scoredVehicles[] = ['vehicle' => $vehicle, 'score' => $score];
}

// Select one vehicle per category
$categoryVehicles = [];
foreach ($categories as $category) {
    $categoryVehicles[$category] = array_filter($scoredVehicles, function($v) use ($category) {
        return strtolower($v['vehicle']['VEHICLE_TYPE']) === strtolower($category);
    });
    usort($categoryVehicles[$category], function($a, $b) {
        return $b['score'] - $a['score'];
    });
}

// Add top vehicle from each category to recommendations
foreach ($categories as $category) {
    if (!empty($categoryVehicles[$category])) {
        $recommendedVehicles[] = $categoryVehicles[$category][0];
    }
}

// Extract recommended vehicle IDs
$recommendedIds = array_column(array_column($recommendedVehicles, 'vehicle'), 'VEHICLE_ID');
?>

<div class="cd">
    <nav class="navbar">
        <div class="icon">
            <a href="vehiclesdetails.php"><img style="height: 50px;" src="images/icon.png" alt="VeloRent Logo"></a>
        </div>
        <div class="menu">
            <ul>
                <li><p class="phello"><a id="pname" href="userprofile.php" style="cursor: pointer;"><?php echo htmlspecialchars($rows['FNAME'].' '.$rows['LNAME']); ?></a></p></li>
                <li><a id="stat" href="bookingstatus.php">BOOKING STATUS</a></li>
                <li><button class="nn"><a href="index.php">LOGOUT</a></button></li>
            </ul>
        </div>
    </nav>
    <br>
    <br>

    <div class="search-filter">
        <input type="text" id="searchInput" placeholder="Search vehicle name...">
        <div class="filter-container">
            <button class="filter-button" onclick="toggleFilterDropdown('type')">
                <ion-icon name="car-outline"></ion-icon> Vehicle Type
            </button>
            <div class="filter-dropdown" id="typeFilterDropdown">
                <a href="#" onclick="filterByType('')">All</a>
                <a href="#" onclick="filterByType('Car')">Car</a>
                <a href="#" onclick="filterByType('Bike')">Bike</a>
                <a href="#" onclick="filterByType('Scooter')">Scooter</a>
            </div>
        </div>
        <div class="filter-container">
            <button class="filter-button" onclick="toggleFilterDropdown('price')">
                <ion-icon name="options-outline"></ion-icon> Sort by Price
            </button>
            <div class="filter-dropdown" id="priceFilterDropdown">
                <a href="?sort=price_asc">Price: Low to High</a>
                <a href="?sort=price_desc">Price: High to Low</a>
            </div>
        </div>
    </div>

    <h1 class="overview">OUR VEHICLE OVERVIEW</h1>

    <!-- Recommended Vehicles Section -->
    <?php if (!empty($recommendedVehicles)): ?>
    <div class="recommended-section">
        <h2>Recommended For You</h2>
        <ul class="de">
            <?php foreach ($recommendedVehicles as $recVehicle): 
                $vehicle = $recVehicle['vehicle'];
                $res = $vehicle['VEHICLE_ID'];
            ?>
            <li class="vehicle-item visible" data-name="<?php echo htmlspecialchars(strtolower($vehicle['VEHICLE_NAME'])); ?>" data-type="<?php echo htmlspecialchars(strtolower($vehicle['VEHICLE_TYPE'])); ?>">
                <form method="POST">
                    <div class="box">
                        <div class="imgBx">
                            <img src="images/<?php echo $vehicle['VEHICLE_IMG']?>" alt="<?php echo $vehicle['VEHICLE_NAME']?>">
                            <div class="recommended-badge">RECOMMENDED</div>
                        </div>
                        <div class="content">
                            <h1><?php echo $vehicle['VEHICLE_NAME']?></h1>
                            <h2>Fuel Type: <a><?php echo $vehicle['FUEL_TYPE']?></a></h2>
                            <h2>Capacity: <a><?php echo $vehicle['CAPACITY']?></a></h2>
                            <h2>Rent Per Day: <a>Rs<?php echo $vehicle['PRICE']?>/-</a></h2>
                            <h2>Vehicle Type: <a><?php echo $vehicle['VEHICLE_TYPE']?></a></h2>
                            <a class="utton" href="booking.php?id=<?php echo $res;?>">Book Now</a>
                        </div>
                    </div>
                </form>
            </li>
            <?php endforeach; ?>
        </ul>
    </div>
    <?php endif; ?>

    <!-- Other Vehicles Section -->
    <?php
        $hasOtherVehicles = false;
        foreach ($vehicleData as $result) {
            if (!in_array($result['VEHICLE_ID'], $recommendedIds)) {
                $hasOtherVehicles = true;
                break;
            }
        }
    ?>
    <?php if ($hasOtherVehicles): ?>
    <div class="other-vehicles-section">
        <h2>Explore More Vehicles</h2>
        <ul class="de">
            <?php foreach ($vehicleData as $result): 
                $res = $result['VEHICLE_ID'];
                if (!in_array($res, $recommendedIds)):
            ?>
            <li class="vehicle-item visible" data-name="<?php echo htmlspecialchars(strtolower($result['VEHICLE_NAME'])); ?>" data-type="<?php echo htmlspecialchars(strtolower($result['VEHICLE_TYPE'])); ?>">
                <form method="POST">
                    <div class="box">
                        <div class="imgBx">
                            <img src="images/<?php echo $result['VEHICLE_IMG']?>" alt="<?php echo $result['VEHICLE_NAME']?>">
                        </div>
                        <div class="content">
                            <h1><?php echo $result['VEHICLE_NAME']?></h1>
                            <h2>Fuel Type: <a><?php echo $result['FUEL_TYPE']?></a></h2>
                            <h2>Capacity: <a><?php echo $result['CAPACITY']?></a></h2>
                            <h2>Rent Per Day: <a>Rs<?php echo $result['PRICE']?>/-</a></h2>
                            <h2>Vehicle Type: <a><?php echo $result['VEHICLE_TYPE']?></a></h2>
                            <a class="utton" href="booking.php?id=<?php echo $res;?>">Book Now</a>
                        </div>
                    </div>
                </form>
            </li>
            <?php endif; ?>
            <?php endforeach; ?>
        </ul>
    </div>
    <?php endif; ?>
</div>

<footer>
    <p>&copy; 2024 VeloRent. All Rights Reserved.</p>
    <div class="socials">
        <a href="https://www.facebook.com/thomasbhattrai" target="_blank"><ion-icon name="logo-facebook"></ion-icon></a>
        <a href="https://x.com/thomashbhattarai" target="_blank"><ion-icon name="logo-twitter"></ion-icon></a>
        <a href="https://www.instagram.com/swostimakaju/" target="_blank"><ion-icon name="logo-instagram"></ion-icon></a>
    </div>
</footer>

<script src="https://unpkg.com/ionicons@5.4.0/dist/ionicons.js"></script>
<script>
    // Loading animation
    // Graceful loader hide with fallback
    (function() {
        const loading = document.querySelector('.loading');
        const hide = () => loading && loading.classList.add('hidden');
        window.addEventListener('load', () => setTimeout(hide, 300));
        document.addEventListener('DOMContentLoaded', () => setTimeout(hide, 800));
        // Safety: force hide after 2s in case resources stall
        setTimeout(hide, 2000);
    })();

    let selectedType = '';

    // Initialize page
    document.addEventListener('DOMContentLoaded', function() {
        const searchInput = document.getElementById('searchInput');
        const vehicleItems = document.querySelectorAll('.vehicle-item');

        // Show all initially
        vehicleItems.forEach(item => item.classList.add('visible'));

        // Search functionality
        searchInput.addEventListener('input', function() {
            const searchTerm = this.value.toLowerCase().trim();
            filterVehicles(searchTerm, selectedType);
        });
    });

    // Toggle filter dropdown
    function toggleFilterDropdown(type) {
        const dropdowns = {
            'type': document.getElementById('typeFilterDropdown'),
            'price': document.getElementById('priceFilterDropdown')
        };
        const dropdown = dropdowns[type];
        const otherDropdown = type === 'type' ? dropdowns['price'] : dropdowns['type'];
        
        dropdown.classList.toggle('show');
        if (otherDropdown) otherDropdown.classList.remove('show');
    }

    // Filter by vehicle type
    function filterByType(type) {
        selectedType = type.toLowerCase();
        const searchInput = document.getElementById('searchInput');
        filterVehicles(searchInput.value.toLowerCase().trim(), selectedType);
        document.getElementById('typeFilterDropdown').classList.remove('show');
    }

    // Combined filtering
    function filterVehicles(searchTerm, vehicleType) {
        const vehicleItems = document.querySelectorAll('.vehicle-item');

        vehicleItems.forEach(item => {
            const vehicleName = item.getAttribute('data-name');
            const itemType = item.getAttribute('data-type');
            
            const matchesSearch = searchTerm === '' || vehicleName.includes(searchTerm);
            const matchesType = vehicleType === '' || itemType === vehicleType;

            if (matchesSearch && matchesType) {
                item.classList.add('visible');
            } else {
                item.classList.remove('visible');
            }
        });
    }

    // Close dropdown on outside click
    window.onclick = function(event) {
        if (!event.target.closest('.filter-button') && !event.target.closest('.filter-dropdown')) {
            document.getElementById('typeFilterDropdown').classList.remove('show');
            document.getElementById('priceFilterDropdown').classList.remove('show');
        }
    }
</script>
</body>
</html>