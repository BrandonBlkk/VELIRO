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
        $alert_message = "Product has been successfully updated in your cart.";
        $alert_class = "bg-green-200 border-green-400 text-green-800";
        $loader = "Load";
    }
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
            $alert_message = "Product has been successfully deleted from your cart.";
            $alert_class = "bg-green-200 border-green-400 text-green-800";
            $loader = "Load";
        }
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

    <!-- Loader -->
    <?php if (!empty($loader)) : ?>
        <div id="loader" class="fixed inset-0 items-center justify-center bg-black/35 z-40 flex transition-all duration-300 ease-in-out">
            <div class="container"></div>
        </div>
    <?php endif; ?>

    <!-- Display products in a styled list -->
    <section class="max-w-[1370px] mx-auto px-4 py-4">
        <div>
            <p class="text-2xl">Cart <span>(<?php echo $cart_query_count ?>)</span></p>
        </div>

        <?php
        $cart_select = "SELECT * FROM carttb WHERE CustomerID = '$id'";
        $cart_query = mysqli_query($connect, $cart_select);
        $cart_query_count = mysqli_num_rows($cart_query);

        if ($cart_query_count > 0) {
            $total_cost = 0;
            $delivery_price = 16;
        ?>

            <div class="flex flex-col md:flex-row justify-between mt-5">
                <div class="md:w-2/3 overflow-y-scroll h-[500px]">
                    <?php
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

                            // Check the price has discount
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
                        }
                    ?>
                        <form action="<?php $_SERVER["PHP_SELF"] ?>" method="post" class="px-4 py-2 rounded-md flex flex-col md:flex-row justify-between cursor-pointer">
                            <input type="hidden" name="cart_id" value="<?php echo $cartId; ?>">
                            <div class="flex items-center">
                                <div class="w-32">
                                    <img class="w-full h-full object-cover select-none" src="<?php echo $productImg1; ?>" alt="Product Image">
                                </div>
                                <div class="ml-4">
                                    <h1 class="text-md sm:text-xl mb-1"><?php echo $productTitle; ?></h1>
                                    <p class="text-xs sm:text-sm text-gray-400">Color: <span class="text-black"><?php echo $productColor ?></span></p>
                                    <p class="text-xs sm:text-sm text-gray-400">Size: <span class="text-black"><?php echo $displaySize ?></span></p>
                                    <input type="number" name="new_quantity" value="<?php echo $productQuantity; ?>" min="1" class="mt-2 w-16 px-2 py-1 border border-gray-300 rounded-md text-gray-700 focus:outline-none focus:border-indigo-500">
                                </div>
                            </div>
                            <div class="text-right flex flex-row-reverse md:flex-col items-center">
                                <div class="px-4 py-2 w-[90px]">
                                    <button type="submit" name="updateBtn" class="text-blue-500 hover:text-blue-700 ml-2">
                                        <i class="ri-edit-circle-line text-lg"></i>
                                    </button>
                                    <button type="submit" name="deleteBtn" class="text-red-500 hover:text-red-700 ml-2">
                                        <i class="ri-delete-bin-6-line text-lg"></i>
                                    </button>
                                </div>

                                <?php
                                if ($productDiscountPrice == 0) {
                                ?>
                                    <div class="mb-2">
                                        <p class="font-bold text-sm">$<?php echo $productPrice ?></p>
                                    </div>
                                <?php
                                } else {
                                ?>
                                    <div class="mb-2">
                                        <span class="text-xs text-gray-500 line-through">$<?php echo $productPrice ?></span>
                                        <span class="font-bold text-sm text-red-500">$<?php echo $productDiscountPrice ?></span>
                                    </div>
                                <?php
                                }
                                ?>

                            </div>
                        </form>
                    <?php } ?>
                </div>

                <!-- Order Summary -->
                <div class="md:w-1/3 mt-10 md:mt-0 md:ml-4">
                    <div class="p-4">
                        <h2 class="text-xl font-semibold mb-4">Order</h2>
                        <div class="space-y-2">
                            <p class="flex justify-between"><span>Subtotal:</span> <span>$<?php echo number_format($total_cost, 2); ?></span></p>
                            <p class="flex justify-between"><span>Delivery:</span> <span><?php echo ($total_cost < 323.72) ? '$' . number_format($delivery_price, 2) : 'Free'; ?></span></p>
                            <p class="flex justify-between font-semibold border-t"><span>Total:</span> <span>$<?php echo ($total_cost < 323.72) ? number_format($total_cost + $delivery_price, 2) : number_format($total_cost, 2); ?></span></p>
                        </div>
                        <div class="mt-6 select-none">
                            <a href="Checkout.php" class="block w-full text-center bg-green-600 text-white py-2 hover:bg-green-700 transition duration-300">Proceed to Checkout</a>
                        </div>
                        <div class="mt-4 select-none">
                            <a href="Collections.php" class="block w-full border border-indigo-400 p-2 text-center text-indigo-400 select-none hover:text-indigo-500 transition-all duration-300">Continue Shopping</a>
                        </div>
                        <div class="flex gap-4 border p-4 mt-4 mb-4">
                            <i class="ri-truck-line text-2xl"></i>
                            <div>
                                <p>Free delivery on qualifying orders.</p>
                                <a href="Delivery.php" class="text-xs underline text-gray-500 hover:text-gray-400 transition-colors duration-200">View our Delivery & Returns Policy</a>
                            </div>
                        </div>
                    </div>
                    <div class="flex flex-col py-5">
                        <h1 class="text-xl font-semibold mb-1">Payment Methods:</h1>
                        <ul class="flex gap-2 select-none">
                            <li>
                                <img class="w-9" src="Images/fashion-designer-cc-visa-icon.svg" alt="Icon">
                            </li>
                            <li>
                                <img class="w-9" src="Images/fashion-designer-cc-mastercard-icon.svg" alt="Icon">
                            </li>
                            <li>
                                <img class="w-9" src="Images/fashion-designer-cc-discover-icon.svg" alt="Icon">
                            </li>
                            <li>
                                <img class="w-9" src="Images/fashion-designer-cc-apple-pay-icon.svg" alt="Icon">
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        <?php } else { ?>
            <div class="flex flex-col md:flex-row justify-between mt-5">
                <p class="text-2xl text-gray-400">Your cart is empty.</p>

                <!-- Order Summary -->
                <div class="md:w-1/3 mt-10 md:mt-0 md:ml-4">
                    <div class="p-4">
                        <h2 class="text-xl font-semibold mb-4">Order</h2>
                        <div class="space-y-2">
                            <p class="flex justify-between"><span>Subtotal:</span> <span>$0.00</span></p>
                            <p class="flex justify-between"><span>Delivery:</span> <span>$0.00</span></p>
                            <p class="flex justify-between font-semibold"><span>Total:</span> <span>$0.00</span></p>
                        </div>
                        <div class="mt-4 select-none">
                            <a href="Collections.php" class="block w-full border border-indigo-400 p-2 text-center text-indigo-400 select-none hover:text-indigo-500 transition-all duration-300">Continue Shopping</a>
                        </div>
                        <div class="flex gap-4 border p-4 mt-4 mb-4">
                            <i class="ri-truck-line text-2xl"></i>
                            <div>
                                <p>Free delivery on qualifying orders.</p>
                                <a href="Delivery.php" class="text-xs underline text-gray-500 hover:text-gray-400 transition-colors duration-200">View our Delivery & Returns Policy</a>
                            </div>
                        </div>
                    </div>
                    <div class="flex flex-col py-5">
                        <h1 class="text-xl font-semibold mb-1">Payment Methods:</h1>
                        <ul class="flex gap-2 select-none">
                            <li>
                                <img class="w-9" src="Images/fashion-designer-cc-visa-icon.svg" alt="Icon">
                            </li>
                            <li>
                                <img class="w-9" src="Images/fashion-designer-cc-mastercard-icon.svg" alt="Icon">
                            </li>
                            <li>
                                <img class="w-9" src="Images/fashion-designer-cc-discover-icon.svg" alt="Icon">
                            </li>
                            <li>
                                <img class="w-9" src="Images/fashion-designer-cc-apple-pay-icon.svg" alt="Icon">
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        <?php } ?>

    </section>

    <?php
    include('./components/Footer.php')
    ?>

    <script src="Customer.js"></script>
</body>

</html>