<?php 

$serverName = "DESKTOP-5QTREIB\SQLEXPRESS";
$connectionOptions = [
    "Database" => "WEBAPP",
    "Uid" => "",
    "PWD" => "",
    ];
$conn = sqlsrv_connect($serverName, $connectionOptions);
if ($conn==false)
    die(print_r(sqlsrv_errors(),true));

$titleId = isset($_GET['TITLE_ID']) ? $_GET['TITLE_ID'] : '';

$sql = "SELECT FILE_PATH, FILE_NAME FROM MANUSCRIPT WHERE TITLE_ID = ?";
$params = [$titleId];
$results = sqlsrv_query($conn, $sql, $params);
if ($results === false) {
    die(print_r(sqlsrv_errors(), true));
}
$file = sqlsrv_fetch_array($results, SQLSRV_FETCH_ASSOC);

if ($file && isset($file['FILE_PATH'])) {
    $filepath = $file['FILE_PATH'];
    $filename = $file['FILE_NAME'];
    if (file_exists($filepath)) { 
        header("Content-type: application/pdf");
        header("Content-Disposition: attachment; filename=\"" . basename($filename) . "\"");
        header("Content-length:" .filesize($filepath));

        readfile($filepath);
        exit;
    } else {
        echo 'File not found';
    }
} else {
    echo 'No file found for the TITLE_ID';
}  
?>