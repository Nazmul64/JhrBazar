<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Verificatiootpsettings;
use Illuminate\Http\Request;

class VerificatiootpsettingsController extends Controller
{
    /**
     * Display the settings page.
     */
    public function index()
    {
        $setting = Verificatiootpsettings::firstOrCreate(
            ['id' => 1],
            [
                'customer_registration_otp_verify'      => false,
                'must_verify_account_on_order_placement' => true,
                'register_otp_send_method'              => 'email',
                'forget_password_otp_send_method'       => 'email',
                'registration_phone_required'           => true,
                'min_phone_length'                      => 9,
                'max_phone_length'                      => 14,
            ]
        );

        return view('admin.verificationotpsettings.index', compact('setting'));
    }

    /**
     * Store / Update settings (single-row settings pattern).
     */
    public function store(Request $request)
    {
        $request->validate([
            'register_otp_send_method'         => 'required|in:phone,email',
            'forget_password_otp_send_method'  => 'required|in:phone,email',
            'min_phone_length'                 => 'required|integer|min:1|max:20',
            'max_phone_length'                 => 'required|integer|min:1|max:20|gte:min_phone_length',
        ]);

        $setting = Verificatiootpsettings::firstOrNew(['id' => 1]);

        $setting->customer_registration_otp_verify      = $request->has('customer_registration_otp_verify');
        $setting->must_verify_account_on_order_placement = $request->has('must_verify_account_on_order_placement');
        $setting->register_otp_send_method              = $request->register_otp_send_method;
        $setting->forget_password_otp_send_method       = $request->forget_password_otp_send_method;
        $setting->registration_phone_required           = $request->has('registration_phone_required');
        $setting->min_phone_length                      = $request->min_phone_length;
        $setting->max_phone_length                      = $request->max_phone_length;
        $setting->save();

        return redirect()->route('admin.verificationotpsettings.index')
            ->with('success', 'Verification OTP settings updated successfully.');
    }

    /**
     * Toggle any boolean field via AJAX.
     */
    public function toggleStatus(Request $request, $id)
    {
        $request->validate([
            'field' => 'required|in:customer_registration_otp_verify,must_verify_account_on_order_placement,registration_phone_required',
        ]);

        $setting = Verificatiootpsettings::firstOrCreate(['id' => 1]);
        $field   = $request->field;

        $setting->$field = !$setting->$field;
        $setting->save();

        return response()->json([
            'success' => true,
            'status'  => $setting->$field,
            'message' => 'Status updated successfully.',
        ]);
    }

    // Unused resource methods kept for route compatibility
    public function create() {}
    public function show(string $id) {}
    public function edit(string $id) {}
    public function update(Request $request, string $id) {}
    public function destroy(string $id) {}
}
