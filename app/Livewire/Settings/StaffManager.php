<?php

namespace App\Livewire\Settings;

use App\Models\Role;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Livewire\Component;
use Livewire\WithPagination;

class StaffManager extends Component
{
    use WithPagination;

    public string $search = '';

    public bool $showModal = false;

    public ?int $userId = null;

    public string $name = '';

    public string $email = '';

    public string $phone = '';

    public string $pin = '';

    public ?string $roleId = null;

    public bool $is_active = true;

    protected $rules = [
        'name' => 'required|string|max:100',
        'email' => 'required|email|max:150',
        'phone' => 'nullable|string|max:20',
        'pin' => 'nullable|string|digits:6',
        'roleId' => 'nullable|exists:roles,id',
        'is_active' => 'boolean',
    ];

    public function mount(): void
    {
        abort_unless(auth()->check() && auth()->user()->hasPermission('MANAGE_STAFF'), 403, 'Akses Ditolak: Anda tidak memiliki izin untuk mengelola staf dan akun kasir.');
    }

    public function openCreateModal(): void
    {
        $this->reset(['userId', 'name', 'email', 'phone', 'pin', 'roleId', 'is_active']);
        $cashierRole = Role::where('slug', 'cashier')->first() ?? Role::first();
        $this->roleId = $cashierRole ? $cashierRole->id : null;
        $this->is_active = true;
        $this->showModal = true;
    }

    public function openEditModal(int $id): void
    {
        $user = User::findOrFail($id);
        $this->userId = $user->id;
        $this->name = $user->name;
        $this->email = $user->email;
        $this->phone = $user->phone ?? '';
        $this->pin = ''; // Keep empty unless updating
        $this->roleId = $user->role_id;
        $this->is_active = (bool) $user->is_active;
        $this->showModal = true;
    }

    public function saveStaff(): void
    {
        $this->validate();

        if ($this->userId) {
            $user = User::findOrFail($this->userId);
            $data = [
                'name' => $this->name,
                'email' => $this->email,
                'phone' => $this->phone,
                'role_id' => $this->roleId,
                'is_active' => $this->is_active,
            ];
            if (! empty($this->pin)) {
                $data['pin'] = Hash::make($this->pin);
            }
            $user->update($data);
            session()->flash('message', 'Data staf kasir berhasil diperbarui.');
        } else {
            $plainPin = ! empty($this->pin) ? $this->pin : (string) random_int(100000, 999999);
            $newUser = User::create([
                'name' => $this->name,
                'email' => $this->email,
                'phone' => $this->phone,
                'password' => Hash::make('kasir12345'),
                'pin' => Hash::make($plainPin),
                'must_change_pin' => empty($this->pin),
                'role_id' => $this->roleId,
                'is_active' => $this->is_active,
            ]);
            session()->flash('message', 'Staf kasir baru berhasil ditambahkan.');
            if (empty($this->pin)) {
                session()->flash('staff_initial_pin', ['name' => $newUser->name, 'pin' => $plainPin]);
            }
        }

        $this->showModal = false;
        $this->reset(['userId', 'name', 'email', 'phone', 'pin', 'roleId', 'is_active']);
    }

    public function toggleStatus(int $id): void
    {
        $user = User::findOrFail($id);
        $user->update(['is_active' => ! $user->is_active]);
        session()->flash('message', 'Status akun staf berhasil diubah.');
    }

    public function deleteStaff(int $id): void
    {
        User::findOrFail($id)->delete();
        session()->flash('message', 'Akun staf berhasil dihapus.');
    }

    public function render()
    {
        $staffMembers = User::with('role')
            ->when($this->search, function ($q) {
                $q->where('name', 'like', '%'.$this->search.'%')
                    ->orWhere('email', 'like', '%'.$this->search.'%');
            })
            ->latest()
            ->paginate(10);

        $roles = Role::all();

        return view('livewire.settings.staff-manager', [
            'staffMembers' => $staffMembers,
            'roles' => $roles,
        ])->layout('layouts.app');
    }
}
