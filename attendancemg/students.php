<?php
// Connection to MySQL database
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "attendancedb";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get subject ID from URL and sanitize it
$subject_id = isset($_GET['subject_id']) ? (int)$_GET['subject_id'] : 0;

// Fetch subject details using a prepared statement
$subject_stmt = $conn->prepare("SELECT subject_name FROM subjects WHERE id = ?");
$subject_stmt->bind_param("i", $subject_id);
$subject_stmt->execute();
$subject_result = $subject_stmt->get_result();
$subject = $subject_result->fetch_assoc();
$subject_stmt->close();

// If the subject is not found, show an error
if (!$subject) {
    die("Subject not found.");
}

// Fetch students for the subject using the student_subjects table
$students_stmt = $conn->prepare("
    SELECT students.student_name 
    FROM student_subjects
    JOIN students ON student_subjects.student_id = students.id
    WHERE student_subjects.subject_id = ?
");
$students_stmt->bind_param("i", $subject_id);
$students_stmt->execute();
$students_result = $students_stmt->get_result();
$students_stmt->close();

$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Students</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            background-color: #f4f4f4;
            display: flex;
        }
        .sidebar {
            width: 200px;
            background-color: #2c3e50;
            color: white;
            height: 100vh;
            padding-top: 20px;
            position: fixed;
        }
        .sidebar a {
            color: white;
            text-decoration: none;
            padding: 15px;
            display: block;
            font-size: 18px;
        }
        .sidebar a:hover {
            background-color: #34495e;
        }
        .content {
            margin-left: 220px;
            padding: 20px;
            width: calc(100% - 220px);
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            background: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        h1, h2 {
            text-align: center;
            color: #333;
        }
        ul {
            list-style: none;
            padding: 0;
        }
        li {
            background: #3498db;
            color: #fff;
            margin: 10px 0;
            padding: 10px;
            border-radius: 4px;
            text-align: center;
        }
    </style>
</head>
<body>

    <!-- Sidebar -->
    <div class="sidebar">
        <a href="subjects.php">Subjects</a>
        <a href="students_with_subjects.php">Students</a>
        <a href="mark_attendance.php">Monitor Attendance</a>
        <a href="view_attendance.php">Attendance Report</a>
        <a href="logout.php">Logout</a>
    </div>

    <!-- Content -->
    <div class="content">
        <div class="container">
            <h1><?php echo htmlspecialchars($subject['subject_name']); ?></h1>
            <h2>Students</h2>
            <ul>
                <?php if ($students_result->num_rows > 0): ?>
                    <?php while ($row = $students_result->fetch_assoc()): ?>
                        <li><?php echo htmlspecialchars($row['student_name']); ?></li>
                    <?php endwhile; ?>
                <?php else: ?>
                    <li>No students enrolled in this subject.</li>
                <?php endif; ?>
            </ul>
        </div>
    </div>

</body>
</html>
