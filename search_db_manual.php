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

ob_start(); // Do not send any output to the web browser. This section is to initialize the database and initiate database connection
include("connect_database.php");
// include("populate_database.php");
ob_end_clean(); // stops blocking output

include("navigation_bar.php");


// $conn = $_SESSION["sqlConnection"];

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
    <input type="radio" onclick="location.href = 'search_NameTable.php';" id="simplified" name="simplified" value="Simplified">
    <label for="manual" style="font-size: 20px;">Manual input</label>
    <input type="radio" onclick="location.href = 'search_db_manual.php';" id="manual" name="manual" value="Manual" checked></div>

    <div class="centerText"><p style="font-size: 20px;">Queries are of the general form: SELECT tableAttribute FROM tableName WHERE condition<br>
    Enter a query in the input text field below:</div></p>
    <div class="centerText"><input type="text" id="query" name="query" size="100"></div><br>
    <!-- <span class="error" style="color:red;">* <?php echo $usernameError;?></span><br> -->

    <div class="centerText"><input type="submit" value="Search" style="font-size: 20px;"></div>
    <!-- <span class="error" style="color:red;">* <?php echo $loginError;?></span><br> -->

</form>
</body>
</html>

<?php
// The following query execution code below is placed below the above HTML code so that the table results
// will be placed below the query input section.


if ($_SERVER["REQUEST_METHOD"] == "POST") { // Once query form is submitted on user end, run this code
    // Gets the entire string inputted by the user in the text field and attempts to run the query
    // Syntax errors are prevalant though with manual input. An alert will be issued to the user of incorrect syntax when this is occurs.
    if (!empty($_POST["query"])){
        try {
            $sqlInput = $_POST["query"];
            $stmt = $conn->query($sqlInput); // stmt --> "statement"
            showResults($stmt);

        } catch(PDOException $e) {
            // echo $sql . "\r\n" . $e->getMessage();
            $errorMsg = "Incorrect query syntax. Please review your input and follow the provided query syntax.";
            echo "<script>alert('$errorMsg');</script>";
        }
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