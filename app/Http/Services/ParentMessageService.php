<?php

namespace App\Http\Services;
use App\Jobs\SendParentMessagesJob;
use App\Models\GuardianEventRecipient;
use App\Models\SchoolParentMessage;
use App\Models\ParentSchoolParentMessage;
use Request;

class ParentMessageService
{
    public function createAndDispatch(array $validated, ?int $creatorId = null): SchoolParentMessage
    {
    
        $message = SchoolParentMessage::create([
            'types' => $validated['types'],
            'subject' => $validated['subject'] ?? null,
            'content' => $validated['content'],
            'created_by' => $creatorId,
        ]);

        
        foreach ($validated['parents'] as $parentId) {
            ParentSchoolParentMessage::create([
                'school_parent_message_id' => $message->id,
                'parent_id' => $parentId,
            ]);
        }

        
        dispatch(new SendParentMessagesJob($message));

        return $message;
    }
        public function getEventRecipients($id)
    {
        $recipients = GuardianEventRecipient::where('guardian_event_id', $id)->with('guardian.user')->paginate();
        return response()->json(['message' => 'loaded.', 'recipients' =>$recipients]); 
    }
}
