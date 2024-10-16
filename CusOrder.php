<?php
session_start();
include('DbConnection.php');

// Initialize search query
$SearchQuery = "";

// Handle Order Status Search
if (isset($_GET['search'])) {
    $searchTerm = mysqli_real_escape_string($connect, $_GET['search']);
    if ($searchTerm == 'pending' || $searchTerm == 'delivered') {
        $SearchQuery = "WHERE Status LIKE '$searchTerm'";
    }
}

// Handle Order ID, Customer, and Payment Method Search
if (isset($_GET['general_search'])) {
    $generalSearch = mysqli_real_escape_string($connect, $_GET['general_search']);
    if (!empty($generalSearch)) {
        if (!empty($SearchQuery)) {
            $SearchQuery .= " AND (OrderID LIKE '%$generalSearch%' 
                               OR c.FullName LIKE '%$generalSearch%' 
                               OR PaymentMethod LIKE '%$generalSearch%')";
        } else {
            $SearchQuery = "WHERE OrderID LIKE '%$generalSearch%' 
                            OR c.FullName LIKE '%$generalSearch%' 
                            OR PaymentMethod LIKE '%$generalSearch%'";
        }
    }
}

// Handle Date Filter
if (isset($_GET['from_date']) && isset($_GET['to_date'])) {
    $fromDate = mysqli_real_escape_string($connect, $_GET['from_date']);
    $toDate = mysqli_real_escape_string($connect, $_GET['to_date']);

    if (!empty($fromDate) && !empty($toDate)) {
        if (!empty($SearchQuery)) {
            $SearchQuery .= " AND OrderDate BETWEEN '$fromDate' AND '$toDate'";
        } else {
            $SearchQuery = "WHERE OrderDate BETWEEN '$fromDate' AND '$toDate'";
        }
    }
}

// Fetch orders with customer name using JOIN
$orderQuery = "SELECT o.*, c.FullName FROM ordertb o 
               JOIN customertb c ON o.CustomerID = c.CustomerID 
               $SearchQuery";
$orderResult = mysqli_query($connect, $orderQuery);
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
        <?php
        include('./components/AdminNav.php');
        ?>

        <section class="ml-0 md:ml-[250px] flex-1 p-8 overflow-x-auto">
            <header class="mb-6">
                <h1 class="text-3xl sm:text-4xl font-semibold text-indigo-400">Our Customers Orders</h1>
            </header>

            <div class="bg-white p-0 sm:p-6 rounded shadow overflow-x-auto mt-5">
                <!-- Filter Form -->
                <form method="GET" class="my-4 flex flex-wrap flex-col sm:flex-row gap-1">
                    <!-- General Search -->
                    <div class="w-full sm:w-auto flex items-center">
                        <label for="general_search" class="sm:ml-4 sm:mr-2 font-semibold">Search:</label>
                        <input type="text" name="general_search" id="general_search" value="<?php echo isset($_GET['general_search']) ? $_GET['general_search'] : ''; ?>" class="border p-2 rounded w-full sm:w-auto" placeholder="Search by Order ID, Customer, or Payment Method">
                    </div>

                    <!-- Filter by Status -->
                    <div class="flex items-center">
                        <label for="search" class="sm:ml-4 sm:mr-2 font-semibold">Filter by:</label>
                        <select name="search" id="search" class="border p-2 rounded" onchange="this.form.submit()">
                            <option value="">All Orders</option>
                            <option value="pending" <?php if (isset($_GET['search']) && $_GET['search'] == 'pending') echo 'selected'; ?>>Pending</option>
                            <option value="delivered" <?php if (isset($_GET['search']) && $_GET['search'] == 'delivered') echo 'selected'; ?>>Delivered</option>
                        </select>
                    </div>

                    <!-- Date Filter: From -->
                    <div class="w-full sm:w-auto flex flex-col sm:flex-row items-start sm:items-center">
                        <label for="from_date" class="mb-2 sm:mb-0 sm:ml-4 sm:mr-2 font-semibold">From:</label>
                        <input type="date" name="from_date" id="from_date" value="<?php echo isset($_GET['from_date']) ? $_GET['from_date'] : ''; ?>" class="border p-2 rounded w-full sm:w-auto">
                    </div>

                    <!-- Date Filter: To -->
                    <div class="w-full sm:w-auto flex flex-col sm:flex-row items-start sm:items-center">
                        <label for="to_date" class="mb-2 sm:mb-0 sm:ml-4 sm:mr-2 font-semibold">To:</label>
                        <input type="date" name="to_date" id="to_date" value="<?php echo isset($_GET['to_date']) ? $_GET['to_date'] : ''; ?>" class="border p-2 rounded w-full sm:w-auto">
                    </div>

                    <!-- Search Button -->
                    <div class="w-full sm:w-auto flex justify-center sm:justify-start sm:ml-4">
                        <button type="submit" class="w-full sm:w-auto bg-indigo-500 text-white px-4 py-2 rounded hover:bg-indigo-600">
                            Search
                        </button>
                    </div>
                </form>

                <div class="overflow-x-auto">
                    <table class="min-w-full table-auto text-left text-gray-600">
                        <thead class="border-b bg-gray-100">
                            <tr>
                                <th class="px-4 py-2">Order ID</th>
                                <th class="px-4 py-2">Customer</th>
                                <th class="px-4 py-2">Payment Method</th>
                                <th class="px-4 py-2">Total Amount</th>
                                <th class="px-4 py-2">Remark</th>
                                <th class="px-4 py-2">Date</th>
                                <th class="px-4 py-2">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($array = mysqli_fetch_assoc($orderResult)) { ?>
                                <tr class="border-b hover:bg-gray-50 group relative">
                                    <td class="px-4 py-2"><?php echo htmlspecialchars($array['OrderID']); ?></td>
                                    <td class="px-4 py-2"><?php echo htmlspecialchars($array['FullName']); ?></td>
                                    <td class="px-4 py-2"><?php echo htmlspecialchars($array['PaymentMethod']); ?></td>
                                    <td class="px-4 py-2"><?php echo htmlspecialchars($array['TotalAmount']); ?></td>
                                    <td class="px-4 py-2"><?php echo htmlspecialchars($array['Remark']); ?></td>
                                    <td class="px-4 py-2"><?php echo date("F j, Y", strtotime($array['OrderDate'])); ?></td>
                                    <td class="px-4 py-2"><?php echo htmlspecialchars($array['Status']); ?></td>
                                    <td class="absolute left-24 px-4 py-2 truncate flex justify-end space-x-2 opacity-0 group-hover:opacity-100 transition-opacity duration-200">
                                        <a class="text-green-500" href='OrderDetails.php?OrderID=<?php echo urlencode($array["OrderID"]); ?>'><i class="ri-edit-box-line text-xl"></i></a>
                                    </td>
                                </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </section>
    </div>
</body>

</html>