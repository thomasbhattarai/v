<?php
header('Content-Type: application/json');
require_once('connection.php');

if (isset($_GET['id'])) {
    $book_id = mysqli_real_escape_string($con, $_GET['id']);
    $query = "UPDATE booking SET BOOK_STATUS = 'APPROVED' WHERE BOOK_ID = '$book_id'";
    if (mysqli_query($con, $query)) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'error' => mysqli_error($con)]);
    }
} else {
    echo json_encode(['success' => false, 'error' => 'No booking ID provided']);
}
mysqli_close($con);
?>