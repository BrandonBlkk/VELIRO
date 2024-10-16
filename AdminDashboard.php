<?php
session_start();
include('DbConnection.php');

if (!isset($_SESSION["AdminEmail"])) {
    echo "<script>window.alert('Login first! You cannot direct access the admin info.')</script>";
    echo "<script>window.location = 'AdminSignIn.php'</script>";
}

// Product Search
$productSearchQuery = "";
if (isset($_GET['product_search'])) {
    $searchTerm = mysqli_real_escape_string($connect, $_GET['product_search']);
    $productSearchQuery = "WHERE Title LIKE '%$searchTerm%' OR ProductDetail LIKE '%$searchTerm%' OR Brand LIKE '%$searchTerm%'";
}

// Product Type Search
$productTypeSearchQuery = "";
if (isset($_GET['producttype_search'])) {
    $searchTerm = mysqli_real_escape_string($connect, $_GET['producttype_search']);
    $productTypeSearchQuery = "WHERE ProductTypeName LIKE '%$searchTerm%'";
}

// Customer Search
$customerSearchQuery = "";
if (isset($_GET['customer_search'])) {
    $searchTerm = mysqli_real_escape_string($connect, $_GET['customer_search']);
    $customerSearchQuery = "WHERE FullName LIKE '%$searchTerm%' OR CustomerEmail LIKE '%$searchTerm%'";
}

// Supplier Search
$supplierSearchQuery = "";
if (isset($_GET['supplier_search'])) {
    $searchTerm = mysqli_real_escape_string($connect, $_GET['supplier_search']);
    $supplierSearchQuery = "WHERE SupplierName LIKE '%$searchTerm%' OR SupplierEmail LIKE '%$searchTerm%'";
}

// Fetch products based on search query
$query = "SELECT * FROM producttb $productSearchQuery";
$result = mysqli_query($connect, $query);

// Fetch product types based on search query
$productTypeQuery = "SELECT * FROM producttypetb $productTypeSearchQuery";
$productTypeResult = mysqli_query($connect, $productTypeQuery);

// Fetch customers based on search query
$customerQuery = "SELECT * FROM customertb $customerSearchQuery";
$customerResult = mysqli_query($connect, $customerQuery);

// Fetch supplier based on search query
$supplierQuery = "SELECT * FROM suppliertb $supplierSearchQuery";
$supplierResult = mysqli_query($connect, $supplierQuery);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>VELIRO Men's Clothing</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/remixicon/3.5.0/remixicon.css" integrity="sha512-HXXR0l2yMwHDrDyxJbrMD9eLvPe3z3qL3PPeozNTsiHJEENxx8DH2CxmV05iwG0dwoz5n4gQZQyYLUNt1Wdgfg==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link rel="stylesheet" href="AdminStyle.css?v=<?php echo time(); ?>">
</head>

<body>
    <div class="flex h-screen">

        <?php
        include('./components/AdminNav.php')
        ?>

        <div class="ml-0 md:ml-[250px] flex-1 overflow-x-auto">
            <header class="p-8 pb-0">
                <h1 class="text-3xl sm:text-4xl font-semibold text-indigo-400">Welcome to the Admin Dashboard</h1>
            </header>

            <div class="bg-white p-6 pb-0 rounded">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
                    <!-- Product Box -->
                    <a href="#product" class="bg-indigo-50 p-4 rounded-lg shadow hover:bg-indigo-100 transition-colors duration-300">
                        <div class="flex items-center gap-5">
                            <div class="text-center">
                                <h2 class="text-xl font-semibold text-gray-800">Products</h2>
                            </div>
                            <div class="relative select-none">
                                <svg class="w-24 h-24">
                                    <circle class="text-indigo-200" stroke-width="8" stroke="currentColor" fill="transparent" r="36" cx="50%" cy="50%" />
                                    <circle class="text-indigo-600" stroke-width="8" stroke-dasharray="226" stroke-dashoffset="<?php echo 226 - (226 * $productCount / 100); ?>" stroke-linecap="round" stroke="currentColor" fill="transparent" r="36" cx="50%" cy="50%" />
                                </svg>
                                <span class="absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 text-gray-600 text-lg font-medium"><?php echo htmlspecialchars($productCount); ?></span>
                            </div>
                        </div>
                        <p class="text-sm text-gray-500 mt-3">Manage products and view detailed analytics.</p>
                    </a>


                    <!-- Product Type Box -->
                    <a href="#producttype" class="bg-rose-50 p-4 rounded-lg shadow hover:bg-rose-100 transition-colors duration-300">
                        <div class="flex items-center gap-5">
                            <div class="text-center">
                                <h2 class="text-xl font-semibold text-gray-800">Product Types</h2>
                            </div>
                            <div class="relative select-none">
                                <svg class="w-24 h-24">
                                    <circle class="text-rose-200" stroke-width="8" stroke="currentColor" fill="transparent" r="36" cx="50%" cy="50%" />
                                    <circle class="text-rose-600" stroke-width="8" stroke-dasharray="226" stroke-dashoffset="<?php echo 226 - (226 * $productTypeCount / 100); ?>" stroke-linecap="round" stroke="currentColor" fill="transparent" r="36" cx="50%" cy="50%" />
                                </svg>
                                <span class="absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 text-gray-600 text-lg font-medium"><?php echo htmlspecialchars($productTypeCount); ?></span>
                            </div>
                        </div>
                        <p class="text-sm text-gray-500 mt-3">Organize products into categories for better management.</p>
                    </a>

                    <!-- Suppliers Box -->
                    <a href="#suppliers" class="bg-cyan-50 p-4 rounded-lg shadow hover:bg-cyan-100 transition-colors duration-300">
                        <div class="flex items-center gap-5">
                            <div class="text-center">
                                <h2 class="text-xl font-semibold text-gray-800">Suppliers</h2>
                            </div>
                            <div class="relative select-none">
                                <svg class="w-24 h-24">
                                    <circle class="text-cyan-200" stroke-width="8" stroke="currentColor" fill="transparent" r="36" cx="50%" cy="50%" />
                                    <circle class="text-cyan-600" stroke-width="8" stroke-dasharray="226" stroke-dashoffset="<?php echo 226 - (226 * $supplierCount / 100); ?>" stroke-linecap="round" stroke="currentColor" fill="transparent" r="36" cx="50%" cy="50%" />
                                </svg>
                                <span class="absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 text-gray-600 text-lg font-medium"><?php echo htmlspecialchars($supplierCount); ?></span>
                            </div>
                        </div>
                        <p class="text-sm text-gray-500 mt-3">Manage suppliers accounts and track their activity.</p>
                    </a>

                    <div class="lg:col-span-2 flex flex-col gap-6 shadow rounded-lg">
                        <?php
                        // Query to get monthly customer signups
                        $monthlyCustomerSignupsQuery = "
                            SELECT 
                                DATE_FORMAT(SignupDate, '%Y-%m') AS month, 
                                COUNT(*) AS count 
                            FROM customertb 
                            GROUP BY DATE_FORMAT(SignupDate, '%Y-%m')
                            ORDER BY month
                        ";
                        $monthlyCustomerSignupsResult = mysqli_query($connect, $monthlyCustomerSignupsQuery);

                        // Query to get monthly admin signups
                        $monthlyAdminSignupsQuery = "
                            SELECT 
                                DATE_FORMAT(SignupDate, '%Y-%m') AS month, 
                                COUNT(*) AS count 
                            FROM admintb 
                            GROUP BY DATE_FORMAT(SignupDate, '%Y-%m')
                            ORDER BY month
                        ";
                        $monthlyAdminSignupsResult = mysqli_query($connect, $monthlyAdminSignupsQuery);

                        // Query to get monthly supplier signups
                        $monthlySupplierSignupsQuery = "
                            SELECT 
                                DATE_FORMAT(AddedDate, '%Y-%m') AS month, 
                                COUNT(*) AS count 
                            FROM suppliertb 
                            GROUP BY DATE_FORMAT(AddedDate, '%Y-%m')
                            ORDER BY month
                        ";
                        $monthlySupplierSignupsResult = mysqli_query($connect, $monthlySupplierSignupsQuery);

                        // Prepare data for the chart
                        $months = [];
                        $customerSignupCounts = [];
                        $adminSignupCounts = [];
                        $supplierSignupCounts = [];

                        // Process customer signups
                        while ($row = mysqli_fetch_assoc($monthlyCustomerSignupsResult)) {
                            $months[] = $row['month'];
                            $customerSignupCounts[$row['month']] = $row['count'];
                        }

                        // Process admin signups
                        while ($row = mysqli_fetch_assoc($monthlyAdminSignupsResult)) {
                            $adminSignupCounts[$row['month']] = $row['count'];
                        }

                        // Process supplier signups
                        while ($row = mysqli_fetch_assoc($monthlySupplierSignupsResult)) {
                            $supplierSignupCounts[$row['month']] = $row['count'];
                        }

                        // Ensure all months are included in the data arrays
                        $allMonths = array_unique(array_merge(array_keys($customerSignupCounts), array_keys($adminSignupCounts), array_keys($supplierSignupCounts)));
                        sort($allMonths);

                        $customerSignupData = [];
                        $adminSignupData = [];
                        $supplierSignupData = [];

                        foreach ($allMonths as $month) {
                            $customerSignupData[] = isset($customerSignupCounts[$month]) ? $customerSignupCounts[$month] : 0;
                            $adminSignupData[] = isset($adminSignupCounts[$month]) ? $adminSignupCounts[$month] : 0;
                            $supplierSignupData[] = isset($supplierSignupCounts[$month]) ? $supplierSignupCounts[$month] : 0;
                        }

                        // Convert PHP arrays to JavaScript-friendly JSON
                        $monthsJson = json_encode($allMonths);
                        $customerSignupCountsJson = json_encode($customerSignupData);
                        $adminSignupCountsJson = json_encode($adminSignupData);
                        $supplierSignupCountsJson = json_encode($supplierSignupData);
                        ?>

                        <div class="flex justify-center">
                            <!-- Bar Chart Box -->
                            <div class="bg-white p-4 w-full max-w-2xl">
                                <h3 class="text-xl font-semibold text-gray-800 mb-4 text-center">Monthly New Members</h3>
                                <canvas id="barChart" class="w-full h-40"></canvas>
                            </div>
                        </div>
                        <script>
                            document.addEventListener('DOMContentLoaded', function() {
                                let ctx = document.getElementById('barChart').getContext('2d');
                                let barChart = new Chart(ctx, {
                                    type: 'bar',
                                    data: {
                                        labels: <?php echo $monthsJson; ?>, // Month labels
                                        datasets: [{
                                                label: 'Admin',
                                                data: <?php echo $adminSignupCountsJson; ?>,
                                                backgroundColor: 'rgba(153, 102, 255, 0.2)',
                                                borderColor: 'rgba(153, 102, 255, 1)',
                                                borderWidth: 1
                                            },
                                            {
                                                label: 'Supplier',
                                                data: <?php echo $supplierSignupCountsJson; ?>,
                                                backgroundColor: 'rgba(255, 159, 64, 0.2)',
                                                borderColor: 'rgba(255, 159, 64, 1)',
                                                borderWidth: 1
                                            },
                                            {
                                                label: 'Customer',
                                                data: <?php echo $customerSignupCountsJson; ?>,
                                                backgroundColor: 'rgba(75, 192, 192, 0.2)',
                                                borderColor: 'rgba(75, 192, 192, 1)',
                                                borderWidth: 1
                                            }
                                        ]
                                    },
                                    options: {
                                        scales: {
                                            x: {
                                                beginAtZero: true
                                            },
                                            y: {
                                                beginAtZero: true
                                            }
                                        }
                                    }
                                });
                            });
                        </script>
                    </div>

                    <?php
                    // Query to get monthly purchases
                    $monthlyPurchasesQuery = "
                        SELECT 
                            DATE_FORMAT(PurchaseDate, '%Y-%m') AS month, 
                            COUNT(*) AS count 
                        FROM purchasetb 
                        GROUP BY DATE_FORMAT(PurchaseDate, '%Y-%m')
                        ORDER BY month
                    ";
                    $monthlyPurchasesResult = mysqli_query($connect, $monthlyPurchasesQuery);

                    // Query to get monthly orders
                    $monthlyOrdersQuery = "
                        SELECT 
                            DATE_FORMAT(OrderDate, '%Y-%m') AS month, 
                            COUNT(*) AS count 
                        FROM ordertb 
                        GROUP BY DATE_FORMAT(OrderDate, '%Y-%m')
                        ORDER BY month
                    ";
                    $monthlyOrdersResult = mysqli_query($connect, $monthlyOrdersQuery);

                    // Prepare data for the chart
                    $months = [];
                    $purchaseCounts = [];
                    $orderCounts = [];

                    // Process purchases
                    while ($row = mysqli_fetch_assoc($monthlyPurchasesResult)) {
                        $purchaseCounts[$row['month']] = $row['count'];
                    }

                    // Process orders
                    while ($row = mysqli_fetch_assoc($monthlyOrdersResult)) {
                        $orderCounts[$row['month']] = $row['count'];
                    }

                    // Ensure all months are included in the data arrays
                    $allMonths = array_unique(array_merge(
                        array_keys($purchaseCounts),
                        array_keys($orderCounts)
                    ));
                    sort($allMonths);

                    $purchaseData = [];
                    $orderData = [];

                    foreach ($allMonths as $month) {
                        $purchaseData[] = isset($purchaseCounts[$month]) ? $purchaseCounts[$month] : 0;
                        $orderData[] = isset($orderCounts[$month]) ? $orderCounts[$month] : 0;
                    }

                    // Convert PHP arrays to JavaScript-friendly JSON
                    $monthsJson = json_encode($allMonths);
                    $purchaseDataJson = json_encode($purchaseData);
                    $orderDataJson = json_encode($orderData);
                    ?>

                    <div class="flex flex-col gap-6">
                        <!-- Users Box -->
                        <a href="#users" class="bg-green-50 p-4 rounded-lg shadow hover:bg-green-100 transition-colors duration-300">
                            <div class="flex items-center gap-5">
                                <div class="text-center">
                                    <h2 class="text-xl font-semibold text-gray-800">Users</h2>
                                </div>
                                <div class="relative">
                                    <svg class="w-24 h-24">
                                        <circle class="text-green-200" stroke-width="8" stroke="currentColor" fill="transparent" r="36" cx="50%" cy="50%" />
                                        <circle class="text-green-600" stroke-width="8" stroke-dasharray="226" stroke-dashoffset="<?php echo 226 - (226 * $customerCount / 100); ?>" stroke-linecap="round" stroke="currentColor" fill="transparent" r="36" cx="50%" cy="50%" />
                                    </svg>
                                    <span class="absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 text-gray-600 text-lg font-medium"><?php echo htmlspecialchars($customerCount); ?></span>
                                </div>
                            </div>
                            <p class="text-sm text-gray-500 mt-3">Manage user accounts and track their activity.</p>
                        </a>

                        <!-- Monthly Purchases & Orders Line Chart -->
                        <div class="bg-white p-4 rounded-lg shadow">
                            <h3 class="text-xl font-semibold text-gray-800 mb-4 text-center">Monthly Purchases and Orders</h3>
                            <canvas id="lineChart" class="w-full h-40"></canvas>
                        </div>
                    </div>

                    <script>
                        document.addEventListener('DOMContentLoaded', function() {
                            // Line Chart
                            let ctxLine = document.getElementById('lineChart').getContext('2d');
                            let lineChart = new Chart(ctxLine, {
                                type: 'line',
                                data: {
                                    labels: <?php echo $monthsJson; ?>, // Month labels
                                    datasets: [{
                                            label: 'Purchases',
                                            data: <?php echo $purchaseDataJson; ?>,
                                            backgroundColor: 'rgba(255, 159, 64, 0.2)',
                                            borderColor: 'rgba(255, 159, 64, 1)',
                                            borderWidth: 2,
                                            fill: true,
                                        },
                                        {
                                            label: 'Orders',
                                            data: <?php echo $orderDataJson; ?>,
                                            backgroundColor: 'rgba(153, 102, 255, 0.2)',
                                            borderColor: 'rgba(153, 102, 255, 1)',
                                            borderWidth: 2,
                                            fill: true,
                                        }
                                    ]
                                },
                                options: {
                                    scales: {
                                        x: {
                                            beginAtZero: true
                                        },
                                        y: {
                                            beginAtZero: true
                                        }
                                    }
                                }
                            });
                        });
                    </script>
                </div>
            </div>

            <section class="p-6 pt-0">

                <!-- Product Search and Filter -->
                <form method="GET" class="my-4 flex items-center flex-col sm:flex-row gap-2 sm:gap-0">
                    <input type="text" name="product_search" class="p-2 border border-gray-300 rounded-lg w-full md:w-1/2" placeholder="Search for products..." value="<?php echo isset($_GET['product_search']) ? htmlspecialchars($_GET['product_search']) : ''; ?>">
                    <div class="flex items-center">
                        <label for="sort" class="ml-4 mr-2 font-semibold">Filter by:</label>
                        <select name="sort" id="sort" class="border p-2 rounded" onchange="this.form.submit()">
                            <option value="random">All Products</option>
                            <option value="newest" <?php if (isset($_GET['sort']) && $_GET['sort'] == 'newest') echo 'selected'; ?>>Newest First</option>
                            <option value="oldest" <?php if (isset($_GET['sort']) && $_GET['sort'] == 'oldest') echo 'selected'; ?>>Oldest First</option>
                            <option value="lowest" <?php if (isset($_GET['sort']) && $_GET['sort'] == 'lowest') echo 'selected'; ?>>Lowest Price</option>
                            <option value="highest" <?php if (isset($_GET['sort']) && $_GET['sort'] == 'highest') echo 'selected'; ?>>Highest Price</option>
                            <option value="discount" <?php if (isset($_GET['sort']) && $_GET['sort'] == 'discount') echo 'selected'; ?>>Discount</option>
                            <option value="extended_sizing" <?php if (isset($_GET['sort']) && $_GET['sort'] == 'extended_sizing') echo 'selected'; ?>>Extended Sizing</option>
                            <option value="selling_fast" <?php if (isset($_GET['sort']) && $_GET['sort'] == 'selling_fast') echo 'selected'; ?>>Selling Fast</option>
                            <option value="more_colors" <?php if (isset($_GET['sort']) && $_GET['sort'] == 'more_colors') echo 'selected'; ?>>More Colors</option>
                        </select>
                    </div>
                </form>

                <?php
                // Filter products based on selected criteria
                $sortQuery = "";
                if (isset($_GET['sort'])) {
                    switch ($_GET['sort']) {
                        case 'newest':
                            $sortQuery = "ORDER BY AddedDate DESC";
                            break;
                        case 'oldest':
                            $sortQuery = "ORDER BY AddedDate ASC";
                            break;
                        case 'lowest':
                            $sortQuery = "ORDER BY Price ASC";
                            break;
                        case 'highest':
                            $sortQuery = "ORDER BY Price DESC";
                            break;
                        case 'discount':
                            $sortQuery = "ORDER BY DiscountPrice DESC";
                            break;
                        case 'extended_sizing':
                            $sortQuery = "ORDER BY ExtendedSizing DESC";
                            break;
                        case 'selling_fast':
                            $sortQuery = "ORDER BY SellingFast DESC";
                            break;
                        case 'more_colors':
                            $sortQuery = "ORDER BY MoreColors DESC";
                            break;
                        default:
                            $sortQuery = "ORDER BY ProductID ASC";
                            break;
                    }
                }

                // Fetch products based on search query and filter
                $query = "SELECT * FROM producttb $productSearchQuery $sortQuery";
                $result = mysqli_query($connect, $query);

                ?>

                <div class="overflow-auto h-[530px]">
                    <table class="min-w-full table-auto text-left text-gray-600">
                        <thead class="border-b bg-gray-100">
                            <tr id="product" class="bg-indigo-50">
                                <th class="px-4 py-2 text-sm sm:text-base whitespace-nowrap">Product ID</th>
                                <th class="px-4 py-2 text-sm sm:text-base whitespace-nowrap">ProductType ID</th>
                                <th class="px-4 py-2 text-sm sm:text-base whitespace-nowrap">Title</th>
                                <th class="px-4 py-2 text-sm sm:text-base whitespace-nowrap">Img1</th>
                                <th class="px-4 py-2 text-sm sm:text-base whitespace-nowrap">Img2</th>
                                <th class="px-4 py-2 text-sm sm:text-base whitespace-nowrap">Img3</th>
                                <th class="px-4 py-2 text-sm sm:text-base whitespace-nowrap">Stock</th>
                                <th class="px-4 py-2 text-sm sm:text-base whitespace-nowrap">Price</th>
                                <th class="px-4 py-2 text-sm sm:text-base whitespace-nowrap">Discount Price</th>
                                <th class="px-4 py-2 text-sm sm:text-base whitespace-nowrap">Color</th>
                                <th class="px-4 py-2 text-sm sm:text-base whitespace-nowrap">Product Detail</th>
                                <th class="px-4 py-2 text-sm sm:text-base whitespace-nowrap">Brand</th>
                                <th class="px-4 py-2 text-sm sm:text-base whitespace-nowrap">Model Height</th>
                                <th class="px-4 py-2 text-sm sm:text-base whitespace-nowrap">Product Size</th>
                                <th class="px-4 py-2 text-sm sm:text-base whitespace-nowrap">Look After Me</th>
                                <th class="px-4 py-2 text-sm sm:text-base whitespace-nowrap">About Me</th>
                                <th class="px-4 py-2 text-sm sm:text-base whitespace-nowrap">Extended Sizing</th>
                                <th class="px-4 py-2 text-sm sm:text-base whitespace-nowrap">More Colors</th>
                                <th class="px-4 py-2 text-sm sm:text-base whitespace-nowrap">Selling Fast</th>
                                <th class="px-4 py-2 text-sm sm:text-base whitespace-nowrap">Added Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($array = mysqli_fetch_assoc($result)) {
                                if ($array['Stock'] < 5) {
                            ?>
                                    <tr class="border-b bg-red-100 border-2 border-red-500 group relative">
                                        <td class="px-4 py-2 truncate"><?php echo htmlspecialchars($array['ProductID']); ?></td>
                                        <td class="px-4 py-2 truncate"><?php echo htmlspecialchars($array['ProductTypeID']); ?></td>
                                        <td class="px-4 py-2 truncate"><?php echo htmlspecialchars($array['Title']); ?></td>
                                        <td class="px-4 py-2 truncate"><img src="<?php echo htmlspecialchars($array['img1']); ?>" alt="Img1" class="w-16 h-16 object-contain"></td>
                                        <td class="px-4 py-2 truncate"><img src="<?php echo htmlspecialchars($array['img2']); ?>" alt="Img2" class="w-16 h-16 object-contain"></td>
                                        <td class="px-4 py-2 truncate"><img src="<?php echo htmlspecialchars($array['img3']); ?>" alt="Img3" class="w-16 h-16 object-contain"></td>
                                        <td class="px-4 py-2 truncate"><?php echo htmlspecialchars($array['Stock']); ?></td>
                                        <td class="px-4 py-2 truncate"><?php echo htmlspecialchars($array['Price']); ?></td>
                                        <td class="px-4 py-2 truncate"><?php echo htmlspecialchars($array['DiscountPrice']); ?></td>
                                        <td class="px-4 py-2 truncate"><?php echo htmlspecialchars($array['Color']); ?></td>
                                        <td class="px-4 py-2 truncate"><?php echo htmlspecialchars($array['ProductDetail']); ?></td>
                                        <td class="px-4 py-2 truncate"><?php echo htmlspecialchars($array['Brand']); ?></td>
                                        <td class="px-4 py-2 truncate"><?php echo htmlspecialchars($array['ModelHeight']); ?></td>
                                        <td class="px-4 py-2 truncate"><?php echo htmlspecialchars($array['ProductSize']); ?></td>
                                        <td class="px-4 py-2 truncate"><?php echo htmlspecialchars($array['LookAfterMe']); ?></td>
                                        <td class="px-4 py-2 truncate"><?php echo htmlspecialchars($array['AboutMe']); ?></td>
                                        <td class="px-4 py-2 truncate"><?php echo htmlspecialchars($array['ExtendedSizing']); ?></td>
                                        <td class="px-4 py-2 truncate"><?php echo htmlspecialchars($array['MoreColors']); ?></td>
                                        <td class="px-4 py-2 truncate"><?php echo htmlspecialchars($array['SellingFast']); ?></td>
                                        <td class="px-4 py-2 truncate"><?php echo htmlspecialchars($array['AddedDate']); ?></td>
                                        <td class="absolute left-5 px-4 py-2 truncate flex justify-end space-x-2 opacity-0 group-hover:opacity-100 transition-opacity duration-200">
                                            <a href="ProductEdit.php?ProductID=<?php echo $array["ProductID"]; ?>" class="text-green-500">
                                                <i class="ri-edit-box-line text-base sm:text-xl"></i>
                                            </a>
                                            <a href="ProductDelete.php?ProductID=<?php echo $array["ProductID"]; ?>" class="text-red-500">
                                                <i class="ri-delete-bin-4-line text-base sm:text-xl"></i>
                                            </a>
                                        </td>
                                    </tr>
                                <?php
                                } else {
                                ?>
                                    <tr class="border-b hover:bg-gray-50 group relative">
                                        <td class="px-4 py-2 truncate"><?php echo htmlspecialchars($array['ProductID']); ?></td>
                                        <td class="px-4 py-2 truncate"><?php echo htmlspecialchars($array['ProductTypeID']); ?></td>
                                        <td class="px-4 py-2 truncate"><?php echo htmlspecialchars($array['Title']); ?></td>
                                        <td class="px-4 py-2 truncate"><img src="<?php echo htmlspecialchars($array['img1']); ?>" alt="Img1" class="w-16 h-16 object-contain"></td>
                                        <td class="px-4 py-2 truncate"><img src="<?php echo htmlspecialchars($array['img2']); ?>" alt="Img2" class="w-16 h-16 object-contain"></td>
                                        <td class="px-4 py-2 truncate"><img src="<?php echo htmlspecialchars($array['img3']); ?>" alt="Img3" class="w-16 h-16 object-contain"></td>
                                        <td class="px-4 py-2 truncate"><?php echo htmlspecialchars($array['Stock']); ?></td>
                                        <td class="px-4 py-2 truncate"><?php echo htmlspecialchars($array['Price']); ?></td>
                                        <td class="px-4 py-2 truncate"><?php echo htmlspecialchars($array['DiscountPrice']); ?></td>
                                        <td class="px-4 py-2 truncate"><?php echo htmlspecialchars($array['Color']); ?></td>
                                        <td class="px-4 py-2 truncate"><?php echo htmlspecialchars($array['ProductDetail']); ?></td>
                                        <td class="px-4 py-2 truncate"><?php echo htmlspecialchars($array['Brand']); ?></td>
                                        <td class="px-4 py-2 truncate"><?php echo htmlspecialchars($array['ModelHeight']); ?></td>
                                        <td class="px-4 py-2 truncate"><?php echo htmlspecialchars($array['ProductSize']); ?></td>
                                        <td class="px-4 py-2 truncate"><?php echo htmlspecialchars($array['LookAfterMe']); ?></td>
                                        <td class="px-4 py-2 truncate"><?php echo htmlspecialchars($array['AboutMe']); ?></td>
                                        <td class="px-4 py-2 truncate"><?php echo htmlspecialchars($array['ExtendedSizing']); ?></td>
                                        <td class="px-4 py-2 truncate"><?php echo htmlspecialchars($array['MoreColors']); ?></td>
                                        <td class="px-4 py-2 truncate"><?php echo htmlspecialchars($array['SellingFast']); ?></td>
                                        <td class="px-4 py-2 truncate"><?php echo htmlspecialchars($array['AddedDate']); ?></td>
                                        <td class="absolute left-5 px-4 py-2 truncate flex justify-end space-x-2 opacity-0 group-hover:opacity-100 transition-opacity duration-200">
                                            <a class="text-green-500" href="ProductEdit.php?ProductID=<?php echo $array["ProductID"]; ?>">
                                                <i class="ri-edit-box-line text-base sm:text-xl"></i>
                                            </a>
                                            <a class="text-red-500" href="ProductDelete.php?ProductID=<?php echo $array["ProductID"]; ?>">
                                                <i class="ri-delete-bin-4-line text-base sm:text-xl"></i>
                                            </a>
                                        </td>
                                    </tr>
                            <?php
                                }
                            } ?>
                        </tbody>
                    </table>

                </div>

                <!-- Product Type Search -->
                <form method="GET" class="my-4 mt-8">
                    <input type="text" name="producttype_search" class="p-2 border border-gray-300 rounded-lg w-full md:w-1/2" placeholder="Search for product types..." value="<?php echo isset($_GET['producttype_search']) ? htmlspecialchars($_GET['producttype_search']) : ''; ?>">
                </form>

                <div class="overflow-x-auto">
                    <table class="min-w-full table-auto text-left text-gray-600">
                        <thead class="border-b bg-gray-100">
                            <tr id="producttype" class="bg-indigo-50">
                                <th class="px-4 py-2 text-sm sm:text-base">Product Type ID</th>
                                <th class="px-4 py-2 text-sm sm:text-base">Product Type Name</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($typeArray = mysqli_fetch_assoc($productTypeResult)) { ?>
                                <tr class="border-b hover:bg-gray-50 group relative">
                                    <td class="px-4 py-2"><?php echo htmlspecialchars($typeArray['ProductTypeID']); ?></td>
                                    <td class="px-4 py-2"><?php echo htmlspecialchars($typeArray['ProductTypeName']); ?></td>
                                    <td class="absolute left-5 px-4 py-2 flex justify-end space-x-2 opacity-0 group-hover:opacity-100 transition-opacity duration-200">
                                        <a class="text-green-500" href='ProductTypeEdit.php?ProductTypeID=<?php echo $typeArray["ProductTypeID"]; ?>'>
                                            <i class="ri-edit-box-line text-base sm:text-xl"></i>
                                        </a>
                                        <a class="text-red-500" href='ProductTypeDelete.php?ProductTypeID=<?php echo $typeArray["ProductTypeID"]; ?>'>
                                            <i class="ri-delete-bin-4-line text-base sm:text-xl"></i>
                                        </a>
                                    </td>
                                </tr>
                            <?php } ?>
                        </tbody>
                    </table>

                </div>

                <!-- Customer Search -->
                <form method="GET" class="my-4 mt-8">
                    <input type="text" name="customer_search" class="p-2 border border-gray-300 rounded-lg w-full md:w-1/2" placeholder="Search for customers..." value="<?php echo isset($_GET['customer_search']) ? htmlspecialchars($_GET['customer_search']) : ''; ?>">
                </form>

                <div class="overflow-x-auto">
                    <table class="min-w-full table-auto text-left text-gray-600">
                        <thead class="border-b bg-gray-100">
                            <tr id="users" class="bg-indigo-50">
                                <th class="px-4 py-2 text-sm sm:text-base whitespace-nowrap">Customer ID</th>
                                <th class="px-4 py-2 text-sm sm:text-base whitespace-nowrap">Full Name</th>
                                <th class="px-4 py-2 text-sm sm:text-base whitespace-nowrap">User Name</th>
                                <th class="px-4 py-2 text-sm sm:text-base whitespace-nowrap">Customer Email</th>
                                <th class="px-4 py-2 text-sm sm:text-base whitespace-nowrap">Customer Password</th>
                                <th class="px-4 py-2 text-sm sm:text-base whitespace-nowrap">Customer Phone</th>
                                <th class="px-4 py-2 text-sm sm:text-base whitespace-nowrap">Customer Birthday</th>
                                <th class="px-4 py-2 text-sm sm:text-base whitespace-nowrap">Signup Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($cusArray = mysqli_fetch_assoc($customerResult)) { ?>
                                <tr class="border-b hover:bg-gray-50 group relative">
                                    <td class="px-4 py-2 truncate"><?php echo htmlspecialchars($cusArray['CustomerID']); ?></td>
                                    <td class="px-4 py-2 truncate"><?php echo htmlspecialchars($cusArray['FullName']); ?></td>
                                    <td class="px-4 py-2 truncate"><?php echo htmlspecialchars($cusArray['UserName']); ?></td>
                                    <td class="px-4 py-2 truncate"><?php echo htmlspecialchars($cusArray['CustomerEmail']); ?></td>
                                    <td class="px-4 py-2 truncate"><?php echo md5(htmlspecialchars($cusArray['CustomerPassword'])); ?></td>
                                    <td class="px-4 py-2 truncate"><?php echo htmlspecialchars($cusArray['CustomerPhone']); ?></td>
                                    <td class="px-4 py-2 truncate"><?php echo htmlspecialchars($cusArray['CustomerBirthday']); ?></td>
                                    <td class="px-4 py-2 truncate"><?php echo htmlspecialchars($cusArray['SignupDate']); ?></td>
                                    <td class="absolute left-5 px-4 py-2 flex justify-end space-x-2 opacity-0 group-hover:opacity-100 transition-opacity duration-200">
                                        <a class="text-red-500" href='CusDelete.php?CustomerID=<?php echo $cusArray["CustomerID"]; ?>'>
                                            <i class="ri-delete-bin-4-line text-base sm:text-xl"></i>
                                        </a>
                                    </td>
                                </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>

                <!-- Supplier Search -->
                <form method="GET" class="my-4 mt-8">
                    <input type="text" name="supplier_search" class="p-2 border border-gray-300 rounded-lg w-full md:w-1/2" placeholder="Search for suppliers..." value="<?php echo isset($_GET['supplier_search']) ? htmlspecialchars($_GET['supplier_search']) : ''; ?>">
                </form>

                <div class="overflow-x-auto">
                    <table class="min-w-full table-auto text-left text-gray-600">
                        <thead class="border-b bg-gray-100">
                            <tr id="suppliers" class="bg-indigo-50">
                                <th class="px-4 py-2 text-sm sm:text-base whitespace-nowrap">Supplier ID</th>
                                <th class="px-4 py-2 text-sm sm:text-base whitespace-nowrap">Supplier Name</th>
                                <th class="px-4 py-2 text-sm sm:text-base whitespace-nowrap">Supplier Email</th>
                                <th class="px-4 py-2 text-sm sm:text-base whitespace-nowrap">Supplier Password</th>
                                <th class="px-4 py-2 text-sm sm:text-base whitespace-nowrap">Supplier Phone</th>
                                <th class="px-4 py-2 text-sm sm:text-base whitespace-nowrap">Supplier Address</th>
                                <th class="px-4 py-2 text-sm sm:text-base whitespace-nowrap">Added Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($supArray = mysqli_fetch_assoc($supplierResult)) { ?>
                                <tr class="border-b hover:bg-gray-50 group relative">
                                    <td class="px-4 py-2 truncate"><?php echo htmlspecialchars($supArray['SupplierID']); ?></td>
                                    <td class="px-4 py-2 truncate"><?php echo htmlspecialchars($supArray['SupplierName']); ?></td>
                                    <td class="px-4 py-2 truncate"><?php echo htmlspecialchars($supArray['SupplierEmail']); ?></td>
                                    <td class="px-4 py-2 truncate"><?php echo md5(htmlspecialchars($supArray['SupplierPassword'])); ?></td>
                                    <td class="px-4 py-2 truncate"><?php echo htmlspecialchars($supArray['SupplierPhone']); ?></td>
                                    <td class="px-4 py-2 truncate"><?php echo htmlspecialchars($supArray['SupplierAddress']); ?></td>
                                    <td class="px-4 py-2 truncate"><?php echo htmlspecialchars($supArray['AddedDate']); ?></td>
                                    <td class="absolute left-5 px-4 py-2 flex justify-end space-x-2 opacity-0 group-hover:opacity-100 transition-opacity duration-200">
                                        <a class="text-green-500" href='SupplierEdit.php?SupplierID=<?php echo $supArray["SupplierID"]; ?>'>
                                            <i class="ri-edit-box-line text-base sm:text-xl"></i>
                                        </a>
                                        <a class="text-red-500" href='SupplierDelete.php?SupplierID=<?php echo $supArray["SupplierID"]; ?>'>
                                            <i class="ri-delete-bin-4-line text-base sm:text-xl"></i>
                                        </a>
                                    </td>
                                </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
            </section>
        </div>
    </div>
</body>

</html>