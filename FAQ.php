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

    <section class="max-w-6xl mx-auto px-6 pb-6 bg-white shadow-md rounded-lg mt-10">
        <h1 class="text-4xl font-bold text-center text-gray-800 mb-8">Frequently Asked Questions</h1>

        <!-- FAQ Items -->
        <div class="space-y-4">

            <div class="border-b border-gray-200">
                <button class="w-full text-left flex items-center justify-between py-4 text-gray-700 focus:outline-none" onclick="toggleFaq(event)">
                    <span class="text-lg font-medium">What is VELIRO's return policy?</span>
                    <svg class="w-6 h-6 transform transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                    </svg>
                </button>
                <div class="faq-content hidden text-gray-600 mt-2">
                    <p>We offer a 60-day return policy. Items must be in their original condition with tags attached. Contact our support team for assistance.</p>
                </div>
            </div>

            <div class="border-b border-gray-200">
                <button class="w-full text-left flex items-center justify-between py-4 text-gray-700 focus:outline-none" onclick="toggleFaq(event)">
                    <span class="text-lg font-medium">How do I track my order?</span>
                    <svg class="w-6 h-6 transform transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                    </svg>
                </button>
                <div class="faq-content hidden text-gray-600 mt-2">
                    <p>You can track your order by logging into your account and navigating to the 'Order History' section.</p>
                </div>
            </div>

            <div class="border-b border-gray-200">
                <button class="w-full text-left flex items-center justify-between py-4 text-gray-700 focus:outline-none" onclick="toggleFaq(event)">
                    <span class="text-lg font-medium">What payment methods are accepted?</span>
                    <svg class="w-6 h-6 transform transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                    </svg>
                </button>
                <div class="faq-content hidden text-gray-600 mt-2">
                    <p>We accept all major credit cards, PayPal, and Apple Pay. More payment options will be available soon.</p>
                </div>
            </div>

            <div class="border-b border-gray-200">
                <button class="w-full text-left flex items-center justify-between py-4 text-gray-700 focus:outline-none" onclick="toggleFaq(event)">
                    <span class="text-lg font-medium">How long does shipping take?</span>
                    <svg class="w-6 h-6 transform transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                    </svg>
                </button>
                <div class="faq-content hidden text-gray-600 mt-2">
                    <p>Shipping typically takes 3-7 business days, depending on your location. Expedited shipping options are available at checkout.</p>
                </div>
            </div>

            <div class="border-b border-gray-200">
                <button class="w-full text-left flex items-center justify-between py-4 text-gray-700 focus:outline-none" onclick="toggleFaq(event)">
                    <span class="text-lg font-medium">Can I cancel my order?</span>
                    <svg class="w-6 h-6 transform transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                    </svg>
                </button>
                <div class="faq-content hidden text-gray-600 mt-2">
                    <p>Orders can be canceled within 24 hours of placement. After that, the order is processed for shipping, and cancellation is not possible.</p>
                </div>
            </div>

            <div class="border-b border-gray-200">
                <button class="w-full text-left flex items-center justify-between py-4 text-gray-700 focus:outline-none" onclick="toggleFaq(event)">
                    <span class="text-lg font-medium">Where can I find the size guide?</span>
                    <svg class="w-6 h-6 transform transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                    </svg>
                </button>
                <div class="faq-content hidden text-gray-600 mt-2">
                    <p>You can find the size guide on each product page under the 'Size Guide' tab. It provides detailed measurements for each size.</p>
                </div>
            </div>

            <div class="border-b border-gray-200">
                <button class="w-full text-left flex items-center justify-between py-4 text-gray-700 focus:outline-none" onclick="toggleFaq(event)">
                    <span class="text-lg font-medium">Do you offer gift cards?</span>
                    <svg class="w-6 h-6 transform transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                    </svg>
                </button>
                <div class="faq-content hidden text-gray-600 mt-2">
                    <p>Yes, we offer digital gift cards available in various denominations. You can purchase them in the 'Gift Cards' section on our website.</p>
                </div>
            </div>

            <div class="border-b border-gray-200">
                <button class="w-full text-left flex items-center justify-between py-4 text-gray-700 focus:outline-none" onclick="toggleFaq(event)">
                    <span class="text-lg font-medium">What are the benefits of becoming a VELIRO member?</span>
                    <svg class="w-6 h-6 transform transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                    </svg>
                </button>
                <div class="faq-content hidden text-gray-600 mt-2">
                    <p>VELIRO members enjoy exclusive benefits such as free delivery, early access to sales, special discounts, and much more.</p>
                </div>
            </div>

        </div>
    </section>

    <script>
        function toggleFaq(event) {
            const faqContent = event.currentTarget.nextElementSibling;
            faqContent.classList.toggle('hidden');
            const icon = event.currentTarget.querySelector('svg');
            icon.classList.toggle('rotate-180');
        }
    </script>

    <?php
    include('./components/Footer.php');
    ?>

    <script
        script src="Customer.js">
    </script>
</body>

</html>