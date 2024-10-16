<section id="sale-section" class="SVG2 p-2 text-center text-slate-400">
    <p>Monsoon Sale. up to 40% off.</p>
</section>
<header class="sticky top-0 bg-white px-5 sm:px-10 shadow-sm z-20">
    <div id="moveRightLoader" class="fixed top-0 left-0 bg-gray-100 w-full h-1 z-10">
        <p id="move-right" class="absolute top-0 bg-indigo-400 h-1"></p>
    </div>
    <nav class="relative flex justify-between items-center bg-white">
        <div class="flex items-center gap-8 bg-white">
            <div class="flex items-center">
                <i id="searchBtn" class="ri-search-line text-xl cursor-pointer p-1"></i>
            </div>
            <form id="searchBox" class="absolute top-0 w-full bg-white p-5 rounded border-b -z-10 transition-all duration-300 ease-in-out" method="GET" action="Search.php">
                <input id="searchInput" class="indent-2 w-full h-full py-2 outline-none" type="text" name="search" placeholder="Search...">
            </form>
            <ul class="hidden lg:flex font-semibold gap-5">
                <li class="relative inline-flex items-center group py-8 cursor-pointer select-none group">
                    <div class="flex items-center group-hover:text-gray-500 transition-colors duration-200">
                        Shop By Category
                        <i class="ri-arrow-right-s-line rotate-90"></i>
                    </div>

                    <!-- Shop category display -->
                    <div class="hidden absolute top-20 -left-[100px] lg:flex flex-col bg-white w-[300px] h-0 shadow-2xl group-hover:h-screen opacity-0 group-hover:opacity-100 overflow-hidden z-50 transition-all duration-300 ease-in-out">
                        <ul class="w-48 mx-auto select-none">
                            <a href="Accessories.php" class="flex items-center justify-between p-1 cursor-pointer">
                                <p>Accessories</p>
                                <img class="w-16" src="Images/205949368-2.webp" alt="image">
                            </a>
                            <a href="Bottoms.php" class="flex items-center justify-between p-1 cursor-pointer">
                                <p>Bottoms</p>
                                <img class="w-16" src="images/206035112-2.webp" alt="Image">
                            </a>
                            <a href="Shirts.php" class="flex items-center justify-between p-1 cursor-pointer">
                                <p>Shirts</p>
                                <img class="w-16" src="images/205901690-2.webp" alt="Image">
                            </a>
                            <a href="JacketsAndCoats.php" class="flex items-center justify-between p-1 cursor-pointer">
                                <p>Jackets & Coats</p>
                                <img class="w-16" src="images/205792802-2.webp" alt="Image">
                            </a>
                            <a href="Shoes.php" class="flex items-center justify-between p-1 cursor-pointer">
                                <p>Shoes</p>
                                <img class="w-16" src="images/206740153-2.webp" alt="Image">
                            </a>
                        </ul>
                    </div>
                </li>
                <li class="py-8 cursor-pointer select-none group">
                    <a class="group-hover:text-gray-500 transition-colors duration-200" href="NewArrivals.php">New Arrivals</a>
                </li>
                <li class="py-8 cursor-pointer select-none group">
                    <a class="group-hover:text-gray-500 transition-colors duration-200" href="Collections.php">Collections</a>
                </li>
            </ul>
            <i id="hamburgerIcon" class="ri-menu-2-line text-2xl flex lg:hidden cursor-pointer py-8"></i>
        </div>

        <a class="text-3xl md:text-4xl hidden sm:flex text-indigo-400 font-semibold" href="Home.php"><img class="w-36" src="Images/Screenshot 2024-08-18 112444.png" alt="Logo"></a>

        <div class="flex gap-16 item-center">
            <ul class="hidden lg:flex items-center gap-7 text-xl">
                <li class="hover:text-rose-700 transition-colors duration-200">
                    <a href="#"><i class="ri-instagram-line"></i></a>
                </li>
                <li class="hover:text-blue-700 transition-colors duration-200">
                    <a href="#"><i class="ri-facebook-circle-fill"></i></a>
                </li>
                <li class="hover:text-gray-700 transition-colors duration-200">
                    <a href="#"><i class="ri-twitter-x-line"></i></a>
                </li>
                <li class="hover:text-red-700 transition-colors duration-200">
                    <a href="#"><i class="ri-youtube-fill"></i></a>
                </li>
            </ul>
            <div class="flex gap-6 md:gap-10 items-center">
                <div class="inline-flex items-center gap-3">

                    <?php
                    // Check if the customer is logged in
                    if (isset($id) && !empty($id)) {
                        // Initialize total cost and total item count
                        $total_cost = 0;
                        $total_items = 0;

                        // Select all items from the cart for the current customer
                        $cart_select = "SELECT * FROM carttb WHERE CustomerID = '$id'";
                        $cart_query = mysqli_query($connect, $cart_select);

                        while ($array = mysqli_fetch_array($cart_query)) {
                            $cartId = $array['CartID'];
                            $productId = $array['ProductID'];
                            $productSize = $array['Size'];
                            $productQuantity = $array['Quantity'];

                            // Fetch the product price and discount price
                            $product_select = "SELECT Price, DiscountPrice FROM producttb WHERE ProductID = '$productId'";
                            $product_query = mysqli_query($connect, $product_select);
                            if ($product_row = mysqli_fetch_array($product_query)) {
                                $productPrice = $product_row['Price'];
                                $productDiscountPrice = $product_row['DiscountPrice'];

                                // Determine the effective price
                                $effectivePrice = ($productDiscountPrice != 0) ? $productDiscountPrice : $productPrice;

                                // Calculate item total
                                $item_total = $effectivePrice * $productQuantity;

                                // Add to total cost
                                $total_cost += $item_total;

                                // Add to total item count
                                $total_items += $productQuantity;
                            }
                        }
                    ?>
                        <!-- Display the total cost -->
                        <p class="text-lg font-semibold">$<?php echo number_format($total_cost, 2); ?></p>
                        <!-- Display the shopping cart icon and total item count -->
                        <div class="relative cursor-pointer group">
                            <i id="shoppingCart" class="ri-shopping-bag-line text-2xl group-hover:text-indigo-500 transition-colors duration-200"></i>
                            <p class="absolute -top-2 -right-3 bg-black text-white text-sm rounded-full w-5 h-5 inline-flex items-center justify-center group-hover:bg-indigo-400 transition-colors duration-200 ease-in"><?php echo $total_items ?></p>
                        </div>
                    <?php
                    } else {
                    ?>
                        <!-- If customer is not logged in, show default values -->
                        <p class="text-lg font-semibold">$0.00</p>
                        <div class="relative cursor-pointer group">
                            <i id="shoppingCart" class="ri-shopping-bag-line text-2xl group-hover:text-indigo-500 transition-colors duration-200"></i>
                            <p class="absolute -top-2 -right-3 bg-black text-white text-sm rounded-full w-5 h-5 inline-flex items-center justify-center group-hover:bg-indigo-400 transition-colors duration-200 ease-in">0</p>
                        </div>
                    <?php
                    }
                    ?>

                </div>

                <div id="profile" class="relative group cursor-pointer">
                    <i class="ri-user-line flex items-center text-2xl group-hover:text-indigo-400 transition-colors duration-200 ease-in"></i>
                </div>
            </div>
        </div>
    </nav>
</header>

<div id="darkOverlay3" class="fixed inset-0 bg-black bg-opacity-50 z-40 hidden"></div>

<!-- Aside nav display -->
<div id="nav" class="fixed top-0 -left-full flex flex-col bg-white w-[260px] sm:w-[300px] h-full p-4 z-50 transition-all duration-500 ease-in-out">
    <div class="mb-4 flex justify-between">
        <div class="flex items-center justify-center">
            <i id="navClose" class="ri-close-line text-3xl cursor-pointer border-2 rounded hover:border-indigo-300 transition-colors duration-300"></i>
        </div>
        <a class="flex sm:hidden text-3xl text-indigo-400 font-semibold" href="Home.php"><img class="w-24" src="Images/Screenshot 2024-08-18 112444.png" alt="Logo"></a>
    </div>
    <ul class="flex flex-col pt-2 border-t select-none  transition-all duration-1000 ease-in-out">
        <div id="shopCategory" class="overflow-hidden h-10 transition-all duration-500 ease-in-out">
            <div class="flex items-center gap-4 p-1 cursor-pointer">
                <i class="ri-shopping-bag-3-line text-2xl"></i>
                <div class="flex items-center">
                    Shop By Category
                    <i class="ri-arrow-right-s-line rotate-90"></i>
                </div>
            </div>
            <div id="category">
                <ul class="max-w-[200px] mx-auto select-none">
                    <a href="Accessories.php" class="flex items-center justify-between p-1 cursor-pointer">
                        <p>Accessories</p>
                        <img class="w-16" src="Images/205949368-2.webp" alt="image">
                    </a>
                    <a href="Bottoms.php" class="flex items-center justify-between p-1 cursor-pointer">
                        <p>Bottoms</p>
                        <img class="w-16" src="images/206035112-2.webp" alt="Image">
                    </a>
                    <a href="Shirts.php" class="flex items-center justify-between p-1 cursor-pointer">
                        <p>Shirts</p>
                        <img class="w-16" src="images/205901690-2.webp" alt="Image">
                    </a>
                    <a href="JacketsAndCoats.php" class="flex items-center justify-between p-1 cursor-pointer">
                        <p>Jackets & Coats</p>
                        <img class="w-16" src="images/205792802-2.webp" alt="Image">
                    </a>
                    <a href="Shoes.php" class="flex items-center justify-between p-1 cursor-pointer">
                        <p>Shoes</p>
                        <img class="w-16" src="images/206740153-2.webp" alt="Image">
                    </a>
                </ul>
            </div>
        </div>
        <div class="flex items-center gap-4 p-1 cursor-pointer">
            <i class="ri-truck-line text-2xl"></i>
            <a href="NewArrivals.php">New Arrivals</a>
        </div>
        <div class="flex items-center gap-4 p-1 cursor-pointer">
            <i class="ri-box-3-line text-2xl"></i>
            <a href="Collections.php">Collections</a>
        </div>
    </ul>
</div>

<div id="darkOverlay" class="fixed inset-0 bg-black bg-opacity-50 z-40 hidden"></div>

<!-- Aside Shopping cart -->
<aside id="aside" class="fixed top-0 -right-full flex flex-col justify-between bg-white w-full md:w-[550px] h-full p-4 z-50 transition-all duration-500 ease-in-out">
    <div class="flex justify-between pb-3">
        <h1 class="text-2xl text-indigo-400 font-semibold">Shopping Cart</h1>
        <i id="closeBtn" class="ri-close-line text-3xl cursor-pointer border-2 rounded hover:border-indigo-300 transition-colors duration-300"></i>
    </div>

    <?php
    if (isset($id) && !empty($id)) {
        $cart_select = "SELECT * FROM carttb WHERE CustomerID = '$id'";
        $cart_query = mysqli_query($connect, $cart_select);
        $cart_query_count = mysqli_num_rows($cart_query);

        if ($cart_query_count > 0) {
            $total_cost = 0;
    ?>

            <!-- Display products in a styled list -->
            <div class="shoppingCart flex-1 overflow-y-auto p-4 bg-white">
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
                    }
                ?>
                    <div class="border-b py-4 hover:bg-gray-50 flex">
                        <div class="w-28 flex-shrink-0">
                            <img class="w-full h-full object-cover" src="<?php echo $productImg1; ?>" alt="Product Image">
                        </div>
                        <div class="ml-4 flex-grow">
                            <h3 class="text-md font-semibold text-gray-900"><?php echo $productTitle; ?></h3>
                            <p class="text-sm text-gray-400">Price: <span class="text-black">$<?php echo number_format($effectivePrice, 2); ?></span></p>
                            <p class="text-sm text-gray-400">Size: <span class="text-black"><?php echo $displaySize; ?></span></p>
                            <p class="text-sm text-gray-400">Quantity: <span class="text-black"><?php echo $productQuantity; ?></span></p>
                            <div class="flex items-center mt-2">
                                <form action="<?php $_SERVER["PHP_SELF"] ?>" method="post" class="flex items-center">
                                    <input type="hidden" name="cart_id" value="<?php echo $cartId; ?>">
                                    <input type="number" name="new_quantity" value="<?php echo $productQuantity; ?>" min="1" max="20" class="w-16 px-2 py-1 border border-gray-300 rounded-md text-gray-700 focus:outline-none focus:border-indigo-500 mr-2">
                                    <button type="submit" name="updateBtn" class="text-blue-500 hover:text-blue-700 ml-2">
                                        <i class="ri-edit-circle-line text-lg"></i>
                                    </button>
                                    <button type="submit" name="deleteBtn" class="text-red-500 hover:text-red-700 ml-2">
                                        <i class="ri-delete-bin-6-line text-lg"></i>
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                <?php } ?>
            </div>

            <!-- Display total cost -->
            <div class="mt-4 flex justify-between">
                <div class="flex items-center group select-none">
                    <a href="AddToCart.php">Show all</a>
                    <i class="ri-arrow-right-line group-hover:translate-x-1 transition-all duration-300"></i>
                </div>
                <p class="text-lg font-semibold">Total: $<?php echo number_format($total_cost, 2); ?></p>
            </div>

        <?php } else { ?>
            <div class="flex flex-col justify-center items-center">
                <img class="w-80 select-none" src="Images/v872-nunny-07-removebg.png" alt="No Product">
                <p class="text-center text-base sm:text-lg text-gray-400">No products in the cart.</p>
            </div>
        <?php }
    } else {
        ?>
        <div class="flex flex-col justify-center items-center">
            <img class="w-80 select-none" src="Images/v872-nunny-07-removebg.png" alt="No Product">
            <p class="text-center text-base sm:text-lg text-gray-400">No products in the cart.</p>
        </div>
    <?php
    }
    ?>

    <div class="flex flex-col md:flex-row justify-center gap-1 mt-4">

        <!-- Check if customer is signed in or not -->
        <?php
        if (isset($id)) { ?>

            <!-- Check if there are products in the cart -->
            <?php if ($cart_query_count > 0) { ?>
                <a class="border w-full border-indigo-400 p-2 text-center text-indigo-400 text-lg font-semibold select-none hover:text-indigo-500 transition-all duration-300" href="Collections.php">Continue Shopping</a>
            <?php } else { ?>
                <a class="border w-full border-indigo-400 p-2 text-center text-indigo-400 text-lg font-semibold select-none hover:text-indigo-500 transition-all duration-300" href="Collections.php">Start Shopping</a>
            <?php } ?>
        <?php } else { ?>
            <a class="border w-full border-indigo-400 p-2 text-center text-indigo-400 text-lg font-semibold select-none hover:text-indigo-500 transition-all duration-300" href="Collections.php">Start Shopping</a>
        <?php } ?>

        <?php if (isset($id)) { ?>

            <!-- Check if there are products in the cart -->
            <?php if ($cart_query_count > 0) { ?>
                <a href="Checkout.php" class="border w-full border-green-600 bg-green-600 text-white p-2 text-center text-lg font-semibold select-none hover:bg-green-700 transition-all duration-300">CheckOut</a>
            <?php } ?>
        <?php } ?>

    </div>
</aside>

<!-- Alert Box -->
<?php if (!empty($alert_message)) : ?>
    <div id="alertBox" class="alert-box fixed -top-4 left-1/2 transform -translate-x-1/2 z-50 opacity-0 transition-all duration-300 ease-in-out w-auto max-w-[90%] p-4">
        <div class="relative bg-white rounded-b-[30px] p-3 flex justify-center items-center border-2 border-gray-300">
            <div class="flex items-center justify-center">
                <div class="mr-3">
                    <?php
                    if ($id) {
                    ?>
                        <i class="ri-checkbox-circle-fill text-3xl text-green-400"></i>
                    <?php
                    } else {
                    ?>
                        <i class="ri-error-warning-line text-3xl text-red-400"></i>
                    <?php
                    }
                    ?>
                </div>
                <div class="text-md sm:text-lg font-semibold text-gray-400">
                    <?php echo $alert_message; ?>
                </div>
            </div>
        </div>
    </div>
<?php endif; ?>

<div id="darkOverlay2" class="fixed inset-0 bg-black bg-opacity-50 z-40 hidden"></div>

<!-- Profile box -->
<div id="profileBox" class="fixed top-0 -right-[100em] flex flex-col justify-between bg-white w-[300px] sm:w-[300px] h-full p-4 z-50 transition-all duration-500 ease-in-out">
    <div>
        <div class="pb-4">
            <i id="profileCloseBtn" class="ri-close-line text-3xl cursor-pointer border-2 rounded hover:border-indigo-300 transition-colors duration-300"></i>
        </div>

        <?php
        $customerSelect = "SELECT * FROM customertb
                WHERE CustomerID = '$id'";

        $customerSelectQuery = mysqli_query($connect, $customerSelect);
        $count = mysqli_num_rows($customerSelectQuery);

        for ($i = 0; $i < $count; $i++) {
            $array = mysqli_fetch_array($customerSelectQuery);
            $username = $array['UserName'];
            $customer_email = $array['CustomerEmail'];
        }

        // Check if cutomer singin or not
        if (isset($id)) { ?>
            <div class="flex items-center gap-2 pb-4">
                <div class="max-w-[300px]">
                    <span class="text-3xl font-semibold">Hello,</span>
                    <span class="font-semibold"><?php echo $username; ?></span>
                    <p class="text-slate-400 text-xs"><?php echo $customer_email; ?></p>
                </div>
            </div>
        <?php
        }
        ?>

        <div class="flex flex-col pt-2 border-t">

            <!-- Check if cutomer singin or not -->
            <?php if (isset($id)) { ?>
                <a href="CusProfile.php" id="profileEditBtn" class="flex items-center gap-4 p-1 rounded hover:bg-slate-100">
                    <i class="ri-profile-line text-2xl"></i>
                    <p>Profile Edit</p>
                </a>
            <?php
            } ?>

            <!-- Check if cutomer singin or not -->
            <?php if (isset($id)) { ?>
                <a href="SignUp.php" class="flex items-center gap-4 p-1 rounded hover:bg-slate-100">
                    <i class="ri-account-box-line text-2xl"></i>
                    <p>Add another account</p>
                </a>
            <?php
            } else {
            ?>
                <a href="SignUp.php" class="flex items-center gap-4 p-1 rounded hover:bg-slate-100">
                    <i class="ri-account-box-line text-2xl"></i>
                    <p>Sign up an account</p>
                </a>
            <?php
            }
            ?>

            <a href="Favorite.php" class="flex items-center gap-4 p-1 rounded hover:bg-slate-100">
                <i class="ri-heart-3-line text-2xl"></i>
                <p>Saved items</p>
            </a>
            <a href="OrderHistory.php" class="flex items-center gap-4 p-1 rounded hover:bg-slate-100">
                <i class="ri-file-list-2-line text-2xl"></i>
                <p>Order History</p>
            </a>
        </div>
    </div>
    <a href="SignOut.php" class="flex items-center gap-4 p-1 rounded hover:bg-slate-100">
        <i class="ri-logout-circle-line text-2xl"></i>
        <p class="font-semibold">Sign out</p>
    </a>
</div>