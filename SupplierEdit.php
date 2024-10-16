<?php
session_start();
include('DbConnection.php');

// Initialize variables
$suppliername = $supplieremail = $supplierphone = $supplieraddress = '';
$errors = [];

if (isset($_GET["SupplierID"])) {
    $supplier_id = $_GET["SupplierID"];

    $query = "SELECT * FROM suppliertb Where SupplierID = '$supplier_id'";

    $result = mysqli_query($connect, $query);
    $array = mysqli_fetch_array($result);

    $supplier_name = $array['SupplierName'];
    $supplier_email = $array['SupplierEmail'];
    $supplier_password = $array['SupplierPassword'];
    $supplier_phone = $array['SupplierPhone'];
    $supplier_address = $array['SupplierAddress'];
}

// Check if the form is submitted
if (isset($_POST['editsupplier'])) {

    $suppliername = mysqli_real_escape_string($connect, $_POST['suppliername']);
    $supplieremail = mysqli_real_escape_string($connect, $_POST['email']);
    $supplierphone = mysqli_real_escape_string($connect, $_POST['phone']);
    $supplieraddress = mysqli_real_escape_string($connect, $_POST['address']);

    // Validation of supplier
    if (empty($suppliername)) {
        $errors['suppliername'] = "Supplier is required.";
    }

    // Validation of email
    if (empty($supplieremail)) {
        $errors['supplieremail'] = "Email is required.";
    }

    // Validation of phone
    if (empty($supplierphone)) {
        $errors['supplierphone'] = "Phone number is required.";
    } elseif (!preg_match('/^\d+$/', $supplierphone)) {
        $errors['supplierphone'] = "Phone number is invalid. Only digits are allowed.";
    } elseif (strlen($supplierphone) < 9 || strlen($supplierphone) > 11) {
        $errors['supplierphone'] = "Phone number is invalid.";
    }

    // Validation of phone
    if (empty($supplieraddress)) {
        $errors['supplieraddress'] = "Address is required.";
    }

    // Update product type data if no errors
    if (empty($errors)) {
        $supplier_update = "UPDATE suppliertb 
        SET SupplierName = '$suppliername', 
        SupplierEmail = '$supplieremail',
        SupplierPhone = '$supplierphone',
        SupplierAddress = '$supplieraddress'
        WHERE SupplierID = '$supplier_id'";

        $supplier_query = mysqli_query($connect, $supplier_update);

        if ($supplier_query) {
            echo "<script>window.alert('Supplier is updated.')</script>";
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
                <h1 class="text-3xl sm:text-4xl text-indigo-400 font-semibold mb-5">Supplier Edit</h1>
                <form class="flex flex-col space-y-4 w-full" action="<?php $_SERVER["PHP_SELF"] ?>" method="post" enctype="multipart/form-data">

                    <!-- Product Type Input -->
                    <div class="flex flex-col space-y-1">
                        <label class="text-xl font-semibold" for="suppliername">Supplier Name</label>
                        <input class="p-3 border rounded focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-opacity-50 transition duration-300 ease-in-out <?php echo isset($errors['suppliername']) ? 'border-red-500' : 'border-gray-300'; ?>" type="text" name="suppliername" placeholder="Enter the supplier" value="<?php echo $supplier_name; ?>">
                        <?php if (isset($errors['suppliername'])) : ?>
                            <p class="text-red-600 text-sm"><?php echo $errors['suppliername']; ?></p>
                        <?php else : ?>
                            <?php if ($suppliername) : ?>
                                <p class="text-green-600 text-sm"><?php echo "Supplier is valid." ?></p>
                            <?php endif; ?>
                        <?php endif; ?>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <!-- Email Input -->
                        <div class="flex flex-col space-y-1">
                            <label class="text-xl font-semibold" for="email">Email</label>
                            <input class="p-3 border rounded focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-opacity-50 transition duration-300 ease-in-out <?php echo isset($errors['supplieremail']) ? 'border-red-500' : 'border-gray-300'; ?>" type="email" name="email" placeholder="Enter the supplier email" value="<?php echo htmlspecialchars($supplier_email); ?>">
                            <?php if (isset($errors['supplieremail'])) : ?>
                                <p class="text-red-600 text-sm"><?php echo $errors['supplieremail']; ?></p>
                            <?php else : ?>
                                <?php if ($supplieremail) : ?>
                                    <p class="text-green-600 text-sm"><?php echo "Email is valid." ?></p>
                                <?php endif; ?>
                            <?php endif; ?>
                        </div>

                        <!-- Password Input -->
                        <div class="flex flex-col space-y-1">
                            <label class="text-xl font-semibold" for="password">Password</label>
                            <input class="p-3 border rounded t:text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-opacity-50 transition duration-300 ease-in-out" type="password" name="password" placeholder="Enter the supplier password" value="<?php echo htmlspecialchars($supplier_password) ?>" disabled>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <!-- Phone Input -->
                        <div class="flex flex-col space-y-1">
                            <label class="text-xl font-semibold" for="phone">Phone</label>
                            <input class="p-3 border rounded focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-opacity-50 transition duration-300 ease-in-out <?php echo isset($errors['supplierphone']) ? 'border-red-500' : 'border-gray-300'; ?>" type="phone" name="phone" placeholder="Enter the supplier phone" value="<?php echo htmlspecialchars($supplier_phone) ?>">
                            <?php if (isset($errors['supplierphone'])) : ?>
                                <p class="text-red-600 text-sm"><?php echo $errors['supplierphone']; ?></p>
                            <?php else : ?>
                                <?php if ($supplierphone) : ?>
                                    <p class="text-green-600 text-sm"><?php echo "Number is valid."; ?></p>
                                <?php endif; ?>
                            <?php endif; ?>
                        </div>
                        <!-- Address Input -->
                        <div class="flex flex-col space-y-1">
                            <label class="text-xl font-semibold" for="address">Address</label>
                            <input class="p-3 border rounded focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-opacity-50 transition duration-300 ease-in-out <?php echo isset($errors['supplieraddress']) ? 'border-red-500' : 'border-gray-300'; ?>" type="text" name="address" placeholder="Enter the supplier address" value="<?php echo htmlspecialchars($supplier_address) ?>">
                            <?php if (isset($errors['supplieraddress'])) : ?>
                                <p class="text-red-600 text-sm"><?php echo $errors['supplieraddress']; ?></p>
                            <?php else : ?>
                                <?php if ($supplieraddress) : ?>
                                    <p class="text-green-600 text-sm"><?php echo "Address is valid."; ?></p>
                                <?php endif; ?>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- Edit Supllier Button -->
                    <div class="flex justify-end">
                        <input class="bg-indigo-500 text-lg font-semibold text-white px-4 py-2 rounded-md hover:bg-indigo-600 cursor-pointer transition-colors duration-200" type="submit" name="editsupplier" value="Edit Supplier">
                    </div>
                </form>
            </div>
        </div>
    </div>
</body>

</html>