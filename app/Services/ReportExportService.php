<?php

namespace App\Services;

use Illuminate\Support\Collection;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use Barryvdh\DomPDF\Facade\Pdf;

class ReportExportService
{
    public function exportExcel(array $data): \Symfony\Component\HttpFoundation\StreamedResponse
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Laporan Kasiva');

        $period = $data['period'] ?? 'SEMUA';
        $omset = $data['omset'] ?? 0;
        $cogsTotal = $data['cogsTotal'] ?? 0;
        $grossProfit = $data['grossProfit'] ?? 0;
        $expenses = $data['expenses'] ?? 0;
        $netProfit = $data['netProfit'] ?? 0;
        $tx = $data['allTx'] ?? collect();
        $exp = $data['allExp'] ?? collect();

        // Title
        $sheet->setCellValue('A1', 'RINGKASAN KASIVA POS — ' . $period . ' • ' . now()->format('d M Y H:i'));
        $sheet->mergeCells('A1:H1');
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(12)->getColor()->setRGB('1E1B4B');
        $sheet->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        // Summary
        $sheet->setCellValue('A2', 'Omset'); $sheet->setCellValue('B2', $omset);
        $sheet->setCellValue('A3', 'Total HPP'); $sheet->setCellValue('B3', $cogsTotal);
        $sheet->setCellValue('A4', 'Laba Kotor'); $sheet->setCellValue('B4', $grossProfit);
        $sheet->setCellValue('A5', 'Pengeluaran'); $sheet->setCellValue('B5', $expenses);
        $sheet->setCellValue('A6', 'Laba Bersih'); $sheet->setCellValue('B6', $netProfit);
        $sheet->getStyle('A2:A6')->getFont()->setBold(true);
        $sheet->getStyle('B2:B6')->getNumberFormat()->setFormatCode('#,##0');
        $sheet->getStyle('B6')->getFont()->setBold(true)->getColor()->setRGB('059669');

        // Transactions header
        $row = 8;
        $sheet->setCellValue("A{$row}", 'TRANSAKSI');
        $sheet->getStyle("A{$row}")->getFont()->setBold(true)->setSize(10);
        $row++;
        $headers = ['No Struk','Tanggal','Metode','Total','HPP','Laba','Kasir','Status'];
        $col = 'A';
        foreach ($headers as $h) {
            $sheet->setCellValue($col.$row, $h);
            $col++;
        }
        $sheet->getStyle("A{$row}:H{$row}")->getFont()->setBold(true)->getColor()->setRGB('FFFFFF');
        $sheet->getStyle("A{$row}:H{$row}")->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('1E1B4B');
        $sheet->getStyle("A{$row}:H{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $row++;
        $startTxRow = $row;
        foreach ($tx as $t) {
            $sheet->setCellValue("A{$row}", $t->receipt_number);
            $sheet->setCellValue("B{$row}", $t->created_at->format('Y-m-d H:i'));
            $sheet->setCellValue("C{$row}", $t->payment_method);
            $sheet->setCellValue("D{$row}", (float)$t->total_amount);
            $sheet->setCellValue("E{$row}", (float)$t->total_hpp);
            $sheet->setCellValue("F{$row}", (float)($t->total_amount - $t->total_hpp));
            $sheet->setCellValue("G{$row}", $t->cashier_name);
            $sheet->setCellValue("H{$row}", $t->status ?? 'COMPLETED');
            $row++;
        }
        if ($tx->count() > 0) {
            $sheet->getStyle("D{$startTxRow}:F".($row-1))->getNumberFormat()->setFormatCode('#,##0');
        }

        // Expenses
        $row++;
        $sheet->setCellValue("A{$row}", 'PENGELUARAN');
        $sheet->getStyle("A{$row}")->getFont()->setBold(true)->setSize(10);
        $row++;
        $expHeaders = ['Judul','Kategori','Jumlah','Tanggal','Catatan'];
        $col = 'A';
        foreach ($expHeaders as $h) {
            $sheet->setCellValue($col.$row, $h);
            $col++;
        }
        $sheet->getStyle("A{$row}:E{$row}")->getFont()->setBold(true)->getColor()->setRGB('FFFFFF');
        $sheet->getStyle("A{$row}:E{$row}")->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('4338CA');
        $sheet->getStyle("A{$row}:E{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $row++;
        $startExpRow = $row;
        foreach ($exp as $e) {
            $sheet->setCellValue("A{$row}", $e->title);
            $sheet->setCellValue("B{$row}", $e->category);
            $sheet->setCellValue("C{$row}", (float)$e->amount);
            $sheet->setCellValue("D{$row}", \Carbon\Carbon::parse($e->expense_date)->format('Y-m-d'));
            $sheet->setCellValue("E{$row}", $e->notes ?? '-');
            $row++;
        }
        if ($exp->count() > 0) {
            $sheet->getStyle("C{$startExpRow}:C".($row-1))->getNumberFormat()->setFormatCode('#,##0');
        }

        // Autosize
        foreach (range('A','H') as $c) {
            $sheet->getColumnDimension($c)->setAutoSize(true);
        }

        $filename = 'kasiva-laporan-' . now()->format('Ymd_His') . '.xlsx';

        return response()->streamDownload(function () use ($spreadsheet) {
            $writer = new Xlsx($spreadsheet);
            $writer->save('php://output');
        }, $filename, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ]);
    }

    public function exportPdf(array $data): \Illuminate\Http\Response
    {
        $pdf = Pdf::loadView('pdf.financial-report', $data)->setPaper('a4', 'landscape');
        return $pdf->download('kasiva-laporan-'.now()->format('Ymd_His').'.pdf');
    }
}
