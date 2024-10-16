<?php
session_start();
include("dbconnection.php");

$supplier_id = $_GET['SupplierID'];

if (isset($_POST['confirmDelete'])) {
    $supplier_id = $_POST['supplierId'];

    // Step 1: Delete all purchase details associated with the purchases made by the supplier
    $deletePurchaseDetails = "DELETE pd FROM purchasedetailtb pd
                              JOIN purchasetb p ON pd.PurchaseID = p.PurchaseID
                              WHERE p.SupplierID = '$supplier_id'";
    $deletePurchaseDetailsQuery = mysqli_query($connect, $deletePurchaseDetails);

    if ($deletePurchaseDetailsQuery) {
        // Step 2: Delete all purchases associated with the supplier
        $deletePurchases = "DELETE FROM purchasetb WHERE SupplierID = '$supplier_id'";
        $deletePurchasesQuery = mysqli_query($connect, $deletePurchases);

        if ($deletePurchasesQuery) {
            // Step 3: Delete the supplier from suppliertb
            $supplierDelete = "DELETE FROM suppliertb WHERE SupplierID = '$supplier_id'";
            $supplierQuery = mysqli_query($connect, $supplierDelete);

            if ($supplierQuery) {
                echo "<script>window.alert('Supplier data has been successfully deleted.')</script>";
                echo "<script>window.location = 'AdminDashboard.php'</script>";
            } else {
                echo "<script>window.alert('Something went wrong.')</script>";
                echo "<script>window.location = 'AdminDashboard.php'</script>";
            }
        } else {
            echo "<script>window.alert('Failed to delete purchases associated with this Supplier.')</script>";
            echo "<script>window.location = 'AdminDashboard.php'</script>";
        }
    } else {
        echo "<script>window.alert('Failed to delete purchase details associated with this Supplier.')</script>";
        echo "<script>window.location = 'AdminDashboard.php'</script>";
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

<body class="bg-gray-100 flex items-center justify-center h-screen">
    <div class="bg-white p-6 rounded shadow-md w-full max-w-md">
        <h2 class="text-2xl font-semibold text-gray-800 mb-4">Delete Supplier</h2>
        <p class="mb-6">Are you sure you want to delete this supplier?</p>
        <form action="" method="post">
            <input type="hidden" name="supplierId" value="<?php echo htmlspecialchars($supplier_id); ?>">
            <div class="flex justify-end space-x-4">
                <a href="AdminDashboard.php" class="px-4 py-2 bg-gray-200 text-gray-700 rounded hover:bg-gray-300 transition duration-200">Cancel</a>
                <button type="submit" name="confirmDelete" class="px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700 transition duration-200">Delete</button>
            </div>
        </form>
    </div>
</body>

</html>