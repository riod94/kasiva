<?php

namespace App\Livewire\Expenses;

use App\Models\Expense;
use Livewire\Component;
use Livewire\WithPagination;

class ExpenseManager extends Component
{
    use WithPagination;

    public $title = '';
    public $amount = '';
    public $category = 'OPERATIONAL';
    public $expense_date = '';
    public $notes = '';
    public $showCreateModal = false;

    public function mount()
    {
        abort_unless(auth()->check() && auth()->user()->hasPermission('MANAGE_EXPENSES'), 403, 'Akses Ditolak: Anda tidak memiliki izin untuk mengelola pengeluaran operasional.');
        $this->expense_date = now()->format('Y-m-d\TH:i');
    }

    public function openModal()
    {
        $this->reset(['title', 'amount', 'notes']);
        $this->category = 'OPERATIONAL';
        $this->expense_date = now()->format('Y-m-d\TH:i');
        $this->showCreateModal = true;
    }

    public function closeModal()
    {
        $this->showCreateModal = false;
    }

    public function saveExpense()
    {
        $this->validate([
            'title' => 'required|string|max:255',
            'amount' => 'required|numeric|min:1',
            'category' => 'required|string',
            'expense_date' => 'required',
        ]);

        Expense::create([
            'title' => $this->title,
            'amount' => $this->amount,
            'category' => $this->category,
            'expense_date' => $this->expense_date,
            'notes' => $this->notes,
        ]);

        $this->showCreateModal = false;
        $this->reset(['title', 'amount', 'notes']);
        session()->flash('message', 'Pengeluaran berhasil dicatat!');
    }

    public function render()
    {
        $expenses = Expense::latest('expense_date')->paginate(15);
        $totalThisMonth = Expense::whereMonth('expense_date', now()->month)
                                 ->whereYear('expense_date', now()->year)
                                 ->sum('amount');

        return view('livewire.expenses.expense-manager', [
            'expenses' => $expenses,
            'totalThisMonth' => $totalThisMonth,
        ])->layout('layouts.app');
    }
}
