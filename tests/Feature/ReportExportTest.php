<?php

use App\Livewire\Reports\FinancialReports;
use App\Models\Transaction;
use App\Models\Expense;
use App\Models\User;
use Livewire\Livewire;

uses(Tests\TestCase::class, Illuminate\Foundation\Testing\RefreshDatabase::class);

test('financial reports exclude VOIDED dan provide trend + paymentMethods', function () {
    $user = User::factory()->create();
    Transaction::create(['receipt_number'=>'KSV-EXP-001','payment_method'=>'CASH','total_amount'=>20000,'total_hpp'=>8000,'paid_amount'=>20000,'change_amount'=>0,'cashier_name'=>'Rizki','sync_status'=>'SYNCED','status'=>'COMPLETED','created_at'=>now(),'updated_at'=>now()]);
    Transaction::create(['receipt_number'=>'KSV-EXP-002','payment_method'=>'QRIS','total_amount'=>30000,'total_hpp'=>12000,'paid_amount'=>30000,'change_amount'=>0,'cashier_name'=>'Rizki','sync_status'=>'SYNCED','status'=>'VOIDED','created_at'=>now(),'updated_at'=>now()]);
    $this->actingAs($user);
    $data = Livewire::test(FinancialReports::class)->set('period','SEMUA')->instance()->getReportData();
    expect($data['omset'])->toBe(20000.0);
    expect($data['trendLabels'])->not->toBeEmpty();
    expect($data['paymentMethods'])->toHaveCount(1);
});

test('report export service menghasilkan XLSX valid', function () {
    $user = User::factory()->create();
    Transaction::create(['receipt_number'=>'KSV-EXP-003','payment_method'=>'CASH','total_amount'=>15000,'total_hpp'=>5000,'paid_amount'=>15000,'change_amount'=>0,'cashier_name'=>'Rizki','sync_status'=>'SYNCED','status'=>'COMPLETED']);
    $data = ['period'=>'SEMUA','omset'=>15000,'cogsTotal'=>5000,'grossProfit'=>10000,'expenses'=>0,'netProfit'=>10000,'txCount'=>1,'expenseCount'=>0,'allTx'=>Transaction::all(),'allExp'=>Expense::all(),'paymentMethods'=>[]];
    $svc = new \App\Services\ReportExportService();
    $resp = $svc->exportExcel($data);
    expect($resp->getStatusCode())->toBe(200);
    expect($resp->headers->get('Content-Type'))->toContain('application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    expect($resp->headers->get('Content-Disposition'))->toContain('.xlsx');
});

test('report export service menghasilkan PDF valid', function () {
    Transaction::create(['receipt_number'=>'KSV-EXP-PDF','payment_method'=>'CASH','total_amount'=>10000,'total_hpp'=>4000,'paid_amount'=>10000,'change_amount'=>0,'cashier_name'=>'Rizki','sync_status'=>'SYNCED','status'=>'COMPLETED']);
    $data = ['period'=>'SEMUA','omset'=>10000,'cogsTotal'=>4000,'grossProfit'=>6000,'expenses'=>0,'netProfit'=>6000,'txCount'=>1,'expenseCount'=>0,'allTx'=>Transaction::all(),'allExp'=>Expense::all(),'paymentMethods'=>[],'startDate'=>null,'endDate'=>null,'netMarginPercent'=>60,'grossMarginPercent'=>60,'opexRatio'=>0];
    $svc = new \App\Services\ReportExportService();
    $resp = $svc->exportPdf($data);
    expect($resp->getStatusCode())->toBe(200);
    expect($resp->headers->get('Content-Type'))->toContain('application/pdf');
});
