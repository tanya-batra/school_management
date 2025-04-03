<?php
include('db_connect.php');

// Initialize transactions as an empty array
$transactions = [];
$totalPaid = 0; // To track the total fee paid

// Check if the student ID is provided and fetch data
if (isset($_GET['id'])) {
    $studentId = $_GET['id'];

    // Prepare the query to fetch student details and fee transactions by student ID
    $stmt = $conn->prepare("
        SELECT s.id, s.admission_no, s.name, s.class_id, s.father_name, s.contact_no, s.relation, 
               s.total_fees, s.balance, ft.id AS transaction_id, ft.amount, ft.payment_mode, 
               ft.comment, ft.created_at AS transaction_date, c.class_name
        FROM students s
        LEFT JOIN fee_transactions ft ON s.id = ft.student_id
        LEFT JOIN class c ON s.class_id = c.id  -- Join the class table
        WHERE s.id = ?
    ");
    $stmt->bind_param("i", $studentId);
    $stmt->execute();
    $result = $stmt->get_result();

    // Check if student exists
    if ($result->num_rows > 0) {
        // Fetch the data into the $transactions array
        while ($row = $result->fetch_assoc()) {
            $transactions[] = $row;
            $totalPaid += $row['amount']; // Sum up the paid amounts
        }
    } else {
        echo "No student found with the given ID.";
    }
}

// Handling Export PDF and Excel Logic
if (isset($_GET['action']) && ($_GET['action'] == 'export_pdf' || $_GET['action'] == 'export_excel')) {
    // Fetch student details to get name and admission number for file name
    $studentName = $transactions[0]['name'] ?? 'Student';
    $admissionNo = $transactions[0]['admission_no'] ?? 'NA';

    // PDF Export Logic
    if ($_GET['action'] == 'export_pdf') {
        require_once 'vendor/autoload.php'; // Make sure Dompdf is installed
        $dompdf = new Dompdf\Dompdf();
        $dompdf->set_option('isHtml5ParserEnabled', true);

        // Start HTML for PDF
        $html = '<h2 style="font-family: Plus Jakarta Sans,sans-serif;">Fee Transactions</h2>';
        $html .= '<style>
                    table { width: 100%; border-collapse: collapse; font-family: Plus Jakarta Sans,sans-serif; }
                    th, td { padding: 8px; text-align: left; border: 1px solid #ddd; }
                    th { background-color: #f2f2f2; }
                  </style>';
        $html .= '<table>';
        $html .= '<thead>
                    <tr>
                        <th>Sr&nbsp;No.</th>
                        <th width="15%">Date</th>
                        <th>Admn.&nbsp;No.</th>
                        <th>Class</th>
                        <th>Student&nbsp;Name</th>
                        <th>Father&nbsp;Name</th>
                        <!--<th>Contact&nbsp;No</th>-->
                        <th>Amount&nbsp;Paid</th>                       
                    </tr>
                  </thead>';
        $html .= '<tbody>';

        $sr_no = 1;
        foreach ($transactions as $transaction) {
            $transactionDate = new DateTime($transaction['transaction_date']);
            $formattedDate = $transactionDate->format('d-m-Y');

            $html .= '<tr>';
            $html .= '<td>' . $sr_no++ . '</td>';
            $html .= '<td width="15%">' . $formattedDate . '</td>';
            $html .= '<td>' . $transaction['admission_no'] . '</td>';
            $html .= '<td>' . $transaction['class_name'] . '</td>';
            $html .= '<td>' . $transaction['name'] . '</td>';
            $html .= '<td>' . $transaction['father_name'] . '</td>';
            //$html .= '<td>' . $transaction['contact_no'] . '</td>';
            $html .= '<td>' . number_format($transaction['amount'], 2) . '</td>';
            // $html .= '<td>' . $transaction['payment_mode'] . '</td>';
            $html .= '</tr>';
        }

        // Add Total Fee Paid and Pending
        $pendingFee = $transactions[0]['total_fees'] - $totalPaid;
        $html .= '<tr><th colspan="6" style="text-align:right;">Total Fee Paid</th><td>' . number_format($totalPaid, 2) . '</td></tr>';
        $html .= '<tr><th colspan="6" style="text-align:right;">Total Fee Pending</th><td>' . number_format($pendingFee, 2) . '</td></tr>';

        $html .= '</tbody>';
        $html .= '</table>';

        // Load HTML content to Dompdf
        $dompdf->loadHtml($html);
        $dompdf->set_paper('A4', 'portrait');
        $dompdf->render();

        // Set file name with student name and admission number
        $filename = 'studentwse_report_' . $studentName . '_' . $admissionNo . '.pdf';
        $dompdf->stream($filename, array("Attachment" => 1));
        exit;
    }

    // Excel Export Logic (CSV)
    if ($_GET['action'] == 'export_excel') {
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="studentwse_report_' . $studentName . '_' . $admissionNo . '.csv"');
        header('Pragma: no-cache');
        header('Expires: 0');

        $output = fopen('php://output', 'w');
        
        // Write headers for CSV file
        fputcsv($output, ['Sr No.', 'Date', 'Admission No.', 'Class', 'Student Name', 'Father Name', 'Contact No', 'Amount Paid']);

        $sr_no = 1;
        foreach ($transactions as $transaction) {
            $transactionDate = new DateTime($transaction['transaction_date']);
            $formattedDate = $transactionDate->format('d-m-Y');

            fputcsv($output, [
                $sr_no++,
                $formattedDate,
                $transaction['admission_no'],
                $transaction['class_name'],  // class_name
                $transaction['name'],
                $transaction['father_name'],
                $transaction['contact_no'],
                number_format($transaction['amount'], 2),
                // $transaction['payment_mode']
            ]);
        }

        // Add Total Fee Paid and Pending
        $pendingFee = $transactions[0]['total_fees'] - $totalPaid;
        fputcsv($output, ['', '', '', '', '', '', 'Total Fee Paid', number_format($totalPaid, 2)]);
        fputcsv($output, ['', '', '', '', '', '', 'Total Fee Pending', number_format($pendingFee, 2)]);

        fclose($output);
        exit;
    }
}
?>  