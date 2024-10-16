<?php
session_start();
include('DbConnection.php');
include('components/UpdateImageFunction.php');

if (!isset($_SESSION["AdminEmail"])) {
    echo "<script>window.alert('Login first! You cannot direct access the admin info.')</script>";
    echo "<script>window.location = 'AdminSignIn.php'</script>";
}

// Initialize variables
$productTitle = $productPrice = $discount_price = $stock = $productColor = $productDetail = $brand = $modelHeight = $productSize =
    $lookAfterMe = $aboutMe = $extendedSizing = $more_colors = $selling_fast = $product_type = '';
$errors = [];

// Retrieve product details if ProductID is set
if (isset($_GET["ProductID"])) {
    $product_id = $_GET["ProductID"];

    $query = "SELECT * FROM producttb WHERE ProductID = '$product_id'";
    $result = mysqli_query($connect, $query);

    if ($result && mysqli_num_rows($result) > 0) {
        $array = mysqli_fetch_array($result);

        // Populate variables with data from the database
        $product_title = $array['Title'];
        $productType_id = $array['ProductTypeID'];
        $img1 = $array['img1'];
        $img2 = $array['img2'];
        $img3 = $array['img3'];
        $price = $array['Price'];
        $discountPrice = $array['DiscountPrice'];
        $color = $array['Color'];
        $product_detail = $array['ProductDetail'];
        $product_brand = $array['Brand'];
        $model_height = $array['ModelHeight'];
        $product_size = $array['ProductSize'];
        $look_after_me = $array['LookAfterMe'];
        $about_me = $array['AboutMe'];
        $extended_sizing = $array['ExtendedSizing'];
        $more_colors = $array['MoreColors'];
        $selling_fast = $array['SellingFast'];
        $product_addedDate = $array['AddedDate'];
        $product_stock = $array['Stock'];
    }
}

// Check if the form is submitted
if (isset($_POST['editproduct'])) {

    $productTitle = mysqli_real_escape_string($connect, $_POST['producttitle']);
    $productPrice = $_POST['price'];
    $discount_price = $_POST['discountprice'];
    $productColor = $_POST['color'];
    $productDetail = mysqli_real_escape_string($connect, $_POST['productdetail']);
    $brand = mysqli_real_escape_string($connect, $_POST['brand']);
    $lookAfterMe = mysqli_real_escape_string($connect, $_POST['lookafterme']);
    $aboutMe = mysqli_real_escape_string($connect, $_POST['aboutme']);
    $modelHeight = mysqli_real_escape_string($connect, $_POST['modelheight']);
    $productSize = $_POST['size'];
    $extended_sizing = $_POST['extendedsizing'];
    $more_colors = $_POST['morecolors'];
    $selling_fast = $_POST['sellingfast'];
    $product_type = $_POST['producttype'];
    $stock = $_POST['stock'];

    // Current image from the database
    $currentImage1 = $img1;
    $currentImage2 = $img2;
    $currentImage3 = $img3;

    // Simulate $_FILES array for three product images
    $imageFile1 = $_FILES['productimage1'];
    $imageFile2 = $_FILES['productimage2'];
    $imageFile3 = $_FILES['productimage3'];

    // Upload Product Image 1
    $result1 = uploadProductImage($imageFile1, $currentImage1);
    if (is_array($result1)) {
        echo $result1['image'] . "<br>";
    } else {
        $product_image1 = $result1;
    }

    // Upload Product Image 2
    $result2 = uploadProductImage($imageFile2, $currentImage2);
    if (is_array($result2)) {
        echo $result2['image'] . "<br>";
    } else {
        $product_image2 = $result2;
    }

    // Upload Product Image 3
    $result3 = uploadProductImage($imageFile3, $currentImage3);
    if (is_array($result3)) {
        echo $result3['image'] . "<br>";
    } else {
        $product_image3 = $result3;
    }

    // Validation of product
    if (empty($productTitle)) {
        $errors['producttitle'] = "Product title is required.";
    }
    if (empty($productPrice)) {
        $errors['price'] = "Price is required.";
    }
    if (empty($productColor)) {
        $errors['color'] = "Color is required.";
    }
    if (empty($productDetail)) {
        $errors['productdetail'] = "Product detail is required.";
    }
    if (empty($brand)) {
        $errors['brand'] = "Brand is required.";
    }
    if (empty($lookAfterMe)) {
        $errors['lookafterme'] = "Look after me is required.";
    }
    if (empty($modelHeight)) {
        $errors['modelheight'] = "Model height is required.";
    }
    if (empty($productSize)) {
        $errors['size'] = "Size is required.";
    }
    if (empty($aboutMe)) {
        $errors['aboutme'] = "About me is required.";
    }
    if (empty($extended_sizing)) {
        $errors['extendedsizing'] = "Extended sizing is required.";
    }
    if (empty($more_colors)) {
        $errors['morecolors'] = "More colors is required.";
    }
    if (empty($selling_fast)) {
        $errors['sellingfast'] = "Selling fast is required.";
    }

    // Insert product type data if no errors
    if (empty($errors)) {
        $product_update = "UPDATE producttb 
        SET 
        ProductTypeID = '$product_type',
        Title = '$productTitle',
        img1 = '$product_image1',
        img2 = '$product_image2',
        img3 = '$product_image3',
        Price = '$productPrice',
        DiscountPrice = '$discount_price',
        Color = '$productColor',
        ProductDetail = '$product_detail',
        Brand = '$brand',
        ModelHeight = '$model_height',
        ProductSize = '$productSize',
        LookAfterMe = '$lookAfterMe',
        AboutMe = '$aboutMe',
        ExtendedSizing = '$extended_sizing',
        MoreColors = '$more_colors',
        SellingFast  = '$selling_fast',
        Stock = '$stock'
        WHERE ProductID = '$product_id'";

        $supplier_query = mysqli_query($connect, $product_update);

        if ($supplier_query) {
            echo "<script>window.alert('Product is updated.')</script>";
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

        <div class="ml-0 md:ml-[250px] flex-1 p-8 overflow-y-auto">
            <div>
                <h1 class="text-3xl sm:text-4xl text-indigo-500 font-semibold mb-8">Product Edit</h1>
                <form class="flex flex-col space-y-6 w-full" action="<?php $_SERVER["PHP_SELF"] ?>" method="post" enctype="multipart/form-data">

                    <!-- Product Title Input -->
                    <div class="flex flex-col space-y-2">
                        <label class="text-lg font-semibold" for="producttitle">Product Title</label>
                        <input class="p-3 border rounded focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-opacity-50 transition duration-300 ease-in-out<?php echo isset($errors['producttitle']) ? 'border-red-500' : 'border-gray-300'; ?>" type="text" name="producttitle" placeholder="Enter the product title" value="<?php echo htmlspecialchars($product_title); ?>">
                        <?php if (isset($errors['producttitle'])) : ?>
                            <p class="text-red-600 text-sm"><?php echo $errors['producttitle']; ?></p>
                        <?php else : ?>
                            <?php if ($productTitle) : ?>
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
                                <?php if ($productPrice) : ?>
                                    <p class="text-green-600 text-sm"><?php echo "Price is valid." ?></p>
                                <?php endif; ?>
                            <?php endif; ?>
                        </div>

                        <!-- Product Discount Price Input -->
                        <div class="flex flex-col space-y-2">
                            <label class="text-lg font-semibold" for="discountprice">Discount Price</label>
                            <input class="p-3 border rounded focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-opacity-50 transition duration-300 ease-in-out" type="text" name="discountprice" placeholder="Enter the discount price" value="<?php echo htmlspecialchars($discountPrice); ?>">
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
                                <?php if ($productColor) : ?>
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
                                <?php if ($productDetail) : ?>
                                    <p class="text-green-600 text-sm"><?php echo "Product detail is valid." ?></p>
                                <?php endif; ?>
                            <?php endif; ?>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <!-- Brand Input -->
                        <div class="flex flex-col space-y-2">
                            <label class="text-lg font-semibold" for="brand">Brand</label>
                            <input class="p-3 border rounded focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-opacity-50 transition duration-300 ease-in-out<?php echo isset($errors['brand']) ? 'border-red-500' : 'border-gray-200'; ?>" type="text" name="brand" placeholder="Enter the brand" value="<?php echo htmlspecialchars($product_brand); ?>">
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
                                <?php if ($lookAfterMe) : ?>
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
                                <?php if ($modelHeight) : ?>
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
                                <?php if ($productSize) : ?>
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
                            <?php if ($aboutMe) : ?>
                                <p class="text-green-600 text-sm"><?php echo "About Me is valid." ?></p>
                            <?php endif; ?>
                        <?php endif; ?>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                        <!-- Choose Extended Sizing -->
                        <div class="flex flex-col space-y-2">
                            <label class="text-lg font-semibold" for="extendedsizing">Extended Sizing</label>
                            <select name="extendedsizing" id="extendedsizing" class="p-3 border rounded focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-opacity-50 transition duration-300 ease-in-out<?php echo isset($errors['extendedsizing']) ? 'border-red-500' : 'border-gray-200'; ?>">
                                <option value="true" <?php echo $extended_sizing == 'true' ? 'selected' : ''; ?>>True</option>
                                <option value="false" <?php echo $extended_sizing == 'false' ? 'selected' : ''; ?>>False</option>
                            </select>
                        </div>

                        <!-- Choose More Colors -->
                        <div class="flex flex-col space-y-2">
                            <label class="text-lg font-semibold" for="morecolors">More Colors</label>
                            <select name="morecolors" id="morecolors" class="p-3 border rounded focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-opacity-50 transition duration-300 ease-in-out<?php echo isset($errors['morecolors']) ? 'border-red-500' : 'border-gray-300'; ?>">
                                <option value="true" <?php echo $more_colors == 'true' ? 'selected' : ''; ?>>True</option>
                                <option value="false" <?php echo $more_colors == 'false' ? 'selected' : ''; ?>>False</option>
                            </select>
                        </div>

                        <!-- Choose Selling Fast -->
                        <div class="flex flex-col space-y-2">
                            <label class="text-lg font-semibold" for="sellingfast">Selling Fast</label>
                            <select name="sellingfast" id="sellingfast" class="p-3 border rounded focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-opacity-50 transition duration-300 ease-in-out<?php echo isset($errors['sellingfast']) ? 'border-red-500' : 'border-gray-300'; ?>">
                                <option value="true" <?php echo $selling_fast == 'true' ? 'selected' : ''; ?>>True</option>
                                <option value="false" <?php echo $selling_fast == 'false' ? 'selected' : ''; ?>>False</option>
                            </select>
                        </div>
                    </div>


                    <!-- Product Stock Input -->
                    <div class="flex flex-col space-y-2">
                        <label class="text-lg font-semibold" for="stock">Stock</label>
                        <input class="p-3 border rounded focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-opacity-50 transition duration-300 ease-in-out" type="text" name="stock" placeholder="Enter the stock" value="<?php echo htmlspecialchars($product_stock); ?>" readonly>
                    </div>

                    <!-- Product Image Input -->
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                        <div class="flex flex-col space-y-2">
                            <label class="text-lg font-semibold" for="productimage1">Product Image 1</label>
                            <?php if (!empty($img1)): ?>
                                <img src="<?php echo $img1; ?>" alt="Current Image 1" class="w-20 h-auto">
                            <?php endif; ?>
                            <input type="file" name="productimage1" id="productimage1" class="p-3 border rounded focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-opacity-50 transition duration-300 ease-in-out">
                        </div>
                        <div class="flex flex-col space-y-2">
                            <label class="text-lg font-semibold" for="productimage2">Product Image 2</label>
                            <?php if (!empty($img2)): ?>
                                <img src="<?php echo $img2; ?>" alt="Current Image 2" class="w-20 h-auto">
                            <?php endif; ?>
                            <input type="file" name="productimage2" id="productimage2" class="p-3 border rounded focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-opacity-50 transition duration-300 ease-in-out">
                        </div>
                        <div class="flex flex-col space-y-2">
                            <label class="text-lg font-semibold" for="productimage3">Product Image 3</label>
                            <?php if (!empty($img3)): ?>
                                <img src="<?php echo $img3; ?>" alt="Current Image 3" class="w-20 h-auto">
                            <?php endif; ?>
                            <input type="file" name="productimage3" id="productimage3" class="p-3 border rounded focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-opacity-50 transition duration-300 ease-in-out">
                        </div>
                    </div>

                    <!-- Choose Product Type -->
                    <div class="flex flex-col space-y-2">
                        <label class="text-lg font-semibold" for="producttype">Product Type</label>
                        <select class="p-3 border rounded focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-opacity-50 transition duration-300 ease-in-out<?php echo isset($errors['producttype']) ? 'border-red-500' : 'border-gray-200'; ?>" name="producttype" id="producttype">
                            <!-- Fetch product types from the database -->
                            <?php
                            $query = "SELECT * FROM producttypetb";
                            $result = mysqli_query($connect, $query);
                            while ($row = mysqli_fetch_assoc($result)) {
                                echo "<option value='" . $row['ProductTypeID'] . "'" . ($row['ProductTypeID'] == $productType_id ? " selected" : "") . ">" . $row['ProductTypeName'] . "</option>";
                            }
                            ?>
                        </select>
                        <?php if (isset($errors['producttype'])) : ?>
                            <p class="text-red-600 text-sm"><?php echo $errors['producttype']; ?></p>
                        <?php elseif ($product_type) : ?>
                            <p class="text-green-600 text-sm">Product type is valid.</p>
                        <?php endif; ?>
                    </div>

                    <!-- Add Product Button -->
                    <div class="flex justify-end">
                        <input class="bg-indigo-500 text-lg font-semibold text-white px-6 py-3 rounded hover:bg-indigo-600 cursor-pointer transition-colors duration-200" type="submit" name="editproduct" value="Edit Product">
                    </div>
                </form>
            </div>
        </div>
    </div>
</body>

</html>