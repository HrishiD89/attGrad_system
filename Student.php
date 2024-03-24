<?php
include("header.php");
include("./config/database.php");

// getting class table info from url
$classname = "";
if (isset($_GET["class_id"])) {
    $class_id = $_GET["class_id"];
    $query = "SELECT * FROM classes WHERE class_id = $class_id";
    $result = mysqli_query($conn, $query);
    if (mysqli_num_rows($result) > 0) {
        $class_details = mysqli_fetch_assoc($result);
        $classname = $class_details["classname"];
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/jquery.dataTables.css" />
    <title>AltGrad</title>
</head>

<body>
    <div class="container">
        <div class="left-container">
            <a href="manageClasses.php?class_id=<?php echo $class_id ?>"><?php echo $classname ?></a>
            <a href="Student.php?class_id=<?php echo $class_id ?>" class="active">Students</a>
            <a href="totalAttendance.php?class_id=<?php echo $class_id ?>">Total Attendance</a>
            <a href="totalGrades.php?class_id=<?php echo $class_id ?>">Total Grades</a>
        </div>
        <div class="right-container">
            <h2> <a href="index.php" style="color:black">
                    <i class="fa-solid fa-backward"></i>
                </a>Add a New Students to <?php echo $classname ?> </h2><br />
            <!-- create Subject form -->
            <form class="classForm" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']) . '?class_id=' . $class_id ?>" method="post">
                <input type="text" name="student_name" placeholder="Enter Student Name">
                <input type="text" name="student_id" placeholder="Enter Student ID">
                <button type="submit" name="submit">Add</button>
            </form>
            <!-- Display Subject -->
            <h2>Students</h2><br>
            <table id="myTable" class="display">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Student-ID</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $query = "SELECT * FROM students WHERE class_id = $class_id";
                    $result = mysqli_query($conn, $query);

                    if (mysqli_num_rows($result) > 0 && $class_id == $_GET['class_id']) {
                        while ($row = mysqli_fetch_assoc($result)) {
                            echo '<tr>';
                            echo '<td>' . $row['student_name'] . '</td>';
                            echo '<td>' . $row['student_id'] .  '</td>';
                            echo '<td> 
                                    <i class="fa-solid fa-pen-to-square" style="color: #52D999;"></i>
                                    <i class="fa-solid fa-trash" style="color: red;"></i>
                                  </td>';
                            echo '</tr>';
                        }
                    }


                    ?>
                </tbody>
            </table>

        </div>
    </div>
    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.js"></script>
    <script>
        let table = $('#myTable').DataTable();
    </script>
    <script src="https://kit.fontawesome.com/ade8d745cb.js" crossorigin="anonymous"></script>
    <script src="script.js"></script>
</body>

</html>


<?php
// check if the form is correctly submitted
if ($_SERVER["REQUEST_METHOD"] == "POST"  && isset($_POST['student_name']) && isset($_POST['student_id'])) {

    $studentName = filter_input(INPUT_POST, "student_name", FILTER_SANITIZE_SPECIAL_CHARS);
    $studentID = filter_input(INPUT_POST, "student_id", FILTER_SANITIZE_SPECIAL_CHARS);
    if (empty($studentName)) {
        echo "Please enter the student name.";
    } elseif (empty($studentID)) {
        echo "Please enter the student ID";
    } else {
        $sql = "INSERT INTO students (student_id,student_name,class_id) VALUES ('$studentID','$studentName','$class_id')";
        if (mysqli_query($conn, $sql)) {
            echo "Attendance added successfully";
            header("Location: " . $_SERVER['PHP_SELF'] . '?class_id=' . $class_id);
            exit();
        } else {
            echo "Error: " . $sql . "<br>" . mysqli_error($conn);
        }
    }
}
mysqli_close($conn);

?>