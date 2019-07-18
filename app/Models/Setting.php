<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Cache;

class Setting extends Model
{
    protected $fillable = [
        'setting_name', 'setting_value', 'setting_type', 'user_id', 'created_by',
    ];

    public static function saveCacheData()
    {
        Cache::flush();
        $allData1 = Cache::remember('settings', 24*60, function () {
            $allData = Setting::all();

            foreach ($allData as $adata) {
                if ($adata->setting_name == 'offday_setting') {
                    $adata->setting_value = explode(',', $adata->setting_value);
                }
            }
            return array_pluck($allData->toArray(), 'setting_value', 'setting_name');
        });

        return $allData1;
    }
}
