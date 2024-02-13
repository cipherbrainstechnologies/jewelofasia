<?php

namespace App\Http\Controllers\Admin;

use App\CentralLogics\Helpers;
use App\Http\Controllers\Controller;
use Box\Spout\Common\Exception\InvalidArgumentException;
use Box\Spout\Common\Exception\IOException;
use Box\Spout\Common\Exception\UnsupportedTypeException;
use Box\Spout\Writer\Exception\WriterNotOpenedException;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use App\Model\AdminRole;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Routing\Redirector;
use Illuminate\Support\Facades\DB;
use Rap2hpoutre\FastExcel\FastExcel;
use Symfony\Component\HttpFoundation\StreamedResponse;

class CustomRoleController extends Controller
{
    public  function __construct(
        private AdminRole $admin_role
    ){}

    /**
     * @param Request $request
     * @return Application|Factory|View
     */
    public function create(Request $request): View|Factory|Application
    {
        $query_param = [];
        $search = $request['search'];
        if($request->has('search'))
        {
            $key = explode(' ', $request['search']);
            $rl = $this->admin_role->where(function ($q) use ($key) {
                foreach ($key as $value) {
                    $q->orWhere('name', 'like', "%{$value}%");
                }
            });
            $query_param = ['search' => $request['search']];
        }else{
            $rl=$this->admin_role->whereNotIn('id',[1]);
        }
        $rl = $rl->latest()->paginate(Helpers::getPagination())->appends($query_param);

        return view('admin-views.custom-role.create',compact('rl', 'search'));
    }

    /**
     * @param Request $request
     * @return RedirectResponse
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => 'required|unique:admin_roles',
        ],[
            'name.required'=>translate('Role name is required!')
        ]);

        if($request['modules'] == null) {
            Toastr::error(translate('Select at least one module permission'));
            return back();
        }

        DB::table('admin_roles')->insert([
            'name'=>$request->name,
            'module_access'=>json_encode($request['modules']),
            'status'=>1,
            'created_at'=>now(),
            'updated_at'=>now()
        ]);

        Toastr::success(translate('Role added successfully!'));
        return back();
    }

    /**
     * @param $id
     * @return Factory|View|Application
     */
    public function edit($id): View|Factory|Application
    {
        $role=$this->admin_role->where(['id'=>$id])->first(['id','name','module_access']);
        return view('admin-views.custom-role.edit',compact('role'));
    }

    /**
     * @param Request $request
     * @param $id
     * @return Redirector|Application|RedirectResponse
     */
    public function update(Request $request, $id): Redirector|RedirectResponse|Application
    {
        $request->validate([
            'name' => 'required',
        ],[
            'name.required'=> translate('Role name is required!')
        ]);

        DB::table('admin_roles')->where(['id'=>$id])->update([
            'name'=>$request->name,
            'module_access'=>json_encode($request['modules']),
            'status'=>1,
            'updated_at'=>now()
        ]);


        Toastr::success(translate('Role updated successfully!'));
        return redirect(route('admin.custom-role.create'));
    }

    /**
     * @param Request $request
     * @return RedirectResponse
     */
    public function delete(Request $request): RedirectResponse
    {
        $role = $this->admin_role->find($request->id);
        $role->delete();
        Toastr::success(translate('Role removed!'));
        return back();
    }

    /**
     * @param Request $request
     * @return RedirectResponse
     */
    public function status(Request $request): RedirectResponse
    {
        $role = $this->admin_role->find($request->id);
        $role->status = $request->status;
        $role->save();
        Toastr::success(translate('Role status updated!'));
        return back();
    }

    /**
     * @return StreamedResponse|string
     * @throws IOException
     * @throws InvalidArgumentException
     * @throws UnsupportedTypeException
     * @throws WriterNotOpenedException
     */
    public function export(): StreamedResponse|string
    {
        $roles = $this->admin_role->whereNotIn('id',[1])->get();
        $storage = [];
        foreach($roles as $role){

            $storage[] = [
                'name' => $role['name'],
                'module_access' => $role['module_access']
            ];
        }
        return (new FastExcel($storage))->download('admin-role.xlsx');
    }
}
