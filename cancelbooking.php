<?php
session_start();
require_once('connection.php');

// Check if user is logged in
if (!isset($_SESSION['email'])) {
    header("Location: login.php");
    exit();
}

$email = $_SESSION['email'];

// Check if booking ID is provided
if (isset($_GET['id'])) {
    $book_id = mysqli_real_escape_string($con, $_GET['id']);
    
    // Verify the booking belongs to the user before canceling
    $verify_sql = "SELECT * FROM booking WHERE BOOK_ID = '$book_id' AND EMAIL = '$email'";
    $verify_result = mysqli_query($con, $verify_sql);
    
    if (mysqli_num_rows($verify_result) > 0) {
        // Update specific booking to Canceled
        $sql = "UPDATE booking SET BOOK_STATUS = 'Canceled' WHERE BOOK_ID = '$book_id' AND EMAIL = '$email'";
        $result = mysqli_query($con, $sql);
        
        if ($result) {
            echo '<script src="main.js"></script>';
            echo '<script>showDialog("Booking canceled successfully", function() { window.location.href = "bookingstatus.php"; });</script>';
        } else {
            echo '<script>showDialog("Error canceling booking. Please try again.", function() { window.location.href = "bookingstatus.php"; });</script>';
        }
    } else {
        echo '<script>showDialog("Invalid booking or unauthorized access", function() { window.location.href = "bookingstatus.php"; });</script>';
    }
} else {
    echo '<script>showDialog("No booking ID provided", function() { window.location.href = "bookingstatus.php"; });</script>';
}
?>