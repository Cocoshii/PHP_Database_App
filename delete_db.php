<?php
/*
This file enables the user to delete existing data entries in the database
(not to be confused with deleting the entire database, as handled by wipe_database.sql)
Syntax for a delete query is as follows: 
DELETE FROM table_name
WHERE condition;
*/

session_start();

ob_start(); // Do not send any output to the web browser. This section is to initialize the database and initiate database connection
include("connect_database.php");
// include("populate_database.php");
ob_end_clean(); // stops blocking output

include("navigation_bar.php");

// Verify condition input

$conditionError = "";
$deletionError = "";
$deletionStatus = null;

if ($_SERVER["REQUEST_METHOD"] == "POST") { // Once form is submitted on user end, run this code
    if (empty($_POST["condition"]))  // if user submits while condition field is empty
        $conditionError = "Condition is required to prevent deletion of all records from the table. Please enter a deletion condition.";

    if (empty($conditionError)) {
        // If values were entered, run executeDeletion() function
        $tableName = $_POST["tableMenu"];
        $condition = $_POST["condition"];
        $deletionStatus = executeDeletion($tableName, $condition, $conn);
        if ($deletionStatus == false) // if entry/entries deletion was unsuccessful...
            $deletionError = "Failed to delete. Please review your syntax as well as the existence of the entered attributes and values.";
        
        // header("Location: verifyLogin.php");
        // exit();
    }

}

function executeDeletion($tableName, $condition, $conn){
    try {
        $stmt = $conn->prepare("DELETE FROM $tableName WHERE $condition");
        $stmt->execute();
        // echo "Record(s) deleted successfully.<br>";

        // Inform the user of how many records were deleted from the operation <-- for some reason the code below always says no records were deleted.
        // needs fixing, but the deletion functionality is still operating quickly otherwise.

        // $recordsAffected = $stmt->rowCount();
        // if ($recordsAffected == 0) 
        //     echo "No records matched the deletion condition. No records were deleted.<br>";
        // else 
        //     echo "$stmt->rowCount() record(s) deleted successfully.<br>";
        
        return true;
    } catch (PDOException $e) {
        // echo "Error: " . $e->getMessage();
        return false;
    }
}

?>


<!-- SEARCH DATABASE WEB USER INTERFACE: HTML -->

<!DOCTYPE html>
<html>
<head>
    <link rel="stylesheet" href="html_classes_text.css"> <!-- Link to CSS file -->
</head>

<body>

<!-- Welcome heading -->
<div class="centerText"><h1>Delete Database Entries</h1></div>

<!-- Form interface:
DELETE FROM (dropdown menu of existing table names) WHERE (condition, manually typed in) -->

<form method="POST">

    <div class="centerText"><p style="font-size: 20px;">Please fill in the blank text field below to delete a data entry from the database:</div></p>
    <!-- <p>DELETE FROM <span id="tableMenu"></span> WHERE <span id="condition"></span>.</p> -->
    <div class="centerText"><p style="font-size: 20px;">Delete operations are of the general form: DELETE FROM tableName WHERE condition<br>
    For example: DELETE FROM NameTable WHERE studentID = '123456789'<br>
    Remember to add single quotes (') s around non-numeric values and StudentID values.</div></p><br>
    <div class="centerText"><p>DELETE FROM

    <select name="tableMenu" id="tableMenu">
    <option value="NameTable">NameTable</option>
    <option value="CourseTable">CourseTable</option>
    <option value="FinalGrades">FinalGrades</option>
    </select>

    WHERE
    <input type="text" id="condition" name="condition"><br>
    <span class="error" style="color:red;"> <?php echo $conditionError;?></span><br>
    
    </p>

    <input type="submit" value="Execute deletion"><br>
    <br><span class="error" style="color:red;"> <?php echo $deletionError;?></span><br></div>


</form>
</body>
</html>

<?php

if ($deletionStatus) // if final grade has been calculated (is not null)
    echo "Record(s) deleted successfully.<br>";


?>