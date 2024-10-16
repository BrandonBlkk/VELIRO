<?php
session_start();
include('DbConnection.php');
// Initialize variables
$admin_email = $admin_password = '';
$errors = [];

// Function to sanitize data
function sanitize_input($data)
{
    return htmlspecialchars(trim($data));
}

// Check if the form is submitted
if (isset($_POST['signin'])) {
    // Sanitize form data
    $admin_email = sanitize_input($_POST['email']);
    $admin_password = sanitize_input($_POST['password']);

    // Validation
    if (empty($admin_email)) {
        $errors['email'] = "Email is required.";
    }

    if (empty($admin_password)) {
        $errors['password'] = "Password is required.";
    }

    // Login account if no errors
    if (empty($errors)) {
        $check_account = "SELECT * FROM admintb
        WHERE AdminEmail = '$admin_email'
        AND AdminPassword = '$admin_password'";

        $check_account_query = mysqli_query($connect, $check_account);
        $rowCount = mysqli_num_rows($check_account_query);

        // Check admin account match with signup account
        if ($rowCount > 0) {
            $array = mysqli_fetch_array($check_account_query);
            $admin_id = $array["AdminID"];
            $admin_username = $array["AdminUserName"];
            $admin_email = $array["AdminEmail"];
            $admin_position = $array["AdminPosition"];

            // Update admin status to active
            $update_status = "UPDATE admintb SET AdminStatus = 'Active' WHERE AdminID = '$admin_id'";
            mysqli_query($connect, $update_status);

            $_SESSION["AdminID"] = $admin_id;
            $_SESSION["AdminEmail"] = $admin_email;
            $_SESSION["AdminPosition"] = $admin_position;

            echo "<script>window.alert('You’ve successfully signed in!')</script>";
            echo "<script>window.location = 'AdminDashboard.php'</script>";
        } else {
            echo "<script>window.alert('You’ve failed to sign in.')</script>";
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
<style>

</style>

<body>
    <section class="flex flex-col items-center justify-center mt-36 mx-auto max-w-lg px-2">
        <div class="flex flex-col sm:flex-row justify-around items-center w-full mb-5">
            <h1 class="text-3xl text-indigo-400 font-semibold"><img class="w-36" src="Images/Screenshot 2024-08-18 112444.png" alt="Logo"></h1>
            <h1 class="text-4xl text-red-400 font-semibold">Admin Panel</h1>
        </div>
        <form class="flex flex-col space-y-4 w-full" action="<?php $_SERVER["PHP_SELF"] ?>" method="post">

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

            <!-- Signin Button -->
            <input class="bg-indigo-500 text-lg font-semibold text-white px-4 py-2 rounded-md hover:bg-indigo-600 cursor-pointer transition-colors duration-200" type="submit" name="signin" value="Sign In">

            <!-- Signup Button -->
            <a href="AdminSignUp.php" class="relative text-center px-4 py-2 rounded-md cursor-pointer overflow-hidden select-none group">
                <p class="relative z-10 text-indigo-500 text-lg font-semibold">Sign Up Now</p>
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