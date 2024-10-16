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

// Check if CustomerID is set in session
if (isset($id) && !empty($_SESSION['CustomerID'])) {

    // Process form submission if favoriteBtn is set
    if (isset($_POST['favoriteBtn'])) {
        $product_id = $_POST['product_Id'];

        $check_query = "SELECT COUNT(*) as count FROM favoritetb WHERE CustomerID = '$id' AND ProductID = '$product_id'";
        $result = mysqli_query($connect, $check_query);
        $row = mysqli_fetch_assoc($result);
        $count = $row['count'];

        // If the product is not already in favorites, insert it
        if ($count == 0) {
            $insert_query = "INSERT INTO favoritetb (CustomerID, ProductID) VALUES ('$id', '$product_id')";
            mysqli_query($connect, $insert_query);
        }

        // Redirect to the same page to avoid form resubmission
        header("Location: " . $_SERVER['PHP_SELF']);
        exit;
    }
} else {
    if (isset($_POST['favoriteBtn'])) {
        // Product already in favorites alert
        $_SESSION['alert_message'] = "Please log in to add products to your favorite.";
        $_SESSION['alert_class'] = "bg-green-200 border-green-400 text-green-800";

        // Redirect to the same page to avoid form resubmission
        header("Location: " . $_SERVER['PHP_SELF']);
        exit;
    }
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

// Check for session alert message
$alert_message = isset($_SESSION['alert_message']) ? $_SESSION['alert_message'] : '';
$alert_class = isset($_SESSION['alert_class']) ? $_SESSION['alert_class'] : '';

// Clear the alert message from session after displaying it
if (!empty($alert_message)) {
    unset($_SESSION['alert_message']);
    unset($_SESSION['alert_class']);
}

// Initialize variables
$search_term = "";
$results = [];

if (isset($_GET['search'])) {
    $search_term = $_GET['search'];
    if (!empty($search_term)) {
        $query = "SELECT * FROM producttb WHERE Title LIKE '%$search_term%' OR Brand LIKE '%$search_term%'";
        $result = mysqli_query($connect, $query);
        $count = mysqli_num_rows($result);

        if ($count > 0) {
            while ($row = mysqli_fetch_array($result)) {
                $product_id = $row['ProductID'];
                $results[] = $row;
            }
        }
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

    <section class="my-10">

        <?php if (!empty($results)) : ?>
            <div class="max-w-[1400px] mx-auto px-4">
                <h1></h1>
                <div class="grid gap-4 grid-cols-2 md:grid-cols-3 lg:grid-cols-4">
                    <?php foreach ($results as $product) : ?>
                        <form action="<?php echo $_SERVER["PHP_SELF"]; ?>" method="post" class="relative group">
                            <input type="hidden" name="product_Id" value="<?php echo $product['ProductID']; ?>">

                            <a class="group" href="ProductsDetails.php?product_ID=<?php echo htmlspecialchars($product['ProductID']) ?>">
                                <div class="relative w-full mb-4 overflow-hidden select-none">
                                    <img class="w-full h-full object-cover transition-opacity duration-300 opacity-100 group-hover:opacity-0" src="<?php echo htmlspecialchars($product['img1']) ?>" alt="Image 1">
                                    <img class="w-full h-full object-cover absolute top-0 left-0 transition-opacity duration-300 opacity-0 group-hover:opacity-100" src="<?php echo htmlspecialchars($product['img2']) ?>" alt="Image 2">
                                    <p class="absolute bottom-0 bg-gradient-to-t from-indigo-300 text-white font-bold w-full p-2 <?php echo ($product['ExtendedSizing'] === 'true') ? 'flex' : 'hidden' ?>">EXTENDED SIZING</p>
                                </div>
                                <div>
                                    <h1 class="text-sm mb-2 truncate md:whitespace-normal"><?php echo htmlspecialchars($product['Title']) ?></h1>

                                    <?php
                                    if ($product['DiscountPrice'] == 0) {
                                    ?>
                                        <p class="text-sm font-bold mb-2">$<?php echo htmlspecialchars($product['Price']); ?></p>
                                    <?php
                                    } else {
                                    ?>
                                        <div class="mb-2">
                                            <span class="text-xs text-gray-500 line-through">$<?php echo htmlspecialchars($product['Price']); ?></span>
                                            <span class="font-bold text-sm text-red-500">$<?php echo htmlspecialchars($product['DiscountPrice']); ?></span>
                                        </div>
                                    <?php
                                    }
                                    ?>

                                    <span class="text-xs text-gray-500 font-bold border border-indigo-400 p-1 mb-1 select-none <?php echo ($product['MoreColors'] === 'false') ? 'hidden' : 'inline-block' ?>">MORE COLOURS</span>
                                    <span class="text-xs text-white bg-indigo-500 font-bold border p-1 select-none <?php echo ($product['SellingFast'] === 'false') ? 'hidden' : 'inline-block' ?>">SELLING FAST</span>
                                </div>
                            </a>

                            <?php
                            $check = "SELECT * FROM favoritetb
                            WHERE ProductID = '{$product['ProductID']}'
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
                    <?php endforeach; ?>
                </div>
            </div>
        <?php else : ?>
            <div class="mt-10 py-36 text-center text-gray-500 text-base sm:text-lg">
                <p>No products found matching your search criteria.</p>
            </div>
        <?php endif; ?>

    </section>

    <?php
    include('./components/Newsletter.php')
    ?>

    <?php
    include('./components/Footer.php')
    ?>

    <script src="Customer.js"></script>
</body>

</html>