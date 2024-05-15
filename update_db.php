<?php
/*
This file enables the user to update existing data rows. Syntax for updating data is as
UPDATE table_name
SET column1 = value1, column2 = value2, ...
WHERE condition;


*/

session_start();

ob_start(); // Do not send any output to the web browser. This section is to initialize the database and initiate database connection
include("connect_database.php");
// include("populate_database.php");
ob_end_clean(); // stops blocking output

include("navigation_bar.php");

// Verify condition input
$setError = "";
$conditionError = "";
$updateError = "";
$updateStatus = null;

if ($_SERVER["REQUEST_METHOD"] == "POST") { // Once form is submitted on user end, run this code
    if (empty($_POST["newValues"]))  // if user submits while SET field is empty
        $setError = "SET input field is required.";

    if (empty($_POST["condition"])) // if user submits while WHERE field is empty
        $conditionError = "WHERE input field is required";

    if (empty($setError) && empty($conditionError)){
        // If values were entered, execute the executeUpdate() function
        $tableName = $_POST["tableMenu"];
        $newValues = $_POST["newValues"];
        $condition = $_POST["condition"];
        $updateStatus = executeUpdate($tableName, $newValues, $condition, $conn);
        if ($updateStatus == false) // if entry/entries deletion was unsuccessful...
            $updateError = "Failed to update. Please review your syntax as well as the existence of the entered attributes and values.";
     
        // header("Location: verifyLogin.php");
        // exit();
    }

}

function executeUpdate($tableName, $newValues, $condition, $conn){
    try {
        $sql = "UPDATE $tableName SET $newValues WHERE $condition";
        $stmt = $conn->prepare($sql);

        // Execute the prepared statement
        $stmt->execute();
        
        // echo "Record(s) updated successfully.<br>";
        
        return true;
    } catch (PDOException $e) {
        // echo "$sql<br>";
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
<div class="centerText"><h1>Update Database Entries</h1></div>

<!-- Form interface:
UPDATE (dropdown menu of existing table names)
SET (manually typed in column names and new values)
WHERE (condition, manually typed in) -->

<form method="POST">

    <div class="centerText"><p style="font-size: 20px;">Please fill in the blank text field below to update data entries from the database:</p>
    <!-- <p>DELETE FROM <span id="tableMenu"></span> WHERE <span id="condition"></span>.</p> -->
    Update operations are of the general form:<br>
    <b>UPDATE tableName<br>
    SET columnName = newValue<br>
    WHERE condition</b><br>
    
    For example:<br>
    <b>UPDATE FinalGrades<br>
    SET FinalGrade = 69.3<br>
    WHERE studentID = '123456789'<br>
    Remember to add single quotes (') s around non-numeric values and StudentID values.</b><br>
    </div></p><br>

    <p>UPDATE

    <select name="tableMenu" id="tableMenu">
    <option value="NameTable">NameTable</option>
    <option value="CourseTable">CourseTable</option>
    <option value="FinalGrades">FinalGrades</option>
    </select><br><br>

    SET 
    <input type="text" id="newValues" name="newValues">
    <span class="error" style="color:red;">* <?php echo $setError;?></span><br><br>
    
    WHERE
    <input type="text" id="condition" name="condition">
    <span class="error" style="color:red;">* <?php echo $conditionError;?></span><br><br>
    
    </p>

    <br><input type="submit" value="Execute update"><br>
    <br><span class="error" style="color:red;"> <?php echo $updateError;?></span><br></div>


</form>
</body>
</html>

<?php


if ($updateStatus) // if final grade has been calculated (is not null)
    echo "Record(s) updated successfully.<br>";



?>
