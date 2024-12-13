<?php
session_start();

if (!isset($_SESSION['user_logged_in']) || $_SESSION['user_logged_in'] !== true) {
    header("Location: Login.php"); 
    exit;
}

$serverName = "DESKTOP-5QTREIB\SQLEXPRESS";
$connectionOptions = [
    "Database" => "WEBAPP",
    "Uid" => "",
    "PWD" => "",
];
$conn = sqlsrv_connect($serverName, $connectionOptions);
if ($conn == false) {
    die(print_r(sqlsrv_errors(), true));
}

$category = isset($_GET['category']) ? $_GET['category'] : 'ALL';
$search = isset($_GET['search']) ? $_GET['search'] : '';
$programFilter = isset($_GET['program']) ? $_GET['program'] : '';
$lastNameSearch = isset($_GET['last_name_search']) ? $_GET['last_name_search'] : '';
$firstNameSearch = isset($_GET['first_name_search']) ? $_GET['first_name_search'] : '';

$sql = "";
$params = [];
$reportTitle = "";

switch ($category) {
    case 'TITLE':
        $sql = "SELECT T.TITLE_ID, T.TITLE_NAME, T.PROGRAM, A.LAST_NAME, A.FIRST_NAME
            FROM TITLE AS T
            INNER JOIN AUTHOR AS A
            ON T.TITLE_ID = A.TITLE_ID
            INNER JOIN (
                SELECT TITLE_ID, MIN(AUTHOR_ID) AS FIRSTAUTHORID
                FROM AUTHOR
                GROUP BY TITLE_ID
            ) AS FIRSTAUTHORS ON A.TITLE_ID = FIRSTAUTHORS.TITLE_ID AND 
            A.AUTHOR_ID = FIRSTAUTHORS.FIRSTAUTHORID
            WHERE T.TITLE_NAME LIKE ?";
        $params = ["%$search%"];
        $reportTitle = "Thesis Title Report";
        break;

    case 'AUTHOR':
        $sql = "SELECT T.TITLE_ID, T.TITLE_NAME, A.MIDDLE_NAME, A.LAST_NAME, A.FIRST_NAME
            FROM TITLE AS T
            INNER JOIN AUTHOR AS A
            ON T.TITLE_ID = A.TITLE_ID
            WHERE (A.LAST_NAME LIKE ? OR A.LAST_NAME = '') AND A.LAST_NAME != 'N/A'
            AND (A.FIRST_NAME LIKE ? OR A.FIRST_NAME = '')";
        $params = ["%$lastNameSearch%", "%$firstNameSearch%"];
        $reportTitle = "Thesis Author Report";
        break;

    case 'ADVISER':
        $sql = "SELECT T.TITLE_ID, T.TITLE_NAME, A.MIDDLE_NAME, A.LAST_NAME, A.FIRST_NAME
            FROM TITLE AS T
            INNER JOIN ADVISER AS A
            ON T.TITLE_ID = A.TITLE_ID
            INNER JOIN (
                SELECT TITLE_ID, MIN(ADVISER_ID) AS FIRSTADVISERID
                FROM ADVISER
                GROUP BY TITLE_ID
            ) AS FIRSTADVISER ON A.TITLE_ID = FIRSTADVISER.TITLE_ID AND 
            A.ADVISER_ID = FIRSTADVISER.FIRSTADVISERID
            WHERE (A.LAST_NAME LIKE ? OR A.LAST_NAME = '') AND (A.FIRST_NAME LIKE ? OR A.FIRST_NAME = '')";
        $params = ["%$lastNameSearch%", "%$firstNameSearch%"];
        $reportTitle = "Thesis Adviser Report";
        break;

    case 'PROGRAM':
        if ($programFilter != "") {
            $sql = "SELECT T.TITLE_ID, T.TITLE_NAME, T.PROGRAM, A.LAST_NAME, A.FIRST_NAME
                    FROM TITLE AS T
                    INNER JOIN AUTHOR AS A
                    ON T.TITLE_ID = A.TITLE_ID
                    INNER JOIN (
                        SELECT TITLE_ID, MIN(AUTHOR_ID) AS FIRSTAUTHORID
                        FROM AUTHOR
                        GROUP BY TITLE_ID
                    ) AS FIRSTAUTHORS ON A.TITLE_ID = FIRSTAUTHORS.TITLE_ID AND 
                    A.AUTHOR_ID = FIRSTAUTHORS.FIRSTAUTHORID
                    WHERE T.PROGRAM = ?";
            $params = [$programFilter];
            $reportTitle = "Thesis Program Report";
        } else {
            $sql = "SELECT T.TITLE_ID, T.TITLE_NAME, T.PROGRAM, A.LAST_NAME, A.FIRST_NAME
                    FROM TITLE AS T
                    INNER JOIN AUTHOR AS A
                    ON T.TITLE_ID = A.TITLE_ID
                    INNER JOIN (
                        SELECT TITLE_ID, MIN(AUTHOR_ID) AS FIRSTAUTHORID
                        FROM AUTHOR
                        GROUP BY TITLE_ID
                    ) AS FIRSTAUTHORS ON A.TITLE_ID = FIRSTAUTHORS.TITLE_ID AND 
                    A.AUTHOR_ID = FIRSTAUTHORS.FIRSTAUTHORID";
            $params = [];
            $reportTitle = "Thesis Program Report";
        }
        break;

    default: 
        $sql = "SELECT T.TITLE_ID, T.TITLE_NAME, T.PROGRAM, A.LAST_NAME, A.FIRST_NAME
        FROM TITLE AS T
        INNER JOIN AUTHOR AS A
        ON T.TITLE_ID = A.TITLE_ID
        INNER JOIN (
            SELECT TITLE_ID, MIN(AUTHOR_ID) AS FIRSTAUTHORID
            FROM AUTHOR
            GROUP BY TITLE_ID
        ) AS FIRSTAUTHORS ON A.TITLE_ID = FIRSTAUTHORS.TITLE_ID AND 
        A.AUTHOR_ID = FIRSTAUTHORS.FIRSTAUTHORID";
        $params = [];
        $reportTitle = "All Thesis Report";
        break;
}

$result = sqlsrv_query($conn, $sql, $params);

$sqlcount = "SELECT COUNT(DISTINCT T.TITLE_ID) AS totalcount
             FROM TITLE AS T
             INNER JOIN AUTHOR AS A ON T.TITLE_ID = A.TITLE_ID
             INNER JOIN (
                 SELECT TITLE_ID, MIN(AUTHOR_ID) AS FIRSTAUTHORID
                 FROM AUTHOR
                 GROUP BY TITLE_ID
             ) AS FIRSTAUTHORS ON A.TITLE_ID = FIRSTAUTHORS.TITLE_ID 
             AND A.AUTHOR_ID = FIRSTAUTHORS.FIRSTAUTHORID";

if ($category == 'TITLE') {
    $sqlcount .= " WHERE T.TITLE_NAME LIKE ?";
    $params_count = ["%$search%"];
} elseif ($category == 'AUTHOR') {
    $sqlcount = " SELECT COUNT(T.TITLE_ID) AS totalcount
        FROM TITLE AS T
        INNER JOIN AUTHOR AS A ON T.TITLE_ID = A.TITLE_ID
        WHERE (A.LAST_NAME LIKE ? OR A.LAST_NAME = '') AND A.LAST_NAME != 'N/A'
            AND (A.FIRST_NAME LIKE ? OR A.FIRST_NAME = '')";
    $params_count = ["%$lastNameSearch%", "%$firstNameSearch%"];
} elseif ($category == 'ADVISER') {
    $sqlcount = " SELECT COUNT(T.TITLE_ID) AS totalcount
        FROM TITLE AS T
        INNER JOIN ADVISER AS AD ON T.TITLE_ID = AD.TITLE_ID
        WHERE (AD.LAST_NAME LIKE ? OR AD.LAST_NAME = '') AND (AD.FIRST_NAME LIKE ? OR AD.FIRST_NAME = '')";
    $params_count = ["%$lastNameSearch%", "%$firstNameSearch%"];
} elseif ($category == 'PROGRAM' && $programFilter != "") {
    $sqlcount .= " WHERE T.PROGRAM = ?";
    $params_count = [$programFilter];
} elseif ($category == 'PROGRAM' && $programFilter == "") {
    $params_count = [];
} else {
    $params_count = [];
}

if (
    empty($search) && $category == "TITLE") {
    $totalcount = ['totalcount' => 0];
    $result = [];
} else if (empty($lastNameSearch) && ($category == "AUTHOR" || $category == "ADVISER") &&
    (empty($firstNameSearch) && ($category == "AUTHOR" || $category == "ADVISER"))) {
    $totalcount = ['totalcount' => 0];
    $result = [];
} else {
    $result = sqlsrv_query($conn, $sql, $params);
    $countResult = sqlsrv_query($conn, $sqlcount, $params_count);
    $totalcount = sqlsrv_fetch_array($countResult);
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="./styles/reports.css">
    <title><?php echo $reportTitle; ?></title>
</head>
<body>
    <div class="navbar">
        <a href="./Registration.php">Registration</a>
        <a href="./Search.php">Search</a>
        <a href="./SelectReports.php" class="active">Reports</a>
        <a href="./DownloadPage.php">Download</a>
        <a href="./Dashboard.php" style="float: right">Dashboard</a>
    </div>
    <div class="user-control">
        <div class="controls-container">
            <form method="GET" style="text-align: center; margin-top: 10px; width: 100%;">
                <label for="category" style="color: white;">Select Category:</label>
                <select name="category" id="category" onchange="this.form.submit()">
                    <option value="ALL" <?php if ($category == 'ALL') echo 'selected'; ?>>All</option>
                    <option value="TITLE" <?php if ($category == 'TITLE') echo 'selected'; ?>>Title</option>
                    <option value="PROGRAM" <?php if ($category == 'PROGRAM') echo 'selected'; ?>>Program</option>
                    <option value="AUTHOR" <?php if ($category == 'AUTHOR') echo 'selected'; ?>>Author</option>
                    <option value="ADVISER" <?php if ($category == 'ADVISER') echo 'selected'; ?>>Adviser</option>
                </select>
    
                <?php if ($category == 'TITLE'): ?>
                    <input type="text" name="search" id="search" placeholder="Search Title..." value="<?php echo htmlspecialchars($search); ?>" style="margin-top: 10px;">
                <?php elseif ($category == 'ADVISER'): ?>
                    <input type="text" name="last_name_search" style="width: 10%;" placeholder="Last Name" value="<?php echo htmlspecialchars($lastNameSearch);?>" style="margin-top: 10px;">
                    <input type="text" name="first_name_search" style="width: 10%;" placeholder="First Name" value="<?php echo htmlspecialchars($firstNameSearch);?>" style="margin-top: 10px;">
                <?php elseif ($category == 'AUTHOR'):?>
                    <input type="text" name="last_name_search" style="width: 10%;" placeholder="Last Name" value="<?php echo htmlspecialchars($lastNameSearch);?>" style="margin-top: 10px;">
                    <input type="text" name="first_name_search" style="width: 10%;" placeholder="First Name" value="<?php echo htmlspecialchars($firstNameSearch);?>" style="margin-top: 10px;">
                <?php elseif ($category == 'PROGRAM'): ?>
                    <select name="program" id="program" style="margin-top: 10px;">
                        <option value="" <?php if ($programFilter == '') echo 'selected' ?>>All Programs</option>
                        <option value="CPE" <?php if ($programFilter == 'CPE') echo 'selected'; ?>>Computer Engineering</option>
                        <option value="ECE" <?php if ($programFilter == 'ECE') echo 'selected'; ?>>Electronics Engineering</option>
                        <option value="CE" <?php if ($programFilter == 'CE') echo 'selected'; ?>>Civil Engineering</option>
                        <option value="IE" <?php if ($programFilter == 'IE') echo 'selected'; ?>>Industrial Engineering</option>
                        <option value="ME" <?php if ($programFilter == 'ME') echo 'selected'; ?>>Mechanical Engineering</option>
                        <option value="EE" <?php if ($programFilter == 'EE') echo 'selected'; ?>>Electrical Engineering</option>
                        <option value="ARC" <?php if ($programFilter == 'ARC') echo 'selected'; ?>>Architecture</option>
                    </select>
                <?php endif; ?>
                
                <?php if ($category != 'ALL'): ?>
                    <button type="submit" style="margin-top: 10px;">Filter</button>
                    <button type="button" id="resetButton">Reset</button>
                <?php endif; ?>
            </form>
        </div>
        <h4 align="center" class="total-count">Total Results: <?php echo $totalcount['totalcount']; ?></h4>
    </div>
    <div class="report-container">
        <div class="dlsud-logo">
            <img src="./images/dlsud-with-name.png" alt="">
        </div>
        <h1 align="center" style="color: #344e41; margin-bottom: 10px"><?php echo $reportTitle; ?></h1>
        <table>
            <thead>
                <tr>
                <?php if ($totalcount['totalcount'] > 0): ?>
                    <?php
                    switch ($category) {
                        case 'AUTHOR':
                            echo "<th>Author Lastname</th>
                                  <th>Author Firstname</th>
                                  <th>Title ID</th>
                                  <th>Title</th>
                                  <th>Manuscript</th>";
                            break;
                        case 'ADVISER':
                            echo "<th>Adviser Lastname</th>
                                  <th>Adviser Firstname</th>
                                  <th>Title ID</th>
                                  <th>Title</th>
                                  <th>Manuscript</th>";
                            break;
                        case 'PROGRAM':
                            echo "<th>Program</th>
                                  <th>Title ID</th>
                                  <th>Title</th>
                                  <th>Author Lastname</th>
                                  <th>Author Firstname</th>
                                  <th>Manuscript</th>";
                            break;
                        default:
                            echo "<th>Title ID</th>
                                  <th>Title</th>
                                  <th>Program</th>
                                  <th>Author Lastname</th>
                                  <th>Author Firstname</th>
                                  <th>Manuscript</th>";
                            break;
                    }
                    ?>
                <?php endif; ?>
                </tr>
            </thead>
            <tbody>
                
                <?php if (!empty($result)): ?>
                    <?php
                    while ($rows = sqlsrv_fetch_array($result)) {
                        $titleId = $rows['TITLE_ID'];
                        switch ($category) {
                            case 'AUTHOR':
                            case 'ADVISER':
                                echo "<tr>
                                    <td>{$rows['LAST_NAME']}</td>
                                    <td>{$rows['FIRST_NAME']}</td>
                                    <td>{$rows['TITLE_ID']}</td>
                                    <td>{$rows['TITLE_NAME']}</td>
                                    <td><button  onclick=\"window.location.href='Download.php?TITLE_ID=$titleId'\">Download Manuscript</button></td>
                                </tr>";
                                break;
                            case 'PROGRAM':
                                echo "<tr>
                                    <td>{$rows['PROGRAM']}</td>
                                    <td>{$rows['TITLE_ID']}</td>
                                    <td>{$rows['TITLE_NAME']}</td>
                                    <td>{$rows['LAST_NAME']}</td>
                                    <td>{$rows['FIRST_NAME']}</td>
                                    <td><button  onclick=\"window.location.href='Download.php?TITLE_ID=$titleId'\">Download Manuscript</button></td>
                                </tr>";
                                break;
                            default:
                                echo "<tr>
                                    <td>{$rows['TITLE_ID']}</td>
                                    <td>{$rows['TITLE_NAME']}</td>
                                    <td>{$rows['PROGRAM']}</td>
                                    <td>{$rows['LAST_NAME']}</td>
                                    <td>{$rows['FIRST_NAME']}</td>
                                    <td><button  onclick=\"window.location.href='Download.php?TITLE_ID=$titleId'\">Download Manuscript</button></td>
                                </tr>";
                                break;
                        }
                    }
                    ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
    <script>
        document.getElementById('resetButton').addEventListener('click', function() {
            resetFields();
            document.querySelector('form').submit();
        });

        document.getElementById('category').addEventListener('change', function() {
            resetFields();
            document.querySelector('form').submit();
            
        });

        function resetFields() {
            const category = document.getElementById('category').value;

            if (category === 'TITLE') {
                document.querySelector('input[name="search"]').value = '';
            } else if (category === 'AUTHOR' || category === 'ADVISER') {
                document.querySelector('input[name="last_name_search"]').value = '';
                document.querySelector('input[name="first_name_search"]').value = '';
            }else if (category === 'PROGRAM') {
                document.getElementById('program').selectedIndex = 0; 
            }
            
        }
    </script>

</body>
</html>
