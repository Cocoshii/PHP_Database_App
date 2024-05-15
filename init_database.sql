-- THIS FILE INITIALIZES AN EMPTY cp476_db DATABASE

-- Creates the "Name Table"
-- Entries are of the form: studentID, Student Name
-- e.g. 308621686, Boone Stevenson
CREATE TABLE IF NOT EXISTS NameTable (
StudentID VARCHAR(9) NOT NULL,
StudentName VARCHAR(100) NOT NULL,
PRIMARY KEY(StudentID),
UNIQUE(StudentID)
);

-- Creates the "Course Table"
CREATE TABLE IF NOT EXISTS CourseTable (
StudentID VARCHAR(9) NOT NULL,
CourseCode VARCHAR(5) NOT NULL,
Test1 FLOAT NOT NULL DEFAULT 0.0 CHECK (Test1>=0 AND Test1 <= 100),
Test2 FLOAT NOT NULL DEFAULT 0.0 CHECK (Test2>=0 AND Test2 <= 100), 
Test3 FLOAT NOT NULL DEFAULT 0.0 CHECK (Test3>=0 AND Test3 <= 100),
FinalExam FLOAT NOT NULL DEFAULT 0.0 CHECK (FinalExam>=0 AND FinalExam <= 100),
FOREIGN KEY(StudentID) REFERENCES NameTable(StudentID) ON UPDATE CASCADE ON DELETE CASCADE); -- relational db tables linked by primary key StudentID from NameTable

-- Creates the "Final Grades Table"
CREATE TABLE IF NOT EXISTS FinalGrades (
    StudentID VARCHAR(9) NOT NULL,
    StudentName VARCHAR(100) NOT NULL,
    CourseCode VARCHAR(5) NOT NULL,
    FinalGrade FLOAT NOT NULL,
    FOREIGN KEY(StudentID) REFERENCES NameTable(StudentID) ON UPDATE CASCADE ON DELETE CASCADE,
    PRIMARY KEY(StudentID, CourseCode)
);
