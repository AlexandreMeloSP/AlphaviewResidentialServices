<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Exchange extends Model
{
    use SoftDeletes;

    protected $fillable = [];

    public function serviceProponente(): BelongsTo
    {
        return $this->belongsTo(Service::class, 'service_proponente_id');
    }

    public function serviceReceptor(): BelongsTo
    {
        return $this->belongsTo(Service::class, 'service_receptor_id');
    }

    public function userProponente(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_proponente_id');
    }

    public function userReceptor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_receptor_id');
    }

    public function contract(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(Contract::class);
    }
}
