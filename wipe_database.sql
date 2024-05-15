-- Removes any existing tables from cp476_db
-- This function is called during initialization of the database since populating the database when it has already been previously populated
-- may result in errors for inserting duplicate entries
-- So this file ensures a fresh initialization to the database upon re-population.
DROP TABLE IF EXISTS CourseTable; -- CourseTable must be dropped first since it has a foreign key attached to NameTable (studentID)
DROP TABLE IF EXISTS FinalGrades;
DROP TABLE IF EXISTS NameTable;
