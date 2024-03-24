<?php
include("header.php");
include("./config/database.php");

// Initialize $classname variable with a default value
$classname = "";

// Check if class ID is provided in the URL
if (isset($_GET['class_id'])) {
    $class_id = $_GET['class_id'];
    // Fetch class details from the database
    $query = "SELECT * FROM classes WHERE class_id = $class_id";
    $result = mysqli_query($conn, $query);

    if (mysqli_num_rows($result) > 0) {
        $class_details = mysqli_fetch_assoc($result);
        $classname = $class_details['classname'];
        // Echo class name
        // echo '<h2>' . $classname . '</h2>';
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
    <title>AltGrad</title>
</head>

<body>
    <!--Nav-Bar  -->
    <div class="container">
        <div class="left-container">
            <a href="manageClasses.php?class_id=<?php echo $class_id ?>" class="active"><?php echo $classname ?></a>
            <a href="Student.php?class_id=<?php echo $class_id ?>">Students</a>
            <a href="totalAttendance.php?class_id=<?php echo $class_id ?>">Total Attendance</a>
            <a href="totalGrades.php?class_id=<?php echo $class_id ?>">Total Grades</a>
        </div>
        <div class="right-container">
            <h2> <a href="index.php" style="color:black">
                    <i class="fa-solid fa-backward"></i>
                </a>Add a New Subject to <?php echo $classname ?> </h2><br />
            <!-- create Subject form -->
            <form class="classForm" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]) . '?class_id=' . $class_id; ?>" method="post">
                <input type="text" name="subject_name" placeholder="Enter Subject Name">
                <button type="submit" name="submit">Add</button>
            </form>
            <!-- Display Subject -->
            <h2>Subject</h2><br>
            <div class="class-cards">
                <?php
                // Fetch subjects related to the specified class ID
                $query = "SELECT * FROM subjects WHERE class_id = $class_id";
                $result = mysqli_query($conn, $query);

                if (mysqli_num_rows($result) > 0  && $class_id === $_GET['class_id']) {
                    while ($row = mysqli_fetch_assoc($result)) {
                        echo '<a href="subject.php?subject_id=' . $row['subject_id'] . '" class="cards">';
                        echo '<h4>' . $row['subject_name']  .   '</h4>';
                        echo "</a>";
                    }
                } else {
                    echo "No class found!Add Subject";
                }

                ?>
            </div>
        </div>
    </div>
</body>
<script src="script.js"></script>
<script src="https://kit.fontawesome.com/ade8d745cb.js" crossorigin="anonymous"></script>

</html>

<?php
// Check if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['subject_name'])) {
    // Validate and sanitize input
    $subjectName = filter_input(INPUT_POST, "subject_name", FILTER_SANITIZE_SPECIAL_CHARS);

    if (empty($subjectName)) {
        echo "Please enter the subject name.";
    } else {
        // Insert into database
        $sql = "INSERT INTO subjects (subject_name,class_id) VALUES ('$subjectName','$class_id')";
        if (mysqli_query($conn, $sql)) {
            // Refresh the page to display the newly added subject
            header("Location: " . $_SERVER['PHP_SELF'] . '?class_id=' . $class_id);
            exit();
        } else {
            echo "Error: " . $sql . "<br>" . mysqli_error($conn);
        }
    }
}

mysqli_close($conn);
?>