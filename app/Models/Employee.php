<?php

namespace App\Models;

use App\Models\Visitor;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Employee extends Model
{
    use HasFactory;

    protected $connection = "mysql";

    protected $table = "employee";

    protected $primaryKey = "name";

    protected $keyType = 'string';

    protected $fillable = [
        'name',
        'email',
        'department',
        'phone_number',
        'employee_code'
    ];

    public $timestamps = false;

    public function employee(): HasMany
    {
        return $this->hasMany(Visitor::class, 'visitor_host', 'name');
    }
}
