<?php // Logs user out of the website and destroys session data
// Also redirects the user back to login page

session_start(); // Start session so we can unset all session variables
$_SESSION = []; // Unset all session variables
session_destroy();

?>

<!DOCTYPE html>
<html>
<head>
    <link rel="stylesheet" href="html_classes_text.css"> <!-- Link to CSS file -->
</head>

<body>
 
<!-- Form interface:
DELETE FROM (dropdown menu of existing table names) WHERE (condition, manually typed in) -->

<form method="POST">

    <div class="centerText"><p style="font-size: 20px;">Logout successful. Click the button below to log back in.</div></p>

    <div class="centerText"><input type="button" value="To login page" onclick="location.href = 'login.php';"><br></div>


</form>
</body>
</html>