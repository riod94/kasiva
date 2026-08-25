<?php

namespace App\Livewire\Marketing;

use App\Models\Campaign;
use App\Models\Product;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.app')]
#[Title('Kampanye & Promosi - Kasiva POS')]
class CampaignManager extends Component
{
    use WithPagination;

    public bool $showModal = false;

    public ?string $campaignId = null;

    public string $name = '';

    public string $description = '';

    public string $type = 'BUNDLE';

    public bool $is_active = true;

    public int $priority = 1;

    public string $reward_type = 'PERCENT_DISCOUNT';

    public float $reward_value = 10;

    public function mount(): void
    {
        abort_unless(auth()->check() && auth()->user()->hasPermission('MANAGE_PROMOS'), 403, 'Akses Ditolak: Anda tidak memiliki izin untuk mengelola kampanye promosi.');
    }

    public function openCreateModal(): void
    {
        $this->reset(['campaignId', 'name', 'description', 'type', 'is_active', 'priority', 'reward_type', 'reward_value']);
        $this->showModal = true;
    }

    public function openEditModal(string $id): void
    {
        $c = Campaign::findOrFail($id);
        $this->campaignId = $c->id;
        $this->name = $c->name;
        $this->description = $c->description ?? '';
        $this->type = $c->type;
        $this->is_active = $c->is_active;
        $this->priority = $c->priority;
        $this->reward_type = $c->reward_type;
        $this->reward_value = (float) $c->reward_value;
        $this->showModal = true;
    }

    public array $selectedProducts = []; // product_id => qty/role

    public array $rewardItems = [];

    public function saveCampaign(): void
    {
        $this->validate([
            'name' => 'required|string|max:150',
            'reward_value' => 'required|numeric|min:0',
        ]);
        $camp = Campaign::updateOrCreate(
            ['id' => $this->campaignId],
            [
                'name' => $this->name,
                'description' => $this->description,
                'type' => $this->type,
                'is_active' => $this->is_active,
                'priority' => $this->priority,
                'reward_type' => $this->reward_type,
                'reward_value' => $this->reward_value,
            ]
        );
        // If campaign_items selected, sync (optional)
        if (! empty($this->selectedProducts)) {
            $camp->items()->delete();
            foreach ($this->selectedProducts as $pid => $qty) {
                if ($qty > 0) {
                    $camp->items()->create(['product_id' => $pid, 'quantity' => (int) $qty, 'role' => 'BUY']);
                }
            }
        }
        $this->showModal = false;
        session()->flash('message', 'Kampanye promosi berhasil disimpan.');
    }

    public function toggleActive(string $id): void
    {
        $c = Campaign::findOrFail($id);
        $c->is_active = ! $c->is_active;
        $c->save();
    }

    public function deleteCampaign(string $id): void
    {
        Campaign::findOrFail($id)->delete();
        session()->flash('message', 'Kampanye berhasil dihapus.');
    }

    public function render()
    {
        return view('livewire.marketing.campaign-manager', [
            'campaigns' => Campaign::with(['items.product', 'rewards'])->latest()->paginate(9),
            'products' => Product::orderBy('name')->get(),
        ]);
    }
}
