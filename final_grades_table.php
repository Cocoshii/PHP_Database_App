<?php

session_start();

ob_start(); // Do not send any output to the web browser. This section is to initialize the database and initiate database connection
include("connect_database.php");
// include("populate_database.php");
ob_end_clean(); // stops blocking output

include("navigation_bar.php");


?>


<!-- Show only the student final grades table -->

<div class="centerText"><h1>Calculate Student Final Grade</h1></div>

<form method="POST">
    <!-- Choose between simplified or manual mode user input mode: -->
    <div class="centerText"><label for="Calculate student final grade" style="font-size: 20px;">Calculate student final grade</label>
    <input type="radio" onclick="location.href = 'calc_final_grade.php';" id="Calculate student final grade"
    name="Calculate student final grade" value="Calculate student final grade"><br>

    <label for="Show all student final grades" style="font-size: 20px;">Show all student final grades</label>
    <input type="radio" onclick="location.href = 'final_grades_table.php';" id="Show all student final grades"
    name="Show all student final grades" value="Show all student final grades" checked></div><br>

</form>
</body>
</html>

<?php

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

$sql = "SELECT * FROM FinalGrades";
$stmt = $conn->query($sql); // stmt --> "statement"
showResults($stmt);



?>