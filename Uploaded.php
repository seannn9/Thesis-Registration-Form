<?php
$finfo = new Finfo(FILEINFO_MIME_TYPE);
$mime_type = $finfo -> file($_FILES['files']['tmp_name']);
$mime_type_manu = $finfo -> file($_FILES['manuscript']['tmp_name']);

$mime_types = ["application/pdf"];

$extErr = '';

if (!in_array($_FILES['files']['type'], $mime_types) || !in_array($_FILES['manuscript']['type'], $mime_types)) {
    $extErr = "File type is not supported. Please upload a PDF file";
    // exit($extErr);
} else {
    // chapter 1
    $filename = $_FILES['files']['name'];
    $filesize = $_FILES['files']['size'];
    $destination = __DIR__."/uploads/chapter1/".$filename;

    // manuscript
    $filenameManu = $_FILES['manuscript']['name'];
    $filesizeManu = $_FILES['manuscript']['size'];
    $destinationManu = __DIR__."/uploads/manuscript/".$filenameManu;
    
    move_uploaded_file($_FILES['files']['tmp_name'], $destination);
    move_uploaded_file($_FILES['manuscript']['tmp_name'], $destinationManu);
    
    if ($_FILES['files']['error'] == 0 && $_FILES['manuscript']['error'] == 0) {
        // echo 'Upload Success';
        $serverName = "DESKTOP-5QTREIB\SQLEXPRESS";
        $connectionOptions = [
            "Database" => "WEBAPP",
            "Uid" => "",
            "PWD" => "",
        ];
        $conn = sqlsrv_connect($serverName, $connectionOptions);
        if ($conn==false)
            die(print_r(sqlsrv_errors(),true));
        // else echo 'Connection Success';
    
        $titlesql = "SELECT TITLE_ID FROM TITLE WHERE TITLE_ID = (SELECT IDENT_CURRENT('TITLE'))";
        $results = sqlsrv_query($conn, $titlesql);
        $userid = sqlsrv_fetch_array($results, SQLSRV_FETCH_ASSOC);
    
        if ($userid === false) {
            die(print_r(sqlsrv_errors(), true));
        }
        $titleid = $userid['TITLE_ID'];
    
        $sql = "INSERT INTO CHAPTER1(FILE_NAME, FILE_SIZE, FILE_PATH, TITLE_ID) VALUES ('$filename', '$filesize', '$destination', '$titleid')";
        $results = sqlsrv_query($conn, $sql);

        $sqlManu = "INSERT INTO MANUSCRIPT(FILE_NAME, FILE_SIZE, FILE_PATH, TITLE_ID) VALUES ('$filenameManu', '$filesizeManu', '$destinationManu', '$titleid')";
        $resultsManu = sqlsrv_query($conn, $sqlManu);
    
        $successMsg = '';
        if ($results && $resultsManu) {
            $successMsg = "Upload to Database is Successful";
        } else {
            die(print_r(sqlsrv_errors(), true));
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="./styles/styles.css">
    <title>Uploaded</title>
</head>
<body>
    <div class="section">
        <img src="./images/dlsud-with-name.png" alt="" style="display: block; margin: 0 auto; width: 50%;">
        <?php if ($extErr): ?>
            <h1 align="center"><?php echo $extErr ?></h1>
        <?php elseif ($successMsg): ?>
            <h1 align="center"><?php echo $successMsg; ?></h1>
        <?php endif; ?>
        <div class="button-container">
            <?php if ($extErr): ?>
                <button onClick="window.location.href='Landing.php'">Upload Again</button>
            <?php elseif ($successMsg): ?>
                <button onClick="window.location.href='Dashboard.php'">Dashboard</button>
                <button onClick="window.location.href='Registration.php'">Register Thesis</button>
                <button onClick="window.location.href='ViewEdit.php'">View / Edit</button>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>