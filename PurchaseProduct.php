<?php
session_start();
include('DbConnection.php');
include('components/AutoIDFunction.php');

if (!isset($_SESSION["AdminEmail"])) {
    echo "<script>window.alert('Login first! You cannot directly access the admin info.')</script>";
    echo "<script>window.location = 'AdminSignIn.php'</script>";
    exit();
}

// Initialize Variables
$purchaseID = AutoID('purchasetb', 'PurchaseID', 'PUR-', 6);
$adminID = $_SESSION['AdminID']; // AdminID stored from session after login
$productId = $quantity = '';
$errors = [];

// Define Tax Rate 
$taxRate = 0.10;

// Fetch product details dynamically
$products = [];
$select = "SELECT * FROM producttb";
$query = mysqli_query($connect, $select);
while ($row = mysqli_fetch_array($query)) {
    $productId = $row['ProductID'];
    $productTypeQuery = "SELECT ProductTypeName FROM producttypetb WHERE ProductTypeID = '{$row['ProductTypeID']}'";
    $typeResult = mysqli_query($connect, $productTypeQuery);
    $typeRow = mysqli_fetch_assoc($typeResult);

    $products[$productId] = [
        'Title' => $row['Title'],
        'Price' => $row['Price'],
        'DiscountPrice' => $row['DiscountPrice'],
        'Brand' => $row['Brand'],
        'Color' => $row['Color'],
        'Type' => $typeRow['ProductTypeName'],
        'Stock' => $row['Stock']
    ];
}

// Initialize or update the session product list
if (!isset($_SESSION['purchaseProducts'])) {
    $_SESSION['purchaseProducts'] = [];
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['addproduct'])) {
        $productId = $_POST['producttype'];
        $quantity = $_POST['stock'];

        // Validation
        if (empty($productId)) {
            $errors['producttype'] = "Please select a product before adding to a list.";
        }
        if (empty($quantity)) {
            $errors['stock'] = "Quantity is required.";
        }

        if (isset($products[$productId]) && $quantity > 0) {
            $product = $products[$productId];
            // Check if product already exists in the session
            $productExists = false;
            foreach ($_SESSION['purchaseProducts'] as &$existingProduct) {
                if ($existingProduct['ProductID'] == $productId) {
                    $existingProduct['Quantity'] += $quantity;
                    $existingProduct['TotalPrice'] = $existingProduct['Price'] * $existingProduct['Quantity'];
                    $productExists = true;
                    break;
                }
            }
            // If product does not exist, add it to the session
            if (!$productExists) {
                $_SESSION['purchaseProducts'][] = [
                    'ProductID' => $productId,
                    'Title' => $product['Title'],
                    'Brand' => $product['Brand'],
                    'Price' => $product['Price'],
                    'Quantity' => $quantity,
                    'TotalPrice' => $product['Price'] * $quantity
                ];
            }
        }
    }

    if (isset($_POST['deletePurchase'])) {
        $productId = $_POST['productid'];
        // Remove the product from session
        foreach ($_SESSION['purchaseProducts'] as $key => $product) {
            if ($product['ProductID'] == $productId) {
                unset($_SESSION['purchaseProducts'][$key]);
                $_SESSION['purchaseProducts'] = array_values($_SESSION['purchaseProducts']);
                break;
            }
        }
    }

    if (isset($_POST['purchase'])) {
        if (!empty($_SESSION['purchaseProducts'])) {
            // Get selected supplier ID
            $supplierID = $_POST['supplier'];

            // Insert into purchase table
            $totalAmount = array_sum(array_column($_SESSION['purchaseProducts'], 'TotalPrice'));
            $purchaseTax = $totalAmount * $taxRate;
            $totalAmount = $totalAmount + $purchaseTax;
            $status = 'Pending';
            $purchaseDate = date("Y-m-d");
            $insertPurchaseQuery = "INSERT INTO purchasetb (PurchaseID, AdminID, SupplierID, TotalAmount, PurchaseTax, Status, PurchaseDate) VALUES ('$purchaseID', '$adminID', '$supplierID', '$totalAmount', '$purchaseTax', '$status', '$purchaseDate')";
            if (mysqli_query($connect, $insertPurchaseQuery)) {
                // Insert into purchasedetailtb
                foreach ($_SESSION['purchaseProducts'] as $product) {
                    $insertDetailQuery = "INSERT INTO purchasedetailtb (PurchaseID, ProductID, PurchaseUnitQuantity, PurchaseUnitPrice, PurchaseDate) VALUES ('$purchaseID', '{$product['ProductID']}', '{$product['Quantity']}', '{$product['Price']}', '$purchaseDate')";
                    mysqli_query($connect, $insertDetailQuery);

                    // Update product stock
                    $currentStockQuery = "SELECT Stock FROM producttb WHERE ProductID = '{$product['ProductID']}'";
                    $stockResult = mysqli_query($connect, $currentStockQuery);
                    $stockRow = mysqli_fetch_assoc($stockResult);
                    $newStock = $stockRow['Stock'] + $product['Quantity'];
                    $updateStockQuery = "UPDATE producttb SET Stock = '$newStock' WHERE ProductID = '{$product['ProductID']}'";
                    mysqli_query($connect, $updateStockQuery);
                }

                // Clear the session purchaseProducts
                unset($_SESSION['purchaseProducts']);
                echo "<script>alert('Purchase completed successfully!');</script>";
                echo "<script>window.location = 'PurchaseProduct.php'</script>";
            } else {
                echo "<script>alert('Error completing purchase.');</script>";
                echo "<script>window.location = 'PurchaseProduct.php'</script>";
            }
            exit();
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
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/remixicon/3.5.0/remixicon.css">
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body>
    <div class="flex h-screen">
        <?php include('./components/AdminNav.php') ?>

        <section class="ml-0 md:ml-[250px] flex-1 p-8 overflow-x-auto">
            <header class="mb-6">
                <h1 class="text-3xl sm:text-4xl font-semibold text-indigo-400">Products Purchasing</h1>
            </header>

            <form class="flex flex-col space-y-6 w-full" action="<?php echo $_SERVER["PHP_SELF"]; ?>" method="post">
                <!-- Choose Product -->
                <div class="flex flex-col space-y-2">
                    <label class="text-lg font-semibold" for="producttype">Choose Product</label>
                    <select name="producttype" id="producttype" class="p-3 border rounded" onchange="populateProductDetails()">
                        <option value="">Select a product</option>
                        <?php foreach ($products as $productId => $productDetails): ?>
                            <option value="<?php echo $productId; ?>"><?php echo $productDetails['Title']; ?></option>
                        <?php endforeach; ?>
                    </select>
                    <?php if (isset($errors['producttype'])) : ?>
                        <p class="text-red-600 text-sm"><?php echo $errors['producttype']; ?></p>
                    <?php endif; ?>
                </div>

                <!-- Auto-filled Product Details -->
                <div class="flex flex-col space-y-2">
                    <label class="text-lg font-semibold" for="producttitle">Product Title</label>
                    <input id="producttitle" class="p-3 border rounded focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-opacity-50 transition duration-300 ease-in-out" type="text" name="producttitle" placeholder="Choose product to see the title" readonly>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div class="flex flex-col space-y-2">
                        <label class="text-lg font-semibold" for="price">Price</label>
                        <input id="price" class="p-3 border rounded focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-opacity-50 transition duration-300 ease-in-out" type="text" name="price" placeholder="Choose product to see the price" readonly>
                    </div>

                    <div class="flex flex-col space-y-2">
                        <label class="text-lg font-semibold" for="discountprice">Discount Price</label>
                        <input id="discountprice" class="p-3 border rounded focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-opacity-50 transition duration-300 ease-in-out" type="text" name="discountprice" placeholder="Choose product to see the discount price" readonly>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div class="flex flex-col space-y-2">
                        <label class="text-lg font-semibold" for="brand">Brand</label>
                        <input id="brand" class="p-3 border rounded focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-opacity-50 transition duration-300 ease-in-out" type="text" name="brand" placeholder="Choose product to see the brand" readonly>
                    </div>

                    <div class="flex flex-col space-y-2">
                        <label class="text-lg font-semibold" for="color">Color</label>
                        <input id="color" class="p-3 border rounded focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-opacity-50 transition duration-300 ease-in-out" type="text" name="color" placeholder="Choose product to see the color" readonly>
                    </div>
                </div>

                <div class="flex flex-col space-y-2">
                    <label class="text-lg font-semibold" for="stock">Quantity</label>
                    <input id="stock" class="p-3 border rounded focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-opacity-50 transition duration-300 ease-in-out <?php echo isset($errors['stock']) ? 'border-red-500' : 'border-gray-300'; ?>" type="number" name="stock" placeholder="Enter quantity" min="1">
                    <?php if (isset($errors['stock'])) : ?>
                        <p class=" text-red-600 text-sm"><?php echo $errors['stock']; ?></p>
                    <?php endif; ?>
                </div>

                <div>
                    <input type="date" class="hidden" id="purchasedate" name="purchasedate" value="<?php echo date("Y-m-d") ?>" required>
                </div>

                <div class="flex flex-col sm:flex-row justify-end gap-1 sm:gap-0 sm:space-x-4">
                    <button type="submit" name="addproduct" class="bg-indigo-500 text-white px-6 py-2 rounded hover:bg-indigo-600 transition duration-300 ease-in-out">Add Product</button>
                    <button type="submit" name="purchase" class="bg-green-500 text-white px-6 py-2 rounded hover:bg-green-600 transition duration-300 ease-in-out">Complete Purchase</button>
                </div>


                <!-- Display added products -->
                <div class="mt-8">
                    <h2 class="text-2xl font-semibold mb-4">Products List</h2>
                    <table class="w-full border-collapse border border-gray-200">
                        <thead class="bg-gray-100">
                            <tr>
                                <th class="border border-gray-300 p-2">Product Title</th>
                                <th class="border border-gray-300 p-2">Brand</th>
                                <th class="border border-gray-300 p-2">Price</th>
                                <th class="border border-gray-300 p-2">Quantity</th>
                                <th class="border border-gray-300 p-2">Total Price</th>
                                <th class="border border-gray-300 p-2">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($_SESSION['purchaseProducts'])): ?>
                                <?php foreach ($_SESSION['purchaseProducts'] as $product): ?>
                                    <tr>
                                        <td class="border border-gray-300 p-2"><?php echo $product['Title']; ?></td>
                                        <td class="border border-gray-300 p-2"><?php echo $product['Brand']; ?></td>
                                        <td class="border border-gray-300 p-2"><?php echo $product['Price']; ?></td>
                                        <td class="border border-gray-300 p-2"><?php echo $product['Quantity']; ?></td>
                                        <td class="border border-gray-300 p-2"><?php echo $product['TotalPrice']; ?></td>
                                        <td class="border border-gray-300 p-2">
                                            <form action="<?php echo $_SERVER["PHP_SELF"]; ?>" method="post">
                                                <input type="hidden" name="productid" value="<?php echo $product['ProductID']; ?>">
                                                <button type="submit" name="deletePurchase" class="bg-red-500 text-white px-2 py-1 rounded hover:bg-red-600 transition duration-300 ease-in-out">Delete</button>
                                            </form>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="6" class="border border-gray-300 p-2 text-center">No products added.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>

                <!-- Choose Supplier -->
                <div class="flex flex-col space-y-2">
                    <label class="text-lg font-semibold" for="supplier">Choose Supplier</label>
                    <select name="supplier" id="supplier" class="p-3 border rounded focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-opacity-50 transition duration-300 ease-in-out">
                        <?php
                        $selectsup = "SELECT * FROM suppliertb";
                        $query = mysqli_query($connect, $selectsup);
                        while ($row = mysqli_fetch_array($query)) {
                            echo "<option value='" . $row["SupplierID"] . "'>" . $row["SupplierName"] . "</option>";
                        }
                        ?>
                    </select>
                </div>
            </form>
        </section>
    </div>

    <script>
        function populateProductDetails() {
            const productId = document.getElementById('producttype').value;
            const products = <?php echo json_encode($products); ?>;
            const product = products[productId] || {};

            document.getElementById('producttitle').value = product.Title || '';
            document.getElementById('price').value = product.Price || '';
            document.getElementById('discountprice').value = product.DiscountPrice || '';
            document.getElementById('brand').value = product.Brand || '';
            document.getElementById('color').value = product.Color || '';
        }
    </script>
</body>

</html>