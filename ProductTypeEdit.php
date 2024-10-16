<?php
session_start();
include('DbConnection.php');

// Initialize variables
$product_type = '';
$errors = [];

if (isset($_GET["ProductTypeID"])) {
    $producttype_id = $_GET["ProductTypeID"];

    $query = "SELECT * FROM producttypetb Where ProductTypeID = '$producttype_id'";

    $result = mysqli_query($connect, $query);
    $array = mysqli_fetch_array($result);

    $producttype_name = $array['ProductTypeName'];
}

// Check if the form is submitted
if (isset($_POST['editproducttype'])) {

    $product_type = mysqli_real_escape_string($connect, $_POST['producttype']);;

    // Validation of product type
    if (empty($product_type)) {
        $errors['producttype'] = "Product Type is required.";
    }

    // Update product type data if no errors
    if (empty($errors)) {
        $product_type_update = "UPDATE producttypetb SET ProductTypeName = '$product_type'
        WHERE ProductTypeID = '$producttype_id'";

        $product_type_query = mysqli_query($connect, $product_type_update);

        if ($product_type_query) {
            echo "<script>window.alert('ProductType is updated.')</script>";
            echo "<script>window.location = 'AdminDashboard.php'</script>";
        } else {
            echo "<script>window.alert('Something went wrong.')</script>";
            echo "<script>window.location = 'AdminDashboard.php'</script>";
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
</head>

<body>
    <div class="flex h-screen">

        <?php
        include('./components/AdminNav.php')
        ?>

        <div class="ml-0 md:ml-[250px] flex-1 p-8">
            <div>
                <h1 class="text-3xl sm:text-4xl text-indigo-400 font-semibold mb-5">Product Type Edit</h1>
                <form class="flex flex-col space-y-4 w-full" action="<?php $_SERVER["PHP_SELF"] ?>" method="post" enctype="multipart/form-data">

                    <!-- Product Type Input -->
                    <div class="flex flex-col space-y-1">
                        <label class="text-xl font-semibold" for="producttype">Product Type</label>
                        <input class="p-3 border rounded focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-opacity-50 transition duration-300 ease-in-out <?php echo isset($errors['producttype']) ? 'border-red-500' : 'border-gray-300'; ?>" type="text" name="producttype" placeholder="Enter your product type" value="<?php echo $producttype_name; ?>">
                        <?php if (isset($errors['producttype'])) : ?>
                            <p class="text-red-600 text-sm"><?php echo $errors['producttype']; ?></p>
                        <?php else : ?>
                            <?php if ($product_type) : ?>
                                <p class="text-green-600 text-sm"><?php echo "Product Type is valid." ?></p>
                            <?php endif; ?>
                        <?php endif; ?>
                    </div>

                    <!-- Edit Product Type Button -->
                    <div class="flex justify-end">
                        <input class="bg-indigo-500 text-lg font-semibold text-white px-4 py-2 rounded-md hover:bg-indigo-600 cursor-pointer transition-colors duration-200" type="submit" name="editproducttype" value="Edit Product Type">
                    </div>
                </form>
            </div>
        </div>
    </div>
</body>

</html>