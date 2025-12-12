<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Invitation extends Model
{
    use HasFactory;

    protected $fillable = [
        'email',
        'company_id',
        'role',
        'token',
        'accepted_at',
    ];

    protected $dates = [
        'accepted_at',
    ];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function isAccepted()
    {
        return !is_null($this->accepted_at);
    }

    public function markAsAccepted()
    {
        $this->update(['accepted_at' => now()]);
    }

    public function inviter()
    {
        return $this->belongsTo(User::class, 'invite_by');
    }

}