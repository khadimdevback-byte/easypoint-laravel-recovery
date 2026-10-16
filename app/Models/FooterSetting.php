<?php
// app/Models/FooterSetting.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FooterSetting extends Model
{
    use HasFactory;

    protected $fillable = [
        'email',
        'phone',
        'address',
        'copyright_text'
    ];

    // Méthode pour récupérer les paramètres du footer (un seul enregistrement)
    public static function getSettings()
    {
        return self::first() ?? self::createDefault();
    }

    // Créer les paramètres par défaut
    public static function createDefault()
    {
        return self::create([
            'email' => 'contact@easypoint.com',
            'phone' => '+228 90 00 00 00',
            'address' => 'Lomé, Togo',
            'copyright_text' => '2026 EasyPoint. Tous droits réservés.'
        ]);
    }
}
