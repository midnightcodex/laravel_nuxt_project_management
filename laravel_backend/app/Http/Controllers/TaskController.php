<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\TaskDetail;
use App\Models\AuditTrail;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class TaskController extends Controller
{
    public function index()
    {
        $tasks = Item::where('type', 'task')->with('taskDetail')->get();
        return response()->json($tasks);
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'status' => 'required|string|max:30',
            'created_by' => 'required|uuid|exists:users,id',
            'assigned_to' => 'nullable|uuid|exists:users,id',
            'task_due_date' => 'nullable|date',
            'task_priority' => 'required|in:low,medium,high',
            'task_type' => 'nullable|string|max:255',
        ]);

        $task = new Item([
            'id' => (string) Str::uuid(),
            'title' => $request->title,
            'description' => $request->description,
            'status' => $request->status,
            'type' => 'task',
            'created_by' => $request->created_by,
            'assigned_to' => $request->assigned_to,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $task->save();

        $taskDetail = new TaskDetail([
            'id' => $task->id,
            'task_due_date' => $request->task_due_date,
            'task_priority' => $request->task_priority,
            'task_type' => $request->task_type,
        ]);

        $taskDetail->save();

        $this->logAuditTrail($request->user()->id, 'create', 'items', $task->id);

        return response()->json($task->load('taskDetail'), 201);
    }

    public function show($id)
    {
        $task = Item::where('id', $id)->where('type', 'task')->with('taskDetail')->firstOrFail();
        return response()->json($task);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'title' => 'sometimes|required|string|max:255',
            'description' => 'sometimes|required|string',
            'status' => 'sometimes|required|string|max:30',
            'assigned_to' => 'sometimes|uuid|exists:users,id',
            'task_due_date' => 'nullable|date',
            'task_priority' => 'nullable|in:low,medium,high',
            'task_type' => 'nullable|string|max:255',
        ]);

        $task = Item::where('id', $id)->where('type', 'task')->firstOrFail();
        $task->update($request->only(['title', 'description', 'status', 'assigned_to']));

        $taskDetail = TaskDetail::findOrFail($id);
        $taskDetail->update($request->only(['task_due_date', 'task_priority', 'task_type']));

        $this->logAuditTrail($request->user()->id, 'update', 'items', $task->id);

        return response()->json($task->load('taskDetail'));
    }

    public function destroy($id)
    {
        $task = Item::where('id', $id)->where('type', 'task')->firstOrFail();
        $task->delete();

        TaskDetail::findOrFail($id)->delete();

        $this->logAuditTrail(auth()->user()->id, 'delete', 'items', $task->id);

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
