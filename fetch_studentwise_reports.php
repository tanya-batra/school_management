<?php
include('db_connect.php');

// Check if the student ID is provided
if (isset($_POST['id'])) {
    $studentId = $_POST['id'];

    // Prepare the query to fetch student details and fee transactions by student ID
    $stmt = $conn->prepare("
        SELECT s.id, s.admission_no, s.name, s.class_id, s.father_name, s.contact_no, s.relation, 
               s.total_fees, s.balance, ft.id AS transaction_id, ft.amount, ft.payment_mode, 
               ft.comment, ft.created_at AS transaction_date
        FROM students s
        LEFT JOIN fee_transactions ft ON s.id = ft.student_id
        WHERE s.id = ?
    ");
    $stmt->bind_param("i", $studentId);
    $stmt->execute();
    $result = $stmt->get_result();

    // Check if the student exists
    if ($result->num_rows > 0) {
        $totalPaid = 0;  // To calculate the total fee paid
        $pendingFee = 0; // To calculate the remaining balance (pending fee)

        // Output the rows
        $count = 1; // To keep track of the row number
        while ($row = $result->fetch_assoc()) {
            // Student details
            $studentName = $row['name'];
            $admission_no = $row['admission_no'];
            $class_id = $row['class_id'];
            $fatherName = $row['father_name'];
            $contact_no = $row['contact_no'];
            $relation = $row['relation']; // S/o or D/o
            $totalFees = $row['total_fees'];
            $balance = $row['balance'];

            // Fetch the class name from the classes table (if required)
            $classStmt = $conn->prepare("SELECT class_name FROM class WHERE id = ?");
            $classStmt->bind_param("i", $class_id);
            $classStmt->execute();
            $classResult = $classStmt->get_result();
            $classRow = $classResult->fetch_assoc();
            $className = $classRow['class_name'];

            // Fetch fee transaction details for the current student
            $transactionDate = new DateTime($row['transaction_date']);
            $formattedDate = $transactionDate->format('d-m-Y');
            $amountPaid = $row['amount'];
            $paymentMode = $row['payment_mode'];
            $comment = $row['comment'] ? $row['comment'] : 'No comment provided';

            // Sum the total paid fees
            $totalPaid += $amountPaid;
            
            // Calculate pending fee (if any)
            $pendingFee = $totalFees - $totalPaid;

            // Output the student details row
            echo '<tr>';
            echo '<td>' . $count++ . '</td>';
            echo '<td>' . $formattedDate . '</td>';
            echo '<td>' . $admission_no . '</td>';
            echo '<td>' . $className . '</td>';
            echo '<td>' . $studentName . '</td>';
            echo '<td>' . $fatherName . '</td>';
            echo '<td>' . $contact_no . '</td>';
            echo '<td>' . number_format($amountPaid) . '</td>';
            echo '</tr>';
        }

        // Output the totals row (Total Fee Paid and Pending Fee)
        echo '<tr>';
        echo '<th colspan="7" style="text-align:right;">Total Fee Paid </th>';
        echo '<td>' . number_format($totalPaid) . '</td>';
        echo '</tr>';

        echo '<tr>';
        echo '<th colspan="7" style="text-align:right;">Total Fee Pending </th>';
        echo '<td>' . number_format($pendingFee) . '</td>';
        echo '</tr>';

    } else {
        echo '<tr><td colspan="8">No student found with the given ID.</td></tr>';
    }
}
?>
