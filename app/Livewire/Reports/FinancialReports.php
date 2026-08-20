<?php

namespace App\Livewire\Reports;

use App\Models\Expense;
use App\Models\Product;
use App\Models\Transaction;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.app')]
#[Title('Laporan Keuangan & Analisis AI - Kasiva POS')]
class FinancialReports extends Component
{
    public string $period = 'HARI_INI'; // 'HARI_INI', 'BULAN_INI', 'SEMUA', 'CUSTOM'
    public ?string $startDate = null;
    public ?string $endDate = null;

    public function mount(): void
    {
        abort_unless(auth()->check() && auth()->user()->hasPermission('VIEW_REPORTS'), 403, 'Akses Ditolak: Anda tidak memiliki izin untuk melihat laporan keuangan.');
    }

    public function setPeriod(string $p): void
    {
        $this->period = $p;
    }

    public function render()
    {
        $queryTx = Transaction::query();
        $queryExp = Expense::query();

        if ($this->period === 'HARI_INI') {
            $queryTx->whereDate('created_at', now()->today());
            $queryExp->whereDate('expense_date', now()->today());
        } elseif ($this->period === 'BULAN_INI') {
            $queryTx->whereMonth('created_at', now()->month)->whereYear('created_at', now()->year);
            $queryExp->whereMonth('expense_date', now()->month)->whereYear('expense_date', now()->year);
        } elseif ($this->period === 'CUSTOM' && $this->startDate && $this->endDate) {
            $queryTx->whereBetween('created_at', [$this->startDate . ' 00:00:00', $this->endDate . ' 23:59:59']);
            $queryExp->whereBetween('expense_date', [$this->startDate . ' 00:00:00', $this->endDate . ' 23:59:59']);
        }

        $allTx = (clone $queryTx)->get();
        $allExp = (clone $queryExp)->get();

        $omset = (float)$allTx->sum('total_amount');
        $cogsTotal = (float)$allTx->sum('total_hpp');
        $grossProfit = $omset - $cogsTotal;

        // Platform Adjustment Calculation
        $platformAdjustment = 0.0;
        foreach ($allTx as $tx) {
            if ($tx->is_adjustment) {
                // If adjusted transaction, difference from items sum
                $itemsSum = (float)$tx->items()->sum('subtotal');
                if ($itemsSum > 0) {
                    $platformAdjustment += ((float)$tx->total_amount - $itemsSum);
                }
            }
        }

        $expenses = (float)$allExp->sum('amount');
        $netProfit = $grossProfit - $expenses;
        $modalReturn = $cogsTotal;
        $trueProfit = $netProfit;

        $txCount = $allTx->count();
        $expenseCount = $allExp->count();

        // Payment Method Distribution
        $paymentMethods = [];
        $groupedPayments = $allTx->groupBy('payment_method');
        foreach ($groupedPayments as $method => $txs) {
            $paymentMethods[] = [
                'method' => $method ?: 'TUNAI',
                'total' => (float)$txs->sum('total_amount'),
                'count' => $txs->count(),
            ];
        }
        usort($paymentMethods, fn($a, $b) => $b['total'] <=> $a['total']);

        // AI Advisor & Margin Projections
        $grossMarginPercent = $omset > 0 ? round(($grossProfit / $omset) * 100, 1) : 0;
        $opexRatio = $omset > 0 ? round(($expenses / $omset) * 100, 1) : 0;
        $netMarginPercent = $omset > 0 ? round(($netProfit / $omset) * 100, 1) : 0;

        // Days in month calculation for Run-rate Projection
        $daysInMonth = (int)now()->daysInMonth;
        $currentDay = max(1, (int)now()->day);
        $projectedMonthlyOmset = ($omset / $currentDay) * $daysInMonth;
        $projectedMonthlyNetProfit = ($netProfit / $currentDay) * $daysInMonth;

        // Generate Dynamic AI Advice
        $aiAdvices = [];
        if ($grossMarginPercent >= 55) {
            $aiAdvices[] = [
                'type' => 'SUCCESS',
                'title' => 'Struktur HPP Sangat Sehat (Margin ' . $grossMarginPercent . '%)',
                'message' => 'Efisiensi resep bahan baku Anda sangat baik. Pertahankan takaran resep dan harga jual saat ini.',
            ];
        } elseif ($grossMarginPercent >= 40) {
            $aiAdvices[] = [
                'type' => 'WARNING',
                'title' => 'Margin Cukup Baik (Margin ' . $grossMarginPercent . '%)',
                'message' => 'Margin masih di batas aman, namun pertimbangkan bundling dengan menu ber-margin tinggi untuk mendongkrak profit.',
            ];
        } else {
            $aiAdvices[] = [
                'type' => 'DANGER',
                'title' => 'Peringatan Margin Kritis (Margin ' . $grossMarginPercent . '%)',
                'message' => 'Biaya bahan baku menyerap lebih dari 60% omset. Periksa kembali harga beli supplier atau sesuaikan harga jual.',
            ];
        }

        if ($opexRatio > 35) {
            $aiAdvices[] = [
                'type' => 'DANGER',
                'title' => 'Beban Operasional Tinggi (' . $opexRatio . '% Omset)',
                'message' => 'Biaya operasional (sewa/listrik/gaji) menekan laba bersih Anda. Evaluasi pos pengeluaran terbesar di modul Pengeluaran.',
            ];
        } else {
            $aiAdvices[] = [
                'type' => 'SUCCESS',
                'title' => 'Rasio OPEX Terkendali (' . $opexRatio . '% Omset)',
                'message' => 'Pengeluaran operasional toko berada dalam batas ideal (<35% dari omset kotor).',
            ];
        }

        return view('livewire.reports.financial-reports', [
            'omset' => $omset,
            'cogsTotal' => $cogsTotal,
            'grossProfit' => $grossProfit,
            'platformAdjustment' => $platformAdjustment,
            'expenses' => $expenses,
            'netProfit' => $netProfit,
            'modalReturn' => $modalReturn,
            'trueProfit' => $trueProfit,
            'txCount' => $txCount,
            'expenseCount' => $expenseCount,
            'paymentMethods' => $paymentMethods,
            'grossMarginPercent' => $grossMarginPercent,
            'opexRatio' => $opexRatio,
            'netMarginPercent' => $netMarginPercent,
            'projectedMonthlyOmset' => $projectedMonthlyOmset,
            'projectedMonthlyNetProfit' => $projectedMonthlyNetProfit,
            'aiAdvices' => $aiAdvices,
        ]);
    }
}
