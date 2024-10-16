<?php
session_start();
include('DbConnection.php');
include('components/UpdateImageFunction.php');

// Initialize variables
$fullName = $username = $email = $phone = '';
$errors = [];

// Fetch the current admin data from the database
$adminID = $_SESSION['AdminID'];
$adminQuery = "SELECT * FROM admintb WHERE AdminID = $adminID";
$adminResult = mysqli_query($connect, $adminQuery);
$adminData = mysqli_fetch_assoc($adminResult);

// Handle form submission
if (isset($_POST['modify'])) {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Collect form data
        $fullName = mysqli_real_escape_string($connect, $_POST['AdminFullName']);
        $username = mysqli_real_escape_string($connect, $_POST['AdminUserName']);
        $email = mysqli_real_escape_string($connect, $_POST['AdminEmail']);
        $phone = mysqli_real_escape_string($connect, $_POST['AdminPhone']);
        $position = $_POST['AdminPosition'];

        // Validate Full Name
        if (empty($fullName)) {
            $errors['fullName'] = "Fullname is required.";
        } elseif (preg_match('/\d/', $fullName)) {
            $errors['fullName'] = "Fullname should not contain numbers.";
        }

        // Validate Username
        if (empty($username)) {
            $errors['username'] = "Username is required.";
        } elseif (strlen($username) > 14) {
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

        // Current image from the database
        $currentProfile = $adminData['AdminProfile'];

        // Simulate $_FILES array for three admin images
        $imageFile = $_FILES['AdminProfile'];

        // Upload Profile Image 
        $result = uploadProductImage($imageFile, $currentProfile);
        if (is_array($result)) {
            echo $result1['image'] . "<br>";
        } else {
            $adminProfile = $result;
        }

        if (empty($errors)) {
            // Update the admin profile in the database
            $updateQuery = "
                UPDATE admintb SET
                    AdminFullName = '$fullName',
                    AdminUserName = '$username',
                    AdminEmail = '$email',
                    AdminPhone = '$phone',
                    AdminProfile = '$adminProfile',
                    AdminPosition = '$position'
                WHERE AdminID = $adminID
            ";

            $profile_query = mysqli_query($connect, $updateQuery);

            if ($profile_query) {
                echo "<script>window.alert('Profile is updated.')</script>";
                echo "<script>window.location = 'AdminProfile.php'</script>";
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
    <link rel="stylesheet" href="AdminStyle.css?v=<?php echo time(); ?>">
</head>

<body>
    <div class="flex h-screen">
        <?php include('./components/AdminNav.php'); ?>

        <div class="flex-1 ml-0 md:ml-[250px] p-6 overflow-x-auto">
            <div class="container mx-auto py-3 bg-white">
                <h2 class="text-3xl font-semibold text-indigo-400 mb-6">My Profile</h2>
                <form method="POST" enctype="multipart/form-data">
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                        <div>
                            <!-- Profile Picture Upload -->
                            <div class="flex flex-col items-center mb-6">
                                <div class="flex flex-col sm:flex-row items-center justify-center gap-5">
                                    <div class="w-40 h-40 rounded-full my-3 relative select-none">
                                        <img class="w-full h-full object-cover rounded-full" src="<?php echo htmlspecialchars($adminData['AdminProfile']); ?>" alt="Profile">
                                    </div>
                                    <div class="flex flex-col sm:flex-row items-center justify-center gap-3 sm:gap-5 select-none">
                                        <div>
                                            <label for="AdminProfile" class="bg-indigo-400 text-white px-4 py-2 rounded mb-2 hover:bg-indigo-500 truncate cursor-pointer">Change Profile</label>
                                            <input type="file" id="AdminProfile" name="AdminProfile" class="hidden">
                                        </div>
                                        <a class="border-2 px-4 py-2 rounded-lg flex items-center justify-center gap-2" href='AdminDelete.php?AdminID=<?php echo $adminID; ?>'>
                                            <i class="ri-delete-bin-line text-xl text-red-500"></i>
                                            <p class="text-sm truncate">Delete Account</p>
                                        </a>
                                    </div>
                                </div>
                                <p class="text-sm text-gray-500 mt-2">Recommended size is 160x160px.</p>
                            </div>
                            <div>
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                    <!-- FullName Input -->
                                    <div class="mb-4">
                                        <label for="AdminFullName" class="block text-gray-700 font-medium mb-2">Full Name</label>
                                        <input type="text" id="AdminFullName" name="AdminFullName" value="<?php echo htmlspecialchars($adminData['AdminFullName']); ?>" class="w-full p-3 border rounded focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-opacity-50 transition duration-300 ease-in-out <?php echo isset($errors['fullName']) ? 'border-red-500' : 'border-gray-200'; ?>">
                                        <?php if (isset($errors['fullName'])) : ?>
                                            <p class="text-red-600 text-sm"><?php echo $errors['fullName']; ?></p>
                                        <?php else : ?>
                                            <?php if ($fullName) : ?>
                                                <p class="text-green-600 text-sm"><?php echo "Full Name is valid." ?></p>
                                            <?php endif; ?>
                                        <?php endif; ?>
                                    </div>
                                    <!-- UserName Input -->
                                    <div class="mb-4">
                                        <label for="AdminUserName" class="block text-gray-700 font-medium mb-2">UserName</label>
                                        <input type="text" id="AdminUserName" name="AdminUserName" value="<?php echo htmlspecialchars($adminData['AdminUserName']); ?>" class="w-full p-3 border rounded focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-opacity-50 transition duration-300 ease-in-out <?php echo isset($errors['username']) ? 'border-red-500' : 'border-gray-200'; ?>">
                                        <?php if (isset($errors['username'])) : ?>
                                            <p class=" text-red-600 text-sm"><?php echo $errors['username']; ?></p>
                                        <?php else : ?>
                                            <?php if ($username) : ?>
                                                <p class="text-green-600 text-sm"><?php echo "Username is valid." ?></p>
                                            <?php endif; ?>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                    <!-- Email Input -->
                                    <div class="mb-4">
                                        <label for="AdminEmail" class="block text-gray-700 font-medium mb-2">Email</label>
                                        <input type="email" id="AdminEmail" name="AdminEmail" value="<?php echo htmlspecialchars($adminData['AdminEmail']); ?>" class="w-full p-3 border rounded focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-opacity-50 transition duration-300 ease-in-out <?php echo isset($errors['email']) ? 'border-red-500' : 'border-gray-200'; ?>">
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
                                        <label for="AdminPassword" class="block text-gray-700 font-medium mb-2">Password</label>
                                        <input type="password" id="AdminPassword" name="AdminPassword" value="<?php echo htmlspecialchars($adminData['AdminPassword']); ?>" class="w-full p-3 border rounded focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-opacity-50 transition duration-300 ease-in-out" disabled>
                                        <?php if (isset($errors['password'])) : ?>
                                            <p class="text-red-600 text-sm"><?php echo $errors['password']; ?></p>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                <!-- Phone Input -->
                                <div class="mb-4">
                                    <label for="AdminPhone" class="block text-gray-700 font-medium mb-2">Phone</label>
                                    <input type="text" id="AdminPhone" name="AdminPhone" value="<?php echo htmlspecialchars($adminData['AdminPhone']); ?>" class="w-full p-3 border rounded focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-opacity-50 transition duration-300 ease-in-out <?php echo isset($errors['phone']) ? 'border-red-500' : 'border-gray-200'; ?>">
                                    <?php if (isset($errors['phone'])) : ?>
                                        <p class=" text-red-600 text-sm"><?php echo $errors['phone']; ?></p>
                                    <?php else : ?>
                                        <?php if ($phone) : ?>
                                            <p class="text-green-600 text-sm"><?php echo "Phone is valid." ?></p>
                                        <?php endif; ?>
                                    <?php endif; ?>
                                </div>
                                <!-- Position Input -->
                                <div class="flex flex-col">
                                    <label class="block text-gray-700 font-medium mb-2" for="position">Position</label>
                                    <select name="AdminPosition" id="AdminPosition" class="w-full p-3 border rounded focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-opacity-50 transition duration-300 ease-in-out">
                                        <option value="Administrator" <?php echo $adminData['AdminPosition'] == 'Administrator' ? 'selected' : ''; ?>>Administrator</option>
                                        <option value="Staff" <?php echo $adminData['AdminPosition'] == 'Staff' ? 'selected' : ''; ?>>Staff</option>
                                    </select>
                                </div>
                                <div class="flex justify-end mt-6 select-none">
                                    <button type="submit" name="modify" class="bg-indigo-500 text-white px-6 py-2 rounded hover:bg-indigo-600 focus:outline-none focus:bg-indigo-700 transition duration-300 ease-in-out">Save Changes</button>
                                </div>
                            </div>
                        </div>
                        <!-- Right Side Note and Illustration -->
                        <div class="p-6 rounded-lg mt-0 sm:mt-6 flex flex-col items-center justify-center">
                            <div class="bg-sky-100 p-3 rounded">
                                <h3 class="text-xl font-semibold text-gray-700 mb-2">Build Trust!</h3>
                                <p class="text-gray-600">Your profile picture appears on internal communications and the dashboard, helping others recognize and associate you with your role.</p>
                            </div>
                            <div class="max-w-[500px] select-none">
                                <img src="./AdminImages/account-concept-illustration_114360-409.avif" alt="Illustration" class="w-full h-full object-cover">
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</body>

</html>