<?php
session_start();
include('DbConnection.php');

// Initialize variables
$email = $message = '';
$errors = [];

// Check if the form is submitted
if (isset($_POST['send'])) {
    $email = mysqli_real_escape_string($connect, $_POST['email']);
    $message = mysqli_real_escape_string($connect, $_POST['message']);
    $date = $_POST['contactdate'];

    // Validation
    if (empty($email)) {
        $errors['email'] = "Email is required.";
    }

    if (empty($message)) {
        $errors['message'] = "Message is required.";
    }

    if (empty($errors)) {
        $select = "INSERT INTO cuscontacttb(CustomerEmail,	ContactMessage, ContactDate, Status)
        VALUE('$email', '$message', '$date', 'Contacted')";

        $select_Query = mysqli_query($connect, $select);

        if ($select_Query) {
            $_SESSION['alert_message'] = "You have successfully sent the message.";
            $_SESSION['alert_class'] = "bg-green-200 border-green-400 text-green-800";
        }
        // Redirect to the same page to avoid form resubmission
        header("Location: " . $_SERVER['PHP_SELF']);
        exit;
    }
}

// Check if cutomer singin or not
$id = (isset($_SESSION['CustomerID']) && !empty($_SESSION['CustomerID'])) ? $_SESSION['CustomerID'] : $id = null;

// Update specified product from cart
if (isset($_POST['updateBtn'])) {
    $cart_id = $_POST['cart_id'];
    $new_quantity = $_POST['new_quantity'];

    // Update product quantity in carttb table
    $update_query = "UPDATE carttb SET Quantity = '$new_quantity' WHERE CartID = '$cart_id'";
    $update_result = mysqli_query($connect, $update_query);

    if ($update_result) {
        // Product successfully updated in the cart
        $_SESSION['alert_message'] = "Product has been successfully updated in your cart.";
        $_SESSION['alert_class'] = "bg-green-200 border-green-400 text-green-800";
    }

    // Redirect to the same page to avoid form resubmission
    header("Location: " . $_SERVER['PHP_SELF']);
    exit;
}

// Delete specified product from cart
if (isset($_POST['deleteBtn'])) {
    $cart_id = $_POST['cart_id'];

    // DELETE product based on clicked product
    $cart_delete_query = "DELETE FROM carttb WHERE CartID = '$cart_id'";
    $cart_delete_result = mysqli_query($connect, $cart_delete_query);

    if ($cart_delete_result) {
        // Product successfully deleted from the cart
        $_SESSION['alert_message'] = "Product has been successfully deleted from your cart.";
        $_SESSION['alert_class'] = "bg-green-200 border-green-400 text-green-800";
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
    <link rel="stylesheet" href="output.css">
    <link rel="stylesheet" href="style.css">
</head>

<body>

    <?php
    include('./components/Header.php');
    ?>

    <section class="max-w-[1400px] mx-auto p-6">
        <div>
            <img class="w-full h-full object-cover" src="Images/hero-desktop-data.jpg" alt="Image">
        </div>
        <div class="flex flex-col md:flex-row gap-6 mt-6">
            <div class="flex-1">
                <div class="bg-white shadow p-6">
                    <h1 class="text-xl sm:text-2xl font-semibold mb-4">Frequently Asked Questions</h1>
                    <div class="space-y-4">
                        <div>
                            <h2 class="font-semibold text-lg">What types of clothing do you offer?</h2>
                            <p class="text-gray-600">VELIRO specializes in men's fashion, offering a range of stylish clothing including shirts, trousers, jackets, and accessories tailored for modern men.</p>
                        </div>
                        <div>
                            <h2 class="font-semibold text-lg">Do you offer free delivery on all orders?</h2>
                            <p class="text-gray-600">Yes, members of VELIRO enjoy complimentary delivery on all in-store purchases. Simply sign up to take advantage of this benefit.</p>
                        </div>
                        <div>
                            <h2 class="font-semibold text-lg">How do I become a VELIRO member?</h2>
                            <p class="text-gray-600">You can become a member by signing up on our website or in-store. Membership is free and includes perks such as complimentary delivery.</p>
                        </div>
                        <div>
                            <h2 class="font-semibold text-lg">Can I return or exchange products?</h2>
                            <p class="text-gray-600">Yes, we offer a 60-day return and exchange policy for all purchases. Items must be in their original condition with tags attached.</p>
                        </div>
                        <div>
                            <h2 class="font-semibold text-lg">Do you offer international shipping?</h2>
                            <p class="text-gray-600">At the moment, we only offer shipping within the country. Stay tuned as we work on expanding our services internationally.</p>
                        </div>
                        <div>
                            <h2 class="font-semibold text-lg">How can I track my order?</h2>
                            <p class="text-gray-600">After placing an order, you'll receive a tracking number via email, allowing you to monitor the status of your delivery.</p>
                        </div>
                        <div>
                            <h2 class="font-semibold text-lg">What sizes do you carry?</h2>
                            <p class="text-gray-600">We carry a wide range of sizes from S to 3XL. Please refer to our size guide for exact measurements to ensure the perfect fit.</p>
                        </div>
                        <div>
                            <h2 class="font-semibold text-lg">How can I contact customer service?</h2>
                            <p class="text-gray-600">You can reach out to our customer service team via the Contact Us page, email, or phone. We’re here to help with any queries you may have.</p>
                        </div>
                        <div>
                            <h2 class="font-semibold text-lg">What payment methods do you accept?</h2>
                            <p class="text-gray-600">We accept all major credit and Visa, as well as popular payment methods like Apple Pay.</p>
                        </div>
                        <div>
                            <h2 class="font-semibold text-lg">How can I find the latest collections?</h2>
                            <p class="text-gray-600">Check our website regularly or subscribe to our newsletter for updates on new arrivals and exclusive collections.</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="sticky top-20 flex-1 md:max-w-xl md:mx-auto h-full bg-white shadow-lg p-6">
                <h1 class="text-2xl font-semibold mb-4">Contact Us</h1>
                <form action="<?php $_SERVER["PHP_SELF"] ?>" method="POST">
                    <div class="mb-4">
                        <label class="block text-gray-700 font-semibold mb-2" for="email">Email</label>
                        <input class="w-full p-2 border rounded focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-opacity-50 transition duration-300 ease-in-out <?php echo isset($errors['email']) ? 'border-red-500' : 'border-gray-300'; ?>" type="email" name="email" placeholder="Enter your email" value="<?php echo htmlspecialchars($email); ?>">
                        <?php if (isset($errors['email'])) : ?>
                            <p class="text-red-600 text-sm"><?php echo $errors['email']; ?></p>
                        <?php else : ?>
                            <?php if ($email) : ?>
                                <p class="text-green-600 text-sm"><?php echo "Email is valid." ?></p>
                            <?php endif; ?>
                        <?php endif; ?>
                    </div>
                    <div class="mb-4">
                        <label class="block text-gray-700 font-semibold mb-2" for="message">Message</label>
                        <textarea class="w-full p-2 border rounded focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-opacity-50 transition duration-300 ease-in-out <?php echo isset($errors['message']) ? 'border-red-500' : 'border-gray-300'; ?>" name="message" id="message" rows="6" placeholder="Enter your thought..." value="<?php echo htmlspecialchars($message); ?>"></textarea>
                        <?php if (isset($errors['message'])) : ?>
                            <p class="text-red-600 text-sm"><?php echo $errors['message']; ?></p>
                        <?php else : ?>
                            <?php if ($message) : ?>
                                <p class="text-green-600 text-sm"><?php echo "Message is valid." ?></p>
                            <?php endif; ?>
                        <?php endif; ?>
                    </div>
                    <!-- Date -->
                    <div>
                        <input type="date" class="hidden" id="contactdate" name="contactdate" value="<?php echo date("Y-m-d") ?>" required>
                    </div>
                    <button class="bg-indigo-500 text-white py-2 w-full rounded hover:bg-indigo-600 transition duration-200" name="send" type="submit">Send</button>
                </form>
            </div>
        </div>
    </section>

    <?php
    include('./components/Footer.php');
    ?>

    <script src="Customer.js"></script>
</body>

</html>