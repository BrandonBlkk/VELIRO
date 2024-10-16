<?php
session_start();
include('DbConnection.php');

// Check if cutomer singin or not
$id = (isset($_SESSION['CustomerID']) && !empty($_SESSION['CustomerID'])) ? $_SESSION['CustomerID'] : $id = null;

// Initialize variables
$fullname = $userName = $email = $phone = $birthday = '';
$errors = [];

// Fetch the current admin data from the database
$customerID = $_SESSION['CustomerID'];
$customerQuery = "SELECT * FROM customertb WHERE CustomerID = $customerID";
$customerResult = mysqli_query($connect, $customerQuery);
$customerData = mysqli_fetch_assoc($customerResult);

// Handle form submission
if (isset($_POST['modify'])) {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Collect form data
        $fullname = mysqli_real_escape_string($connect, $_POST['FullName']);
        $userName = mysqli_real_escape_string($connect, $_POST['UserName']);
        $email = mysqli_real_escape_string($connect, $_POST['Email']);
        $phone = $_POST['Phone'];
        $birthday = $_POST['Birthday'];

        // Validate Full Name
        if (empty($fullname)) {
            $errors['fullname'] = "Fullname is required.";
        } elseif (preg_match('/\d/', $fullname)) {
            $errors['fullname'] = "Fullname should not contain numbers.";
        }

        // Validate Username
        if (empty($userName)) {
            $errors['username'] = "Username is required.";
        } elseif (strlen($userName) > 14) {
            $errors['username'] = "Username should not exceed 14 characters.";
        }

        // Validate Email
        if (empty($email)) {
            $errors['email'] = "Email is required.";
        }

        // Validate Phone Number
        if (empty($phone)) {
            $errors['phone'] = "Phone number is required.";
        } elseif (!preg_match('/^\d+$/', $phone)) {
            $errors['phone'] = "Phone number is invalid. Only digits are allowed.";
        } elseif (strlen($phone) < 9 || strlen($phone) > 11) {
            $errors['phone'] = "Phone number is invalid.";
        }

        if (empty($errors)) {
            // Update the admin profile in the database
            $updateQuery = "UPDATE customertb SET
                    FullName = '$fullname',
                    UserName = '$userName',
                    CustomerEmail = '$email',
                    CustomerPhone = '$phone',
                    CustomerBirthday = '$birthday'
                WHERE CustomerID = $customerID";

            $profile_query = mysqli_query($connect, $updateQuery);

            if ($profile_query) {
                $_SESSION['alert_message'] = "Your profile is updated.";
                $_SESSION['alert_class'] = "bg-green-200 border-green-400 text-green-800";
            }
        }
        // Redirect to the same page to avoid form resubmission
        header("Location: " . $_SERVER['PHP_SELF']);
        exit;
    }
}

// Update specified product from cart
if (isset($_POST['updateBtn'])) {
    $cart_id = $_POST['cart_id'];
    $new_quantity = $_POST['new_quantity'];

    // Update product quantity in carttb table
    $update_query = "UPDATE carttb SET Quantity = '$new_quantity' WHERE CartID = '$cart_id'";
    $update_result = mysqli_query($connect, $update_query);

    if ($update_result) {
        // Product successfully updated in the cart
        $_SESSION['alert_message'] = "Product has been successfully updated in your cart.";
        $_SESSION['alert_class'] = "bg-green-200 border-green-400 text-green-800";
    }

    // Redirect to the same page to avoid form resubmission
    header("Location: " . $_SERVER['PHP_SELF']);
    exit;
}

// Delete specified product from cart
if (isset($_POST['deleteBtn'])) {
    $cart_id = $_POST['cart_id'];

    // DELETE product based on clicked product
    $cart_delete_query = "DELETE FROM carttb WHERE CartID = '$cart_id'";
    $cart_delete_result = mysqli_query($connect, $cart_delete_query);

    if ($cart_delete_result) {
        // Product successfully deleted from the cart
        $_SESSION['alert_message'] = "Product has been successfully deleted from your cart.";
        $_SESSION['alert_class'] = "bg-green-200 border-green-400 text-green-800";
    }

    // Redirect to the same page to avoid form resubmission
    header("Location: " . $_SERVER['PHP_SELF']);
    exit;
}

// Check for session alert message
$alert_message = isset($_SESSION['alert_message']) ? $_SESSION['alert_message'] : '';
$alert_class = isset($_SESSION['alert_class']) ? $_SESSION['alert_class'] : '';

// Clear the alert message from session after displaying it
if (!empty($alert_message)) {
    unset($_SESSION['alert_message']);
    unset($_SESSION['alert_class']);
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

<body>
    <?php
    include('./components/Header.php');
    ?>

    <section class="max-w-[1300px] mx-auto">
        <div class="flex-1 px-0 sm:px-6 overflow-x-auto">
            <div class="mx-auto py-3 bg-white">
                <h2 class="text-3xl font-semibold text-indigo-400 mb-6">My Profile</h2>
                <form method="POST" enctype="multipart/form-data">
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                        <div>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                <!-- FullName Input -->
                                <div class="mb-4">
                                    <label for="FullName" class="block text-gray-700 font-medium mb-2">Full Name</label>
                                    <input type="text" id="FullName" name="FullName" value="<?php echo htmlspecialchars($customerData['FullName']); ?>" class="w-full p-3 border rounded focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-opacity-50 transition duration-300 ease-in-out <?php echo isset($errors['fullname']) ? 'border-red-500' : 'border-gray-200'; ?>">
                                    <?php if (isset($errors['fullname'])) : ?>
                                        <p class="text-red-600 text-sm"><?php echo $errors['fullname']; ?></p>
                                    <?php else : ?>
                                        <?php if ($fullname) : ?>
                                            <p class="text-green-600 text-sm"><?php echo "Full Name is valid." ?></p>
                                        <?php endif; ?>
                                    <?php endif; ?>
                                </div>
                                <!-- UserName Input -->
                                <div class="flex flex-col space-y-1">
                                    <label class="text-xl font-semibold" for="UserName">Username</label>
                                    <input class="p-3 border rounded focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-opacity-50 transition duration-300 ease-in-out <?php echo isset($errors['username']) ? 'border-red-500' : 'border-gray-200'; ?>" type="text" id="UserName" name="UserName" placeholder="Enter your usernames" value="<?php echo htmlspecialchars($customerData['UserName']); ?>">
                                    <?php if (isset($errors['username'])) : ?>
                                        <p class=" text-red-600 text-sm"><?php echo $errors['username']; ?></p>
                                    <?php else : ?>
                                        <?php if ($userName) : ?>
                                            <p class="text-green-600 text-sm"><?php echo "Username is valid." ?></p>
                                        <?php endif; ?>
                                    <?php endif; ?>
                                </div>
                            </div>

                            <!-- Email Input -->
                            <div class="mb-4">
                                <label for="Email" class="block text-gray-700 font-medium mb-2">Email</label>
                                <input type="email" id="Email" name="Email" value="<?php echo htmlspecialchars($customerData['CustomerEmail']); ?>" class="w-full p-3 border rounded focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-opacity-50 transition duration-300 ease-in-out <?php echo isset($errors['email']) ? 'border-red-500' : 'border-gray-200'; ?>">
                                <?php if (isset($errors['email'])) : ?>
                                    <p class=" text-red-600 text-sm"><?php echo $errors['email']; ?></p>
                                <?php else : ?>
                                    <?php if ($email) : ?>
                                        <p class="text-green-600 text-sm"><?php echo "Email is valid." ?></p>
                                    <?php endif; ?>
                                <?php endif; ?>
                            </div>
                            <!-- Password Input -->
                            <div class="mb-4">
                                <label for="Password" class="block text-gray-700 font-medium mb-2">Password</label>
                                <input type="password" id="Password" name="Password" value="<?php echo htmlspecialchars($customerData['CustomerPassword']); ?>" class="w-full p-3 border rounded focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-opacity-50 transition duration-300 ease-in-out" disabled>
                                <?php if (isset($errors['password'])) : ?>
                                    <p class="text-red-600 text-sm"><?php echo $errors['password']; ?></p>
                                <?php endif; ?>
                            </div>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                <!-- Phone Input -->
                                <div class="mb-4">
                                    <label for="Phone" class="block text-gray-700 font-medium mb-2">Phone</label>
                                    <input type="text" id="Phone" name="Phone" value="<?php echo htmlspecialchars($customerData['CustomerPhone']); ?>" class="w-full p-3 border rounded focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-opacity-50 transition duration-300 ease-in-out <?php echo isset($errors['phone']) ? 'border-red-500' : 'border-gray-200'; ?>">
                                    <?php if (isset($errors['phone'])) : ?>
                                        <p class=" text-red-600 text-sm"><?php echo $errors['phone']; ?></p>
                                    <?php else : ?>
                                        <?php if ($phone) : ?>
                                            <p class="text-green-600 text-sm"><?php echo "Phone is valid." ?></p>
                                        <?php endif; ?>
                                    <?php endif; ?>
                                </div>
                                <!-- Birthday Input -->
                                <div class="flex flex-col space-y-1">
                                    <label class="text-xl font-semibold" for="Birthday">Birthday</label>
                                    <input class="p-2 border rounded focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-opacity-50 transition duration-300 ease-in-out" type="date" name="Birthday" id="Birthday" value="<?php echo htmlspecialchars($customerData['CustomerBirthday']); ?>">
                                </div>
                            </div>
                            <div class="flex items-center justify-end gap-3 select-none">
                                <a class="border-2 px-4 py-2 rounded-lg flex items-center justify-center gap-2" href='CustomerDelete.php?CustomerID=<?php echo $customerID; ?>'>
                                    <i class="ri-delete-bin-line text-xl text-red-500"></i>
                                    <p class="text-sm">Delete Account</p>
                                </a>

                                <div class="flex">
                                    <button type="submit" name="modify" class="bg-indigo-500 text-white px-6 py-2 rounded hover:bg-indigo-600 focus:outline-none focus:bg-indigo-700 transition duration-300 ease-in-out">Save Changes</button>
                                </div>
                            </div>
                        </div>
                        <!-- Right Side Note and Illustration -->
                        <div class="px-0 sm:px-6 rounded-lg flex flex-col items-center justify-center">
                            <div class="bg-sky-100 p-3 rounded">
                                <h3 class="text-xl font-semibold text-gray-700 mb-2">Build Trust!</h3>
                                <p class="text-gray-600">Your profile is displayed on your account and in communications, making it easy for others to recognize and connect with you.</p>
                            </div>
                            <div class="max-w-[500px] select-none">
                                <img src="./AdminImages/account-concept-illustration_114360-409.avif" alt="Illustration" class="w-full h-full object-cover">
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </section>

    <?php
    include('./components/Footer.php');
    ?>

    <script src="Customer.js"></script>
</body>

</html>