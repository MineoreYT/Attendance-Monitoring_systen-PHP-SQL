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

// Fetch all subjects
$subjects_result = $conn->query("SELECT * FROM subjects");

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Subjects</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            background-color: #f4f4f4;
            display: flex;
        }
        .sidebar {
            width: 200px;
            background-color: #333;
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
        h1 {
            text-align: center;
            color: #333;
        }
        ul {
            list-style: none;
            padding: 0;
        }
        li {
            background: #e67e22;
            color: #fff;
            margin: 10px 0;
            padding: 10px;
            border-radius: 4px;
            text-align: center;
        }
        li a {
            text-decoration: none;
            color: #fff;
            display: block;
        }
        li a:hover {
            background-color: #d35400;
        }
    </style>
</head>
<body>

    <!-- Sidebar -->
    <div class="sidebar">
    <a href="index.php">Dashboard</a>
        <a href="students_with_subjects.php">Students</a>
        <a href="mark_attendance.php">Monitor Attendance</a>
        <a href="view_attendance.php">Attendance Report</a>
        <a href="logout.php">Logout</a>
    </div>

    <!-- Content -->
    <div class="content">
        <div class="container">
            <h1>Subjects</h1>
            <ul>
                <?php while ($row = $subjects_result->fetch_assoc()) { ?>
                    <li><a href="students.php?subject_id=<?php echo $row['id']; ?>"><?php echo $row['subject_name']; ?></a></li>
                <?php } ?>
            </ul>
        </div>
    </div>

</body>
</html>
