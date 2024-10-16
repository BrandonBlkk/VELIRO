<?php
session_start();
include("dbconnection.php");

$customer_id = $_GET['CustomerID'];

// Delete customer account
if (isset($_POST['confirmDelete'])) {
    $customerID = $_POST['customerId'];

    // Step 1: Increase the product quantity before deleting associated cart records
    $getCartItems = "SELECT ProductID, Quantity FROM carttb WHERE CustomerID = '$customerID'";
    $getCartItemsQuery = mysqli_query($connect, $getCartItems);

    if ($getCartItemsQuery) {
        while ($cartItem = mysqli_fetch_assoc($getCartItemsQuery)) {
            $productID = $cartItem['ProductID'];
            $quantity = $cartItem['Quantity'];

            // Update product quantity in the producttb
            $updateProductQuantity = "UPDATE producttb SET stock = stock + $quantity WHERE ProductID = '$productID'";
            mysqli_query($connect, $updateProductQuantity);
        }
    }

    // Step 2: Delete associated records from the carttb
    $deleteCart = "DELETE FROM carttb WHERE CustomerID = '$customerID'";
    $deleteCartQuery = mysqli_query($connect, $deleteCart);

    // Step 3: Delete associated records from the favoritetb
    $deleteFavorites = "DELETE FROM favoritetb WHERE CustomerID = '$customerID'";
    $deleteFavoritesQuery = mysqli_query($connect, $deleteFavorites);

    // Step 4: Delete associated records from the reviewtb
    $deleteReviews = "DELETE FROM reviewtb WHERE CustomerID = '$customerID'";
    $deleteReviewsQuery = mysqli_query($connect, $deleteReviews);

    // Step 5: Delete associated records from the orderdetailtb
    $deleteOrderDetails = "DELETE FROM orderdetailtb WHERE OrderID IN (SELECT OrderID FROM ordertb WHERE CustomerID = '$customerID')";
    $deleteOrderDetailsQuery = mysqli_query($connect, $deleteOrderDetails);

    // Step 6: Delete associated records from the ordertb
    $deleteOrders = "DELETE FROM ordertb WHERE CustomerID = '$customerID'";
    $deleteOrdersQuery = mysqli_query($connect, $deleteOrders);

    // Proceed to delete the customer only if all related records are successfully deleted
    if ($deleteCartQuery && $deleteFavoritesQuery && $deleteReviewsQuery && $deleteOrderDetailsQuery && $deleteOrdersQuery) {
        // Step 7: Delete the customer account
        $deleteCustomer = "DELETE FROM customertb WHERE CustomerID = '$customerID'";
        $deleteCustomerQuery = mysqli_query($connect, $deleteCustomer);

        if ($deleteCustomerQuery) {
            // Account successfully deleted
            session_destroy();
            $_SESSION['alert_message'] = "Customer account has been successfully deleted.";
            $_SESSION['alert_class'] = "bg-green-200 border-green-400 text-green-800";
            echo "<script>window.location = 'AdminDashboard.php'</script>";
        } else {
            echo "<script>window.alert('Failed to delete customer account. Please try again.')</script>";
        }
    } else {
        echo "<script>window.alert('Failed to delete associated records. Cannot proceed with account deletion.')</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>VELIRO Men's Clothing</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/remixicon/3.5.0/remixicon.css" integrity="sha512-HXXR0l2yMwHDrDyxJbrMD9eLvPe3z3qL3PPeozNTsiHJEENxx8DH2CxmV05iwG0dwoz5n4gQZQyYLUNt1Wdgfg==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="output.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="style.css?v=<?php echo time(); ?>">
</head>

<body class="bg-gray-100 flex items-center justify-center h-screen">
    <div class="bg-white p-6 rounded shadow-md w-full max-w-md">
        <h2 class="text-2xl font-semibold text-gray-800 mb-4">Delete Customer</h2>
        <p class="mb-6">Are you sure you want to delete this customer?</p>
        <form action="" method="post">
            <input type="hidden" name="customerId" value="<?php echo htmlspecialchars($customer_id); ?>">
            <div class="flex justify-end space-x-4">
                <a href="AdminDashboard.php" class="px-4 py-2 bg-gray-200 text-gray-700 rounded hover:bg-gray-300 transition duration-200">Cancel</a>
                <button type="submit" name="confirmDelete" class="px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700 transition duration-200">Delete</button>
            </div>
        </form>
    </div>
</body>

</html>