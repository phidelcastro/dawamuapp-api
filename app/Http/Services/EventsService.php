<?php

namespace App\Http\Services;

use App\Models\Guardian;
use App\Models\GuardianEvent;
use App\Models\GuardianEventRecipient;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;

class EventsService
{
    public function saveEventRecord(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'date' => 'required|date',
            'confirm_by' => 'nullable|date',
            'requirements' => 'nullable|string',
            'location' => 'nullable|string|max:255',
            'notification_types' => 'required|array',
            'notification_types.*' => 'in:email,push,phone',
            'reminder_frequency' => 'nullable|in:once,daily,weekly,daily_x_days_before',
            'reminder_days_before' => 'nullable|integer',
            'remind_until' => 'nullable|date',
            'parents' => 'required|array',
            'parents.*' => 'exists:guardians,id',
            'banner' => 'nullable|file|image|max:2048',
        ]);

        DB::beginTransaction();

        try {
            if ($request->hasFile('banner')) {
                $validated['banner_path'] = $request->file('banner')->store('event_banners', 'public');
            }

            $event = GuardianEvent::create([
                ...$validated,
                'notification_types' => json_encode($validated['notification_types']),
                'parents' => json_encode($validated['parents']),
            ]);

            foreach ($validated['parents'] as $guardianId) {
                GuardianEventRecipient::create([
                    'guardian_event_id' => $event->id,
                    'guardian_id' => $guardianId,
                    'phone_delivery_status' => 'not_sent',
                    'email_delivery_status' => 'not_sent',
                    'push_status' => 'not_sent',
                    'status' => 'pending',
                ]);
            }

            DB::commit();

            return response()->json(['message' => 'Event reminder created successfully.', 'event' => $event], 201);
        } catch (\Throwable $e) {
            DB::rollBack();
            return response()->json(['message' => 'Failed to create event reminder', 'error' => $e->getMessage()], 500);
        }
    }

    public function updateEventRecord(Request $request, $id)
    {
        $event = GuardianEvent::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'date' => 'required|date',
            'confirm_by' => 'nullable|date',
            'requirements' => 'nullable|string',
            'location' => 'nullable|string|max:255',
            'notification_types' => 'required|array',
            'notification_types.*' => 'in:email,push,phone',
            'reminder_frequency' => 'nullable|in:once,daily,weekly,daily_x_days_before',
            'reminder_days_before' => 'nullable|integer',
            'remind_until' => 'nullable|date',
            'parents' => 'required|array',
            'parents.*' => 'exists:guardians,id',
            'banner' => 'nullable|file|image|max:2048',
        ]);

        DB::beginTransaction();

        try {
            if ($request->hasFile('banner')) {
                if ($event->banner_path) {
                    Storage::disk('public')->delete($event->banner_path);
                }
                $validated['banner_path'] = $request->file('banner')->store('event_banners', 'public');
            }

            $event->update([
                ...$validated,
                'notification_types' => json_encode($validated['notification_types']),
                'parents' => json_encode($validated['parents']),
            ]);

            // Optionally, sync recipients
            $existingGuardianIds = GuardianEventRecipient::where('guardian_event_id', $event->id)->pluck('guardian_id')->toArray();

            $newGuardians = array_diff($validated['parents'], $existingGuardianIds);
            foreach ($newGuardians as $guardianId) {
                GuardianEventRecipient::create([
                    'gurdian_event_id' => $event->id,
                    'guardian_id' => $guardianId,
                    'phone_delivery_status' => 'not_sent',
                    'email_delivery_status' => 'not_sent',
                    'push_status' => 'not_sent',
                    'status' => 'pending',
                ]);
            }

            DB::commit();
            return response()->json(['message' => 'Event reminder updated successfully.', 'event' => $event]);
        } catch (\Throwable $e) {
            DB::rollBack();
            return response()->json(['message' => 'Failed to update event reminder', 'error' => $e->getMessage()], 500);
        }
    }

    public function getEventsRecords(Request $request)
    {
        $query = GuardianEvent::with('recipients.guardian.user');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where('name', 'like', "%$search%");
        }
        return response()->json(['message' => 'Event reminder updated successfully.', 'events' => $query->latest()->paginate($request->input('perPage', 10))]);
        
    }

    public function showEvent($id)
    {
        return GuardianEvent::with('recipients.guardian.user')->findOrFail($id);
    }
    public function getEventRecipients($id)
    {
        $recipients = GuardianEventRecipient::where('guardian_event_id', $id)->with('guardian.user')->paginate();
        return response()->json(['message' => 'loaded.', 'recipients' =>$recipients]); 
    }
}
