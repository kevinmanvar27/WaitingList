<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Settings extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'application_name',
        'logo',
        'favicon',
        'app_logo',
        'app_version',
    ];

    /**
     * Get the singleton settings instance
     */
    public static function getInstance()
    {
        $settings = self::first();

        if (!$settings) {
            $settings = self::create([
                'application_name' => 'Waitinglist App',
                'app_version' => '1.0.0',
            ]);
        }

        return $settings;
    }
}
