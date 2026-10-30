<?php
use Backpack\Settings\app\Models\Setting;
use Illuminate\Database\Seeder;

class SettingsSeeder extends Seeder
{
    private $settings;

    public function __construct()
    {
        $this->settings = [
            [
                'key' => 'issue_price',
                'name' => 'Issue Price',
                'value' => '250000',
                'field' => json_encode([
                    'type' => 'number',
                    'name' => 'value'
                ]),
                'active' => 1
            ],
            [
                'key' => 'volume_price',
                'name' => 'Volume Price (Users)',
                'value' => '600000',
                'field' => json_encode([
                    'type' => 'number',
                    'name' => 'value'
                ]),
                'active' => 1
            ],
            [
                'key' => 'volume_price_multi',
                'name' => 'Volume Price (Institutions/Hospitals)',
                'value' => '800000',
                'field' => json_encode([
                    'type' => 'number',
                    'name' => 'value'
                ]),
                'active' => 1
            ],
        ];
    }

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        foreach ($this->settings as $setting) {
            $exists = Setting::where('key', $setting['key'])->count();
            if($exists || !$setting['key']) // continue if setting already exists
                continue;
            Setting::create($setting);
        }
    }
}