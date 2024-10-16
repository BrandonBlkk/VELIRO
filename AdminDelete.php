<?php
session_start();
include("dbconnection.php");

$admin_id = $_GET['AdminID'];

if (isset($_POST['confirmDelete'])) {
    $admin_id = $_POST['adminId'];

    // Step 1: Delete associated records from the productdetailtb
    $deletePurchaseDetails = "DELETE FROM purchasedetailtb WHERE PurchaseID IN (SELECT PurchaseID FROM purchasetb WHERE AdminID = '$admin_id')";
    $deletePurchaseDetailsQuery = mysqli_query($connect, $deletePurchaseDetails);

    // Step 2: Delete associated records from the purchasetb
    $deletePurchases = "DELETE FROM purchasetb WHERE AdminID = '$admin_id'";
    $deletePurchasesQuery = mysqli_query($connect, $deletePurchases);

    if ($deletePurchaseDetails && $deletePurchasesQuery) {
        // Step 3: Delete the ProductType from producttypetb
        $adminDelete = "DELETE FROM admintb WHERE AdminID = '$admin_id'";
        $adminQuery = mysqli_query($connect, $adminDelete);

        if ($adminQuery) {
            echo "<script>window.alert('Your account has been successfully deleted.')</script>";
            echo "<script>window.location = 'AdminSignIn.php'</script>";
        } else {
            echo "<script>window.alert('Something went wrong.')</script>";
            echo "<script>window.location = 'AdminProfile.php'</script>";
        }
    } else {
        echo "<script>window.alert('Failed to delete associated records. Cannot proceed with account deletion.')</script>";
        echo "<script>window.location = 'AdminProfile.php'</script>";
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
        <h2 class="text-2xl font-semibold text-gray-800 mb-4">Delete Account</h2>
        <p class="mb-6">Are you sure you want to delete your account?</p>
        <form action="" method="post">
            <input type="hidden" name="adminId" value="<?php echo htmlspecialchars($admin_id); ?>">
            <div class="flex justify-end space-x-4">
                <a href="AdminProfile.php" class="px-4 py-2 bg-gray-200 text-gray-700 rounded hover:bg-gray-300 transition duration-200">Cancel</a>
                <button type="submit" name="confirmDelete" class="px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700 transition duration-200">Delete</button>
            </div>
        </form>
    </div>
</body>

</html>