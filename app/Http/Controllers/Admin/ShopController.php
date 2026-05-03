<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Shop;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class ShopController extends Controller
{
    // ─── INDEX ────────────────────────────────────────────────────────────────

    public function index()
    {
        $shops = Shop::with('user')->latest()->paginate(12);
        return view('admin.shops.index', compact('shops'));
    }

    // ─── CREATE ───────────────────────────────────────────────────────────────

    public function create()
    {
        return view('admin.shops.create');
    }

    // ─── STORE ────────────────────────────────────────────────────────────────

    public function store(Request $request)
    {
        $request->validate([
            'first_name'    => 'required|string|max:100',
            'last_name'     => 'nullable|string|max:100',
            'phone'         => 'required|string|max:20',
            'gender'        => 'required|in:Male,Female,Other',
            'profile_image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'email'         => 'required|email|unique:users,email',
            'password'      => ['required', 'confirmed', Password::min(6)],
            'shop_name'     => 'required|string|max:200',
            'address'       => 'nullable|string|max:500',
            'shop_logo'     => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'shop_banner'   => 'nullable|image|mimes:jpg,jpeg,png,webp|max:5120',
            'description'   => 'nullable|string',
            'latitude'      => 'nullable|numeric',
            'longitude'     => 'nullable|numeric',
        ]);

        // ── Profile image ──
        $profileImage = null;
        if ($request->hasFile('profile_image')) {
            $profileImage = $this->uploadFile($request->file('profile_image'), 'uploads/shops/profiles');
        }

        // ── Create seller user ──
        $user = User::create([
            'name'          => trim($request->first_name . ' ' . $request->last_name),
            'email'         => $request->email,
            'phone'         => $request->phone,
            'gender'        => $request->gender,
            'profile_image' => $profileImage,
            'password'      => Hash::make($request->password),
            'role'          => User::ROLE_SELLER,
        ]);

        // ── Shop logo ──
        $shopLogo = null;
        if ($request->hasFile('shop_logo')) {
            $shopLogo = $this->uploadFile($request->file('shop_logo'), 'uploads/shops/logos');
        }

        // ── Shop banner ──
        $shopBanner = null;
        if ($request->hasFile('shop_banner')) {
            $shopBanner = $this->uploadFile($request->file('shop_banner'), 'uploads/shops/banners');
        }

        // ── Create shop ──
        Shop::create([
            'user_id'     => $user->id,
            'name'        => $request->shop_name,
            'address'     => $request->address,
            'logo'        => $shopLogo,
            'banner'      => $shopBanner,
            'description' => $request->description,
            'latitude'    => $request->latitude,
            'longitude'   => $request->longitude,
            'status'      => true,
        ]);

        return redirect()->route('admin.shops.index')
            ->with('success', 'Shop created successfully!');
    }

    // ─── EDIT ─────────────────────────────────────────────────────────────────

    public function edit(Shop $shop)
    {
        $shop->load('user');
        return view('admin.shops.edit', compact('shop'));
    }

    // ─── UPDATE ───────────────────────────────────────────────────────────────

    public function update(Request $request, Shop $shop)
    {
        $shop->load('user');
        $user = $shop->user;

        $request->validate([
            'first_name'    => 'required|string|max:100',
            'last_name'     => 'nullable|string|max:100',
            'phone'         => 'required|string|max:20',
            'gender'        => 'required|in:Male,Female,Other',
            'profile_image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'email'         => 'required|email|unique:users,email,' . $user->id,
            'shop_name'     => 'required|string|max:200',
            'address'       => 'nullable|string|max:500',
            'shop_logo'     => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'shop_banner'   => 'nullable|image|mimes:jpg,jpeg,png,webp|max:5120',
            'description'   => 'nullable|string',
            'latitude'      => 'nullable|numeric',
            'longitude'     => 'nullable|numeric',
        ]);

        // ── Update profile image ──
        $profileImage = $user->profile_image;
        if ($request->hasFile('profile_image')) {
            $this->deleteFile($profileImage);
            $profileImage = $this->uploadFile($request->file('profile_image'), 'uploads/shops/profiles');
        }

        $user->update([
            'name'          => trim($request->first_name . ' ' . $request->last_name),
            'email'         => $request->email,
            'phone'         => $request->phone,
            'gender'        => $request->gender,
            'profile_image' => $profileImage,
            'role'          => User::ROLE_SELLER,
        ]);

        // ── Update shop logo ──
        $shopLogo = $shop->logo;
        if ($request->hasFile('shop_logo')) {
            $this->deleteFile($shopLogo);
            $shopLogo = $this->uploadFile($request->file('shop_logo'), 'uploads/shops/logos');
        }

        // ── Update shop banner ──
        $shopBanner = $shop->banner;
        if ($request->hasFile('shop_banner')) {
            $this->deleteFile($shopBanner);
            $shopBanner = $this->uploadFile($request->file('shop_banner'), 'uploads/shops/banners');
        }

        $shop->update([
            'name'        => $request->shop_name,
            'address'     => $request->address,
            'description' => $request->description,
            'latitude'    => $request->latitude,
            'longitude'   => $request->longitude,
            'logo'        => $shopLogo,
            'banner'      => $shopBanner,
        ]);

        return redirect()->route('admin.shops.index')
            ->with('success', 'Shop updated successfully!');
    }

    // ─── TOGGLE STATUS ────────────────────────────────────────────────────────

    public function toggleStatus(Shop $shop)
    {
        $shop->update(['status' => !$shop->status]);
        return response()->json([
            'status'  => $shop->status,
            'message' => 'Status updated.',
        ]);
    }

    // ─── DESTROY ──────────────────────────────────────────────────────────────

    public function destroy(Shop $shop)
    {
        $shop->load('user');

        $this->deleteFile($shop->logo);
        $this->deleteFile($shop->banner);

        $user = $shop->user;
        $shop->delete();

        if ($user) {
            $this->deleteFile($user->profile_image);
            $user->delete();
        }

        return redirect()->route('admin.shops.index')
            ->with('success', 'Shop deleted successfully!');
    }

    // ─── PRIVATE HELPERS ─────────────────────────────────────────────────────

    /**
     * Upload file to public/{$folder}
     * Returns relative path like: uploads/shops/logos/abc_123.jpg
     * In blade: asset($shop->logo)
     */
    private function uploadFile($file, string $folder): string
    {
        $destination = public_path($folder);

        if (!file_exists($destination)) {
            mkdir($destination, 0755, true);
        }

        $filename = uniqid() . '_' . time() . '.' . $file->getClientOriginalExtension();
        $file->move($destination, $filename);

        return $folder . '/' . $filename;
    }

    /**
     * Delete file from public/ directory
     */
    private function deleteFile(?string $path): void
    {
        if ($path && file_exists(public_path($path))) {
            unlink(public_path($path));
        }
    }
}
