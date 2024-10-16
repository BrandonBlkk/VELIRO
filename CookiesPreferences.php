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

    <div class="p-4">
        <div class="bg-white shadow-lg rounded-lg p-6 max-w-4xl mx-auto">
            <h1 class="text-2xl font-bold mb-4">Cookie Preferences</h1>
            <p class="mb-6 text-gray-600">We use cookies to ensure that we give you the best experience on our website. You can manage your preferences below.</p>

            <form action="#" method="post">
                <!-- Essential Cookies -->
                <div class="mb-6">
                    <h2 class="text-lg font-semibold">Essential Cookies</h2>
                    <p class="text-gray-600 mb-4">These cookies are necessary for the website to function and cannot be switched off in our systems.</p>
                    <label class="inline-flex items-center">
                        <input type="checkbox" class="form-checkbox text-blue-600" checked disabled>
                        <span class="ml-2 text-gray-700">Always Active</span>
                    </label>
                </div>

                <!-- Performance Cookies -->
                <div class="mb-6">
                    <h2 class="text-lg font-semibold">Performance Cookies</h2>
                    <p class="text-gray-600 mb-4">These cookies allow us to count visits and traffic sources so we can measure and improve the performance of our site.</p>
                    <label class="inline-flex items-center">
                        <input type="checkbox" class="form-checkbox text-blue-600">
                        <span class="ml-2 text-gray-700">Enable</span>
                    </label>
                </div>

                <!-- Functional Cookies -->
                <div class="mb-6">
                    <h2 class="text-lg font-semibold">Functional Cookies</h2>
                    <p class="text-gray-600 mb-4">These cookies enable the website to provide enhanced functionality and personalization.</p>
                    <label class="inline-flex items-center">
                        <input type="checkbox" class="form-checkbox text-blue-600">
                        <span class="ml-2 text-gray-700">Enable</span>
                    </label>
                </div>

                <!-- Targeting Cookies -->
                <div class="mb-6">
                    <h2 class="text-lg font-semibold">Targeting Cookies</h2>
                    <p class="text-gray-600 mb-4">These cookies may be set through our site by our advertising partners.</p>
                    <label class="inline-flex items-center">
                        <input type="checkbox" class="form-checkbox text-blue-600">
                        <span class="ml-2 text-gray-700">Enable</span>
                    </label>
                </div>

                <!-- Save Preferences Button -->
                <div class="flex justify-end">
                    <button type="submit" class="bg-blue-600 text-white font-semibold py-2 px-4 rounded-lg hover:bg-blue-700 focus:outline-none">Save Preferences</button>
                </div>
            </form>
        </div>
    </div>

    <?php
    include('./components/Footer.php');
    ?>

    <script
        script src="Customer.js">
    </script>
</body>

</html>