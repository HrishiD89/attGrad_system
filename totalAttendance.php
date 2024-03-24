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

    <title>AltGrad - Total Attendance</title>
    <style>
        td {
            border: 1px solid white;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="left-container">
            <a href="manageClasses.php?class_id=<?php echo $class_id ?>"><?php echo $classname ?></a>
            <a href="totalAttendance.php?class_id=<?php echo $class_id ?>" class="active">Total Attendance</a>
            <a href="totalGrades.php?class_id=<?php echo $class_id ?>">Total Grades</a>
        </div>
        <div class="right-container">
            <h2> <a href="manageClasses.php?class_id=<?php echo $class_id ?>" style="color:black">
                    <i class="fa-solid fa-backward"></i>
                </a>Total Attendance </h2><br />
            <!-- <form id="totalAttendanceForm"> -->
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
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $query_a = "SELECT * FROM  students WHERE class_id=$class_id";
                    $result_a = mysqli_query($conn, $query_a);
                    if (mysqli_num_rows($result_a) > 0) {
                        while ($row = mysqli_fetch_assoc($result_a)) {
                            echo '<tr>';
                            echo '<td>' . $row['student_name']  . '</td>';
                            echo '<td>' . $row['student_id']  . '</td>';
                            foreach ($subjects as $x) {
                                echo '<td style="background-color:red;color:white">0%</td>';;
                            }
                            echo '</tr>';
                        }
                    }
                    ?>
                </tbody>
            </table>
            <br>
            <button type="button" onclick="calculateAttendanceStatistics()">Calculate Attendance Statistics</button>
            <!-- </form> -->

            <!-- Display attendance statistics -->
            <div id="attendanceStatistics" style="margin-top: 20px;">
                <h3>Attendance Statistics</h3>
                <p>Number of people who got below 75% attendance: <span id="below75Count">- 1.</span></p>
            </div>
        </div>
    </div>
    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.js"></script>
    <script src="https://kit.fontawesome.com/ade8d745cb.js" crossorigin="anonymous"></script>
    <script src="script.js"></script>
    <script>
        let table = $('#myTable').DataTable();

        function calculateAttendanceStatistics() {
            // Get attendance data from the table
            const attendanceData = table.rows().data().toArray();

            // Calculate the number of people who got below 75% attendance
            const below75Count = attendanceData.reduce((count, row) => {
                // Assuming the attendance values are in columns 2 and onward (index 1 and onward)
                for (let i = 2; i < row.length; i++) {
                    if (parseFloat(row[i]) < 75) {
                        return count + 1;
                    }
                }
                return count;
            }, 0);

            // Display the calculated statistics
            $('#below75Count').text(below75Count);
        }
    </script>
</body>

</html>