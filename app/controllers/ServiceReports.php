<?php

class ServiceReports
{
    use Controller;

    public function index()
    {
        // Check if user is logged in
        if (!isset($_SESSION['user_id'])) {
            header("Location: " . ROOT . "/Login");
            exit;
        }

        // Check if user has service_provider role
        if (($_SESSION['user_role'] ?? '') !== 'service_provider') {
            header("Location: " . ROOT . "/Home");
            exit;
        }

        $provider_id = $_SESSION['user_id'];
        
        // Load models
        $model = new M_service_provider();
        $paymentModel = new M_payment();
        $requestModel = new M_service_request();
        
        // Get provider details for profile image
        $provider = $model->getProviderById($provider_id);
        
        // Get date range from query or form
        $reportType = $_POST['reportType'] ?? $_GET['type'] ?? null;
        $dateRange = $_POST['dateRange'] ?? $_GET['range'] ?? 'this_month';
        $startDate = $_POST['startDate'] ?? $_GET['startDate'] ?? null;
        $endDate = $_POST['endDate'] ?? $_GET['endDate'] ?? null;
        $exportFormat = $_POST['exportFormat'] ?? null;

        // PRG pattern: convert filter POST to GET so refresh does not trigger resubmission warning.
        if (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST' && empty($exportFormat) && !empty($reportType)) {
            $queryParams = [
                'type' => $reportType,
                'range' => $dateRange
            ];

            if (!empty($startDate)) {
                $queryParams['startDate'] = $startDate;
            }
            if (!empty($endDate)) {
                $queryParams['endDate'] = $endDate;
            }

            header('Location: ' . ROOT . '/ServiceReports?' . http_build_query($queryParams));
            exit;
        }
        
        // Calculate date range
        $dates = $this->calculateDateRange($dateRange, $startDate, $endDate);
        $startDate = $dates['start'];
        $endDate = $dates['end'];
        
        // Get all reports data
        $data = [
            'provider' => $provider,
            'pageTitle' => 'Reports',
            'reportType' => $reportType,
            'dateRange' => $dateRange,
            'start_date' => $startDate,
            'end_date' => $endDate
        ];
        
        // Load report data based on type
        if ($reportType) {
            switch ($reportType) {
                case 'revenue':
                    $data['revenue_summary'] = $paymentModel->getRevenueSummary($provider_id, $startDate, $endDate);
                    $data['revenue_report'] = $paymentModel->getRevenueReport($provider_id, $startDate, $endDate);
                    break;
                case 'bookings':
                    $data['bookings_report'] = $requestModel->getBookingsReport($provider_id, $startDate, $endDate);
                    break;
                case 'performance':
                    $data['service_performance'] = $requestModel->getServicePerformance($provider_id, $startDate, $endDate);
                    break;
                case 'cancellation':
                    $data['cancellation_report'] = $requestModel->getCancellationReport($provider_id, $startDate, $endDate);
                    break;
                default:
                    // Load all reports
                    $data['revenue_summary'] = $paymentModel->getRevenueSummary($provider_id, $startDate, $endDate);
                    $data['revenue_report'] = $paymentModel->getRevenueReport($provider_id, $startDate, $endDate);
                    $data['bookings_report'] = $requestModel->getBookingsReport($provider_id, $startDate, $endDate);
                    $data['service_performance'] = $requestModel->getServicePerformance($provider_id, $startDate, $endDate);
                    $data['cancellation_report'] = $requestModel->getCancellationReport($provider_id, $startDate, $endDate);
            }
            
            // Handle export if format selected
            if ($exportFormat) {
                $this->exportReport($data, $reportType, $exportFormat);
                return;
            }
        } else {
            // Load all reports for overview
            $data['revenue_summary'] = $paymentModel->getRevenueSummary($provider_id, $startDate, $endDate);
            $data['revenue_report'] = $paymentModel->getRevenueReport($provider_id, $startDate, $endDate);
            $data['bookings_report'] = $requestModel->getBookingsReport($provider_id, $startDate, $endDate);
            $data['service_performance'] = $requestModel->getServicePerformance($provider_id, $startDate, $endDate);
            $data['cancellation_report'] = $requestModel->getCancellationReport($provider_id, $startDate, $endDate);
        }
        
        $this->view('service_reports', $data);
    }
    
    /**
     * Calculate start and end dates based on range
     */
    private function calculateDateRange($rangeType, $customStart = null, $customEnd = null)
    {
        $today = new DateTime();
        $start = clone $today;
        $end = clone $today;
        
        switch ($rangeType) {
            case 'this_week':
                $start->modify('monday this week');
                break;
            case 'last_month':
                $start->modify('first day of last month');
                $end->modify('last day of last month');
                break;
            case 'last_3_months':
                $start->modify('-3 months');
                break;
            case 'last_6_months':
                $start->modify('-6 months');
                break;
            case 'this_year':
                $start->modify('first day of january');
                break;
            case 'last_year':
                $start->modify('first day of january last year');
                $end->modify('last day of december last year');
                break;
            case 'custom':
                if ($customStart && $customEnd) {
                    $start = DateTime::createFromFormat('Y-m-d', $customStart);
                    $end = DateTime::createFromFormat('Y-m-d', $customEnd);
                }
                break;
            default: // this_month
                $start->modify('first day of');
                break;
        }
        
        return [
            'start' => $start->format('Y-m-d') . ' 00:00:00',
            'end' => $end->format('Y-m-d') . ' 23:59:59'
        ];
    }
    
    /**
     * Export report in selected format
     */
    private function exportReport($data, $reportType, $format)
    {
        $filename = $reportType . '_report_' . date('Y-m-d');
        
        switch ($format) {
            case 'csv':
                $this->exportCSV($data, $reportType, $filename);
                break;
            case 'pdf':
                $this->exportPDF($data, $reportType, $filename);
                break;
            case 'excel':
                $this->exportExcel($data, $reportType, $filename);
                break;
        }
    }
    
    /**
     * Export as CSV
     */
    private function exportCSV($data, $reportType, $filename)
    {
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="' . $filename . '.csv"');

        $output = fopen('php://output', 'w');

        switch ($reportType) {
            case 'revenue':
                fputcsv($output, ['Revenue Report']);
                fputcsv($output, ['Period', date('d M Y', strtotime($data['start_date'])) . ' to ' . date('d M Y', strtotime($data['end_date']))]);
                fputcsv($output, []);
                fputcsv($output, ['Total Revenue', 'Rs ' . number_format($data['revenue_summary']->total_revenue ?? 0, 2)]);
                fputcsv($output, ['Total Transactions', $data['revenue_summary']->total_transactions ?? 0]);
                fputcsv($output, []);
                fputcsv($output, ['Transaction ID', 'Drama/Project', 'Service Type', 'Payment Type', 'Payment Method', 'Amount', 'Date', 'Reference']);
                foreach ($data['revenue_report'] as $row) {
                    fputcsv($output, [
                        'PAY-' . str_pad($row->id, 5, '0', STR_PAD_LEFT),
                        $row->drama_name ?? 'N/A',
                        $row->service_type ?? 'N/A',
                        $row->payment_type ?? 'Booking',
                        $row->payment_gateway ?? 'N/A',
                        'Rs ' . number_format($row->amount ?? 0, 2),
                        date('d M Y', strtotime($row->paid_at ?? $row->created_at)),
                        $row->reference_number ?? 'N/A'
                    ]);
                }
                break;

            case 'bookings':
                fputcsv($output, ['Bookings Report']);
                fputcsv($output, ['Period', date('d M Y', strtotime($data['start_date'])) . ' to ' . date('d M Y', strtotime($data['end_date']))]);
                fputcsv($output, []);
                fputcsv($output, ['Request ID', 'Drama/Project', 'Service Type', 'Client', 'Budget', 'Duration (Days)', 'Status', 'Payment Status']);
                foreach ($data['bookings_report'] as $row) {
                    $paymentStatus = (isset($row->amount_paid) && ($row->amount_paid >= $row->budget)) ? 'Completed' : ((isset($row->amount_paid) && $row->amount_paid > 0) ? 'Partial' : 'Pending');
                    $duration = (isset($row->end_date) && $row->end_date) ? ((strtotime($row->end_date) - strtotime($row->start_date)) / (24 * 3600)) : 0;
                    fputcsv($output, [
                        'REQ-' . str_pad($row->id, 5, '0', STR_PAD_LEFT),
                        $row->drama_name ?? 'N/A',
                        $row->service_type ?? 'N/A',
                        $row->client_name ?? 'N/A',
                        'Rs ' . number_format($row->budget ?? 0, 2),
                        (int) $duration,
                        ucfirst(str_replace('_', ' ', $row->status ?? 'pending')),
                        $paymentStatus
                    ]);
                }
                break;

            case 'performance':
                fputcsv($output, ['Service Performance Report']);
                fputcsv($output, ['Period', date('d M Y', strtotime($data['start_date'])) . ' to ' . date('d M Y', strtotime($data['end_date']))]);
                fputcsv($output, []);
                fputcsv($output, ['Request ID', 'Drama/Project', 'Service Type', 'Client', 'Amount', 'Amount Paid', 'Duration (Days)', 'Completed']);
                foreach ($data['service_performance'] as $row) {
                    $duration = (isset($row->end_date) && $row->end_date) ? ((strtotime($row->end_date) - strtotime($row->start_date)) / (24 * 3600)) : 0;
                    fputcsv($output, [
                        'REQ-' . str_pad($row->id, 5, '0', STR_PAD_LEFT),
                        $row->drama_name ?? 'N/A',
                        $row->service_type ?? 'N/A',
                        $row->client_name ?? 'N/A',
                        'Rs ' . number_format($row->amount ?? $row->amount_paid ?? 0, 2),
                        'Rs ' . number_format($row->amount_paid ?? 0, 2),
                        (int) $duration,
                        ($row->is_completed ?? 0) ? 'Yes' : 'Pending'
                    ]);
                }
                break;

            case 'cancellation':
                fputcsv($output, ['Cancellation/Rejection Report']);
                fputcsv($output, ['Period', date('d M Y', strtotime($data['start_date'])) . ' to ' . date('d M Y', strtotime($data['end_date']))]);
                fputcsv($output, []);
                fputcsv($output, ['Request ID', 'Drama/Project', 'Service Type', 'Client', 'Budget', 'Date', 'Status', 'Reason']);
                foreach ($data['cancellation_report'] as $row) {
                    fputcsv($output, [
                        'REQ-' . str_pad($row->id, 5, '0', STR_PAD_LEFT),
                        $row->drama_name ?? 'N/A',
                        $row->service_type ?? 'N/A',
                        $row->client_name ?? 'N/A',
                        'Rs ' . number_format($row->budget ?? 0, 2),
                        date('d M Y', strtotime($row->created_at)),
                        ucfirst($row->status ?? 'cancelled'),
                        $row->rejection_reason ?? 'N/A'
                    ]);
                }
                break;
        }

        fclose($output);
        exit;
    }
    
    /**
     * Export as PDF with professional table layout (no external libraries).
     */
    private function exportPDF($data, $reportType, $filename)
    {
        $report = $this->preparePdfReportData($data, $reportType);
        $period = date('d M Y', strtotime($data['start_date'])) . ' to ' . date('d M Y', strtotime($data['end_date']));
        $pdf = $this->buildProfessionalPdf($report, $period, $filename);

        header('Content-Type: application/pdf');
        header('Content-Disposition: attachment; filename="' . $filename . '.pdf"');
        header('Content-Length: ' . strlen($pdf));
        echo $pdf;
        exit;
    }

    private function preparePdfReportData($data, $reportType)
    {
        $report = [
            'title' => ucfirst(str_replace('_', ' ', $reportType)) . ' Report',
            'summary' => [],
            'headers' => [],
            'rows' => [],
            'widths' => []
        ];

        switch ($reportType) {
            case 'revenue':
                $report['summary'] = [
                    ['label' => 'Total Revenue', 'value' => 'Rs ' . number_format($data['revenue_summary']->total_revenue ?? 0, 2)],
                    ['label' => 'Total Transactions', 'value' => (string) ($data['revenue_summary']->total_transactions ?? 0)]
                ];
                $report['headers'] = ['Transaction ID', 'Drama', 'Service', 'Method', 'Amount', 'Date'];
                $report['widths'] = [84, 108, 84, 70, 72, 80];
                foreach (($data['revenue_report'] ?? []) as $row) {
                    $report['rows'][] = [
                        'PAY-' . str_pad($row->id, 5, '0', STR_PAD_LEFT),
                        $row->drama_name ?? 'N/A',
                        ucfirst(str_replace('_', ' ', $row->service_type ?? 'N/A')),
                        ucfirst($row->payment_gateway ?? 'N/A'),
                        'Rs ' . number_format($row->amount ?? 0, 2),
                        date('d M Y', strtotime($row->paid_at ?? $row->created_at))
                    ];
                }
                break;

            case 'bookings':
                $report['headers'] = ['Request ID', 'Drama', 'Service', 'Client', 'Budget', 'Status'];
                $report['widths'] = [84, 100, 84, 92, 72, 66];
                foreach (($data['bookings_report'] ?? []) as $row) {
                    $report['rows'][] = [
                        'REQ-' . str_pad($row->id, 5, '0', STR_PAD_LEFT),
                        $row->drama_name ?? 'N/A',
                        ucfirst(str_replace('_', ' ', $row->service_type ?? 'N/A')),
                        $row->requester_name ?? 'N/A',
                        'Rs ' . number_format($row->budget ?? 0, 2),
                        ucfirst(str_replace('_', ' ', $row->status ?? 'pending'))
                    ];
                }
                break;

            case 'performance':
                $report['headers'] = ['Request ID', 'Drama', 'Service', 'Amount', 'Paid', 'Completed'];
                $report['widths'] = [84, 110, 96, 72, 72, 64];
                foreach (($data['service_performance'] ?? []) as $row) {
                    $report['rows'][] = [
                        'REQ-' . str_pad($row->id, 5, '0', STR_PAD_LEFT),
                        $row->drama_name ?? 'N/A',
                        ucfirst(str_replace('_', ' ', $row->service_type ?? 'N/A')),
                        'Rs ' . number_format($row->amount ?? $row->amount_paid ?? 0, 2),
                        'Rs ' . number_format($row->amount_paid ?? 0, 2),
                        ($row->is_completed ?? 0) ? 'Yes' : 'Pending'
                    ];
                }
                break;

            case 'cancellation':
                $report['headers'] = ['Request ID', 'Drama', 'Service', 'Budget', 'Status', 'Date'];
                $report['widths'] = [84, 110, 96, 72, 64, 72];
                foreach (($data['cancellation_report'] ?? []) as $row) {
                    $report['rows'][] = [
                        'REQ-' . str_pad($row->id, 5, '0', STR_PAD_LEFT),
                        $row->drama_name ?? 'N/A',
                        ucfirst(str_replace('_', ' ', $row->service_type ?? 'N/A')),
                        'Rs ' . number_format($row->budget ?? 0, 2),
                        ucfirst($row->status ?? 'cancelled'),
                        date('d M Y', strtotime($row->created_at ?? 'now'))
                    ];
                }
                break;
        }

        return $report;
    }

    private function buildProfessionalPdf(array $report, $period, $fileTitle)
    {
        $pageWidth = 595;
        $pageHeight = 842;
        $left = 36;
        $right = 36;
        $top = 42;
        $bottom = 42;
        $tableWidth = $pageWidth - $left - $right;
        $lineHeight = 16;
        $headerHeight = 22;
        $rowHeight = 20;

        $streams = [];
        $stream = '';
        $yTop = $top;
        $isFirstPage = true;

        $startNewPage = function () use (&$stream, &$yTop, $top, &$isFirstPage, &$streams, $report, $period, $left, $pageHeight, $lineHeight, $headerHeight, $tableWidth) {
            if ($stream !== '') {
                $streams[] = $stream;
            }
            $stream = '';
            $yTop = $top;

            $title = $report['title'] . ($isFirstPage ? '' : ' (Cont.)');
            $stream .= $this->pdfText($left, $this->toPdfY($yTop, $pageHeight), $title, 'F2', 16);
            $yTop += $lineHeight + 4;
            $stream .= $this->pdfText($left, $this->toPdfY($yTop, $pageHeight), 'Period: ' . $period, 'F1', 10);
            $yTop += $lineHeight + 4;

            if ($isFirstPage && !empty($report['summary'])) {
                foreach ($report['summary'] as $summaryRow) {
                    $line = $summaryRow['label'] . ': ' . $summaryRow['value'];
                    $stream .= $this->pdfText($left, $this->toPdfY($yTop, $pageHeight), $line, 'F1', 10);
                    $yTop += $lineHeight;
                }
                $yTop += 6;
            }

            // Header background and border
            $stream .= $this->pdfRect($left, $yTop, $tableWidth, $headerHeight, $pageHeight, true, false, 0.95, 0.95, 0.95);
            $stream .= $this->pdfRect($left, $yTop, $tableWidth, $headerHeight, $pageHeight, false, true);

            $x = $left;
            foreach ($report['headers'] as $i => $header) {
                $colWidth = $report['widths'][$i];
                if ($i > 0) {
                    $stream .= $this->pdfLine($x, $yTop, $x, $yTop + $headerHeight, $pageHeight);
                }
                $stream .= $this->pdfText($x + 4, $this->toPdfY($yTop + 15, $pageHeight), $this->truncateCell($header, $colWidth, 9), 'F2', 9);
                $x += $colWidth;
            }

            $yTop += $headerHeight;
            $isFirstPage = false;
        };

        $startNewPage();

        if (empty($report['rows'])) {
            $stream .= $this->pdfRect($left, $yTop, $tableWidth, $rowHeight, $pageHeight, false, true);
            $stream .= $this->pdfText($left + 6, $this->toPdfY($yTop + 14, $pageHeight), 'No data available for this report.', 'F1', 9);
            $yTop += $rowHeight;
        } else {
            foreach ($report['rows'] as $row) {
                if ($yTop + $rowHeight > ($pageHeight - $bottom)) {
                    $startNewPage();
                }

                $stream .= $this->pdfRect($left, $yTop, $tableWidth, $rowHeight, $pageHeight, false, true);

                $x = $left;
                foreach ($report['widths'] as $i => $colWidth) {
                    if ($i > 0) {
                        $stream .= $this->pdfLine($x, $yTop, $x, $yTop + $rowHeight, $pageHeight);
                    }
                    $value = isset($row[$i]) ? $row[$i] : '';
                    $stream .= $this->pdfText($x + 4, $this->toPdfY($yTop + 13, $pageHeight), $this->truncateCell($value, $colWidth, 8), 'F1', 8);
                    $x += $colWidth;
                }

                $yTop += $rowHeight;
            }
        }

        if ($stream !== '') {
            $streams[] = $stream;
        }

        return $this->composePdfDocument($streams, $fileTitle);
    }

    private function escapePdfText($text)
    {
        $text = str_replace(['\\', '(', ')'], ['\\\\', '\\(', '\\)'], (string) $text);
        return preg_replace('/[^\x09\x0A\x0D\x20-\x7E]/', '', $text);
    }

    private function truncateCell($text, $width, $fontSize)
    {
        $text = trim(preg_replace('/\s+/', ' ', (string) $text));
        $maxChars = (int) floor(($width - 8) / max(4.5, $fontSize * 0.5));
        if ($maxChars < 2) {
            return '';
        }
        if (strlen($text) <= $maxChars) {
            return $text;
        }
        return substr($text, 0, $maxChars - 1) . '.';
    }

    private function toPdfY($yTop, $pageHeight)
    {
        return $pageHeight - $yTop;
    }

    private function pdfText($x, $y, $text, $font = 'F1', $size = 10)
    {
        return 'BT /' . $font . ' ' . $size . ' Tf 1 0 0 1 ' . number_format($x, 2, '.', '') . ' ' . number_format($y, 2, '.', '') . ' Tm (' . $this->escapePdfText($text) . ") Tj ET\n";
    }

    private function pdfLine($x1, $yTop1, $x2, $yTop2, $pageHeight)
    {
        $y1 = $this->toPdfY($yTop1, $pageHeight);
        $y2 = $this->toPdfY($yTop2, $pageHeight);
        return number_format($x1, 2, '.', '') . ' ' . number_format($y1, 2, '.', '') . ' m ' . number_format($x2, 2, '.', '') . ' ' . number_format($y2, 2, '.', '') . " l S\n";
    }

    private function pdfRect($x, $yTop, $w, $h, $pageHeight, $fill = false, $stroke = true, $r = 1, $g = 1, $b = 1)
    {
        $yBottom = $pageHeight - ($yTop + $h);
        $rect = number_format($x, 2, '.', '') . ' ' . number_format($yBottom, 2, '.', '') . ' ' . number_format($w, 2, '.', '') . ' ' . number_format($h, 2, '.', '') . ' re ';
        if ($fill && $stroke) {
            return 'q ' . $r . ' ' . $g . ' ' . $b . ' rg ' . $rect . "B Q\n";
        }
        if ($fill) {
            return 'q ' . $r . ' ' . $g . ' ' . $b . ' rg ' . $rect . "f Q\n";
        }
        if ($stroke) {
            return $rect . "S\n";
        }
        return '';
    }

    private function composePdfDocument(array $streams, $title)
    {
        $objects = [];
        $fontRegularId = 1;
        $fontBoldId = 2;
        $catalogId = 3;
        $pagesId = 4;
        $nextId = 5;
        $pageIds = [];
        $contentIds = [];

        foreach ($streams as $stream) {
            $pageIds[] = $nextId++;
            $contentIds[] = $nextId++;
        }

        $objects[$fontRegularId] = '<< /Type /Font /Subtype /Type1 /BaseFont /Helvetica >>';
        $objects[$fontBoldId] = '<< /Type /Font /Subtype /Type1 /BaseFont /Helvetica-Bold >>';

        $kids = [];
        foreach ($streams as $index => $stream) {
            $pageId = $pageIds[$index];
            $contentId = $contentIds[$index];
            $kids[] = $pageId . ' 0 R';

            $objects[$contentId] = '<< /Length ' . strlen($stream) . " >>\nstream\n" . $stream . "\nendstream";
            $objects[$pageId] = '<< /Type /Page /Parent ' . $pagesId . ' 0 R /MediaBox [0 0 595 842] /Resources << /Font << /F1 ' . $fontRegularId . ' 0 R /F2 ' . $fontBoldId . ' 0 R >> >> /Contents ' . $contentId . ' 0 R >>';
        }

        $objects[$pagesId] = '<< /Type /Pages /Kids [' . implode(' ', $kids) . '] /Count ' . count($streams) . ' >>';
        $objects[$catalogId] = '<< /Type /Catalog /Pages ' . $pagesId . ' 0 R >>';

        $pdf = "%PDF-1.4\n";
        $offsets = [0];
        for ($i = 1; $i <= count($objects); $i++) {
            $offsets[$i] = strlen($pdf);
            $pdf .= $i . " 0 obj\n" . $objects[$i] . "\nendobj\n";
        }

        $xrefStart = strlen($pdf);
        $pdf .= "xref\n";
        $pdf .= '0 ' . (count($objects) + 1) . "\n";
        $pdf .= "0000000000 65535 f \n";
        for ($i = 1; $i <= count($objects); $i++) {
            $pdf .= sprintf('%010d 00000 n ', $offsets[$i]) . "\n";
        }
        $pdf .= "trailer\n<< /Size " . (count($objects) + 1) . ' /Root ' . $catalogId . " 0 R >>\n";
        $pdf .= "startxref\n" . $xrefStart . "\n%%EOF";

        return $pdf;
    }
    
    /**
     * Export as Excel (CSV with Excel formatting)
     */
    private function exportExcel($data, $reportType, $filename)
    {
        header('Content-Type: application/vnd.ms-excel; charset=utf-8');
        header('Content-Disposition: attachment; filename="' . $filename . '.xls"');
        
        $output = fopen('php://output', 'w');
        fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF)); // BOM for UTF-8
        
        switch ($reportType) {
            case 'revenue':
                fputcsv($output, ['Revenue Report']);
                fputcsv($output, ['Period', date('d M Y', strtotime($data['start_date'])) . ' to ' . date('d M Y', strtotime($data['end_date']))]);
                fputcsv($output, []);
                fputcsv($output, ['Total Revenue', 'Rs ' . number_format($data['revenue_summary']->total_revenue ?? 0, 2)]);
                fputcsv($output, ['Total Transactions', $data['revenue_summary']->total_transactions ?? 0]);
                fputcsv($output, []);
                fputcsv($output, ['Transaction ID', 'Drama/Project', 'Service Type', 'Payment Type', 'Payment Method', 'Amount', 'Date', 'Reference']);
                foreach ($data['revenue_report'] as $row) {
                    fputcsv($output, [
                        $row->id,
                        $row->drama_name ?? 'N/A',
                        $row->service_type ?? 'N/A',
                        $row->payment_type ?? 'Booking',
                        $row->payment_gateway ?? 'N/A',
                        'Rs ' . number_format($row->amount ?? 0, 2),
                        date('d M Y', strtotime($row->paid_at ?? $row->created_at)),
                        $row->reference_number ?? 'N/A'
                    ]);
                }
                break;
                
            case 'bookings':
                fputcsv($output, ['Bookings Report']);
                fputcsv($output, ['Period', date('d M Y', strtotime($data['start_date'])) . ' to ' . date('d M Y', strtotime($data['end_date']))]);
                fputcsv($output, []);
                fputcsv($output, ['Request ID', 'Drama/Project', 'Service Type', 'Client', 'Budget', 'Duration (Days)', 'Status', 'Payment Status']);
                foreach ($data['bookings_report'] as $row) {
                    $paymentStatus = (isset($row->amount_paid) && ($row->amount_paid >= $row->budget)) ? 'Completed' : ((isset($row->amount_paid) && $row->amount_paid > 0) ? 'Partial' : 'Pending');
                    $duration = (isset($row->end_date) && $row->end_date) ? ((strtotime($row->end_date) - strtotime($row->start_date)) / (24 * 3600)) : 0;
                    fputcsv($output, [
                        'REQ-' . str_pad($row->id, 5, '0', STR_PAD_LEFT),
                        $row->drama_name ?? 'N/A',
                        $row->service_type ?? 'N/A',
                        $row->client_name ?? 'N/A',
                        'Rs ' . number_format($row->budget ?? 0, 2),
                        (int)$duration,
                        ucfirst(str_replace('_', ' ', $row->status ?? 'pending')),
                        $paymentStatus
                    ]);
                }
                break;
                
            case 'performance':
                fputcsv($output, ['Service Performance Report']);
                fputcsv($output, ['Period', date('d M Y', strtotime($data['start_date'])) . ' to ' . date('d M Y', strtotime($data['end_date']))]);
                fputcsv($output, []);
                fputcsv($output, ['Request ID', 'Drama/Project', 'Service Type', 'Client', 'Amount', 'Amount Paid', 'Duration (Days)', 'Completed']);
                foreach ($data['service_performance'] as $row) {
                    $duration = (isset($row->end_date) && $row->end_date) ? ((strtotime($row->end_date) - strtotime($row->start_date)) / (24 * 3600)) : 0;
                    $completed = ($row->is_completed ?? 0) ? 'Yes' : 'No';
                    fputcsv($output, [
                        'REQ-' . str_pad($row->id, 5, '0', STR_PAD_LEFT),
                        $row->drama_name ?? 'N/A',
                        $row->service_type ?? 'N/A',
                        $row->client_name ?? 'N/A',
                        'Rs ' . number_format($row->amount ?? $row->amount_paid ?? 0, 2),
                        'Rs ' . number_format($row->amount_paid ?? 0, 2),
                        (int)$duration,
                        $completed
                    ]);
                }
                break;
                
            case 'cancellation':
                fputcsv($output, ['Cancellation/Rejection Report']);
                fputcsv($output, ['Period', date('d M Y', strtotime($data['start_date'])) . ' to ' . date('d M Y', strtotime($data['end_date']))]);
                fputcsv($output, []);
                fputcsv($output, ['Request ID', 'Drama/Project', 'Service Type', 'Client', 'Budget', 'Date', 'Status', 'Reason']);
                foreach ($data['cancellation_report'] as $row) {
                    fputcsv($output, [
                        'REQ-' . str_pad($row->id, 5, '0', STR_PAD_LEFT),
                        $row->drama_name ?? 'N/A',
                        $row->service_type ?? 'N/A',
                        $row->client_name ?? 'N/A',
                        'Rs ' . number_format($row->budget ?? 0, 2),
                        date('d M Y', strtotime($row->created_at)),
                        ucfirst($row->status ?? 'cancelled'),
                        $row->rejection_reason ?? 'N/A'
                    ]);
                }
                break;
        }
        
        fclose($output);
        exit;
    }
}
