<?php 
$serverName = "DESKTOP-5QTREIB\SQLEXPRESS";
$connectionOptions = [
    "Database" => "WEBAPP",
    "Uid" => "",
    "PWD" => "",
];
$conn = sqlsrv_connect($serverName, $connectionOptions);
if ($conn === false) {
    die(print_r(sqlsrv_errors(), true));
}

$search = isset($_GET['search']) ? $_GET['search'] : '';
$params = [];
$totalcount = 0;
$results = [];

if (!empty($search)) {
    // $sql = "SELECT DISTINCT(T.TITLE_ID), T.TITLE_NAME, T.PROGRAM, A.LAST_NAME AS AUTHOR_LAST, A.FIRST_NAME AS AUTHOR_FIRST, AD.LAST_NAME AS ADVISER_LAST, AD.FIRST_NAME AS ADVISER_FIRST
    //         FROM TITLE AS T
    //         LEFT JOIN AUTHOR AS A ON T.TITLE_ID = A.TITLE_ID
    //         LEFT JOIN ADVISER AS AD ON T.TITLE_ID = AD.TITLE_ID
    //         WHERE T.TITLE_NAME LIKE ? OR T.PROGRAM LIKE ? OR A.LAST_NAME LIKE ? OR A.FIRST_NAME LIKE ? OR AD.LAST_NAME LIKE ? OR AD.FIRST_NAME LIKE ?";
    // $params = ["%$search%", "%$search%", "%$search%", "%$search%", "%$search%", "%$search%"];
    // $result = sqlsrv_query($conn, $sql, $params);
    $sql = "WITH RankedResults AS (
        SELECT 
            t.TITLE_ID, 
            t.TITLE_NAME, 
            t.PROGRAM, 
            a.LAST_NAME AS AUTHOR_LAST, 
            a.FIRST_NAME AS AUTHOR_FIRST,
            adv.LAST_NAME AS ADVISER_LAST, 
            adv.FIRST_NAME AS ADVISER_FIRST,
            ROW_NUMBER() OVER (PARTITION BY t.TITLE_ID ORDER BY t.TITLE_ID) AS RowNum
        FROM TITLE t 
        JOIN AUTHOR a ON t.TITLE_ID = a.TITLE_ID 
        LEFT JOIN ADVISER adv ON t.TITLE_ID = adv.TITLE_ID
        WHERE t.TITLE_NAME LIKE ? 
           OR t.PROGRAM LIKE ? 
           OR a.LAST_NAME LIKE ? 
           OR a.FIRST_NAME LIKE ? 
           OR adv.LAST_NAME LIKE ? 
           OR adv.FIRST_NAME LIKE ?
    )
    SELECT * FROM RankedResults WHERE RowNum = 1"; 
    $params = ["%$search%", "%$search%", "%$search%", "%$search%", "%$search%", "%$search%"];
    $result = sqlsrv_query($conn, $sql, $params);

    if ($result === false) {
        die(print_r(sqlsrv_errors(), true));
    }

    while ($row = sqlsrv_fetch_array($result, SQLSRV_FETCH_ASSOC)) {
        $results[] = $row;
    }

    // Count query
    $sqlCount = "SELECT COUNT(T.TITLE_NAME) AS total
                 FROM TITLE AS T
                 LEFT JOIN AUTHOR AS A ON T.TITLE_ID = A.TITLE_ID
                 LEFT JOIN ADVISER AS AD ON T.TITLE_ID = AD.TITLE_ID
                 WHERE T.TITLE_NAME LIKE ? OR A.LAST_NAME LIKE ? OR A.FIRST_NAME LIKE ? OR AD.LAST_NAME LIKE ? OR AD.FIRST_NAME LIKE ?";
    $countResults = sqlsrv_query($conn, $sqlCount, $params);

    if ($countResults === false) {
        die(print_r(sqlsrv_errors(), true));
    }

    $countRow = sqlsrv_fetch_array($countResults, SQLSRV_FETCH_ASSOC);
    $totalcount = $countRow['total'];
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="./styles/search.css">
    <title>Search</title>
</head>
<body>
    <div class="navbar">
        <a href="./Registration.php">Registration</a>
        <a href="./SelectReports.php">Reports</a>
        <a href="./Search.php" class="active">Search</a>
        <a href="./Dashboard.php" style="float: right">Dashboard</a>
    </div>
    <div class="search-container">
        <h1>Search</h1>
        <form method="GET">
            <input type="text" name="search" id="search" placeholder="Search for a Thesis title, Program, or Author's/Adviser's first or last name..." value="<?php echo htmlspecialchars($search); ?>">
            <div class="button-container">
                <button value="submit">Search</button>
                <button type="button" id="resetButton">Reset</button>
            </div>
        </form>
    </div>
    <?php if (!empty($search)): ?>
        <div class="results-container">
            <h1>Results</h1>
            <?php if (empty($results)): ?>
                <p align="center">No results found</p>
            <?php else: ?>
                <table>
                    <thead>
                        <tr>
                            <th>Title Name</th>
                            <th>Program</th>
                            <th>Author (Last, First Name)</th>
                            <th>Adviser (Last, First Name)</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($results as $row): ?>
                            <tr onclick="window.location.href='ViewChapter1.php?TITLE_ID=<?php echo $row['TITLE_ID']; ?>'">
                                <td><?php echo htmlspecialchars($row['TITLE_NAME']); ?></td>
                                <td><?php echo htmlspecialchars($row['PROGRAM']); ?></td>
                                <td><?php echo htmlspecialchars($row['AUTHOR_LAST'] . ', ' . $row['AUTHOR_FIRST']); ?></td>
                                <td><?php echo htmlspecialchars($row['ADVISER_LAST'] . ', ' . $row['ADVISER_FIRST']); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>
    <?php endif; ?>
    <script>
        document.getElementById('resetButton').addEventListener('click', function() {
     
            document.getElementById('search').value = '';
            
            document.querySelector('form').submit();
        });
    </script>
</body>
</html>