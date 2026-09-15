<?php

namespace App\Http\Livewire\Admin;

use App\Models\Quote;
use App\Models\Seminar;
use Carbon\Carbon;
use App\Models\Webinar;
use App\Models\User;
use App\Models\Booking;
use Livewire\Component;
use Illuminate\Support\Facades\DB;
use Jantinnerezo\LivewireAlert\LivewireAlert;

class Index extends Component
{
    use LivewireAlert;

    // protected $layout = null;

    public function mount()
    {
    }

    public function render()
    {

        $webinar = Webinar::query()
        ->select('*')->selectRaw('(TIMESTAMPDIFF(SECOND, NOW(), CONCAT(start_date, " ", end_time))) AS time_diff_seconds')
        ->orderByRaw('CASE WHEN CONCAT(start_date, " ", end_time) < NOW() THEN 1 ELSE 0 END') 
        ->orderBy(\DB::raw('time_diff_seconds > 0 DESC, ABS(time_diff_seconds)'), 'asc')
        ->first();


        $seminar = Seminar::query()
        ->select('*')->selectRaw('(TIMESTAMPDIFF(SECOND, NOW(), CONCAT(start_date, " ", end_time))) AS time_diff_seconds')
        ->orderByRaw('CASE WHEN CONCAT(start_date, " ", end_time) < NOW() THEN 1 ELSE 0 END') 
        ->orderBy(\DB::raw('time_diff_seconds > 0 DESC, ABS(time_diff_seconds)'), 'asc')
        ->first();


        $today = Carbon::today();
        $todaysQuote = Quote::whereDate('created_at', $today)->first();
        $submissionPercentage = 0;
        
        if ($todaysQuote) {
            $submissionPercentage = round($todaysQuote->users()->count() / getTotalUsers() * 100);
        }

        $leadUsersList = cacheVipUsers();

        $totalRegisteredUser = User::whereHas('roles',function($query){
          $query->whereIn('id',[config('constants.role.user'),config('constants.role.volunteer')]);
        })->count();
      
        $totalActiveUser = User::whereHas('roles',function($query){
          $query->whereIn('id',[config('constants.role.user'),config('constants.role.volunteer')]);
        })->where('is_active',0)->count();
      
        $totalVIPUser = User::whereHas('roles',function($query){
          $query->where('id',config('constants.role.user'));
        })->whereNotNull('vip_at')->count();
      
        $totalSeminarTikcketPurchased = Booking::count();


        return view('livewire.admin.index', compact('webinar', 'seminar', 'todaysQuote', 'submissionPercentage', 'leadUsersList','totalRegisteredUser','totalActiveUser','totalVIPUser','totalSeminarTikcketPurchased'));
    }

}
