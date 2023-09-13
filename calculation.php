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
// ... (previous code) ...
$hostname = "localhost"; // e.g., "localhost"
$username = "root";
$password = "mysql";
$database = "calculater";

$conn = new mysqli($hostname, $username, $password, $database);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
function calculateResult($number1, $number2, $operation) {
    switch ($operation) {
        case "Add":
            return $number1 + $number2;
        case "Subtract":
            return $number1 - $number2;
        case "Multiply":
            return $number1 * $number2;
        case "Divide":
            if ($number2 != 0) {
                return $number1 / $number2;
            } else {
                return "Division by zero is undefined.";
            }
        default:
            return "Invalid operation!";
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST["getHistory"])) {
        // Code to retrieve and display calculation history
        $historyQuery = "SELECT * FROM details";
        $historyResult = $conn->query($historyQuery);

        if ($historyResult->num_rows > 0) {
            echo "<h2>Calculation History</h2>";
            echo "<table border='1'><tr><th>Number 1</th><th>Number 2</th><th>Operation</th><th>Result</th><th>Edit</th></tr>";
            while ($row = $historyResult->fetch_assoc()) {
                echo "<tr><td>".$row["number1"]."</td><td>".$row["number2"]."</td><td>".$row["operation"]."</td><td>".$row["result"]."</td>";
                echo "<td><form method='post' action=''>
                          <input type='hidden' name='editId' value='".$row["ID"]."'>
                          <input type='submit' name='editRecord' value='Edit'>
                      </form></td></tr>";
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
    } elseif (isset($_POST["editRecord"])) {
        // Code to edit a specific record
          $editId = $_POST["editId"];
    
    $editQuery = "SELECT * FROM details WHERE ID = $editId";
    $editResult = $conn->query($editQuery);
    if ($editResult->num_rows == 1) {
        $row = $editResult->fetch_assoc();
        // Display an edit form with the record's values
        echo "<h2>Edit Calculation Record</h2>";
        echo "<form method='post' action=''>
                  Number 1: <input type='text' name='editedNumber1' value='".$row["number1"]."'><br><br>
                  Number 2: <input type='text' name='editedNumber2' value='".$row["number2"]."'><br><br>
                  Operation: <input type='text' name='editedOperation' value='".$row["operation"]."'><br><br>
                  Result: <input type='text' name='editedResult' value='".$row["result"]."' readonly><br><br>
                  <input type='hidden' name='editId' value='$editId'>
                  <input type='submit' name='saveEdit' value='Save Edit'>
              </form>";
    } else {
        echo "Record not found for editing.";
    }
    } elseif (isset($_POST["saveEdit"])) {
        // Code to save the edited record
        $editId = $_POST["editId"];
    $editedNumber1 = $_POST["editedNumber1"];
    $editedNumber2 = $_POST["editedNumber2"];
    $editedOperation = $_POST["editedOperation"];
    // Calculate the result based on the edited numbers and operation
    $editedResult = calculateResult($editedNumber1, $editedNumber2, $editedOperation);

    // Perform the update query to save the edited values and calculated result
    $updateQuery = "UPDATE details SET number1 = '$editedNumber1', number2 = '$editedNumber2', operation = '$editedOperation', result = '$editedResult' WHERE id = $editId";
    if ($conn->query($updateQuery) === TRUE) {
        echo "Record updated successfully.";
    } else {
        echo "Error updating record: " . $conn->error;
    }
    } else {
        $number1 = $_POST["number1"];
        $number2 = $_POST["number2"];
        $operation = $_POST["operation"];

        // Check if inputs are valid numbers
        if (!is_numeric($number1) || !is_numeric($number2)) {
            echo "Invalid input. Please enter valid numbers.";
        } else {
            $result = calculateResult($number1, $number2, $operation);

            // Insert the calculation result into the database
            if (is_numeric($result)) { // Only insert if the result is numeric (not an error message)
                $sql = "INSERT INTO details (number1, number2, operation, result) VALUES ('$number1','$number2','$operation','$result')";

                if ($conn->query($sql) === TRUE) {
                    echo "Result: $result";
                } else {
                    echo "Error inserting data into the database: " . $conn->error;
                }
            } else {
                echo $result; // Display error message
            }
        }
    }
}
?>
</body>
</html>
