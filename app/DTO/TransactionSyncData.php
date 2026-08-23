<?php

namespace App\DTO;

class TransactionSyncData
{
    public function __construct(
        public string $clientTransactionId,
        public string $receiptNumber,
        public string $paymentMethod,
        public float $totalAmount,
        public float $totalHpp,
        public float $paidAmount,
        public float $changeAmount,
        public ?float $discountTotal = null,
        public ?string $discountNote = null,
        public string $cashierName = '',
        public ?string $loyaltyMemberId = null,
        public ?string $loyaltyMemberQr = null,
        public array $items = [],
        public ?array $outlet = null,
    ) {}

    public function toArray(): array
    {
        return [
            'client_transaction_id' => $this->clientTransactionId,
            'receipt_number' => $this->receiptNumber,
            'payment_method' => $this->paymentMethod,
            'total_amount' => $this->totalAmount,
            'total_hpp' => $this->totalHpp,
            'paid_amount' => $this->paidAmount,
            'change_amount' => $this->changeAmount,
            'discount_total' => $this->discountTotal ?? 0,
            'discount_note' => $this->discountNote,
            'cashier_name' => $this->cashierName,
            'loyalty_member_id' => $this->loyaltyMemberId,
            'loyalty_member_qr' => $this->loyaltyMemberQr,
            'items' => $this->items,
            'outlet' => $this->outlet,
        ];
    }

    public static function fromArray(array $data): self
    {
        return new self(
            clientTransactionId: $data['client_transaction_id'],
            receiptNumber: $data['receipt_number'],
            paymentMethod: $data['payment_method'],
            totalAmount: (float) $data['total_amount'],
            totalHpp: (float) $data['total_hpp'],
            paidAmount: (float) $data['paid_amount'],
            changeAmount: (float) $data['change_amount'],
            discountTotal: isset($data['discount_total']) ? (float) $data['discount_total'] : null,
            discountNote: $data['discount_note'] ?? null,
            cashierName: $data['cashier_name'] ?? '',
            loyaltyMemberId: $data['loyalty_member_id'] ?? null,
            loyaltyMemberQr: $data['loyalty_member_qr'] ?? null,
            items: $data['items'] ?? [],
            outlet: $data['outlet'] ?? null,
        );
    }
}
