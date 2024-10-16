<?php
session_start();
include('DbConnection.php');

// Get ContactID from query string
if (isset($_GET['ContactID'])) {
    $contactID = intval($_GET['ContactID']);

    // Prepare and execute the update statement
    $stock_update = "UPDATE cuscontacttb SET Status = 'Responded' WHERE ContactID = '$contactID'";
    $stock_update_query = mysqli_query($connect, $stock_update);

    if ($stock_update_query) {
        echo "<script>window.location = 'UserContact.php'</script>";
    }
}
