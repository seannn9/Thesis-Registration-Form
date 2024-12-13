 <!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0" />
        <title>Thesis Registration Form</title>
        <link rel="stylesheet" href="./styles/styles.css" />
        
    </head>
    <body>
        <div class="navbar">
            <a href="./Registration.php" class="active">Registration</a>
            <a href="./Search.php">Search</a>
            <a href="./SelectReports.php">Reports</a>
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
        // if (empty($_POST['author2_lname']) or empty($_POST['author2_mname']) or empty($_POST['author2_fname'])) {
        //     $author2nameErr = "Author's full name is required";
        // }
        // if (empty($_POST['author3_lname']) or empty($_POST['author3_mname']) or empty($_POST['author3_fname'])) {
        //     $author3nameErr = "Author's full name is required";
        // }
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
    ?>
        <form
            id="thesis_form"
            action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>"
            method="post"
            style="display: flex; justify-content: center">
            <div class="section">
                <img src="./images/dlsud-with-name.png" style="display: block; margin: 0 auto;">
                <fieldset>
                    <legend class="title-legend">Thesis Submission</legend>
                    <label for="title">Title</label>
                    <input
                        type="text"
                        name="title"
                        id="title"
                        placeholder="Thesis Title"
                        
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
                                    
                                />
                                <input
                                    type="text"
                                    name="author1_fname"
                                    placeholder="First Name"
                                    class="name-input"
                                    
                                />
                                <input
                                    type="text"
                                    name="author1_mname"
                                    placeholder="Middle Name"
                                    class="name-input"
                                    
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
                                    
                                />
                                <input
                                    type="text"
                                    name="author2_fname"
                                    placeholder="First Name"
                                    class="name-input"
                                    
                                />
                                <input
                                    type="text"
                                    name="author2_mname"
                                    placeholder="Middle Name"
                                    class="name-input"
                                    
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
                                    
                                />
                                <input
                                    type="text"
                                    name="author3_fname"
                                    placeholder="First Name"
                                    class="name-input"
                                    
                                />
                                <input
                                    type="text"
                                    name="author3_mname"
                                    placeholder="Middle Name"
                                    class="name-input"
                                    
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
                                    
                                />
                                <input
                                    type="text"
                                    name="adviser_fname"
                                    placeholder="First Name"
                                    class="name-input"
                                    
                                />
                                <input
                                    type="text"
                                    name="adviser_mname"
                                    placeholder="Middle Name"
                                    class="name-input"
                                    
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
                                    
                                />
                                <input
                                    type="text"
                                    name="coadviser_fname"
                                    placeholder="First Name"
                                    class="name-input"
                                    
                                />
                                <input
                                    type="text"
                                    name="coadviser_mname"
                                    placeholder="Middle Name"
                                    class="name-input"
                                    
                                />
                            </div>
                            <p style="color: red; font-size: 10px;"> <?php echo $coadviser_nameErr;?></p>

                            <label for="program">Program:</label>
                            <select name="program" id="program" >
                                <option value="">...</option>
                                <option value="CPE">
                                    Computer Engineering
                                </option>
                                <option value="ECE">
                                    Electronics Engineering
                                </option>
                                <option value="CE">
                                    Civil Engineering
                                </option>
                                <option value="IE">
                                    Industrial Engineering
                                </option>
                                <option value="ME">
                                    Mechanical Engineering
                                </option>
                                <option value="EE">
                                    Electrical Engineering
                                </option>
                                <option value="ARC">Architecture</option>
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
                            />
                            <p style="color: red; font-size: 10px;"> <?php echo $schoolyearErr;?></p>

                            <label for="submission">Date of Submission:</label>
                            <input
                                type="date"
                                name="submission"
                                id="submission"
                                
                            />
                            <p style="color: red; font-size: 10px;"> <?php echo $submissionErr;?></p>

                            <label for="subjects">Subject of Study:</label>
                            <select name="subjects" id="subjects" >
                                <option value="N/A">...</option>
                                <option value="WebDev">Web Development</option>
                                <option value="Micro">Microservices</option>
                                <option value="Embedded">
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
                                
                            />
                            <p style="color: red; font-size: 10px;"> <?php echo $contactlenErr;?></p>

                            <label for="coprogram">Co-Program:</label>
                            <select name="coprogram" id="coprogram" >
                                <option value="N/A">...</option>
                                <option value="CPE">
                                    Computer Engineering
                                </option>
                                <option value="ECE">
                                    Electronics Engineering
                                </option>
                            </select>
                            <p style="color: red; font-size: 10px;"> <?php echo $coprogramErr;?></p>
                        </div>
                    </div>
                    <div class="button-container">
                        <button onClick="window.location.reload();"> Refresh</button>   
                        <button
                        onClick="return confirm('Do you want to proceed?');" type="submit" value="Submit" name="submit">Submit</button>
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
    
                $title_sql = "INSERT INTO TITLE(TITLE_NAME, PROGRAM, CO_PROGRAM, SCHOOL_YEAR, DATE_OF_SUBMISSION, SUBJECT) VALUES ('$title_name', '$program', '$co_program', '$school_year', '$date_of_submission', '$subject')";
                $title_results = sqlsrv_query($conn, $title_sql);
                
                if ($title_results) {
                    $get_last_id_sql = "SELECT SCOPE_IDENTITY() AS TITLE_ID";
                    $last_id_result = sqlsrv_query($conn, $get_last_id_sql);
                    if ($last_id_result !== false && sqlsrv_has_rows($last_id_result)) {
                        $row = sqlsrv_fetch_array($last_id_result, SQLSRV_FETCH_ASSOC);
                        $title_id = $row['TITLE_ID'];
                    }
                }

                $author1_sql = "INSERT INTO AUTHOR(LAST_NAME, FIRST_NAME, MIDDLE_NAME, TITLE_ID) VALUES ('$author1_lname', '$author1_fname', '$author1_mname', '$title_id')";
                $author2_sql = "INSERT INTO AUTHOR(LAST_NAME, FIRST_NAME, MIDDLE_NAME, TITLE_ID) VALUES ('$author2_lname', '$author2_fname', '$author2_mname', '$title_id')";
                $author3_sql = "INSERT INTO AUTHOR(LAST_NAME, FIRST_NAME, MIDDLE_NAME, TITLE_ID) VALUES ('$author3_lname', '$author3_fname', '$author3_mname', '$title_id')";
                $adviser_sql = "INSERT INTO ADVISER(LAST_NAME, FIRST_NAME, MIDDLE_NAME, TITLE_ID) VALUES ('$adviser_lname', '$adviser_fname', '$adviser_mname', '$title_id')";
                $coadviser_sql = "INSERT INTO CO_ADVISER(LAST_NAME, FIRST_NAME, MIDDLE_NAME, TITLE_ID) VALUES ('$coadviser_lname', '$coadviser_fname', '$coadviser_mname', '$title_id')";
                $contact_sql = "INSERT INTO CONTACT(PHONE_NUMBER, EMAIL, TITLE_ID) VALUES ('$phone_number', '$email', '$title_id')";
      
                $author1_results = sqlsrv_query($conn, $author1_sql);
                $author2_results = sqlsrv_query($conn, $author2_sql);
                $author3_results = sqlsrv_query($conn, $author3_sql);
                $adviser_results = sqlsrv_query($conn, $adviser_sql);
                $coadviser_results = sqlsrv_query($conn, $coadviser_sql);
                $contact_results = sqlsrv_query($conn, $contact_sql);

                if ($title_results and $author1_results and $author2_results and $author3_results and $adviser_results and $coadviser_results and $contact_results) {
                    header("Location: Landing.php");
                    exit();
                }
                else echo "Error";
            }
        }
        ?>
    </body>
</html>
