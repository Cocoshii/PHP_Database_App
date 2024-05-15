<?php
// RE-ENGINEERING REFERENCE: Lecture slides week 5 part 2 <-- CITE IN PROJECT REPORT

// Upon successful connection to the database, cp476_db, populates the initialized empty tables with data
// The data is taken from NameFile.txt and CourseFile.txt
// Located under the data folder of the project folder

session_start();

include("connect_database.php"); // connect to database before populating it

$nameFile = fopen("data/NameFile.txt", "r");
$courseFile = fopen("data/CourseFile.txt", "r");


function readData($file){
    $data = [];
    while (!feof($file)){ // loop until we reach the end of the file
        $line = fgets($file);
        if (!empty($line)) { // only add data valaues to data array if the line is not empty
            // (there may be trailing or empty lines in the file and we don't want to add those)
            $dataValues = explode(",", $line);
            array_push($data, $dataValues);
        }
    }
    return $data;
}

function insertData($data, $table, $conn){
    // $table is a string that identifies whether to insert into the NameTable or CourseTable
    // so $table = "name" or "course"
    if (strcmp($table, "name") == 0){
        // then insert $data into NameTable
        $stmt = $conn->prepare("INSERT INTO NameTable (StudentID, StudentName) VALUES (:StudentID, :StudentName)");
        foreach ($data as $row) {
            $studentID = $row[0];
            $studentName = ltrim($row[1]);
            $stmt->bindParam(":StudentID", $studentID);
            $stmt->bindParam(":StudentName", $studentName);
            $stmt->execute();
        }
    } else { // for populating the CourseTable with $data
        $stmt = $conn->prepare("INSERT INTO CourseTable (StudentID, CourseCode, Test1, Test2, Test3, FinalExam)
        VALUES (:StudentID, :CourseCode, :Test1, :Test2, :Test3, :FinalExam)");
        foreach ($data as $row) {
            $stmt->bindParam(":StudentID", $row[0]);
            $stmt->bindParam(":CourseCode", ltrim($row[1]));
            $stmt->bindParam(":Test1", $row[2]);
            $stmt->bindParam(":Test2", $row[3]);
            $stmt->bindParam(":Test3", $row[4]);
            $stmt->bindParam(":FinalExam", $row[5]);
            $stmt->execute();
        }
    }
    // Note that this function only assumes that NameTable and CourseTable are the only two tables
    // It can be edited to add data to other tables if needed.
}

$nameData = readData($nameFile);
$courseData = readData($courseFile);

insertData($nameData, "name", $conn);
echo "NameTable populated with data from NameFile.txt successfully.<br>";

insertData($courseData, "course", $conn);
echo "CourseTable populated with data from CourseFile.txt successfully.<br>";

// $conn = null; // close connection. Will be reopened by re-including $connect_database.php in the necessary file

// POPULATES FINAL GRADES TABLE
try {
    // Check if the FinalGrades table exists, drop if it does, and create a fresh one
    $dropAndCreateTableSQL = "
    DROP TABLE IF EXISTS FinalGrades;
    CREATE TABLE FinalGrades (
        StudentID VARCHAR(9) NOT NULL,
        StudentName VARCHAR(100) NOT NULL,
        CourseCode VARCHAR(5) NOT NULL,
        FinalGrade FLOAT NOT NULL,
        FOREIGN KEY(StudentID) REFERENCES NameTable(StudentID) ON UPDATE CASCADE ON DELETE CASCADE,
        PRIMARY KEY(StudentID, CourseCode)
    );";

    $conn->exec($dropAndCreateTableSQL);
    // echo "FinalGrades table has been dropped and recreated.<br>"; // debug statement

    // Continue with the process of fetching grades and inserting them into the new table
    $sql = "SELECT n.StudentID, n.StudentName, c.CourseCode, c.Test1, c.Test2, c.Test3, c.FinalExam FROM NameTable n JOIN CourseTable c ON n.StudentID = c.StudentID";
    $stmt = $conn->prepare($sql);
    $stmt->execute();

    $results = $stmt->fetchAll();

    if ($results) {
        foreach ($results as $row) {
            $finalGrade = ($row['Test1'] * 0.2) + ($row['Test2'] * 0.2) + ($row['Test3'] * 0.2) + ($row['FinalExam'] * 0.4);

            // Prepare insert statement for the FinalGrades table
            $insertSql = "INSERT INTO FinalGrades (StudentID, StudentName, CourseCode, FinalGrade) VALUES (:StudentID, :StudentName, :CourseCode, :FinalGrade)";
            $insertStmt = $conn->prepare($insertSql);
            $insertStmt->execute([
                ':StudentID' => $row['StudentID'],
                ':StudentName' => $row['StudentName'],
                ':CourseCode' => $row['CourseCode'],
                ':FinalGrade' => $finalGrade
            ]);

            // echo "Record inserted: StudentID - " . $row['StudentID'] . ", CourseCode - " . $row['CourseCode'] . ", Final Grade - " . $finalGrade . "<br>";
        }
    } else {
        echo "No course records found.";
    }
} catch (PDOException $e) {
    error_log($e->getMessage());
    exit("Error occurred while handling the FinalGrades table or fetching/inserting data. Exiting program...");
}


?>
