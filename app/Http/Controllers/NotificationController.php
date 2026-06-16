<?php
namespace App\Http\Controllers;
use App\Models\Notification;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function index()
    {
        $notifications = Notification::where('user_id',auth()->id())->latest()->paginate(20);
        return view('notifications.index', compact('notifications'));
    }

    public function markRead($id)
    {
        Notification::where('id',$id)->where('user_id',auth()->id())->update(['is_read'=>true]);
        return back();
    }

    public function markAllRead()
    {
        Notification::where('user_id',auth()->id())->update(['is_read'=>true]);
        return back()->with('success','Semua notifikasi ditandai sudah dibaca.');
    }
}
