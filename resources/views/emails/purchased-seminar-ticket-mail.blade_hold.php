@extends('emails.layouts.admin')
<style>
body{
    color: #0A2540;
}

.webinar-item {
    border-radius: 10px;
    border: 1px solid #DDDDE9;
    background: #ffffff;
    padding: 30px 30px 40px;
    margin-bottom: 40px;
    position: relative;
}

.ticket-wrapper {
    max-width: 546px;
    margin: 0 auto;
    background-color: #F5F7F9;
    padding: 1rem;
    border-radius: 10px;
}   

.ticket-wrapper .webinar-item {
    border: none;
    padding: 10px;
    margin-bottom: 0;
}

.ticket-wrapper .webinar-item-inner>.webinar-img {
    width: 100%;
    max-width: 156px;
    height: 110px;
    border-radius: 8px;
    overflow: hidden;
}

.ticket-wrapper .webinar-item-inner>.webinar-img>img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.ticket-wrapper .webinar-item-inner .webinar-content {
    width: inherit;
    margin-left:15px;
    color:#0A2540;
    padding: 5% 0;
}

.ticket-wrapper .webinar-content h3 {
    margin-bottom: 7px;
    font-size: 16px;
    font-weight: 600;
    line-height: normal;
}

.ticket-wrapper .webinar-item-inner {
    display: flex;
    align-items: center;
    gap: 15px;
    position: relative;
}

.ticket-wrapper .seminar-date{
    display:flex;
}

.ticket-wrapper .seminar-date svg path {
    fill: #0A2540;
    stroke: none;
}
.ticketBooking-list ul {
    padding: 0 !important;
    list-style: none;
}

.ticketBooking-list ul li {
    margin:0 !important;
    padding: 21px 0;
    border-bottom: 1px solid #DDDDE9;
}

.ticketBooking-inner {
    display: flex;
    align-items: center;
}

.ticketBooking-list ul li .ticketBooking-inner{
    color: #0A2540 !important;
}
.ticketBooking-list ul li .ticketBooking-inner span {
    font-size: 14px;
    font-style: normal;
    font-weight: 300;
    width: 120px;
}

.ticketBooking-list ul li .ticketBooking-inner b{
    font-size: 14px;
    font-weight: 600;
}

.ticketBooking-inner .space {
    margin-right: 15px;
}

.total-box {
    padding: 20px;
    border-radius: 5px;
    background-color: #ffffff;
    display: flex;
    align-items: center;
    justify-content: space-between;
}

.total-box .title {
    color: #0A2540;
    text-align: left;
    font-size: 16px;
    font-weight: 500;
}

.total-box .total-amount {
    color: #23D21F;
    text-align: right;
    font-size: 20px;
    font-weight: 600;
}

.date_icon{
    width:20px;
    height:15px;
}
</style>
@section('email-content')
    @php
        $mailContent  = null;

        $mailContent  = getSetting('booked_seminar_mail_content');
        
        $mailContent  = str_replace('[NAME]',ucwords($name),$mailContent);

        $mailContent  = str_replace('[BOOKING_NUMBER]',$bookingNumber,$mailContent);
        $mailContent  = str_replace('[SEMINAR_TITLE]',ucwords($seminar->title),$mailContent);
        $mailContent  = str_replace('[SEMINAR_DATE]',convertDateTimeFormat($seminar->start_date,'fulldate'),$mailContent);

        $mailContent  = str_replace('[SEMINAR_START_TIME]',\Carbon\Carbon::parse($seminar->start_time)->format('h:i A'),$mailContent);
        $mailContent  = str_replace('[SEMINAR_END_TIME]',\Carbon\Carbon::parse($seminar->end_time)->format('h:i A'),$mailContent);

        $mailContent  = str_replace('[SEMINAR_VENUE]',ucwords($seminar->venue),$mailContent);


        $mailContent  = str_replace('[SUPPORT_EMAIL]', getSetting('support_email'), $mailContent);
        $mailContent  = str_replace('[SUPPORT_PHONE]', getSetting('support_phone'), $mailContent);

        $mailContent  = str_replace('[APP_NAME]', config('app.name'), $mailContent);

    @endphp

    @if($mailContent)
         {!! $mailContent !!}
    @else

        <tr>
            <tr>
                <td>
                    <p class="mail-title" style="font-size:14px;">
                        Hello <b>{{ ucwords($name) }}</b>,
                    </p>
                    <div class="mail-desc">
                        <p style="font-size:14px;">Congratulations! Your recent purchase of seminar tickets has been successfully completed. We're thrilled to have you join us at {{$seminar->title ?? null}}!
                        </p>
                    </div>
                </td>
            </tr>

            <tr>
                <td style="padding:1rem 0;">
                    <div class="ticket-wrapper">
                        <div class="webinar-item">
                            <div class="webinar-item-inner">
                                <div class="webinar-img">
                                    <img class="img-fluid" src="{{ $seminar->image_url ? $seminar->image_url : asset(config('constants.default.no_image')) }}"" alt="{{$seminar->title ?? '' }}">
                                </div>
                                <div class="webinar-content">
                                <h3>
                                     {{ ucwords($seminar->title) }}
                                </h3>
                                <div class="date-time seminar-date d-flex">
                                    <div class="date_icon">
                                        <img class="img-fluid" src="{{ asset('images/icons/date.png') }}" alt="Date">
                                    </div>
                                    <span>
                                        {{ convertDateTimeFormat($seminar->start_date,'fulldate') }}
                                    </span>
                                </div>
                                </div>
                            </div>
                        </div>
                        <div class="ticketBooking-list">
                            <ul>
                                <li>
                                    <div class="ticketBooking-inner"><span>Name</span><div class="space">:</div><b>{{ ucwords($name) }}</b></div>
                                </li>
                                <li>
                                    <div class="ticketBooking-inner"><span>Booking Number</span><div class="space">:</div><b>{{ $bookingNumber }}</b></div>
                                </li>
                                <li>
                                    <div class="ticketBooking-inner"><span>Seminar Time</span><div class="space">:</div><b>{{ \Carbon\Carbon::parse($seminar->start_time)->format('h:i A') }} To {{ \Carbon\Carbon::parse($seminar->end_time)->format('h:i A') }}</b></div>
                                </li>
                                <li>
                                    <div class="ticketBooking-inner"><span>Seminar Venue</span><div class="space">:</div><b>{{ ucwords($seminar->venue) }}</b></div>
                                </li>
                            </ul>
                            <div class="total-box">
                                <table style="width: 100%;">
                                    <tbody>
                                        <tr>
                                            <td style="width: 49%;">
                                                <div class="title">
                                                    Total Price
                                                </div>
                                            </td>
                                            <td>
                                                <div>:</div>
                                            </td>
                                            <td style="width: 43%;">
                                                <div class="total-amount">
                                                    ${{ number_format($seminar->ticket_price,2) }}
                                                </div>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
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
