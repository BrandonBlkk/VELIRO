<?php
$adminEmail = $_SESSION['AdminEmail'];

$adminSelect = "SELECT * FROM admintb
                WHERE AdminEmail = '$adminEmail'";

$adminSelectQuery = mysqli_query($connect, $adminSelect);
$count = mysqli_num_rows($adminSelectQuery);

for ($i = 0; $i < $count; $i++) {
    $array = mysqli_fetch_array($adminSelectQuery);
    $adminprofile = $array['AdminProfile'];
    $adminusername = $array['AdminUserName'];
    $adminPosition = $array['AdminPosition'];
}

// Count the number of product types
$productTypeCountQuery = "SELECT COUNT(*) as count FROM producttypetb";
$productTypeCountResult = mysqli_query($connect, $productTypeCountQuery);
$productTypeCountRow = mysqli_fetch_assoc($productTypeCountResult);
$productTypeCount = $productTypeCountRow['count'];

$productCountQuery = "SELECT COUNT(*) as count FROM producttb";
$productCountResult = mysqli_query($connect, $productCountQuery);
$productCountRow = mysqli_fetch_assoc($productCountResult);
$productCount = $productCountRow['count'];

// Fetch customer count
$customerCountQuery = "SELECT COUNT(*) as count FROM customertb";
$customerCountResult = mysqli_query($connect, $customerCountQuery);
$customerCountRow = mysqli_fetch_assoc($customerCountResult);
$customerCount = $customerCountRow['count'];

// Fetch suplier count
$supplierCountQuery = "SELECT COUNT(*) as count FROM suppliertb";
$supplierCountResult = mysqli_query($connect, $supplierCountQuery);
$supplierCountRow = mysqli_fetch_assoc($supplierCountResult);
$supplierCount = $supplierCountRow['count'];

// Fetch cuscontact count
$cuscontactCountQuery = "SELECT COUNT(*) as count FROM cuscontacttb WHERE Status = 'Contacted'";
$cuscontactCountResult = mysqli_query($connect, $cuscontactCountQuery);
$cuscontactCountRow = mysqli_fetch_assoc($cuscontactCountResult);
$cuscontactCount = $cuscontactCountRow['count'];

// Fetch cuscontact count
$orderCountQuery = "SELECT COUNT(*) as count FROM ordertb WHERE Status = 'Pending'";
$orderCountResult = mysqli_query($connect, $orderCountQuery);
$orderCountRow = mysqli_fetch_assoc($orderCountResult);
$orderCount = $orderCountRow['count'];
?>

<!-- Hamburger Menu Button -->
<button id="menu-toggle" class="fixed top-4 right-4 z-50 md:hidden p-2 backdrop-blur-sm text-indigo-500 rounded shadow">
    <i class="ri-menu-line text-2xl"></i>
</button>

<!-- Sidebar -->
<nav id="sidebar" class="fixed top-0 left-0 h-full w-64 md:w-[250px] p-4 flex flex-col justify-between bg-white shadow-lg transform -translate-x-full md:translate-x-0 transition-transform duration-300 z-40">
    <div>
        <a class="text-3xl md:text-4xl flex text-indigo-400 font-semibold" href="AdminDashboard.php"><img class="w-36" src="Images/Screenshot 2024-08-18 112444.png" alt="Logo"></a>
        <div class="divide-y-2 divide-slate-100">
            <div x-data="{ open: false }" class="flex flex-col">
                <button @click="open = !open" class="flex items-center gap-2 p-1 rounded">
                    <div class="w-12 h-12 rounded-full my-3 relative select-none">
                        <img class="w-full h-full object-cover rounded-full" src="<?php echo $adminprofile ?>" alt="Profile">
                        <div class="w-3 h-3 bg-green-500 rounded-full absolute bottom-0 right-0"></div>
                    </div>
                    <div class="text-start">
                        <p class="font-semibold"><?php echo $adminusername ?></p>
                        <p class="text-xs text-gray-400">Welcome</p>
                    </div>
                    <i :class="open ? 'ri-arrow-up-s-line' : 'ri-arrow-down-s-line'" class="text-xl ml-auto"></i>
                </button>

                <div x-show="open" x-transition.duration.500ms x-transition:leave.duration.200ms class="pl-5 mb-2">
                    <a href="AdminProfile.php" class="flex items-center gap-3 p-1 rounded hover:bg-slate-100">
                        <i class="ri-user-3-line text-xl"></i>
                        <span>Your profile</span>
                    </a>
                    <a href="AdminSignUp.php" class="flex items-center gap-3 p-1 rounded hover:bg-slate-100">
                        <i class="ri-user-add-line text-xl"></i>
                        <span>Add another account</span>
                    </a>
                </div>
            </div>
            <div class="flex flex-col pt-2">
                <a href="AdminDashboard.php" class="flex items-center gap-4 p-1 rounded hover:bg-slate-100">
                    <i class="ri-dashboard-3-line text-2xl"></i>
                    <span>Dashboard</span>
                </a>
                <?php if ($adminPosition === 'Staff') : ?>
                    <div x-data="{ open: false }" class="flex flex-col">
                        <button @click="open = !open" class="flex items-center gap-4 p-1 rounded hover:bg-slate-100">
                            <i class="ri-shopping-cart-line text-2xl"></i>
                            <span>Purchase Menu</span>
                            <i :class="open ? 'ri-arrow-up-s-line' : 'ri-arrow-down-s-line'" class="text-xl ml-auto"></i>
                        </button>

                        <div x-show="open" x-transition.duration.500ms x-transition:leave.duration.200ms class="pl-8">
                            <a href="PurchaseProduct.php" class="flex items-center gap-4 p-1 rounded hover:bg-slate-100">
                                <i class="ri-truck-line text-xl"></i>
                                <span>Purchase Product</span>
                            </a>
                            <a href="PurchaseHistory.php" class="flex items-center gap-4 p-1 rounded hover:bg-slate-100">
                                <i class="ri-history-line purchase-history-icon text-xl"></i>
                                <span>Purchase History</span>
                            </a>
                        </div>
                    </div>
                <?php endif; ?>

                <!-- Combined Product Section -->
                <?php if ($adminPosition === 'Administrator') : ?>
                    <div x-data="{ open: false }" class="flex flex-col">
                        <button @click="open = !open" class="flex items-center gap-4 p-1 rounded hover:bg-slate-100">
                            <i class="ri-stock-line text-2xl"></i>
                            <span>Inventory</span>
                            <i :class="open ? 'ri-arrow-up-s-line' : 'ri-arrow-down-s-line'" class="text-xl ml-auto"></i>
                        </button>

                        <div x-show="open" x-transition.duration.500ms x-transition:leave.duration.200ms class="pl-8">
                            <a href="AddSupplier.php" class="flex items-center gap-4 p-1 rounded hover:bg-slate-100">
                                <i class="ri-group-line text-xl"></i>
                                <span>Add supplier</span>
                            </a>
                            <a href="AddProducts.php" class="flex items-center gap-4 p-1 rounded hover:bg-slate-100">
                                <i class="ri-shirt-line text-xl"></i>
                                <span>Add product</span>
                            </a>
                            <a href="AddProductType.php" class="flex items-center gap-4 p-1 rounded hover:bg-slate-100">
                                <i class="ri-list-check-3 text-xl"></i>
                                <span>Add product type</span>
                            </a>
                        </div>
                    </div>
                <?php endif; ?>

                <a href="CusOrder.php" class="flex items-center gap-4 p-1 rounded hover:bg-slate-100">
                    <i class="ri-shopping-bag-2-line text-2xl relative">
                        <p class="bg-red-500 rounded-full text-sm text-white w-5 h-5 text-center absolute -top-1 -right-2 select-none <?php echo ($orderCount != 0) ? 'block' : 'hidden'; ?>"><?php echo $orderCount ?></p>
                    </i>
                    <span>Orders</span>
                </a>
                <a href="UserContact.php" class="flex items-center gap-4 p-1 rounded hover:bg-slate-100">
                    <i class="ri-message-3-line text-2xl relative">
                        <p class="bg-red-500 rounded-full text-sm text-white w-5 h-5 text-center absolute -top-1 -right-2 select-none <?php echo ($cuscontactCount != 0) ? 'block' : 'hidden'; ?>"><?php echo $cuscontactCount ?></p>
                    </i>
                    <span>User Contacts</span>
                </a>
            </div>
        </div>
        <script src="//unpkg.com/alpinejs" defer></script>
    </div>
    <div>
        <div class="flex items-center gap-4 p-1 rounded hover:bg-slate-100">
            <i class="ri-logout-circle-line text-2xl"></i>
            <a href="AdminSignOut.php">Sign Out</a>
        </div>
        <p class="text-xs text-gray-400">© <span id="year"></span> VELIRO. Powered by VELIRO</p>
    </div>
</nav>

<div id="overlay" class="fixed inset-0 bg-black opacity-50 hidden z-30"></div>

<script>
    document.getElementById('menu-toggle').addEventListener('click', function() {
        const sidebar = document.getElementById('sidebar');
        const overlay = document.getElementById('overlay');
        if (sidebar.classList.contains('-translate-x-full')) {
            sidebar.classList.remove('-translate-x-full');
            sidebar.classList.add('translate-x-0');
            overlay.classList.remove('hidden');
        } else {
            sidebar.classList.add('-translate-x-full');
            sidebar.classList.remove('translate-x-0');
            overlay.classList.add('hidden');
        }
    });

    document.getElementById('overlay').addEventListener('click', function() {
        document.getElementById('sidebar').classList.add('-translate-x-full');
        document.getElementById('sidebar').classList.remove('translate-x-0');
        this.classList.add('hidden');
    });

    // Get Year
    const getDate = new Date();
    const getYear = getDate.getFullYear();

    document.getElementById('year').textContent = getYear;
</script>