<?php
session_start();
require_once('connection.php');

// Check if user is logged in
if (!isset($_SESSION['email'])) {
    header("Location: login.php");
    exit();
}

$email = $_SESSION['email'];

// Update the most recent booking to Canceled
$sql = "UPDATE booking SET BOOK_STATUS = 'Canceled' WHERE EMAIL = '$email' ORDER BY BOOK_ID DESC LIMIT 1";
$result = mysqli_query($con, $sql);

if ($result) {
    echo '<script>alert("Booking canceled successfully")</script>';
    echo '<script>window.location.href = "vehiclesdetails.php";</script>';
} else {
    echo '<script>alert("Error canceling booking. Please try again.")</script>';
    echo '<script>window.location.href = "bookingstatus.php";</script>';
}
?>