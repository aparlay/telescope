<?php

use Aparlay\Core\Models\Setting;
use Illuminate\Database\Migrations\Migration;
use MongoDB\BSON\ObjectId;

return new class() extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $settings = [
            'core' => [
                'tiers' => [
                    '1' => [
                        'US', 'AU', 'CA', 'GB', 'NZ', 'SG', 'DE', 'AE', 'HK', 'NL', 'FR', 'KR', 'JP', 'SA', 'KW', 'QA',
                    ],
                    '3' => [
                        'PH', 'ID', 'MY', 'BR', 'CO', 'AR', 'PE', 'VE', 'CL', 'EC', 'BO', 'PY', 'UY', 'IN', 'VN', 'KH',
                    ],
                ],
            ],
        ];

        foreach ($settings as $key => $setting) {
            foreach ($setting as $name => $value) {
                $array = [
                    'group' => $key,
                    'created_by' => new ObjectId(),
                    'updated_by' => new ObjectId(),
                    'title' => $name,
                    'value' => $value,
                ];

                Setting::create($array);
            }
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
    }
};
