<?php
header('Content-Type: application/json');
require_once('connection.php');

if (isset($_GET['id']) && isset($_GET['bookid'])) {
    $vehicle_id = mysqli_real_escape_string($con, $_GET['id']);
    $book_id = mysqli_real_escape_string($con, $_GET['bookid']);
    $query = "DELETE FROM booking WHERE BOOK_ID = '$book_id' AND VEHICLE_ID = '$vehicle_id'";
    if (mysqli_query($con, $query)) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'error' => mysqli_error($con)]);
    }
} else {
    echo json_encode(['success' => false, 'error' => 'Invalid parameters']);
}
mysqli_close($con);
?>