<?php

namespace App\Http\Controllers;

use App\Models\Permission;
use App\Models\AuditTrail;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class PermissionController extends Controller
{
    public function index()
    {
        $permissions = Permission::all();
        return response()->json($permissions);
    }

    public function store(Request $request)
    {
        $request->validate([
            'role_id' => 'required|uuid|exists:roles,id',
            'entity_type' => 'required|string|max:30',
            'entity_id' => 'required|uuid',
            'read_permission' => 'required|boolean',
            'write_permission' => 'required|boolean',
        ]);

        $permission = new Permission([
            'id' => (string) Str::uuid(),
            'role_id' => $request->role_id,
            'entity_type' => $request->entity_type,
            'entity_id' => $request->entity_id,
            'read_permission' => $request->read_permission,
            'write_permission' => $request->write_permission,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $permission->save();

        $this->logAuditTrail($request->user()->id, 'create', 'permissions', $permission->id);

        return response()->json($permission, 201);
    }

    public function show($id)
    {
        $permission = Permission::findOrFail($id);
        return response()->json($permission);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'role_id' => 'sometimes|required|uuid|exists:roles,id',
            'entity_type' => 'sometimes|required|string|max:30',
            'entity_id' => 'sometimes|required|uuid',
            'read_permission' => 'sometimes|required|boolean',
            'write_permission' => 'sometimes|required|boolean',
        ]);

        $permission = Permission::findOrFail($id);
        $permission->update($request->all());

        $this->logAuditTrail($request->user()->id, 'update', 'permissions', $permission->id);

        return response()->json($permission);
    }

    public function destroy($id)
    {
        $permission = Permission::findOrFail($id);
        $permission->delete();

        $this->logAuditTrail(auth()->user()->id, 'delete', 'permissions', $permission->id);

        return response()->json(null, 204);
    }

    protected function logAuditTrail($userId, $action, $entityType, $entityId)
    {
        AuditTrail::create([
            'id' => (string) Str::uuid(),
            'user_id' => $userId,
            'action' => $action,
            'entity_type' => $entityType,
            'entity_id' => $entityId,
            'created_at' => now(),
        ]);
    }
}
