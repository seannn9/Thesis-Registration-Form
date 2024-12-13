<?php
    
    $serverName = "DESKTOP-5QTREIB\SQLEXPRESS"; 
    $connectionOptions = [
        "Database" => "WEBAPP",        
        "Uid" => "",                   
        "PWD" => ""                    
    ];

    $conn = sqlsrv_connect($serverName, $connectionOptions);
    if ($conn === false) {
        die(print_r(sqlsrv_errors(), true));
    }

 
    $sql = "SELECT TITLE_ID FROM TITLE WHERE TITLE_ID = (SELECT IDENT_CURRENT('TITLE'))";
    $results = sqlsrv_query($conn, $sql);


    $userid = sqlsrv_fetch_array($results, SQLSRV_FETCH_ASSOC);

    if ($userid === false) {
        die(print_r(sqlsrv_errors(), true));
    }
?>

<!DOCTYPE html>
<html lang="en" xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta charset="utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="./styles/styles.css" />
    <title>Registration Successful</title>
</head>
<body>
    <div class="section">
        <img src="./images/dlsud-with-name.png" alt="" style="display: block; margin: 0 auto; width: 50%;">    
        <h1 align="center">Registration Successful</h1>
        <h2 align="center">Your TITLE ID is: <span style="color: #00bd65"><?php echo $userid['TITLE_ID']; ?></span> </h2>
        <form action="Uploaded.php" method="post" enctype="multipart/form-data" class="upload-file">
            <p>Select file to upload:</p>
            <label for="files" style="margin: 0px">Chapter 1</label>
            <input type="file" name="files" id="files"required>
            <label for="manuscript" style="margin: 0px">Manuscript</label>
            <input type="file" name="manuscript" id="manuscript" required>
            <button align="center" type="submit" value="upload file" name="submit" style="margin-top: 10px">Upload File</button>
        </form>
        <div class="button-container">
            <!-- <button onClick="window.location.href='Registration.php'">Register Thesis</button> -->
            <button onClick="window.location.href='ViewEdit.php'">View / Edit</button>
        </div>
    </div>
</body>

</html>