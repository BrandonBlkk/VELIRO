<?php
session_start();
include('DbConnection.php');

// Check if cutomer singin or not
$id = (isset($_SESSION['CustomerID']) && !empty($_SESSION['CustomerID'])) ? $_SESSION['CustomerID'] : $id = null;

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

    <section class="SVG1 relative mx-4 md:mx-8">
        <div class="h-[601px] select-none">
            <img class="w-full h-full object-cover rounded-bl-[280px] rounded-br-[230px]" src="Images/portrait-young-man-yellow-scene.jpg" alt="Image">
        </div>
        <div class="absolute top-16 md:top-20 left-6 sm:left-10 md:left-20 md:max-w-80">
            <h1 class="text-white text-6xl md:text-8xl mb-9 tracking-wider">Party Wears</h1>
            <p class="text-white mb-8">Stand out with exclusive collection of festive outfits</p>
            <a class="text-indigo-400 font-semibold bg-white py-3 px-8 rounded-md hover:shadow-lg transition-all duration-200 select-none" href="Collections.php">Shop The Collections</a>
        </div>
    </section>

    <section class="mt-20 max-w-[1270px] mx-auto px-4">
        <div class="flex justify-between items-center">
            <h1 class="text-2xl">Latest Collections</h1>
            <a class="relative inline-block group border-2 border-indigo-400 text-indigo-400 font-semibold py-2 px-6 select-none overflow-hidden" href="Collections.php">
                <span class="relative z-10">View All</span>
                <span class="absolute inset-0 w-0 h-full bg-indigo-50 group-hover:w-full transition-all duration-300 ease-out"></span>
            </a>
        </div>
        <div class="grid grid-cols-2 md:grid-cols-4 py-10 gap-4">
            <div class="group cursor-pointer">
                <div class="relative max-w-[300px] mx-auto flex justify-center items-center overflow-hidden">
                    <img class="w-full h-full object-cover select-none" src="Images/206082838-2.webp" alt="Image">
                    <a href="NewArrivals.php" class="absolute -bottom-full bg-gradient-to-t from-indigo-400/65 to-indigo-200/20 w-full text-center text-2xl text-white font-semibold h-full flex items-center justify-center group-hover:bottom-0 transition-all duration-200">Shop Now</a>
                </div>
                <div class="text-center mt-4">
                    <h1 class="font-semibold">ADIDAS ORIGINALS</h1>
                    <p class="text-gray-600">New drops, new shapes</p>
                </div>
            </div>
            <div class="group cursor-pointer">
                <div class="relative max-w-[300px] mx-auto flex justify-center items-center overflow-hidden">
                    <img class="w-full h-full object-cover select-none" src="Images/mw_global_carhartt_moment_870x1110.webp" alt="Image">
                    <a href="Shirts.php" class="absolute -bottom-full bg-gradient-to-t from-indigo-400/65 to-indigo-200/20 w-full text-center text-2xl text-white font-semibold h-full flex items-center justify-center group-hover:bottom-0 transition-all duration-200">Shop Now</a>
                </div>
                <div class="text-center mt-4">
                    <h1 class="font-semibold">CARHARTT WIP</h1>
                    <p class="text-gray-600">Streetwear legends</p>
                </div>
            </div>
            <div class="group cursor-pointer">
                <div class="relative max-w-[300px] mx-auto flex justify-center items-center overflow-hidden">
                    <img class="w-full h-full object-cover select-none" src="Images/206585802-2.webp" alt="Image">
                    <a href="Accessories.php" class="absolute -bottom-full bg-gradient-to-t from-indigo-400/65 to-indigo-200/20 w-full text-center text-2xl text-white font-semibold h-full flex items-center justify-center group-hover:bottom-0 transition-all duration-200">Shop Now</a>
                </div>
                <div class="text-center mt-4">
                    <h1 class="font-semibold">SUMMER ACCESSORIES</h1>
                    <p class="text-gray-600">Vital add-ons</p>
                </div>
            </div>
            <div class="group cursor-pointer">
                <div class="relative max-w-[300px] mx-auto flex justify-center items-center overflow-hidden">
                    <img class="w-full h-full object-cover select-none" src="Images/mw_global_trainers_moment_870x1110.avif" alt="Image">
                    <a href="Shoes.php" class="absolute -bottom-full bg-gradient-to-t from-indigo-400/65 to-indigo-200/20 w-full text-center text-2xl text-white font-semibold h-full flex items-center justify-center group-hover:bottom-0 transition-all duration-200">Shop Now</a>
                </div>
                <div class="text-center mt-4">
                    <h1 class="font-semibold">NEW TRAINERS</h1>
                    <p class="text-gray-600">Unbox a beaut</p>
                </div>
            </div>
        </div>
    </section>

    <section class="SVG3 py-5 px-3 flex justify-center items-center">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-8 md:gap-3">
            <div class="flex flex-col items-center">
                <div class="max-w-[400px] select-none">
                    <img class="w-full h-full object-cover rounded-tl-[280px]" src="Images/portrait-handsome-young-man.jpg" alt="Image">
                </div>
                <div class="text-center mt-2">
                    <h1 class="font-bold text-2xl">JUST DROPPED</h1>
                    <p class="text-sm mb-6">Already trending</p>
                    <a class="border-2 border-indigo-300 font-semibold py-2 px-6 select-none hover:border-indigo-400 transition-colors duration-300" href="NewArrivals.php">Shop Now</a>
                </div>
            </div>
            <div class="flex flex-col items-center">
                <div class="max-w-[400px] select-none">
                    <img class="w-full h-full object-cover rounded-br-[200px]" src="Images/suede-jackets-data.jpg" alt="Image">
                </div>
                <div class="text-center mt-2">
                    <h1 class="font-bold text-2xl">LIGHTWEIGHT JACKETS</h1>
                    <p class="text-sm mb-6">Resort-ready-styles</p>
                    <a class="border-2 border-indigo-300 font-semibold py-2 px-6 select-none hover:border-indigo-400 transition-colors duration-300" href="JacketsAndCoats.php">Shop Now</a>
                </div>
            </div>
        </div>
    </section>

    <section class="text-center py-12 bg-gradient-to-r from-indigo-400 via-violet-300 to-rose-300">
        <div class="mb-5">
            <h1 class="text-white text-3xl font-bold">Fave piece sold out</h1>
            <p class="text-white text-lg">Get back-in stock alerts on the app</p>
        </div>
        <a class="bg-black text-white text-lg px-5 py-2 rounded-full select-none" href="#">Download Now</a>
    </section>

    <section class="max-w-[1270px] mx-auto p-4">
        <h1 class="text-center text-2xl font-semibold">TRENDING BRANDS</h1>
        <div class="grid grid-cols-3 lg:grid-cols-6 mx-0 lg:mx-24 cursor-pointer select-none">
            <div class="max-w-48">
                <img class="w-full h-full" src="Images/tommy.webp" alt="Image">
            </div>
            <div class="max-w-48">
                <img class="w-full h-full" src="Images/north-face.avif" alt="Image">
            </div>
            <div class="max-w-48">
                <img class="w-full h-full" src="Images/adidas.avif" alt="Image">
            </div>
            <div class="max-w-48">
                <img class="w-full h-full" src="Images/carhartt-hp-logos-256x256.avif" alt="Image">
            </div>
            <div class="max-w-48">
                <img class="w-full h-full" src="Images/ellesse-hp-logos-256x256.avif" alt="Image">
            </div>
            <div class="max-w-48">
                <img class="w-full h-full" src="Images/dr-martens-hp-logos-256x256.avif" alt="Image">
            </div>
        </div>
    </section>

    <?php
    include('./components/Newsletter.php')
    ?>

    <?php
    include('./components/Footer.php');
    ?>

    <script src="Customer.js"></script>
</body>

</html>