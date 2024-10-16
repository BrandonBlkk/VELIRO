<?php
session_start();
include("dbconnection.php");

$product_id = $_GET['ProductID'];

// Define Tax Rate
$taxRate = 0.10;

if (isset($_POST['confirmDelete'])) {
    $product_id = $_POST['productId'];

    // Step 1: Retrieve the purchase details related to the product
    $getPurchaseDetails = "SELECT PurchaseID, PurchaseUnitPrice, PurchaseUnitQuantity 
                           FROM purchasedetailtb 
                           WHERE ProductID = '$product_id'";
    $purchaseDetailsQuery = mysqli_query($connect, $getPurchaseDetails);
    $purchaseDetails = mysqli_fetch_assoc($purchaseDetailsQuery);

    if ($purchaseDetails) {
        $purchaseID = $purchaseDetails['PurchaseID'];
        $productPrice = $purchaseDetails['PurchaseUnitPrice'];
        $productQuantity = $purchaseDetails['PurchaseUnitQuantity'];

        // Step 2: Subtract the total cost of the deleted product 
        $totalReduction = $productPrice * $productQuantity;
        $updateTotalAmount = "UPDATE purchasetb 
                              SET TotalAmount = TotalAmount - ($totalReduction + ($totalReduction * $taxRate)) 
                              WHERE PurchaseID = '$purchaseID'";
        $updateTotalAmountQuery = mysqli_query($connect, $updateTotalAmount);

        // Step 3: Subtract the tax for the deleted product 
        $taxReduction = $totalReduction * $taxRate;
        $updateTaxAmount = "UPDATE purchasetb 
                            SET PurchaseTax = PurchaseTax - '$taxReduction' 
                            WHERE PurchaseID = '$purchaseID'";
        $updateTaxAmountQuery = mysqli_query($connect, $updateTaxAmount);

        // Update order detail amounts
        $getOrderDetails = "SELECT OrderID, OrderUnitPrice, OrderUnitQuantity 
                            FROM orderdetailtb 
                            WHERE ProductID = '$product_id'";
        $orderDetailsQuery = mysqli_query($connect, $getOrderDetails);
        $orderDetails = mysqli_fetch_assoc($orderDetailsQuery);

        if ($orderDetails) {
            $orderID = $orderDetails['OrderID'];
            $orderPrice = $orderDetails['OrderUnitPrice'];
            $orderQuantity = $orderDetails['OrderUnitQuantity'];

            // Calculate reductions for order total amount and tax
            $orderTotalReduction = $orderPrice * $orderQuantity;
            $orderTaxReduction = $orderTotalReduction * $taxRate;

            // Update order total amount
            $updateOrderTotalAmount = "UPDATE ordertb 
                                        SET TotalAmount = TotalAmount - ($orderTotalReduction + ($orderTotalReduction * $taxRate)) 
                                        WHERE OrderID = '$orderID'";
            $updateOrderTotalAmountQuery = mysqli_query($connect, $updateOrderTotalAmount);

            // Update order tax amount
            $updateOrderTaxAmount = "UPDATE ordertb 
                                      SET OrderTax = OrderTax - '$orderTaxReduction' 
                                      WHERE OrderID = '$orderID'";
            $updateOrderTaxAmountQuery = mysqli_query($connect, $updateOrderTaxAmount);
        }
    }

    // Step 4: Delete associated records from the reviewtb
    $deleteReview = "DELETE FROM reviewtb WHERE ProductID = '$product_id'";
    $deleteReviewQuery = mysqli_query($connect, $deleteReview);

    // Step 5: Delete associated records from the favoritetb
    $deleteFavorite = "DELETE FROM favoritetb WHERE ProductID = '$product_id'";
    $deleteFavoriteQuery = mysqli_query($connect, $deleteFavorite);

    // Step 6: Delete associated records from the carttb
    $deleteCart = "DELETE FROM carttb WHERE ProductID = '$product_id'";
    $deleteCartQuery = mysqli_query($connect, $deleteCart);

    // Step 7: Delete associated records from the orderdetailtb
    $deleteOrderDetails = "DELETE FROM orderdetailtb WHERE ProductID = '$product_id'";
    $deleteOrderDetailsQuery = mysqli_query($connect, $deleteOrderDetails);

    // Step 8: Delete associated records from the purchasedetailtb
    $deletePurchaseDetails = "DELETE FROM purchasedetailtb WHERE ProductID = '$product_id'";
    $deletePurchaseDetailsQuery = mysqli_query($connect, $deletePurchaseDetails);

    if ($deleteReviewQuery && $deleteFavoriteQuery && $deleteCartQuery && $deleteOrderDetailsQuery && $deletePurchaseDetailsQuery) {
        // Step 9: Delete the Product from producttb
        $productDelete = "DELETE FROM producttb WHERE ProductID = '$product_id'";
        $productQuery = mysqli_query($connect, $productDelete);

        if ($productQuery) {
            echo "<script>window.alert('Product data has been successfully deleted.')</script>";
            echo "<script>window.location = 'AdminDashboard.php'</script>";
        } else {
            echo "<script>window.alert('Something went wrong.')</script>";
            echo "<script>window.location = 'AdminDashboard.php'</script>";
        }
    } else {
        echo "<script>window.alert('Failed to delete associated records. Cannot proceed with product deletion.')</script>";
        echo "<script>window.location = 'AdminDashboard.php'</script>";
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
        <h2 class="text-2xl font-semibold text-gray-800 mb-4">Delete Product</h2>
        <p class="mb-6">Are you sure you want to delete this product?</p>
        <form action="" method="post">
            <input type="hidden" name="productId" value="<?php echo htmlspecialchars($product_id); ?>">
            <div class="flex justify-end space-x-4">
                <a href="AdminDashboard.php" class="px-4 py-2 bg-gray-200 text-gray-700 rounded hover:bg-gray-300 transition duration-200">Cancel</a>
                <button type="submit" name="confirmDelete" class="px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700 transition duration-200">Delete</button>
            </div>
        </form>
    </div>
</body>

</html>