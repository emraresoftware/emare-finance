<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Permission;
use App\Models\Role;
use Illuminate\Http\Request;

class RoleController extends Controller
{
    /**
     * Rol listesi
     */
    public function index()
    {
        $roles = Role::withCount('permissions')->orderBy('is_system', 'desc')->orderBy('name')->get();

        return view('admin.roles.index', compact('roles'));
    }

    /**
     * Rol detayı — izinleri göster
     */
    public function show(Role $role)
    {
        $role->load('permissions');

        // Tüm izinleri grupla
        $allPermissions = Permission::orderBy('group')->orderBy('code')->get()->groupBy('group');

        // Rolün sahip olduğu izin ID'leri
        $rolePermissionIds = $role->permissions->pluck('id')->toArray();

        return view('admin.roles.show', compact('role', 'allPermissions', 'rolePermissionIds'));
    }

    /**
     * Rolün izinlerini güncelle
     */
    public function updatePermissions(Request $request, Role $role)
    {
        // Sistem rolü kontrolü — admin rolü düzenlenemez
        if ($role->code === 'admin') {
            return back()->with('error', 'Admin rolünün izinleri değiştirilemez.');
        }

        $validated = $request->validate([
            'permissions'   => ['nullable', 'array'],
            'permissions.*' => ['integer', 'exists:permissions,id'],
        ]);

        $permissionIds = $validated['permissions'] ?? [];

        $role->permissions()->sync($permissionIds);

        return back()->with('success', "{$role->name} rolünün izinleri başarıyla güncellendi.");
    }
}
