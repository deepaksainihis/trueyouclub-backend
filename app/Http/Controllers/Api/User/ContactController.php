<?php

namespace App\Http\Controllers\Api\User;

use App\Models\Contact;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Http\Controllers\Controller;
use App\Rules\ValidEmail;


class ContactController extends Controller
{

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'first_name'    => 'required',
            'last_name'     => 'required',
            'email'         => ['required','email:dns',new ValidEmail],
            'phone_number'  => 'required|numeric|regex:/^[0-9]{7,15}$/|not_in:-',
            'message'       => 'required',
        ], [
            'first_name.required'   => 'This field is required',
            'last_name.required'    => 'This field is required',
            'email.required'        => 'This field is required',
            'phone_number.required' => 'This field is required',
            'phone_number.regex'    => 'The phone number length must be 7 to 15 digits.',
            'message.required'      => 'This field is required',
        ]);

        DB::beginTransaction();
        try {
            $storeRecords = [
                'first_name'    => $request->first_name,
                'last_name'     => $request->last_name,
                'name'          => $request->first_name . ' ' . $request->last_name,
                'phone_number'  => $request->phone_number,
                'email'         => $request->email,
                'message'       => $request->message,
            ];

            $contactData = Contact::create($storeRecords);

            DB::commit();

            $responseData = [
                'status'        => true,
                'message'       => "Thank you! Your message has been successfully sent.",
            ];
            return response()->json($responseData, 200);
        } catch (\Exception $e) {
            DB::rollBack();
            //  dd($e->getMessage().'->'.$e->getLine());

            //Return Error Response
            $responseData = [
                'status'        => false,
                'error'         => trans('messages.error_message'),
            ];
            return response()->json($responseData, 400);
        }
    }
}
