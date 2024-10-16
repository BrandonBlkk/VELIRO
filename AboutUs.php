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

    <section class="flex flex-col gap-10 items-center p-3 pb-10">
        <div class="text-center max-w-[750px]">
            <h1 class="text-4xl sm:text-6xl md:text-7xl font-semibold mb-10">Who we are</h1>
            <p class="text-justify">We believe in a world where you have total freedom to be you, without judgement. To experiment. To express yourself. To be brave and grab life as the extraordinary adventure it is. So we make sure everyone has an equal chance to discover all the amazing things they’re capable of – no matter who they are, where they’re from or what looks they like to boss. We exist to give you the confidence to be whoever you want to be.</p>
        </div>
        <div class="max-w-[850px] select-none">
            <img class="w-full h-full object-cover" src="Images/MicrosoftTeams-image (25).png" alt="Image">
        </div>
        <div class="max-w-[750px]">
            <h1 class="text-2xl md:text-3xl font-semibold mb-4">Fashion democracy - Choice for all</h1>
            <p class="text-justify">Our audience (VELI) is wonderfully unique, and we do everything we can to help you find your fit. We offer our VELIRO Brands in more than 30 sizes – and we're committed to providing all sizes at the same price – so you can be confident we’ve got the perfect thing for you.</p>
        </div>
        <div class="max-w-[850px] select-none">
            <img class="w-full h-full object-cover" src="Images/MicrosoftTeams-image (23).png" alt="Image">
        </div>
        <div class="max-w-[750px]">
            <h1 class="text-2xl md:text-3xl font-semibold mb-4">Body positivity</h1>
            <p class="text-justify">It’s important for us to promote a healthy body image – we’re not about conforming to any stereotypes – so we work with more than 200 models to represent our audience. And we’re not in the business of digitally altering their appearance either… there’s no reshaping or removing stretch marks here. Our models are part of the VELIRO family and we support them by following a Model Welfare Policy. </p>
        </div>
        <div class="max-w-[850px] select-none">
            <img class="w-full h-full object-cover" src="Images/MicrosoftTeams-image (24).png" alt="Image">
        </div>
        <div class="max-w-[750px]">
            <h1 class="text-2xl md:text-3xl font-semibold mb-4">ParalympicsGB partnership</h1>
            <p class="text-justify">We’re more than honoured to be an official partner of the British Paralympic Association, providing the ParalympicsGB team with their formal and ceremonies outfits. Creating bespoke collections that meet the athletes’ needs, as well as making them look and feel great, is a privilege and has improved our learning about designing adaptive clothing.</p>
        </div>
        <div class="max-w-[850px] select-none">
            <img class="w-full h-full object-cover" src="Images/MicrosoftTeams-image (26).png" alt="Image">
        </div>
        <div class="text-center text-lg max-w-[750px]">
            <h1 class="text-2xl md:text-3xl font-semibold mb-4">THE LEGAL BIT</h1>
            <p>Company Name: VELIRO.com </p>
            <p>No (15) Ground floor , 49 lower street, Merchant Rd, 11161 Yangon, Myanmar</p>
            <p>Email Address: mail@veliro.com</p>
            <p>Company Register: Companies House (Myanmar)</p>
            <p>Company Registration Number: 03724191</p>
            <p>Authorised Representative: Min Thu Aung CEO</p>
            <p>VAT number: GB 788 6225 77</p>
        </div>
    </section>

    <?php
    include('./components/Footer.php');
    ?>

    <script src="Customer.js"></script>
</body>

</html>