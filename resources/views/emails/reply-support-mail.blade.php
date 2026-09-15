@extends('emails.layouts.admin')

 @section('email-content')
		<p class="mail-title">
			Hello {{ ucwords($name) }},
		</p>
		<div class="mail-desc">
            <p style="margin-bottom: 0;font-weight: normal;">{!! $reply ?? '' !!}</p>
		</div>
@endsection

{{-- @section('email-content')

    <h4 style="font-family: 'Barlow', sans-serif; color: #464B70; font-weight: 700; font-size: 18px;margin-top: 0;">Hello {{ $name ?? "" }}</h4>


    <p style="font-size: 18px; line-height: 25.5px; font-weight: 600; font-family: 'Nunito Sans', sans-serif; color: #464B70; margin-bottom: 27px; margin-top:27px;">If you did not create an account, no further action is required.</p>

    <div class="regards" style="font-family: 'Barlow', sans-serif; color: #464B70; font-weight: 700; font-size: 18px;">Regards,<br><br><br> {{ config('app.name') }}</div>

@endsection --}}

