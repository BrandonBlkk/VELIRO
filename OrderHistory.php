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

// Initialize search query
$SearchQuery = "";

// Handle Order Status Search
if (isset($_GET['search'])) {
    $searchTerm = mysqli_real_escape_string($connect, $_GET['search']);
    if ($searchTerm == 'pending' || $searchTerm == 'delivered') {
        $SearchQuery = "AND Status LIKE '$searchTerm'";
    }
}

// Handle Date Filter
if (isset($_GET['from_date']) && isset($_GET['to_date'])) {
    $fromDate = mysqli_real_escape_string($connect, $_GET['from_date']);
    $toDate = mysqli_real_escape_string($connect, $_GET['to_date']);

    if (!empty($fromDate) && !empty($toDate)) {
        if (!empty($SearchQuery)) {
            $SearchQuery .= " AND OrderDate BETWEEN '$fromDate' AND '$toDate'";
        } else {
            $SearchQuery = "AND OrderDate BETWEEN '$fromDate' AND '$toDate'";
        }
    }
}

// Fetch order history for the customer
$order_query = "SELECT * FROM ordertb WHERE CustomerID = '$id'" . $SearchQuery;
$order_result = mysqli_query($connect, $order_query);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order History - VELIRO Men's Clothing</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/remixicon/3.5.0/remixicon.css" integrity="sha512-HXXR0l2yMwHDrDyxJbrMD9eLvPe3z3qL3PPeozNTsiHJEENxx8DH2CxmV05iwG0dwoz5n4gQZQyYLUNt1Wdgfg==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="output.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="style.css?v=<?php echo time(); ?>">
</head>

<body class="bg-gray-50">

    <?php include('./components/Header.php'); ?>

    <!-- Order History Section -->
    <section class="max-w-[1370px] mx-auto px-4 py-6">
        <div>
            <h1 class="text-2xl mb-4">Order History</h1>
        </div>
        <form method="GET" class="my-4 flex flex-wrap flex-col sm:flex-row gap-1">
            <!-- Filter by Status -->
            <div class="flex items-center">
                <label for="search" class="sm:ml-4 sm:mr-2 font-semibold">Filter by:</label>
                <select name="search" id="search" class="border p-2 rounded" onchange="this.form.submit()">
                    <option value="">All Orders</option>
                    <option value="pending" <?php if (isset($_GET['search']) && $_GET['search'] == 'pending') echo 'selected'; ?>>Pending</option>
                    <option value="delivered" <?php if (isset($_GET['search']) && $_GET['search'] == 'delivered') echo 'selected'; ?>>Delivered</option>
                </select>
            </div>

            <!-- Date Filter: From -->
            <div class="w-full sm:w-auto flex flex-col sm:flex-row items-start sm:items-center">
                <label for="from_date" class="mb-2 sm:mb-0 sm:ml-4 sm:mr-2 font-semibold">From:</label>
                <input type="date" name="from_date" id="from_date" value="<?php echo isset($_GET['from_date']) ? $_GET['from_date'] : ''; ?>" class="border p-2 rounded w-full sm:w-auto">
            </div>

            <!-- Date Filter: To -->
            <div class="w-full sm:w-auto flex flex-col sm:flex-row items-start sm:items-center">
                <label for="to_date" class="mb-2 sm:mb-0 sm:ml-4 sm:mr-2 font-semibold">To:</label>
                <input type="date" name="to_date" id="to_date" value="<?php echo isset($_GET['to_date']) ? $_GET['to_date'] : ''; ?>" class="border p-2 rounded w-full sm:w-auto">
            </div>

            <!-- Search Button -->
            <div class="w-full sm:w-auto flex justify-center sm:justify-start sm:ml-4">
                <button type="submit" class="w-full sm:w-auto bg-indigo-500 text-white px-4 py-2 rounded hover:bg-indigo-600">
                    Search
                </button>
            </div>
        </form>

        <?php if (mysqli_num_rows($order_result) > 0) { ?>
            <div class="overflow-x-auto bg-white">
                <table class="min-w-full bg-white border border-gray-200">
                    <thead>
                        <tr class="bg-gray-100 text-gray-600 uppercase text-xs tracking-wider">
                            <th class="py-4 px-6 text-left font-medium">Order ID</th>
                            <th class="py-4 px-6 text-left font-medium">Tax</th>
                            <th class="py-4 px-6 text-left font-medium">Total Amount</th>
                            <th class="py-4 px-6 text-left font-medium">Status</th>
                            <th class="py-4 px-6 text-left font-medium">Order Date</th>
                            <th class="py-4 px-6 text-center font-medium">Action</th>
                        </tr>
                    </thead>
                    <tbody class="text-gray-600 text-sm font-light">
                        <?php while ($order = mysqli_fetch_array($order_result)) { ?>
                            <tr class="border-b border-gray-200 hover:bg-gray-50 transition">
                                <td class="py-4 px-6 flex items-center gap-3">
                                    <i class="ri-file-list-2-line text-blue-500"></i>
                                    <span><?php echo $order['OrderID']; ?></span>
                                </td>
                                <td class="py-4 px-6">$<?php echo number_format($order['OrderTax'], 2); ?></td>
                                <td class="py-4 px-6">$<?php echo number_format($order['TotalAmount'], 2); ?></td>
                                <td class="py-4 px-6">
                                    <span class="<?php echo ($order['Status'] === 'Delivered') ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800'; ?> px-2 py-1 rounded-full text-xs">
                                        <?php echo $order['Status']; ?>
                                    </span>
                                </td>
                                <td class="py-4 px-6"><?php echo date("F j, Y", strtotime($order['OrderDate'])); ?></td>
                                <td class="py-4 px-6 text-center">
                                    <a href="OrderHistoryDetails.php?OrderID=<?php echo $order['OrderID']; ?>" class="text-blue-600 hover:text-blue-800 flex items-center justify-center">
                                        <i class="ri-eye-line mr-1"></i>
                                        <p>View Details</p>
                                    </a>
                                </td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        <?php } else { ?>
            <!-- No Order History Message -->
            <div class="mt-10 py-20 flex flex-col justify-center items-center text-center text-gray-500 text-base sm:text-lg bg-white">
                <p>You have no order history yet. Start shopping to see your orders here!</p>
                <div class="mt-4 select-none">
                    <a href="Collections.php" class="block w-full border border-indigo-400 p-2 text-center text-indigo-400 select-none hover:text-indigo-500 transition-all duration-300">Start Shopping</a>
                </div>
            </div>
        <?php } ?>
    </section>

    <?php include('./components/Footer.php'); ?>

    <script src="Customer.js"></script>
</body>

</html>