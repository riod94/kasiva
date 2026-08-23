<?php

namespace App\Services\Native;

use App\Contracts\CatalogRepositoryInterface;
use App\Contracts\CartRepositoryInterface;
use App\Contracts\ExpenseRepositoryInterface;
use App\Contracts\MemberRepositoryInterface;
use App\Contracts\TransactionRepositoryInterface;
use Illuminate\Support\Str;
use PDO;

class SqliteRepository
{
    public function __construct(private readonly ?string $dbPath = null) {}

    private function pdo(): PDO { return SqliteConnection::pdo($this->dbPath); }

    private function now(): string { return now()->toIsoString(); }

    // --- CatalogRepository ---

    public function catalogList(?string $categoryId = null, ?string $search = null): array
    {
        $pdo = $this->pdo();
        $sql = 'SELECT * FROM catalog WHERE is_active=1';
        $params = [];
        if ($search) { $sql .= ' AND (name LIKE :q OR sku LIKE :q)'; $params['q'] = "%{$search}%"; }
        if ($categoryId && $categoryId !== 'ALL') { $sql .= ' AND id IN (SELECT id FROM catalog WHERE 1)'; }
        $stmt = $pdo->prepare($sql); $stmt->execute($params); return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function findById(string $id): ?array
    {
        $st = $this->pdo()->prepare('SELECT * FROM catalog WHERE id=:id'); $st->execute(['id'=>$id]); $r=$st->fetch(PDO::FETCH_ASSOC); return $r ?: null;
    }

    public function findBySku(string $sku): ?array
    {
        $st = $this->pdo()->prepare('SELECT * FROM catalog WHERE sku=:sku'); $st->execute(['sku'=>$sku]); $r=$st->fetch(PDO::FETCH_ASSOC); return $r ?: null;
    }

    public function categories(): array
    {
        // categories are not cached locally in v1; return empty and let cloud be source after sync
        return [];
    }

    public function stockSnapshot(): array
    {
        $rows = $this->pdo()->query('SELECT id, current_stock FROM catalog')->fetchAll(PDO::FETCH_ASSOC);
        $out=[]; foreach($rows as $r) $out[$r['id']]=(float)$r['current_stock']; return $out;
    }

    public function bootstrapFromCloud(string $deviceId, string $cursor = null): int
    {
        return 0;
    }

    public function upsertCatalog(array $products): void
    {
        $pdo=$this->pdo(); $st=$pdo->prepare('INSERT INTO catalog(id,name,sku,price,hpp,current_stock,is_active,updated_at) VALUES(:id,:name,:sku,:price,:hpp,:current_stock,:is_active,:updated_at) ON CONFLICT(id) DO UPDATE SET name=excluded.name, sku=excluded.sku, price=excluded.price, hpp=excluded.hpp, current_stock=excluded.current_stock, is_active=excluded.is_active, updated_at=excluded.updated_at');
        foreach($products as $p) $st->execute(['id'=>$p['id'],'name'=>$p['name'],'sku'=>$p['sku']??null,'price'=>$p['price']??0,'hpp'=>$p['hpp']??0,'current_stock'=>$p['current_stock']??0,'is_active'=>(int)($p['is_active']??1),'updated_at'=>$p['updated_at']??$this->now()]);
    }

    public function upsertMembers(array $members): void
    {
        $pdo=$this->pdo(); $st=$pdo->prepare('INSERT INTO members(id,name,phone,qr_code,status,email,stamps_count,total_visits,updated_at) VALUES(:id,:name,:phone,:qr_code,:status,:email,:stamps_count,:total_visits,:updated_at) ON CONFLICT(id) DO UPDATE SET name=excluded.name, phone=excluded.phone, qr_code=excluded.qr_code, status=excluded.status, email=excluded.email, stamps_count=excluded.stamps_count, total_visits=excluded.total_visits, updated_at=excluded.updated_at');
        foreach($members as $m) $st->execute(['id'=>$m['id'],'name'=>$m['name']??null,'phone'=>$m['phone']??null,'qr_code'=>$m['qr_code']??null,'status'=>$m['status']??'ASSIGNED','email'=>$m['email']??null,'stamps_count'=>$m['stamps_count']??0,'total_visits'=>$m['total_visits']??0,'updated_at'=>$m['updated_at']??$this->now()]);
    }

    // --- CartRepository (backed by carts.id='carts') ---

    public function getMultiCart(): array
    {
        $st=$this->pdo()->prepare('SELECT payload FROM carts WHERE id=:id'); $st->execute(['id'=>'carts']); $r=$st->fetch(PDO::FETCH_ASSOC); if(!$r) return []; $d=json_decode($r['payload'], true); return $d['items'] ?? $d ?? [];
    }

    public function getActiveCart(): array
    {
        $carts=$this->getMultiCart(); $idx=$this->getActiveCartIndex(); return $carts[$idx]['items'] ?? [];
    }

    public function setActiveCartIndex(int $index): void
    {
        $pdo=$this->pdo(); $st=$pdo->prepare('SELECT payload FROM carts WHERE id=:id'); $st->execute(['id'=>'carts']); $r=$st->fetch(PDO::FETCH_ASSOC); $d=$r?json_decode($r['payload'],true):['items'=>[]]; $d['activeCartId']=$index; $pdo->prepare('INSERT INTO carts(id,payload,updated_at) VALUES(:id,:payload,:updated_at) ON CONFLICT(id) DO UPDATE SET payload=excluded.payload, updated_at=excluded.updated_at')->execute(['id'=>'carts','payload'=>json_encode($d),'updated_at'=>$this->now()]);
    }

    public function getActiveCartIndex(): int
    {
        $st=$this->pdo()->prepare('SELECT payload FROM carts WHERE id=:id'); $st->execute(['id'=>'carts']); $r=$st->fetch(PDO::FETCH_ASSOC); if(!$r) return 0; $d=json_decode($r['payload'], true); return (int)($d['activeCartId'] ?? $d['active_cart'] ?? 0);
    }

    public function addItem(array $item): void
    {
        $carts=$this->getMultiCart(); $idx=$this->getActiveCartIndex(); if(!isset($carts[$idx])) $carts[$idx]=['name'=>'Cart '.($idx+1),'items'=>[]]; $carts[$idx]['items'][]=$item; $this->saveCarts($carts);
    }

    public function updateQuantity(string $itemKey, int $qty): void
    {
        $carts=$this->getMultiCart(); $idx=$this->getActiveCartIndex(); if(!isset($carts[$idx])) return; foreach($carts[$idx]['items'] as $k=>&$it){ if(($it['id']??null)===$itemKey){ if($qty<=0) unset($carts[$idx]['items'][$k]); else $it['qty']=$qty; break; } } $carts[$idx]['items']=array_values($carts[$idx]['items']); $this->saveCarts($carts);
    }

    public function removeItem(string $itemKey): void { $this->updateQuantity($itemKey, 0); }
    public function clearActiveCart(): void { $carts=$this->getMultiCart(); $idx=$this->getActiveCartIndex(); if(isset($carts[$idx])) $carts[$idx]['items']=[]; $this->saveCarts($carts); }
    public function saveCarts(array $carts): void { $pdo=$this->pdo(); $pdo->prepare('INSERT INTO carts(id,payload,updated_at) VALUES(:id,:payload,:updated_at) ON CONFLICT(id) DO UPDATE SET payload=excluded.payload, updated_at=excluded.updated_at')->execute(['id'=>'carts','payload'=>json_encode(['items'=>$carts,'activeCartId'=>$this->getActiveCartIndex()]),'updated_at'=>$this->now()]); }
    public function createNewCart(string $name = null): void { $carts=$this->getMultiCart(); $carts[]=['name'=>$name?:('Cart '.(count($carts)+1)),'items'=>[]]; $this->saveCarts($carts); }
    public function closeCart(int $index): void { $carts=$this->getMultiCart(); if(count($carts)<=1){ $carts[0]['items']=[]; $this->saveCarts($carts); return; } array_splice($carts,$index,1); foreach($carts as $i=>&$c) $c['name']='Cart '.($i+1); $this->saveCarts($carts); }

    // --- TransactionRepository ---

    public function createTransaction(array $data, array $items): string
    {
        $pdo=$this->pdo(); $txId=$data['id'] ?? (string)Str::uuid(); $now=$this->now();
        $pdo->beginTransaction();
        try {
            $pdo->prepare('INSERT INTO transactions_local(id,receipt_number,payment_method,total_amount,total_hpp,paid_amount,change_amount,loyalty_member_id,sync_status,payment_confirmed_manually,cashier_name,payload,created_at) VALUES(:id,:receipt_number,:payment_method,:total_amount,:total_hpp,:paid_amount,:change_amount,:loyalty_member_id,:sync_status,:payment_confirmed_manually,:cashier_name,:payload,:created_at)')->execute([
                'id'=>$txId,'receipt_number'=>$data['receipt_number']??('KSV-OFF-'.time()),'payment_method'=>$data['payment_method']??'CASH','total_amount'=>$data['total_amount']??0,'total_hpp'=>$data['total_hpp']??0,'paid_amount'=>$data['paid_amount']??($data['total_amount']??0),'change_amount'=>$data['change_amount']??0,'loyalty_member_id'=>$data['loyalty_member_id']??null,'sync_status'=>'PENDING_SYNC','payment_confirmed_manually'=>(int)($data['payment_confirmed_manually']??0),'cashier_name'=>$data['cashier_name']??'Kasir Offline','payload'=>json_encode($data),'created_at'=>$now,
            ]);
            $st=$pdo->prepare('INSERT INTO transaction_items_local(id,transaction_id,product_id,product_name,unit_price,unit_hpp,quantity,subtotal) VALUES(:id,:transaction_id,:product_id,:product_name,:unit_price,:unit_hpp,:quantity,:subtotal)');
            foreach($items as $it) $st->execute(['id'=>(string)Str::uuid(),'transaction_id'=>$txId,'product_id'=>$it['product_id']??$it['id']??null,'product_name'=>$it['product_name']??$it['name']??'','unit_price'=>$it['unit_price']??$it['price']??0,'unit_hpp'=>$it['unit_hpp']??$it['hpp']??0,'quantity'=>$it['quantity']??$it['qty']??1,'subtotal'=>$it['subtotal']??0]);
            // stock decrement + outbox in same tx
            foreach($items as $it){ $pid=$it['product_id']??$it['id']??null; $qty=(int)($it['quantity']??$it['qty']??0); if($pid) $pdo->prepare('UPDATE catalog SET current_stock = current_stock - :qty WHERE id=:id')->execute(['qty'=>$qty,'id'=>$pid]); }
            $pdo->prepare('INSERT INTO pending_operations(id,type,payload,status,created_at,attempts) VALUES(:id,:type,:payload,:status,:created_at,0)')->execute(['id'=>(string)Str::uuid(),'type'=>'transaction','payload'=>json_encode(array_merge($data, ['id'=>$txId, 'items'=>$items])),'status'=>'PENDING','created_at'=>$now]);
            $pdo->commit(); return $txId;
        } catch (\Throwable $e) { if($pdo->inTransaction()) $pdo->rollBack(); throw $e; }
    }

    public function findTransaction(string $id): ?array
    {
        $st=$this->pdo()->prepare('SELECT * FROM transactions_local WHERE id=:id'); $st->execute(['id'=>$id]); $r=$st->fetch(PDO::FETCH_ASSOC); return $r ?: null;
    }

    public function listTransactions(int $limit = 50): array
    {
        return $this->pdo()->query('SELECT * FROM transactions_local ORDER BY created_at DESC LIMIT '.(int)$limit)->fetchAll(PDO::FETCH_ASSOC);
    }

    // --- ExpenseRepository ---

    public function createExpense(array $data): string
    {
        $pdo=$this->pdo(); $id=$data['id']??(string)Str::uuid(); $now=$this->now(); $pdo->beginTransaction();
        try {
            $pdo->prepare('INSERT INTO expenses_local(id,title,amount,category,expense_date,notes,sync_status,created_at,payload) VALUES(:id,:title,:amount,:category,:expense_date,:notes,:sync_status,:created_at,:payload)')->execute([
                'id'=>$id,'title'=>$data['title'],'amount'=>$data['amount'],'category'=>$data['category']??'OPERATIONAL','expense_date'=>$data['expense_date']??date('Y-m-d'),'notes'=>$data['notes']??null,'sync_status'=>'PENDING_SYNC','created_at'=>$now,'payload'=>json_encode($data),
            ]);
            $pdo->prepare('INSERT INTO pending_operations(id,type,payload,status,created_at,attempts) VALUES(:id,:type,:payload,:status,:created_at,0)')->execute(['id'=>(string)Str::uuid(),'type'=>'expense','payload'=>json_encode(array_merge($data,['id'=>$id])),'status'=>'PENDING','created_at'=>$now]);
            $pdo->commit(); return $id;
        } catch (\Throwable $e) { if($pdo->inTransaction()) $pdo->rollBack(); throw $e; }
    }

    public function listExpenses(int $limit = 50): array
    {
        return $this->pdo()->query('SELECT * FROM expenses_local ORDER BY created_at DESC LIMIT '.(int)$limit)->fetchAll(PDO::FETCH_ASSOC);
    }

    // --- MemberRepository ---

    public function findMemberByQr(string $qr): ?array
    {
        $st=$this->pdo()->prepare('SELECT * FROM members WHERE qr_code=:qr LIMIT 1'); $st->execute(['qr'=>$qr]); $r=$st->fetch(PDO::FETCH_ASSOC); if($r) return $r;
        $suffix = preg_replace('/^(KSV-MBR-|NGEPOS-MBR-)/','', $qr);
        $st=$this->pdo()->prepare('SELECT * FROM members WHERE qr_code LIKE :q LIMIT 1'); $st->execute(['q'=>'%'.$suffix]); return $st->fetch(PDO::FETCH_ASSOC) ?: null;
    }

    public function findMemberByPhone(string $phone): ?array
    {
        $st=$this->pdo()->prepare('SELECT * FROM members WHERE phone=:p LIMIT 1'); $st->execute(['p'=>$phone]); return $st->fetch(PDO::FETCH_ASSOC) ?: null;
    }
}
