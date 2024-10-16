<?php
session_start();
include('DbConnection.php');

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

// Remove specified product from favorite
if (isset($_POST['removeBtn'])) {
    $product_id = $_POST['product_Id'];

    $delete_query = "DELETE FROM favoritetb WHERE ProductID = '$product_id'";
    mysqli_query($connect, $delete_query);

    // Redirect to the same page to avoid form resubmission
    header("Location: " . $_SERVER['PHP_SELF']);
    exit;
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

    <!-- Display products in a styled list -->
    <section class="max-w-[1370px] mx-auto px-4 py-4">
        <?php

        // Fetch the favorite products for the customer
        $favorite_select = "SELECT * FROM favoritetb WHERE CustomerID = '$id'";
        $favorite_query = mysqli_query($connect, $favorite_select);
        $favorite_query_count = mysqli_num_rows($favorite_query);

        ?>
        <div>
            <p class="text-2xl mb-4">Favorite <span>(<?php echo $favorite_query_count ?>)</span></p>
        </div>
        <?php
        if ($favorite_query_count > 0) {
        ?>
            <div class="grid gap-4 grid-cols-2 md:grid-cols-3 lg:grid-cols-4">
                <?php
                // Loop through each favorite entry
                while ($favorite = mysqli_fetch_array($favorite_query)) {
                    $productId = $favorite['ProductID'];

                    // Fetch product details from producttb using the ProductID
                    $product_select = "SELECT * FROM producttb WHERE ProductID = '$productId'";
                    $product_query = mysqli_query($connect, $product_select);

                    if ($product_query && mysqli_num_rows($product_query) > 0) {
                        $product = mysqli_fetch_array($product_query);

                        // Extract product details
                        $product_id = $product['ProductID'];
                        $title = $product['Title'];
                        $img1 = $product['img1'];
                        $img2 = $product['img2'];
                        $price = $product['Price'];
                        $discount_price = $product['DiscountPrice'];
                        $extended_sizing = $product['ExtendedSizing'];
                        $more_colors = $product['MoreColors'];
                        $selling_fast = $product['SellingFast'];
                ?>

                        <form action="<?php $_SERVER["PHP_SELF"] ?>" method="post" class="relative group">
                            <input type="hidden" name="product_Id" value="<?php echo $product_id; ?>">

                            <a class="group" href="ProductsDetails.php?product_ID=<?php echo $product_id ?>">
                                <div class="relative w-full mb-4 overflow-hidden select-none">
                                    <img class="w-full h-full object-cover transition-opacity duration-300 opacity-100 group-hover:opacity-0" src="<?php echo $img1 ?>" alt="Image 1">
                                    <img class="w-full h-full object-cover absolute top-0 left-0 transition-opacity duration-300 opacity-0 group-hover:opacity-100" src="<?php echo $img2 ?>" alt="Image 2">
                                    <p class="absolute bottom-0 bg-gradient-to-t from-indigo-300 text-white font-bold w-full p-2 <?php echo ($extended_sizing === 'true') ? 'flex' : 'hidden' ?>">EXTENDED SIZING</p>
                                </div>
                                <div>
                                    <h1 class="text-sm mb-2 truncate md:whitespace-normal"><?php echo $title ?></h1>

                                    <?php
                                    if ($discount_price == 'none') {
                                    ?>
                                        <div class="mb-2">
                                            <p class="font-bold text-sm">$<?php echo $price ?></p>
                                        </div>
                                    <?php
                                    } else {
                                    ?>
                                        <div class="mb-2">
                                            <span class="text-xs text-gray-500 line-through">$<?php echo $price ?></span>
                                            <span class="font-bold text-sm text-red-500">$<?php echo $discount_price ?></span>
                                        </div>
                                    <?php
                                    }
                                    ?>

                                    <span class="text-xs text-gray-500 font-bold border border-indigo-400 p-1 mb-1 select-none <?php echo ($more_colors === 'false') ? 'hidden' : 'inline-block' ?>">MORE COLOURS</span>
                                    <span class="text-xs text-white bg-indigo-500 font-bold border p-1 select-none <?php echo ($selling_fast === 'false') ? 'hidden' : 'inline-block' ?>">SELLING FAST</span>
                                </div>
                            </a>
                            <button type="submit" name="removeBtn">
                                <i class="ri-heart-3-fill text-indigo-400 text-xl sm:text-2xl absolute left-1 top-1 flex justify-center items-center bg-black/5 w-8 sm:w-9 h-8 sm:h-9 rounded-full cursor-pointer" onclick="toggleFavorite(this)"></i>
                            </button>
                        </form>

                <?php
                    }
                }
                ?>
            </div>
        <?php } else { ?>

            <!-- No Favorite Products Message -->
            <div class="mt-10 py-36 flex justify-center text-center text-gray-400 text-base sm:text-lg">
                <p>You have no favorite products.</p>
            </div>
        <?php } ?>

    </section>

    <?php
    include('./components/Footer.php')
    ?>

    <script src="Customer.js"></script>
</body>

</html>