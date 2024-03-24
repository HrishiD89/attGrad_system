<?php
include("header.php");
include("./config/database.php");
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
    <div class="container">
        <div class="left-container">
            <a class='active' href="index.php">Manage Classes</a>
        </div>
        <div class="right-container">
            <h2>Add a New Class to the System:</h2><br />
            <!-- create class form -->
            <form class="classForm" action="<?php htmlspecialchars($_SERVER["PHP_SELF"]) ?>" method="post">
                <input type="text" name="classname" placeholder="Enter Class Name">
                <button type="submit" name="submit">Add</button>
            </form>
            <!-- Display Classes -->
            <h2>Classes</h2><br>
            <div class="class-cards">
                <?php
                $query = "SELECT * FROM classes ORDER BY created_at ASC";
                $result = mysqli_query($conn, $query);

                if (mysqli_num_rows($result) > 0) {
                    while ($row = mysqli_fetch_assoc($result)) {
                        echo '<a href="manageClasses.php?class_id=' . $row['class_id'] . '" class="cards">';
                        echo '<h4>' . $row['classname'] . '</h4>';
                        echo '<div class="icon-container">';
                        echo '</div>';
                        echo '</a>';
                    }
                } else {
                    echo "No class found! Add Class";
                }
                ?>
            </div>
        </div>
    </div>
</body>
<script src="https://kit.fontawesome.com/ade8d745cb.js" crossorigin="anonymous"></script>

</html>

<?php
// Check if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['classname'])) {
    // Validate and sanitize input
    $className = filter_input(INPUT_POST, "classname", FILTER_SANITIZE_SPECIAL_CHARS);

    if (empty($className)) {
        echo "Please enter the class name :<";
    } else {
        // Insert into database
        $sql = "INSERT INTO classes (classname) VALUES ('$className')";
        if (mysqli_query($conn, $sql)) {
            header("Location: " . $_SERVER['PHP_SELF']);
            echo "Class added successfully.";
        } else {
            echo "Error: " . $sql . "<br>" . mysqli_error($conn);
        }
    }
}

mysqli_close($conn);
?>