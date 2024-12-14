<?php
// Connection to MySQL database
$servername = "localhost";
$username = "root";
$password = "jeuznalogoc";
$dbname = "attendancedb"; // Change the database name if necessary

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Add subject
if (isset($_POST['add_subject'])) {
    $subject_name = $_POST['subject_name'];
    $sql = "INSERT INTO subjects (subject_name) VALUES ('$subject_name')";
    $conn->query($sql);
}

// Add student
if (isset($_POST['add_student'])) {
    $student_name = $_POST['student_name'];
    $subject_ids = $_POST['subject_ids']; // Array of subject IDs
    $conn->query("INSERT INTO students (student_name) VALUES ('$student_name')");
    $student_id = $conn->insert_id; // Get the ID of the newly added student

    foreach ($subject_ids as $subject_id) {
        $conn->query("INSERT INTO student_subjects (student_id, subject_id) VALUES ('$student_id', '$subject_id')");
    }
}

$subjects_result = $conn->query("SELECT * FROM subjects");
$students_result = $conn->query("
    SELECT students.id, students.student_name, GROUP_CONCAT(subjects.subject_name SEPARATOR ', ') AS subject_names
    FROM students
    JOIN student_subjects ON students.id = student_subjects.student_id
    JOIN subjects ON student_subjects.subject_id = subjects.id
    GROUP BY students.id
");

$conn->close();
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Attendance Management</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            color: #333;
            display: flex;
        }

        .sidebar {
            width: 250px;
            height: 100vh;
            background-color: #333;
            color: white;
            display: flex;
            flex-direction: column;
            align-items: center;
            padding-top: 20px;
            position: fixed;
            transition: transform 0.3s ease;
        }

        .sidebar.hidden {
            transform: translateX(-250px);
        }

        .sidebar h2 {
            margin-bottom: 30px;
        }

        .sidebar a {
            color: white;
            text-decoration: none;
            font-size: 18px;
            margin: 10px 0;
            padding: 10px 20px;
            width: 100%;
            text-align: center;
            border-radius: 4px;
            transition: background-color 0.3s;
        }

        .sidebar a:hover {
            background-color: #444;
        }

        .toggle-btn {
            position: fixed;
            top: 20px;
            left: 20px;
            background-color: #e67e22;
            color: white;
            border: none;
            padding: 10px 15px;
            cursor: pointer;
            border-radius: 4px;
        }

        .toggle-btn:hover {
            background-color: #d35400;
        }

        .container {
            flex: 1;
            margin: 20px;
            margin-left: 270px;
            padding: 20px;
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            transition: margin-left 0.3s ease;
        }

        .container.sidebar-hidden {
            margin-left: 20px;
        }

        h1 {
            text-align: center;
            margin-bottom: 30px;
        }

        .form-section {
            margin-bottom: 30px;
        }

        .form-section h2 {
            margin-bottom: 15px;
            color: #e67e22;
        }

        input, select {
            width: 100%;
            padding: 10px;
            margin: 10px 0;
            border: 1px solid #ccc;
            border-radius: 4px;
        }

        button {
            background-color: #e67e22;
            color: #fff;
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }

        button:hover {
            background-color: #d35400;
        }

        .list-section table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        .list-section th, .list-section td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }

        .list-section th {
            background-color: #f39c12;
            color: white;
        }

        .list-section tr:nth-child(even) {
            background-color: #f9f9f9;
        }
    </style>
    <script>
        function toggleSidebar() {
            const sidebar = document.querySelector('.sidebar');
            const container = document.querySelector('.container');
            sidebar.classList.toggle('hidden');
            container.classList.toggle('sidebar-hidden');
        }
    </script>
</head>
<body>
    <!-- Sidebar -->
    <button class="toggle-btn" onclick="toggleSidebar()">Toggle Sidebar</button>
    <div class="sidebar">
        <h2>Attendance System</h2>
        
        <a href="subjects.php">Subjects</a>
        <a href="students_with_subjects.php">Students</a>
        <a href="mark_attendance.php">Monitor Attendance</a>
        <a href="view_attendance.php">Attendance Report</a>
        <a href="logout.php">Logout</a>
    </div>

    <!-- Main Content -->
    <div class="container">
        <h1>Attendance Management Dashboard</h1>

        <!-- Add Subject Form -->
        <div class="form-section">
            <h2>Add Subject</h2>
            <form method="POST" action="">
                <input type="text" name="subject_name" placeholder="Enter Subject Name" required>
                <button type="submit" name="add_subject">Add Subject</button>
            </form>
        </div>

       <!-- Add Student Form -->
<div class="form-section">
    <h2>Add Student</h2>
    <form method="POST" action="">
        <input type="text" name="student_name" placeholder="Enter Student Name" required>
        <select name="subject_ids[]" multiple required>
            <?php while ($row = $subjects_result->fetch_assoc()) { ?>
                <option value="<?php echo $row['id']; ?>"><?php echo $row['subject_name']; ?></option>
            <?php } ?>
        </select>
        <button type="submit" name="add_student">Add Student</button>
    </form>
</div>


        <!-- List of Students -->
<div class="list-section">
    <h2>Student List</h2>
    <table>
        <tr>
            <th>Student Name</th>
            <th>Subjects</th>
        </tr>
        <?php while ($row = $students_result->fetch_assoc()) { ?>
            <tr>
                <td><?php echo $row['student_name']; ?></td>
                <td><?php echo $row['subject_names']; ?></td>
            </tr>
        <?php } ?>
    </table>
</div>

    </div>
</body>
</html>
