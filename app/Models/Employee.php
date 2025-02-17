<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Employee extends Model
{
    use HasFactory;
    protected $fillable = [
        'first_name',
        'last_name',
        'company_id',
        'email',
        'phone',
        'created_by',
        'updated_by',
    ];

    public function companyID()
    {
        return $this->belongsTo(Company::class, 'company_id');
    }
}
