<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Customer;
use App\Models\FirebaseSetting;
use App\Models\FirebaseNotification;
use App\Services\FirebaseNotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class FirebaseNotificationController extends Controller
{
    public function __construct(private readonly FirebaseNotificationService $notificationService) {}

    /**
     * Display a listing of sent notifications.
     */
    public function index()
    {
        $notifications = FirebaseNotification::with('user')->orderBy('id', 'desc')->paginate(15);
        $settings = FirebaseSetting::first();
        return view('admin.firebase.notifications.index', compact('notifications', 'settings'));
    }

    /**
     * Show the form for creating/sending a new notification.
     */
    public function create()
    {
        // Load customers (role = customer)
        $customers = User::where('role', 'customer')->orderBy('name')->get();
        return view('admin.firebase.notifications.create', compact('customers'));
    }

    /**
     * Send and store the notification.
     */
    public function store(Request $request)
    {
        $request->validate([
            'title'       => 'required|string|max:255',
            'body'        => 'required|string',
            'target_type' => 'required|in:all,single',
            'user_id'     => 'required_if:target_type,single|nullable|exists:users,id',
            'image_url'   => 'nullable|url',
        ]);

        $tokens = [];
        $userIds = [];

        if ($request->target_type === 'single') {
            $user = User::find($request->user_id);
            if (!$user || empty($user->fcm_token)) {
                return back()->with('error', 'The selected customer does not have a registered FCM token (device token).');
            }
            $tokens[] = $user->fcm_token;
            $userIds[] = $user->id;
        } else {
            // Target: all customers with tokens
            $users = User::where('role', 'customer')->whereNotNull('fcm_token')->get();
            if ($users->isEmpty()) {
                return back()->with('error', 'No customers found with registered FCM tokens.');
            }
            $tokens = $users->pluck('fcm_token')->toArray();
            $userIds = $users->pluck('id')->toArray();
        }

        // Send Notification via Service
        $result = $this->notificationService->sendNotification(
            $tokens,
            $request->title,
            $request->body,
            $request->image_url
        );

        // Save records in history
        if ($request->target_type === 'single') {
            FirebaseNotification::create([
                'user_id'   => $userIds[0],
                'title'     => $request->title,
                'body'      => $request->body,
                'image_url' => $request->image_url,
                'status'    => $result['success'] ? 'success' : 'failed',
                'response'  => json_encode($result),
            ]);
        } else {
            // Bulk notification history record
            foreach ($userIds as $uid) {
                FirebaseNotification::create([
                    'user_id'   => $uid,
                    'title'     => $request->title,
                    'body'      => $request->body,
                    'image_url' => $request->image_url,
                    'status'    => $result['success'] ? 'success' : 'failed',
                    'response'  => json_encode($result),
                ]);
            }
        }

        if ($result['success']) {
            $msg = "Notification sent successfully! Success: {$result['success_count']}, Failed: {$result['failure_count']}.";
            return redirect()->route('admin.notifications.index')->with('success', $msg);
        } else {
            $err = isset($result['errors'][0]) ? $result['errors'][0] : 'Unknown error';
            return back()->withInput()->with('error', "Failed to send notification: " . $err);
        }
    }

    /**
     * Show Firebase credentials and setup settings page.
     */
    public function settings()
    {
        $setting = FirebaseSetting::first();
        return view('admin.firebase.settings', compact('setting'));
    }

    /**
     * Update Firebase configuration settings.
     */
    public function updateSettings(Request $request)
    {
        $request->validate([
            'project_id'           => 'required|string',
            'api_key'              => 'nullable|string',
            'service_account_json' => 'required|json',
            'status'               => 'nullable|boolean',
        ]);

        $setting = FirebaseSetting::first();
        $data = [
            'project_id'           => $request->project_id,
            'api_key'              => $request->api_key,
            'service_account_json' => $request->service_account_json,
            'status'               => $request->has('status') ? $request->status : 0,
        ];

        if ($setting) {
            $setting->update($data);
        } else {
            FirebaseSetting::create($data);
        }

        return redirect()->back()->with('success', 'Firebase configuration updated successfully!');
    }

    /**
     * Save customer device FCM Token via public/client API.
     */
    public function saveToken(Request $request)
    {
        $request->validate([
            'fcm_token' => 'required|string',
        ]);

        $user = auth()->user() ?? auth('sanctum')->user();
        if ($user) {
            $user->update(['fcm_token' => $request->fcm_token]);
            return response()->json([
                'success' => true,
                'message' => 'FCM Token registered successfully.',
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Unauthenticated user.',
        ], 401);
    }
}
