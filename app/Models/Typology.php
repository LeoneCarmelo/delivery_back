<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Typology extends Model
{
    use HasFactory;

    protected $fillable = ['name'];

    public function restaurant() : BelongsToMany {
        return $this->belongsToMany(Restaurant::class);
    }
}
