CREATE database attendancedb
use attendancedb;
CREATE TABLE teachers (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL
);
CREATE TABLE subjects (
    id INT AUTO_INCREMENT PRIMARY KEY,
    subject_name VARCHAR(255) NOT NULL
);

CREATE TABLE students (
    id INT AUTO_INCREMENT PRIMARY KEY,
    student_name VARCHAR(255) NOT NULL,
    subject_id INT,
    FOREIGN KEY (subject_id) REFERENCES subjects(id)
);
CREATE TABLE student_subjects (
    id INT AUTO_INCREMENT PRIMARY KEY,
    student_id INT NOT NULL,
    subject_id INT NOT NULL,
    FOREIGN KEY (student_id) REFERENCES students(id),
    FOREIGN KEY (subject_id) REFERENCES subjects(id)
);
CREATE TABLE attendance (
    id INT AUTO_INCREMENT PRIMARY KEY,
    student_id INT,
    subject_id INT,
    attendance_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    is_present BOOLEAN,
    FOREIGN KEY (student_id) REFERENCES students(id),
    FOREIGN KEY (subject_id) REFERENCES subjects(id)
);

ALTER TABLE attendance MODIFY COLUMN attendance_date DATE;

ALTER TABLE attendance 
ADD COLUMN attendance_date DATE DEFAULT CURRENT_DATE;



