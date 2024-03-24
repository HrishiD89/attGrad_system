<?php
include("header.php");
include("./config/database.php");

$classname = "";
$subjects = array();

// Check if class ID is provided in the URL
if (isset($_GET['class_id'])) {
    $class_id = $_GET['class_id'];
    // Fetch class details from the database
    $query = "SELECT * FROM classes WHERE class_id = $class_id";
    $result = mysqli_query($conn, $query);

    if (mysqli_num_rows($result) > 0) {
        $class_details = mysqli_fetch_assoc($result);
        $classname = $class_details['classname'];
    }

    $query_s = "SELECT * FROM subjects WHERE class_id = $class_id";
    $result_s = mysqli_query($conn, $query_s);

    if (mysqli_num_rows($result_s) > 0) {
        while ($subj_details = mysqli_fetch_assoc($result_s)) {
            $subjects[] = $subj_details;
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
        .subject-marks {
            width: 100px;
        }

        .stats {
            margin-top: 20px;
        }
    </style>
    <title>AltGrad - Total Grading</title>
</head>

<body>
    <!-- Nav-Bar -->
    <div class="container">
        <div class="left-container">
            <a href="manageClasses.php?class_id=<?php echo $class_id ?>"><?php echo $classname ?></a>
            <a href="Student.php?class_id=<?php echo $class_id ?>">Students</a>
            <a href="totalAttendance.php?class_id=<?php echo $class_id ?>">Total Attendance</a>
            <a href="totalGrades.php?class_id=<?php echo $class_id ?>" class="active">Total Grades</a>
        </div>
        <div class="right-container">
            <h2> <a href="manageClasses.php?class_id=<?php echo $class_id ?>" style="color:black">
                    <i class="fa-solid fa-backward"></i>
                </a>Total Grading </h2><br />
            <table id="myTable" class="display">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Student-ID</th>
                        <?php
                        foreach ($subjects as $x) {
                            echo '<th>' . $x['subject_name'] . '</th>';
                        }
                        ?>
                        <th>AVG</th>
                        <th>Percentage%</th>
                        <!-- Add more subject columns as needed -->
                    </tr>
                </thead>
                <tbody>
                    <?php
                    // Query to fetch grades for each student
                    $query_grades = "SELECT s.student_name, s.student_id, g.subject_id, g.grade
                            FROM students s
                            LEFT JOIN grades g ON s.student_id = g.student_id
                            WHERE s.class_id = $class_id";

                    $result_grades = mysqli_query($conn, $query_grades);
                    $student_grades = array();

                    if (mysqli_num_rows($result_grades) > 0) {
                        // Fetch student grades and calculate average and percentage
                        while ($row = mysqli_fetch_assoc($result_grades)) {
                            $student_name = $row['student_name'];
                            $student_id = $row['student_id'];
                            $subject_id = $row['subject_id'];
                            $grade = $row['grade'];

                            // Store grades for each student and subject
                            $student_grades[$student_id][$subject_id] = $grade;
                        }

                        // Display student grades and calculate average and percentage
                        // Display student grades and calculate average and percentage
                        foreach ($student_grades as $student_id => $grades) {
                            // Fetch student name and ID within the loop
                            $student_query = "SELECT student_name FROM students WHERE student_id = $student_id";
                            $student_result = mysqli_query($conn, $student_query);
                            $student_row = mysqli_fetch_assoc($student_result);
                            $student_name = $student_row['student_name'];

                            echo '<tr>';
                            // Display student name and ID
                            echo '<td>' . $student_name . '</td>';
                            echo '<td>' . $student_id . '</td>';

                            $total_marks = 0;
                            $subject_count = count($subjects);

                            // Display grades for each subject
                            foreach ($subjects as $subject) {
                                $subject_id = $subject['subject_id'];
                                $grade = isset($grades[$subject_id]) ? $grades[$subject_id] : 0;
                                echo '<td>' . $grade . '</td>';
                                $total_marks += $grade;
                            }

                            // Calculate and display average
                            $average = $total_marks / $subject_count;
                            echo '<td>' . number_format($average, 2) . '</td>';

                            // Calculate and display percentage
                            $percentage = ($total_marks / ($subject_count * 100)) * 100;
                            echo '<td>' . number_format($percentage, 2) . '%</td>';
                            echo '</tr>';
                        }
                    }
                    ?>
                </tbody>
            </table>
            <button type="button" onclick="downloadGradingSheet()">Download Grading Sheet</button>


        </div>
    </div>
    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.js"></script>
    <script src="https://kit.fontawesome.com/ade8d745cb.js" crossorigin="anonymous"></script>
    <script src="script.js"></script>
    <script>
        let table = $('#myTable').DataTable();

        function downloadGradingSheet() {
            // Get table data
            var table = document.getElementById("myTable");
            var rows = table.rows;
            var csv = [];

            // Iterate through rows and cells to create CSV
            for (var i = 0; i < rows.length; i++) {
                var row = [];
                var cells = rows[i].cells;
                for (var j = 0; j < cells.length; j++) {
                    row.push(cells[j].textContent.trim());
                }
                csv.push(row.join(","));
            }

            // Create CSV file content
            var csvContent = "data:text/csv;charset=utf-8," + csv.join("\n");

            // Create download link
            var encodedUri = encodeURI(csvContent);
            var link = document.createElement("a");
            link.setAttribute("href", encodedUri);
            link.setAttribute("download", "grading_sheet.csv");

            // Append link to the document and trigger download
            document.body.appendChild(link);
            link.click();

            // Remove the link from the document
            document.body.removeChild(link);
        }
    </script>
</body>

</html>