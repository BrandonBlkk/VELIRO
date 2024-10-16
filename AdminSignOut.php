<?php
session_start();
include("DbConnection.php");

$admin_id = $_SESSION['AdminID'];
// Update the current admin's status to Inactive
$update_status = "UPDATE admintb SET AdminStatus = 'Iactive' WHERE AdminID = '$admin_id'";
mysqli_query($connect, $update_status);

session_destroy();

echo "<script>window.alert('Signout Successful.')</script>";
echo "<script>window.location = 'AdminSignIn.php'</script>";
