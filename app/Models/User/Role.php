<?php

    namespace App\Models\User;

    use Illuminate\Support\Facades\DB;
    use Spatie\Permission\Models\Role as SpatieRole;

    class Role extends SpatieRole
    {

        public static $defaultGuardName = 'web';
        protected $perPage = 50;
        protected $table = 'roles';

        public function getCountUsersAttribute()
        {
            return DB::table('model_has_roles')
                ->where('role_id', $this->id)
                ->count();
        }

    }
