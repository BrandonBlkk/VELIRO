<?php
session_start();
include('DbConnection.php');

// Initialize variables
$product_type = '';
$errors = [];

// Check if the form is submitted
if (isset($_POST['addproducttype'])) {

    $product_type = mysqli_real_escape_string($connect, $_POST['producttype']);

    // Validation of product type
    if (empty($product_type)) {
        $errors['producttype'] = "Product Type is required.";
    }

    // Insert product type data if no errors
    if (empty($errors)) {
        $product_type_select = "SELECT * FROM producttypetb 
        WHERE ProductTypeName = '$product_type'";

        $product_type_query = mysqli_query($connect, $product_type_select);
        $product_type_query_count = mysqli_num_rows($product_type_query);

        if ($product_type_query_count > 0) {
            echo "<script>window.alert('Product Type is already exists.')</script>";
            echo "<script>window.location = 'AddProductType.php'</script>";
        } else {
            $product_type_insert = "INSERT INTO producttypetb(ProductTypeName)
            VALUES('$product_type')";

            $query = mysqli_query($connect, $product_type_insert);

            if ($query) {
                echo "<script>window.alert('Product Type has been successfully added.')</script>";
                echo "<script>window.location = 'AddProductType.php'</script>";
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
</head>

<body>
    <div class="flex h-screen">

        <?php
        include('./components/AdminNav.php')
        ?>

        <div class="ml-0 md:ml-[250px] flex-1 p-8">
            <div>
                <h1 class="text-3xl sm:text-4xl text-indigo-400 font-semibold mb-5">Product Type</h1>
                <form class="flex flex-col space-y-4 w-full" action="<?php $_SERVER["PHP_SELF"] ?>" method="post" enctype="multipart/form-data">

                    <!-- Product Type Input -->
                    <div class="flex flex-col space-y-1">
                        <label class="text-xl font-semibold" for="producttype">Product Type</label>
                        <input class="p-3 border rounded focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-opacity-50 transition duration-300 ease-in-out <?php echo isset($errors['producttype']) ? 'border-red-500' : 'border-gray-200'; ?>" type="text" name="producttype" placeholder="Enter your product type">
                        <?php if (isset($errors['producttype'])) : ?>
                            <p class="text-red-600 text-sm"><?php echo $errors['producttype']; ?></p>
                        <?php else : ?>
                            <?php if ($product_type) : ?>
                                <p class="text-green-600 text-sm"><?php echo "Product Type is valid." ?></p>
                            <?php endif; ?>
                        <?php endif; ?>
                    </div>

                    <!-- Add Product Type Button -->
                    <div class="flex justify-end">
                        <input class="bg-indigo-500 text-lg font-semibold text-white px-4 py-2 rounded-md hover:bg-indigo-600 cursor-pointer transition-colors duration-200" type="submit" name="addproducttype" value="Add Product Type">
                    </div>
                </form>
            </div>
        </div>
    </div>
</body>

</html>