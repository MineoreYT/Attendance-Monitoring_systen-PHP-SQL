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

// Fetch all subjects for the subject selection dropdown
$subjects_stmt = $conn->prepare("SELECT * FROM subjects");
$subjects_stmt->execute();
$subjects_result = $subjects_stmt->get_result();
$subjects_stmt->close();

// Fetch attendance for a specific subject and date if selected
$attendance_result = null;
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['subject_id'])) {
    $subject_id = $_POST['subject_id'];

    // Query to fetch attendance data for the selected subject, grouped by date
    $attendance_stmt = $conn->prepare("
        SELECT s.student_name, a.attendance_date, a.is_present
        FROM attendance a
        JOIN students s ON a.student_id = s.id
        WHERE a.subject_id = ?
        ORDER BY a.attendance_date DESC
    ");
    $attendance_stmt->bind_param("i", $subject_id);
    $attendance_stmt->execute();
    $attendance_result = $attendance_stmt->get_result();
    $attendance_stmt->close();
}

// Close the connection
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Attendance Report</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background-color: #f2f2f2;
            margin: 0;
            padding: 0;
            display: flex;
        }

        /* Sidebar styles */
        .sidebar {
            width: 250px;
            height: 100vh;
            background-color: #333;
            padding-top: 30px;
            position: fixed;
        }
        .sidebar a {
            display: block;
            color: white;
            padding: 15px;
            text-decoration: none;
            font-size: 18px;
        }
        .sidebar a:hover {
            background-color: #575757;
        }

        /* Main content styles */
        .container {
            margin-left: 270px;
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
            margin-bottom: 30px;
        }

        label, select, button {
            display: block;
            width: 100%;
            padding: 10px;
            margin-bottom: 20px;
            font-size: 16px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 30px;
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
    <a href="index.php">Dashboard</a>
        <a href="subjects.php">Subjects</a>
        <a href="students_with_subjects.php">Students</a>
        <a href="mark_attendance.php">Monitor Attendance</a>
        <a href="logout.php">Logout</a>
    </div>

    <!-- Main content -->
    <div class="container">
        <h1>Attendance Report</h1>

        <!-- Subject Selection Form -->
        <form method="POST">
            <label for="subject_id">Select Subject:</label>
            <select name="subject_id" id="subject_id" required>
                <option value="">-- Select a Subject --</option>
                <?php while ($subject = $subjects_result->fetch_assoc()): ?>
                    <option value="<?php echo $subject['id']; ?>"><?php echo htmlspecialchars($subject['subject_name']); ?></option>
                <?php endwhile; ?>
            </select>

            <button type="submit">View Attendance</button>
        </form>

        <!-- Attendance Tables for Each Date -->
        <?php if ($attendance_result && $attendance_result->num_rows > 0): ?>
            <?php
                // Group attendance by date
                $attendance_data = [];
                while ($row = $attendance_result->fetch_assoc()) {
                    $attendance_data[$row['attendance_date']][] = $row;
                }

                // Display a table for each date
                foreach ($attendance_data as $date => $attendance_records):
            ?>
                <h2>Attendance for <?php echo htmlspecialchars($date); ?></h2>
                <table>
                    <thead>
                        <tr>
                            <th>Student Name</th>
                            <th>Present/Absent</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($attendance_records as $record): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($record['student_name']); ?></td>
                                <td><?php echo $record['is_present'] ? 'Present' : 'Absent'; ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endforeach; ?>
        <?php elseif ($attendance_result): ?>
            <p>No attendance data found for the selected subject.</p>
        <?php endif; ?>
    </div>

</body>
</html>
