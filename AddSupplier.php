<?php
session_start();
include('DbConnection.php');

// Initialize variables
$suppliername = $email = $password = $phone = $address = '';
$errors = [];

// Check if the form is submitted
if (isset($_POST['addsupplier'])) {

    $suppliername = mysqli_real_escape_string($connect, $_POST['suppliername']);
    $email = mysqli_real_escape_string($connect, $_POST['email']);
    $password = mysqli_real_escape_string($connect, $_POST['password']);
    $phone = mysqli_real_escape_string($connect, $_POST['phone']);
    $address = mysqli_real_escape_string($connect, $_POST['address']);
    $addeddate = $_POST['addeddate'];

    // Validation of supplier
    if (empty($suppliername)) {
        $errors['suppliername'] = "Supplier is required.";
    }

    // Validation of email
    if (empty($email)) {
        $errors['email'] = "Email is required.";
    }

    // Validation of password
    if (empty($password)) {
        $errors['password'] = "Password is required.";
    } elseif (strlen($password) < 8) {
        $errors['password'] = "Minimum 8 characters.";
    } elseif (!preg_match('/\d/', $password)) {
        $errors['password'] = "At least 1 number.";
    } elseif (!preg_match('/[A-Z]/', $password)) {
        $errors['password'] = "At least 1 uppercase letter.";
    } elseif (!preg_match('/[a-z]/', $password)) {
        $errors['password'] = "At least 1 lowercase letter.";
    } elseif (!preg_match('@[^\w]@', $password)) {
        $errors['password'] = "At least 1 special character.";
    }

    // Validation of phone
    if (empty($phone)) {
        $errors['phone'] = "Phone number is required.";
    } elseif (!preg_match('/^\d+$/', $phone)) {
        $errors['phone'] = "Phone number is invalid. Only digits are allowed.";
    } elseif (strlen($phone) < 9 || strlen($phone) > 11) {
        $errors['phone'] = "Phone number is invalid.";
    }

    // Validation of phone
    if (empty($address)) {
        $errors['address'] = "Address is required.";
    }

    // Insert supplier data if no errors
    if (empty($errors)) {
        $supplier_select = "SELECT * FROM suppliertb 
        WHERE SupplierName = '$suppliername'";

        $supplier_query = mysqli_query($connect, $supplier_select);
        $supplier_query_count = mysqli_num_rows($supplier_query);

        if ($supplier_query_count > 0) {
            echo "<script>window.alert('Supplier is already exists.')</script>";
            echo "<script>window.location = 'AddSupplier.php'</script>";
        } else {
            $supplier_insert = "INSERT INTO suppliertb(SupplierEmail, SupplierName, SupplierPassword, SupplierPhone, SupplierAddress, AddedDate)
            VALUES('$email', '$suppliername', '$password', '$phone', '$address', '$addeddate')";

            $query = mysqli_query($connect, $supplier_insert);

            if ($query) {
                echo "<script>window.alert('Supplier has been successfully added.')</script>";
                echo "<script>window.location = 'AddSupplier.php'</script>";
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
                <h1 class="text-3xl sm:text-4xl text-indigo-400 font-semibold mb-5">Supplier</h1>
                <form class="flex flex-col space-y-4 w-full" action="<?php $_SERVER["PHP_SELF"] ?>" method="post" enctype="multipart/form-data">

                    <!-- Supplier Input -->
                    <div class="flex flex-col space-y-1">
                        <label class="text-xl font-semibold" for="suppliername">Supplier Name</label>
                        <input class="p-3 border rounded focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-opacity-50 transition duration-300 ease-in-out <?php echo isset($errors['suppliername']) ? 'border-red-500' : 'border-gray-200'; ?>" type="text" name="suppliername" placeholder="Enter the supplier name" value="<?php echo htmlspecialchars($suppliername); ?>">
                        <?php if (isset($errors['suppliername'])) : ?>
                            <p class=" text-red-600 text-sm"><?php echo $errors['suppliername']; ?></p>
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
                            <input class="p-3 border rounded focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-opacity-50 transition duration-300 ease-in-out <?php echo isset($errors['email']) ? 'border-red-500' : 'border-gray-200'; ?>" type="email" name="email" placeholder="Enter the supplier email" value="<?php echo htmlspecialchars($email); ?>">
                            <?php if (isset($errors['email'])) : ?>
                                <p class="text-red-600 text-sm"><?php echo $errors['email']; ?></p>
                            <?php else : ?>
                                <?php if ($email) : ?>
                                    <p class="text-green-600 text-sm"><?php echo "Email is valid." ?></p>
                                <?php endif; ?>
                            <?php endif; ?>
                        </div>

                        <!-- Password Input -->
                        <div class="flex flex-col space-y-1">
                            <label class="text-xl font-semibold" for="password">Password</label>
                            <input class="p-3 border rounded t:text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-opacity-50 transition duration-300 ease-in-out <?php echo isset($errors['password']) ? 'border-red-500' : 'border-gray-200'; ?>" type="password" name="password" placeholder="Enter the supplier password" value="<?php echo htmlspecialchars($password) ?>">
                            <?php if (isset($errors['password'])) : ?>
                                <p class="text-red-600 text-sm"><?php echo $errors['password']; ?></p>
                            <?php else : ?>
                                <?php if ($password) : ?>
                                    <p class="text-green-600 text-sm"><?php echo "Password is valid."; ?></p>
                                <?php endif; ?>
                            <?php endif; ?>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <!-- Phone Input -->
                        <div class="flex flex-col space-y-1">
                            <label class="text-xl font-semibold" for="phone">Phone</label>
                            <input class="p-3 border rounded focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-opacity-50 transition duration-300 ease-in-out <?php echo isset($errors['phone']) ? 'border-red-500' : 'border-gray-200'; ?>" type="phone" name="phone" placeholder="Enter the supplier phone" value="<?php echo htmlspecialchars($phone) ?>">
                            <?php if (isset($errors['phone'])) : ?>
                                <p class="text-red-600 text-sm"><?php echo $errors['phone']; ?></p>
                            <?php else : ?>
                                <?php if ($phone) : ?>
                                    <p class="text-green-600 text-sm"><?php echo "Number is valid."; ?></p>
                                <?php endif; ?>
                            <?php endif; ?>
                        </div>
                        <!-- Address Input -->
                        <div class="flex flex-col space-y-1">
                            <label class="text-xl font-semibold" for="address">Address</label>
                            <input class="p-3 border rounded focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-opacity-50 transition duration-300 ease-in-out <?php echo isset($errors['address']) ? 'border-red-500' : 'border-gray-200'; ?>" type="text" name="address" placeholder="Enter the supplier address" value="<?php echo htmlspecialchars($address) ?>">
                            <?php if (isset($errors['address'])) : ?>
                                <p class="text-red-600 text-sm"><?php echo $errors['address']; ?></p>
                            <?php else : ?>
                                <?php if ($address) : ?>
                                    <p class="text-green-600 text-sm"><?php echo "Address is valid."; ?></p>
                                <?php endif; ?>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- Date -->
                    <div>
                        <input type="date" class="hidden" id="addeddate" name="addeddate" value="<?php echo date("Y-m-d") ?>" required>
                    </div>

                    <!-- Add Supplier Button -->
                    <div class="flex justify-end">
                        <input class="bg-indigo-500 text-lg font-semibold text-white px-4 py-2 rounded-md hover:bg-indigo-600 cursor-pointer transition-colors duration-200" type="submit" name="addsupplier" value="Add Supplier">
                    </div>
                </form>
            </div>
        </div>
    </div>
</body>

</html>