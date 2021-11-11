<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;

class ResetPermissions extends Command
{
    protected $signature = 'reset_permissions';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        try {
            $_permissions = DB::table('permissions')
                ->pluck('name');
            $_role = Role::findByName('super_admin');
            $_role->syncPermissions($_permissions);
        }catch (\Exception $e){
        }
    }
}
