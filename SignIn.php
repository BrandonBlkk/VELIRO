<?php
session_start();
include('DbConnection.php');
// Initialize variables
$email = $password = '';
$errors = [];

// Function to sanitize data
function sanitize_input($data)
{
    return htmlspecialchars(trim($data));
}

// Check if the form is submitted
if (isset($_POST['signin'])) {
    // Sanitize form data
    $email = sanitize_input($_POST['email']);
    $password = sanitize_input($_POST['password']);

    // Validation
    if (empty($email)) {
        $errors['email'] = "Email is required.";
    }

    if (empty($password)) {
        $errors['password'] = "Password is required.";
    }

    // Initialize login attempt counter
    if (!isset($_SESSION['login_attempts'])) {
        $_SESSION['login_attempts'] = 0;
    }

    // Login account if no errors
    if (empty($errors)) {
        $email = mysqli_real_escape_string($connect, $_POST['email']);
        $password = mysqli_real_escape_string($connect, $_POST['password']);

        $check_account = "SELECT * FROM customertb
                      WHERE CustomerEmail = '$email'
                      AND CustomerPassword = '$password'";

        $check_account_query = mysqli_query($connect, $check_account);
        $rowCount = mysqli_num_rows($check_account_query);

        // Check customer account match with signup account
        if ($rowCount > 0) {
            $array = mysqli_fetch_array($check_account_query);
            $customer_id = $array["CustomerID"];
            $customer_username = $array["UserName"];
            $customer_email = $array["CustomerEmail"];

            $_SESSION["CustomerID"] = $customer_id;
            $_SESSION["CustomerEmail"] = $customer_email;

            // Reset login attempts on successful login
            $_SESSION['login_attempts'] = 0;

            echo "<script>window.alert('You’ve successfully signed in!')</script>";
            echo "<script>window.location = 'Home.php'</script>";
        } else {

            // Increment login attempt counter
            $_SESSION['login_attempts']++;

            // Check if login attempts exceed limit
            if ($_SESSION['login_attempts'] >= 3) {
                echo "<script>window.alert('You have exceeded the maximum number of login attempts. Please try again later.')</script>";
                echo "<script>window.location = 'WaitingRoom.php'</script>";
                exit; // Stop further execution
            } else {
                echo "<script>window.alert('You’ve failed to sign in.')</script>";
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
<style>

</style>

<body class="SVG1">
    <section class="flex flex-col items-center justify-center mt-36 mx-auto max-w-lg px-2">
        <h1 class="text-4xl text-indigo-400 font-semibold mb-5"><img class="w-36" src="Images/Screenshot 2024-08-18 112444.png" alt="Logo"></h1>
        <form class="flex flex-col space-y-4 w-full" action="<?php $_SERVER["PHP_SELF"] ?>" method="post">

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
                <input class="p-2 border rounded focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-opacity-50 transition duration-300 ease-in-out <?php echo isset($errors['password']) ? 'border-red-500' : 'border-gray-300'; ?>" type="password" name="password" placeholder="Enter your password" value="<?php echo htmlspecialchars($password); ?>">
                <?php if (isset($errors['password'])) : ?>
                    <p class="text-red-600 text-sm"><?php echo $errors['password']; ?></p>
                <?php else : ?>
                    <?php if ($password) : ?>
                        <p class="text-green-600 text-sm"><?php echo "Password is valid."; ?></p>
                    <?php endif; ?>
                <?php endif; ?>
            </div>

            <!-- Signin Button -->
            <input class="bg-indigo-500 text-lg font-semibold text-white px-4 py-2 rounded-md hover:bg-indigo-600 cursor-pointer transition-colors duration-200" type="submit" name="signin" value="Sign In">

            <div class="relative my-6">
                <div class="absolute inset-0 flex items-center">
                    <div class="w-full border-t border-gray-300"></div>
                </div>
                <div class="relative flex justify-center text-sm">
                    <span class="px-3 bg-white text-gray-500">New to VELIRO?</span>
                </div>
            </div>

            <!-- Signup Button -->
            <a href="SignUp.php" class="relative text-center px-4 py-2 rounded-md cursor-pointer select-none group">
                <p class="relative z-10 text-indigo-500 text-lg font-semibold">Sign Up Now</p>
                <div class="absolute inset-0 rounded-md group-hover:bg-indigo-300 opacity-35 transition-colors duration-300"></div>
            </a>

            <!-- Footer -->
            <ul class="grid grid-cols-3 pt-10 text-slate-400 text-sm">
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