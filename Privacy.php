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

    <header class="bg-white shadow">
        <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
            <h1 class="text-3xl font-bold text-gray-900">Privacy Policy</h1>
        </div>
    </header>

    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-5">
        <section class="bg-white p-8">
            <h2 class="text-2xl font-bold mb-4">Introduction</h2>
            <p class="mb-6">
                At VELIRO, we value the privacy of our customers. This Privacy Policy explains how we collect, use, and protect your personal information when you visit our website and make purchases from our store.
            </p>

            <h2 class="text-2xl font-bold mb-4">Information We Collect</h2>
            <p class="mb-6">
                We collect personal information such as your name, email address, shipping address, payment details, and browsing behavior to provide a better shopping experience. This information is collected when you register, make a purchase, or interact with our website.
            </p>

            <h2 class="text-2xl font-bold mb-4">How We Use Your Information</h2>
            <p class="mb-6">
                The information we collect is used to process orders, manage your account, improve our website, and offer personalized recommendations. We may also use your email address to send promotional offers and updates about our products and services.
            </p>

            <h2 class="text-2xl font-bold mb-4">Sharing Your Information</h2>
            <p class="mb-6">
                We do not sell, trade, or rent your personal information to third parties. We may share your information with trusted partners to fulfill orders, process payments, and improve our services. These partners are obligated to keep your information secure.
            </p>

            <h2 class="text-2xl font-bold mb-4">Cookies and Tracking Technologies</h2>
            <p class="mb-6">
                Our website uses cookies and similar technologies to enhance user experience and analyze website traffic. You can control the use of cookies through your browser settings. However, disabling cookies may affect the functionality of our website.
            </p>

            <h2 class="text-2xl font-bold mb-4">Security of Your Information</h2>
            <p class="mb-6">
                We implement various security measures to protect your personal information from unauthorized access, disclosure, or misuse. Despite our efforts, no method of transmission over the internet or electronic storage is 100% secure.
            </p>

            <h2 class="text-2xl font-bold mb-4">Your Rights</h2>
            <p class="mb-6">
                You have the right to access, correct, or delete your personal information. If you wish to exercise these rights or have any questions about our Privacy Policy, please contact us at <a href="mailto:mail@veliro.com" class="text-blue-500 hover:underline">mail@veliro.com</a>.
            </p>

            <h2 class="text-2xl font-bold mb-4">Changes to This Policy</h2>
            <p class="mb-6">
                We reserve the right to update this Privacy Policy at any time. We will notify you of any significant changes by posting a notice on our website or by sending you an email.
            </p>

            <h2 class="text-2xl font-bold mb-4">Contact Us</h2>
            <p>
                If you have any questions or concerns about our Privacy Policy or your personal information, please contact us at <a href="mailto:mail@veliro.com" class="text-blue-500 hover:underline">mail@veliro.com</a>.
            </p>
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