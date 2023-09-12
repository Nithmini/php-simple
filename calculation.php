<!DOCTYPE html>
<html>
<head>
    <title>Calculator</title>
</head>
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

<!-- Add button for fetching history -->
<form method="post" action="">
    <input type="submit" name="getHistory" value="Get Calculation History">
</form>

<!-- Add button for clearing history -->
<form method="post" action="">
    <input type="submit" name="clearHistory" value="Clear Calculation History">
</form>

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
    if (isset($_POST["getHistory"])) {
        // Code to retrieve and display calculation history
        $historyQuery = "SELECT * FROM details";
        $historyResult = $conn->query($historyQuery);

        if ($historyResult->num_rows > 0) {
            echo "<h2>Calculation History</h2>";
            echo "<table border='1'><tr><th>Number 1</th><th>Number 2</th><th>Operation</th><th>Result</th></tr>";
            while ($row = $historyResult->fetch_assoc()) {
                echo "<tr><td>".$row["number1"]."</td><td>".$row["number2"]."</td><td>".$row["operation"]."</td><td>".$row["result"]."</td></tr>";
            }
            echo "</table>";
        } else {
            echo "No calculation history yet.";
        }
    } elseif (isset($_POST["clearHistory"])) {
        // Code to clear calculation history
        $clearHistoryQuery = "DELETE FROM details";
        if ($conn->query($clearHistoryQuery) === TRUE) {
            echo "Calculation history cleared.";
        } else {
            echo "Error clearing calculation history: " . $conn->error;
        }
    } else {
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
}
?>

</body>
</html>
