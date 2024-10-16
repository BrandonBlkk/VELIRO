<?php
session_start();
include('DbConnection.php');

// Check if OrderID is provided in the URL
if (isset($_GET["OrderID"])) {
    $order_id = $_GET["OrderID"];

    // Fetch Order Details
    $order_query = "SELECT o.*, c.FullName 
                    FROM ordertb o
                    JOIN customertb c ON o.CustomerID = c.CustomerID
                    WHERE o.OrderID = '$order_id'";

    $order_result = mysqli_query($connect, $order_query);
    $order = mysqli_fetch_assoc($order_result);

    // Fetch Order Product Details
    $orderdetail_query = "SELECT od.*, pr.Title, pr.Brand
                          FROM orderdetailtb od
                          JOIN producttb pr ON od.ProductID = pr.ProductID
                          WHERE od.OrderID = '$order_id'";

    $orderdetail_result = mysqli_query($connect, $orderdetail_query);
} else {
    echo "No Order ID provided.";
    exit();
}

// Update status if form is submitted
if (isset($_POST['update_status'])) {

    // Update the status in ordertb
    $update_query = "UPDATE ordertb SET Status = 'Delivered' WHERE OrderID = '$order_id'";
    mysqli_query($connect, $update_query);
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
    <link rel="stylesheet" href="AdminStyle.css?v=<?php echo time(); ?>">
</head>

<body>
    <div class="flex h-screen">

        <?php
        include('./components/AdminNav.php');
        ?>

        <section class="ml-0 md:ml-[250px] flex-1 p-8 overflow-x-auto">
            <header class="mb-6">
                <h1 class="text-4xl font-semibold text-indigo-400">Order Detail</h1>
            </header>

            <?php if ($order): ?>
                <div class="bg-white p-8 shadow-lg rounded-lg">
                    <div class=" mb-4 border-b-2">
                        <h2 class="text-2xl font-bold text-gray-800">Order ID: <?php echo $order['OrderID']; ?></h2>
                        <p class="text-sm text-gray-500">VELIRO Men's Clothing</p>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-2">
                        <p><strong>Customer:</strong> <?php echo $order['FullName']; ?></p>
                        <p><strong>Phone:</strong> <?php echo $order['CustomerPhone']; ?></p>
                        <p><strong>Shipping Address:</strong> <?php echo $order['ShippingAddress']; ?></p>
                        <p><strong>City:</strong> <?php echo $order['City']; ?></p>
                        <p><strong>State:</strong> <?php echo $order['State']; ?></p>
                        <p><strong>Total Amount:</strong> $<?php echo number_format($order['TotalAmount'], 2); ?></p>
                        <p><strong>Order Tax:</strong> $<?php echo number_format($order['OrderTax'], 2); ?></p>
                        <p class="flex items-center"><strong>Status:</strong> <?php echo $order['Status']; ?>
                            <i class="pl-2 <?php echo $order['Status'] === 'Delivered' ? 'ri-checkbox-circle-line text-green-500' : 'ri-indeterminate-circle-line text-red-500'; ?>"></i>
                        </p>
                        <p><strong>Order Date:</strong> <?php echo date("F j, Y", strtotime($order['OrderDate'])); ?></p>
                        <p><strong>Payment Method:</strong> <?php echo $order['PaymentMethod']; ?></p>
                    </div>

                    <h3 class="text-xl font-semibold mt-6 mb-4">Ordered Products</h3>
                    <div class="overflow-x-auto">
                        <table class="min-w-full bg-white border border-gray-200 rounded-lg shadow">
                            <thead class="bg-gray-100">
                                <tr>
                                    <th class="py-3 px-4 border-b border-gray-200 text-left font-medium text-gray-600">Product</th>
                                    <th class="py-3 px-4 border-b border-gray-200 text-left font-medium text-gray-600">Brand</th>
                                    <th class="py-3 px-4 border-b border-gray-200 text-left font-medium text-gray-600">Quantity</th>
                                    <th class="py-3 px-4 border-b border-gray-200 text-left font-medium text-gray-600">Unit Price</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200">
                                <?php while ($detail = mysqli_fetch_assoc($orderdetail_result)): ?>
                                    <tr class="hover:bg-gray-50">
                                        <td class="py-2 px-4 text-gray-800"><?php echo $detail['Title']; ?></td>
                                        <td class="py-2 px-4 text-gray-800"><?php echo $detail['Brand']; ?></td>
                                        <td class="py-2 px-4 text-gray-800"><?php echo $detail['OrderUnitQuantity']; ?></td>
                                        <td class="py-2 px-4 text-gray-800">$<?php echo number_format($detail['PurchaseUnitPrice'], 2); ?></td>
                                    </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                        <div class="mt-6 bg-gray-50 p-3 border-l-4 border-indigo-400 rounded">
                            <h3 class="text-lg font-semibold text-gray-700">Remarks</h3>
                            <p class="text-gray-600 text-sm"><?php echo $order['Remark']; ?></p>
                        </div>
                    </div>
                </div>
            <?php else: ?>
                <p class="text-red-600">Order details not found!</p>
            <?php endif; ?>

            <div class="flex items-center gap-3 mt-6">
                <!-- Form to Update Status -->
                <form method="POST">
                    <button type="submit" name="update_status" class="px-4 py-2 bg-green-500 text-white rounded hover:bg-green-700 transition duration-200">Confirm</button>
                </form>
                <button onclick="window.print()" class="px-4 py-2 bg-indigo-500 text-white rounded hover:bg-indigo-700 transition duration-200">Get Voucher</button>
            </div>
        </section>

    </div>
</body>

</html>