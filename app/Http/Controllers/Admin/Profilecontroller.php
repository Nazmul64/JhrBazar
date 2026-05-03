<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Shop;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class ProfileController extends Controller
{
    /**
     * Files go directly to public/uploads/profiledetails/
     * No storage symlink needed.
     */
    private const UPLOAD_BASE = 'uploads/profiledetails';

    // ─────────────────────────────────────────────────────────────────────────
    //  INDEX
    // ─────────────────────────────────────────────────────────────────────────

    /**
     * Route: GET /admin/profile
     */
    public function index()
    {
        /** @var User $user */
        $user = auth()->user();
        $shop = DB::table('shops')->where('user_id', $user->id)->first();

        $totalProducts = 0;
        $totalOrders   = 0;
        $totalReviews  = 0;

        if ($shop) {
            $totalProducts = DB::table('products')->where('shop_id', $shop->id)->count();
            $totalOrders   = DB::table('orders')->where('shop_id', $shop->id)->count();
            $totalReviews  = DB::table('reviews')->where('shop_id', $shop->id)->count();

            // Attach avg_rating to shop object
            $avgRating = DB::table('reviews')
                ->where('shop_id', $shop->id)
                ->avg('rating');
            $shop->avg_rating = $avgRating ?? 0;
        }

        return view('admin.profile.index', compact(
            'user',
            'shop',
            'totalProducts',
            'totalOrders',
            'totalReviews'
        ));
    }

    // ─────────────────────────────────────────────────────────────────────────
    //  CREATE / STORE
    // ─────────────────────────────────────────────────────────────────────────

    /**
     * Route: GET /admin/profile/create
     */
    public function create()
    {
        $bdDivisions = $this->bangladeshDivisions();

        return view('admin.profile.create', compact('bdDivisions'));
    }

    /**
     * Route: POST /admin/profile/store
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            // User
            'first_name'    => 'required|string|max:100',
            'last_name'     => 'nullable|string|max:100',
            'phone'         => 'required|string|max:20',
            'gender'        => 'nullable|in:Male,Female,Other',
            'email'         => 'required|email|unique:users,email',
            'profile_image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',

            // Shop
            'shop_name'            => 'required|string|max:150',
            'address'              => 'nullable|string|max:255',
            'division'             => 'nullable|string|max:100',
            'district'             => 'nullable|string|max:100',
            'minimum_order_amount' => 'nullable|numeric|min:0',
            'opening_time'         => 'nullable|date_format:H:i',
            'closing_time'         => 'nullable|date_format:H:i',
            'estimated_delivery'   => 'nullable|integer|min:1',
            'order_id_prefix'      => 'required|string|max:20',
            'description'          => 'nullable|string',
            'logo'                 => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'banner'               => 'nullable|image|mimes:jpg,jpeg,png,webp|max:4096',
            'latitude'             => 'nullable|numeric',
            'longitude'            => 'nullable|numeric',
        ]);

        DB::transaction(function () use ($request, $validated) {

            $userId = DB::table('users')->insertGetId([
                'name'          => trim($validated['first_name'] . ' ' . ($validated['last_name'] ?? '')),
                'email'         => $validated['email'],
                'phone'         => $validated['phone'],
                'gender'        => $validated['gender'] ?? null,
                'role'          => 'admin',
                'password'      => Hash::make('password'),
                'profile_image' => $this->uploadFile($request, 'profile_image', 'profiles'),
                'created_at'    => now(),
                'updated_at'    => now(),
            ]);

            DB::table('shops')->insert([
                'user_id'              => $userId,
                'name'                 => $validated['shop_name'],
                'address'              => $validated['address']              ?? null,
                'division'             => $validated['division']             ?? null,
                'district'             => $validated['district']             ?? null,
                'minimum_order_amount' => $validated['minimum_order_amount'] ?? 0,
                'opening_time'         => $validated['opening_time']         ?? null,
                'closing_time'         => $validated['closing_time']         ?? null,
                'estimated_delivery'   => $validated['estimated_delivery']   ?? 3,
                'order_id_prefix'      => $validated['order_id_prefix'],
                'description'          => $validated['description']          ?? null,
                'logo'                 => $this->uploadFile($request, 'logo',   'logos'),
                'banner'               => $this->uploadFile($request, 'banner', 'banners'),
                'latitude'             => $validated['latitude']             ?? null,
                'longitude'            => $validated['longitude']            ?? null,
                'created_at'           => now(),
                'updated_at'           => now(),
            ]);
        });

        return redirect()->route('admin.profile.index')
                         ->with('success', 'Profile & Shop created successfully.');
    }

    // ─────────────────────────────────────────────────────────────────────────
    //  EDIT / UPDATE
    // ─────────────────────────────────────────────────────────────────────────

    /**
     * Route: GET /admin/profile/edit
     */
    public function edit()
    {
        /** @var User $user */
        $user        = auth()->user();
        $shop        = DB::table('shops')->where('user_id', $user->id)->first();
        $bdDivisions = $this->bangladeshDivisions();

        return view('admin.profile.edit', compact('user', 'shop', 'bdDivisions'));
    }

    /**
     * Route: POST /admin/profile/update
     */
    public function update(Request $request)
    {
        /** @var User $user */
        $user = auth()->user();
        $shop = DB::table('shops')->where('user_id', $user->id)->first();

        $validated = $request->validate([
            // User
            'first_name'    => 'required|string|max:100',
            'last_name'     => 'nullable|string|max:100',
            'phone'         => 'required|string|max:20',
            'gender'        => 'nullable|in:Male,Female,Other',
            'email'         => ['required', 'email', Rule::unique('users', 'email')->ignore($user->id)],
            'profile_image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',

            // Shop
            'shop_name'            => 'required|string|max:150',
            'address'              => 'nullable|string|max:255',
            'division'             => 'nullable|string|max:100',
            'district'             => 'nullable|string|max:100',
            'minimum_order_amount' => 'nullable|numeric|min:0',
            'opening_time'         => 'nullable|date_format:H:i',
            'closing_time'         => 'nullable|date_format:H:i',
            'estimated_delivery'   => 'nullable|integer|min:1',
            'order_id_prefix'      => 'required|string|max:20',
            'description'          => 'nullable|string',
            'logo'                 => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'banner'               => 'nullable|image|mimes:jpg,jpeg,png,webp|max:4096',
            'latitude'             => 'nullable|numeric',
            'longitude'            => 'nullable|numeric',
        ]);

        DB::transaction(function () use ($request, $validated, $user, $shop) {

            // ── Update User ──────────────────────────────────────────────────
            $userUpdate = [
                'name'       => trim($validated['first_name'] . ' ' . ($validated['last_name'] ?? '')),
                'email'      => $validated['email'],
                'phone'      => $validated['phone'],
                'gender'     => $validated['gender'] ?? null,
                'updated_at' => now(),
            ];

            if ($request->hasFile('profile_image')) {
                $this->deleteFile($user->profile_image ?? null);
                $userUpdate['profile_image'] = $this->uploadFile($request, 'profile_image', 'profiles');
            }

            DB::table('users')->where('id', $user->id)->update($userUpdate);

            // ── Update / Create Shop ─────────────────────────────────────────
            $shopData = [
                'name'                 => $validated['shop_name'],
                'address'              => $validated['address']              ?? null,
                'division'             => $validated['division']             ?? null,
                'district'             => $validated['district']             ?? null,
                'minimum_order_amount' => $validated['minimum_order_amount'] ?? 0,
                'opening_time'         => $validated['opening_time']         ?? null,
                'closing_time'         => $validated['closing_time']         ?? null,
                'estimated_delivery'   => $validated['estimated_delivery']   ?? 3,
                'order_id_prefix'      => $validated['order_id_prefix'],
                'description'          => $validated['description']          ?? null,
                'latitude'             => $validated['latitude']             ?? null,
                'longitude'            => $validated['longitude']            ?? null,
                'updated_at'           => now(),
            ];

            if ($request->hasFile('logo')) {
                $this->deleteFile($shop->logo ?? null);
                $shopData['logo'] = $this->uploadFile($request, 'logo', 'logos');
            }

            if ($request->hasFile('banner')) {
                $this->deleteFile($shop->banner ?? null);
                $shopData['banner'] = $this->uploadFile($request, 'banner', 'banners');
            }

            if ($shop) {
                DB::table('shops')->where('id', $shop->id)->update($shopData);
            } else {
                $shopData['user_id']    = $user->id;
                $shopData['created_at'] = now();
                DB::table('shops')->insert($shopData);
            }
        });

        return redirect()->route('admin.profile.index')
                         ->with('success', 'Profile updated successfully.');
    }

    // ─────────────────────────────────────────────────────────────────────────
    //  PRIVATE HELPERS
    // ─────────────────────────────────────────────────────────────────────────

    /**
     * Upload a file into public/uploads/profiledetails/{subfolder}/
     * Returns the relative path stored in DB.
     */
    private function uploadFile(Request $request, string $inputName, string $subfolder): ?string
    {
        if (! $request->hasFile($inputName)) {
            return null;
        }

        $file      = $request->file($inputName);
        $extension = $file->getClientOriginalExtension();
        $filename  = uniqid('img_', true) . '.' . $extension;

        $dir = public_path(self::UPLOAD_BASE . '/' . $subfolder);

        if (! is_dir($dir)) {
            mkdir($dir, 0755, true);
        }

        $file->move($dir, $filename);

        return self::UPLOAD_BASE . '/' . $subfolder . '/' . $filename;
    }

    /**
     * Delete a file from public/ using its relative path.
     */
    private function deleteFile(?string $relativePath): void
    {
        if (! $relativePath) {
            return;
        }

        $fullPath = public_path($relativePath);

        if (file_exists($fullPath)) {
            @unlink($fullPath);
        }
    }

    /**
     * Bangladesh — 8 Divisions with their 64 Districts.
     *
     * @return array<string, list<string>>
     */
    private function bangladeshDivisions(): array
    {
        return [
            'Barisal' => [
                'Barguna', 'Barisal', 'Bhola', 'Jhalokati', 'Patuakhali', 'Pirojpur',
            ],
            'Chattogram' => [
                'Bandarban', 'Brahmanbaria', 'Chandpur', 'Chattogram', "Cox's Bazar",
                'Cumilla', 'Feni', 'Khagrachhari', 'Lakshmipur', 'Noakhali', 'Rangamati',
            ],
            'Dhaka' => [
                'Dhaka', 'Faridpur', 'Gazipur', 'Gopalganj', 'Kishoreganj', 'Madaripur',
                'Manikganj', 'Munshiganj', 'Narayanganj', 'Narsingdi', 'Rajbari',
                'Shariatpur', 'Tangail',
            ],
            'Khulna' => [
                'Bagerhat', 'Chuadanga', 'Jessore', 'Jhenaidah', 'Khulna', 'Kushtia',
                'Magura', 'Meherpur', 'Narail', 'Satkhira',
            ],
            'Mymensingh' => [
                'Jamalpur', 'Mymensingh', 'Netrokona', 'Sherpur',
            ],
            'Rajshahi' => [
                'Bogura', 'Chapai Nawabganj', 'Joypurhat', 'Naogaon', 'Natore',
                'Pabna', 'Rajshahi', 'Sirajganj',
            ],
            'Rangpur' => [
                'Dinajpur', 'Gaibandha', 'Kurigram', 'Lalmonirhat', 'Nilphamari',
                'Panchagarh', 'Rangpur', 'Thakurgaon',
            ],
            'Sylhet' => [
                'Habiganj', 'Moulvibazar', 'Sunamganj', 'Sylhet',
            ],
        ];
    }
}
