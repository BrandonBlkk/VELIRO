<?php
session_start();
include('DbConnection.php');

// Check if cutomer singin or not
if (isset($_SESSION['CustomerID']) && !empty($_SESSION['CustomerID'])) {
    $id = $_SESSION['CustomerID'];
} else {
    $id = null;
}

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

    <header class="bg-white shadow">
        <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
            <h1 class="text-3xl font-bold text-gray-900">Terms of Use</h1>
        </div>
    </header>

    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-5">
        <section class="bg-white p-8">
            <h2 class="text-2xl font-semibold mb-4">1. Introduction</h2>
            <p class="mb-6">Welcome to our men's clothing e-commerce website. By accessing and using our website, you agree to comply with and be bound by the following terms and conditions. Please review these terms carefully.</p>

            <h2 class="text-2xl font-semibold mb-4">2. Intellectual Property</h2>
            <p class="mb-6">All content on this website, including but not limited to text, graphics, logos, images, and software, is the property of our company and is protected by applicable intellectual property laws. You may not reproduce, distribute, or otherwise use any content without our prior written permission.</p>

            <h2 class="text-2xl font-semibold mb-4">3. User Conduct</h2>
            <p class="mb-6">You agree to use our website for lawful purposes only. You may not use our website in any way that could damage, disable, or impair our website or interfere with any other party's use and enjoyment of our website.</p>

            <h2 class="text-2xl font-semibold mb-4">4. Account Registration</h2>
            <p class="mb-6">To access certain features of our website, you may be required to create an account. You agree to provide accurate and complete information when creating an account and to keep your account information updated. You are responsible for maintaining the confidentiality of your account and password.</p>

            <h2 class="text-2xl font-semibold mb-4">5. Limitation of Liability</h2>
            <p class="mb-6">Our company shall not be liable for any damages arising from your use of our website or from any information, content, materials, or products included on or otherwise made available to you through our website.</p>

            <h2 class="text-2xl font-semibold mb-4">6. Changes to Terms of Use</h2>
            <p class="mb-6">We reserve the right to change these terms of use at any time. Your continued use of the website following the posting of changes will mean that you accept and agree to the changes.</p>

            <h2 class="text-2xl font-semibold mb-4">7. Governing Law</h2>
            <p class="mb-6">These terms and conditions are governed by and construed in accordance with the laws of [Your Country/State], and you irrevocably submit to the exclusive jurisdiction of the courts in that State or location.</p>

            <h2 class="text-2xl font-semibold mb-4">8. Contact Us</h2>
            <p class="mb-6">If you have any questions about these Terms of Use, please contact us at <a href="mailto:mail@veliro.com" class="text-blue-500 hover:underline">mail@veliro.com</a>.</p>
        </section>
    </main>

    <?php
    include('./components/Footer.php');
    ?>

    <script
        script src="Customer.js">
    </script>
</body>

</html>