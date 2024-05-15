# How to connect to this project's database

1. Clone the repository onto VS Code
2. Open Command Prompt by pressing the Windows Key and typing in cmd
3. Enter: mysql -u root -p
4. Enter your MySQL password when prompted
5. Once connected (command prompt should display mysql> on the current line), enter the following command
    if you have not already created the required database: CREATE DATABASE cp476_db;
6. Now open connect_database.php and type in http://localhost/connect_database.php in your web browser. Make sure
    that connect_database.php file is in your htdocs folder under Apache24
7. The webpage should say "Connected successfully to database: cp476_db"
    If it doesn't say this, it probably will say "Something wrong"
    If something is wrong, double check the $password variable and that dbname=cp476_db under $dsn

Note that we might need to make changes to this setup before submission as the prof should be able to test
the server on his end. This setup is for debugging purposes only.