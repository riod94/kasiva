<?php

namespace App\Livewire\Reports;

use App\Models\Expense;
use App\Models\Transaction;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.app')]
#[Title('Laporan Keuangan & Analisis AI - Kasiva POS')]
class FinancialReports extends Component
{
    public string $period = 'HARI_INI';
    public ?string $startDate = null;
    public ?string $endDate = null;

    public function mount(): void
    {
        abort_unless(auth()->check() && auth()->user()->hasPermission('VIEW_REPORTS'), 403, 'Akses Ditolak');
    }

    public function setPeriod(string $p): void
    {
        $this->period = $p;
    }

    public function exportPdf()
    {
        abort_unless(auth()->user()->hasPermission('VIEW_REPORTS') || auth()->user()->isOwner(), 403);
        $data = $this->getReportData();
        return app(\App\Services\ReportExportService::class)->exportPdf($data);
    }

    public function exportExcel()
    {
        abort_unless(auth()->user()->hasPermission('VIEW_REPORTS') || auth()->user()->isOwner(), 403);
        $data = $this->getReportData();
        return app(\App\Services\ReportExportService::class)->exportExcel($data);
    }

    public function getReportData(): array
    {
        $queryTx = Transaction::query()->where(function($q){ $q->whereNull('status')->orWhere('status','!=','VOIDED'); });
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

        $allTx = (clone $queryTx)->with('items')->get();
        $allExp = (clone $queryExp)->get();

        $omset = (float) $allTx->sum('total_amount');
        $cogsTotal = (float) $allTx->sum('total_hpp');
        $grossProfit = $omset - $cogsTotal;

        $platformAdjustment = 0.0;
        foreach ($allTx as $tx) {
            $isAdj = (bool) ($tx->is_backdated ?? false) || isset($tx->platform_discount) && $tx->platform_discount != 0;
            if ($isAdj) {
                $itemsSum = (float) $tx->items->sum('subtotal');
                if ($itemsSum > 0) $platformAdjustment += ((float)$tx->total_amount - $itemsSum);
            }
        }

        $expenses = (float) $allExp->sum('amount');
        $netProfit = $grossProfit - $expenses;
        $modalReturn = $cogsTotal;
        $trueProfit = $netProfit;
        $txCount = $allTx->count();
        $expenseCount = $allExp->count();

        $paymentMethods = [];
        foreach ($allTx->groupBy('payment_method') as $method => $txs) {
            $paymentMethods[] = ['method' => $method ?: 'TUNAI', 'total' => (float)$txs->sum('total_amount'), 'count' => $txs->count()];
        }
        usort($paymentMethods, fn($a,$b) => $b['total'] <=> $a['total']);

        $grossMarginPercent = $omset > 0 ? round(($grossProfit / $omset) * 100, 1) : 0;
        $opexRatio = $omset > 0 ? round(($expenses / $omset) * 100, 1) : 0;
        $netMarginPercent = $omset > 0 ? round(($netProfit / $omset) * 100, 1) : 0;

        $daysInMonth = (int) now()->daysInMonth;
        $currentDay = max(1, (int) now()->day);
        $projectedMonthlyOmset = ($omset / $currentDay) * $daysInMonth;
        $projectedMonthlyNetProfit = ($netProfit / $currentDay) * $daysInMonth;

        $aiAdvices = [];
        if ($grossMarginPercent >= 55) {
            $aiAdvices[] = ['type'=>'SUCCESS','title'=>'Struktur HPP Sangat Sehat (Margin '.$grossMarginPercent.'%)','message'=>'Efisiensi resep bahan baku sangat baik. Pertahankan takaran resep dan harga jual saat ini.'];
        } elseif ($grossMarginPercent >= 40) {
            $aiAdvices[] = ['type'=>'WARNING','title'=>'Margin Cukup Baik (Margin '.$grossMarginPercent.'%)','message'=>'Margin masih aman, pertimbangkan bundling menu ber-margin tinggi.'];
        } else {
            $aiAdvices[] = ['type'=>'DANGER','title'=>'Peringatan Margin Kritis (Margin '.$grossMarginPercent.'%)','message'=>'Biaya bahan baku >60% omset. Periksa harga supplier atau sesuaikan harga jual.'];
        }
        if ($opexRatio > 35) {
            $aiAdvices[] = ['type'=>'DANGER','title'=>'Beban Operasional Tinggi ('.$opexRatio.'%)','message'=>'OPEX menekan laba. Evaluasi sewa/listrik/gaji di modul Pengeluaran.'];
        } else {
            $aiAdvices[] = ['type'=>'SUCCESS','title'=>'Rasio OPEX Terkendali ('.$opexRatio.'%)','message'=>'OPEX ideal <35% omset.'];
        }

        $trendLabels=[]; $trendOmset=[]; $trendCogs=[];
        if ($this->period === 'HARI_INI') {
            for ($h=0;$h<24;$h++){
                $trendLabels[]=sprintf('%02d:00',$h);
                $hourTx=$allTx->filter(fn($tx)=>(int)$tx->created_at->format('H')===$h);
                $trendOmset[]=(float)$hourTx->sum('total_amount');
                $trendCogs[]=(float)$hourTx->sum('total_hpp');
            }
        } else {
            $grouped=$allTx->groupBy(fn($tx)=>$tx->created_at->format('d M'));
            foreach($grouped as $label=>$group){
                $trendLabels[]=$label;
                $trendOmset[]=(float)$group->sum('total_amount');
                $trendCogs[]=(float)$group->sum('total_hpp');
            }
            if(empty($trendLabels)){ $trendLabels=['Belum ada data']; $trendOmset=[0]; $trendCogs=[0]; }
        }

        return [
            'period' => $this->period,
            'startDate' => $this->startDate,
            'endDate' => $this->endDate,
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
            'trendLabels' => $trendLabels,
            'trendOmset' => $trendOmset,
            'trendCogs' => $trendCogs,
            'allTx' => $allTx,
            'allExp' => $allExp,
        ];
    }

    public function render()
    {
        return view('livewire.reports.financial-reports', $this->getReportData())->layout('layouts.app');
    }
}
