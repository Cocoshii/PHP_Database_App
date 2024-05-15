<?php
session_start();

// CODE FROM LECTURE SLIDE: WEEK 5 PART 2 (cite this in the project report I guess)
$dbName = "cp476_db";
$_SESSION["dbName"] = $dbName;
$dsn = "mysql:host=localhost;dbname=cp476_db;charset=utf8mb4"; // cp476_db is the name of the database to connect to
$options = [
PDO::ATTR_EMULATE_PREPARES => false, // turn off emulation mode
PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, //turn on errors in the form of exceptions
PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC, //make the default fetch be an associative array
];
try {
    $username = $_SESSION["username"];
    $password = $_SESSION["password"]; // REPLACE WITH YOUR MYSQL PASSWORD BEFORE RUNNING FILE
    // Note: When editing this and committing changes, it's best to update $password back to a placeholder string
    // if you don't want others to see your password here.

    $conn = new PDO($dsn, $username, $password, $options);
} catch (PDOException $e) {
    error_log($e->getMessage());
    exit("Could not connect to database. Exiting program...");
}

echo "Connected successfully to database: " . $dbName . "<br>";

// To prevent inserting duplicate data entries and receiving an error when running populate_database.php
// we need to delete any existing tables and re-create and re-populate them
// This code would only run if we haven't already populated the database yet

if ($_SESSION["initialPopulation"] == false) {
    try {
        $sql = file_get_contents("wipe_database.sql");
        $conn->exec($sql);
        echo "All existing database tables wiped and removed.<br>";
    } catch (PDOException $e) {
        echo $sql . '\r\n'. $e->getMessage();
    }

    // Initialize database if it has not already been initialized
    $sqlFile = "init_database.sql";
    $sql = file_get_contents($sqlFile);

    // Execute SQL from the file
    try {
        $conn->exec($sql);
        echo "New tables created successfully<br>";
    }  catch(PDOException $e) {
        // echo $sql . '\r\n'. $e->getMessage();
        echo '\r\n'. $e->getMessage();
    }
}


// $conn=null; // close connection

?>
