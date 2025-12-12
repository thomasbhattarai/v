<?php

require_once('connection.php');
$vehicleid=$_GET['id'];
$sql="DELETE from vehicles where VEHICLE_ID=$vehicleid";
$result=mysqli_query($con,$sql);

echo '<script>alert("VEHICLE DELETED SUCCESFULLY")</script>';
echo '<script> window.location.href = "adminvehicle.php";</script>';



?>