<?php
session_start();
require_once('connection.php');

// Check if user is logged in
if (!isset($_SESSION['email'])) {
    header("Location: login.php");
    exit();
}

$vehicleid = $_GET['id'];

// Fetch vehicle details
$sql = "SELECT * FROM vehicles WHERE VEHICLE_ID='$vehicleid'";
$cname = mysqli_query($con, $sql);
$vehicle = mysqli_fetch_assoc($cname);

// Fetch user details
$value = $_SESSION['email'];
$sql = "SELECT * FROM users WHERE EMAIL='$value'";
$name = mysqli_query($con, $sql);
$user = mysqli_fetch_assoc($name);
$uemail = $user['EMAIL'];
$base_price = $vehicle['PRICE'];

// Dynamic Pricing Algorithm
function calculateDynamicPrice($base_price, $booking_date, $duration, $vehicle_type)
{
    // Demand Factor: Increase price by 20% on weekends
    $date = new DateTime($booking_date);
    $day_of_week = $date->format('N'); // 1 (Monday) to 7 (Sunday)
    $demand_factor = ($day_of_week >= 6) ? 1.2 : 1.0; // 20% increase on weekends (Saturday/Sunday)

    // Seasonal Factor: Adjust based on month (e.g., peak season: June, July, December)
    $month = $date->format('n'); // 1 to 12
    $peak_months = [6, 7, 12]; // June, July, December
    $seasonal_factor = in_array($month, $peak_months) ? 1.3 : 1.0; // 30% increase in peak season

    // Duration Factor: Discount for longer bookings
    $duration_factor = 1.0;
    if ($duration >= 7) {
        $duration_factor = 0.9; // 10% discount for bookings of 7+ days
    } elseif ($duration >= 3) {
        $duration_factor = 0.95; // 5% discount for bookings of 3-6 days
    }

    // Calculate final price
    $dynamic_price = $base_price * $demand_factor * $seasonal_factor * $duration_factor;
    return round($dynamic_price, 2);
}

if (isset($_POST['book'])) {
    $bplace = mysqli_real_escape_string($con, $_POST['place']);
    $bdate = date('Y-m-d', strtotime($_POST['date']));
    $dur = mysqli_real_escape_string($con, $_POST['dur']);
    $phno = mysqli_real_escape_string($con, $_POST['ph']);
    $des = mysqli_real_escape_string($con, $_POST['des']);
    $rdate = date('Y-m-d', strtotime($_POST['rdate']));

    if (empty($bplace) || empty($bdate) || empty($dur) || empty($phno) || empty($des) || empty($rdate)) {
        echo '<script>alert("Please fill all fields")</script>';
    } else {
        if ($bdate < $rdate) {
            // Assume vehicle type is stored in the vehicles table; adjust as per your database schema
            $vehicle_type = isset($vehicle['VEHICLE_TYPE']) ? $vehicle['VEHICLE_TYPE'] : 'Standard';
            $dynamic_price = calculateDynamicPrice($base_price, $bdate, $dur, $vehicle_type);
            $total_price = $dynamic_price * $dur;

            // Store booking data in session instead of inserting into database
            $_SESSION['pending_booking'] = [
                'vehicle_id' => $vehicleid,
                'email' => $uemail,
                'book_place' => $bplace,
                'book_date' => $bdate,
                'duration' => $dur,
                'phone_number' => $phno,
                'destination' => $des,
                'price' => $total_price,
                'return_date' => $rdate,
                'vehicle_name' => $vehicle['VEHICLE_NAME']
            ];
            
            $_SESSION['total_price'] = $total_price;
            
            header("Location: esewa-intregation.php");
            exit();
        } else {
            echo '<script>alert("Please enter a correct return date")</script>';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>VEHICLE BOOKING - VeloRent</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
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

        /* Cancel Button */
        .cancel-btn {
            position: fixed;
            top: 30px;
            right: 30px;
            background: linear-gradient(45deg, #ff7b00, #ff5500);
            border: none;
            color: white;
            padding: 12px 25px;
            font-size: 16px;
            font-weight: 600;
            border-radius: 50px;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 5px 15px rgba(255, 123, 0, 0.3);
            z-index: 100;
        }

        .cancel-btn:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 20px rgba(255, 123, 0, 0.4);
        }

        /* Main Content Box */
        .content-box {
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 100px 20px 50px;
        }

        .register {
            background: rgba(255, 255, 255, 0.05);
            backdrop-filter: blur(10px);
            padding: 40px;
            border-radius: 20px;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.5);
            border: 1px solid rgba(255, 255, 255, 0.1);
            width: 100%;
            max-width: 500px;
        }

        .register h2 {
            font-size: 2rem;
            margin-bottom: 30px;
            text-align: center;
            color: #ff7b00;
            background: linear-gradient(45deg, #ff7b00, #ffaa00);
            -webkit-background-clip: text;
            background-clip: text;
            color: transparent;
        }

        /* Form Styling */
        #register {
            display: flex;
            flex-direction: column;
        }

        #register > h2 {
            font-size: 1.5rem;
            color: #fff;
            margin-bottom: 20px;
            text-align: center;
            text-shadow: 0 0 10px rgba(255, 123, 0, 0.5);
        }

        label {
            font-size: 1rem;
            font-weight: 500;
            margin-bottom: 8px;
            color: #ddd;
        }

        input[type="text"],
        input[type="date"],
        input[type="number"],
        input[type="tel"],
        input[type="email"],
        input[type="password"] {
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

        input[type="text"]:focus,
        input[type="date"]:focus,
        input[type="number"]:focus,
        input[type="tel"]:focus {
            background: rgba(255, 255, 255, 0.15);
            box-shadow: 0 0 10px rgba(255, 123, 0, 0.5);
        }

        input::placeholder {
            color: rgba(255, 255, 255, 0.6);
        }

        input[readonly] {
            background: rgba(255, 255, 255, 0.08);
            cursor: not-allowed;
        }

        /* Price Display */
        .price-display {
            background: rgba(255, 255, 255, 0.08);
            border: 1px solid rgba(255, 123, 0, 0.3);
            border-radius: 10px;
            padding: 15px;
            margin-bottom: 20px;
            font-size: 1.1rem;
            color: #ffaa00;
            font-weight: 600;
            text-align: center;
        }

        /* Submit Button */
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
            margin-top: 10px;
            box-shadow: 0 5px 15px rgba(255, 123, 0, 0.3);
        }

        .btnn:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 20px rgba(255, 123, 0, 0.4);
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .content-box {
                padding: 80px 15px 40px;
            }
            
            .register {
                padding: 30px 20px;
                margin-top: 20px;
            }
            
            .register h2 {
                font-size: 1.8rem;
            }
            
            .cancel-btn {
                top: 20px;
                right: 20px;
                padding: 10px 20px;
                font-size: 14px;
            }
            
            .price-display {
                font-size: 1rem;
                padding: 12px;
            }
        }

        @media (max-width: 480px) {
            .content-box {
                padding: 70px 10px 30px;
            }
            
            .register {
                padding: 25px 15px;
            }
            
            .register h2 {
                font-size: 1.5rem;
            }
            
            input[type="text"],
            input[type="date"],
            input[type="number"],
            input[type="tel"] {
                padding: 12px;
                font-size: 0.95rem;
            }
            
            .btnn {
                padding: 12px;
                font-size: 1rem;
            }
            
            .cancel-btn {
                top: 15px;
                right: 15px;
                padding: 8px 16px;
                font-size: 13px;
            }
        }
    </style>
</head>

<body>
    <!-- Your existing HTML content remains exactly the same -->
    <button class="cancel-btn" onclick="window.location.href='vehiclesdetails.php'">CANCEL</button>

    <div class="content-box">
        <div class="register">
            <h2>BOOKING</h2>
            <form id="register" method="POST">
                <h2>VEHICLE NAME: <?php echo htmlspecialchars($vehicle['VEHICLE_NAME']); ?></h2>
                
                <label for="baseprice">BASE PRICE PER DAY:</label>
                <div class="price-display">
                    Rs.<?php echo number_format($base_price, 2); ?>
                </div>

                <label for="place">BOOKING PLACE:</label>
                <input type="text" name="place" id="place" placeholder="Enter Booking Place" required>

                <label for="date">BOOKING DATE:</label>
                <input type="date" name="date" id="datefield" required onchange="updatePriceAndDuration()">

                <label for="rdate">RETURN DATE:</label>
                <input type="date" name="rdate" id="dfield" required onchange="updatePriceAndDuration()">

                <label for="dur">DURATION (days):</label>
                <input type="number" name="dur" id="dur" readonly>

                <label for="ph">PHONE NUMBER:</label>
                <input type="tel" name="ph" id="ph" maxlength="10" placeholder="Enter Your Phone Number" required>

                <label for="des">DESTINATION:</label>
                <input type="text" name="des" id="des" placeholder="Enter Your Destination" required>

                <label for="totalprice">TOTAL PRICE:</label>
                <div class="price-display" id="total-price">
                    Select valid dates to see total price
                </div>

                <input type="submit" class="btnn" value="PROCEED TO PAYMENT" name="book">
            </form>
        </div>
    </div>

    <script>
        // Your JavaScript remains exactly the same
        const basePrice = <?php echo json_encode($base_price); ?>;
        const vehicleType = <?php echo json_encode(isset($vehicle['VEHICLE_TYPE']) ? $vehicle['VEHICLE_TYPE'] : 'Standard'); ?>;

        function calculateDynamicPrice(bookingDate, duration) {
            const date = new Date(bookingDate);
            const dayOfWeek = date.getDay() + 1;
            const demandFactor = (dayOfWeek >= 6) ? 1.2 : 1.0;

            const month = date.getMonth() + 1;
            const peakMonths = [6, 7, 12];
            const seasonalFactor = peakMonths.includes(month) ? 1.3 : 1.0;

            let durationFactor = 1.0;
            if (duration >= 7) {
                durationFactor = 0.9;
            } else if (duration >= 3) {
                durationFactor = 0.95;
            }

            const dynamicPrice = basePrice * demandFactor * seasonalFactor * durationFactor;
            return Math.round(dynamicPrice * 100) / 100;
        }

        function updatePriceAndDuration() {
            const bookingDate = document.getElementById('datefield').value;
            const returnDate = document.getElementById('dfield').value;
            const totalPriceElement = document.getElementById('total-price');

            if (bookingDate && returnDate && new Date(bookingDate) < new Date(returnDate)) {
                const differenceInTime = new Date(returnDate).getTime() - new Date(bookingDate).getTime();
                const differenceInDays = Math.ceil(differenceInTime / (1000 * 3600 * 24));
                document.getElementById('dur').value = differenceInDays;

                const dynamicPrice = calculateDynamicPrice(bookingDate, differenceInDays);
                const totalPrice = dynamicPrice * differenceInDays;
                totalPriceElement.innerHTML = `<span class="price-value">Rs.${totalPrice.toFixed(2)} (Rs.${dynamicPrice.toFixed(2)}/day)</span>`;
            } else {
                document.getElementById('dur').value = '';
                totalPriceElement.innerHTML = 'Select valid dates to see total price';
                if (bookingDate && returnDate && new Date(bookingDate) >= new Date(returnDate)) {
                    alert("Return date must be after the booking date.");
                    document.getElementById('dfield').value = '';
                }
            }

            if (bookingDate) {
                document.getElementById('dfield').setAttribute('min', bookingDate);
                const returnDate = document.getElementById('dfield').value;
                if (returnDate && new Date(returnDate) < new Date(bookingDate)) {
                    document.getElementById('dfield').value = '';
                    document.getElementById('dur').value = '';
                    totalPriceElement.innerHTML = 'Select valid dates to see total price';
                    alert("Return date has been reset as it was earlier than the new pickup date.");
                }
            }
        }

        const today = new Date();
        const dd = today.getDate();
        const mm = today.getMonth() + 1;
        const yyyy = today.getFullYear();
        const formattedToday = `${yyyy}-${mm < 10 ? '0' + mm : mm}-${dd < 10 ? '0' + dd : dd}`;
        document.getElementById("datefield").setAttribute("min", formattedToday);
        document.getElementById("dfield").setAttribute("min", formattedToday);

        document.getElementById("datefield").addEventListener('change', updatePriceAndDuration);
        document.getElementById("dfield").addEventListener('change', updatePriceAndDuration);
    </script>
</body>
</html>