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

$orderDetails = null;
$errorMsg = '';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get user input
    $orderID = mysqli_real_escape_string($connect, $_POST['order_number']);
    $customerEmail = mysqli_real_escape_string($connect, $_POST['customer_email']);

    // Fetch order details based on OrderID and CustomerEmail
    $orderQuery = "
        SELECT o.OrderID, o.CustomerPhone, o.ShippingAddress, o.City, o.State, o.PaymentMethod, o.TotalAmount, o.Status, o.OrderDate
        FROM ordertb o
        JOIN customertb c ON o.CustomerID = c.CustomerID
        WHERE o.OrderID = '$orderID' AND c.CustomerEmail = '$customerEmail'";

    $orderResult = mysqli_query($connect, $orderQuery);

    // Check if order exists
    if (mysqli_num_rows($orderResult) > 0) {
        $orderDetails = mysqli_fetch_assoc($orderResult);
    } else {
        $errorMsg = "No order found with the provided Order Number and Email.";
    }
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

    <!-- Help Center Header -->
    <header class="bg-white shadow">
        <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
            <h1 class="text-3xl font-bold text-gray-900">Help Center</h1>
        </div>
    </header>

    <!-- Main Content -->
    <main class="py-5">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- FAQ Section -->
            <section class="bg-white shadow-sm rounded-lg p-6 mb-8">
                <h2 class="text-2xl font-semibold text-gray-800 mb-4">Frequently Asked Questions (FAQs)</h2>
                <div class="space-y-4">
                    <div>
                        <h3 class="text-lg font-medium text-gray-700">How do I track my order?</h3>
                        <p class="text-gray-600">You can track your order by visiting the 'Order Tracking' section and entering your order number and email address.</p>
                    </div>
                    <div>
                        <h3 class="text-lg font-medium text-gray-700">What is your return policy?</h3>
                        <p class="text-gray-600">We accept returns within 60 days of delivery. The product must be in its original condition. Visit our 'Shipping & Returns' page for more details.</p>
                    </div>
                    <div>
                        <h3 class="text-lg font-medium text-gray-700">How can I contact customer support?</h3>
                        <p class="text-gray-600">You can reach our customer support team via email at <a href="mailto:mail@veliro.com" class="text-blue-500 hover:underline">mail@veliro.com</a> or call us at +123-456-7890.</p>
                    </div>
                </div>
            </section>

            <!-- Contact Information Section -->
            <section class="bg-white shadow-sm rounded-lg p-6 mb-8">
                <h2 class="text-2xl font-semibold text-gray-800 mb-4">Contact Us</h2>
                <p class="text-gray-600 mb-4">For any queries, feel free to reach out to us:</p>
                <ul class="list-disc list-inside text-gray-600">
                    <li>Email: <a href="mailto:mail@veliro.com" class="text-blue-500">mail@veliro.com</a></li>
                    <li>Phone: +1 123-456-7890</li>
                    <li>Address: No (15) Ground floor , 49 lower street, Merchant Rd, 11161
                        Yangon, Myanmar</li>
                </ul>
            </section>

            <!-- Order Tracking Section -->
            <section class="bg-white shadow-sm rounded-lg p-6 mb-8">
                <h2 class="text-2xl font-semibold text-gray-800 mb-4">Order Tracking</h2>
                <p class="text-gray-600 mb-4">Enter your order number and email address to track your order:</p>
                <form class="space-y-4" method="POST">
                    <input type="text" name="order_number" class="w-full border border-gray-300 p-2 rounded" placeholder="Order Number" required>
                    <input type="email" name="customer_email" class="w-full border border-gray-300 p-2 rounded" placeholder="Email Address" required>
                    <button type="submit" class="bg-indigo-500 text-white py-2 px-4 rounded hover:bg-indigo-600">Track Order</button>
                </form>

                <!-- Display order details if found -->
                <?php if ($orderDetails) { ?>
                    <div class="mt-6 p-4 bg-gray-50 border border-gray-200 rounded">
                        <h3 class="text-xl font-semibold text-gray-800 mb-4">Order Details</h3>
                        <p><strong>Order ID:</strong> <?php echo htmlspecialchars($orderDetails['OrderID']); ?></p>
                        <p><strong>Phone:</strong> <?php echo htmlspecialchars($orderDetails['CustomerPhone']); ?></p>
                        <p><strong>Shipping Address:</strong> <?php echo htmlspecialchars($orderDetails['ShippingAddress']); ?>, <?php echo htmlspecialchars($orderDetails['City']); ?>, <?php echo htmlspecialchars($orderDetails['State']); ?></p>
                        <p><strong>Payment Method:</strong> <?php echo htmlspecialchars($orderDetails['PaymentMethod']); ?></p>
                        <p><strong>Total Amount:</strong> $<?php echo number_format($orderDetails['TotalAmount'], 2); ?></p>
                        <p><strong>Status:</strong> <?php echo htmlspecialchars($orderDetails['Status']); ?></p>
                        <p><strong>Order Date:</strong> <?php echo htmlspecialchars($orderDetails['OrderDate']); ?></p>
                    </div>
                <?php } elseif ($errorMsg) { ?>
                    <!-- Display error message if no order is found -->
                    <p class="text-red-500 mt-4"><?php echo htmlspecialchars($errorMsg); ?></p>
                <?php } ?>
            </section>

            <!-- Shipping & Returns Section -->
            <section class="bg-white shadow-sm rounded-lg p-6 mb-8">
                <h2 class="text-2xl font-semibold text-gray-800 mb-4">Shipping & Returns</h2>
                <p class="text-gray-600 mb-4">We offer free shipping on all orders over $323.72. Returns are accepted within 60 days of delivery, provided the item is in its original condition.</p>
                <p class="text-gray-600">For more details, visit our 'Shipping & Returns' page or contact our support team.</p>
            </section>
        </div>
    </main>

    <?php
    include('./components/Footer.php');
    ?>

    <script
        script src="Customer.js">
    </script>
</body>

</html>