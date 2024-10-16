<?php
session_start();
include('DbConnection.php');

if (!isset($_SESSION["AdminEmail"])) {
    echo "<script>window.alert('Login first! You cannot direct access the admin info.')</script>";
    echo "<script>window.location = 'AdminSignIn.php'</script>";
}

// Initialize variables
$product_title = $price = $discount_price = $stock = $color = $product_detail = $brand = $model_height = $product_size =
    $look_after_me = $about_me = $extended_sizing = $more_colors = $selling_fast = $product_type = '';
$errors = [];

// Check if the form is submitted
if (isset($_POST['addproduct'])) {

    $product_title = mysqli_real_escape_string($connect, $_POST['producttitle']);
    $price = $_POST['price'];
    $discount_price = $_POST['discountprice'];
    $color = $_POST['color'];
    $product_detail = mysqli_real_escape_string($connect, $_POST['productdetail']);
    $brand = mysqli_real_escape_string($connect, $_POST['brand']);
    $look_after_me = mysqli_real_escape_string($connect, $_POST['lookafterme']);
    $about_me = mysqli_real_escape_string($connect, $_POST['aboutme']);
    $model_height = mysqli_real_escape_string($connect, $_POST['modelheight']);
    $product_size = $_POST['size'];
    $extended_sizing = $_POST['extendedsizing'];
    $more_colors = $_POST['morecolors'];
    $selling_fast = $_POST['sellingfast'];
    $product_type = $_POST['producttype'];
    $added_date = $_POST['addeddate'];
    $stock = $_POST['stock'];

    // Product image 1 upload 
    $product_image1 = $_FILES["productimage1"]["name"];
    $copyFile = "AdminImages/";
    $fileName1 = $copyFile . uniqid() . "_" . $product_image1;
    $copy = copy($_FILES["productimage1"]["tmp_name"], $fileName1);

    if (!$copy) {
        echo "<p>Cannot upload Product Image 1.</p>";
        exit();
    }

    // Product image 1 upload 
    $product_image2 = $_FILES["productimage2"]["name"];
    $copyFile = "AdminImages/";
    $fileName2 = $copyFile . uniqid() . "_" . $product_image2;
    $copy = copy($_FILES["productimage2"]["tmp_name"], $fileName2);

    if (!$copy) {
        echo "<p>Cannot upload Product Image 2.</p>";
        exit();
    }

    // Product image 1 upload 
    $product_image3 = $_FILES["productimage3"]["name"];
    $copyFile = "AdminImages/";
    $fileName3 = $copyFile . uniqid() . "_" . $product_image3;
    $copy = copy($_FILES["productimage3"]["tmp_name"], $fileName3);

    if (!$copy) {
        echo "<p>Cannot upload Product Image 3.</p>";
        exit();
    }

    // Validation of product
    if (empty($product_title)) {
        $errors['producttitle'] = "Product title is required.";
    }
    if (empty($price)) {
        $errors['price'] = "Price is required.";
    }
    if (empty($discount_price)) {
        $errors['discountprice'] = "Discount Price is required.";
    }
    if (empty($color)) {
        $errors['color'] = "Color is required.";
    }
    if (empty($product_detail)) {
        $errors['productdetail'] = "Product detail is required.";
    }
    if (empty($brand)) {
        $errors['brand'] = "Brand is required.";
    }
    if (empty($look_after_me)) {
        $errors['lookafterme'] = "Look after me is required.";
    }
    if (empty($model_height)) {
        $errors['modelheight'] = "Model height is required.";
    }
    if (empty($product_size)) {
        $errors['size'] = "Size is required.";
    }
    if (empty($about_me)) {
        $errors['aboutme'] = "About me is required.";
    }

    // Insert type data if no errors
    if (empty($errors)) {
        $product_select = "SELECT * FROM producttb 
        WHERE Title = '$product_title'";

        $product_query = mysqli_query($connect, $product_select);
        $product_query_count = mysqli_num_rows($product_query);

        if ($product_query_count > 0) {
            echo "<script>window.alert('Product is already exists.')</script>";
            echo "<script>window.location = 'AddProducts.php'</script>";
        } else {
            $product_insert = "INSERT INTO producttb(ProductTypeID, Title,img1, img2, img3,	Price, DiscountPrice, Color, ProductDetail, Brand, ModelHeight, ProductSize, LookAfterMe, AboutMe, ExtendedSizing, MoreColors, SellingFast, AddedDate, Stock)
            VALUES('$product_type','$product_title', '$fileName1', '$fileName2', '$fileName3', '$price', '$discount_price', '$color', '$product_detail', '$brand', '$model_height', '$product_size', '$look_after_me', '$about_me', '$extended_sizing', '$more_colors', '$selling_fast', '$added_date', '$stock')";

            $query = mysqli_query($connect, $product_insert);

            if ($query) {
                echo "<script>window.alert('Product has been successfully added.')</script>";
                echo "<script>window.location = 'AddProducts.php'</script>";
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

        <div class="ml-0 md:ml-[250px] flex-1 p-8 overflow-y-auto">
            <div>
                <h1 class="text-3xl sm:text-4xl text-indigo-500 font-semibold mb-8">Add Product</h1>
                <form class="flex flex-col space-y-6 w-full" action="<?php $_SERVER["PHP_SELF"] ?>" method="post" enctype="multipart/form-data">

                    <!-- Product Title Input -->
                    <div class="flex flex-col space-y-2">
                        <label class="text-lg font-semibold" for="producttitle">Product Title</label>
                        <input class="p-3 border rounded focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-opacity-50 transition duration-300 ease-in-out<?php echo isset($errors['producttitle']) ? 'border-red-500' : 'border-gray-300'; ?>" type="text" name="producttitle" placeholder="Enter the product title" value="<?php echo htmlspecialchars($product_title); ?>">
                        <?php if (isset($errors['producttitle'])) : ?>
                            <p class="text-red-600 text-sm"><?php echo $errors['producttitle']; ?></p>
                        <?php else : ?>
                            <?php if ($product_title) : ?>
                                <p class="text-green-600 text-sm"><?php echo "Product title is valid." ?></p>
                            <?php endif; ?>
                        <?php endif; ?>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <!-- Product Price Input -->
                        <div class="flex flex-col space-y-2">
                            <label class="text-lg font-semibold" for="price">Price</label>
                            <input class="p-3 border rounded focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-opacity-50 transition duration-300 ease-in-out<?php echo isset($errors['price']) ? 'border-red-500' : 'border-gray-200'; ?>" type="text" name="price" placeholder="Enter the price" value="<?php echo htmlspecialchars($price); ?>">
                            <?php if (isset($errors['price'])) : ?>
                                <p class="text-red-600 text-sm"><?php echo $errors['price']; ?></p>
                            <?php else : ?>
                                <?php if ($price) : ?>
                                    <p class="text-green-600 text-sm"><?php echo "Price is valid." ?></p>
                                <?php endif; ?>
                            <?php endif; ?>
                        </div>

                        <!-- Product Discount Price Input -->
                        <div class="flex flex-col space-y-2">
                            <label class="text-lg font-semibold" for="discountprice">Discount Price</label>
                            <input class="p-3 border rounded focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-opacity-50 transition duration-300 ease-in-out<?php echo isset($errors['discountprice']) ? 'border-red-500' : 'border-gray-200'; ?>" type="text" name="discountprice" placeholder="Enter the discount price" value="<?php echo htmlspecialchars($discount_price); ?>">
                            <?php if (isset($errors['discountprice'])) : ?>
                                <p class="text-red-600 text-sm"><?php echo $errors['discountprice']; ?></p>
                            <?php else : ?>
                                <?php if ($discount_price) : ?>
                                    <p class="text-green-600 text-sm"><?php echo "Discount Price is valid." ?></p>
                                <?php endif; ?>
                            <?php endif; ?>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <!-- Color Input -->
                        <div class="flex flex-col space-y-2">
                            <label class="text-lg font-semibold" for="color">Color</label>
                            <input class="p-3 border rounded focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-opacity-50 transition duration-300 ease-in-out<?php echo isset($errors['color']) ? 'border-red-500' : 'border-gray-200'; ?>" type="text" name="color" placeholder="Enter the color" value="<?php echo htmlspecialchars($color); ?>">
                            <?php if (isset($errors['color'])) : ?>
                                <p class="text-red-600 text-sm"><?php echo $errors['color']; ?></p>
                            <?php else : ?>
                                <?php if ($color) : ?>
                                    <p class="text-green-600 text-sm"><?php echo "Color is valid." ?></p>
                                <?php endif; ?>
                            <?php endif; ?>
                        </div>

                        <!-- Product Detail Input -->
                        <div class="flex flex-col space-y-2">
                            <label class="text-lg font-semibold" for="productdetail">Product Detail</label>
                            <input class="p-3 border rounded focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-opacity-50 transition duration-300 ease-in-out<?php echo isset($errors['productdetail']) ? 'border-red-500' : 'border-gray-200'; ?>" type="text" name="productdetail" placeholder="Enter the product detail" value="<?php echo htmlspecialchars($product_detail); ?>">
                            <?php if (isset($errors['productdetail'])) : ?>
                                <p class="text-red-600 text-sm"><?php echo $errors['productdetail']; ?></p>
                            <?php else : ?>
                                <?php if ($product_detail) : ?>
                                    <p class="text-green-600 text-sm"><?php echo "Product detail is valid." ?></p>
                                <?php endif; ?>
                            <?php endif; ?>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <!-- Brand Input -->
                        <div class="flex flex-col space-y-2">
                            <label class="text-lg font-semibold" for="brand">Brand</label>
                            <input class="p-3 border rounded focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-opacity-50 transition duration-300 ease-in-out<?php echo isset($errors['brand']) ? 'border-red-500' : 'border-gray-200'; ?>" type="text" name="brand" placeholder="Enter the brand" value="<?php echo htmlspecialchars($brand); ?>">
                            <?php if (isset($errors['brand'])) : ?>
                                <p class="text-red-600 text-sm"><?php echo $errors['brand']; ?></p>
                            <?php else : ?>
                                <?php if ($brand) : ?>
                                    <p class="text-green-600 text-sm"><?php echo "Brand is valid." ?></p>
                                <?php endif; ?>
                            <?php endif; ?>
                        </div>

                        <!-- Look After Me Input -->
                        <div class="flex flex-col space-y-2">
                            <label class="text-lg font-semibold" for="lookafterme">Look After Me</label>
                            <input class="p-3 border rounded focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-opacity-50 transition duration-300 ease-in-out<?php echo isset($errors['lookafterme']) ? 'border-red-500' : 'border-gray-200'; ?>" type="text" name="lookafterme" placeholder="Enter care instructions" value="<?php echo htmlspecialchars($look_after_me); ?>">
                            <?php if (isset($errors['lookafterme'])) : ?>
                                <p class="text-red-600 text-sm"><?php echo $errors['lookafterme']; ?></p>
                            <?php else : ?>
                                <?php if ($look_after_me) : ?>
                                    <p class="text-green-600 text-sm"><?php echo "Look after me is valid." ?></p>
                                <?php endif; ?>
                            <?php endif; ?>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <!-- Medel Height Input -->
                        <div class="flex flex-col space-y-2">
                            <label class="text-lg font-semibold" for="modelheight">Model Height</label>
                            <input class="p-3 border rounded focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-opacity-50 transition duration-300 ease-in-out<?php echo isset($errors['modelheight']) ? 'border-red-500' : 'border-gray-200'; ?>" type="text" name="modelheight" placeholder="Enter the model height" value="<?php echo htmlspecialchars($model_height); ?>">
                            <?php if (isset($errors['modelheight'])) : ?>
                                <p class="text-red-600 text-sm"><?php echo $errors['modelheight']; ?></p>
                            <?php else : ?>
                                <?php if ($model_height) : ?>
                                    <p class="text-green-600 text-sm"><?php echo "Model height is valid." ?></p>
                                <?php endif; ?>
                            <?php endif; ?>
                        </div>

                        <!-- Product Size Input -->
                        <div class="flex flex-col space-y-2">
                            <label class="text-lg font-semibold" for="size">Size</label>
                            <input class="p-3 border rounded focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-opacity-50 transition duration-300 ease-in-out<?php echo isset($errors['size']) ? 'border-red-500' : 'border-gray-200'; ?>" type="text" name="size" placeholder="Enter care size" value="<?php echo htmlspecialchars($product_size); ?>">
                            <?php if (isset($errors['size'])) : ?>
                                <p class="text-red-600 text-sm"><?php echo $errors['size']; ?></p>
                            <?php else : ?>
                                <?php if ($product_size) : ?>
                                    <p class="text-green-600 text-sm"><?php echo "Size is valid." ?></p>
                                <?php endif; ?>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- About Me Input -->
                    <div class="flex flex-col space-y-2">
                        <label class="text-lg font-semibold" for="aboutme">About Me</label>
                        <input class="p-3 border rounded focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-opacity-50 transition duration-300 ease-in-out<?php echo isset($errors['aboutme']) ? 'border-red-500' : 'border-gray-200'; ?>" type="text" name="aboutme" placeholder="Describe the product" value="<?php echo htmlspecialchars($about_me); ?>">
                        <?php if (isset($errors['aboutme'])) : ?>
                            <p class="text-red-600 text-sm"><?php echo $errors['aboutme']; ?></p>
                        <?php else : ?>
                            <?php if ($about_me) : ?>
                                <p class="text-green-600 text-sm"><?php echo "About Me is valid." ?></p>
                            <?php endif; ?>
                        <?php endif; ?>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                        <!-- Choose Extended Sizing -->
                        <div class="flex flex-col space-y-2">
                            <label class="text-lg font-semibold" for="extendedsizing">Extended Sizing</label>
                            <select name="extendedsizing" id="extendedsizing" class="p-3 border rounded focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-opacity-50 transition duration-300 ease-in-out<?php echo isset($errors['extendedsizing']) ? 'border-red-500' : 'border-gray-200'; ?>">
                                <option value="true">True</option>
                                <option value="false">False</option>
                            </select>
                        </div>

                        <!-- Choose More Colors -->
                        <div class="flex flex-col space-y-2">
                            <label class="text-lg font-semibold" for="morecolors">More Colors</label>
                            <select name="morecolors" id="morecolors" class="p-3 border rounded focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-opacity-50 transition duration-300 ease-in-out<?php echo isset($errors['morecolors']) ? 'border-red-500' : 'border-gray-300'; ?>">
                                <option value="true">True</option>
                                <option value="false">False</option>
                            </select>
                        </div>

                        <!-- Choose Selling Fast -->
                        <div class="flex flex-col space-y-2">
                            <label class="text-lg font-semibold" for="sellingfast">Selling Fast</label>
                            <select name="sellingfast" id="sellingfast" class="p-3 border rounded focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-opacity-50 transition duration-300 ease-in-out<?php echo isset($errors['sellingfast']) ? 'border-red-500' : 'border-gray-300'; ?>">
                                <option value="true">True</option>
                                <option value="false">False</option>
                            </select>
                        </div>
                    </div>


                    <!-- Product Stock Input -->
                    <div class="flex flex-col space-y-2">
                        <label class="text-lg font-semibold" for="stock">Stock</label>
                        <input class="p-3 border rounded focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-opacity-50 transition duration-300 ease-in-out" type="text" name="stock" placeholder="Enter the stock" value="0" readonly>
                    </div>

                    <!-- Date -->
                    <div>
                        <input type="date" class="hidden" id="addeddate" name="addeddate" value="<?php echo date("Y-m-d") ?>" required>
                    </div>

                    <!-- Product Image Input -->
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                        <div class="flex flex-col space-y-2">
                            <label class="text-lg font-semibold" for="productimage1">Product Image 1</label>
                            <input type="file" name="productimage1" id="productimage1" class="p-3 border rounded focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-opacity-50 transition duration-300 ease-in-out" required>
                        </div>
                        <div class="flex flex-col space-y-2">
                            <label class="text-lg font-semibold" for="productimage2">Product Image 2</label>
                            <input type="file" name="productimage2" id="productimage2" class="p-3 border rounded focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-opacity-50 transition duration-300 ease-in-out" required>
                        </div>
                        <div class="flex flex-col space-y-2">
                            <label class="text-lg font-semibold" for="productimage3">Product Image 3</label>
                            <input type="file" name="productimage3" id="productimage3" class="p-3 border rounded focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-opacity-50 transition duration-300 ease-in-out" required>
                        </div>
                    </div>

                    <!-- Choose Product Type -->
                    <div class="flex flex-col space-y-2">
                        <label class="text-lg font-semibold" for="producttype">Choose Product Type</label>
                        <select name="producttype" id="producttype" class="p-3 border rounded focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-opacity-50 transition duration-300 ease-in-out">
                            <?php
                            $select = "SELECT * FROM producttypetb";
                            $query = mysqli_query($connect, $select);
                            $count = mysqli_num_rows($query);

                            for ($i = 0; $i < $count; $i++) {
                                $row = mysqli_fetch_array($query);
                                $product_type_id = $row['ProductTypeID'];
                                $product_type = $row['ProductTypeName'];

                                echo "<option value= '$product_type_id'>$product_type</option>";
                            }
                            ?>
                        </select>
                    </div>

                    <!-- Add Product Button -->
                    <div class="flex justify-end">
                        <input class="bg-indigo-500 text-lg font-semibold text-white px-6 py-3 rounded hover:bg-indigo-600 cursor-pointer transition-colors duration-200" type="submit" name="addproduct" value="Add Product">
                    </div>
                </form>
            </div>
        </div>
    </div>
</body>

</html>