<?php

use Illuminate\Support\Str as Str;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\Uploads;
use App\Models\User;
use App\Models\Role;
use Illuminate\Support\Facades\Mail;
use Carbon\Carbon;
use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Support\Facades\Storage;
use App\Models\Setting;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\File;


if (!function_exists('convertToFloat')) {
	function convertToFloat($value)
	{
		if (is_numeric($value)) {
			return number_format((float)$value, 2, '.', ' ');
		}
		return $value;
	}
}

if (!function_exists('convertToFloatNotRound')) {
	function convertToFloatNotRound($value)
	{
		if (is_numeric($value)) {
			$dec = 2;
			return number_format(floor($value * pow(10, $dec)) / pow(10, $dec), $dec);
		}
		return $value;
	}
}

if (!function_exists('uploadImage')) {
	/**
	 * Upload Image.
	 *
	 * @param array $input
	 *
	 * @return array $input
	 */
	function uploadImage($directory, $file, $folder, $type = "profile", $fileType = "jpg", $actionType = "save", $uploadId = null, $orientation = null)
	{
		$oldFile = null;
		if ($actionType == "save") {
			$upload               		= new Uploads;
		} else {
			$upload               		= Uploads::find($uploadId);
			$oldFile = $upload->file_path;
		}
		$upload->file_path      	= $file->store($folder, 'public');
		$upload->extension      	= $file->getClientOriginalExtension();
		$upload->original_file_name = $file->getClientOriginalName();
		$upload->type 				= $type;
		$upload->file_type 			= $fileType;
		$upload->orientation 		= $orientation;
		$response             		= $directory->uploads()->save($upload);
		// delete old file
		if ($oldFile) {
			Storage::disk('public')->delete($oldFile);
		}
		return $upload;
	}
}

if (!function_exists('uploadFile')) {
	function uploadFile($directory,$tmpFolderPath, $newFolderPath, $type="profile", $fileType="jpg",$actionType="save",$uploadId=null,$orientation=null)
	{
		$saveFilePath = $newFolderPath;

		 // Set the paths for the tmp and new directories
		 $tmpPath = storage_path('app/public/'.$tmpFolderPath);
		 $newPath = storage_path('app/public/'.$newFolderPath);
	 
		// Check if the file exists in the tmp directory
		if (File::exists("{$tmpPath}")) {
			
			if (!File::exists($newPath)) {
				File::makeDirectory($newPath, 0775, true, true);
			}

			$extension = pathinfo($tmpPath, PATHINFO_EXTENSION);

			$timestamp = now()->timestamp;
			$uniqueId = uniqid();

			$fileName = $timestamp . '_' . $uniqueId;

			$newPath .= $fileName.'.'.$extension; 
			$saveFilePath .= $fileName.'.'.$extension;

			// Move the file to the new directory
			File::move("{$tmpPath}", "{$newPath}");
			
			$oldFile = null;
			if($actionType == "save"){
				$upload               		= new Uploads;
			}else{
				$upload               		= Uploads::find($uploadId);
				$oldFile = $upload->file_path;
			}

			$upload->file_path      	= $saveFilePath;
			$upload->extension      	= $extension;
			$upload->original_file_name = null;
			$upload->type 				= $type;
			$upload->file_type 			= $fileType;
			$upload->orientation 		= $orientation;
			$response             		= $directory->uploads()->save($upload);
			// delete old file
			if($oldFile){
				Storage::disk('public')->delete($oldFile);
			}
			
			return $upload;
		}

	}
}

if (!function_exists('deleteFile')) {
	/**
	 * Destroy Old Image.	 *
	 * @param int $id
	 */
	function deleteFile($upload_id)
	{
		$upload = Uploads::find($upload_id);
		if($upload){
			Storage::disk('public')->delete($upload->file_path);
			$upload->delete();
		}
		
		return true;
	}
}


if (!function_exists('CurlPostRequest')) {
	function CurlPostRequest($url, $headers, $postFields)
	{
		$curl = curl_init();
		curl_setopt_array($curl, array(
			CURLOPT_URL => $url,
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_ENCODING => '',
			CURLOPT_MAXREDIRS => 10,
			CURLOPT_TIMEOUT => 0,
			CURLOPT_FOLLOWLOCATION => true,
			CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
			CURLOPT_CUSTOMREQUEST => 'POST',
			CURLOPT_POSTFIELDS => $postFields,
			CURLOPT_HTTPHEADER => $headers,
		));
		$response = curl_exec($curl);
		curl_close($curl);
		return json_decode($response);
	}
}

if (!function_exists('CurlGetRequest')) {
	function CurlGetRequest($url, $headers)
	{
		$curl = curl_init();

		curl_setopt_array($curl, array(
			CURLOPT_URL => $url,
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_ENCODING => '',
			CURLOPT_MAXREDIRS => 10,
			CURLOPT_TIMEOUT => 0,
			CURLOPT_FOLLOWLOCATION => true,
			CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
			CURLOPT_CUSTOMREQUEST => 'GET',
			CURLOPT_HTTPHEADER => $headers
		));

		$response = curl_exec($curl);
		curl_close($curl);
		return json_decode($response);
	}
}

if (!function_exists('getCommonValidationRuleMsgs')) {
	function getCommonValidationRuleMsgs()
	{
		return [
			'password.required' => 'The new password is required.',
			'password.min' => 'The new password must be at least 8 characters',
			'password.different' => 'The new password and current password must be different.',
			'password.confirmed' => 'The password confirmation does not match.',
			'password_confirmation.required' => 'The new password confirmation is required.',
			'password_confirmation.min' => 'The new password confirmation must be at least 8 characters',
			'email.required' => 'Please enter email address.',
			'email.email' => 'Email is not valid. Enter email address for example test@gmail.com',
		];
	}
}

if (!function_exists('generateRandomString')) {
	function generateRandomString($length = 20)
	{

		$randomString = Str::random($length);

		return $randomString;
	}
}

if (!function_exists('convertDateTimeFormat')) {
	function convertDateTimeFormat($value, $type = 'date')
	{
		$changeFormatValue = Carbon::parse($value);
		if ($type == 'date') {
			return $changeFormatValue->format(config('constants.date_format'));
		} else if ($type == 'datetime') {
			return $changeFormatValue->format(config('constants.datetime_format'));
		} else if ($type == 'fulldatetime') {
			return $changeFormatValue->format(config('constants.full_datetime_format'));
		} else if ($type == 'fulldate') {
			return $changeFormatValue->format(config('constants.full_date_format'));
		}else if ($type == 'fulltime') {
			return $changeFormatValue->format(config('constants.full_time_format'));
		}else if ($type == 'date_month_year') {
			return $changeFormatValue->format(config('constants.date_month_year'));
		}
		return $changeFormatValue;
	}
}


if (!function_exists('generateOTP')) {
	function generateOTP()
	{
		return rand(1000, 9999);
	}
}

if (!function_exists('getDecryptSlug')) {
	function getDecryptSlug($encryptSlug)
	{
		try {
			$id = last(explode('-', $encryptSlug));
			return decrypt($id);
		} catch (DecryptException $e) {
			return false;
		}
	}
}

if (!function_exists('getSetting')) {
	function getSetting($key)
	{
		$result = null;
		$setting = Setting::where('key', $key)->where('status', 1)->first();
		if ($setting) {
			if ($setting->type == 'image') {
				$result = $setting->image_url;
			} elseif ($setting->type == 'video') {
				$result = $setting->video_url;
			} else {
				$result = $setting->value;
			}
		}
		return $result;
	}
}

if (!function_exists('getSettingByGroup')) {
	function getSettingByGroup($groupkey)
	{
		$settings = Setting::select('id','key','value')->where('group', $groupkey)->where('status', 1)->get();
		if ($settings->count()> 0) {
			foreach($settings as $setting){
				if ($setting->type == 'image') {
					$setting->image_url = $setting->image_url;
				} elseif ($setting->type == 'video') {
					$setting->image_url = $setting->video_url;
				} 
			}
			
		}
		return $settings;
	}
}

if (!function_exists('cacheVipUsers')) {
	function cacheVipUsers($type ='get'){
		if($type == 'get'){
			if (Cache::has('vip_users')) {
				$vipUsers = Cache::get('vip_users');
			} else {
				$vipUsers = getVipUsers();
				Cache::put('vip_users', $vipUsers);
			}
			return $vipUsers;
		} else if($type == 'set'){
			if (Cache::has('vip_users')) {
				Cache::forget('vip_users');
			}
			$vipUsers = getVipUsers();
			Cache::put('vip_users', $vipUsers);
		}
	}
}

if (!function_exists('getVipUsers')) {
	function getVipUsers(){
		$vipUsers = User::select('id', 'name', 'vip_at')->whereNotNull('vip_at')->orderBy('vip_at', 'desc')->get();
		foreach($vipUsers as $vipUser){
			$vipUser->profile_image_url = $vipUser->profile_image_url ? $vipUser->profile_image_url : asset(config('constants.default.profile_image'));
		}
		return $vipUsers;
	}
}

if (!function_exists('getTotalUsers')) {
	function getTotalUsers()
	{
		$userCount = User::whereHas('roles', function ($query) {
			$query->where('id', 2);
		})->count();

		return $userCount;
	}
}

if (!function_exists('generateBookingNumber')) {
   function generateBookingNumber($seminarId){
		$length = 6;
		$currentYear = date('y');

		// Get the latest ticket number and increment it
		$latestBooking = DB::table('bookings')->where('bookingable_id',$seminarId)->latest()->first();
		$latestNumber = optional($latestBooking)->booking_number;

		// Extract the last 6 digits
		if($latestNumber){
			$latestNumber = substr($latestNumber, -($length));
		}

		$ticketNumber = $latestNumber ? intval($latestNumber) + 1 : 1;

		// Ensure the ticket number has the desired length
		$formattedTicketNumber = str_pad($ticketNumber, $length, '0', STR_PAD_LEFT);

		return  $currentYear.$seminarId.$formattedTicketNumber;
   }
}

if (!function_exists('encryptValue')) {

	function encryptValue($id)
	{
		$encryptedId = Crypt::encryptString($id);
		return $encryptedId;
	}
}

if (!function_exists('decryptValue')) {

	function decryptValue($encryptedId)
	{
		$decryptedId = Crypt::decryptString($decodedId);
		return $decryptedId;

	}
}

if (!function_exists('getYouTubeVideoId')) {

	function getYouTubeVideoId($url)
	{
		// Parse the URL
		$parsedUrl = parse_url($url);
		
		// Check if the URL is a short YouTube URL (e.g., youtu.be)
		if (isset($parsedUrl['host']) && $parsedUrl['host'] == 'youtu.be') {
			// Extract the video ID from the path
			return ltrim($parsedUrl['path'], '/');
		}
		
		// For full YouTube URLs (optional handling)
		// if the structure is https://www.youtube.com/watch?v=video_id
		if (isset($parsedUrl['query'])) {
			parse_str($parsedUrl['query'], $queryParams);
			if (isset($queryParams['v'])) {
				return $queryParams['v'];
			}
		}

		// Return null if no valid video ID is found
		return null;
	}

}

if (!function_exists('normalizeYouTubeEmbedUrl')) {
    function normalizeYouTubeEmbedUrl($url)
    {
        $parsedUrl = parse_url($url);
    
        // Handle embedded YouTube URL
        if (isset($parsedUrl['host']) && strpos($parsedUrl['host'], 'youtube.com') !== false && 
            isset($parsedUrl['path']) && strpos($parsedUrl['path'], '/embed/') === 0) {
            // Extract the video ID directly
            return $url; // Already in embedded format, return as is
        }
    
        // Handle short YouTube URL (e.g., youtu.be)
        if (isset($parsedUrl['host']) && strpos($parsedUrl['host'], 'youtu.be') !== false) {
            $videoId = ltrim($parsedUrl['path'], '/');
            return config('constants.embed_video_link').$videoId;
        }
    
        // Handle full YouTube URL (e.g., youtube.com/watch?v=video_id)
        if (isset($parsedUrl['host']) && strpos($parsedUrl['host'], 'youtube.com') !== false) {
            if (isset($parsedUrl['query'])) {
                parse_str($parsedUrl['query'], $queryParams);
                if (isset($queryParams['v'])) {
                    return config('constants.embed_video_link').$queryParams['v'];
                }
            }
        }
    
        // Return null if no valid YouTube video ID can be extracted
        return null;
    }
}

