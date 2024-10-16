<?php
session_start();
include('DbConnection.php');

// Check if PurchaseID is provided in the URL
if (isset($_GET["PurchaseID"])) {
    $purchase_id = $_GET["PurchaseID"];

    // Fetch Purchase Details
    $purchase_query = "SELECT p.*, a.AdminFullName, s.SupplierName 
                       FROM purchasetb p
                       JOIN admintb a ON p.AdminID = a.AdminID
                       JOIN suppliertb s ON p.SupplierID = s.SupplierID
                       WHERE p.PurchaseID = '$purchase_id'";

    $purchase_result = mysqli_query($connect, $purchase_query);
    $purchase = mysqli_fetch_assoc($purchase_result);

    // Fetch Purchase Product Details
    $purchasedetail_query = "SELECT pd.*, pr.Title, pr.Brand
                             FROM purchasedetailtb pd
                             JOIN producttb pr ON pd.ProductID = pr.ProductID
                             WHERE pd.PurchaseID = '$purchase_id'";

    $purchasedetail_result = mysqli_query($connect, $purchasedetail_query);
} else {
    echo "No Purchase ID provided.";
    exit();
}

// Update status if form is submitted
if (isset($_POST['update_status'])) {

    // Update the status in purchasetb
    $update_query = "UPDATE purchasetb SET Status = 'Confirmed' WHERE PurchaseID = '$purchase_id'";
    mysqli_query($connect, $update_query);

    echo "<script>window.alert('Purchase has been confirmed successfully!')</script>";
    echo "<script>window.location = 'PurchaseHistory.php'</script>";
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
                <h1 class="text-4xl font-semibold text-indigo-400">Purchase Detail</h1>
            </header>

            <?php if ($purchase): ?>
                <div class="bg-white p-8 shadow-lg rounded-lg">
                    <div class="py-2 mb-2 border-b-2">
                        <h2 class="text-2xl font-bold">Purchase ID: <?php echo $purchase['PurchaseID']; ?></h2>
                        <p class="text-gray-500 text-sm">VELIRO Men's Clothing</p>
                    </div>
                    <p><strong>Staff:</strong> <?php echo $purchase['AdminFullName']; ?></p>
                    <p><strong>Supplier:</strong> <?php echo $purchase['SupplierName']; ?></p>
                    <p><strong>Purchase Tax:</strong> $<?php echo number_format($purchase['PurchaseTax'], 2); ?></p>
                    <p><strong>Total Amount:</strong> $<?php echo number_format($purchase['TotalAmount'], 2); ?></p>
                    <p><strong>Status:</strong> <?php echo $purchase['Status']; ?><i class="pl-1 <?php echo $purchase['Status'] === 'Confirmed' ? 'ri-checkbox-circle-line text-green-500' : 'ri-indeterminate-circle-line text-red-500'; ?>"></i></p>

                    <p><strong>Purchase Date:</strong> <?php echo $purchase['PurchaseDate']; ?></p>

                    <h3 class="text-xl font-semibold mt-6 mb-4">Purchased Products</h3>
                    <table class="min-w-full bg-white border border-gray-200">
                        <thead>
                            <tr>
                                <th class="py-2 px-4 border-b border-gray-200 text-left">Product Name</th>
                                <th class="py-2 px-4 border-b border-gray-200 text-left">Brand</th>
                                <th class="py-2 px-4 border-b border-gray-200 text-left">Quantity</th>
                                <th class="py-2 px-4 border-b border-gray-200 text-left">Unit Price</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($detail = mysqli_fetch_assoc($purchasedetail_result)): ?>
                                <tr>
                                    <td class="py-2 px-4 border-b border-gray-200"><?php echo $detail['Title']; ?></td>
                                    <td class="py-2 px-4 border-b border-gray-200"><?php echo $detail['Brand']; ?></td>
                                    <td class="py-2 px-4 border-b border-gray-200"><?php echo $detail['PurchaseUnitQuantity']; ?></td>
                                    <td class="py-2 px-4 border-b border-gray-200">$<?php echo number_format($detail['PurchaseUnitPrice'], 2); ?></td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <p class="text-red-600">Purchase details not found!</p>
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