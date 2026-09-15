@extends('emails.layouts.admin')

@section('email-content')
    @php
        $mailContent  = getSetting('welcome_mail_content');
        $mailContent  = str_replace('[NAME]',ucwords($name),$mailContent);
        $mailContent  = str_replace('[EMAIL]',ucwords($email),$mailContent);
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
                    <p style="font-size:14px;">Congratulations and welcome to {{ config('app.name') }}! We are thrilled to have you on board and look forward to your success in our program. This email serves as a confirmation of your successful registration.
                    </p>
                    <p style="font-size:14px;">Here are some key details to help you get started:</p>
                    <div class="mail-desc">
                        <ul>
                            <li style="font-size:14px;">Email : {{ $email }}</li>
                            @if(!is_null($password))
                            <li style="font-size:14px;">Password : {{ $password }}</li>
                            @endif
                        </ul>
                    </div>
                </div>
            </td>
        
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
