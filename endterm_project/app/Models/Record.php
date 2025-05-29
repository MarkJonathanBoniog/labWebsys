<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Record extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'hasOtr', 'hasForm', 'number', 'refnumber', 'status',
        'fname', 'mname', 'lname', 'sex', 'semester', 'program',
        'transferfrom', 'transferto', 'isUndergrad','yearGraduated', 'year', 'claimed', 'address',
    ];

    protected $casts = [
        'hasOtr' => 'boolean',
        'hasForm' => 'boolean',
        'isUndergrad' => 'boolean',
        'claimed' => 'datetime',
        'year' => 'integer',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
