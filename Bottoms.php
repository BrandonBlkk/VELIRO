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
        <div class="max-w-[1300px] mx-auto mb-5 px-4 text-sm text-gray-500">
            <a class="hover:text-gray-700" href="Home.php">Home</a>
            <i class="ri-arrow-drop-right-line"></i>
            <a href="Bottoms.php">Bottoms</a>
        </div>
        <div class="flex flex-col md:flex-row gap-5 justify-between max-w-[1300px] mx-auto mb-10 px-4">
            <h1 class="text-3xl">Men's Bottoms</h1>
            <div class="flex gap-2 flex-wrap">
                <div>
                    <form method="GET" class="flex items-center">
                        <label for="sort" class="mr-2 font-semibold">Filter by:</label>
                        <select name="sort" id="sort" class="border p-2 rounded" onchange="this.form.submit()">
                            <option value="random" <?php if (isset($_GET['sort']) && $_GET['sort'] == 'random') echo 'selected'; ?>>All Products</option>
                            <option value="newest" <?php if (isset($_GET['sort']) && $_GET['sort'] == 'newest') echo 'selected'; ?>>Newest First</option>
                            <option value="oldest" <?php if (isset($_GET['sort']) && $_GET['sort'] == 'oldest') echo 'selected'; ?>>Oldest First</option>
                            <option value="lowest" <?php if (isset($_GET['sort']) && $_GET['sort'] == 'lowest') echo 'selected'; ?>>Lowest Price</option>
                            <option value="highest" <?php if (isset($_GET['sort']) && $_GET['sort'] == 'highest') echo 'selected'; ?>>Highest Price</option>
                            <option value="discount" <?php if (isset($_GET['sort']) && $_GET['sort'] == 'discount') echo 'selected'; ?>>Discount</option>
                            <option value="extended_sizing" <?php if (isset($_GET['sort']) && $_GET['sort'] == 'extended_sizing') echo 'selected'; ?>>Extended Sizing</option>
                            <option value="selling_fast" <?php if (isset($_GET['sort']) && $_GET['sort'] == 'selling_fast') echo 'selected'; ?>>Selling Fast</option>
                            <option value="more_colors" <?php if (isset($_GET['sort']) && $_GET['sort'] == 'more_colors') echo 'selected'; ?>>More Colors</option>
                        </select>
                    </form>
                </div>
                <div>
                    <form method="GET" class="flex items-center">
                        <label for="color" class="mr-2 font-semibold">Filter by Color:</label>
                        <?php
                        $color_query = "SELECT DISTINCT Color FROM producttb WHERE Color IS NOT NULL AND Color != ''";
                        $color_result = mysqli_query($connect, $color_query);
                        if (!$color_result) {
                            die('Query failed: ' . mysqli_error($connect));
                        }
                        ?>
                        <select name="color" id="color" class="border p-2 rounded" onchange="this.form.submit()">
                            <option value="" <?php if (isset($_GET['color']) && $_GET['color'] == '') echo 'selected'; ?>>All Colors</option>
                            <?php
                            while ($row = mysqli_fetch_assoc($color_result)) {
                                $color = $row['Color'];
                                $selected = isset($_GET['color']) && $_GET['color'] == $color ? 'selected' : '';
                                echo "<option value=\"$color\" $selected>$color</option>";
                            }
                            ?>
                        </select>
                    </form>
                </div>
            </div>
        </div>

        <?php
        $sort_option = isset($_GET['sort']) ? $_GET['sort'] : 'random';
        $color_option = isset($_GET['color']) ? $_GET['color'] : '';

        $product_select = "SELECT * FROM producttb WHERE ProductTypeID = '4'";
        if ($color_option != '') {
            $product_select .= " AND Color = '$color_option'";
        }

        $sort_clause = "";
        switch ($sort_option) {
            case 'newest':
                $sort_clause = " ORDER BY AddedDate DESC";
                break;
            case 'oldest':
                $sort_clause = " ORDER BY AddedDate ASC";
                break;
            case 'lowest':
                $sort_clause = " ORDER BY Price ASC";
                break;
            case 'highest':
                $sort_clause = " ORDER BY Price DESC";
                break;
            case 'discount':
                $sort_clause .= " AND DiscountPrice > 0";
                break;
            case 'extended_sizing':
                $product_select .= " AND ExtendedSizing = 'true'";
                break;
            case 'selling_fast':
                $product_select .= " AND SellingFast = 'true'";
                break;
            case 'more_colors':
                $product_select .= " AND MoreColors = 'true'";
                break;
            default:
                $sort_clause = " ORDER BY RAND()";
                break;
        }

        if ($sort_clause) {
            $product_select .= $sort_clause;
        }

        $select_query = mysqli_query($connect, $product_select);
        $count = mysqli_num_rows($select_query);
        ?>

        <div class="max-w-[1400px] mx-auto px-4">
            <?php if ($count > 0) { ?>
                <div class="grid gap-4 grid-cols-2 md:grid-cols-3 lg:grid-cols-4">
                    <?php
                    $display_limit = 16;
                    for ($i = 0; $i < $count; $i++) {
                        $array = mysqli_fetch_array($select_query);
                        $product_id = $array['ProductID'];
                        $title = $array['Title'];
                        $img1 = $array['img1'];
                        $img2 = $array['img2'];
                        $price = $array['Price'];
                        $discount_price = $array['DiscountPrice'];
                        $extended_sizing = $array['ExtendedSizing'];
                        $more_colors = $array['MoreColors'];
                        $selling_fast = $array['SellingFast'];

                        // Hide products beyond the initial limit
                        $hidden_class = $i >= $display_limit ? 'hidden' : '';

                        echo "<div class='product-item $hidden_class' data-index='$i'>";
                        include('./components/ProductForm.php');
                        echo "</div>";
                    } ?>
                </div>
                <?php if ($count > $display_limit) { ?>
                    <div class="flex justify-center mt-5 select-none">
                        <button id="see-more-btn" class="border-2 border-indigo-500 text-xl font-semibold px-24 py-2">See More</button>
                    </div>
                <?php } ?>
            <?php } else { ?>
                <div class="mt-10 py-36 flex justify-center text-center text-gray-400 text-lg">
                    <p>There are no available products.</p>
                </div>
            <?php } ?>
        </div>

        <?php if ($count > 0) {
        ?>
            <div class="max-w-[200px] mx-auto">
                <p id="viewed-count" class="text-center text-sm text-gray-500 mt-10 pb-1">You've viewed <?php echo min($count, $display_limit); ?> of <?php echo $count; ?> products</p>
                <div class="bg-gray-300 w-full h-[2px]">
                    <p id="progress-bar" class="h-[2px] bg-indigo-400" style="width: <?php echo min(100, ($display_limit / $count) * 100); ?>%"></p>
                </div>
            </div>
        <?php
        } else {
        ?>
            <div class="max-w-[200px] mx-auto">
                <p id="viewed-count" class="text-center text-sm text-gray-500 mt-10 pb-1">You've viewed <?php echo $count; ?> of <?php echo $count; ?> products</p>
                <div class="bg-gray-300 w-full h-[2px]">
                    <p id="progress-bar" class="h-[2px] bg-indigo-400" style="width: <?php echo $count ?>"></p>
                </div>
            </div>
        <?php
        }
        ?>
    </section>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const seeMoreBtn = document.getElementById('see-more-btn');
            const productItems = document.querySelectorAll('.product-item');
            const viewedCount = document.getElementById('viewed-count');
            const progressBar = document.getElementById('progress-bar');
            let itemsToShow = 16;
            const totalItems = productItems.length;

            seeMoreBtn.addEventListener('click', function() {
                const hiddenItems = Array.from(productItems).filter(item => item.classList.contains('hidden'));
                hiddenItems.slice(0, 4).forEach(item => item.classList.remove('hidden'));

                itemsToShow += 16;
                if (itemsToShow >= totalItems) {
                    seeMoreBtn.style.display = 'none';
                }

                // Update the viewed count and progress bar
                viewedCount.textContent = `You've viewed ${Math.min(itemsToShow, totalItems)} of ${totalItems} products`;
                progressBar.style.width = `${(Math.min(itemsToShow, totalItems) / totalItems) * 100}%`;
            });
        });
    </script>

    <?php
    include('./components/Newsletter.php')
    ?>

    <?php
    include('./components/Footer.php');
    ?>

    <script src="Customer.js"></script>
</body>

</html>