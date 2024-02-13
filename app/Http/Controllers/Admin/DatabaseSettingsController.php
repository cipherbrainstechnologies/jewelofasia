<?php

namespace App\Http\Controllers\Admin;

use App\CentralLogics\Helpers;
use App\Http\Controllers\Controller;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use File;
use Illuminate\Container\Container;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class DatabaseSettingsController extends Controller
{
    /**
     * @return Application|Factory|View
     */
    public function db_index(): View|Factory|Application
    {
        $tables = DB::connection()->getDoctrineSchemaManager()->listTableNames();
        $filter_tables = array('admins', 'admin_roles', 'branches', 'business_settings', 'email_verifications', 'failed_jobs', 'migrations', 'oauth_access_tokens', 'oauth_auth_codes', 'oauth_clients', 'oauth_personal_access_clients', 'oauth_refresh_tokens', 'password_resets', 'phone_verifications', 'soft_credentials', 'users', 'currencies', 'colors');
        $tables = array_values(array_diff($tables, $filter_tables));

        $rows = [];
        foreach ($tables as $table) {
            $count = DB::table($table)->count();
            $rows[] = $count;
        }

        return view('admin-views.business-settings.db-index', compact('tables', 'rows'));
    }

    /**
     * @param Request $request
     * @return RedirectResponse
     */
    public function clean_db(Request $request): RedirectResponse
    {
        $tables = (array)$request->tables;

        if(count($tables) == 0) {
            Toastr::error(translate('No Table Updated'));
            return back();
        }

        try {
            DB::transaction(function () use ($tables) {
                foreach ($tables as $table) {
                    DB::table($table)->delete();
                }
            });
        } catch (\Exception $exception) {
            Toastr::error(translate('Failed to update!'));
            return back();
        }

        Toastr::success(translate('Updated successfully!'));
        return back();
    }
}
