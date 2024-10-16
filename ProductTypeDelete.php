<?php
session_start();
include("dbconnection.php");

$producttype_id = $_GET['ProductTypeID'];

if (isset($_POST['confirmDelete'])) {
    $producttype_id = $_POST['productTypeId'];

    // Step 1: Delete all products associated with the ProductTypeID
    $deleteProducts = "DELETE FROM producttb WHERE ProductTypeID = '$producttype_id'";
    $deleteProductsQuery = mysqli_query($connect, $deleteProducts);

    if ($deleteProductsQuery) {
        // Step 2: Delete the ProductType from producttypetb
        $productTypeDelete = "DELETE FROM producttypetb WHERE ProductTypeID = '$producttype_id'";
        $productTypeQuery = mysqli_query($connect, $productTypeDelete);

        if ($productTypeQuery) {
            echo "<script>window.alert('ProductType data has been successfully deleted.')</script>";
            echo "<script>window.location = 'AdminDashboard.php'</script>";
        } else {
            echo "<script>window.alert('Something went wrong.')</script>";
            echo "<script>window.location = 'AdminDashboard.php'</script>";
        }
    } else {
        echo "<script>window.alert('Failed to delete products associated with this ProductType.')</script>";
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
        <h2 class="text-2xl font-semibold text-gray-800 mb-4">Delete Product Type</h2>
        <p class="mb-6">Are you sure you want to delete this product type?</p>
        <form action="" method="post">
            <input type="hidden" name="productTypeId" value="<?php echo htmlspecialchars($producttype_id); ?>">
            <div class="flex justify-end space-x-4">
                <a href="AdminDashboard.php" class="px-4 py-2 bg-gray-200 text-gray-700 rounded hover:bg-gray-300 transition duration-200">Cancel</a>
                <button type="submit" name="confirmDelete" class="px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700 transition duration-200">Delete</button>
            </div>
        </form>
    </div>
</body>

</html>