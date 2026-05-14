<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AdminSupport;
use Illuminate\Http\Request;

class AdminSupportController extends Controller
{
    public function index(Request $request)
    {
        $support = AdminSupport::first();
        if (!$support) {
            $support = AdminSupport::create([
                'messenger_url' => 'https://m.me/yourpage',
                'whatsapp_number' => '01700000000',
                'phone_number' => '01700000000',
                'is_active' => true,
            ]);
        }

        if ($request->expectsJson()) {
            return response()->json(['success' => true, 'data' => $support]);
        }

        return view('admin.support.index', ['data' => $support]);
    }

    public function update(Request $request)
    {
        $support = AdminSupport::first();
        if (!$support) {
            $support = new AdminSupport();
        }

        $support->messenger_url = $request->messenger_url;
        $support->whatsapp_number = $request->whatsapp_number;
        $support->phone_number = $request->phone_number;
        $support->is_active = $request->is_active ?? true;
        $support->save();

        if ($request->expectsJson()) {
            return response()->json(['success' => true, 'message' => 'Admin support updated successfully', 'data' => $support]);
        }

        return redirect()->back()->with('success', 'Admin support updated successfully');
    }
}
