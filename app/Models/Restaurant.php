<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Str;

class Restaurant extends Model
{
    use HasFactory;

    protected $fillable  = ['user_id', 'name', 'slug', 'address','piva', 'photo'];

    public static function generateSlug($name){
        return Str::slug($name, '-');
    }

    public function user() : BelongsTo {
            return $this->belongsTo(User::class);
    }

    public function dishes() : HasMany {
        return $this->hasMany(Dish::class);
    }

    public function typologies() : BelongsToMany {
        return $this->belongsToMany(Typology::class);
    }
}