<?php

namespace App\Http\Controllers\web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Enquiry;
use App\Helpers\Mailer;
use App\Http\Requests\EnquiryRequest;
use App\Http\Requests\EnquiryLiteRequest;
use App\Http\Requests\HelpRequest;
use App\Http\Requests\EnquiryPlannerRequest;

class EnquiryController extends Controller
{
    public function enquiry(EnquiryRequest $request){
        $data = $request->all();
        $response = [];
        $status = 200;


        $n = new Enquiry;
        $n->name = $data['name'];
        $n->email = $data['email'];
        $n->phone = $data['phone'];
        $n->service = empty($data['service']) ? '' : $data['service'];
        $n->description = empty($data['description']) ? '' : $data['description'];
        $n->save();



        $mail = Mailer::sendMail('Thank You for Contacting Us | DMS', array($data['email']), 'DMS', 'web.emails.response', $data);
        $mail = Mailer::sendMail('#'.$n->id.' - New Inquiry Received! | DMS', ['waseem@datamysite.com', 'pooja.u@datamysite.com', 'abhishek@datamysite.com'], 'DMS', 'web.emails.enquiry', $data);


        $response['success'] = 'success';
        $response['message'] = 'Thank you for your enquiry - We’ve received your enquiry and our team will contact you shortly.';



        return response()->json($response, 200);
    }

    public function enquiryLite(EnquiryLiteRequest $request){
        $data = $request->all();
        $response = [];
        $status = 200;


        $n = new Enquiry;
        $n->name = $data['name'];
        $n->email = $data['email'];
        $n->phone = $data['phone'];
        $n->service = empty($data['service']) ? '' : $data['service'];
        $n->description = empty($data['description']) ? '' : $data['description'];
        $n->save();



        $mail = Mailer::sendMail('Thank You for Contacting Us | DMS', array($data['email']), 'DMS', 'web.emails.response', $data);
        $mail = Mailer::sendMail('#'.$n->id.' - New Inquiry Received! | DMS', ['waseem@datamysite.com', 'pooja.u@datamysite.com', 'abhishek@datamysite.com'], 'DMS', 'web.emails.enquiry', $data);


        $response['success'] = 'success';
        $response['message'] = 'Thank you for your enquiry - We’ve received your enquiry and our team will contact you shortly.';



        return response()->json($response, 200);
    }

    /** Lead form of the service pages (outdoor advertising planner): enquiry + budget and preferred dates. */
    public function planner(EnquiryPlannerRequest $request){
        $data = $request->validated();
        $data['service'] = empty($data['service']) ? 'Outdoor Advertising' : $data['service'];
        $data['budget'] = $data['budget'] ?? '';
        $data['dates'] = $data['dates'] ?? '';

        $n = new Enquiry;
        $n->name = $data['name'];
        $n->email = $data['email'];
        $n->phone = $data['phone'];
        $n->service = $data['service'];
        $n->description = $data['description'];
        $n->budget = $data['budget'] === '' ? null : $data['budget'];
        $n->preferred_dates = $data['dates'] === '' ? null : $data['dates'];
        $n->save();

        $confirmed = Mailer::sendMail('Thank You for Contacting Us | DMS', array($data['email']), 'DMS', 'web.emails.response', $data);
        Mailer::sendMail('#'.$n->id.' - New Inquiry Received! | DMS', ['waseem@datamysite.com', 'pooja.u@datamysite.com', 'abhishek@datamysite.com'], 'DMS', 'web.emails.enquiry', $data);

        // reference + whether the visitor's confirmation email really went out: the thank-you popup shows both
        return response()->json([
            'success' => 'success',
            'reference' => $n->id,
            'emailed' => $confirmed,
            'message' => 'Thank you. A planner will reply within 60 minutes with available sites and prices.',
        ], 200);
    }

    public function enquiryHelp(HelpRequest $request){
        $data = $request->all();
        $response = [];
        $status = 200;

        $recep = ['waseem@datamysite.com', 'pooja.u@datamysite.com', 'abhishek@datamysite.com'];

        $n = new Enquiry;
        $n->name = $data['name'];
        $n->email = $data['email'];
        $n->phone = $data['phone'];
        $n->service = empty($data['service']) ? '' : $data['service'];
        $n->website_link = empty($data['website_link']) ? '' : $data['website_link'];
        $n->save();

        if(!empty($recep)){
            array_push($recep,"seodms@datamysite.com");
        }


        $mail = Mailer::sendMail('Thank You for Contacting Us | DMS', array($data['email']), 'DMS', 'web.emails.response', $data);
        $mail = Mailer::sendMail('#'.$n->id.' - New Inquiry Received! | DMS', $recep, 'DMS', 'web.emails.enquiry', $data);


        $response['success'] = 'success';
        $response['message'] = 'Thank you - we’ve received your request and our team will contact you shortly.';



        return response()->json($response, 200);
    }
}
