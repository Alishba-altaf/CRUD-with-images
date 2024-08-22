<?php
include 'database.php'; // Ensure this file includes the connection to your database

if (isset($_POST['std_id'])) {
    $id = $_POST['std_id'];
    
    // Prepare the SQL statement to prevent SQL injection
    $stmt = $conn->prepare("SELECT * FROM students WHERE std_id = ?");
    $stmt->bind_param("i", $id);
    
    // Execute the statement
    $stmt->execute();
    
    // Get the result
    $result = $stmt->get_result();
    
    if ($result && $result->num_rows > 0) {
        // Fetch the record as an associative array
        $row = $result->fetch_assoc();
        
        // Return the record as a JSON response
        echo json_encode($row);
    } else {
        // Return an error message if no record was found
        echo json_encode(["error" => "Record not found"]);
    }
    
    // Close the statement
    $stmt->close();
} else {
    // Return an error message if the ID was not set
    echo json_encode(["error" => "ID not set"]);
}

// Close the database connection
$conn->close();
?>
