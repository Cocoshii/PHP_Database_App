<?php
/* Calculates the final grade of a student based on their three test scores and final exam score.
Note from MLS:
- Each test weighs 20% and the final exam weighs 40%. The final grade is calculated with
the following: (test,1,2,3) 3x20% + (final exam) 40% = 100%.
- All the grades should be decimal number with one digital after the dot.
*/

session_start();

ob_start(); // Do not send any output to the web browser. This section is to initialize the database and initiate database connection
include("connect_database.php");
// include("populate_database.php");
ob_end_clean(); // stops blocking output

include("navigation_bar.php");

// CODE FOR VALIDATING USER INPUT

$finalGrade = null; // starting value

// Error handling variables
$studentIDError = "";
$courseCodeError = "";
$queryError = "";

// Username and password fields cannot be left blank. Most values must also be valid.

if ($_SERVER["REQUEST_METHOD"] == "POST") { // Once form is submitted on user end, run this code
    if (empty($_POST["studentID"]))  // if user submits while username field is empty
        $studentIDError = "Student ID cannot be left blank.";

    if (empty($_POST["courseCode"])) // if user submits while course code field is empty
        $courseCodeError = "Course code cannot be left blank.";

    if (empty($studentIDError) && empty($courseCodeError)){
        $studentID = trim((string) $_POST["studentID"]); // trim() removes any trailing and preceding whitespace
        // echo $studentID . "<br>"; // debug statement
        $courseCode = trim((string) $_POST["courseCode"]);
        // echo $courseCode . "<br>"; // debug statement
        $sql = "SELECT * FROM FinalGrades WHERE StudentID = '" . $studentID . "' AND CourseCode = '" . $courseCode . "'";
        // echo $sql . "<br>"; // debug statement to view above query
        $stmt = $conn->query($sql); // if the entered student ID and course code exist in the course table then proceed with calculating the final grade
        if ($stmt->rowCount() == 0) // if the query results in an empty set however, then we cannot calculate the final grade. Display error to user.
            $queryError = "The student ID and course code record entered does not exist in the database. Please review the database and your input.";
    }

    if (empty($studentIDError) && empty($courseCodeError) && empty($queryError)){ // run this code if the student ID and course code exists in the database
        $studentID = trim((string) $_POST["studentID"]); 
        $courseCode = trim((string) $_POST["courseCode"]);
        $finalGrade = retrieveFinalGrade($studentID, $courseCode, $conn);
        // echo "Final grade: " . $finalGrade . "<br>";
    }

}


function retrieveFinalGrade($studentID, $courseCode, $conn){
    $stmt = $conn->prepare("SELECT FinalGrade FROM FinalGrades WHERE StudentID=:studentID AND CourseCode=:courseCode");
    $stmt->execute([
        ':studentID' => $studentID,
        ':courseCode' => $courseCode
    ]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC); // Fetch the row as an associative array
    if ($row) // if row exists in the query results, which it should, since the input was validated in the if block above this function 
        return $row["FinalGrade"];
     else 
        return null; // Return null just in case validation failed in the if block above this function

}

?>

<!-- Interface enables user to enter a studentID along with a course code.
If there's time, the interface will offer a dropdown menu for the course codes to simplify user input and reduce errors. -->

<!-- Welcome heading -->
<div class="centerText"><h1>Calculate Student Final Grade</h1></div>

<form method="POST">
    <!-- Choose between simplified or manual mode user input mode: -->
    <div class="centerText"><label for="Calculate student final grade" style="font-size: 20px;">Calculate student final grade</label>
    <input type="radio" onclick="location.href = 'calc_final_grade.php';" id="Calculate student final grade"
    name="Calculate student final grade" value="Calculate student final grade" checked><br>

    <label for="Show all student final grades" style="font-size: 20px;">Show all student final grades</label>
    <input type="radio" onclick="location.href = 'final_grades_table.php';" id="Show all student final grades"
    name="Show all student final grades" value="Show all student final grades"></div><br>


    <label for="studentID">Student ID:</label><br>
    <input type="text" id="studentID" name="studentID">
    <span class="error" style="color:red;">* <?php echo $studentIDError;?></span><br>

    <label for="courseCode">Course Code:</label><br>
    <input type="text" id="courseCode" name="courseCode">
    <span class="error" style="color:red;">* <?php echo $courseCodeError?></span><br>

    <br><input type="submit" value="Calculate final grade">
    <span class="error" style="color:red;">* <?php echo $queryError;?></span><br>

</form>
</body>
</html>

<!-- Displays the final grade below the form (putting it in the php block above results in it being displayed above the form) -->
<?php

if ($finalGrade)  // if final grade has been calculated (is not null)
    echo "<p>Final grade: " . $finalGrade . "</p>";


?>

