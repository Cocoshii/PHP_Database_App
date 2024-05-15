<?php

/* Handles user input queries
There are two versions for the interface: Simplified and manual. The user can select which interface they want through two radio buttons.
Manual interface is simple. There is simply a textbox where the user can enter their full query manually through a text field
And it will be validated accordingly.
Simplified interface is described below for users that are less familiar with the database and writing SQL queries:

Simplified interface as the following features (text in square brackets [] are optional):
SELECT (attribute or *) from (table name)[where attribute = value]

To make input easier, two radio buttons will be displayed at the top of the page, for example:
>> Which table would you like to search?
>> () Student Names Data Table
>> (x) Course Data Table
... and clicking on one of the above radio buttons autofills in the query for "from (table name)"

It also affects which attributes can be selected in the SELECT (attribute or *) section of the query
If Student Names is selected, a dropdown menu of the following items are available:
> All attributes (*)
> Student name
> StudentID

If Course Data is selected, a dropdown menu of the following items are available:
> All attributes (*)
> Student ID
> Course code
> Test 1 grade
> Test 2 grade
> Test 3 grade
> Final exam grade

The [where attribute = value] is optional. The condition is typed in manually in a text field. This may result in
an alert saying the query is invalid, if the user enters their condition incorrectly.
*/


// RE-ENGINEERING REFERENCE:
// - Lecture slides week 5 part 2
// - https://stackoverflow.com/questions/31527781/printing-pdo-query-results
// - https://www.w3schools.com/php/php_mysql_select.asp

session_start();
$dbName = $_SESSION["dbName"];

ob_start(); // Do not send any output to the web browser. This section is to initialize the database and initiate database connection
include("connect_database.php");
// include("populate_database.php");
ob_end_clean(); // stops blocking output

include("navigation_bar.php");

function getTableColumnNames($table, $dbName, $conn){
    $tableCols = [];
    $sql = "SELECT COLUMN_NAME 
        FROM INFORMATION_SCHEMA.COLUMNS 
        WHERE TABLE_SCHEMA = '$dbName'
        AND TABLE_NAME = '$table'";

    $stmt = $conn->query($sql);
    if ($stmt->rowCount() > 0) {
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            array_push($tableCols, $row["COLUMN_NAME"]);
        }
    } else {
        echo "0 results";
    }

    return $tableCols;
}

function tableAttributeList($tableCols){
    // Generates HTML Checkbox Inputs
    $attributeList = "";
    foreach ($tableCols as $attribute) {
        // Replace 'name' attribute with the desired name for the checkboxes
        $attributeList .= '<input type="checkbox" name="selectedAttributes[]" value="' . $attribute . '">';
        $attributeList .= '<label>' . $attribute . '</label><br>'; // Label for each checkbox
    }
    return $attributeList;
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
<div class="centerText"><h1>Search Database</h1></div>

<form method="POST">
    <!-- Choose between simplified or manual mode user input mode: -->
    <div class="centerText"><label for="simplified" style="font-size: 20px;">Simplified input</label>
    <input type="radio" onclick="location.href = 'search_db_simplified.php';" id="simplified" name="simplified" value="Simplified" checked>
    <label for="manual" style="font-size: 20px;">Manual input</label>
    <input type="radio" onclick="location.href = 'search_db_manual.php';" id="manual" name="manual" value="Manual"></div>


    <b><p>Table to search:</b>
    
    <select name="tableMenu[]" id="tableMenu" onchange="location.href = this.value">
    <option value="search_NameTable.php" selected>NameTable</option>
    <option value="search_CourseTable.php">CourseTable</option>
    <option value="search_FinalGrades.php">FinalGrades</option>
    </select><br><br>

    <b>Table Attributes to search:<br></b>
    <?php
    $table = "NameTable";
    $tableCols = getTableColumnNames($table, $dbName, $conn);
    $attributeList = tableAttributeList($tableCols);
    echo "<br>" . $attributeList;
    ?>
    
    <!-- Insert attribute checkbox list here -->
    <br>

    Conditions take on the general form: tableAttribute = value<br>
    Click <a href="https://www.w3schools.com/sql/sql_where.asp">here</a> for more information on WHERE clauses. <br>
    NOTE: Do not include the 'WHERE' keyword in your conditional clause.<br>
    Remember to add single quotes (') s around non-numeric values and StudentID values.<br>
    <b>Condition (optional):<br></b> <!-- Uses manual input due to the ambiguous nature of WHERE clauses -->

    <!-- Hyperlink this: https://www.w3schools.com/sql/sql_where.asp -->
    <!-- Insert manual text input field here-->
    <input type="text" id="condition" name="condition" size="50"><br>
    </p>

    <div class="centerText"><input type="submit" value="Search" style="font-size: 20px;"></div>
    

</form>
</body>
</html>


<?php
// The following query execution code below is placed below the above HTML code so that the table results
// will be placed below the query input section.

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $selectedAttributes = isset($_POST["selectedAttributes"]) ? $_POST["selectedAttributes"] : [];
    $condition = isset($_POST["condition"]) ? $_POST["condition"] : ""; // update condition if values were entered

    // Make SQL SELECT query statement, based on user input, by concatenating user input parts to query operators
    $sql = "SELECT ";
    if (empty($selectedAttributes))
        $sql .= "*"; // if nothing was selected and no condition was entered, do a SELECT * FROM tableName by default
    else 
        $sql .= implode(", ", $selectedAttributes);
    
    $sql .= " FROM $table";
    if (!empty($condition))
        $sql .= " WHERE $condition";

    // echo $sql . "<br>";
    
    // Execute SELECT statement
    try {
        $stmt = $conn->query($sql);
        if ($stmt)
            showResults($stmt);
        else 
            echo "No results found.";
        
    } catch(PDOException $e) { // Display alert if query failed
        $errorMsg = "Incorrect query syntax. Please review your condition input and follow the provided query syntax.";
        echo "<script>alert('$errorMsg');</script>";
    }
}


function showResults($stmt){ // show query results from a successful SELECT ... statement
    echo "<br>";
    echo "<center>";
    echo "<table border='1'>";
    echo "<tr>";
    for ($i = 0; $i < $stmt->columnCount(); $i++) { // display table attribute names (columns)
        $col = $stmt->getColumnMeta($i);
        echo "<th>{$col['name']}</th>";
    }
    echo "</tr>";
    // Display each row fetched from the query results
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)){
        echo "<tr>";
        foreach ($row as $value) {
            echo "<td>{$value}</td>";
        }
        echo "</tr>";
    }
    echo "</table>";
    echo "</center>";
}


?>