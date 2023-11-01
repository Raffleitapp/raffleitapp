<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Stripe;

class RaffleController extends Controller
{
    public function generateRandomAlphanumeric($length = 10)
    {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $randomString = '';

        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, strlen($characters) - 1)];
        }

        return $randomString;
    }
    public function createRaffle(Request $req)
    {

        $image1 = '';
        $image2 = '';
        $image3 = '';
        $image4 = '';

        if ($req->has('image1')) {
            $file = $req->file('image1');
            $fileName = time() . $file->getClientOriginalName();;
            $file->storeAs('uploads', $fileName);
            $path = $file->store("public/images");
            $imageNames = basename($path);
            global $image1;
            $image1 = $imageNames;
        }

        if ($req->has('image2')) {
            $file = $req->file('image2');
            $fileName = time() . $file->getClientOriginalName();;
            $file->storeAs('uploads', $fileName);
            $path = $file->store("public/images");
            $imageNames = basename($path);
            global $image2;
            $image2 = $imageNames;
        }
        if ($req->has('image3')) {
            $file = $req->file('image3');
            $fileName = time() . $file->getClientOriginalName();;
            $file->storeAs('uploads', $fileName);
            $path = $file->store("public/images");
            $imageNames = basename($path);
            global $image3;
            $image3 = $imageNames;
        }

        if ($req->has('image4')) {
            $file = $req->file('image4');
            $fileName = time() . $file->getClientOriginalName();;
            $file->storeAs('uploads', $fileName);
            $path = $file->store("public/images");
            $imageNames = basename($path);
            global $image4;
            $image4 = $imageNames;
        }


        $link = $this->generateRandomAlphanumeric(10);

        $data = DB::table('raffle')->insertGetId([
            'user_id' => session()->get('user_id'),
            'organisation_id' => $req->organisation_id,
            'fundraising_id' => $req->fundraiser_id,
            'description' => $req->description,
            'host_name' => $req->host_name,
            'image1' => $image1,
            'image2' => $image2,
            'image3' => $image3,
            'image4' => $image4,
            'starting_date' => $req->starting_date,
            'ending_date' => $req->ending_date,
            'state_raffle_hosted' => $link,
            'approve_status' => 2
        ]);
        if ($data) {

            DB::table("ticket_price")->insert([
                'raffle_id' => $data,
                'three' => $req->three_ticket,
                'ten' => $req->ten_ticket,
                'twenty' => $req->twenty_ticket,
                'one_twenty' => $req->one_twenty,
                'two_hundred' => $req->two_hundred
            ]);
            return response()->json([
                'code' => 201,
                'message' => "Raffle created successfully"
            ]);
        } else {
            return response()->json([
                'code' => 405,
                'message' => "Unable to create raffle"
            ]);
        }
    }

    public function allRaffles()
    {
        $data =  DB::table("raffle")->where('approve_status', 1)
            ->leftJoin('organisation', 'raffle.organisation_id', 'organisation.id')
            ->select('raffle.*', 'organisation.organisation_name', 'organisation.cover_image', 'organisation.handle')
            ->paginate(7);
        return view('allraffle', compact('data'));
    }

    public function raffleDetails($id)
    {
        $data = DB::table("raffle")->where('state_raffle_hosted', $id)->first();
        if ($data) {
            return view('raffle-detail', compact('data'));
        }
    }




    //pay

    public function createStripeToken(Request $request){
        try {
            $validator = Validator::make($request->all(), [
                'card_email' => 'required',
                'card_no' => 'required',
                'month' => 'required',
                'year' => 'required',
                'card_cvv' => 'required',
            ]);
            if ($validator->fails()) {
                $response = [
                    'success' => false,
                    'message' => 'Validation Error.', $validator->errors(),
                    'status'=> 500
                ];
                return response()->json($response, 404);
            }
            // $payCreds = DB::table('payments')
            // ->select('*')->where('id',2)->first();
            // if (is_null($payCreds) || is_null($payCreds->creds)) {
            //     $response = [
            //         'success' => false,
            //         'message' => 'Payment issue please contact administrator',
            //         'status' => 404
            //     ];
            //     return response()->json($response, 404);
            // }
            // $credsData = json_decode($payCreds->creds);
            // if(is_null($credsData) || is_null($credsData->secret)){
            //     $response = [
            //         'success' => false,
            //         'message' => 'Payment issue please contact administrator',
            //         'status' => 404
            //     ];
            //     return response()->json($response, 404);
            // }
            $stripe = new \Stripe\StripeClient("sk_test_51HIb63EFkSOsWovihAgvTLfBDmMm4IAXuUvQ9PCeNdJhiNj8uRwpC34f8tX4QtKBIOTTkzyVECBwWRpWFilyZ38z00DUBhGP4o");
            $data = $stripe->tokens->create([
                'card' => [
                    'number' => $request->card_no,
                    'exp_month' => $request->month,
                    'exp_year' => $request->year,
                    'cvc' => $request->card_cvc,
                ],
            ]);
            dd($data);
            $response = [
                'success' => $data,
                'message' => 'success',
                'status' => 200
            ];
            return response()->json($response, 200);
        } catch (Exception $e) {
            return response()->json($e->getMessage(),200);
        }

    }

    public function handlePost(Request $request)
    {
        Stripe\Stripe::setApiKey(env('STRIPE_SECRET'));
        Stripe\Charge::create ([
                "amount" => 100 * 150,
                "currency" => "inr",
                "source" => $request->stripeToken,
                "description" => "Making test payment."
        ]);

        Session::flash('success', 'Payment has been successfully processed.');

        return back();


    }
}
