<?php
session_start();
include('DbConnection.php');

// Check if cutomer singin or not
$id = (isset($_SESSION['CustomerID']) && !empty($_SESSION['CustomerID'])) ? $_SESSION['CustomerID'] : $id = null;

if (isset($_GET["product_ID"])) {
    $product_id = $_GET["product_ID"];

    $query = "SELECT * FROM producttb p, producttypetb pt
        WHERE p.ProductTypeID = pt.ProductTypeID
        AND p.ProductID = '$product_id'";

    $result = mysqli_query($connect, $query);
    $array = mysqli_fetch_array($result);

    $product_id = $array['ProductID'];
    $product_type_id = $array['ProductTypeID'];
    $title = $array['Title'];
    $img1 = $array['img1'];
    $img2 = $array['img2'];
    $img3 = $array['img3'];
    $price = $array['Price'];
    $discount_price = $array['DiscountPrice'];
    $color = $array['Color'];
    $product_detail = $array['ProductDetail'];
    $brand = $array['Brand'];
    $model_height = $array['ModelHeight'];
    $product_size = $array['ProductSize'];
    $look_after_me = $array['LookAfterMe'];
    $about_me = $array['AboutMe'];
    $extended_sizing = $array['ExtendedSizing'];
    $more_colors = $array['MoreColors'];
    $selling_fast = $array['SellingFast'];
    $added_date = $array['AddedDate'];
    $stock = $array['Stock'];

    $alert_message = "";
}

// Add to Cart
if (isset($_POST['addtobag'])) {
    if (isset($id)) {
        $product_id = $_POST['product_Id'];
        $product_size = isset($_POST['size']) ? $_POST['size'] : Null;
        $product_quantity = 1;

        if (empty($product_size)) {
            // Size not selected
            $alert_message1 = "Please select a size before adding to cart";
            $alert_class = "bg-red-200 border-red-400 text-red-800";
        } else {
            $cart_select = "SELECT * FROM carttb WHERE ProductID = '$product_id' AND CustomerID = '$id' AND Size = '$product_size'";
            $cart_query = mysqli_query($connect, $cart_select);
            $cart_query_count = mysqli_num_rows($cart_query);

            if ($cart_query_count > 0) {
                // Product with the same size already exists in cart
                $cart_row = mysqli_fetch_assoc($cart_query);
                $current_quantity = $cart_row['Quantity'];

                $stock_select = "SELECT Stock FROM producttb WHERE ProductID = '$product_id'";
                $stock_query = mysqli_query($connect, $stock_select);
                if ($stock_row = mysqli_fetch_array($stock_query)) {
                    $current_stock = $stock_row['Stock'];

                    if ($current_stock > 0) {
                        // Update the quantity of the existing product in the cart
                        $new_quantity = $current_quantity + 1;
                        $cart_update = "UPDATE carttb SET Quantity = '$new_quantity' WHERE ProductID = '$product_id' AND CustomerID = '$id' AND Size = '$product_size'";
                        $cart_update_query = mysqli_query($connect, $cart_update);

                        if ($cart_update_query) {
                            // Update the stock of the product
                            $new_stock = $current_stock - 1;
                            $stock_update = "UPDATE producttb SET Stock = '$new_stock' WHERE ProductID = '$product_id'";
                            $stock_update_query = mysqli_query($connect, $stock_update);

                            if ($stock_update_query) {
                                // Product quantity successfully updated in cart
                                $alert_message = "Product quantity has been successfully updated in your cart.";
                                $alert_class = "bg-green-200 border-green-400 text-green-800";
                                $loader = "Load";
                            } else {
                                // Error updating the stock
                                $alert_message = "Failed to update the product stock. Please try again.";
                                $alert_class = "bg-red-200 border-red-400 text-red-800";
                                $loader = "Load";
                            }
                        } else {
                            // Error updating product quantity in cart
                            $alert_message = "Failed to update product quantity in your cart. Please try again.";
                            $alert_class = "bg-red-200 border-red-400 text-red-800";
                            $loader = "Load";
                        }
                    }
                }
            } else {
                // Fetch the current stock of the product
                $stock_select = "SELECT Stock FROM producttb WHERE ProductID = '$product_id'";
                $stock_query = mysqli_query($connect, $stock_select);
                if ($stock_row = mysqli_fetch_array($stock_query)) {
                    $current_stock = $stock_row['Stock'];

                    if ($current_stock > 0) {
                        // Insert product into cart
                        $cart_insert = "INSERT INTO carttb(CustomerID, ProductID, Size, Quantity)
                                        VALUES('$id', '$product_id', '$product_size', '$product_quantity')";
                        $cart_insert_query = mysqli_query($connect, $cart_insert);

                        if ($cart_insert_query) {
                            // Update the stock of the product
                            $new_stock = $current_stock - 1;
                            $stock_update = "UPDATE producttb SET Stock = '$new_stock' WHERE ProductID = '$product_id'";
                            $stock_update_query = mysqli_query($connect, $stock_update);

                            if ($stock_update_query) {
                                // Product successfully added to cart
                                $alert_message = "Product has been successfully added to your cart.";
                                $alert_class = "bg-green-200 border-green-400 text-green-800";
                                $loader = "Load";
                            } else {
                                // Error updating the stock
                                $alert_message = "Failed to update the product stock. Please try again.";
                                $alert_class = "bg-red-200 border-red-400 text-red-800";
                                $loader = "Load";
                            }
                        } else {
                            // Error adding product to cart
                            $alert_message = "Failed to add product to your cart. Please try again.";
                            $alert_class = "bg-red-200 border-red-400 text-red-800";
                            $loader = "Load";
                        }
                    }
                }
            }
        }
    } else {
        // If user not logged in
        $requestLogin = "Request Login";
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

// Check if CustomerID is set in session
if (isset($id) && !empty($_SESSION['CustomerID'])) {

    // Process form submission if favoriteBtn is set
    if (isset($_POST['favoriteBtn'])) {
        $loader = "Load";
        $product_id = htmlspecialchars($_POST['product_Id']);

        $check_query = "SELECT COUNT(*) as count FROM favoritetb WHERE CustomerID = '$id' AND ProductID = '$product_id'";
        $result = mysqli_query($connect, $check_query);
        $row = mysqli_fetch_assoc($result);
        $count = $row['count'];

        // If the product is not already in favorites, insert it
        if ($count == 0) {
            $insert_query = "INSERT INTO favoritetb (CustomerID, ProductID) VALUES ('$id', '$product_id')";
            mysqli_query($connect, $insert_query);
        }
    }
} else {
    if (isset($_POST['favoriteBtn'])) {

        // If user not logged in
        $requestLogin = "Request Login";
    }
}

// Remove specified product from favorite
if (isset($_POST['removeBtn'])) {
    $product_id = htmlspecialchars($_POST['product_Id']);

    $delete_query = "DELETE FROM favoritetb WHERE ProductID = '$product_id'";
    mysqli_query($connect, $delete_query);
}

// Submit review
if (isset($_POST['reviewsubmit'])) {
    if (isset($id)) {
        $product_id = $_POST['product_Id'];
        $rating = $_POST['rating'];
        $comment = mysqli_real_escape_string($connect, $_POST['comment']);
        $reviewdate = $_POST['reviewdate'];

        // Check if a review already exists for this product by this customer
        $check_review_query = "SELECT * FROM reviewtb WHERE CustomerID = '$id' AND ProductID = '$product_id'";
        $check_review_result = mysqli_query($connect, $check_review_query);

        if (mysqli_num_rows($check_review_result) == 0) {
            // If no review exists, proceed with inserting the new review
            $review_insert = "INSERT INTO reviewtb(CustomerID, ProductID, Rating, Comment, ReviewDate)
                                                VALUES('$id', '$product_id', '$rating', '$comment', '$reviewdate')";
            $review_insert_query = mysqli_query($connect, $review_insert);

            $alert_message = "Your review has been successfully submitted.";
            $alert_class = "bg-green-200 border-green-400 text-green-800";
            $loader = "Load";
        } else {
            // If a review already exists, show an alert message
            $alert_message = "You have already reviewed this product.";
            $alert_class = "bg-yellow-200 border-yellow-400 text-yellow-800";
            $loader = "Load";
        }
    } else {
        // If user is not logged in
        $requestLogin = "Request Login";
    }
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

    <!-- Request to Login -->
    <?php if (!empty($requestLogin)) : ?>
        <div id="requestLogin" class="fixed inset-0 flex items-center justify-center bg-black/45 z-40 transition-all duration-300 ease-in-out opacity-100">
            <div class="bg-white rounded flex flex-col justify-center p-6 py-10 w-full h-full sm:h-[350px] mx-0 sm:mx-3 max-w-full sm:max-w-[450px] shadow-lg relative">
                <button id="closeRequestLogin" class="absolute top-3 right-3 focus:outline-none">
                    <i class="ri-close-line text-2xl text-gray-600 hover:text-gray-800"></i>
                </button>
                <div class="text-center">
                    <p class="text-xl font-semibold text-gray-800 mb-8">Want to Make an Order?</p>
                    <a href="SignIn.php" class="flex justify-between bg-indigo-400 text-white py-2 px-4 w-full hover:bg-indigo-500 transition-colors duration-150">
                        <i class="ri-mail-line text-2xl"></i>
                        <div class="w-full">
                            <p class="font-semibold text-lg">Sign in with email</p>
                        </div>
                    </a>
                </div>

                <div class="relative my-6">
                    <div class="absolute inset-0 flex items-center">
                        <div class="w-full border-t border-gray-300"></div>
                    </div>
                    <div class="relative flex justify-center text-sm">
                        <span class="px-3 bg-white text-gray-500">New to VELIRO?</span>
                    </div>
                </div>

                <!-- Signup Button -->
                <a href="SignUp.php" class="relative text-center cursor-pointer select-none group">
                    <p class="relative z-10 text-indigo-500 text-lg font-semibold pb-6">Create one</p>
                </a>
                <p class="text-center text-xs text-gray-500">Click "Sign in" to agree to VELIRO's <a class="underline hover:no-underline" href="TermsOfUse.php">Terms of Use</a> and acknowledge that VELIRO's <a class="underline hover:no-underline" href="Privacy.php">Privacy Ploicy</a> applies to you.</p>
            </div>
        </div>
    <?php endif; ?>

    <section class="SVG flex justify-center p-4">
        <form action="<?php $_SERVER["PHP_SELF"] ?>" method="post" enctype="multipart/form-data" class="flex flex-col md:flex-row items-center gap-10 max-w-[1000px]">

            <input type="hidden" name="product_Id" value="<?php echo $product_id; ?>">
            <input type="hidden" name="product_size" value="<?php echo $product_size; ?>">

            <div class="flex flex-col-reverse sm:flex-row gap-3 w-full select-none sm:w-2/3">
                <div class="select-none cursor-pointer space-y-1 flex gap-2 sm:block">
                    <div class="product-detail-img w-14 h-16" onclick="changeImage('<?php echo $img1; ?>')">
                        <img class="w-full h-full rounded object-cover hover:border-2 hover:border-indigo-300" src="<?php echo $img1; ?>" alt="Image" onmouseover="changeMainImage(this)" onmouseout="resetMainImage()">
                    </div>
                    <div class="product-detail-img w-14 h-16" onclick="changeImage('<?php echo $img2; ?>')">
                        <img class="w-full h-full rounded object-cover hover:border-2 hover:border-indigo-300" src="<?php echo $img2; ?>" alt="Image" onmouseover="changeMainImage(this)" onmouseout="resetMainImage()">
                    </div>
                    <div class="product-detail-img w-14 h-16" onclick="changeImage('<?php echo $img3; ?>')">
                        <img class="w-full h-full rounded object-cover hover:border-2 hover:border-indigo-300" src="<?php echo $img3; ?>" alt="Image" onmouseover="changeMainImage(this)" onmouseout="resetMainImage()">
                    </div>
                </div>
                <div class="max-w-[500px] mx-auto relative">
                    <div class="w-full md:max-w-[500px]">
                        <img id="mainImage" class="w-full h-full object-cover" src="<?php echo $img1; ?>" alt="Image">
                    </div>
                    <?php
                    $check_query = "SELECT COUNT(*) as count FROM favoritetb WHERE  ProductID = '$product_id'";
                    $result = mysqli_query($connect, $check_query);
                    $row = mysqli_fetch_assoc($result);
                    $count = $row['count'];

                    if ($count) {
                    ?>
                        <div class="flex gap-2 absolute bottom-10 right-0 bg-zinc-900 text-white px-4 rounded-l-full">
                            <p><?php echo $count; ?></p>
                            <i class="ri-heart-3-fill text-lg"></i>
                        </div>
                    <?php
                    }
                    ?>

                </div>
            </div>
            <div class="w-full md:max-w-[290px]">
                <h1 class="text-xl mb-2"><?php echo $title ?></h1>

                <?php
                if ($discount_price == 0) {
                ?>
                    <p class="text-lg font-bold mb-2">$<?php echo $price; ?></p>
                <?php
                } else {
                ?>
                    <div class="mb-2">
                        <span class="text-sm line-through">$<?php echo $price ?></span>
                        <span class="font-bold text-lg text-red-500">$<?php echo $discount_price ?></span>
                    </div>
                <?php
                }
                ?>

                <?php
                $review_select = "SELECT Rating FROM reviewtb WHERE ProductID = '$product_id'";
                $select_query = mysqli_query($connect, $review_select);

                // Check if there are any reviews
                $totalReviews = mysqli_num_rows($select_query);
                if ($totalReviews > 0) {
                    $totalRating = 0;

                    // Sum all ratings
                    while ($review = mysqli_fetch_array($select_query)) {
                        $totalRating += $review['Rating'];
                    }

                    // Calculate the average rating
                    $averageRating = $totalRating / $totalReviews;
                } else {
                    $averageRating = 0;
                }
                ?>

                <div class="flex items-center gap-3 mb-4">
                    <div class="select-none space-x-1 cursor-pointer">
                        <?php
                        $fullStars = floor($averageRating);
                        $halfStar = ($averageRating - $fullStars) >= 0.5 ? 1 : 0;
                        $emptyStars = 5 - ($fullStars + $halfStar);

                        // Display full stars
                        for ($i = 0; $i < $fullStars; $i++) {
                            echo '<i class="ri-star-fill"></i>';
                        }

                        // Display half star if needed
                        if ($halfStar) {
                            echo '<i class="ri-star-half-line"></i>';
                        }

                        // Display empty stars
                        for ($i = 0; $i < $emptyStars; $i++) {
                            echo '<i class="ri-star-line"></i>';
                        }
                        ?>
                    </div>
                    <p class="text-gray-500 text-sm">
                        <?php echo number_format($averageRating, 1); ?> out of 5
                        (<?php echo $totalReviews; ?> review<?php echo ($totalReviews > 1) ? 's' : ''; ?>)
                    </p>
                </div>

                <?php
                if (isset($_GET["product_ID"])) {
                    $product_id = $_GET["product_ID"];

                    $query = "SELECT * FROM producttb p, producttypetb pt
                    WHERE p.ProductTypeID = pt.ProductTypeID
                    AND p.ProductID = '$product_id'";

                    $result = mysqli_query($connect, $query);
                    $array = mysqli_fetch_array($result);

                    $stock = $array['Stock'];
                }
                ?>

                <div class="mb-4 flex justify-between items-center">
                    <p class="text-sm"><span class="text-xs font-bold">COLOUR : </span><?php echo $color; ?></p>
                    <p class="text-sm text-gray-500">(<?php echo $stock ?> available)</p>
                </div>

                <div class="mb-4">
                    <div class="flex <?php echo ($product_type_id == 3) ? 'justify-start' : 'justify-between'; ?>">
                        <h1>Size:</h1>

                        <!-- Condition based on accessories -->
                        <?php
                        if ($product_type_id == 9) {
                        ?>
                            <p class="pl-1">One Size</p>
                        <?php
                        } else {
                        ?>
                            <a href="#" id="sizeGuideLink" class="text-sm underline text-gray-500 hover:text-gray-400 transition-colors duration-200">Size Guide</a>

                            <!-- Size Guide Modal -->
                            <div id="sizeGuideModal" class="fixed inset-0 hidden bg-gray-800 bg-opacity-50 items-center justify-center z-50">
                                <div class="bg-white rounded-lg shadow-lg h-full sm:h-[500px] max-w-full sm:max-w-lg w-full p-3 sm:p-6 scrollable-div">
                                    <div class="flex justify-between items-center pb-3">
                                        <h2 class="text-lg font-semibold text-gray-800">Size Guide</h2>
                                        <button id="closeModal" class="text-gray-500 hover:text-gray-800 focus:outline-none">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                            </svg>
                                        </button>
                                    </div>
                                    <div class="overflow-y-auto max-h-full sm:max-h-96">
                                        <!-- Size chart content -->
                                        <table class="w-full text-sm text-left text-gray-600">
                                            <thead>
                                                <tr>
                                                    <th class="px-4 py-2">Size</th>
                                                    <th class="px-4 py-2">Chest (inches)</th>
                                                    <th class="px-4 py-2">Waist (inches)</th>
                                                    <th class="px-4 py-2">Hips (inches)</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr>
                                                    <td class="border px-4 py-2">XS</td>
                                                    <td class="border px-4 py-2">32-34</td>
                                                    <td class="border px-4 py-2">26-28</td>
                                                    <td class="border px-4 py-2">33-35</td>
                                                </tr>
                                                <tr>
                                                    <td class="border px-4 py-2">S</td>
                                                    <td class="border px-4 py-2">34-36</td>
                                                    <td class="border px-4 py-2">28-30</td>
                                                    <td class="border px-4 py-2">35-37</td>
                                                </tr>
                                                <tr>
                                                    <td class="border px-4 py-2">M</td>
                                                    <td class="border px-4 py-2">38-40</td>
                                                    <td class="border px-4 py-2">32-34</td>
                                                    <td class="border px-4 py-2">39-41</td>
                                                </tr>
                                                <tr>
                                                    <td class="border px-4 py-2">L</td>
                                                    <td class="border px-4 py-2">42-44</td>
                                                    <td class="border px-4 py-2">36-38</td>
                                                    <td class="border px-4 py-2">43-45</td>
                                                </tr>
                                                <tr>
                                                    <td class="border px-4 py-2">XL</td>
                                                    <td class="border px-4 py-2">46-48</td>
                                                    <td class="border px-4 py-2">40-42</td>
                                                    <td class="border px-4 py-2">47-49</td>
                                                </tr>
                                                <tr>
                                                    <td class="border px-4 py-2">XXL</td>
                                                    <td class="border px-4 py-2">50-52</td>
                                                    <td class="border px-4 py-2">44-46</td>
                                                    <td class="border px-4 py-2">51-53</td>
                                                </tr>
                                                <tr>
                                                    <td class="border px-4 py-2">3XL</td>
                                                    <td class="border px-4 py-2">54-56</td>
                                                    <td class="border px-4 py-2">48-50</td>
                                                    <td class="border px-4 py-2">55-57</td>
                                                </tr>
                                            </tbody>
                                        </table>
                                        <table class="w-full mt-4 text-sm text-left text-gray-600">
                                            <thead>
                                                <tr>
                                                    <th class="px-4 py-2">UK Size</th>
                                                    <th class="px-4 py-2">US Size</th>
                                                    <th class="px-4 py-2">EU Size</th>
                                                    <th class="px-4 py-2">Foot Length (inches)</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr>
                                                    <td class="border px-4 py-2">UK 3</td>
                                                    <td class="border px-4 py-2">US 4</td>
                                                    <td class="border px-4 py-2">EU 36</td>
                                                    <td class="border px-4 py-2">8.5</td>
                                                </tr>
                                                <tr>
                                                    <td class="border px-4 py-2">UK 3.5</td>
                                                    <td class="border px-4 py-2">US 4.5</td>
                                                    <td class="border px-4 py-2">EU 36.5</td>
                                                    <td class="border px-4 py-2">8.75</td>
                                                </tr>
                                                <tr>
                                                    <td class="border px-4 py-2">UK 4</td>
                                                    <td class="border px-4 py-2">US 5</td>
                                                    <td class="border px-4 py-2">EU 37</td>
                                                    <td class="border px-4 py-2">9</td>
                                                </tr>
                                                <tr>
                                                    <td class="border px-4 py-2">UK 4.5</td>
                                                    <td class="border px-4 py-2">US 5.5</td>
                                                    <td class="border px-4 py-2">EU 37.5</td>
                                                    <td class="border px-4 py-2">9.125</td>
                                                </tr>
                                                <tr>
                                                    <td class="border px-4 py-2">UK 5</td>
                                                    <td class="border px-4 py-2">US 6</td>
                                                    <td class="border px-4 py-2">EU 38</td>
                                                    <td class="border px-4 py-2">9.25</td>
                                                </tr>
                                                <tr>
                                                    <td class="border px-4 py-2">UK 5.5</td>
                                                    <td class="border px-4 py-2">US 6.5</td>
                                                    <td class="border px-4 py-2">EU 38.5</td>
                                                    <td class="border px-4 py-2">9.5</td>
                                                </tr>
                                                <tr>
                                                    <td class="border px-4 py-2">UK 6</td>
                                                    <td class="border px-4 py-2">US 7</td>
                                                    <td class="border px-4 py-2">EU 39</td>
                                                    <td class="border px-4 py-2">9.75</td>
                                                </tr>
                                                <tr>
                                                    <td class="border px-4 py-2">UK 6.5</td>
                                                    <td class="border px-4 py-2">US 7.5</td>
                                                    <td class="border px-4 py-2">EU 40</td>
                                                    <td class="border px-4 py-2">10</td>
                                                </tr>
                                                <tr>
                                                    <td class="border px-4 py-2">UK 7</td>
                                                    <td class="border px-4 py-2">US 8</td>
                                                    <td class="border px-4 py-2">EU 40.5</td>
                                                    <td class="border px-4 py-2">10.125</td>
                                                </tr>
                                                <tr>
                                                    <td class="border px-4 py-2">UK 7.5</td>
                                                    <td class="border px-4 py-2">US 8.5</td>
                                                    <td class="border px-4 py-2">EU 41</td>
                                                    <td class="border px-4 py-2">10.25</td>
                                                </tr>
                                                <tr>
                                                    <td class="border px-4 py-2">UK 8</td>
                                                    <td class="border px-4 py-2">US 9</td>
                                                    <td class="border px-4 py-2">EU 42</td>
                                                    <td class="border px-4 py-2">10.5</td>
                                                </tr>
                                                <tr>
                                                    <td class="border px-4 py-2">UK 8.5</td>
                                                    <td class="border px-4 py-2">US 9.5</td>
                                                    <td class="border px-4 py-2">EU 42.5</td>
                                                    <td class="border px-4 py-2">10.75</td>
                                                </tr>
                                                <tr>
                                                    <td class="border px-4 py-2">UK 9</td>
                                                    <td class="border px-4 py-2">US 10</td>
                                                    <td class="border px-4 py-2">EU 43</td>
                                                    <td class="border px-4 py-2">11</td>
                                                </tr>
                                                <tr>
                                                    <td class="border px-4 py-2">UK 9.5</td>
                                                    <td class="border px-4 py-2">US 10.5</td>
                                                    <td class="border px-4 py-2">EU 44</td>
                                                    <td class="border px-4 py-2">11.125</td>
                                                </tr>
                                                <tr>
                                                    <td class="border px-4 py-2">UK 10</td>
                                                    <td class="border px-4 py-2">US 11</td>
                                                    <td class="border px-4 py-2">EU 44.5</td>
                                                    <td class="border px-4 py-2">11.25</td>
                                                </tr>
                                                <tr>
                                                    <td class="border px-4 py-2">UK 10.5</td>
                                                    <td class="border px-4 py-2">US 11.5</td>
                                                    <td class="border px-4 py-2">EU 45</td>
                                                    <td class="border px-4 py-2">11.5</td>
                                                </tr>
                                                <tr>
                                                    <td class="border px-4 py-2">UK 11</td>
                                                    <td class="border px-4 py-2">US 12</td>
                                                    <td class="border px-4 py-2">EU 45.5</td>
                                                    <td class="border px-4 py-2">11.75</td>
                                                </tr>
                                                <tr>
                                                    <td class="border px-4 py-2">UK 11.5</td>
                                                    <td class="border px-4 py-2">US 12.5</td>
                                                    <td class="border px-4 py-2">EU 46</td>
                                                    <td class="border px-4 py-2">12</td>
                                                </tr>
                                                <tr>
                                                    <td class="border px-4 py-2">UK 12</td>
                                                    <td class="border px-4 py-2">US 13</td>
                                                    <td class="border px-4 py-2">EU 47</td>
                                                    <td class="border px-4 py-2">12.25</td>
                                                </tr>
                                                <tr>
                                                    <td class="border px-4 py-2">UK 12.5</td>
                                                    <td class="border px-4 py-2">US 13.5</td>
                                                    <td class="border px-4 py-2">EU 47.5</td>
                                                    <td class="border px-4 py-2">12.5</td>
                                                </tr>
                                                <tr>
                                                    <td class="border px-4 py-2">UK 13</td>
                                                    <td class="border px-4 py-2">US 14</td>
                                                    <td class="border px-4 py-2">EU 48</td>
                                                    <td class="border px-4 py-2">12.75</td>
                                                </tr>
                                                <tr>
                                                    <td class="border px-4 py-2">UK 13.5</td>
                                                    <td class="border px-4 py-2">US 14.5</td>
                                                    <td class="border px-4 py-2">EU 48.5</td>
                                                    <td class="border px-4 py-2">13</td>
                                                </tr>
                                                <tr>
                                                    <td class="border px-4 py-2">UK 14</td>
                                                    <td class="border px-4 py-2">US 15</td>
                                                    <td class="border px-4 py-2">EU 49</td>
                                                    <td class="border px-4 py-2">13.25</td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        <?php
                        }
                        ?>
                    </div>

                    <!-- Condition based on clothing or shoes or accessories -->
                    <?php
                    if ($product_type_id == 9) {
                    ?>
                        <div class="max-w-md mx-auto mt-4">
                            <select id="size" name="size" class="hidden" required>
                                <option value="One size" selected>One Size</option>
                            </select>
                        </div>
                    <?php
                    } elseif ($product_type_id == 7) {
                    ?>
                        <div class="max-w-md mx-auto mt-4">
                            <select id="size" name="size" class="block w-full p-2 border border-gray-300 rounded-md text-gray-700 bg-white cursor-pointer focus:border-indigo-500 focus:ring-indigo-500 transition-colors duration-200">
                                <option value="" disabled selected>Choose a size</option>
                                <option value="3">UK 3</option>
                                <option value="3.5">UK 3.5</option>
                                <option value="4">UK 4</option>
                                <option value="4.5">UK 4.5</option>
                                <option value="5">UK 5</option>
                                <option value="5.5">UK 5.5</option>
                                <option value="6">UK 6</option>
                                <option value="6.5">UK 6.5</option>
                                <option value="7">UK 7</option>
                                <option value="7.5">UK 7.5</option>
                                <option value="8">UK 8</option>
                                <option value="8.5">UK 8.5</option>
                                <option value="9">UK 9</option>
                                <option value="9.5">UK 9.5</option>
                                <option value="10">UK 10</option>
                                <option value="10.5">UK 10.5</option>
                                <option value="11">UK 11</option>
                                <option value="11.5">UK 11.5</option>
                                <option value="12">UK 12</option>
                                <option value="12.5">UK 12.5</option>
                                <option value="13">UK 13</option>
                                <option value="13.5">UK 13.5</option>
                                <option value="14">UK 14</option>
                            </select>
                        </div>
                        <?php
                        if (!empty($alert_message1)) {
                        ?>
                            <p class="text-red-600 text-sm ml-2"><?php echo $alert_message1 ?></p>
                        <?php
                        }
                        ?>
                    <?php
                    } else {
                    ?>
                        <!-- For product except shoes -->
                        <div class="grid grid-cols-3 gap-2 mt-2 select-none">
                            <label class="cursor-pointer">
                                <input type="radio" name="size" value="1" class="sr-only peer" />
                                <span class="flex items-center justify-center px-3 py-2 border rounded-md text-sm font-medium text-gray-700 bg-white peer-checked:border-indigo-500 peer-checked:text-indigo-500 peer-checked:bg-indigo-50 transition-colors duration-200">
                                    XS
                                </span>
                            </label>
                            <label class="cursor-pointer">
                                <input type="radio" name="size" value="2" class="sr-only peer" />
                                <span class="flex items-center justify-center px-3 py-2 border rounded-md text-sm font-medium text-gray-700 bg-white peer-checked:border-indigo-500 peer-checked:text-indigo-500 peer-checked:bg-indigo-50 transition-colors duration-200">
                                    S
                                </span>
                            </label>
                            <label class="cursor-pointer">
                                <input type="radio" name="size" value="3" class="sr-only peer" />
                                <span class="flex items-center justify-center px-3 py-2 border rounded-md text-sm font-medium text-gray-700 bg-white peer-checked:border-indigo-500 peer-checked:text-indigo-500 peer-checked:bg-indigo-50 transition-colors duration-200">
                                    M
                                </span>
                            </label>
                            <label class="cursor-pointer">
                                <input type="radio" name="size" value="4" class="sr-only peer" />
                                <span class="flex items-center justify-center px-3 py-2 border rounded-md text-sm font-medium text-gray-700 bg-white peer-checked:border-indigo-500 peer-checked:text-indigo-500 peer-checked:bg-indigo-50 transition-colors duration-200">
                                    L
                                </span>
                            </label>
                            <label class="cursor-pointer">
                                <input type="radio" name="size" value="5" class="sr-only peer" />
                                <span class="flex items-center justify-center px-3 py-2 border rounded-md text-sm font-medium text-gray-700 bg-white peer-checked:border-indigo-500 peer-checked:text-indigo-500 peer-checked:bg-indigo-50 transition-colors duration-200">
                                    XL
                                </span>
                            </label>
                            <label class="cursor-pointer">
                                <input type="radio" name="size" value="6" class="sr-only peer" />
                                <span class="flex items-center justify-center px-3 py-2 border rounded-md text-sm font-medium text-gray-700 bg-white peer-checked:border-indigo-500 peer-checked:text-indigo-500 peer-checked:bg-indigo-50 transition-colors duration-200">
                                    XXL
                                </span>
                            </label>
                            <label class="cursor-pointer">
                                <input type="radio" name="size" value="7" class="sr-only peer" />
                                <span class="flex items-center justify-center px-3 py-2 border rounded-md text-sm font-medium text-gray-700 bg-white peer-checked:border-indigo-500 peer-checked:text-indigo-500 peer-checked:bg-indigo-50 transition-colors duration-200">
                                    3XL
                                </span>
                            </label>
                        </div>
                        <?php
                        if (!empty($alert_message1)) {
                        ?>
                            <p class="text-red-600 text-sm ml-2"><?php echo $alert_message1 ?></p>
                    <?php
                        }
                    }
                    ?>
                </div>

                <div class="flex items-center justify-between mb-4">

                    <!-- Check if user is logged in -->
                    <?php
                    if ($id) {
                        if ($stock > 0) {
                    ?>
                            <input type="submit" value="ADD TO BAG" name="addtobag" class="bg-indigo-400 w-56 text-center font-semibold text-white py-3 select-none cursor-pointer hover:bg-indigo-500 transition-colors duration-200">
                        <?php
                        } else {
                        ?>
                            <button disabled class="bg-gray-400 w-56 text-center font-semibold text-white py-3 select-none cursor-not-allowed">Out of Stock</button>
                        <?php
                        }
                    } else {
                        if ($stock > 0) {
                        ?>
                            <input type="submit" value="ADD TO BAG" name="addtobag" class="bg-indigo-400 w-56 text-center font-semibold text-white py-3 select-none cursor-pointer hover:bg-indigo-500 transition-colors duration-200">
                        <?php
                        } else {
                        ?>
                            <button disabled class="bg-gray-400 w-56 text-center font-semibold text-white py-3 select-none cursor-not-allowed">Out of Stock</button>
                    <?php
                        }
                    }
                    ?>
                    <?php
                    $check = "SELECT * FROM favoritetb 
                    WHERE ProductID = '$product_id'
                    And CustomerID = '$id'";

                    $check_query = mysqli_query($connect, $check);
                    $rowCount = mysqli_num_rows($check_query);

                    $buttonHtml = $rowCount > 0
                        ? '<button type="submit" name="removeBtn"><i class="ri-heart-3-fill text-indigo-400 text-xl sm:text-2xl flex justify-center items-center bg-black/5 w-8 sm:w-9 h-8 sm:h-9 rounded-full hover:bg-black/10 transition-colors duration-200 cursor-pointer" onclick="toggleFavorite(this)"></i></button>'
                        : '<button type="submit" name="favoriteBtn"><i class="ri-heart-3-line text-indigo-400 text-xl sm:text-2xl flex justify-center items-center bg-black/5 w-8 sm:w-9 h-8 sm:h-9 rounded-full hover:bg-black/10 transition-colors duration-200 cursor-pointer" onclick="toggleFavorite(this)"></i></button>';
                    echo $buttonHtml;
                    ?>
                </div>

                <div class="flex gap-4 border p-4 mb-4">
                    <i class="ri-truck-line text-2xl"></i>
                    <div>
                        <p>Free delivery on qualifying orders.</p>
                        <a href="Delivery.php" class="text-xs underline text-gray-500 hover:text-gray-400 transition-colors duration-200">View our Delivery & Returns Policy</a>
                    </div>
                </div>

                <div class="divide-y cursor-pointer" id="accordion">
                    <div class="p-1" data-target="details">
                        <div class="flex items-center justify-between font-semibold">
                            <h1>Product Details</h1>
                            <i class="ri-add-line text-xl"></i>
                        </div>
                        <div class="h-0 overflow-hidden transition-all duration-300 ease-in-out text-gray-600 text-sm" id="details">
                            <p><?php echo $product_detail; ?></p>
                        </div>
                    </div>

                    <?php if ($brand !== 'none') { ?>
                        <div class="p-1" data-target="brand">
                            <div class="flex items-center justify-between font-semibold">
                                <h1>Brand</h1>
                                <i class="ri-add-line text-xl"></i>
                            </div>
                            <div class="h-0 overflow-hidden transition-all duration-300 ease-in-out text-gray-600 text-sm" id="brand">
                                <p><?php echo $brand; ?></p>
                            </div>
                        </div>
                    <?php } ?>

                    <?php if ($model_height !== 'none' && $product_size !== 'none') { ?>
                        <div class="p-1" data-target="size-fit">
                            <div class="flex items-center justify-between font-semibold">
                                <h1>Size & Fit</h1>
                                <i class="ri-add-line text-xl"></i>
                            </div>
                            <div class="h-0 overflow-hidden transition-all duration-300 ease-in-out text-gray-600 text-sm" id="size-fit">
                                <p>Model's height: <span><?php echo $model_height ?></span></p>
                                <p>Model is wearing: <span><?php echo $product_size ?></span></p>
                            </div>
                        </div>
                    <?php } ?>

                    <?php if ($look_after_me !== 'none') { ?>
                        <div class="p-1 group" data-target="look-after">
                            <div class="flex items-center justify-between font-semibold">
                                <h1>Look After Me</h1>
                                <i class="ri-add-line text-xl"></i>
                            </div>
                            <div class="h-0 overflow-hidden transition-all duration-300 ease-in-out text-gray-600 text-sm" id="look-after">
                                <p><?php echo $look_after_me; ?></p>
                            </div>
                        </div>
                    <?php } ?>

                    <?php if ($about_me !== 'none') { ?>
                        <div class="p-1" data-target="about">
                            <div class="flex items-center justify-between font-semibold">
                                <h1>About Me</h1>
                                <i class="ri-add-line text-xl"></i>
                            </div>
                            <div class="h-0 overflow-hidden transition-all duration-300 ease-in-out text-gray-600 text-sm" id="about">
                                <p><?php echo $about_me; ?></p>
                            </div>
                        </div>
                    <?php } ?>
                </div>
            </div>
        </form>
    </section>

    <section class="border-t p-4">
        <h1 class="max-w-[800px] mx-auto font-bold text-xl mb-5">PEOPLE ALSO BOUGHT</h1>
        <div class="max-w-[700px] mx-auto grid gap-4 grid-cols-2 md:grid-cols-3 lg:grid-cols-4">
            <?php
            $product_select = "SELECT * FROM producttb
                ORDER BY RAND() LIMIT 4";
            $select_query = mysqli_query($connect, $product_select);
            $count = mysqli_num_rows($select_query);

            for ($i = 0; $i < $count; $i++) {
                $array = mysqli_fetch_array($select_query);
                $product_id = $array['ProductID'];
                $title = $array['Title'];
                $img1 = $array['img1'];
                $price = $array['Price'];
                $discount_price = $array['DiscountPrice'];
            ?>

                <form action="<?php $_SERVER["PHP_SELF"] ?>" method="post" enctype="multipart/form-data" class="relative">
                    <input type="hidden" name="product_Id" value="<?php echo $product_id; ?>">

                    <a href="ProductsDetails.php?product_ID=<?php echo $product_id ?>">
                        <div class="w-full mb-4 overflow-hidden select-none">
                            <img class="w-full h-full object-cover" src="<?php echo $img1 ?>" alt="Image">
                        </div>
                        <div>
                            <h1 class="text-sm mb-2 truncate md:whitespace-normal"><?php echo $title ?></h1>
                            <?php
                            if ($discount_price == 0) {
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
                        </div>
                    </a>

                    <?php
                    $check = "SELECT * FROM favoritetb
                    WHERE ProductID = '$product_id'
                    And CustomerID = '$id'";

                    $check_query = mysqli_query($connect, $check);
                    $rowCount = mysqli_num_rows($check_query);

                    if ($rowCount > 0) { ?>
                        <button type="submit" name="removeBtn">
                            <i class="ri-heart-3-fill text-indigo-400 text-xl sm:text-2xl absolute left-1 top-1 flex justify-center items-center bg-black/5 w-8 sm:w-9 h-8 sm:h-9 rounded-full hover:bg-black/10 transition-colors duration-200 cursor-pointer" onclick="toggleFavorite(this)"></i>
                        </button>
                    <?php } else {
                    ?>
                        <button type="submit" name="favoriteBtn">
                            <i class="ri-heart-3-line text-indigo-400 text-xl sm:text-2xl absolute left-1 top-1 flex justify-center items-center bg-black/5 w-8 sm:w-9 h-8 sm:h-9 rounded-full hover:bg-black/10 transition-colors duration-200 cursor-pointer" onclick="toggleFavorite(this)"></i>
                        </button>
                    <?php
                    }
                    ?>

                </form>

            <?php
            }
            ?>

        </div>
    </section>

    <section>
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 overflow-hidden border-t pt-12">
            <h2 class="text-2xl sm:text-3xl font-extralight text-gray-900 text-center mb-8">What Our Customers Are Saying</h2>

            <?php
            $product_id = $_GET["product_ID"];

            // Handle review deletion
            if (isset($_POST['delete_review_id'])) {
                $delete_review_id = $_POST['delete_review_id'];
                $delete_query = "DELETE FROM reviewtb WHERE ReviewID = '$delete_review_id'";
                mysqli_query($connect, $delete_query);
                exit();
            }

            // Handle review update
            if (isset($_POST['edit_review_id'])) {
                $edit_review_id = $_POST['edit_review_id'];
                $new_rating = $_POST['edit_rating'];
                $new_comment = $_POST['edit_comment'];

                $update_query = "UPDATE reviewtb SET Rating = '$new_rating', Comment = '$new_comment' WHERE ReviewID = '$edit_review_id'";
                mysqli_query($connect, $update_query);
                exit();
            }
            ?>

            <!-- Reviews Section -->
            <div class="overflow-auto">
                <div class="divide-y-2 divide-gray-100">
                    <?php
                    // Ensure $product_id is set and contains a valid product ID
                    if (isset($product_id)) {
                        $review_select = "SELECT * FROM reviewtb WHERE ProductID = '$product_id'";
                        $select_query = mysqli_query($connect, $review_select);

                        // Check if there are any reviews for this specific product
                        if (mysqli_num_rows($select_query) > 0) {
                            // Loop through the reviews
                            while ($review = mysqli_fetch_array($select_query)) {
                                $customerid = $review['CustomerID'];
                                $rating = $review['Rating'];
                                $comment = $review['Comment'];
                                $reviewdate = $review['ReviewDate'];

                                // Fetch the corresponding customer details
                                $customer_select = "SELECT * FROM customertb WHERE CustomerID = '$customerid'";
                                $customer_query = mysqli_query($connect, $customer_select);
                                $customer = mysqli_fetch_array($customer_query);
                                $fullname = $customer['FullName'];

                                // Fetch the corresponding product details
                                $product_select = "SELECT * FROM producttb WHERE ProductID = '$product_id'";
                                $product_query = mysqli_query($connect, $product_select);
                                $product = mysqli_fetch_array($product_query);
                                $brand = $product['Brand'];
                                $color = $product['Color'];
                                $producttype = $product['ProductTypeID'];

                                // Fetch the corresponding product type details
                                $producttype_select = "SELECT * FROM producttypetb WHERE ProductTypeID = '$producttype'";
                                $producttype_select_query = mysqli_query($connect, $producttype_select);
                                $producttype = mysqli_fetch_array($producttype_select_query);
                                $producttypename = $producttype['ProductTypeName'];

                                // Generate the star rating
                                $stars = '';
                                for ($i = 0; $i < 5; $i++) {
                                    if ($i < $rating) {
                                        $stars .= '<svg class="w-4 h-4 fill-current text-yellow-500 inline-block" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path d="M12 .587l3.668 7.431 8.197 1.19-5.922 5.768 1.397 8.157-7.34-3.856-7.339 3.856 1.397-8.157-5.922-5.768 8.197-1.19z"/></svg>';
                                    } else {
                                        $stars .= '<svg class="w-4 h-4 fill-current text-gray-300 inline-block" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path d="M12 .587l3.668 7.431 8.197 1.19-5.922 5.768 1.397 8.157-7.34-3.856-7.339 3.856 1.397-8.157-5.922-5.768 8.197-1.19z"/></svg>';
                                    }
                                }

                                // Check if the logged-in user is the same as the review's customer
                                if (isset($_SESSION['CustomerID']) && $_SESSION['CustomerID'] == $customerid) {
                                    $showIcons = true;
                                } else {
                                    $showIcons = false;
                                }
                    ?>
                                <!-- Output the review and customer details -->
                                <div class="bg-white py-4 shadow-sm flex items-start space-x-4">
                                    <img class="w-9 h-9 rounded-full border border-gray-300 hidden sm:block" src="Images/90d1ac48711f63c6a290238c8382632f.jpg" alt="User Logo">
                                    <div>
                                        <div class="flex items-center gap-2">
                                            <img class="w-9 h-9 rounded-full border border-gray-300 block sm:hidden" src="Images/90d1ac48711f63c6a290238c8382632f.jpg" alt="User Logo">
                                            <div class="flex items-center flex-wrap space-x-2">
                                                <p class="text-sm font-semibold text-gray-800"><?php echo $fullname ?></p>
                                                <span class="text-xs text-gray-500">• Verified Buyer <i class="ri-checkbox-circle-line text-green-500"></i></span>
                                                <span class="text-xs text-gray-500">Reviewed on <span><?php echo $reviewdate ?></span></span>
                                                <?php if ($showIcons): ?>

                                                    <!-- Edit and Delete Icons -->
                                                    <span class="text-gray-500 ml-2">
                                                        <a href="javascript:void(0);" onclick="openEditModal(<?php echo $review['ReviewID']; ?>, <?php echo $rating; ?>, '<?php echo $comment; ?>');" class="text-indigo-600 hover:text-indigo-800">
                                                            <i class="ri-edit-line"></i>
                                                        </a>
                                                        <a href="javascript:void(0);" onclick="deleteReview(<?php echo $review['ReviewID']; ?>);" class="text-red-600 hover:text-red-800 ml-2">
                                                            <i class="ri-delete-bin-line"></i>
                                                        </a>
                                                    </span>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                        <div class="flex items-center mt-1"><?php echo $stars ?></div>
                                        <div class="flex gap-1 divide-x-2 mt-1">
                                            <p class="text-gray-700 text-xs font-semibold px-1">Brand: <span class="font-normal"><?php echo $brand ?></span></p>
                                            <p class="text-gray-700 text-xs font-semibold px-1">Color: <span class="font-normal"><?php echo $color ?></span></p>
                                            <p class="text-gray-700 text-xs font-semibold px-1">Pattern: <span class="font-normal"><?php echo $producttypename ?></span></p>
                                        </div>

                                        <?php
                                        // Split the comment into an array of words
                                        $comment_words = explode(' ', $comment);

                                        // Check if the comment has more than 100 words
                                        if (count($comment_words) > 100) {
                                            $truncated_comment = implode(' ', array_slice($comment_words, 0, 100)) . '...';
                                            $full_comment = $comment;
                                        } else {
                                            $truncated_comment = $comment;
                                            $full_comment = '';
                                        }
                                        ?>

                                        <!-- Truncated Comment -->
                                        <p class="text-gray-700 mt-2 text-sm leading-relaxed truncated-comment">
                                            <?php echo $truncated_comment; ?>
                                        </p>
                                        <?php if ($full_comment): ?>
                                            <p class="text-indigo-600 text-sm cursor-pointer mt-1 read-more">
                                                <i class="ri-arrow-down-s-line"></i> Read More
                                            </p>
                                        <?php endif; ?>

                                        <!-- Full Comment -->
                                        <p class="text-gray-700 mt-2 text-sm leading-relaxed full-comment hidden">
                                            <?php echo $full_comment; ?>
                                        </p>
                                        <?php if ($full_comment): ?>
                                            <p class="text-indigo-600 text-sm cursor-pointer mt-1 read-less hidden">
                                                <i class="ri-arrow-up-s-line"></i> Read Less
                                            </p>
                                        <?php endif; ?>
                                    </div>
                                </div>

                            <?php
                            }
                        } else {
                            ?>
                            <p class="mt-10 py-14 text-center text-gray-500 text-base sm:text-lg">No reviews for this product.</p>
                    <?php
                        }
                    } else {
                        echo "<p class='mt-10 py-36 text-center text-gray-500 text-lg'>Product ID is not set.</p>";
                    }
                    ?>
                </div>
            </div>

            <!-- Edit Review Modal -->
            <div id="editReviewModal" class="fixed inset-0 z-50 items-center justify-center bg-black bg-opacity-50 hidden">
                <div class="bg-white w-full max-w-lg p-5 rounded-lg shadow-lg">
                    <h2 class="text-xl font-semibold text-gray-800 mb-4">Edit Review</h2>
                    <form id="editReviewForm" method="POST">
                        <input type="hidden" name="edit_review_id" id="edit_review_id">
                        <div class="mb-4">
                            <label for="edit_rating" class="block text-sm font-medium text-gray-700">Rating</label>
                            <select name="edit_rating" id="edit_rating" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                                <option value="1">1 Star</option>
                                <option value="2">2 Stars</option>
                                <option value="3">3 Stars</option>
                                <option value="4">4 Stars</option>
                                <option value="5">5 Stars</option>
                            </select>
                        </div>
                        <div class="mb-4">
                            <label for="edit_comment" class="block text-sm font-medium text-gray-700">Comment</label>
                            <textarea name="edit_comment" id="edit_comment" rows="4" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm"></textarea>
                        </div>
                        <div class="flex justify-end gap-2">
                            <button type="button" onclick="closeEditModal()" class="bg-gray-500 text-white px-4 py-2 rounded-md">Cancel</button>
                            <button type="submit" class="bg-indigo-600 text-white px-4 py-2 rounded-md">Save Changes</button>
                        </div>
                    </form>
                </div>
            </div>

            <script>
                // Open the edit modal and populate it with review data
                function openEditModal(reviewId, rating, comment) {
                    document.getElementById('edit_review_id').value = reviewId;
                    document.getElementById('edit_rating').value = rating;
                    document.getElementById('edit_comment').value = comment;
                    document.getElementById('editReviewModal').classList.remove('hidden');
                    document.getElementById('editReviewModal').classList.add('flex');
                }

                // Close the edit modal
                function closeEditModal() {
                    document.getElementById('editReviewModal').classList.add('hidden');
                    document.getElementById('editReviewModal').classList.remove('flex');
                }

                // Handle review edit form submission with AJAX
                document.getElementById('editReviewForm').addEventListener('submit', function(e) {
                    e.preventDefault();
                    var formData = new FormData(this);

                    fetch('', { // Empty URL since the form is submitted to the same page
                        method: 'POST',
                        body: formData
                    }).then(response => {
                        if (response.ok) {
                            location.reload();
                        } else {
                            alert('Error updating the review.');
                        }
                    });
                });

                // Handle review deletion with AJAX
                function deleteReview(reviewId) {
                    if (confirm('Are you sure you want to delete this review?')) {
                        var formData = new FormData();
                        formData.append('delete_review_id', reviewId);

                        fetch('', { // Empty URL since the form is submitted to the same page
                            method: 'POST',
                            body: formData
                        }).then(response => {
                            if (response.ok) {
                                location.reload();
                            } else {
                                alert('Error deleting the review.');
                            }
                        });
                    }
                }
            </script>

            <!-- Write Review Button -->
            <div class="text-center mt-8">
                <button id="writeReviewBtn" class="text-indigo-600 hover:text-indigo-700 font-semibold">Write a Review</button>
            </div>

            <!-- Review Form-->
            <div id="reviewFormContainer" class="mt-8 h-0">
                <div class="bg-white p-6">
                    <h3 class="text-2xl font-semibold text-gray-800 mb-4">Leave a Review</h3>
                    <form action="#" method="POST">
                        <input type="hidden" name="product_Id" value="<?php echo $_GET["product_ID"] ?>">

                        <div class="mb-4">
                            <label for="rating" class="block text-sm font-medium text-gray-700">Rating</label>
                            <select id="rating" name="rating" required class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                <option value="5">5 Stars</option>
                                <option value="4">4 Stars</option>
                                <option value="3">3 Stars</option>
                                <option value="2">2 Stars</option>
                                <option value="1">1 Star</option>
                            </select>
                        </div>
                        <div class="mb-4">
                            <label for="comment" class="block text-sm font-medium text-gray-700">Comment</label>
                            <textarea id="comment" name="comment" rows="4" required class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"></textarea>
                        </div>
                        <!-- Date -->
                        <div>
                            <input type="date" class="hidden" id="reviewdate" name="reviewdate" value="<?php echo date("Y-m-d") ?>" required>
                        </div>
                        <div class="text-right">
                            <button type="submit" name="reviewsubmit" class="bg-indigo-600 text-white py-2 px-4 rounded-md hover:bg-indigo-700">Submit Review</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </section>

    <script>
        document.getElementById('writeReviewBtn').addEventListener('click', function() {
            const reviewFormContainer = document.getElementById('reviewFormContainer');
            const comment = document.getElementById('comment');

            if (reviewFormContainer.classList.contains('h-0')) {
                reviewFormContainer.classList.remove('h-0');
                reviewFormContainer.classList.add('h-full');
                comment.focus();
            } else {
                reviewFormContainer.classList.remove('h-full');
                reviewFormContainer.classList.add('h-0');
                comment.value = "";
            }
        });
    </script>

    <?php
    include('./components/Footer.php');
    ?>

    <script src="Customer.js"></script>
</body>

</html>