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

    // Fetch latest Title_ID
    $get_last_id_sql = "SELECT TOP 1 TITLE_ID FROM TITLE ORDER BY TITLE_ID DESC";
    $last_id_result = sqlsrv_query($conn, $get_last_id_sql);

    if ($last_id_result) {
        $row = sqlsrv_fetch_array($last_id_result, SQLSRV_FETCH_ASSOC);
        $last_id = $row['TITLE_ID'];

        //Fetch Title related data From Title
        $titleinfo_sql = "SELECT * FROM TITLE WHERE TITLE_ID = ?";
        $info_result = sqlsrv_query($conn, $titleinfo_sql, array($last_id));
        $info = sqlsrv_fetch_array($info_result, SQLSRV_FETCH_ASSOC);

        // Fetch related data from AUTHOR
        $author_sql = "SELECT * FROM AUTHOR WHERE TITLE_ID = ?";
        $author_result = sqlsrv_query($conn, $author_sql, array($last_id));
        $authors = [];
        while ($author = sqlsrv_fetch_array($author_result, SQLSRV_FETCH_ASSOC)) {
            $authors[] = $author;
        }

        // Fetch related data from ADVISER
        $adviser_sql = "SELECT * FROM ADVISER WHERE TITLE_ID = ?";
        $adviser_result = sqlsrv_query($conn, $adviser_sql, array($last_id));
        $adviser = sqlsrv_fetch_array($adviser_result, SQLSRV_FETCH_ASSOC);

        // Fetch related data from CO_ADVISER
        $coadviser_sql = "SELECT * FROM CO_ADVISER WHERE TITLE_ID = ?";
        $coadviser_result = sqlsrv_query($conn, $coadviser_sql, array($last_id));
        $coadviser = sqlsrv_fetch_array($coadviser_result, SQLSRV_FETCH_ASSOC);

        // Fetch related data from CONTACT
        $contact_sql = "SELECT * FROM CONTACT WHERE TITLE_ID = ?";
        $contact_result = sqlsrv_query($conn, $contact_sql, array($last_id));
        $contact = sqlsrv_fetch_array($contact_result, SQLSRV_FETCH_ASSOC);

            // Check if 'School_Year' exists in the $info array
    $schoolYear = isset($info['SCHOOL_YEAR']) ? $info['SCHOOL_YEAR'] : 'N/A';
    }
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0" />
        <title>Review Submission</title>
        <link rel="stylesheet" href="./styles/styles.css" />
    </head>
    <body>
        <div class="navbar">
            <a href="./Registration.php">Registration</a>
            <a href="./SelectReports.php">Reports</a>
            <a href="./Search.php">Search</a>
            <a href="./Dashboard.php" style="float: right">Dashboard</a>
        </div>
    <?php
    ob_start();
    $title_nameErr = "";
    $author1nameErr = "";
    $author2nameErr = "";
    $author3nameErr = "";
    $adviser_nameErr = "";
    $coadviser_nameErr = "";
    $programErr = "";
    $coprogramErr = "";
    $emailErr = "";
    $contactErr = "";
    $contactlenErr = "";
    $subjectErr = "";
    $submissionErr = "";
    $schoolyearErr = "";
    
    $errors = [];
    if (isset($_POST['submit'])){
        if (empty($_POST['title'])) {
            $title_nameErr = "Thesis title is required";
        }
        if (empty($_POST['author1_lname']) or empty($_POST['author1_fname'])) {
            $author1nameErr = "Author's full name is required";
        }
        if (empty($_POST['adviser_lname']) or empty($_POST['adviser_fname'])) {
            $adviser_nameErr = "Adviser's full name is required";
        }
        if (empty($_POST['program'])) {
            $programErr = "Program is required";
        }
        if (empty($_POST['email'])) {
            $emailErr = "Email is required";
        }
        if (empty($_POST['contact_number'])) {
            $contactErr = "Contact number is required";
        }
        if (isset($_POST['contact_number'])) {
            $contact_number = $_POST['contact_number'];
            if (strlen($contact_number) != 11) {
                $contactlenErr = "Contact number should be 10 digits";
            }
        }
        if (empty($_POST['submission'])) {
            $submissionErr = "Date of submission is required";
        }
        if(empty($_POST['sy'])) {
            $schoolyearErr = "School year is required";
        }
    }

        $serverName = "DESKTOP-5QTREIB\SQLEXPRESS";
        $connectionOptions = [
            "Database" => "WEBAPP",
            "Uid" => "",
            "PWD" => "",
        ];
        $conn = sqlsrv_connect($serverName, $connectionOptions);
        if ($conn==false)
            die(print_r(sqlsrv_errors(),true));

        $idsql = "SELECT TITLE_ID FROM TITLE WHERE TITLE_ID = (SELECT IDENT_CURRENT('TITLE'))";
        $results = sqlsrv_query($conn, $idsql);
        $userid = sqlsrv_fetch_array($results, SQLSRV_FETCH_ASSOC);
        $titleid = $userid['TITLE_ID'];

        $titlesql = "SELECT * FROM TITLE WHERE TITLE_ID = $titleid";
        $results = sqlsrv_query($conn, $titlesql);
        $title = sqlsrv_fetch_array($results, SQLSRV_FETCH_ASSOC);

    ?>
        <form
            id="thesis_form"
            action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>"
            method="post"
            style="display: flex; justify-content: center">
            <div class="section">
                <img src="./images/dlsud-with-name.png" style="display: block; margin: 0 auto;">
                <fieldset>
                    <legend class="title-legend">Update Thesis Submission</legend>
                    <label for="title">Title</label>
                    <input
                        type="text"
                        value="<?php echo htmlspecialchars($title['TITLE_NAME']); ?>"
                        name="title"
                        id="title"
                        placeholder="Thesis Title"
                        value="<?php echo isset($info['Title_Name']) ? htmlspecialchars($info['Title_Name']) : 'N/A'; ?>"
                        
                    />
                    <p style="color: red; font-size: 10px;"> <?php echo $title_nameErr;?></p>

                    <div class="columns">
                        <div class="left-column">
                            <label>Author 1</label>
                            <div class="name-group">
                                <input
                                    type="text"
                                    name="author1_lname"
                                    placeholder="Last Name"
                                    class="name-input"
                                    value="<?php echo htmlspecialchars($authors[0]['LAST_NAME'] ?? ''); ?>"
                                />
                                <input
                                    type="text"
                                    name="author1_fname"
                                    placeholder="First Name"
                                    class="name-input"
                                    value="<?php echo htmlspecialchars($authors[0]['FIRST_NAME'] ?? ''); ?>"
                                />
                                <input
                                    type="text"
                                    name="author1_mname"
                                    placeholder="Middle Name"
                                    class="name-input"
                                    value="<?php echo htmlspecialchars($authors[0]['MIDDLE_NAME'] ?? ''); ?>"
                                />
                            </div>
                            <p style="color: red; font-size: 10px;"> <?php echo $author1nameErr;?></p>

                            <label>Author 2</label>
                            <div class="name-group">
                                <input
                                    type="text"
                                    name="author2_lname"
                                    placeholder="Last Name"
                                    class="name-input"
                                    value="<?php echo htmlspecialchars($authors[1]['LAST_NAME'] ?? ''); ?>"
                                />
                                <input
                                    type="text"
                                    name="author2_fname"
                                    placeholder="First Name"
                                    class="name-input"
                                    value="<?php echo htmlspecialchars($authors[1]['FIRST_NAME'] ?? ''); ?>"
                                />
                                <input
                                    type="text"
                                    name="author2_mname"
                                    placeholder="Middle Name"
                                    class="name-input"
                                    value="<?php echo htmlspecialchars($authors[1]['MIDDLE_NAME'] ?? ''); ?>"
                                />
                            </div>
                            <p style="color: red; font-size: 10px;"> <?php echo $author2nameErr;?></p>

                            <label>Author 3</label>
                            <div class="name-group">
                                <input
                                    type="text"
                                    name="author3_lname"
                                    placeholder="Last Name"
                                    class="name-input"
                                    value="<?php echo htmlspecialchars($authors[2]['LAST_NAME'] ?? ''); ?>"
                                />
                                <input
                                    type="text"
                                    name="author3_fname"
                                    placeholder="First Name"
                                    class="name-input"
                                    value="<?php echo htmlspecialchars($authors[2]['FIRST_NAME'] ?? ''); ?>"
                                />
                                <input
                                    type="text"
                                    name="author3_mname"
                                    placeholder="Middle Name"
                                    class="name-input"
                                    value="<?php echo htmlspecialchars($authors[2]['MIDDLE_NAME'] ?? ''); ?>"
                                />
                            </div>
                            <p style="color: red; font-size: 10px;"> <?php echo $author3nameErr;?></p>

                            <label>Adviser</label>
                            <div class="name-group">
                                <input
                                    type="text"
                                    name="adviser_lname"
                                    placeholder="Last Name"
                                    class="name-input"
                                    value="<?php echo htmlspecialchars($adviser['LAST_NAME'] ?? '') ?>"
                                />
                                <input
                                    type="text"
                                    name="adviser_fname"
                                    placeholder="First Name"
                                    class="name-input"
                                    value="<?php echo htmlspecialchars($adviser['FIRST_NAME'] ?? '') ?>"
                                />
                                <input
                                    type="text"
                                    name="adviser_mname"
                                    placeholder="Middle Name"
                                    class="name-input"
                                    value="<?php echo htmlspecialchars($adviser['MIDDLE_NAME'] ?? '') ?>"
                                />
                            </div>
                            <p style="color: red; font-size: 10px;"> <?php echo $adviser_nameErr;?></p>

                            <label>Co-Adviser</label>
                            <div class="name-group">
                                <input
                                    type="text"
                                    name="coadviser_lname"
                                    placeholder="Last Name"
                                    class="name-input"
                                    value="<?php echo htmlspecialchars($coadviser['LAST_NAME'] ?? '') ?>"
                                />
                                <input
                                    type="text"
                                    name="coadviser_fname"
                                    placeholder="First Name"
                                    class="name-input"
                                    value="<?php echo htmlspecialchars($coadviser['FIRST_NAME'] ?? '') ?>"
                                />
                                <input
                                    type="text"
                                    name="coadviser_mname"
                                    placeholder="Middle Name"
                                    class="name-input"
                                    value="<?php echo htmlspecialchars($coadviser['MIDDLE_NAME'] ?? '') ?>"
                                />
                            </div>
                            <p style="color: red; font-size: 10px;"> <?php echo $coadviser_nameErr;?></p>

                            <label for="program">Program:</label>
                            <select name="program" id="program" value >
                                <option value="">...</option>
                                <option value="CPE"<?php if (isset($info['PROGRAM']) && $info['PROGRAM'] == 'CPE') echo 'selected'; ?>>
                                    Computer Engineering
                                </option>
                                <option value="ECE"<?php if (isset($info['PROGRAM']) && $info['PROGRAM'] == 'ECE') echo 'selected'; ?>>
                                    Electronics Engineering
                                </option>
                                <option value="CE"<?php if (isset($info['PROGRAM']) && $info['PROGRAM'] == 'CE') echo 'selected'; ?>>
                                    Civil Engineering
                                </option>
                                <option value="IE"<?php if (isset($info['PROGRAM']) && $info['PROGRAM'] == 'IE') echo 'selected'; ?>>
                                    Industrial Engineering
                                </option>
                                <option value="ME"<?php if (isset($info['PROGRAM']) && $info['PROGRAM'] == 'ME') echo 'selected'; ?>>
                                    Mechanical Engineering
                                </option>
                                <option value="EE"<?php if (isset($info['PROGRAM']) && $info['PROGRAM'] == 'EE') echo 'selected'; ?>>
                                    Electrical Engineering
                                </option>
                                <option value="ARC"<?php if (isset($info['PROGRAM']) && $info['PROGRAM'] == 'ARC') echo 'selected'; ?>>Architecture</option>
                            </select>
                            <p style="color: red; font-size: 10px;"> <?php echo $programErr;?></p>
                        </div>
                        
                        <div class="right-column">
                            <label for="sy">School Year:</label>
                            <input
                                type="text"
                                name="sy"
                                id="sy"
                                placeholder="e.g. 2024-2025"
                                value="<?php echo $schoolYear?>"
                            />
                            <p style="color: red; font-size: 10px;"> <?php echo $schoolyearErr;?></p>

                            <label for="submission">Date of Submission:</label>
                            <input
                                type="date"
                                name="submission"
                                id="submission"
                                value="<?php echo isset($info['DATE_OF_SUBMISSION']) ? htmlspecialchars($info['DATE_OF_SUBMISSION']->format('Y-m-d')) : 'N/A'; ?>"
                            />
                            <p style="color: red; font-size: 10px;"> <?php echo $submissionErr;?></p>

                            <label for="subjects">Subject of Study:</label>
                            <select name="subjects" id="subjects" >
                                <option value="N/A">...</option>
                                <option value="WebDev"<?php if (isset($info['SUBJECT']) && $info['SUBJECT'] == 'WebDev') echo 'selected'; ?>>Web Development</option>
                                <option value="Micro"<?php if (isset($info['SUBJECT']) && $info['SUBJECT'] == 'Micro') echo 'selected'; ?>>Microservices</option>
                                <option value="Embedded"<?php if (isset($info['SUBJECT']) && $info['SUBJECT'] == 'Embedded') echo 'selected'; ?>>
                                    Embedded Systems
                                </option>
                            </select>
                            <p style="color: red; font-size: 10px;"> <?php echo $subjectErr;?></p>

                            <label for="email">Email:</label>
                            <input
                                type="email"
                                name="email"
                                id="email"
                                placeholder="e.g. hello@email.com"
                                value="<?php echo isset($contact['EMAIL']) ? htmlspecialchars($contact['EMAIL']) : 'N/A' ?>"
                            />
                            <p style="color: red; font-size: 10px;"> <?php echo $emailErr;?></p>

                            <label for="contact_number">Contact Number:</label>
                            <input
                                type="tel"
                                name="contact_number"
                                id="contact_number"
                                placeholder="e.g. 09876543210"
                                pattern="[0-9]{11}"
                                title="Mobile Number should be 11 digits"
                                value="<?php echo isset($contact['PHONE_NUMBER']) ? '0' . htmlspecialchars($contact['PHONE_NUMBER']) : ''; ?>"
                            />
                            <p style="color: red; font-size: 10px;"> <?php echo $contactlenErr;?></p>

                            <label for="coprogram">Co-Program:</label>
                            <select name="coprogram" id="coprogram" >
                            <option value="">...</option>
                                <option value="CPE"<?php if (isset($info['CO_PROGRAM']) && $info['CO_PROGRAM'] == 'CPE') echo 'selected'; ?>>
                                    Computer Engineering
                                </option>
                                <option value="ECE"<?php if (isset($info['CO_PROGRAM']) && $info['CO_PROGRAM'] == 'ECE') echo 'selected'; ?>>
                                    Electronics Engineering
                                </option>
                                <option value="CE"<?php if (isset($info['CO_PROGRAM']) && $info['CO_PROGRAM'] == 'CE') echo 'selected'; ?>>
                                    Civil Engineering
                                </option>
                                <option value="IE"<?php if (isset($info['CO_PROGRAM']) && $info['CO_PROGRAM'] == 'IE') echo 'selected'; ?>>
                                    Industrial Engineering
                                </option>
                                <option value="ME"<?php if (isset($info['CO_PROGRAM']) && $info['CO_PROGRAM'] == 'ME') echo 'selected'; ?>>
                                    Mechanical Engineering
                                </option>
                                <option value="EE"<?php if (isset($info['CO_PROGRAM']) && $info['CO_PROGRAM'] == 'EE') echo 'selected'; ?>>
                                    Electrical Engineering
                                </option>
                                <option value="ARC"<?php if (isset($info['CO_PROGRAM']) && $info['CO_PROGRAM'] == 'ARC') echo 'selected'; ?>>Architecture</option>
                            </select>
                            <p style="color: red; font-size: 10px;"> <?php echo $coprogramErr;?></p>
                        </div>
                    </div>
                    <div class="button-container">
                        <button onClick="event.preventDefault(); window.location.href='./Registration.php'">Go Back</button>   
                        <button
                        onClick="return confirm('Do you want to proceed?');" type="submit" value="Submit" name="submit">Update</button>
                        <button onClick="event.preventDefault(); window.location.href='./reports/SelectReports.php'">View Report</button>   
                    </div>
                </fieldset>
            </div>
        </form>
        <!-- PHP code for sending data to database -->
        <?php
        if (isset($_POST['submit'])) {
            if ($title_nameErr == "" && $adviser_nameErr == "" && $coadviser_nameErr == "" && $author1nameErr == "" && $author2nameErr == "" && $author3nameErr == "" && $programErr == "" && $coprogramErr == "" && $emailErr == "" && $contactErr == "" && $contactlenErr == "" && $subjectErr == "" && $submissionErr == "" && $schoolyearErr == "") {
                $serverName = "DESKTOP-5QTREIB\SQLEXPRESS";
                $connectionOptions = [
                    "Database" => "WEBAPP",
                    "Uid" => "",
                    "PWD" => "",
                ];
                $conn = sqlsrv_connect($serverName, $connectionOptions);
                if ($conn==false)
                    die(print_r(sqlsrv_errors(),true));
                else echo 'Connection Success';
    
                $title_name = $_POST['title'];
                $program = $_POST['program'];
                $co_program = $_POST['coprogram'];
                $school_year = $_POST['sy'];
                $date_of_submission = $_POST['submission'];
                $subject = $_POST['subjects'];
    
                $author1_lname = $_POST['author1_lname'];
                $author1_fname = $_POST['author1_fname'];
                $author1_mname = empty($_POST['author1_mname']) ? "N/A" : $_POST['author1_mname'];
                $author2_lname = empty($_POST['author2_lname']) ? "N/A" : $_POST['author2_lname'];
                $author2_fname = empty($_POST['author2_fname']) ? "N/A" : $_POST['author2_fname'];
                $author2_mname = empty($_POST['author2_mname']) ? "N/A" : $_POST['author2_mname'];
                $author3_lname = empty($_POST['author3_lname']) ? "N/A" : $_POST['author3_lname'];
                $author3_fname = empty($_POST['author3_fname']) ? "N/A" : $_POST['author3_fname'];
                $author3_mname = empty($_POST['author3_mname']) ? "N/A" : $_POST['author3_mname'];
    
                $adviser_lname = $_POST['adviser_lname'];
                $adviser_fname = $_POST['adviser_fname'];
                $adviser_mname = empty($_POST['adviser_mname']) ? "N/A" : $_POST['adviser_mname'];
    
                $coadviser_lname = empty($_POST['coadviser_lname']) ? "N/A" : $_POST['coadviser_lname'];
                $coadviser_fname = empty($_POST['coadviser_fname']) ? "N/A" : $_POST['coadviser_fname'];
                $coadviser_mname = empty($_POST['coadviser_mname']) ? "N/A" : $_POST['coadviser_mname'];
    
                $phone_number = $_POST['contact_number'];
                $email = $_POST['email'];
                
                $get_last_id_sql = "SELECT TOP 1 TITLE_ID FROM TITLE ORDER BY TITLE_ID DESC";
                $last_id_result = sqlsrv_query($conn, $get_last_id_sql);
                if ($last_id_result === false) {
                    die(print_r(sqlsrv_errors(), true));
                } else {
                    $row = sqlsrv_fetch_array($last_id_result, SQLSRV_FETCH_ASSOC);
                    $title_id = $row['TITLE_ID'];
                }
                // UPDATE TITLE
                $title_sql = "UPDATE TITLE SET TITLE_NAME = '$title_name', PROGRAM = '$program', CO_PROGRAM = '$co_program', SCHOOL_YEAR = '$school_year', DATE_OF_SUBMISSION = '$date_of_submission', SUBJECT = '$subject' WHERE TITLE_ID = '$title_id'";
                $title_results = sqlsrv_query($conn, $title_sql);
                
                // UPDATE AUTHOR
                $author1 = $authors[0]['AUTHOR_ID'];
                $author2 = $authors[1]['AUTHOR_ID'];
                $author3 = $authors[2]['AUTHOR_ID'];

                $author_sql = "UPDATE AUTHOR SET
                    LAST_NAME = CASE WHEN AUTHOR_ID = '$author1' THEN '$author1_lname' WHEN AUTHOR_ID = '$author2' THEN '$author2_lname' WHEN AUTHOR_ID = '$author3' THEN '$author3_lname' END,
                    FIRST_NAME = CASE WHEN AUTHOR_ID = '$author1' THEN '$author1_fname' WHEN AUTHOR_ID = '$author2' THEN '$author2_fname' WHEN AUTHOR_ID = '$author3' THEN '$author3_fname' END,
                    MIDDLE_NAME = CASE WHEN AUTHOR_ID = '$author1' THEN '$author1_mname' WHEN AUTHOR_ID = '$author2' THEN '$author2_mname' WHEN AUTHOR_ID = '$author3' THEN '$author3_mname' END WHERE AUTHOR_ID IN ('$author1', '$author2', '$author3')";
                
                $adviser_sql = "UPDATE ADVISER SET LAST_NAME = '$adviser_lname', FIRST_NAME = '$adviser_fname', MIDDLE_NAME = '$adviser_mname' WHERE TITLE_ID = '$title_id'";

                $coadviser_sql = "UPDATE CO_ADVISER SET LAST_NAME = '$coadviser_lname', FIRST_NAME = '$coadviser_fname', MIDDLE_NAME = '$coadviser_mname' WHERE TITLE_ID = '$title_id'";

                $contact_sql = "UPDATE CONTACT SET PHONE_NUMBER = '$phone_number', EMAIL = '$email' WHERE TITLE_ID = '$title_id'";
                // $author1_sql = "INSERT INTO AUTHOR(LAST_NAME, FIRST_NAME, MIDDLE_NAME, TITLE_ID) VALUES ('$author1_lname', '$author1_fname', '$author1_mname', '$title_id')";
                // $author2_sql = "INSERT INTO AUTHOR(LAST_NAME, FIRST_NAME, MIDDLE_NAME, TITLE_ID) VALUES ('$author2_lname', '$author2_fname', '$author2_mname', '$title_id')";
                // $author3_sql = "INSERT INTO AUTHOR(LAST_NAME, FIRST_NAME, MIDDLE_NAME, TITLE_ID) VALUES ('$author3_lname', '$author3_fname', '$author3_mname', '$title_id')";
                // $adviser_sql = "INSERT INTO ADVISER(LAST_NAME, FIRST_NAME, MIDDLE_NAME, TITLE_ID) VALUES ('$adviser_lname', '$adviser_fname', '$adviser_mname', '$title_id')";
                // $coadviser_sql = "INSERT INTO CO_ADVISER(LAST_NAME, FIRST_NAME, MIDDLE_NAME, TITLE_ID) VALUES ('$coadviser_lname', '$coadviser_fname', '$coadviser_mname', '$title_id')";
                // $contact_sql = "INSERT INTO CONTACT(PHONE_NUMBER, EMAIL, TITLE_ID) VALUES ('$phone_number', '$email', '$title_id')";
      
                // $author1_results = sqlsrv_query($conn, $author1_sql);
                // $author2_results = sqlsrv_query($conn, $author2_sql);
                $author_results = sqlsrv_query($conn, $author_sql);
                $adviser_results = sqlsrv_query($conn, $adviser_sql);
                $coadviser_results = sqlsrv_query($conn, $coadviser_sql);
                $contact_results = sqlsrv_query($conn, $contact_sql);

                if ($title_results and $author_results and $adviser_results and $coadviser_results and $contact_results) {
                    header("Location: Landing.php");
                    exit();
                }
                else echo "Error";
            }
        }
        ?>
    </body>
</html>
