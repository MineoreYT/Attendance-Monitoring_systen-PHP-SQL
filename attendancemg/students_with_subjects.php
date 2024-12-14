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

// Fetch students and their associated subjects
$students_query = "SELECT students.id AS student_id, students.student_name, subjects.subject_name 
                   FROM students 
                   LEFT JOIN student_subjects ON students.id = student_subjects.student_id
                   LEFT JOIN subjects ON student_subjects.subject_id = subjects.id
                   ORDER BY students.id, subjects.id";

$students_result = $conn->query($students_query);

// Organize data for table display
$students_data = [];
while ($row = $students_result->fetch_assoc()) {
    $student_id = $row['student_id'];
    $student_name = $row['student_name'];
    $subject_name = $row['subject_name'];

    if (!isset($students_data[$student_id])) {
        $students_data[$student_id] = [
            'name' => $student_name,
            'subjects' => []
        ];
    }
    if ($subject_name) {
        $students_data[$student_id]['subjects'][] = $subject_name;
    }
}

// Fetch all distinct subjects for column headers
$subjects_result = $conn->query("SELECT subject_name FROM subjects ORDER BY id");
$subjects = [];
while ($row = $subjects_result->fetch_assoc()) {
    $subjects[] = $row['subject_name'];
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Students with Subjects</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
            background-color: #f4f4f4;
        }
        .container {
            max-width: 800px;
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
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 10px;
            text-align: left;
        }
        th {
            background-color: orange;
            color: #fff;
        }
        tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        .subject-column {
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Students with Subjects</h1>
        <table>
            <thead>
                <tr>
                    <th>Student Name</th>
                    <?php foreach ($subjects as $subject) { ?>
                        <th class="subject-column"><?php echo $subject; ?></th>
                    <?php } ?>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($students_data as $student) { ?>
                    <tr>
                        <td><?php echo $student['name']; ?></td>
                        <?php foreach ($subjects as $subject) { ?>
                            <td class="subject-column">
                                <?php echo in_array($subject, $student['subjects']) ? "✔" : ""; ?>
                            </td>
                        <?php } ?>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
</body>
</html>
