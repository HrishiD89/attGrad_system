<?php
include("header.php");
include("./config/database.php");

// Check if class ID is provided in the URL
if (isset($_GET['class_id'])) {
    $class_id = $_GET['class_id'];

    // Fetch class details from the database
    $query_class = "SELECT classname FROM classes WHERE class_id = $class_id";
    $result_class = mysqli_query($conn, $query_class);

    if (mysqli_num_rows($result_class) > 0) {
        $class_details = mysqli_fetch_assoc($result_class);
        $classname = $class_details['classname'];
    }

    // Query to get attendance percentage for each subject in the class
    $query_attendance = "
        SELECT 
            subject_id,
            COUNT(*) AS total_classes,
            SUM(CASE WHEN status = 'present' THEN 1 ELSE 0 END) AS attended_classes,
            (SUM(CASE WHEN status = 'present' THEN 1 ELSE 0 END) / COUNT(*)) * 100 AS attendance_percentage
        FROM 
            attendance
        WHERE
            class_id = $class_id
        GROUP BY 
            subject_id;
    ";

    $result_attendance = mysqli_query($conn, $query_attendance);

    // Display class details
    echo "<h2>$classname Attendance Percentage</h2>";
    echo "<table border='1'>
            <tr>
                <th>Subject ID</th>
                <th>Total Classes</th>
                <th>Attended Classes</th>
                <th>Attendance Percentage</th>
            </tr>";

    // Iterate over the result set and display data
    while ($row = mysqli_fetch_assoc($result_attendance)) {
        echo "<tr>";
        echo "<td>" . $row['subject_id'] . "</td>";
        echo "<td>" . $row['total_classes'] . "</td>";
        echo "<td>" . $row['attended_classes'] . "</td>";
        echo "<td>" . $row['attendance_percentage'] . "%</td>";
        echo "</tr>";
    }

    echo "</table>";
}
