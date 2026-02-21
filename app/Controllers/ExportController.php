<?php

namespace App\Controllers;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class ExportController extends BaseController
{
    public function journals()
    {
        $companyId = session('company_id');
        $month = $this->request->getGet('month');
        $year  = $this->request->getGet('year');

        if (!$month || !$year) {
            return redirect()->back();
        }

        $db = db_connect();

        $rows = $db->query("
            SELECT
                jh.journal_no,
                jh.journal_date,
                jh.description,
                coa.account_code,
                coa.account_name,
                jd.debit,
                jd.credit
            FROM journal_details jd
            JOIN journal_headers jh ON jh.id = jd.journal_id
            JOIN coa ON coa.id = jd.account_id
            WHERE jh.company_id = ?
              AND jh.period_month = ?
              AND jh.period_year = ?
              AND jh.status = 'posted'
            ORDER BY jh.journal_date, jh.journal_no
        ", [$companyId, $month, $year])->getResultArray();

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // HEADER
        $sheet->setCellValue('A1', 'Journal Export');
        $sheet->setCellValue('A2', 'Period: '.$month.' / '.$year);

        $sheet->setCellValue('A4', 'Journal No');
        $sheet->setCellValue('B4', 'Date');
        $sheet->setCellValue('C4', 'Description');
        $sheet->setCellValue('D4', 'Account Code');
        $sheet->setCellValue('E4', 'Account Name');
        $sheet->setCellValue('F4', 'Debit');
        $sheet->setCellValue('G4', 'Credit');

        $rowIndex = 5;

        foreach ($rows as $row) {

            $sheet->setCellValue('A'.$rowIndex, $row['journal_no']);
            $sheet->setCellValue('B'.$rowIndex, $row['journal_date']);
            $sheet->setCellValue('C'.$rowIndex, $row['description']);
            $sheet->setCellValue('D'.$rowIndex, $row['account_code']);
            $sheet->setCellValue('E'.$rowIndex, $row['account_name']);
            $sheet->setCellValue('F'.$rowIndex, $row['debit']);
            $sheet->setCellValue('G'.$rowIndex, $row['credit']);

            $rowIndex++;
        }

        // AUTO WIDTH
        foreach (range('A','G') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        $writer = new Xlsx($spreadsheet);

        $filename = "Journal_{$month}_{$year}.xlsx";

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header("Content-Disposition: attachment; filename=\"{$filename}\"");
        header('Cache-Control: max-age=0');

        $writer->save('php://output');
        exit;
    }
}