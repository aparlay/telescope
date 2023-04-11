<?php

use Aparlay\Core\Constants\Roles;
use Illuminate\Database\Migrations\Migration;
use Maklad\Permission\Models\Permission;
use Maklad\Permission\Models\Role;

class AddUserNotePermission extends Migration
{
    const PERMISSION_CREATE_NOTES = 'create notes';
    const PERMISSION_DELETE_NOTES = 'delete notes';

    const PERMISSIONS_LIST = [
        self::PERMISSION_CREATE_NOTES,
        self::PERMISSION_DELETE_NOTES,
    ];

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $roleNames = [
            Roles::ADMINISTRATOR,
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

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
