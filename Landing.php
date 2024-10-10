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

    <link rel="stylesheet" href="stylesheet.css">
    <meta charset="utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="./styles.css" />
    <title>Registration Successful</title>
</head>
<body>
    <div class="section">        
        <h1 align="center">Registration Successful</h1>
        <h2 align="center">Your TITLE ID is: <span style="color: #00bd65"><?php echo $userid['TITLE_ID']; ?></span> </h2>
        <div class="button-container">
            <button onClick="window.location.href='Registration.php'">Go Back</button>
        </div>
    </div>
</body>

</html>