<?php
session_start();
include('DbConnection.php');
// Initialize variables
$admin_fullname = $admin_username = $admin_email = $admin_password = $admin_phone = '';
$errors = [];

// Check if the form is submitted
if (isset($_POST['signup'])) {
    // Sanitize form data
    $admin_fullname = mysqli_real_escape_string($connect, $_POST['fullname']);
    $admin_username = mysqli_real_escape_string($connect, $_POST['username']);
    $admin_email = mysqli_real_escape_string($connect, $_POST['email']);
    $admin_password = mysqli_real_escape_string($connect, $_POST['password']);
    $admin_phone = mysqli_real_escape_string($connect, $_POST['phone']);
    $admin_position = $_POST['position'];
    $admin_signup_date = $_POST["signupdate"];

    // Admin image upload 
    $adminProfile = $_FILES["adminprofile"]["name"];
    $copyFile = "AdminImages/";
    $fileName = $copyFile . uniqid() . "_" . $adminProfile;
    $copy = copy($_FILES["adminprofile"]["tmp_name"], $fileName);

    if (!$copy) {
        echo "<p>Cannot upload Profile Image.</p>";
        exit();
    }

    // Validation of fullname
    if (empty($admin_fullname)) {
        $errors['fullname'] = "Fullname is required.";
    } elseif (preg_match('/\d/', $admin_fullname)) {
        $errors['fullname'] = "Fullname should not contain numbers.";
    }

    // Validation of username
    if (empty($admin_username)) {
        $errors['username'] = "Username is required.";
    } elseif (strlen($admin_username) > 14) {
        $errors['username'] = "Username should not over 14 characters.";
    }

    // Validation of email
    if (empty($admin_email)) {
        $errors['email'] = "Email is required.";
    }

    // Validation of password
    if (empty($admin_password)) {
        $errors['password'] = "Password is required.";
    } elseif (strlen($admin_password) < 8) {
        $errors['password'] = "Minimum 8 characters.";
    } elseif (!preg_match('/\d/', $admin_password)) {
        $errors['password'] = "At least 1 number.";
    } elseif (!preg_match('/[A-Z]/', $admin_password)) {
        $errors['password'] = "At least 1 uppercase letter.";
    } elseif (!preg_match('/[a-z]/', $admin_password)) {
        $errors['password'] = "At least 1 lowercase letter.";
    } elseif (!preg_match('@[^\w]@', $admin_password)) {
        $errors['password'] = "At least 1 special character.";
    }

    // Validation of phone
    if (empty($admin_phone)) {
        $errors['phone'] = "Phone number is required.";
    } elseif (!preg_match('/^\d+$/', $admin_phone)) {
        $errors['phone'] = "Phone number is invalid. Only digits are allowed.";
    } elseif (strlen($admin_phone) < 9 || strlen($admin_phone) > 11) {
        $errors['phone'] = "Phone number is invalid.";
    }

    // Insert customer data if no errors
    if (empty($errors)) {
        $check_admin_account = "SELECT * FROM admintb
        WHERE AdminUserName = '$admin_username'
        AND AdminEmail = '$admin_email'";

        $check_admin_account_query = mysqli_query($connect, $check_admin_account);
        $count = mysqli_num_rows($check_admin_account_query);

        // Check if account duplicate
        if ($count > 0) {
            echo "<script>window.alert('An account with Email: $admin_email and Username: $admin_username already exists.')</script>";
            echo "<script>window.location = 'AdminSignUp.php'</script>";
        } else {
            $insert = "INSERT INTO admintb(AdminFullName, AdminUserName, AdminProfile, AdminEmail, AdminPassword, AdminPhone, AdminPosition, SignupDate, AdminStatus)
            VALUE('$admin_fullname', '$admin_username', '$fileName', '$admin_email', '$admin_password', '$admin_phone', '$admin_position', '$admin_signup_date', 'Active')";

            $insert_Query = mysqli_query($connect, $insert);

            if ($insert_Query) {
                echo "<script>window.alert('You have successfully created an account.')</script>";
                echo "<script>window.location = 'AdminSignUp.php'</script>";
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
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="output.css?v=<?php echo time(); ?>">
</head>

<body>
    <section class="flex flex-col items-center justify-center mt-16 mx-auto max-w-lg px-2 mb-3">
        <div class="flex flex-col sm:flex-row justify-around items-center w-full mb-5">
            <h1 class="text-3xl text-indigo-400 font-semibold"><img class="w-36" src="Images/Screenshot 2024-08-18 112444.png" alt="Logo"></h1>
            <h1 class="text-4xl text-red-400 font-semibold">Admin Panel</h1>
        </div>
        <form class="flex flex-col space-y-4 w-full" action="<?php $_SERVER["PHP_SELF"] ?>" method="post" enctype="multipart/form-data">

            <!-- FullName Input -->
            <div class="flex flex-col space-y-1">
                <label class="text-xl font-semibold" for="fullname">Full Name</label>
                <input class="p-2 border rounded focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-opacity-50 transition duration-300 ease-in-out <?php echo isset($errors['fullname']) ? 'border-red-500' : 'border-gray-300'; ?>" type="text" name="fullname" placeholder="Enter your full name" value="<?php echo htmlspecialchars($admin_fullname); ?>">
                <?php if (isset($errors['fullname'])) : ?>
                    <p class="text-red-600 text-sm"><?php echo $errors['fullname']; ?></p>
                <?php else : ?>
                    <?php if ($admin_fullname) : ?>
                        <p class="text-green-600 text-sm"><?php echo "Fullname is valid." ?></p>
                    <?php endif; ?>
                <?php endif; ?>
            </div>

            <!-- Profile -->
            <div>
                <label class="text-xl font-semibold" for="adminprofile">Profile </label>
            </div>
            <div>
                <input type="file" name="adminprofile" id="adminprofile" required><br>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <!-- UserName Input -->
                <div class="flex flex-col space-y-1">
                    <label class="text-xl font-semibold" for="username">Username</label>
                    <input class="p-2 border rounded focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-opacity-50 transition duration-300 ease-in-out <?php echo isset($errors['username']) ? 'border-red-500' : 'border-gray-300'; ?>" type="text" name="username" placeholder="Enter your username" value="<?php echo htmlspecialchars($admin_username); ?>">
                    <?php if (isset($errors['username'])) : ?>
                        <p class=" text-red-600 text-sm"><?php echo $errors['username']; ?></p>
                    <?php else : ?>
                        <?php if ($admin_username) : ?>
                            <p class="text-green-600 text-sm"><?php echo "Username is valid." ?></p>
                        <?php endif; ?>
                    <?php endif; ?>
                </div>
                <!-- Email Input -->
                <div class="flex flex-col space-y-1">
                    <label class="text-xl font-semibold" for="email">Email</label>
                    <input class="p-2 border rounded focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-opacity-50 transition duration-300 ease-in-out <?php echo isset($errors['email']) ? 'border-red-500' : 'border-gray-300'; ?>" type="email" name="email" placeholder="Enter your email" value="<?php echo htmlspecialchars($admin_email); ?>">
                    <?php if (isset($errors['email'])) : ?>
                        <p class="text-red-600 text-sm"><?php echo $errors['email']; ?></p>
                    <?php else : ?>
                        <?php if ($admin_email) : ?>
                            <p class="text-green-600 text-sm"><?php echo "Email is valid." ?></p>
                        <?php endif; ?>
                    <?php endif; ?>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <!-- Password Input -->
                <div class="flex flex-col space-y-1">
                    <label class="text-xl font-semibold" for="password">Password</label>
                    <input class="p-2 border rounded t:text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-opacity-50 transition duration-300 ease-in-out <?php echo isset($errors['password']) ? 'border-red-500' : 'border-gray-300'; ?>" type="password" name="password" placeholder="Enter your password" value="<?php echo htmlspecialchars($admin_password) ?>">
                    <?php if (isset($errors['password'])) : ?>
                        <p class="text-red-600 text-sm"><?php echo $errors['password']; ?></p>
                    <?php else : ?>
                        <?php if ($admin_password) : ?>
                            <p class="text-green-600 text-sm"><?php echo "Password is valid."; ?></p>
                        <?php endif; ?>
                    <?php endif; ?>
                </div>
                <!-- Phone Input -->
                <div class="flex flex-col space-y-1">
                    <label class="text-xl font-semibold" for="phone">Phone</label>
                    <input class="p-2 border rounded focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-opacity-50 transition duration-300 ease-in-out <?php echo isset($errors['phone']) ? 'border-red-500' : 'border-gray-300'; ?>" type="phone" name="phone" placeholder="Enter your phone" value="<?php echo htmlspecialchars($admin_phone) ?>">
                    <?php if (isset($errors['phone'])) : ?>
                        <p class="text-red-600 text-sm"><?php echo $errors['phone']; ?></p>
                    <?php else : ?>
                        <?php if ($admin_phone) : ?>
                            <p class="text-green-600 text-sm"><?php echo "Number is valid."; ?></p>
                        <?php endif; ?>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Position Input -->
            <div class="flex flex-col space-y-1">
                <label class="text-xl font-semibold" for="position">Position</label>
                <select name="position" id="position" class="p-2 border rounded">
                    <option value='Administrator'>Administrator</option>
                    <option value='Staff'>Staff</option>
                </select>
            </div>

            <!-- Date -->
            <div>
                <input type="date" class="hidden" id="signupdate" name="signupdate" value="<?php echo date("Y-m-d") ?>" required>
            </div>

            <div class="flex justify-center">
                <div class="g-recaptcha" data-sitekey="6LcE3G0pAAAAAE1GU9UXBq0POWnQ_1AMwyldy8lX"></div>
                <script type="text/javascript" src="https://www.google.com/recaptcha/api.js" async defer></script>
            </div>

            <!-- Signup Button -->
            <input class="bg-indigo-500 text-lg font-semibold text-white px-4 py-2 rounded-md hover:bg-indigo-600 cursor-pointer transition-colors duration-200" type="submit" name="signup" value="Sign Up">

            <!-- Signin Button -->
            <a href="AdminSignIn.php" class="relative text-center px-4 py-2 rounded-md cursor-pointer overflow-hidden select-none group">
                <p class="relative z-10 text-indigo-500 text-lg font-semibold">Sign In</p>
                <div class="absolute inset-0 rounded-md group-hover-center-fill"></div>
            </a>
        </form>
    </section>
    <a href="User Manual/User Manual (For Admin).pdf" target="_blank" title="Go to User Manual" class="text-slate-500 text-sm font-semibold hover:underline fixed bottom-4 right-4 flex items-center space-x-2 bg-slate-100 p-2 rounded-lg hover:bg-slate-200 transition">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-slate-400" viewBox="0 0 20 20" fill="currentColor">
            <path fill-rule="evenodd" d="M9 2a7 7 0 11-4.546 12.32.75.75 0 01.562-1.388A5.5 5.5 0 109.5 3v1.25a.75.75 0 01-1.5 0V2h.001zM6.25 10a.75.75 0 000 1.5h1.5a.75.75 0 000-1.5H6.25zM6 13.25a.75.75 0 011.5 0V15H9v-1.75a.75.75 0 011.5 0v1.75h1.25a.75.75 0 010 1.5h-1.25v1.25a.75.75 0 01-1.5 0V16.5H7.5v1.25a.75.75 0 01-1.5 0V15H5.25a.75.75 0 010-1.5H7.5v-1.75a.75.75 0 010-1.5H5.25v1.75z" clip-rule="evenodd" />
        </svg>
        User Manual
    </a>
</body>

</html>