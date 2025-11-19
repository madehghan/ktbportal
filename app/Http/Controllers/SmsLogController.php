<?php

namespace App\Http\Controllers;

use App\Models\SmsLog;
use App\Models\User;
use App\Services\IPPanelService;
use Illuminate\Http\Request;

class SmsLogController extends Controller
{
    /**
     * Display a listing of SMS logs.
     */
    public function index(Request $request)
    {
        $query = SmsLog::with('user')->latest();

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by type
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        // Search by mobile number
        if ($request->filled('search')) {
            $query->where('to_number', 'like', '%' . $request->search . '%');
        }

        $smsLogs = $query->paginate(20);
        
        // Get statistics
        $stats = [
            'total' => SmsLog::count(),
            'sent' => SmsLog::sent()->count(),
            'failed' => SmsLog::failed()->count(),
            'pending' => SmsLog::pending()->count(),
        ];

        // Get all users for the send SMS modal
        $users = User::select('id', 'name', 'mobile')->orderBy('name')->get();

        return view('sms-logs.index', compact('smsLogs', 'stats', 'users'));
    }

    /**
     * Display the specified SMS log.
     */
    public function show(SmsLog $smsLog)
    {
        $smsLog->load('user');
        return view('sms-logs.show', compact('smsLog'));
    }

    /**
     * Send a new SMS message.
     */
    public function send(Request $request, IPPanelService $smsService)
    {
        // Validation
        $validated = $request->validate([
            'recipient_type' => 'required|in:user,manual',
            'user_id' => 'required_if:recipient_type,user|nullable|exists:users,id',
            'mobile' => 'required_if:recipient_type,manual|nullable|regex:/^09[0-9]{9}$/',
            'message' => 'required|string|max:500',
        ], [
            'recipient_type.required' => 'نوع گیرنده الزامی است',
            'user_id.required_if' => 'انتخاب کاربر الزامی است',
            'user_id.exists' => 'کاربر انتخابی معتبر نیست',
            'mobile.required_if' => 'شماره موبایل الزامی است',
            'mobile.regex' => 'شماره موبایل باید با 09 شروع شود و 11 رقم باشد',
            'message.required' => 'متن پیامک الزامی است',
            'message.max' => 'متن پیامک نباید بیشتر از 500 کاراکتر باشد',
        ]);

        // Determine recipient mobile number
        if ($validated['recipient_type'] === 'user') {
            $user = User::findOrFail($validated['user_id']);
            $mobile = $user->mobile;
            $recipientName = $user->name;
        } else {
            $mobile = $validated['mobile'];
            $recipientName = $mobile;
        }

        // Send SMS
        try {
            $result = $smsService->sendSMS(
                'manual',
                $mobile,
                $validated['message']
            );

            if ($result['success']) {
                return redirect()
                    ->route('sms-logs.index')
                    ->with('success', "پیامک با موفقیت به {$recipientName} ارسال شد");
            } else {
                return redirect()
                    ->route('sms-logs.index')
                    ->with('error', "خطا در ارسال پیامک: " . ($result['error'] ?? 'خطای ناشناخته'));
            }
        } catch (\Exception $e) {
            return redirect()
                ->route('sms-logs.index')
                ->with('error', 'خطا در ارسال پیامک: ' . $e->getMessage());
        }
    }
}

