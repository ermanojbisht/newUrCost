<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OverheadMaster extends Model
{
    use HasFactory;

    protected $table = 'overhead_masters';

    protected $fillable = [
        'code',
        'flag',
        'organization_id',
        'description',
    ];

    public function organization()
    {
        return $this->belongsTo(Organization::class);
    }
}
