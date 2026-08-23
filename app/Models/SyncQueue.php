<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
class SyncQueue extends Model
{
    use HasUuids;
    protected $table='sync_queue';
    protected $fillable=['id','device_id','operation','entity_type','entity_id','payload','client_operation_id','status','attempts','available_at','sent_at','processed_at','last_error'];
    protected $casts=['payload'=>'array','available_at'=>'datetime','sent_at'=>'datetime','processed_at'=>'datetime'];
    public function device(){return $this->belongsTo(SyncDevice::class,'device_id');}
}
