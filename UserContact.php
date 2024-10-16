<?php
session_start();
include('DbConnection.php');

// Contact Search with Date Filter
$SearchQuery = "";
if (isset($_GET['search']) || isset($_GET['from_date']) || isset($_GET['to_date'])) {
    $conditions = [];

    // Search by status
    if (isset($_GET['search'])) {
        $searchTerm = mysqli_real_escape_string($connect, $_GET['search']);
        if ($searchTerm == 'contacted' || $searchTerm == 'responded') {
            $conditions[] = "Status LIKE '$searchTerm'";
        }
    }

    // Search by date range
    if (!empty($_GET['from_date'])) {
        $fromDate = mysqli_real_escape_string($connect, $_GET['from_date']);
        $conditions[] = "ContactDate >= '$fromDate'";
    }

    if (!empty($_GET['to_date'])) {
        $toDate = mysqli_real_escape_string($connect, $_GET['to_date']);
        $conditions[] = "ContactDate <= '$toDate'";
    }

    // Build final query string based on conditions
    if (count($conditions) > 0) {
        $SearchQuery = "WHERE " . implode(' AND ', $conditions);
    }
}

// Fetch product types based on search query
$contactQuery = "SELECT * FROM cuscontacttb $SearchQuery";
$contactResult = mysqli_query($connect, $contactQuery);
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
                <h1 class="text-3xl sm:text-4xl font-semibold text-indigo-400">What Our Customers are Saying?</h1>
            </header>

            <div class="bg-white p-0 sm:p-6 rounded shadow overflow-x-auto mt-5">
                <!-- Filter Form -->
                <form method="GET" class="my-4 flex flex-wrap flex-col sm:flex-row gap-2 sm:gap-0">
                    <div class="flex items-center">
                        <label for="search" class="ml-4 mr-2 font-semibold">Filter by:</label>
                        <select name="search" id="search" class="border p-2 rounded" onchange="this.form.submit()">
                            <option value="">All Contacts</option>
                            <option value="contacted" <?php if (isset($_GET['search']) && $_GET['search'] == 'contacted') echo 'selected'; ?>>Contacted</option>
                            <option value="responded" <?php if (isset($_GET['search']) && $_GET['search'] == 'responded') echo 'selected'; ?>>Responded</option>
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
                                <th class="px-4 py-2">Contact ID</th>
                                <th class="px-4 py-2">Customer Email</th>
                                <th class="px-4 py-2">Message</th>
                                <th class="px-4 py-2">Date</th>
                                <th class="px-4 py-2">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($array = mysqli_fetch_assoc($contactResult)) { ?>
                                <tr class="border-b hover:bg-gray-50 group relative">
                                    <td class="px-4 py-2"><?php echo htmlspecialchars($array['ContactID']); ?></td>
                                    <td class="px-4 py-2"><?php echo htmlspecialchars($array['CustomerEmail']); ?></td>
                                    <td class="px-4 py-2"><?php echo htmlspecialchars($array['ContactMessage']); ?></td>
                                    <td class="px-4 py-2"><?php echo date("F j, Y", strtotime($array['ContactDate'])); ?></td>
                                    <td class="px-4 py-2"><?php echo htmlspecialchars($array['Status']); ?></td>
                                    <td class="absolute left-5 px-4 py-2 truncate flex justify-end space-x-2 opacity-0 group-hover:opacity-100 transition-opacity duration-200">
                                        <a class="text-green-500" href='UpdateContact.php?ContactID=<?php echo $array["ContactID"]; ?>'><i class="ri-edit-box-line text-xl"></i></a>
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