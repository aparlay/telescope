<?php

use Aparlay\Core\Constants\Roles;
use Aparlay\Core\Models\Media;
use Aparlay\Core\Models\Setting;
use Illuminate\Database\Migrations\Migration;
use Jenssegers\Mongodb\Schema\Blueprint;
use Maklad\Permission\Models\Permission;
use Maklad\Permission\Models\Role;
use MongoDB\BSON\ObjectId;

return new class() extends Migration {
    const PERMISSION_UPDATE_MEDIA_ALGORITHMS = 'update medias-algorithms';

    const PERMISSIONS_LIST = [
        self::PERMISSION_UPDATE_MEDIA_ALGORITHMS,
    ];

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $settings = [
            'app' => [
                'media' => [
                    'grpc' => config('app.media.grpc'),
                    'path' => config('app.media.path'),
                    'visit_multiplier' => 7,
                    'score_weights' => [
                        'default' => [
                            'awesomeness' => 0.3,
                            'beauty' => 0.25,
                            'skin' => -0.3,
                            'like' => 0.1,
                            'promote' => 0,
                            'comment' => 0.1,
                            'visit' => 0.1,
                            'time' => 0.25,
                        ],
                        'guest' => [
                            'awesomeness' => 0.3,
                            'beauty' => 0.3,
                            'skin' => -0.3,
                            'like' => 0.2,
                            'promote' => 0,
                            'comment' => 0.1,
                            'visit' => 0.1,
                            'time' => 0.3,
                        ],
                        'returned' => [
                            'awesomeness' => 0.3,
                            'beauty' => 0.25,
                            'skin' => 0.1,
                            'like' => 0.1,
                            'promote' => 0,
                            'comment' => 0.1,
                            'visit' => 0.1,
                            'time' => 0.2,
                        ],
                        'registered' => [
                            'awesomeness' => 0.3,
                            'beauty' => 0.25,
                            'skin' => 0.1,
                            'like' => 0.1,
                            'promote' => 0,
                            'comment' => 0.1,
                            'visit' => 0.1,
                            'time' => 0.25,
                        ],
                        'paid' => [
                            'awesomeness' => 0.3,
                            'beauty' => 0.25,
                            'skin' => 0.3,
                            'like' => 0.1,
                            'promote' => 0,
                            'comment' => 0.1,
                            'visit' => 0.1,
                            'time' => 0.25,
                        ],
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

        Media::query()->update([
            'sort_scores' => [
                'default' => 0,
                'guest' => 0,
                'returned' => 0,
                'registered' => 0,
                'paid' => 0,
            ],
        ]);

        Artisan::call('config:clear', []);
        Artisan::call('config:cache', []);

        foreach (Media::query()->where('is_fake', ['$exists' => false])->lazy() as $media) {
            /** @var Media $media */
            $media->recalculateSortScores();
            $media->drop('sort_score');
        }
        Schema::table((new Media())->getCollection(), function (Blueprint $table) {
            $table->index(['status', 'sort_scores.default', 'visibility'], null, ['background' => true]);
            $table->index(['status', 'sort_scores.guest', 'visibility'], null, ['background' => true]);
            $table->index(['status', 'sort_scores.returned', 'visibility'], null, ['background' => true]);
            $table->index(['status', 'sort_scores.registered', 'visibility'], null, ['background' => true]);
            $table->index(['status', 'sort_scores.paid', 'visibility'], null, ['background' => true]);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }

    private function assignListPermissions()
    {
        $roleNames = [
            Roles::SUPER_ADMINISTRATOR,
        ];

        $roles = Role::whereIn('name', $roleNames)
            ->where('guard_name', 'admin')
            ->get();

        foreach ($roles as $role) {
            foreach (self::PERMISSIONS_LIST as $permissionName) {
                $role->givePermissionTo(Permission::firstOrCreate([
                    'name' => $permissionName,
                    'guard_name' => 'admin',
                ]));
            }
        }
    }
};
