<?php
session_start();
include('DbConnection.php');
include('components/AutoIDFunction.php');

// Initialize variables
$orderID = AutoID('ordertb', 'OrderID', 'OR-', 6);
$fullName = $email = $phone = $address = $city = $state = $zip = $paymentMethod = '';
$errors = [];


// Check if cutomer singin or not
$id = (isset($_SESSION['CustomerID']) && !empty($_SESSION['CustomerID'])) ? $_SESSION['CustomerID'] : $id = null;

// Check if the form is submitted
if (isset($_POST['sent'])) {
    // Gather order details from POST data
    $customerID = $id;
    $fullName = $_POST['full_name'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $address = $_POST['address'];
    $city = $_POST['city'];
    $state = $_POST['state'];
    $zip = $_POST['zip'];
    $paymentMethod = isset($_POST['payment_method']) ? $_POST['payment_method'] : null;
    $remark = $_POST['remark'] ? $_POST['remark'] : 'No Remark';
    $orderdate = $_POST['orderdate'];
    $total_cost = $_POST['total_cost'];

    $totalAmount = $total_cost;
    $delivery_price = 16;

    // Validation
    if (empty($fullName)) {
        $errors['full_name'] = "Fullname is required.";
    } elseif (preg_match('/\d/', $fullName)) {
        $errors['full_name'] = "Fullname should not contain numbers.";
    }
    if (empty($email)) {
        $errors['email'] = "Email is required.";
    }
    if (empty($phone)) {
        $errors['phone'] = "Phone number is required.";
    } elseif (!preg_match('/^\d+$/', $phone)) {
        $errors['phone'] = "Phone number is invalid. Only digits are allowed.";
    } elseif (strlen($phone) < 9 || strlen($phone) > 11) {
        $errors['phone'] = "Phone number is invalid.";
    }
    if (empty($address)) {
        $errors['address'] = "Address is required.";
    }
    if (empty($city)) {
        $errors['city'] = "City is required.";
    }
    if (empty($state)) {
        $errors['state'] = "State is required.";
    }
    if (empty($zip)) {
        $errors['zip'] = "Zip code is required.";
    } elseif (!preg_match('/^\d+$/', $zip)) {
        $errors['zip'] = "Zip code is invalid. Only digits are allowed.";
    }
    if (empty($paymentMethod)) {
        $errors['paymentMethod'] = "Please select payment method before placing an order.";
    }

    if (empty($errors)) {
        // Query to fetch products from carttb
        $cart_select = "SELECT * FROM carttb WHERE CustomerID = '$customerID'";
        $cart_query = mysqli_query($connect, $cart_select);

        if (mysqli_num_rows($cart_query) > 0) {
            // Insert into ordertb
            $orderTax = $total_cost * 0.10;
            $totalAmount = $total_cost + $orderTax + $delivery_price;

            $insertOrderQuery = "INSERT INTO ordertb (OrderID, CustomerID, CustomerPhone, ShippingAddress, City, State, PaymentMethod, TotalPrice, OrderTax, TotalAmount, Remark, OrderDate, Status) VALUES ('$orderID', '$customerID', '$phone', '$address', '$city', '$state', '$paymentMethod', '$total_cost', '$orderTax', '$totalAmount', '$remark', '$orderdate', 'Pending')";

            if (mysqli_query($connect, $insertOrderQuery)) {
                // Insert into orderdetailtb
                mysqli_data_seek($cart_query, 0); // Reset the cart query pointer

                while ($array = mysqli_fetch_array($cart_query)) {
                    $productId = $array['ProductID'];
                    $productSize = $array['Size'];
                    $productQuantity = $array['Quantity'];

                    // Fetch product details from producttb
                    $product_select = "SELECT * FROM producttb WHERE ProductID = '$productId'";
                    $product_query = mysqli_query($connect, $product_select);

                    if ($product_row = mysqli_fetch_array($product_query)) {
                        $productPrice = $product_row['Price'];
                        $productDiscountPrice = $product_row['DiscountPrice'];

                        // Determine effective price
                        $effectivePrice = ($productDiscountPrice != 0) ? $productDiscountPrice : $productPrice;

                        // Calculate item total and add to total cost
                        $item_total = $effectivePrice * $productQuantity;
                        $total_cost += $item_total;

                        // Insert into orderdetailtb
                        $insertOrderDetailQuery = "INSERT INTO orderdetailtb (OrderID, ProductID, OrderUnitQuantity, PurchaseUnitPrice) VALUES ('$orderID', '$productId', '$productQuantity', '$effectivePrice')";
                        mysqli_query($connect, $insertOrderDetailQuery);
                    }
                }

                // Clear the cart for the customer after placing the order
                $clearCartQuery = "DELETE FROM carttb WHERE CustomerID = '$customerID'";
                mysqli_query($connect, $clearCartQuery);

                // Product successfully deleted from cart
                $_SESSION['alert_message'] = "Thank you for choosing us! Your order is being processed.";
                $_SESSION['alert_class'] = "bg-green-200 border-green-400 text-green-800";

                $fullName = '';
                $email = '';
                $phone = '';
                $address = '';
                $city = '';
                $state = '';
                $zip = '';
                $remark = '';

                $loader = "Load";
            } else {
                echo "<script>alert('Error placing order: " . mysqli_error($connect) . "');</script>";
                echo "<script>window.location = 'Checkout.php'</script>";
            }
        }
    }
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

    <!-- Loader -->
    <?php if (!empty($loader)) : ?>
        <div id="loader" class="fixed inset-0 items-center justify-center bg-black/35 z-40 flex transition-all duration-300 ease-in-out">
            <div class="container"></div>
        </div>
    <?php endif; ?>

    <section>
        <div class="flex flex-col md:flex-row items-center justify-center md:items-start md:space-x-8 p-3">

            <?php
            $cart_select = "SELECT * FROM carttb WHERE CustomerID = '$id'";
            $cart_query = mysqli_query($connect, $cart_select);
            $cart_query_count = mysqli_num_rows($cart_query);

            $products = []; // Initialize an array to store product details
            $total_cost = 0;
            $delivery_price = 16;

            if ($cart_query_count > 0) {
                while ($array = mysqli_fetch_array($cart_query)) {
                    $cartId = $array['CartID'];
                    $productId = $array['ProductID'];
                    $productSize = $array['Size'];
                    $productQuantity = $array['Quantity'];

                    // Fetch the product details from producttb and producttypetb
                    $product_select = " SELECT p.ProductTypeID, p.Title, p.img1,
                    p.Price, p.Color, p.Brand, p.DiscountPrice, pt.ProductTypeName 
                    FROM producttb p
                    JOIN producttypetb pt 
                    ON p.ProductTypeID = pt.ProductTypeID 
                    WHERE p.ProductID = '$productId'
                    ";
                    $product_query = mysqli_query($connect, $product_select);
                    if ($product_row = mysqli_fetch_array($product_query)) {
                        $productTypeId = $product_row['ProductTypeID'];
                        $productTitle = $product_row['Title'];
                        $productImg1 = $product_row['img1'];
                        $productPrice = $product_row['Price'];
                        $productColor = $product_row['Color'];
                        $productBrand = $product_row['Brand'];
                        $productDiscountPrice = $product_row['DiscountPrice'];
                        $productTypeName = $product_row['ProductTypeName'];

                        // Check if the price has a discount
                        $effectivePrice = ($productDiscountPrice != 0) ? $productDiscountPrice : $productPrice;

                        // Calculate item total
                        $item_total = $effectivePrice * $productQuantity;

                        // Add to total cost
                        $total_cost += $item_total;

                        $displaySize = $productSize;
                        if ($productTypeName != 'Shoes') {
                            switch ($productSize) {
                                case 1:
                                    $displaySize = 'XS';
                                    break;
                                case 2:
                                    $displaySize = 'S';
                                    break;
                                case 3:
                                    $displaySize = 'M';
                                    break;
                                case 4:
                                    $displaySize = 'L';
                                    break;
                                case 5:
                                    $displaySize = 'XL';
                                    break;
                                case 6:
                                    $displaySize = 'XXL';
                                    break;
                                case 7:
                                    $displaySize = '3XL';
                                    break;
                            }
                        }

                        // Store product details in the array
                        $products[] = [
                            'title' => $productTitle,
                            'img' => $productImg1,
                            'price' => $effectivePrice,
                            'quantity' => $productQuantity,
                            'color' => $productColor,
                            'brand' => $productBrand,
                            'size' => $displaySize,
                            'product_type_name' => $productTypeName
                        ];
                    }
                }
            }
            ?>

            <!-- Product Summary -->
            <div class="static md:sticky md:top-20 bg-white p-8 shadow-lg w-full md:max-w-[700px] mb-10 md:mb-0">
                <h2 class="text-xl font-semibold mb-6 text-gray-800">Order Summary</h2>
                <div class="p-4 h-[345px] overflow-auto">
                    <?php if (!empty($products)) : ?>
                        <?php foreach ($products as $product) : ?>
                            <div class="flex items-center mb-2 cursor-pointer">
                                <img class="w-24 object-cover select-none" src="<?php echo $product['img']; ?>" alt="Product Image">
                                <div class="ml-4 flex justify-between w-full">
                                    <div>
                                        <h3 class="text-lg font-semibold text-gray-900"><?php echo $product['title']; ?></h3>
                                        <p class="text-sm text-gray-400">Brand: <span class="text-black"><?php echo $product['brand']; ?></span></p>
                                        <p class="text-sm text-gray-400">Color: <span class="text-black"><?php echo $product['color']; ?></span></p>
                                        <p class="text-sm text-gray-400">Size: <span class="text-black"><?php echo $product['size']; ?></span></p>
                                        <p class="text-sm text-gray-400">Quantity: <span class="text-black"><?php echo $product['quantity']; ?></span></p>
                                    </div>
                                    <div class="flex items-center">
                                        <p class="font-semibold">$<?php echo number_format($product['price'], 2); ?></p>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php else : ?>
                        <p class="text-gray-600">Your cart is empty.</p>
                    <?php endif; ?>
                </div>

                <!-- Order Summary Details -->
                <div class="mt-3">
                    <div class="flex justify-between mb-4">
                        <span class="text-gray-600">Subtotal</span>
                        <span class="text-gray-800">$<?php echo number_format($total_cost, 2); ?></span>
                    </div>
                    <div class="flex justify-between mb-4">
                        <span class="text-gray-600">Delivery</span>
                        <span class="text-gray-800">
                            <?php echo ($cart_query_count === 0) ? '$0' : ($total_cost < 323.72 ? '$' . number_format($delivery_price, 2) : 'Free'); ?>
                        </span>
                    </div>
                    <hr class="my-4 border-t border-gray-300">
                    <div class="flex justify-between">
                        <span class="text-lg font-semibold">Total to Pay</span>
                        <span class="text-lg font-semibold text-indigo-500">
                            <?php
                            $totalToPay = ($cart_query_count > 0 && $total_cost < 323.72) ? $total_cost + $delivery_price : $total_cost;
                            echo '$' . number_format($totalToPay, 2);
                            ?>
                        </span>
                    </div>
                </div>
            </div>


            <!-- Checkout Form -->
            <div class="bg-white p-8 rounded-lg shadow-lg w-full md:max-w-lg">
                <h2 class="text-xl font-semibold mb-6 text-gray-800">Checkout</h2>

                <?php
                $check_account = "SELECT * FROM customertb
                WHERE CustomerID = '$id'";

                $check_account_query = mysqli_query($connect, $check_account);
                $rowCount = mysqli_num_rows($check_account_query);

                // Check customer account match with signup account
                if ($rowCount > 0) {
                    $array = mysqli_fetch_array($check_account_query);
                    $customer_fullname = $array["FullName"];
                    $customer_email = $array["CustomerEmail"];
                    $customer_phone = $array["CustomerPhone"];
                }
                ?>

                <form action="<?php $_SERVER["PHP_SELF"] ?>" method="POST" class="space-y-4">
                    <input type="hidden" name="product_id" value="<?php echo $product_id; ?>">
                    <input type="hidden" name="total_cost" value="<?php echo $total_cost; ?>">

                    <!-- Full Name -->
                    <div>
                        <label for="full_name" class="block text-sm font-medium text-gray-700">Full Name</label>
                        <input class="mt-1 block w-full p-3 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm transition duration-300 ease-in-out <?php echo isset($errors['full_name']) ? 'border-red-500' : 'border-gray-300'; ?>" type="text" name="full_name" placeholder="Enter your full name" value="<?php echo htmlspecialchars($customer_fullname); ?>">
                        <?php if (isset($errors['full_name'])) : ?>
                            <p class="text-red-600 text-sm"><?php echo $errors['full_name']; ?></p>
                        <?php else : ?>
                            <?php if ($fullName) : ?>
                                <p class="text-green-600 text-sm"><?php echo "Fullname is valid." ?></p>
                            <?php endif; ?>
                        <?php endif; ?>
                    </div>

                    <!-- Email Address -->
                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700">Email Address</label>
                        <input class="mt-1 block w-full p-3 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm transition duration-300 ease-in-out <?php echo isset($errors['email']) ? 'border-red-500' : 'border-gray-300'; ?>" type="text" name="email" placeholder="Enter your email" value="<?php echo htmlspecialchars($customer_email); ?>" readonly>
                        <?php if (isset($errors['email'])) : ?>
                            <p class="text-red-600 text-sm"><?php echo $errors['email']; ?></p>
                        <?php else : ?>
                            <?php if ($email) : ?>
                                <p class="text-green-600 text-sm"><?php echo "Email is valid." ?></p>
                            <?php endif; ?>
                        <?php endif; ?>
                    </div>

                    <!-- Mobile Phone -->
                    <div>
                        <label for="phone" class="block text-sm font-medium text-gray-700">Phone Number</label>
                        <input class="mt-1 block w-full p-3 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm transition duration-300 ease-in-out <?php echo isset($errors['phone']) ? 'border-red-500' : 'border-gray-300'; ?>" type="text" name="phone" placeholder="Enter your phone" value="<?php echo htmlspecialchars($customer_phone); ?>">
                        <?php if (isset($errors['phone'])) : ?>
                            <p class="text-red-600 text-sm"><?php echo $errors['phone']; ?></p>
                        <?php else : ?>
                            <?php if ($phone) : ?>
                                <p class="text-green-600 text-sm"><?php echo "Number is valid."; ?></p>
                            <?php endif; ?>
                        <?php endif; ?>
                    </div>

                    <!-- Shipping Address -->
                    <div>
                        <label for="address" class="block text-sm font-medium text-gray-700">Shipping Address</label>
                        <input class="mt-1 block w-full p-3 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm transition duration-300 ease-in-out <?php echo isset($errors['address']) ? 'border-red-500' : 'border-gray-300'; ?>" type="text" name="address" placeholder="Enter your address" value="<?php echo htmlspecialchars($address); ?>">
                        <?php if (isset($errors['address'])) : ?>
                            <p class=" text-red-600 text-sm"><?php echo $errors['address']; ?></p>
                        <?php else : ?>
                            <?php if ($address) : ?>
                                <p class="text-green-600 text-sm"><?php echo "Address is valid." ?></p>
                            <?php endif; ?>
                        <?php endif; ?>
                    </div>

                    <!-- City and State -->
                    <div class="flex space-x-4">
                        <div class="w-1/2">
                            <label for="city" class="block text-sm font-medium text-gray-700">City</label>
                            <input class="mt-1 block w-full p-3 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm transition duration-300 ease-in-out <?php echo isset($errors['city']) ? 'border-red-500' : 'border-gray-300'; ?>" type="text" name="city" placeholder="Enter your city" value="<?php echo htmlspecialchars($city); ?>">
                            <?php if (isset($errors['city'])) : ?>
                                <p class=" text-red-600 text-sm"><?php echo $errors['city']; ?></p>
                            <?php else : ?>
                                <?php if ($city) : ?>
                                    <p class="text-green-600 text-sm"><?php echo "City is valid." ?></p>
                                <?php endif; ?>
                            <?php endif; ?>
                        </div>

                        <div class="w-1/2">
                            <label for="state" class="block text-sm font-medium text-gray-700">State</label>
                            <input class="mt-1 block w-full p-3 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm transition duration-300 ease-in-out <?php echo isset($errors['state']) ? 'border-red-500' : 'border-gray-300'; ?>" type="text" name="state" placeholder="Enter your state" value="<?php echo htmlspecialchars($state); ?>">
                            <?php if (isset($errors['state'])) : ?>
                                <p class=" text-red-600 text-sm"><?php echo $errors['state']; ?></p>
                            <?php else : ?>
                                <?php if ($state) : ?>
                                    <p class="text-green-600 text-sm"><?php echo "State is valid." ?></p>
                                <?php endif; ?>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- ZIP Code -->
                    <div>
                        <label for="zip" class="block text-sm font-medium text-gray-700">ZIP Code</label>
                        <input class="mt-1 block w-full p-3 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm transition duration-300 ease-in-out <?php echo isset($errors['zip']) ? 'border-red-500' : 'border-gray-300'; ?>" type="text" name="zip" placeholder="Enter your zip" value="<?php echo htmlspecialchars($zip); ?>">
                        <?php if (isset($errors['zip'])) : ?>
                            <p class=" text-red-600 text-sm"><?php echo $errors['zip']; ?></p>
                        <?php else : ?>
                            <?php if ($zip) : ?>
                                <p class="text-green-600 text-sm"><?php echo "Zip code is valid." ?></p>
                            <?php endif; ?>
                        <?php endif; ?>
                    </div>

                    <div>
                        <label for="payment_method" class="block text-sm font-medium text-gray-700">Payment Method</label>
                        <select name="payment_method" id="payment_method" class="mt-1 block w-full p-3 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm <?php echo isset($errors['paymentMethod']) ? 'border-red-500' : 'border-gray-300'; ?>" onchange="togglePaymentInfo(this.value)">
                            <option value="" disabled selected>Select a payment method</option>
                            <option value="cash">Cash</option>
                            <option value="visa">Visa</option>
                            <option value="mastercard">Master Card</option>
                            <option value="discover">Discover</option>
                            <option value="applepay">Apple Pay</option>
                        </select>
                        <?php if (isset($errors['paymentMethod'])) : ?>
                            <p class=" text-red-600 text-sm"><?php echo $errors['paymentMethod']; ?></p>
                        <?php endif; ?>
                    </div>

                    <!-- Payment Information Fields -->
                    <div id="payment-info" class="mt-4 hidden">
                        <!-- Card Information for Visa/MasterCard/Discover -->
                        <div id="credit-card-info" class="hidden">
                            <label for="card_number" class="block text-sm font-medium text-gray-700">Card Number</label>
                            <input type="text" id="card_number" name="card_number" class="mt-1 block w-full p-3 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" placeholder="1234 5678 9012 3456">

                            <label for="card_expiry" class="block text-sm font-medium text-gray-700 mt-4">Expiry Date</label>
                            <input type="text" id="card_expiry" name="card_expiry" class="mt-1 block w-full p-3 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" placeholder="MM/YY">

                            <label for="card_cvc" class="block text-sm font-medium text-gray-700 mt-4">CVC</label>
                            <input type="text" id="card_cvc" name="card_cvc" class="mt-1 block w-full p-3 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" placeholder="CVC">
                        </div>
                    </div>

                    <script>
                        function togglePaymentInfo(paymentMethod) {
                            // Hide all sections initially
                            document.getElementById('payment-info').classList.add('hidden');
                            document.getElementById('credit-card-info').classList.add('hidden');

                            // Show credit card fields for Visa, MasterCard, or Discover
                            if (paymentMethod === 'visa' || paymentMethod === 'mastercard' || paymentMethod === 'discover') {
                                document.getElementById('payment-info').classList.remove('hidden');
                                document.getElementById('credit-card-info').classList.remove('hidden');
                            }
                            // Hide payment fields for Cash and Apple Pay
                            else {
                                document.getElementById('payment-info').classList.add('hidden');
                            }
                        }
                    </script>

                    <div>
                        <label for="remark" class="block text-sm font-medium text-gray-700">Remark (Optional)</label>
                        <input class="mt-1 block w-full p-3 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm transition duration-300 ease-in-out" type="text" name="remark" placeholder="Enter your remark">
                    </div>

                    <div>
                        <input type="date" class="hidden" id="orderdate" name="orderdate" value="<?php echo date("Y-m-d") ?>" required>
                    </div>

                    <div class="flex items-center gap-2">
                        <h1 class="font-bold text-sm text-gray-600">WE ACCEPT:</h1>
                        <ul class="flex gap-2 select-none">
                            <li>
                                <img src="Images/fashion-designer-cc-visa-icon.svg" alt="Icon">
                            </li>
                            <li>
                                <img src="Images/fashion-designer-cc-mastercard-icon.svg" alt="Icon">
                            </li>
                            <li>
                                <img src="Images/fashion-designer-cc-discover-icon.svg" alt="Icon">
                            </li>
                            <li>
                                <img src="Images/fashion-designer-cc-apple-pay-icon.svg" alt="Icon">
                            </li>
                        </ul>
                    </div>

                    <!-- Complete Purchase Button -->
                    <?php if ($cart_query_count > 0) { ?>
                        <button type="submit" name="sent" class="w-full bg-gradient-to-r from-indigo-500 to-purple-500 text-white py-3 rounded-md font-semibold hover:from-indigo-600 hover:to-purple-600 transition-colors duration-200">
                            Place Order
                        </button>
                    <?php } else { ?>
                        <a href="Collections.php" class="w-full bg-gradient-to-r from-green-500 to-teal-500 text-white py-3 rounded-md font-semibold hover:from-green-600 hover:to-teal-600 transition-all duration-200 text-center block">
                            Start Shopping
                        </a>
                    <?php } ?>
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