<?php
session_start();
include('DbConnection.php');

// Check if cutomer singin or not
$id = (isset($_SESSION['CustomerID']) && !empty($_SESSION['CustomerID'])) ? $_SESSION['CustomerID'] : $id = null;

// Update specified product from cart
if (isset($_POST['updateBtn'])) {
    $cart_id = $_POST['cart_id'];
    $new_quantity = $_POST['new_quantity'];

    // Get the old quantity from the cart
    $old_quantity_query = "SELECT Quantity, ProductID FROM carttb WHERE CartID = '$cart_id' AND CustomerID = '$id'";
    $old_quantity_result = mysqli_query($connect, $old_quantity_query);
    $old_quantity_row = mysqli_fetch_assoc($old_quantity_result);
    $old_quantity = $old_quantity_row['Quantity'];
    $product_id = $old_quantity_row['ProductID'];

    // Calculate the quantity difference
    $quantity_difference = $new_quantity - $old_quantity;

    // Update product quantity in carttb table
    $update_query = "UPDATE carttb SET Quantity = '$new_quantity' WHERE CartID = '$cart_id' AND CustomerID = '$id'";
    $update_result = mysqli_query($connect, $update_query);

    if ($update_result) {
        // Update product stock in producttb table
        $update_stock_query = "UPDATE producttb SET Stock = Stock - '$quantity_difference' WHERE ProductID = '$product_id' AND Stock >= '$quantity_difference'";
        mysqli_query($connect, $update_stock_query);

        // Product successfully updated in cart
        $_SESSION['alert_message'] = "Product has been successfully updated in your cart.";
        $_SESSION['alert_class'] = "bg-green-200 border-green-400 text-green-800";
        $loader = "Load";
    }
    // Redirect to the same page to avoid form resubmission
    header("Location: " . $_SERVER['PHP_SELF']);
    exit;
}

// Delete specified product from cart
if (isset($_POST['deleteBtn'])) {
    $cart_id = $_POST['cart_id'];

    // Get the product ID and quantity from the cart
    $cart_item_query = "SELECT ProductID, Quantity FROM carttb WHERE CartID = '$cart_id' AND CustomerID = '$id'";
    $cart_item_result = mysqli_query($connect, $cart_item_query);

    if ($cart_item_result && mysqli_num_rows($cart_item_result) > 0) {
        $cart_item = mysqli_fetch_assoc($cart_item_result);
        $product_id = $cart_item['ProductID'];
        $quantity = $cart_item['Quantity'];

        // DELETE product based on clicked product
        $cart_delete_query = "DELETE FROM carttb WHERE CartID = '$cart_id' AND CustomerID = '$id'";
        $cart_delete_result = mysqli_query($connect, $cart_delete_query);

        if ($cart_delete_result) {
            // Update the stock in the producttb table
            $update_stock_query = "UPDATE producttb SET stock = stock + $quantity WHERE ProductID = '$product_id'";
            mysqli_query($connect, $update_stock_query);

            // Product successfully deleted from cart
            $_SESSION['alert_message'] = "Product has been successfully deleted from your cart.";
            $_SESSION['alert_class'] = "bg-green-200 border-green-400 text-green-800";
            $loader = "Load";
        }
    }
    // Redirect to the same page to avoid form resubmission
    header("Location: " . $_SERVER['PHP_SELF']);
    exit;
}

// Check for session alert message
$alert_message = isset($_SESSION['alert_message']) ? $_SESSION['alert_message'] : '';
$alert_class = isset($_SESSION['alert_class']) ? $_SESSION['alert_class'] : '';

// Clear the alert message from session after displaying it
if (!empty($alert_message)) {
    unset($_SESSION['alert_message']);
    unset($_SESSION['alert_class']);
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

<body>

    <?php
    include('./components/Header.php');
    ?>

    <section class="flex flex-col  items-center p-3 pb-10">
        <div class="text-center">
            <h1 class="text-2xl sm:text-6xl md:text-3xl font-semibold mb-2">RETURNS</h1>
            <p class="text-justify">See below for information about the delivery & returns</p>
        </div>
        <div class="border-b-0 sm:border-b-2 flex flex-col sm:flex-row justify-center mt-5 w-full md:w-[800px]">
            <a href="Delivery.php" class="flex items-center gap-5 py-4 px-16">
                <i class="ri-truck-line text-2xl"></i>
                <p>DELIVERY</p>
            </a>
            <a href="ReturnPolicy.php" class="relative flex items-center gap-5 py-4 px-16 border-t-0 sm:border-t-2 border-l-0 sm:border-l-2 border-r-0 sm:border-r-2">
                <i class="ri-arrow-go-back-line text-2xl"></i>
                <p>RETURNS</p>
                <div class="absolute left-0 -bottom-1 bg-white w-full h-2"></div>
            </a>
        </div>
        <div class="max-w-[600px] mt-5 p-7">
            <h1 class="text-xl font-semibold mb-3">About VELIRO returns</h1>
            <ul>
                <li class="list-disc mb-1"><span class="font-semibold">Return Period:</span> Factory-damaged products can be returned within 60 days of the purchase date.</li>
                <li class="list-disc mb-1"><span class="font-semibold">Eligible Products:</span> Only products with factory defects or damages are eligible for returns or repairs.</li>
                <li class="list-disc mb-1"><span class="font-semibold">Return Locations:</span> Returns can be processed at the physical store where the product was originally purchased.</li>
                <li class="list-disc mb-1"><span class="font-semibold">Service Options:</span> Customers have the option to:</li>
                <ul class="ml-5">
                    <li class="list-disc">Repair the damaged product.</li>
                    <li class="list-disc">Replace the damaged product with a new one, subject to availability.</li>
                </ul>
                <li class="list-disc mb-1"><span class="font-semibold">Proof of Purchase:</span> Please bring your original receipt or proof of purchase when returning the product.</li>
                <li class="list-disc mb-1"><span class="font-semibold">Condition of Products:</span> Products should be in their original condition with all tags and packaging intact.</li>
                <li class="list-disc mb-1"><span class="font-semibold">Inspection:</span> All returns are subject to an inspection to confirm the factory damage.</li>
                <li class="list-disc mb-1"><span class="font-semibold">Customer Service:</span> For questions or assistance with the return process, customers can contact our customer service team at [customer service phone number] or [customer service email].</li>
                <li class="list-disc"><span class="font-semibold">Exclusions:</span> Products not showing factory damage or those beyond the 60-day return window are not eligible for return or repair under this policy.</li>
            </ul>
        </div>
    </section>

    <?php
    include('./components/Footer.php');
    ?>

    <script src="Customer.js"></script>
</body>

</html>