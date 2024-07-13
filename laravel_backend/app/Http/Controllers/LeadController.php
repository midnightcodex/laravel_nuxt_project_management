<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\LeadDetail;
use App\Models\AuditTrail;
use App\Models\ItemCycle;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class LeadController extends Controller
{
    public function index()
    {
        $leads = Item::where('type', 'lead')->with('leadDetail')->get();
        return response()->json($leads);
    }



    public function store(Request $request)
    {
        // Start a database transaction
        DB::beginTransaction();

        try {
            // Validate the request
            $request->validate([
                'title' => 'required|string|max:255',
                'description' => 'required|string',
                'status' => 'required|string|max:30',
                'created_by' => 'required|uuid|exists:users,id',
                'assigned_to' => 'nullable|uuid|exists:users,id',
                'lead_source' => 'nullable|string|max:255',
                'lead_value' => 'nullable|numeric',
                'potential_close_date' => 'nullable|date',
            ]);

            // Create the lead item
            $lead = new Item([
                'id' => (string) Str::uuid(),
                'title' => $request->title,
                'description' => $request->description,
                'status' => $request->status,
                'type' => 'lead',
                'created_by' => $request->created_by,
                'assigned_to' => $request->assigned_to,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            $lead->save();

            // Create the lead details
            $leadDetail = new LeadDetail([
                'item_id' => $lead->id,
                'lead_source' => $request->lead_source,
                'lead_value' => $request->lead_value,
                'potential_close_date' => $request->potential_close_date,
            ]);

            $leadDetail->save();

            // Create the initial item cycle entry
            $itemCycle = new ItemCycle([
                'id' => (string) Str::uuid(),
                'item_id' => $lead->id,
                'status' => 'created',
                'updated_by' => $request->created_by, // Assuming the creator initiates the cycle
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            $itemCycle->save();

            // Log the action in the audit trail
            $this->logAuditTrail($request->user()->id, 'create', 'items', $lead->id);

            // Commit the transaction
            DB::commit();

            // Return the response
            return response()->json($lead->load('leadDetail', 'itemCycle'), 201);
        } catch (\Exception $e) {
            // If an exception occurs, rollback the transaction
            DB::rollBack();

            // Log the exception or handle errors as needed
            return response()->json(['error' => 'Transaction failed. ' . $e->getMessage()], 500);
        }
    }


    public function show($id)
    {
        $lead = Item::where('id', $id)->where('type', 'lead')->with('leadDetail')->firstOrFail();
        return response()->json($lead);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'title' => 'sometimes|required|string|max:255',
            'description' => 'sometimes|required|string',
            'status' => 'sometimes|required|string|max:30',
            'assigned_to' => 'sometimes|uuid|exists:users,id',
            'lead_source' => 'nullable|string|max:255',
            'lead_value' => 'nullable|numeric',
            'potential_close_date' => 'nullable|date',
        ]);

        $lead = Item::where('id', $id)->where('type', 'lead')->firstOrFail();
        $lead->update($request->only(['title', 'description', 'status', 'assigned_to']));

        $leadDetail = LeadDetail::findOrFail($id);
        $leadDetail->update($request->only(['lead_source', 'lead_value', 'potential_close_date']));

        $this->logAuditTrail($request->user()->id, 'update', 'items', $lead->id);

        return response()->json($lead->load('leadDetail'));
    }

    public function destroy($id)
    {
        $lead = Item::where('id', $id)->where('type', 'lead')->firstOrFail();
        $lead->delete();

        LeadDetail::findOrFail($id)->delete();

        $this->logAuditTrail(auth()->user()->id, 'delete', 'items', $lead->id);

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
