<form action="<?php $_SERVER["PHP_SELF"] ?>" method="post" class="relative group">
    <input type="hidden" name="product_Id" value="<?php echo $product_id; ?>">

    <a class="group" href="ProductsDetails.php?product_ID=<?php echo $product_id ?>">
        <div class="relative w-full mb-4 overflow-hidden select-none">
            <img class="w-full h-full object-cover transition-opacity duration-300 opacity-100 group-hover:opacity-0" src="<?php echo $img1 ?>" alt="Image 1">
            <img class="w-full h-full object-cover absolute top-0 left-0 transition-opacity duration-300 opacity-0 group-hover:opacity-100" src="<?php echo $img2 ?>" alt="Image 2">
            <p class="absolute bottom-0 bg-gradient-to-t from-indigo-300 text-white font-bold w-full p-2 <?php echo ($extended_sizing === 'true') ? 'flex' : 'hidden' ?>">EXTENDED SIZING</p>
        </div>
        <div>
            <h1 class="text-sm mb-2 truncate md:whitespace-normal"><?php echo $title ?></h1>

            <?php
            if ($discount_price == 0) {
            ?>
                <div class="mb-2">
                    <p class="font-bold text-sm">$<?php echo $price ?></p>
                </div>
            <?php
            } else {
            ?>
                <div class="mb-2">
                    <span class="text-xs text-gray-500 line-through">$<?php echo $price ?></span>
                    <span class="font-bold text-sm text-red-500">$<?php echo $discount_price ?></span>
                </div>
            <?php
            }
            ?>

            <span class="text-xs text-gray-500 font-bold border border-indigo-400 p-1 mb-1 select-none <?php echo ($more_colors === 'false') ? 'hidden' : 'inline-block' ?>">MORE COLOURS</span>
            <span class="text-xs text-white bg-indigo-500 font-bold border p-1 select-none <?php echo ($selling_fast === 'false') ? 'hidden' : 'inline-block' ?>">SELLING FAST</span>
        </div>
    </a>

    <?php
    $check = "SELECT * FROM favoritetb
    WHERE ProductID = '$product_id'
    And CustomerID = '$id'";

    $check_query = mysqli_query($connect, $check);
    $rowCount = mysqli_num_rows($check_query);

    if ($rowCount > 0) { ?>
        <button type="submit" name="removeBtn">
            <i class="ri-heart-3-fill text-indigo-400 text-xl sm:text-2xl absolute left-1 top-1 flex justify-center items-center bg-black/5 w-8 sm:w-9 h-8 sm:h-9 rounded-full hover:bg-black/10 transition-colors duration-200 cursor-pointer" onclick="toggleFavorite(this)"></i>
        </button>
    <?php } else {
    ?>
        <button type="submit" name="favoriteBtn">
            <i class="ri-heart-3-line text-indigo-400 text-xl sm:text-2xl absolute left-1 top-1 flex justify-center items-center bg-black/5 w-8 sm:w-9 h-8 sm:h-9 rounded-full hover:bg-black/10 transition-colors duration-200 cursor-pointer" onclick="toggleFavorite(this)"></i>
        </button>
    <?php
    }
    ?>

</form>