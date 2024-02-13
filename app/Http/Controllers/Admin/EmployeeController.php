<?php

namespace App\Http\Controllers\Admin;

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
use App\Model\Admin;
use App\Model\AdminRole;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Support\Facades\DB;
use App\CentralLogics\Helpers;
use Illuminate\Support\Facades\Storage;
use Rap2hpoutre\FastExcel\FastExcel;
use Symfony\Component\HttpFoundation\StreamedResponse;

class EmployeeController extends Controller
{
    public function __construct(
        private Admin $admin,
        private AdminRole $admin_role
    ){}

    /**
     * @return Factory|View|Application
     */
    public function add_new(): View|Factory|Application
    {
        $rls = $this->admin_role->whereNotIn('id', [1])->get();
        return view('admin-views.employee.add-new', compact('rls'));
    }

    /**
     * @param Request $request
     * @return RedirectResponse
     */
    public function store(Request $request): RedirectResponse
    {
        //return $request;
        $request->validate([
            'name' => 'required',
            'role_id' => 'required',
            'image' => 'required',
            'email' => 'required|email|unique:admins',
            'phone'=>'required',
            'password' => 'required|min:8',
            'password_confirmation' => 'required_with:password|same:password|min:8'

        ], [
            'name.required' => translate('Role name is required!'),
            'role_id.required' => translate('Role ID is required!'),
            'role_name.required' => translate('Role id is Required'),
            'email.required' => translate('Email id is Required'),
            'image.required' => translate('Image is Required'),

        ]);

        if ($request->role_id == 1) {
            Toastr::warning(translate('Access Denied!'));
            return back();
        }

        if ($request->has('image')) {
            $image_name = Helpers::upload('admin/', 'png', $request->file('image'));
        } else {
            $image_name = 'def.png';
        }

        $id_img_names = [];
        if (!empty($request->file('identity_image'))) {
            foreach ($request->identity_image as $img) {
                $identity_image = Helpers::upload('admin/', 'png', $img);
                $id_img_names[] = $identity_image;
            }
            $identity_image = json_encode($id_img_names);
        } else {
            $identity_image = json_encode([]);
        }

        DB::table('admins')->insert([
            'f_name' => $request->name,
            'phone' => $request->phone,
            'email' => $request->email,
            'identity_number' => $request->identity_number,
            'identity_type' => $request->identity_type,
            'identity_image' => $identity_image,
            'admin_role_id' => $request->role_id,
            'password' => bcrypt($request->password),
            'status'=>1,
            'image' => $image_name,
            'created_at' => now(),
            'updated_at' => now(),
        ]);


        Toastr::success(translate('Employee added successfully!'));
        return redirect()->route('admin.employee.list');
    }

    /**
     * @param Request $request
     * @return Factory|View|Application
     */
    function list(Request $request): View|Factory|Application
    {
        $search = $request['search'];
        $key = explode(' ', $request['search']);

        $query = $this->admin->with(['role'])
            ->when($search != null, function ($query) use ($key) {
                $query->whereNotIn('id', [1])->where(function ($query) use ($key) {
                    foreach ($key as $value) {
                        $query->where('f_name', 'like', "%{$value}%")
                            ->orWhere('phone', 'like', "%{$value}%")
                            ->orWhere('email', 'like', "%{$value}%");
                    }
                });
            }, function ($query) {
                $query->whereNotIn('id', [1]);
            });

        $sql = $query->toSql();
        $em = $query->paginate(Helpers::getPagination());

        return view('admin-views.employee.list', compact('em','search'));
    }

    /**
     * @param $id
     * @return Factory|View|Application
     */
    public function edit($id): View|Factory|Application
    {
        $e = $this->admin->where(['id' => $id])->first();
        $rls = $this->admin_role->whereNotIn('id', [1])->get();
        return view('admin-views.employee.edit', compact('rls', 'e'));
    }

    /**
     * @param Request $request
     * @param $id
     * @return RedirectResponse
     */
    public function update(Request $request, $id): RedirectResponse
    {
        $request->validate([
            'name' => 'required',
            'role_id' => 'required',
            'email' => 'required|email|unique:admins,email,'.$id,
            'password_confirmation' => 'required_with:password|same:password'
        ], [
            'name.required' => translate('name is required!'),
        ]);

        if ($request->role_id == 1) {
            Toastr::warning(translate('Access Denied!'));
            return back();
        }

        $e = $this->admin->find($id);
        if ($request['password'] == null) {
            $pass = $e['password'];
        } else {
            if (strlen($request['password']) < 7) {
                Toastr::warning(translate('Password length must be 8 character.'));
                return back();
            }
            $pass = bcrypt($request['password']);
        }


        if ($request->has('image')) {
            $e['image'] = Helpers::update('admin/', $e['image'], 'png', $request->file('image'));
        }

        if ($request->has('identity_image')){
            foreach (json_decode($e['identity_image'], true) as $img) {
                if (Storage::disk('public')->exists('admin/' . $img)) {
                    Storage::disk('public')->delete('admin/' . $img);
                }
            }
            $img_keeper = [];
            foreach ($request->identity_image as $img) {
                $identity_image = Helpers::upload('admin/', 'png', $img);
                $img_keeper[] = $identity_image;
            }
            $identity_image = json_encode($img_keeper);
        } else {
            $identity_image = $e['identity_image'];
        }

        DB::table('admins')->where(['id' => $id])->update([
            'f_name' => $request->name,
            'phone' => $request->phone,
            'email' => $request->email,
            'identity_number' => $request->identity_number,
            'identity_type' => $request->identity_type,
            'identity_image' => $identity_image,
            'admin_role_id' => $request->role_id,
            'password' => $pass,
            'image' => $e['image'],
            'updated_at' => now(),
        ]);

        Toastr::success(translate('Employee updated successfully!'));
        return redirect()->route('admin.employee.list');
    }

    /**
     * @param Request $request
     * @return RedirectResponse
     */
    public function status(Request $request): RedirectResponse
    {
        $employee = $this->admin->find($request->id);
        $employee->status = $request->status;
        $employee->save();

        Toastr::success(translate('Employee status updated!'));
        return back();
    }

    /**
     * @param Request $request
     * @return RedirectResponse
     */
    public function delete(Request $request): RedirectResponse
    {
        $employee = $this->admin->where('id', $request->id)->whereNotIn('id', [1])->first();
        $employee->delete();
        Toastr::success(translate('Employee removed!'));
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
        $employees = $this->admin->whereNotIn('id', [1])->get();
        $storage = [];
        foreach($employees as $employee){
            $role = $employee->role ? $employee->role->name : '';
            $storage[] = [
                'name' => $employee['f_name'],
                'phone' => $employee['phone'],
                'email' => $employee['email'],
                'admin_role' => $role,
            ];
        }
        return (new FastExcel($storage))->download('employee.xlsx');
    }
}
