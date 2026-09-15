@extends('emails.layouts.admin')

@section('email-content')
    @php
        $mailContent  = getSetting('passcode_mail_content');
        $mailContent  = str_replace('[NAME]',ucwords($name),$mailContent);
        $mailContent  = str_replace('[GOLDEN_GATEWAY_CODE]',ucwords($passcode),$mailContent);
        $mailContent  = str_replace('[SITE_URL]',config('constants.front_end_url').'register',$mailContent);
        $mailContent  = str_replace('[SUPPORT_EMAIL]', getSetting('support_email'), $mailContent);
        $mailContent  = str_replace('[SUPPORT_PHONE]', getSetting('support_phone'), $mailContent);
        $mailContent  = str_replace('[APP_NAME]', config('app.name'), $mailContent);
    @endphp

    @if($mailContent)
         {!! $mailContent !!}
    @else

        <tr>
            <td>
                <p class="mail-title" style="font-size:14px;">
                    Dear {{ ucwords($name) }},
                </p>
                <div class="mail-desc">
                    <p style="font-size:14px;">
                        Thank you for attending the event I’m glad you’ve enjoyed it!
                    </p>

                    <p style="font-size:14px;">As a token of our appreciation, we are delighted to offer you exclusive access to our membership program with the “Golden Gateway Code.”</p>
                    <div class="mail-desc">
                        <p style="font-size:14px;"><span style="width:100%;box-sizing: border-box;line-height: 1.5em;margin-top: 0;margin: 0;background-color: #212529;padding: 9px 10px;text-align: center;font-size: 14px;font-weight: 400;font-family: 'Nunito Sans',sans-serif;color: #fff;display: inline-block;">{{ $passcode }}</span></p>
                    </div>
                </div>
            </td>
        
            <tr>
                <td>
                    <p style="font-size:14px;">
                       Simply input the Golden Gateway Code during signup, and embrace this novel journey with {{ config('app.name') }}.
                    </p>
                    <p style="font-size:14px;">Url : <a href="{{ config('constants.front_end_url').'register' }}">{{ config('constants.front_end_url').'register' }}</a></p>
                </td>
            </tr>
            
            <tr>
                <td>
                    <p style="font-size:14px;">
                        If you have any questions or need assistance, our support team is always here to help. Don't hesitate to reach out by replying to this email or visiting our support section on the website.
                        please contact us at {{ getSetting('support_email') ? getSetting('support_email') : config('constants.support_email') }} or {{ getSetting('support_phone') ? getSetting('support_phone') : config('constants.support_phone') }}
                    </p>

                </td>
            </tr>

            <tr>
                <td>
                    <p style="font-size:14px;">Best regards,</p>
                    <p style="font-size:14px;">{{ config('app.name') }}</p>
                </td>
            </tr>
        </tr>
    
    @endif

@endsection
