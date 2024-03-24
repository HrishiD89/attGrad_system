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
            <a href="totalAttendance.php?class_id=<?php echo $class_id ?>">Total Attendance</a>
            <a href="totalGrades.php?class_id=<?php echo $class_id ?>" class="active">Total Grades</a>
        </div>
        <div class="right-container">
            <h2> <a href="manageClasses.php?class_id=<?php echo $class_id ?>" style="color:black">
                    <i class="fa-solid fa-backward"></i>
                </a>Total Grading </h2><br />
            <form id="totalGradingForm">
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
                        <tr>
                            <td>Hrishikesh</td>
                            <td>100</td>
                            <td>65</td>
                            <td>89</td>
                            <td>66</td>
                            <td>55</td>
                            <td>70%</td>
                            <!-- Add more subject input fields as needed -->
                        </tr>
                        <tr>
                            <td>Mukesh</td>
                            <td>105</td>
                            <td>22</td>
                            <td>91</td>
                            <td>67</td>
                            <td>68</td>
                            <td>55%</td>
                            <!-- Add more subject input fields as needed -->
                        </tr>
                        <!-- Add more rows as needed -->
                    </tbody>
                </table>
                <br>
                <button type="button" onclick="submitTotalGrading()">DownLoad Grading Sheet</button>
            </form>

            <!-- Display demo statistics -->
            <div class="stats">
                <h3>Demo Statistics</h3>
                <p>Highest Percentage: <span id="averageMarks">-</span></p>
                <p>Highest Scorer: <span id="highestScore">-</span></p>
            </div>
        </div>
    </div>
    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.js"></script>
    <script src="https://kit.fontawesome.com/ade8d745cb.js" crossorigin="anonymous"></script>
    <script src="script.js"></script>
    <script>
        let table = $('#myTable').DataTable();

        function submitTotalGrading() {
            const formData = $('#totalGradingForm').serializeArray();
            console.log(formData);
            // Add your logic here to handle the submitted total grading data
            // For example, you can send it to the server using AJAX

            // Update demo statistics
            updateDemoStatistics();
        }

        function updateDemoStatistics() {
            // Calculate and update demo statistics based on the input data
            const averageMarks = calculateAverageMarks();
            const highestScore = calculateHighestScore();

            // Display the calculated statistics
            $('#averageMarks').text(averageMarks.toFixed(2));
            $('#highestScore').text(highestScore);
        }

        function calculateAverageMarks() {
            // Calculate average marks based on the input data
            // Replace this with your actual calculation logic
            const totalMarks = $('.subject-marks').toArray().reduce((sum, input) => sum + parseFloat(input.value), 0);
            const numberOfSubjects = $('.subject-marks').length;

            return totalMarks / numberOfSubjects;
        }

        function calculateHighestScore() {
            // Calculate highest score based on the input data
            // Replace this with your actual calculation logic
            const highestScore = Math.max(...$('.subject-marks').toArray().map(input => parseFloat(input.value)));

            return highestScore;
        }
    </script>
</body>

</html>