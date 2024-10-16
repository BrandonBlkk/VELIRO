<?php
session_start();
include("dbconnection.php");
session_destroy();

echo "<script>window.alert('All done! Have a nice day.')</script>";
echo "<script>window.location = 'Home.php'</script>";
