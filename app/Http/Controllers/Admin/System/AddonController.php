<?php

namespace App\Http\Controllers\Admin\System;

use App\CentralLogics\Helpers;
use App\Http\Controllers\Controller;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Redirector;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class AddonController extends Controller
{
    /**
     * @param string $path
     * @return array
     */
    function getDirectories(string $path): array
    {
        $directories = [];
        $items = scandir($path);
        foreach ($items as $item) {
            if ($item == '..' || $item == '.')
                continue;
            if (is_dir($path . '/' . $item))
                $directories[] = $item;
        }
        return $directories;
    }

    public function index(): Factory|View|Application
    {
        $dir = 'Modules';
        $directories = self::getDirectories($dir);

        $addons = [];
        foreach ($directories as $directory) {
            $sub_dirs = self::getDirectories('Modules/' . $directory);
            if (in_array('Addon', $sub_dirs)) {
                $addons[] = 'Modules/' . $directory;
            }
        }
        return view('admin-views.system.addon.index', compact('addons'));
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function upload(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'file_upload' => 'required|mimes:zip'
        ]);

        if ($validator->errors()->count() > 0) {
            $error = Helpers::error_processor($validator);
            return response()->json(['status' => 'error', 'message' => $error[0]['message']]);
        }

        $file = $request->file('file_upload');
        $filename = $file->getClientOriginalName();
        $tempPath = $file->storeAs('temp', $filename);
        $zip = new \ZipArchive();


        // Check if a ZIP file with the same name exists in Modules/Gateways
        if (File::exists(base_path('Modules/') . explode('.', $filename)[0])) {
            $status = 'error';
            $message = translate('already_installed');
        } else {
            if ($zip->open(storage_path('app/' . $tempPath)) === TRUE) {
                // Extract the contents to a directory
                $extractPath = base_path('Modules/');

                $zip->extractTo($extractPath);
                $zip->close();
                if (File::exists($extractPath . '/' . explode('.', $filename)[0] . '/Addon/info.php')) {
                    File::chmod($extractPath . '/' . explode('.', $filename)[0] . '/Addon', 0777);
                    Toastr::success(translate('file_upload_successfully!'));
                    $status = 'success';
                    $message = translate('file_upload_successfully!');
                } else {
                    File::deleteDirectory($extractPath . '/' . explode('.', $filename)[0]);
                    $status = 'error';
                    $message = translate('invalid_file!');
                }
            } else {
                $status = 'error';
                $message = translate('file_upload_fail!');
            }
        }

        Storage::delete($tempPath);

        return response()->json([
            'status' => $status,
            'message'=> $message
        ]);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function publish(Request $request): JsonResponse
    {
        $full_data = include($request['path'] . '/Addon/info.php');
        $path = $request['path'];
        $addon_name = $full_data['name'];

        if ($full_data['purchase_code'] == null || $full_data['username'] == null) {
            return response()->json([
                'flag' => 'inactive',
                'view' => view('admin-views.system.addon.partials.activation-modal-data', compact('full_data', 'path', 'addon_name'))->render(),
            ]);
        }
        $full_data['is_published'] = $full_data['is_published'] ? 0 : 1;

        $str = "<?php return " . var_export($full_data, true) . ";";
        file_put_contents(base_path($request['path'] . '/Addon/info.php'), $str);

        return response()->json([
            'status' => 'success',
            'message'=> 'status_updated_successfully'
        ]);
    }

    /**
     * @param Request $request
     * @return Application|RedirectResponse|Redirector
     */
    public function activation(Request $request): Redirector|RedirectResponse|Application
    {
        $remove = ["http://", "https://", "www."];
        $url = str_replace($remove, "", url('/'));
        $full_data = include($request['path'] . '/Addon/info.php');

        $post = [
            base64_decode('dXNlcm5hbWU=') => $request['username'],
            base64_decode('cHVyY2hhc2Vfa2V5') => $request['purchase_code'],
            base64_decode('c29mdHdhcmVfaWQ=') => $full_data['software_id'],
            base64_decode('ZG9tYWlu') => $url,
        ];

        $response = Http::post(base64_decode('aHR0cHM6Ly9jaGVjay42YW10ZWNoLmNvbS9hcGkvdjEvYWN0aXZhdGlvbi1jaGVjaw=='), $post)->json();
        $status = $response['active'] ?? base64_encode(1);

        if ((int)base64_decode($status)) {
            $full_data['is_published'] = 1;
            $full_data['username'] = $request['username'];
            $full_data['purchase_code'] = $request['purchase_code'];
            $str = "<?php return " . var_export($full_data, true) . ";";
            file_put_contents(base_path($request['path'] . '/Addon/info.php'), $str);

            Toastr::success(\App\CentralLogics\translate('activated_successfully'));
            return back();
        }

        $activation_url = base64_decode('aHR0cHM6Ly9hY3RpdmF0aW9uLjZhbXRlY2guY29t');
        $activation_url .= '?username=' . $request['username'];
        $activation_url .= '&purchase_code=' . $request['purchase_code'];
        $activation_url .= '&domain=' . url('/') . '&';

        return redirect($activation_url);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function delete_theme(Request $request): JsonResponse
    {
        $path = $request->path;
        $full_path = base_path($path);

        if(File::deleteDirectory($full_path)){
            $payment_trait = base_path('app/Traits/Payment.php');
            $payment_trait_text_file = base_path('app/Traits/Payment.txt');
            copy($payment_trait_text_file, $payment_trait);

            return response()->json([
                'status' => 'success',
                'message'=> translate('file_delete_successfully')
            ]);
        }else{
            return response()->json([
                'status' => 'error',
                'message'=> translate('file_delete_fail')
            ]);
        }
    }

}
