<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Models\OrderSetting;
use App\Models\SMTPSetting;
use App\Models\GoogleSetting;
use App\Models\PaypalSetting;

class SettingController extends Controller
{

    public function __construct()
    {
        $this->middleware('canAny:cau_hinh_smtp,cau_hinh_google')->only('index');
        $this->middleware('can:cau_hinh_smtp')->only('updateSMTP');
        $this->middleware('can:cau_hinh_google')->only('updateGoogle');
    }

    public function index()
    {
        $smtpSetting = null;
        $googleSetting = null;

        if(auth()->user()->can('cau_hinh_smtp')){
            $smtpSetting = SMTPSetting::first() ?? new SMTPSetting();
        }

        if(auth()->user()->can('cau_hinh_google')){
            $googleSetting = GoogleSetting::first() ?? new GoogleSetting();
        }

        return view('admin.pages.settings.index', compact(
            'smtpSetting',
            'googleSetting'
        ));
    }



    public function updateSMTP(Request $request)
    {
        $request->validate([
            'mailer' => 'required|string',
            'host' => 'required|string',
            'port' => 'required|string',
            'username' => 'required|string',
            'password' => 'required|string',
            'encryption' => 'nullable|string',
            'from_address' => 'required|email',
            'from_name' => 'nullable|string',
            'admin_email' => 'required|email',
        ]);

        $smtpSetting = SMTPSetting::first();
        if (!$smtpSetting) {
            $smtpSetting = new SMTPSetting();
        }

        if ($smtpSetting->admin_email && 
            $smtpSetting->admin_email !== auth()->user()->email && 
            $request->admin_email !== $smtpSetting->admin_email) {
            abort(403, 'Chỉ ' . $smtpSetting->admin_email . ' mới có quyền thay đổi Admin Email');
        }

        $smtpSetting->fill($request->all());
        $smtpSetting->save();

        return redirect()->route('admin.setting.index', ['tab' => 'smtp'])
            ->with('success', 'Cài đặt SMTP đã được cập nhật thành công.');
    }

    public function updateGoogle(Request $request)
    {
        $request->validate([
            'google_client_id' => 'required|string',
            'google_client_secret' => 'required|string',
        ]);

        $googleSetting = GoogleSetting::first();
        if (!$googleSetting) {
            $googleSetting = new GoogleSetting();
        }

        $googleSetting->fill($request->all());
        $googleSetting->save();

        return redirect()->route('admin.setting.index', ['tab' => 'google'])
            ->with('success', 'Cài đặt Google đã được cập nhật thành công.');
    }


}
