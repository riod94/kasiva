<?php

namespace App\DTO;

class CatalogItem
{
    public function __construct(
        public string $id,
        public string $name,
        public string $sku,
        public float $price,
        public float $hpp,
        public float $currentStock,
        public bool $isActive,
        public ?string $categoryId = null,
        public ?string $categoryName = null,
        public ?string $imageUrl = null,
        public ?array $variants = null,
        public ?array $recipes = null,
    ) {}

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'sku' => $this->sku,
            'price' => $this->price,
            'hpp' => $this->hpp,
            'current_stock' => $this->currentStock,
            'is_active' => $this->isActive,
            'category_id' => $this->categoryId,
            'category_name' => $this->categoryName,
            'image_url' => $this->imageUrl,
            'variants' => $this->variants,
            'recipes' => $this->recipes,
        ];
    }

    public static function fromArray(array $data): self
    {
        return new self(
            id: $data['id'],
            name: $data['name'],
            sku: $data['sku'],
            price: (float) $data['price'],
            hpp: (float) $data['hpp'],
            currentStock: (float) ($data['current_stock'] ?? $data['stock'] ?? 0),
            isActive: (bool) ($data['is_active'] ?? true),
            categoryId: $data['category_id'] ?? null,
            categoryName: $data['category_name'] ?? $data['category'] ?? null,
            imageUrl: $data['image_url'] ?? null,
            variants: $data['variants'] ?? null,
            recipes: $data['recipes'] ?? null,
        );
    }

    public static function fromModel(\App\Models\Product $product): self
    {
        return new self(
            id: $product->id,
            name: $product->name,
            sku: $product->sku,
            price: (float) $product->price,
            hpp: (float) $product->hpp,
            currentStock: (float) $product->current_stock,
            isActive: (bool) $product->is_active,
            categoryId: $product->category_id,
            categoryName: $product->category?->name,
            imageUrl: $product->image_url,
            variants: $product->variants?->map(fn ($v) => [
                'id' => $v->id,
                'name' => $v->name,
                'selection_type' => $v->selection_type,
                'is_required' => (bool) $v->is_required,
                'options' => $v->options?->map(fn ($o) => [
                    'id' => $o->id,
                    'name' => $o->name,
                    'price_modifier' => (float) $o->price_modifier,
                    'cogs_modifier' => (float) $o->cogs_modifier,
                ])->toArray(),
            ])->toArray(),
            recipes: $product->recipes?->map(fn ($r) => [
                'material_id' => $r->material_id,
                'quantity' => (float) ($r->pivot?->quantity ?? $r->quantity ?? 0),
            ])->toArray(),
        );
    }
}
