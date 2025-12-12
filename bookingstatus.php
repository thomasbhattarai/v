<?php
require_once('connection.php');
session_start();
$email = $_SESSION['email'];

$sql = "SELECT * FROM booking WHERE EMAIL='$email' AND BOOK_STATUS != 'Canceled' ORDER BY BOOK_ID DESC LIMIT 1";
$name = mysqli_query($con, $sql);
$rows = mysqli_fetch_assoc($name);

if ($rows == null) {
    // Removed alert here
    ?>
    <button class="utton"><a href="vehiclesdetails.php">Go to Home</a></button>
    <div class="name">HELLO!</div>
    <div class="box">
        <div class="content">
            <p class="no-bookings">No active bookings</p>
        </div>
    </div>
    <?php
} else {

    $sql2 = "SELECT * FROM users WHERE EMAIL='$email'";
    $name2 = mysqli_query($con, $sql2);
    $rows2 = mysqli_fetch_assoc($name2);

    $vehicle_id = $rows['VEHICLE_ID'];
    $sql3 = "SELECT * FROM vehicles WHERE VEHICLE_ID='$vehicle_id'";
    $name3 = mysqli_query($con, $sql3);
    $rows3 = mysqli_fetch_assoc($name3);
?>

<button class="utton"><a href="vehiclesdetails.php">Go to Home</a></button>
<div class="name">HELLO!</div>
<button class="cancel-btn"><a href="cancelbooking.php">Cancel Booking</a></button>
<div class="box">
    <div class="content">
        <h1>VEHICLE NAME: <?php echo htmlspecialchars($rows3['VEHICLE_NAME']); ?></h1><br>
        <h1>NO OF DAYS: <?php echo $rows['DURATION']; ?></h1><br>
        <h1>BOOKING STATUS: <?php echo $rows['BOOK_STATUS']; ?></h1><br>
    </div>
</div>

<?php } ?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BOOKING STATUS - VeloRent</title>
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
            align-items: center;
            justify-content: center;
            padding: 20px;
            position: relative;
        }

        /* Cancel Button */
        .cancel-btn {
            position: absolute;
            top: 30px;
            right: 30px;
            background: linear-gradient(45deg, #ff4444, #cc0000);
            border: none;
            color: white;
            padding: 12px 25px;
            font-size: 16px;
            font-weight: 600;
            border-radius: 50px;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 5px 15px rgba(255, 68, 68, 0.3);
            z-index: 100;
        }

        .cancel-btn:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 20px rgba(255, 68, 68, 0.4);
        }

        .cancel-btn a {
            color: white;
            text-decoration: none;
        }

        /* Home Button */
        .utton {
            position: absolute;
            top: 30px;
            left: 30px;
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

        .utton:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 20px rgba(255, 123, 0, 0.4);
        }

        .utton a {
            color: white;
            text-decoration: none;
        }

        /* Welcome Message */
        .name {
            font-size: 2.5rem;
            font-weight: 700;
            color: #fff;
            margin-bottom: 30px;
            text-align: center;
            background: linear-gradient(45deg, #ff7b00, #ffaa00);
            -webkit-background-clip: text;
            background-clip: text;
            color: transparent;
            text-shadow: 0 0 10px rgba(255, 123, 0, 0.5);
            animation: fadeIn 1s ease;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(-20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        /* Content Box */
        .box {
            background: rgba(255, 255, 255, 0.05);
            backdrop-filter: blur(10px);
            border-radius: 20px;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.5);
            border: 1px solid rgba(255, 255, 255, 0.1);
            padding: 40px;
            margin: 20px 0;
            width: 100%;
            max-width: 600px;
            animation: slideUp 0.8s ease;
        }

        @keyframes slideUp {
            from { opacity: 0; transform: translateY(30px); }
            to { opacity: 1; transform: translateY(0); }
        }

        /* Content Styling */
        .content {
            text-align: center;
        }

        h1 {
            font-size: 1.5rem;
            margin-bottom: 20px;
            color: #fff;
            font-weight: 600;
            padding-bottom: 15px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }

        /* No Bookings Message */
        .no-bookings {
            font-size: 1.2rem;
            color: #ff7b00;
            padding: 20px;
            text-align: center;
            font-weight: 500;
        }

        /* Status Color Coding */
        .status-active {
            color: #4CAF50;
            font-weight: 700;
        }

        .status-pending {
            color: #FFC107;
            font-weight: 700;
        }

        .status-completed {
            color: #2196F3;
            font-weight: 700;
        }

        /* Vehicle Info Highlight */
        .vehicle-info {
            color: #ff7b00;
            font-weight: 600;
            margin: 5px 0;
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            body {
                padding: 80px 15px 40px;
            }
            
            .utton, .cancel-btn {
                position: fixed;
                top: 20px;
                padding: 10px 20px;
                font-size: 14px;
            }
            
            .utton {
                left: 20px;
            }
            
            .cancel-btn {
                right: 20px;
            }
            
            .name {
                font-size: 2rem;
                margin-top: 40px;
            }
            
            .box {
                padding: 30px 20px;
                margin-top: 20px;
            }
            
            h1 {
                font-size: 1.3rem;
            }
        }

        @media (max-width: 480px) {
            body {
                padding: 70px 10px 30px;
            }
            
            .utton, .cancel-btn {
                top: 15px;
                padding: 8px 16px;
                font-size: 13px;
            }
            
            .name {
                font-size: 1.8rem;
                margin-top: 50px;
            }
            
            .box {
                padding: 25px 15px;
            }
            
            h1 {
                font-size: 1.1rem;
                margin-bottom: 15px;
            }
            
            .no-bookings {
                font-size: 1rem;
            }
        }

        /* Scrollbar Styling */
        ::-webkit-scrollbar {
            width: 10px;
        }

        ::-webkit-scrollbar-track {
            background: rgba(255, 255, 255, 0.05);
        }

        ::-webkit-scrollbar-thumb {
            background: linear-gradient(45deg, #ff7b00, #ff5500);
            border-radius: 5px;
        }

        ::-webkit-scrollbar-thumb:hover {
            background: linear-gradient(45deg, #ff5500, #ff7b00);
        }
    </style>
</head>
<body>

    
</body>
</html>