<?php

namespace App\Livewire\Marketing;

use App\Models\LoyaltyMember;
use App\Models\LoyaltyProgram;
use App\Models\Product;
use App\Services\LoyaltyService;
use Livewire\Component;
use Livewire\WithPagination;

class LoyaltyManager extends Component
{
    use WithPagination;

    public string $search = '';
    public bool $showMemberModal = false;
    public string $memberName = '';
    public string $memberPhone = '';

    public ?string $selectedMemberId = null;
    public $selectedMember = null;
    public bool $showStampModal = false;
    public ?string $selectedProgramId = null;

    // Program config
    public bool $showProgramModal = false;
    public ?string $programId = null;
    public string $programName = '';
    public int $targetStamps = 10;
    public float $minTransaction = 0;
    public int $expiryMonths = 12;
    public string $rewardType = 'FREE_PRODUCT';
    public float $rewardValue = 0;
    public int $rewardClaimDays = 30;
    public string $afterClaim = 'RESET';
    public bool $allowWithPromo = false;
    public bool $programIsActive = true;
    public array $excludedProductIds = [];

    protected function memberRules(): array
    {
        return [
            'memberName' => 'required|string|max:100',
            'memberPhone' => 'required|string|max:20|unique:loyalty_members,phone',
        ];
    }

    public function mount(): void
    {
        abort_unless(auth()->check() && auth()->user()->hasPermission('MANAGE_LOYALTY'), 403, 'Akses Ditolak: Anda tidak memiliki izin untuk mengelola program loyalitas.');
    }

    public function openCreateMemberModal(): void
    {
        $this->reset(['memberName', 'memberPhone']);
        $this->showMemberModal = true;
    }

    public function saveMember(): void
    {
        $this->validate($this->memberRules());

        $member = LoyaltyMember::create([
            'name' => $this->memberName,
            'phone' => $this->memberPhone,
            'stamps_count' => 0,
            'total_visits' => 0,
        ]);

        // beri 1 stempel awal untuk program aktif pertama (jika memenuhi syarat)
        $program = LoyaltyProgram::where('is_active', true)->first();
        if ($program) {
            $this->addStampToMember($member, $program);
        }

        $this->showMemberModal = false;
        $this->reset(['memberName', 'memberPhone']);
        session()->flash('message', 'Member baru terdaftar. Stempel awal diberikan sesuai program aktif.');
    }

    public function openStampModal(string $id): void
    {
        $this->selectedMemberId = $id;
        $this->selectedMember = LoyaltyMember::with('stamps')->findOrFail($id);
        $this->showStampModal = true;
    }

    private function addStampToMember(LoyaltyMember $member, LoyaltyProgram $program, ?string $transactionId = null): void
    {
        LoyaltyService::addStamp($member, $program, $transactionId);
    }

    public function addStamp(): void
    {
        if (!$this->selectedMember) return;

        $member = LoyaltyMember::findOrFail($this->selectedMember->id);
        $program = $this->selectedProgramId
            ? LoyaltyProgram::findOrFail($this->selectedProgramId)
            : LoyaltyProgram::where('is_active', true)->first();

        if (!$program) {
            session()->flash('message', 'Belum ada program loyalitas aktif.');
            $this->showStampModal = false;
            return;
        }

        // eligibility bisa diperluas (minTransaction, excluded) — untuk manual tambah tetap izinkan
        $this->addStampToMember($member, $program);
        $this->selectedMember = $member->fresh();

        // cek reward
        $progress = $this->getCustomerProgress($member, $program);
        if ($progress['isEligibleForReward']) {
            session()->flash('message', '1 stempel ditambahkan. Member sudah berhak klaim reward!');
        } else {
            session()->flash('message', '1 Stempel berhasil ditambahkan untuk ' . $member->name . '.');
        }
    }

    public function redeemReward(): void
    {
        if (!$this->selectedMember) return;
        $member = LoyaltyMember::findOrFail($this->selectedMember->id);
        $program = $this->selectedProgramId
            ? LoyaltyProgram::findOrFail($this->selectedProgramId)
            : LoyaltyProgram::where('is_active', true)->first();

        if (!$program) return;

        $progress = LoyaltyService::getCustomerProgress($member, $program);
        if (!$progress['isEligibleForReward']) {
            session()->flash('message', 'Belum cukup stempel untuk klaim.');
            return;
        }

        // cari reward AVAILABLE, jika belum ada buat satu (parity Ngepos checkAndCreateReward)
        $reward = \App\Models\CustomerReward::where('loyalty_member_id', $member->id)
            ->where('program_id', $program->id)->where('status','AVAILABLE')->where('expires_at','>', now())->first();
        if (!$reward) {
            $reward = LoyaltyService::checkAndCreateReward($member, $program);
        }
        if ($reward) {
            LoyaltyService::claimReward($reward->id, $member->id); // claimedTransaction dummy — admin claim
        } else {
            // fallback langsung reset bila reward tidak tercipta
            LoyaltyService::resetStamps($member, $program);
        }

        session()->flash('message', 'Reward berhasil diklaim untuk ' . $member->name . '!');
        $this->showStampModal = false;
    }

    /** Port Ngepos getCustomerProgress dengan expiry per-stamp */
    public function getCustomerProgress(LoyaltyMember $member, LoyaltyProgram $program): array
    {
        return LoyaltyService::getCustomerProgress($member, $program);
    }

    public function openProgramModal(?string $id=null): void {
        if($id){
            $pr = LoyaltyProgram::findOrFail($id);
            $this->programId=$pr->id; $this->programName=$pr->name; $this->targetStamps=(int)$pr->target_stamps;
            $this->minTransaction=(float)$pr->min_transaction; $this->expiryMonths=(int)$pr->expiry_months;
            $this->rewardType=$pr->reward_type ?? 'FREE_PRODUCT'; $this->rewardValue=(float)($pr->reward_value ?? 0);
            $this->rewardClaimDays=(int)($pr->reward_claim_days ?? 30); $this->afterClaim=$pr->after_claim ?? 'RESET';
            $this->allowWithPromo=(bool)($pr->allow_with_promo ?? false); $this->programIsActive=(bool)$pr->is_active;
            $this->excludedProductIds=$pr->excluded_product_ids ?? [];
        } else {
            $this->reset(['programId','programName']); $this->targetStamps=10; $this->minTransaction=0; $this->expiryMonths=12;
            $this->rewardType='FREE_PRODUCT'; $this->rewardValue=0; $this->rewardClaimDays=30; $this->afterClaim='RESET';
            $this->allowWithPromo=false; $this->programIsActive=true; $this->excludedProductIds=[];
        }
        $this->showProgramModal=true;
    }

    public function saveProgram(): void {
        $this->validate([
            'programName'=>'required|string|max:120',
            'targetStamps'=>'required|integer|min:2|max:50',
            'minTransaction'=>'required|numeric|min:0',
            'expiryMonths'=>'required|integer|min:1|max:60',
            'rewardType'=>'required|in:FREE_PRODUCT,PERCENT_DISCOUNT,FIXED_DISCOUNT',
            'rewardValue'=>'required|numeric|min:0',
            'rewardClaimDays'=>'required|integer|min:1|max:365',
            'afterClaim'=>'required|in:RESET,COMPLETE',
        ]);

        $data = [
            'name'=>$this->programName,
            'target_stamps'=>$this->targetStamps,
            'min_transaction'=>$this->minTransaction,
            'expiry_months'=>$this->expiryMonths,
            'reward_type'=>$this->rewardType,
            'reward_value'=>$this->rewardValue,
            'reward_claim_days'=>$this->rewardClaimDays,
            'after_claim'=>$this->afterClaim,
            'allow_with_promo'=>$this->allowWithPromo,
            'is_active'=>$this->programIsActive,
            'excluded_product_ids'=>$this->excludedProductIds,
        ];

        if ($this->programId) {
            LoyaltyProgram::where('id', $this->programId)->update($data);
        } else {
            LoyaltyProgram::create($data);
        }
        $this->showProgramModal=false; session()->flash('message','Program loyalitas berhasil disimpan.');
    }

    public function render()
    {
        $members = LoyaltyMember::query()
            ->when($this->search, function ($q) {
                $q->where('name', 'like', '%' . $this->search . '%')
                  ->orWhere('phone', 'like', '%' . $this->search . '%');
            })
            ->latest()
            ->paginate(10);
        $programs = LoyaltyProgram::orderByDesc('is_active')->orderBy('name')->get();
        $products = Product::orderBy('name')->limit(50)->get(['id','name']);
        return view('livewire.marketing.loyalty-manager', [
            'members' => $members,
            'programs' => $programs,
            'allProducts' => $products,
        ])->layout('layouts.app');
    }
}
