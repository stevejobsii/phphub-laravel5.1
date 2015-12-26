<?php namespace App;

use Zizaco\Entrust\EntrustRole;
use Cache;
use DB;

class Role extends EntrustRole
{
    public static function relationArrayWithCache()
    {
        return Cache::remember('all_assigned_roles', $minutes = 60, function()
        {
            return DB::table('role_user')->get();
        });
    }

    public static function rolesArrayWithCache()
    {
        return Cache::remember('all_roles', $minutes = 60, function()
        {
            return DB::table('roles')->get();
        });
    }
}
