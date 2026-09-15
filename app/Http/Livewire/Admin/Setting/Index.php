<?php

namespace App\Http\Livewire\Admin\Setting;

use Gate;
use Livewire\Component;
use App\Models\Setting;
use Illuminate\Support\Str;
use Illuminate\Support\Arr;
use Livewire\WithPagination;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\DB;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Symfony\Component\HttpFoundation\Response;


class Index extends Component
{
    use WithPagination, LivewireAlert, WithFileUploads;

    // protected $layout = null;

    public $tab = 'site', $settings = null, $state = [], $removeFile = [];

    protected $listeners = [
        'changeTab','copyTextAlert',
    ];

    public function mount(){
        abort_if(Gate::denies('setting_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $this->settings = Setting::where('group',$this->tab)->where('status',1)->get();

        $this->state = $this->settings->pluck('value','key')->toArray();

        $this->removeFile['remove_site_logo'] = false;
        $this->removeFile['remove_favicon'] = false;
        $this->removeFile['remove_footer_logo'] = false;
        $this->removeFile['remove_short_logo'] = false;
    }

    public function changeTab($tab){
        $this->tab = $tab;
        $this->mount();
        $this->initializePlugins();
    }


    public function render()
    {
        $allSettingType = Setting::groupBy('group')->where('status',1)->pluck('group');

        return view('livewire.admin.setting.index',compact('allSettingType'));
    }


    public function update(){

        $rules = [];
        $dimensionsDetails['site_logo']     = '';
        $dimensionsDetails['favicon']       = '';
        $dimensionsDetails['short_logo']    = '';
        $dimensionsDetails['footer_logo']   = '';

        foreach ($this->settings as $setting) {
            if($setting){

                if ($setting->type == 'text') {
                    $rules['state.'.$setting->key] = 'required|string';
                }

                if ($setting->type == 'number') {
                    $rules['state.'.$setting->key] = 'required|integer|numeric|min:0';
                }

                if ($setting->type == 'text_area') {
                    if($setting->group == 'mail'){
                        $textAreaValidation = ($setting->group != 'mail') ? '|nullable_strip_tags' : '';
                        $rules['state.'.$setting->key] = 'nullable'.$textAreaValidation;
                    }else{
                        $textAreaValidation = ($setting->group != 'mail') ? 'required|' : '';
                        $rules['state.'.$setting->key] = $textAreaValidation.'strip_tags';
                    }
                }

                if($setting->type == 'image' && (!$this->removeFile['remove_'.$setting->key])){
                    $dimensions = explode(' × ',$setting->details);
                    $dimensionsDetails[$setting->key] = $setting->details;

                    if(isset($dimensions[0]) && isset($dimensions[1])){
                        $rules['state.'.$setting->key] = 'nullable|image|dimensions:max_width='.$dimensions[0].',max_height='.$dimensions[1].'|max:'.config('constants.img_max_size').'|mimes:jpeg,png,jpg,svg,PNG,JPG,SVG|';
                    }else{
                        $rules['state.'.$setting->key] = 'nullable|image|max:'.config('constants.img_max_size').'|mimes:jpeg,png,jpg,svg,PNG,JPG,SVG|';
                    }
                }elseif($setting->type == 'image' && $this->removeFile['remove_'.$setting->key]){
                    $rules['state.'.$setting->key] = '';
                }
                
                if($setting->type == 'video' && (!$this->removeFile['remove_'.$setting->key])){
                    $rules['state.'.$setting->key] = 'nullable|max:'.config('constants.video_max_size').'|mimetypes:video/webm,video/mp4, video/avi,video/wmv,video/flv,video/mov';
                }elseif($setting->type == 'video' && $this->removeFile['remove_'.$setting->key]){
                    $rules['state.'.$setting->key] = '';
                }

                if ($setting->type == 'toggle') {
                    $rules['state.'.$setting->key] = 'required|in:'.$setting->details;
                }

            }
        }

        $customMessages = [
            'required' => 'The field is required.',
            'strip_tags'=>'The field is required.',
            'state.site_logo' => 'The logo must be an image.',
            'state.site_logo.mimes' => 'The logo must be jpeg,png,jpg,PNG,JPG.',
            'state.site_logo.max'   => 'The logo maximum size is '.config('constants.img_max_size').' KB.',
            'state.site_logo.dimensions'=> 'The logo size must be '.$dimensionsDetails['site_logo'],

            'state.favicon.dimensions'=> 'The favicon size must be '.$dimensionsDetails['favicon'],
            'state.short_logo.dimensions'=> 'The short logo size must be '.$dimensionsDetails['short_logo'],
            'state.footer_logo.dimensions'=> 'The footer logo size must be '.$dimensionsDetails['footer_logo'],


            'state.introduction_video.video' => 'The introduction video must be an video.',
            'state.introduction_video.mimes' => 'The introduction video must be webm, mp4, avi, wmv, flv, mov.',
            'state.introduction_video.max'   => 'The favicon icon maximum size is '.config('constants.video_max_size').' KB.',

            'state.welcome_mail_content.string'=> 'The welcome mail content must be a string.',
            'state.package_purchased_mail_content.string'=> 'The package purchased mail content must be a string.',
            'state.reset_password_mail_content.string'=> 'The reset password mail content must be a string.',
            'state.contact_us_mail_content.string'=> 'The contact us mail content must be a string.',

            'state.razorpay_status.in' => 'The selected razorpay status is invalid.',
            'state.max_skip_day.min' => 'The maximum skip day must be at least 0',

        ];

        $validatedData = $this->validate($rules,$customMessages);

        DB::beginTransaction();
        try {
            foreach($validatedData['state'] as $key=>$stateVal){
                $setting = Setting::where('key',$key)->first();

                $setting_value = !empty($stateVal) && !is_null($stateVal) ? $stateVal : null;

                if($setting->type == 'image'){

                    $uploadId = $setting->image ? $setting->image->id : null;

                    if ($stateVal && (!$this->removeFile['remove_'.$key])) {
                        if($uploadId){
                            uploadImage($setting, $stateVal, 'settings/images/',"setting", 'original', 'update', $uploadId);
                        }else{
                            uploadImage($setting, $stateVal, 'settings/images/',"setting", 'original', 'save', null);
                        }
                    }else{
                        if($uploadId && $this->removeFile['remove_'.$key]){
                            deleteFile($uploadId);
                        }
                    }

                    $setting_value = null;
                }

                if($setting->type == 'video'){

                    $uploadId = $setting->video ? $setting->video->id : null;
                    if ($stateVal && (!$this->removeFile['remove_'.$key])) {
                        if($uploadId){
                            uploadImage($setting, $stateVal, 'settings/videos/',"setting", 'original', 'update', $uploadId);
                        }else{
                            uploadImage($setting, $stateVal, 'settings/videos/',"setting", 'original', 'save', null);
                        }
                    }else{
                        if($uploadId && $this->removeFile['remove_'.$key]){
                            deleteFile($uploadId);
                        }
                    }

                    $setting_value = null;
                }

                if ($setting->type == 'text_area') {
                    if($setting->group == 'mail'){
                        $checkEmpty = trim(strip_tags($stateVal));
                        $setting_value = !empty($checkEmpty) ? $stateVal : null;
                    }
                }
                
                $setting->value = $setting_value;
                $setting->save();

                DB::commit();
            }

            // $this->reset(['state']);

            $this->alert('success',trans('messages.edit_success_message'));

        }catch (\Exception $e) {
            DB::rollBack();
            // dd($e->getMessage().'->'.$e->getLine());
            $this->alert('error',trans('messages.error_message'));
        }

    }


    public function initializePlugins(){
        $this->dispatchBrowserEvent('loadPlugins');
    }


    public function copyTextAlert(){
        $this->alert('success','Copied Successfully!');
    }
}
