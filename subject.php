<?php
include("header.php");
include("./config/database.php");

$className = "";
$class_id = "";
$subjectname = "";
$studentID = "";
$subjectid = "";

if (isset($_GET['subject_id'])) {
    $subject_id = $_GET['subject_id'];

    $query1 = "SELECT * FROM subjects WHERE subject_id = $subject_id";
    $result1 = mysqli_query($conn, $query1);

    if (mysqli_num_rows($result1) > 0) {
        $subject_detail = mysqli_fetch_assoc($result1);
        $subjectname = $subject_detail['subject_name'];
        $subjectid = $subject_detail['subject_id'];

        // finding classname
        $query3 = "SELECT * FROM classes WHERE class_id = (SELECT class_id FROM subjects WHERE subject_id = $subjectid)";
        $result3 = mysqli_query($conn, $query3);
        if (mysqli_num_rows($result3) > 0) {
            $class_details = mysqli_fetch_assoc($result3);
            $className = $class_details["classname"];
            $class_id = $class_details["class_id"]; // Update class_id here
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/jquery.dataTables.css" />
    <link rel="stylesheet" href="style.css">

    <style>
        .attendance {
            display: flex;
            align-items: center;
            text-align: center;
        }

        .present {
            padding: 0 20px;
            display: flex;
            align-items: center;
            color: #52D999;
            font-weight: bold;

        }

        .absent {
            padding: 0 20px;
            display: flex;
            align-items: center;
            color: red;
            font-weight: bold;
        }
    </style>
    <title>AltGrad</title>
</head>

<body>
    <div class="container">
        <div class="left-container">
            <a href="index.php"><?php echo $className . "  -    " . $subjectname ?></a>
            <a href="subject.php?subject_id=<?php echo $subject_id ?>" class="active">Attendance</a>
            <a href="grading.php?subject_id=<?php echo $subject_id ?>">Grades</a>
        </div>
        <div class="right-container">
            <h2> <a href="manageClasses.php?class_id=<?php echo $class_id ?>" style="color:black">
                    <i class="fa-solid fa-backward"></i>
                </a>Attendance </h2><br />
            <form id="attendanceForm" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']) . '?subject_id=' . $subject_id;  ?>" method="POST">
                <table id="myTable" class="display">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Student-ID</th>
                            <th>Attendance</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        if (!empty($class_id)) {
                            // Construct the SQL query
                            $query = "SELECT * FROM students WHERE class_id = $class_id";

                            // Execute the query
                            $result = mysqli_query($conn, $query);
                            if (mysqli_num_rows($result) > 0 && $subject_id == $_GET['subject_id']) {
                                while ($row = mysqli_fetch_assoc($result)) {
                                    echo   '<tr>';
                                    echo '<td>' . $row['student_name'] . '</td>';
                                    echo '<td>' . $row['student_id'] . '</td>';
                                    echo '<td class="attendance">
                                <label><input type="radio" name="attendance[' . $row['student_id'] . ']" value="present">Present</label>
                                <label><input type="radio" name="attendance[' . $row['student_id'] . ']" value="absent">Absent</label>
                            </td>';
                                    echo '</tr>';
                                }
                            }
                        } else {
                            echo "Error: class_id is empty";
                        }
                        ?>
                        <!-- Add more rows as needed -->
                    </tbody>
                </table>
                <br>
                <input type="date" name="date"> <br><br>
                <button type="submit" name="submit">Submit Attendance</button>
            </form>
        </div>
    </div>
    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.js"></script>
    <script src="https://kit.fontawesome.com/ade8d745cb.js" crossorigin="anonymous"></script>
    <script src="script.js"></script>
    <script>
        let table = $('#myTable').DataTable();
    </script>
</body>

</html>

<?php
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['submit'])) {
    // Validate and sanitize date input
    $date = isset($_POST['date']) ? $_POST['date'] : date('Y-m-d'); // Use today's date if not provided
    $date = mysqli_real_escape_string($conn, trim($_POST['date'])); // Sanitize the input

    // Initialize an array to store attendance data
    $attendanceData = array();

    // Process attendance for each student
    foreach ($_POST['attendance'] as $studentId => $status) {
        // Validate student ID
        $studentId = (int) $studentId;
        if ($studentId <= 0) {
            continue; // Skip invalid student ID
        }

        // Validate attendance status
        $status = ($status === 'present') ? 'present' : 'absent';

        // Add attendance data to the array **without echoing**
        $attendanceData[] = "($studentId, $subject_id,'$class_id','$date', '$status')";
    }

    // Insert attendance data into the database
    if (!empty($attendanceData)) {
        $insertQuery = "INSERT INTO attendance (student_id, subject_id,class_id, date, status) VALUES " . implode(',', $attendanceData);
        if (mysqli_query($conn, $insertQuery)) {
            // Redirect to the same page with the subject_id parameter
            header("Location: " . $_SERVER['PHP_SELF'] . '?subject_id=' . $subject_id);
            exit();
        } else {
            echo "Error: " . $insertQuery . "<br>" . mysqli_error($conn);
        }
    } else {
        echo "No valid attendance data submitted.";
    }
}
?>