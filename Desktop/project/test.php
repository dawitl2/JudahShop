<?php
$servername = 'localhost';
$username = 'root'; 
$password = 'password'; 
$database = 'my_database'; 

// Create connection
$conn = mysqli_connect($servername, $username, $password, $database);

// Check connection
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}
echo "Connected successfully<br>";

// Example query
$sql = "SELECT * FROM my_table"; // Replace with your table name
$result = mysqli_query($conn, $sql);

// Check if there are results
if (mysqli_num_rows($result) > 0) {
    // Output data of each row
    while ($row = mysqli_fetch_assoc($result)) {
        echo "ID: " . $row["id"] . " - Name: " . $row["name"] . " - Age: " . $roSw["age"] . " - Email: " . $row["email"] . "<br>";
    }
} else {
    echo "0 results";
}

// Close connection
mysqli_close($conn);
?>
