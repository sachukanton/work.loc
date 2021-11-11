<?php

    namespace App\Observers;

    use App\Models\User\Permission;
    use App\Models\User\Role;

    class RoleObservers
    {

        public function created(Role $_item)
        {
        }

        public function saved(Role $_item)
        {
            if ($permissions = request()->input('permissions')) {
                $_permissions = Permission::whereIn('name', array_keys($permissions))
                    ->get();
                $_item->syncPermissions($_permissions);
            }
        }

        public function deleting(Role $_item)
        {
        }

    }