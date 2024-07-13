<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\AuditTrail;
use Illuminate\Http\Request;

class RoleController extends Controller
{
    public function index()
    {
        $roles = Role::all();
        return response()->json(['roles' => $roles]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'role_name' => 'required|string|max:300',
        ]);

        $role = Role::create([
            'role_name' => $request->input('role_name'),
        ]);

        AuditTrail::create([
            'user_id' => auth()->id(), // Assuming authentication is set up
            'action' => 'role_created',
            'entity_type' => Role::class,
            'entity_id' => $role->id,
        ]);

        return response()->json(['message' => 'Role created successfully', 'role' => $role], 201);
    }

    public function show($id)
    {
        $role = Role::findOrFail($id);
        return response()->json(['role' => $role]);
    }

    public function update(Request $request, $id)
    {
        $role = Role::findOrFail($id);

        $request->validate([
            'role_name' => 'required|string|max:30',
        ]);

        $role->update([
            'role_name' => $request->input('role_name'),
        ]);

        AuditTrail::create([
            'user_id' => auth()->id(), // Assuming authentication is set up
            'action' => 'role_updated',
            'entity_type' => Role::class,
            'entity_id' => $role->id,
        ]);

        return response()->json(['message' => 'Role updated successfully', 'role' => $role]);
    }

    public function destroy($id)
    {
        $role = Role::findOrFail($id);

        $role->delete();

        AuditTrail::create([
            'user_id' => auth()->id(), // Assuming authentication is set up
            'action' => 'role_deleted',
            'entity_type' => Role::class,
            'entity_id' => $role->id,
        ]);

        return response()->json(['message' => 'Role deleted successfully']);
    }
}
