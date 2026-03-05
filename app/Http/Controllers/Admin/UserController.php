<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\Role;
use App\Models\User;
use App\Models\UserRole;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class UserController extends Controller
{
    /**
     * Kullanıcı listesi (tenant bazlı)
     */
    public function index(Request $request)
    {
        $tenant = $request->user()->tenant;
        $tenantId = $tenant?->id ?? $request->user()->tenant_id;

        // Super admin: tüm kullanıcıları göster
        $query = User::with(['primaryRole', 'branch'])->orderBy('name');
        if ($tenantId) {
            $query->where('tenant_id', $tenantId);
        }

        $users = $query->paginate(20);

        return view('admin.users.index', compact('users'));
    }

    /**
     * Yeni kullanıcı ekleme formu
     */
    public function create(Request $request)
    {
        $tenant = $request->user()->tenant;
        $tenantId = $tenant?->id ?? $request->user()->tenant_id;

        $roles = Role::orderBy('name')->get();
        $branchQuery = Branch::where('is_active', true);
        if ($tenantId) {
            $branchQuery->where('tenant_id', $tenantId);
        }
        $branches = $branchQuery->get();

        return view('admin.users.create', compact('roles', 'branches'));
    }

    /**
     * Yeni kullanıcı kaydet
     */
    public function store(Request $request)
    {
        $tenant = $request->user()->tenant;
        $tenantId = $tenant?->id ?? $request->user()->tenant_id;

        $validated = $request->validate([
            'name'      => ['required', 'string', 'max:255'],
            'email'     => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password'  => ['required', 'confirmed', Password::min(8)],
            'role_id'   => ['required', 'exists:roles,id'],
            'branch_id' => ['required', 'exists:branches,id'],
        ], [
            'name.required'      => 'Ad soyad gereklidir.',
            'email.required'     => 'E-posta adresi gereklidir.',
            'email.unique'       => 'Bu e-posta adresi zaten kullanılıyor.',
            'password.required'  => 'Şifre gereklidir.',
            'password.confirmed' => 'Şifreler eşleşmiyor.',
            'role_id.required'   => 'Rol seçimi gereklidir.',
            'branch_id.required' => 'Şube seçimi gereklidir.',
        ]);

        // Şubenin bu tenant'a ait olduğunu doğrula
        $branchQuery = Branch::where('id', $validated['branch_id']);
        if ($tenantId) {
            $branchQuery->where('tenant_id', $tenantId);
        }
        $branch = $branchQuery->firstOrFail();

        $user = User::create([
            'name'      => $validated['name'],
            'email'     => $validated['email'],
            'password'  => Hash::make($validated['password']),
            'tenant_id' => $tenantId ?? $branch->tenant_id,
            'branch_id' => $branch->id,
            'role_id'   => $validated['role_id'],
        ]);

        // user_roles pivot kaydı
        UserRole::create([
            'user_id'    => $user->id,
            'role_id'    => $validated['role_id'],
            'tenant_id'  => $user->tenant_id,
            'branch_id'  => $branch->id,
            'created_at' => now(),
        ]);

        return redirect()->route('admin.users.index')
            ->with('success', "{$user->name} kullanıcısı başarıyla oluşturuldu.");
    }

    /**
     * Kullanıcı düzenleme formu
     */
    public function edit(Request $request, User $user)
    {
        $tenant = $request->user()->tenant;
        $tenantId = $tenant?->id ?? $request->user()->tenant_id;

        if ($tenantId && $user->tenant_id !== $tenantId) {
            abort(403);
        }

        $roles = Role::orderBy('name')->get();
        $branchQuery = Branch::where('is_active', true);
        if ($tenantId) {
            $branchQuery->where('tenant_id', $tenantId);
        } elseif ($user->tenant_id) {
            $branchQuery->where('tenant_id', $user->tenant_id);
        }
        $branches = $branchQuery->get();

        return view('admin.users.edit', compact('user', 'roles', 'branches'));
    }

    /**
     * Kullanıcı güncelle
     */
    public function update(Request $request, User $user)
    {
        $tenant = $request->user()->tenant;
        $tenantId = $tenant?->id ?? $request->user()->tenant_id;

        if ($tenantId && $user->tenant_id !== $tenantId) {
            abort(403);
        }

        $rules = [
            'name'      => ['required', 'string', 'max:255'],
            'email'     => ['required', 'string', 'email', 'max:255', 'unique:users,email,' . $user->id],
            'role_id'   => ['required', 'exists:roles,id'],
            'branch_id' => ['required', 'exists:branches,id'],
        ];

        // Şifre opsiyonel (boş bırakılırsa değişmez)
        if ($request->filled('password')) {
            $rules['password'] = ['confirmed', Password::min(8)];
        }

        $validated = $request->validate($rules);

        $branchQuery = Branch::where('id', $validated['branch_id']);
        if ($tenantId) {
            $branchQuery->where('tenant_id', $tenantId);
        }
        $branch = $branchQuery->firstOrFail();

        $user->update([
            'name'      => $validated['name'],
            'email'     => $validated['email'],
            'role_id'   => $validated['role_id'],
            'branch_id' => $branch->id,
        ]);

        if ($request->filled('password')) {
            $user->update(['password' => Hash::make($validated['password'])]);
        }

        // user_roles güncelle
        UserRole::updateOrCreate(
            ['user_id' => $user->id, 'tenant_id' => $user->tenant_id],
            ['role_id' => $validated['role_id'], 'branch_id' => $branch->id]
        );

        return redirect()->route('admin.users.index')
            ->with('success', "{$user->name} kullanıcısı başarıyla güncellendi.");
    }

    /**
     * Kullanıcı sil
     */
    public function destroy(Request $request, User $user)
    {
        $tenant = $request->user()->tenant;
        $tenantId = $tenant?->id ?? $request->user()->tenant_id;

        if ($tenantId && $user->tenant_id !== $tenantId) {
            abort(403);
        }

        // Kendini silemez
        if ($user->id === $request->user()->id) {
            return back()->with('error', 'Kendinizi silemezsiniz.');
        }

        $name = $user->name;
        $user->delete();

        return redirect()->route('admin.users.index')
            ->with('success', "{$name} kullanıcısı başarıyla silindi.");
    }

    /**
     * Kullanıcı rolünü hızlı güncelle
     */
    public function updateRole(Request $request, User $user)
    {
        $tenant = $request->user()->tenant;
        $tenantId = $tenant?->id ?? $request->user()->tenant_id;

        if ($tenantId && $user->tenant_id !== $tenantId) {
            abort(403);
        }

        $validated = $request->validate([
            'role_id' => ['required', 'exists:roles,id'],
        ]);

        $user->update(['role_id' => $validated['role_id']]);

        UserRole::updateOrCreate(
            ['user_id' => $user->id, 'tenant_id' => $user->tenant_id],
            ['role_id' => $validated['role_id']]
        );

        return back()->with('success', 'Kullanıcı rolü güncellendi.');
    }
}
