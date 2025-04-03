<?php
session_start();

if (!isset($_SESSION['email'])) {
    header("Location: index.php");
    exit;
}
include('db_connect.php');
$sql = "SELECT * FROM session WHERE status = 1 LIMIT 1"; // Only get the active session
$result = mysqli_query($conn, $sql);

if (mysqli_num_rows($result) > 0) {
    // Session is found, get the session data
    $sessionData = mysqli_fetch_assoc($result);
    $session = $sessionData['session']; 
} else {
    // No active session found, handle accordingly
    $_SESSION['errorMessage'] = 'No active session found.';
    header('Location: dashboard.php');
    exit;
}

// Fetch transactions based on the selected date range (this should be same as your main page)
if (isset($_GET['action']) && ($_GET['action'] == 'export_pdf' || $_GET['action'] == 'export_excel')) {
    // Get date range from URL or defaults to today's date
    $from_date = isset($_GET['from_date']) ? $_GET['from_date'] : date('Y-m-d');
    $to_date = isset($_GET['to_date']) ? $_GET['to_date'] : date('Y-m-d');

    // Query to fetch the transactions within the date range and active session
    $query = "SELECT * FROM staff_expenditure WHERE date BETWEEN '$from_date' AND '$to_date' AND session = '$session'";

    // Execute query and fetch transactions
    $result = mysqli_query($conn, $query);
    $transactions = [];

    if (mysqli_num_rows($result) > 0) {
        while ($row = mysqli_fetch_assoc($result)) {
            $transactions[] = $row;
        }
    } else {
        $_SESSION['errorMessage'] = 'No records found for the selected date range.';
        header('Location: dashboard.php');
        exit;
    }

    // Handle PDF Export
    if ($_GET['action'] == 'export_pdf') {
        require_once 'vendor/autoload.php'; 

        $dompdf = new Dompdf\Dompdf();
        $dompdf->set_option('isHtml5ParserEnabled', true);

        $html = '<h2>Datewise Report</h2>';
		$html .= '<style>
                    table { width: 100%; border-collapse: collapse; font-family: Plus Jakarta Sans,sans-serif; }
                    th, td { padding: 8px; text-align: left; border: 1px solid #ddd; }
                    th { background-color: #f2f2f2; }
                  </style>';
        $html .= '<table>';
        $html .= '<thead>
                    <tr>
                    <th>Sr&nbsp;No.</th>
                    <th>Date</th>
                    <th>Comments</th>
                    <th>Expenditure&nbsp;Amount</th>
                    </tr>
                </thead>';
        $html .= '<tbody>';

        $sr_no = 1;
        foreach ($transactions as $transaction) {
            $html .= '<tr>';
            $html .= '<td>' . $sr_no . '</td>';
            $html .= '<td>' . date('d-m-Y', strtotime($transaction['date'])) . '</td>';
            $html .= '<td>' . htmlspecialchars($transaction['comment']) . '</td>';
            $html .= '<td>' . number_format($transaction['amount'], 2) . '</td>';            
            $html .= '</tr>';
            $sr_no++;


           
        }

        $html .= '</tbody>';
        $html .= '</table>';

        $dompdf->loadHtml($html);
        $dompdf->set_paper('A4', 'portrait');
        $dompdf->render();
        $dompdf->stream("expendture_report_".(new DateTime($from_date))->format('d-m-Y'). "_to_" .(new DateTime($to_date))->format('d-m-Y').".pdf", array("Attachment" => 1));
        exit;
    }

    // Handle Excel (CSV) Export
    elseif ($_GET['action'] == 'export_excel') {
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="expendture_report_'. (new DateTime($from_date))->format('d-m-Y') . '_to_' .(new DateTime($to_date))->format('d-m-Y'). '.csv"');
        header('Pragma: no-cache');
        header('Expires: 0');

        $output = fopen('php://output', 'w');
        fputcsv($output, ['Sr No.', 'Date', 'Comments', 'Amount Received']);

        $sr_no = 1;
        foreach ($transactions as $transaction) {
            fputcsv($output, [
                $sr_no,
                date('Y-m-d', strtotime($transaction['date'])),
                htmlspecialchars($transaction['comment']),
                number_format($transaction['amount'])
            ]);
            $sr_no++;
        }

        fclose($output);
        exit;
    }
} else {
    // Redirect or handle error if no active session or action
    $_SESSION['errorMessage'] = "Invalid action or session.";
    header('Location: index.php');
    exit;
}
?>
