<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\ChatRoom;

class ChatRoomController extends Controller
{
    public function index()
    {
        $rooms = ChatRoom::where('is_active', true)->get();
        return response()->json([
            'success' => true,
            'data' => $rooms
        ]);
    }

    public function join(Request $request, $roomId)
    {
        $room = ChatRoom::findOrFail($roomId);
        $user = $request->user();
        
        if (!$room->users()->where('user_id', $user->id)->exists()) {
            $room->users()->attach($user->id);
        }

        return response()->json(['success' => true, 'message' => 'Joined room']);
    }

    public function leave(Request $request, $roomId)
    {
        $room = ChatRoom::findOrFail($roomId);
        $user = $request->user();

        $room->users()->detach($user->id);

        return response()->json(['success' => true, 'message' => 'Left room']);
    }

    public function members($roomId)
    {
        $room = ChatRoom::findOrFail($roomId);
        
        // Get basic user details and append profile image url via the model accessor
        // Also get pivot data to check if blocked
        $members = $room->users()->select('users.id', 'users.name')
            ->withPivot('is_blocked')
            ->get()->map(function($user) {
                $user->append('profile_image_url');
                return $user;
            });

        return response()->json([
            'success' => true,
            'data' => $members
        ]);
    }

    public function blockUser(Request $request, $roomId, $userId)
    {
        // Check if admin (Assuming current user is admin, you should add proper middleware/checks)
        $user = $request->user();
        if ($user->role_id != 1 && $user->role != 'admin') {
            return response()->json(['success' => false, 'message' => 'Unauthorized. Admin only.'], 403);
        }

        $room = ChatRoom::findOrFail($roomId);
        $pivot = $room->users()->where('user_id', $userId)->first();
        if ($pivot) {
            $isBlocked = $pivot->pivot->is_blocked;
            $newValue = $isBlocked ? 0 : 1;
            $room->users()->updateExistingPivot($userId, ['is_blocked' => $newValue]);
        }

        return response()->json(['success' => true, 'message' => 'User block status updated.']);
    }

    public function blockUserAdmin(Request $request, $roomId, $userId)
    {
        if ($request->secret !== 'trueyou_admin_secret_2026') {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $room = ChatRoom::findOrFail($roomId);
        $pivot = $room->users()->where('user_id', $userId)->first();
        if ($pivot) {
            $isBlocked = $pivot->pivot->is_blocked;
            $newValue = $isBlocked ? 0 : 1;
            $room->users()->updateExistingPivot($userId, ['is_blocked' => $newValue]);
        }

        return response()->json(['success' => true, 'message' => 'User block status updated.']);
    }

    public function reportMessage(Request $request, $roomId)
    {
        $request->validate([
            'firebase_message_id' => 'required|string',
            'reason' => 'nullable|string'
        ]);

        \App\Models\ReportedMessage::create([
            'firebase_message_id' => $request->firebase_message_id,
            'room_id' => $roomId,
            'reported_by' => $request->user()->id,
            'reason' => $request->reason
        ]);

        return response()->json(['success' => true, 'message' => 'Message reported successfully.']);
    }

    public function uploadFile(Request $request, $roomId)
    {
        $request->validate([
            'file' => 'required|file|max:10240' // 10MB limit
        ]);

        $file = $request->file('file');
        $path = $file->store('chat_uploads', 'public');
        
        return response()->json([
            'success' => true,
            'file_url' => asset('storage/' . $path),
            'file_type' => $file->getClientMimeType(),
            'file_name' => $file->getClientOriginalName()
        ]);
    }
}
