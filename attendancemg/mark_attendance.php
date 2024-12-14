<?php
// Connection to MySQL database
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "attendancedb";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle the form submission for marking attendance
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['submit_attendance'])) {
    $subject_id = $_POST['subject_id'];
    $attendance_date = $_POST['attendance_date'];
    
    // Loop through each student and save their attendance
    foreach ($_POST['attendance'] as $student_id => $is_present) {
        $stmt = $conn->prepare("
            INSERT INTO attendance (student_id, subject_id, attendance_date, is_present)
            VALUES (?, ?, ?, ?)
            ON DUPLICATE KEY UPDATE is_present = VALUES(is_present)
        ");
        $stmt->bind_param("iisi", $student_id, $subject_id, $attendance_date, $is_present);
        $stmt->execute();
        $stmt->close();
    }
}

// Fetch subjects for the subject selection dropdown
$subjects_stmt = $conn->prepare("SELECT * FROM subjects");
$subjects_stmt->execute();
$subjects_result = $subjects_stmt->get_result();
$subjects_stmt->close();

// Fetch students for the selected subject
$students_result = null;
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['subject_id'])) {
    $subject_id = $_POST['subject_id'];
    $students_stmt = $conn->prepare("
        SELECT id, student_name FROM students
        WHERE id NOT IN (SELECT student_id FROM attendance WHERE subject_id = ? AND attendance_date = ?)
    ");
    $students_stmt->bind_param("is", $subject_id, $_POST['attendance_date']);
    $students_stmt->execute();
    $students_result = $students_stmt->get_result();
    $students_stmt->close();
}

// Close the connection after the operations
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mark Attendance</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f2f2f2;
        }
        .sidebar {
            width: 250px;
            height: 100%;
            position: fixed;
            top: 0;
            left: 0;
            background-color: #333;
            padding-top: 20px;
            color: white;
        }
        .sidebar a {
            padding: 10px 15px;
            text-decoration: none;
            font-size: 18px;
            color: white;
            display: block;
            margin-bottom: 10px;
        }
        .sidebar a:hover {
            background-color: #575757;
        }
        .content {
            margin-left: 260px;
            padding: 20px;
        }
        .container {
            max-width: 900px;
            margin: 50px auto;
            padding: 20px;
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
        h1 {
            text-align: center;
            color: #333;
        }
        form {
            margin-top: 30px;
        }
        label, select, input[type="date"], button {
            display: block;
            width: 100%;
            padding: 10px;
            margin-bottom: 20px;
            font-size: 16px;
        }
        table {
            width: 100%;
            margin-top: 30px;
            border-collapse: collapse;
        }
        th, td {
            padding: 15px;
            text-align: center;
            border: 1px solid #ddd;
        }
        th {
            background-color: #4CAF50;
            color: white;
        }
        td {
            background-color: #f9f9f9;
        }
        button {
            background-color: #4CAF50;
            color: white;
            border: none;
            font-size: 16px;
            cursor: pointer;
        }
        button:hover {
            background-color: #45a049;
        }
    </style>
</head>
<body>

    <!-- Sidebar -->
    <div class="sidebar">
        <h2>Admin Panel</h2>
        <a href="index.php">Dashboard</a>
        <a href="mark_attendance.php">Mark Attendance</a>
        <a href="view_attendance.php">View Reports</a>
        <a href="students_with_subjects.php">Students</a>
        <a href="subjects.php">Subjects</a>
        <a href="logout.php">Logout</a>
    </div>

    <!-- Content -->
    <div class="content">
        <div class="container">
            <h1>Mark Attendance</h1>

            <!-- Subject and Date Selection Form -->
            <form method="POST">
                <label for="subject_id">Select Subject:</label>
                <select name="subject_id" id="subject_id" required>
                    <option value="">-- Select a Subject --</option>
                    <?php while ($subject = $subjects_result->fetch_assoc()): ?>
                        <option value="<?php echo $subject['id']; ?>"><?php echo htmlspecialchars($subject['subject_name']); ?></option>
                    <?php endwhile; ?>
                </select>

                <label for="attendance_date">Attendance Date:</label>
                <input type="date" name="attendance_date" id="attendance_date" required>

                <button type="submit">Load Students</button>
            </form>

            <!-- Student Attendance Form -->
            <?php if ($students_result && $students_result->num_rows > 0): ?>
                <form method="POST">
                    <input type="hidden" name="subject_id" value="<?php echo $_POST['subject_id']; ?>">
                    <input type="hidden" name="attendance_date" value="<?php echo $_POST['attendance_date']; ?>">
                    
                    <h2>Mark Attendance for Students</h2>
                    <table>
                        <thead>
                            <tr>
                                <th>Student Name</th>
                                <th>Present</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($row = $students_result->fetch_assoc()): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($row['student_name']); ?></td>
                                    <td>
                                        <select name="attendance[<?php echo $row['id']; ?>]" required>
                                            <option value="1">Present</option>
                                            <option value="0">Absent</option>
                                        </select>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>

                    <button type="submit" name="submit_attendance">Save Attendance</button>
                </form>
            <?php elseif ($students_result): ?>
                <p>No students found for the selected subject and date.</p>
            <?php endif; ?>
        </div>
    </div>

</body>
</html>
