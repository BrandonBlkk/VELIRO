<?php
include('DbConnection.php');
// Initialize variables
$fullname = $username = $email = $password = $phone = '';
$errors = [];

// Check if the form is submitted
if (isset($_POST['signup'])) {
    // Sanitize form data
    $fullname = mysqli_real_escape_string($connect, $_POST['fullname']);
    $username = mysqli_real_escape_string($connect, $_POST['username']);
    $email = mysqli_real_escape_string($connect, $_POST['email']);
    $password = mysqli_real_escape_string($connect, $_POST['password']);
    $phone = mysqli_real_escape_string($connect, $_POST['phone']);
    $signup_date = $_POST["signupdate"];
    $birthday = $_POST['birthday'];

    // Validation of fullname
    if (empty($fullname)) {
        $errors['fullname'] = "Fullname is required.";
    } elseif (preg_match('/\d/', $fullname)) {
        $errors['fullname'] = "Fullname should not contain numbers.";
    }

    // Validation of username
    if (empty($username)) {
        $errors['username'] = "Username is required.";
    } elseif (strlen($username) > 14) {
        $errors['username'] = "Username should not over 14 characters.";
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

    // Insert customer data if no errors
    if (empty($errors)) {
        $check_user_account = "SELECT * FROM customertb
        WHERE UserName = '$username'
        AND CustomerEmail = '$email'";

        $check_user_account_query = mysqli_query($connect, $check_user_account);
        $count = mysqli_num_rows($check_user_account_query);

        // Check if account duplicate
        if ($count > 0) {
            echo "<script>window.alert('An account with Email: $email and Username: $username already exists.')</script>";
            echo "<script>window.location = 'SignUp.php'</script>";
        } else {
            $insert = "INSERT INTO customertb(FullName, UserName, CustomerEmail, CustomerPassword, CustomerPhone, CustomerBirthday, SignupDate)
            VALUE('$fullname', '$username', '$email', '$password', '$phone', '$birthday' , '$signup_date')";

            $insert_Query = mysqli_query($connect, $insert);

            if ($insert_Query) {
                echo "<script>window.alert('You have successfully created an account.')</script>";
                echo "<script>window.location = 'SignUp.php'</script>";
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
    <link rel="stylesheet" href="style.css?v=<?php echo time(); ?>">
</head>

<body class="SVG1">
    <section class="flex flex-col items-center justify-center mt-16 mx-auto max-w-lg px-2 mb-3">
        <h1 class="text-4xl text-indigo-400 font-semibold mb-5"><img class="w-36" src="Images/Screenshot 2024-08-18 112444.png" alt="Logo"></h1>
        <form class="flex flex-col space-y-4 w-full" action="<?php $_SERVER["PHP_SELF"] ?>" method="post" enctype="multipart/form-data">

            <!-- FullName Input -->
            <div class="flex flex-col space-y-1">
                <label class="text-xl font-semibold" for="fullname">Full Name</label>
                <input class="p-2 border rounded focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-opacity-50 transition duration-300 ease-in-out <?php echo isset($errors['fullname']) ? 'border-red-500' : 'border-gray-300'; ?>" type="text" name="fullname" placeholder="Enter your full name" value="<?php echo htmlspecialchars($fullname); ?>">
                <?php if (isset($errors['fullname'])) : ?>
                    <p class="text-red-600 text-sm"><?php echo $errors['fullname']; ?></p>
                <?php else : ?>
                    <?php if ($fullname) : ?>
                        <p class="text-green-600 text-sm"><?php echo "Fullname is valid." ?></p>
                    <?php endif; ?>
                <?php endif; ?>
            </div>

            <!-- UserName Input -->
            <div class="flex flex-col space-y-1">
                <label class="text-xl font-semibold" for="username">Username</label>
                <input class="p-2 border rounded focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-opacity-50 transition duration-300 ease-in-out <?php echo isset($errors['username']) ? 'border-red-500' : 'border-gray-300'; ?>" type="text" name="username" placeholder="Enter your username" value="<?php echo htmlspecialchars($username); ?>">
                <?php if (isset($errors['username'])) : ?>
                    <p class=" text-red-600 text-sm"><?php echo $errors['username']; ?></p>
                <?php else : ?>
                    <?php if ($username) : ?>
                        <p class="text-green-600 text-sm"><?php echo "Username is valid." ?></p>
                    <?php endif; ?>
                <?php endif; ?>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <!-- Email Input -->
                <div class="flex flex-col space-y-1">
                    <label class="text-xl font-semibold" for="email">Email</label>
                    <input class="p-2 border rounded focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-opacity-50 transition duration-300 ease-in-out <?php echo isset($errors['email']) ? 'border-red-500' : 'border-gray-300'; ?>" type="email" name="email" placeholder="Enter your email" value="<?php echo htmlspecialchars($email); ?>">
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
                    <input class="p-2 border rounded t:text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-opacity-50 transition duration-300 ease-in-out <?php echo isset($errors['password']) ? 'border-red-500' : 'border-gray-300'; ?>" type="password" name="password" placeholder="Enter your password" value="<?php echo htmlspecialchars($password) ?>">
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
                    <input class="p-2 border rounded focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-opacity-50 transition duration-300 ease-in-out <?php echo isset($errors['phone']) ? 'border-red-500' : 'border-gray-300'; ?>" type="phone" name="phone" placeholder="Enter your phone" value="<?php echo htmlspecialchars($phone) ?>">
                    <?php if (isset($errors['phone'])) : ?>
                        <p class="text-red-600 text-sm"><?php echo $errors['phone']; ?></p>
                    <?php else : ?>
                        <?php if ($phone) : ?>
                            <p class="text-green-600 text-sm"><?php echo "Number is valid."; ?></p>
                        <?php endif; ?>
                    <?php endif; ?>
                </div>
                <!-- Birthday Input -->
                <div class="flex flex-col space-y-1">
                    <label class="text-xl font-semibold" for="birthday">Birthday</label>
                    <input class="p-2 border rounded focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-opacity-50 transition duration-300 ease-in-out" type="date" name="birthday" id="birthday" required><br>
                </div>
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

            <div class="relative my-6">
                <div class="absolute inset-0 flex items-center">
                    <div class="w-full border-t border-gray-300"></div>
                </div>
                <div class="relative flex justify-center text-sm">
                    <span class="px-3 bg-white text-gray-500">Fellow VELIRO?</span>
                </div>
            </div>

            <!-- Signin Button -->
            <a href="SignIn.php" class="relative text-center px-4 py-2 rounded-md cursor-pointer select-none group">
                <p class="relative z-10 text-indigo-500 text-lg font-semibold">Sign In</p>
                <div class="absolute inset-0 rounded-md group-hover:bg-indigo-300 opacity-35 transition-colors duration-300"></div>
            </a>

            <!-- Footer -->
            <ul class="grid grid-cols-3 flex-wrap pt-10 text-slate-400 text-sm">
                <li>
                    <a class="hover:underline" href="FAQ.php">FAQ</a>
                </li>
                <li>
                    <a class="hover:underline" href="CookiesPreferences.php">Cookie Preferences</a>
                </li>
                <li>
                    <a class="hover:underline" href="HelpCenter.php">Help Center</a>
                </li>
                <li>
                    <a class="hover:underline" href="TermsOfUse.php">Terms of Use</a>
                </li>
                <li>
                    <a class="hover:underline" href="Privacy.php">Privacy</a>
                </li>
            </ul>
        </form>
    </section>
    <a href="User Manual/User Manual (For Customer).pdf" target="_blank" title="Go to User Manual" class="text-slate-500 text-sm font-semibold hover:underline fixed bottom-4 right-4 flex items-center space-x-2 bg-slate-100 p-2 rounded-lg hover:bg-slate-200 transition">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-slate-400" viewBox="0 0 20 20" fill="currentColor">
            <path fill-rule="evenodd" d="M9 2a7 7 0 11-4.546 12.32.75.75 0 01.562-1.388A5.5 5.5 0 109.5 3v1.25a.75.75 0 01-1.5 0V2h.001zM6.25 10a.75.75 0 000 1.5h1.5a.75.75 0 000-1.5H6.25zM6 13.25a.75.75 0 011.5 0V15H9v-1.75a.75.75 0 011.5 0v1.75h1.25a.75.75 0 010 1.5h-1.25v1.25a.75.75 0 01-1.5 0V16.5H7.5v1.25a.75.75 0 01-1.5 0V15H5.25a.75.75 0 010-1.5H7.5v-1.75a.75.75 0 010-1.5H5.25v1.75z" clip-rule="evenodd" />
        </svg>
        User Manual
    </a>
</body>

</html>