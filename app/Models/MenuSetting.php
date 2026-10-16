<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MenuSetting extends Model
{
    protected $fillable = ['company_id', 'key', 'label', 'is_enabled', 'parent_id'];

    public static function isEnabled($key)
    {
        return self::where('key', $key)->value('is_enabled') == 1;
    }


    public function children()
    {
        return $this->hasMany(MenuSetting::class, 'parent_id');
    }

    public function parent()
    {
        return $this->belongsTo(MenuSetting::class, 'parent_id');
    }
}

