<!-- Re-engineering references:
https://www.w3schools.com/css/css_navbar.asp
https://www.w3schools.com/css/css_navbar_horizontal.asp
-->
<!-- <link rel="stylesheet" href="html_classes.css"> Link to CSS file. This isn't working for some reason. -->

<!DOCTYPE html>
<html>
<head>
<style>
ul {
  list-style-type: none;
  margin: 0;
  padding: 0;
  overflow: hidden;
  background-color: #333;
}

li {
  float: left;
  border-right:1px solid #bbb;
}

li:last-child {
  border-right: none;
}

li a {
  display: block;
  color: white;
  text-align: center;
  padding: 14px 20px;
  text-decoration: none;
}

li a:hover:not(.active) {
  background-color: #111;
}

.active {
  background-color: #04AA6D;
}

</style>
</head>
<body>


<nav>
    <ul>
        <li><a href="homepage.html">Home</a></li>
        <li><a href="search_NameTable.php">Search</a></li>
        <li><a href="update_db.php">Update</a></li>
        <li><a href="delete_db.php">Delete</a></li>
        <li><a href="calc_final_grade.php">Final grades</a></li>
    </ul>
</nav>

</body>
</html>