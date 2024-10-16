<?php
session_start();
include('DbConnection.php');

// Update Contact Status
if (isset($_GET['ContactID'])) {
    $contactID = intval($_GET['ContactID']);
    $stock_update = "UPDATE cuscontacttb SET Status = 'Responded' WHERE ContactID = '$contactID'";
    $stock_update_query = mysqli_query($connect, $stock_update);
}

// Purchase Search
$SearchQuery = "";
$searchConditions = [];

// Search by Purchase ID, Staff, or Supplier
if (isset($_GET['search'])) {
    $searchTerm = mysqli_real_escape_string($connect, $_GET['search']);
    if (!empty($searchTerm)) {
        $searchConditions[] = "(p.PurchaseID LIKE '%$searchTerm%' OR a.AdminFullName LIKE '%$searchTerm%' OR s.SupplierName LIKE '%$searchTerm%')";
    }
}

// Filter by Status (Pending or Confirmed)
if (isset($_GET['status'])) {
    $status = mysqli_real_escape_string($connect, $_GET['status']);
    if ($status == 'pending' || $status == 'confirmed') {
        $searchConditions[] = "p.Status = '$status'";
    }
}

if (isset($_GET['from_date']) && isset($_GET['to_date'])) {
    $fromDate = mysqli_real_escape_string($connect, $_GET['from_date']);
    $toDate = mysqli_real_escape_string($connect, $_GET['to_date']);
    if (!empty($fromDate) && !empty($toDate)) {
        $searchConditions[] = "p.PurchaseDate BETWEEN '$fromDate' AND '$toDate'";
    }
}

if (!empty($searchConditions)) {
    $SearchQuery = "WHERE " . implode(' AND ', $searchConditions);
}

// Fetch purchase based on search query
$purchaseQuery = "
    SELECT p.PurchaseID, p.AdminID, p.SupplierID, p.TotalAmount, p.PurchaseTax, p.Status, p.PurchaseDate,
           a.AdminFullName, s.SupplierName  
    FROM purchasetb p
    INNER JOIN admintb a ON p.AdminID = a.AdminID
    INNER JOIN suppliertb s ON p.SupplierID = s.SupplierID  
    $SearchQuery
    Order by p.PurchaseID
";
$purchaseResult = mysqli_query($connect, $purchaseQuery);

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
                <h1 class="text-3xl sm:text-4xl font-semibold text-indigo-400">Purchase History</h1>
            </header>

            <div class="bg-white p-0 sm:p-6 rounded shadow overflow-x-auto mt-5">
                <!-- Filter Form -->
                <form method="GET" class="my-4 flex flex-wrap flex-col sm:flex-row gap-1">
                    <!-- General Search Bar -->
                    <div class="w-full sm:w-auto flex flex-col sm:flex-row items-start sm:items-center">
                        <label for="search" class="mb-2 sm:mb-0 sm:ml-4 sm:mr-2 font-semibold">Search:</label>
                        <input type="text" name="search" id="search" placeholder="Search by ID, Staff, Supplier" value="<?php echo isset($_GET['search']) ? $_GET['search'] : ''; ?>" class="border p-2 rounded w-full sm:w-auto">
                    </div>

                    <!-- Filter by Status -->
                    <div class="flex items-center">
                        <label for="status" class="sm:ml-4 sm:mr-2 font-semibold">Filter by Status:</label>
                        <select name="status" id="status" class="border p-2 rounded" onchange="this.form.submit()">
                            <option value="">All Purchases</option>
                            <option value="pending" <?php if (isset($_GET['status']) && $_GET['status'] == 'pending') echo 'selected'; ?>>Pending</option>
                            <option value="confirmed" <?php if (isset($_GET['status']) && $_GET['status'] == 'confirmed') echo 'selected'; ?>>Confirmed</option>
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
                                <th class="px-4 py-2">Purchase ID</th>
                                <th class="px-4 py-2">Staff</th>
                                <th class="px-4 py-2">Supplier</th>
                                <th class="px-4 py-2">Tax</th>
                                <th class="px-4 py-2">Total Amount</th>
                                <th class="px-4 py-2">Status</th>
                                <th class="px-4 py-2">Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($array = mysqli_fetch_assoc($purchaseResult)) { ?>
                                <tr class="border-b hover:bg-gray-50 group relative">
                                    <td class="px-4 py-2"><?php echo htmlspecialchars($array['PurchaseID']); ?></td>
                                    <td class="px-4 py-2"><?php echo htmlspecialchars($array['AdminFullName']); ?></td>
                                    <td class="px-4 py-2"><?php echo htmlspecialchars($array['SupplierName']); ?></td>
                                    <td class="px-4 py-2"><?php echo htmlspecialchars($array['PurchaseTax']); ?></td>
                                    <td class="px-4 py-2"><?php echo htmlspecialchars($array['TotalAmount']); ?></td>
                                    <td class="px-4 py-2"><?php echo htmlspecialchars($array['Status']); ?></td>
                                    <td class="px-4 py-2"><?php echo htmlspecialchars($array['PurchaseDate']); ?></td>
                                    <td class="absolute left-24 px-4 py-2 truncate flex justify-end space-x-2 opacity-0 group-hover:opacity-100 transition-opacity duration-200">
                                        <a class="text-green-500" href='PurchaseDetails.php?PurchaseID=<?php echo $array["PurchaseID"]; ?>'><i class="ri-edit-box-line text-xl"></i></a>
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