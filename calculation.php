<html>
<body>
<h2>Enter your calculation</h2>
    <form method="post" action="">
        Number 1: <input type="text" name="number1" placeholder="Enter first number" required><br><br>
        Number 2: <input type="text" name="number2" placeholder="Enter second number" required><br><br>
        <input type="submit" name="operation" value="Add">
        <input type="submit" name="operation" value="Subtract">
        <input type="submit" name="operation" value="Multiply">
        <input type="submit" name="operation" value="Divide">
        <br><br>
    </form>
</body>
<?php
$hostname = "localhost"; // e.g., "localhost"
$username = "root";
$password = "mysql";
$database = "calculater";

$conn = new mysqli($hostname, $username, $password, $database);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $number1 = $_POST["number1"];
    $number2 = $_POST["number2"];
    $operation = $_POST["operation"];

    // Check if inputs are valid numbers
    if (!is_numeric($number1) || !is_numeric($number2)) {
        echo "Invalid input. Please enter valid numbers.";
    } else {
        if ($operation == "Add") {
            $result = $number1 + $number2;
        } elseif ($operation == "Subtract") {
            $result = $number1 - $number2;
        } elseif ($operation == "Multiply") {
            $result = $number1 * $number2;
        } elseif ($operation == "Divide") {
            if ($number2 != 0) {
                $result = $number1 / $number2;
            } else {
                echo "Division by zero is undefined.";
                $result = null; // Set result to null to avoid inserting an invalid result
            }
        } else {
            echo "Invalid operation!";
            $result = null; // Set result to null to avoid inserting an invalid result
        }

        // Insert the calculation result into the database
        if ($result !== null) { // Only insert if the result is valid
            $sql = "INSERT INTO details (number1, number2, operation, result) VALUES ('$number1','$number2','$operation','$result')";

            if ($conn->query($sql) === TRUE) {
                echo "Result: $result";
            } else {
                echo "Error inserting data into the database: " . $conn->error;
            }
        }
    }
}

?>
</html>
