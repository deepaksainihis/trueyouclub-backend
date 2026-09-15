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

        <tr>
            <tr>
                <td>
                    <p class="mail-title" style="font-size:14px;">
                        Dear {{ ucwords($name) }},
                    </p>
                    <div class="mail-desc">
                        <p style="font-size:14px;">
                            {{ $custom_message }}
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
                                    <img class="img-fluid" src="{{ $eventDetail['featured_image_url'] ? $eventDetail['featured_image_url'] : asset(config('constants.default.no_image')) }}" alt="{{ $eventDetail['title'] ?? '' }}">                                   
                                </div>
                                <div class="webinar-content">
                                <h3>
                                     {{ $eventDetail['title'] ?? '' }}
                                </h3> 
                                
                                <div class="date-time seminar-date d-flex">
                                    <div class="date_icon">
                                        <img class="img-fluid" src="{{ asset('images/icons/date.png') }}" alt="Date">
                                    </div>
                                    <span>
                                        {{ $eventDetail['formatted_date_time'] ?? ''}}
                                    </span>
                                </div>
                                </div>
                            </div>
                        </div>
                        <div class="ticketBooking-list">
                            {!! $eventDetail['description'] !!}
                            
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
    
    

@endsection
