<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use DocuSign\eSign\Client\ApiClient;
use DocuSign;

class DocusignController extends BaseController
{
    protected $first = 'hi myname is G-lalala';
    protected static $access_token;

    /**
     * 初見
     */
    public function index()
    {
        return view('index')->with([
            'first' => session()->get('ds_access_token')
        ]);
    }

    /**
     *
     */
    public function testTuckerEric()
    {
        $host = "https://demo.docusign.net/restapi";

        $client = new DocuSign\Rest\Client([
            'username'       => env('DS_CLIENT_USER_NAME'),
            'password'       => env('DS_CLIENT_PASSWORD'),
            'integrator_key' => env('DS_CLIENT_ID'),
            'host'           => $host
        ]);

        $envelopeOptions = $client->envelopes->createEnvelopeOptions([
            'set_merge_roles_on_draft' => true,
        ]);

        $img = base64_encode(file_get_contents(public_path().'/pdfs/document_1.pdf'));

        $envelopeDefinition = $client->envelopeDefinition([
            'status'         => 'sent',
            'email_subject'  => '[DocuSign PHP SDK] - 上手いこといってないなぁ',
            'template_id'    => env('DS_TEMPLATE_ID'),
            'template_roles' => [
                $client->templateRole(
                    ['email' 	=> env('DS_CLIENT_USER_NAME'),
                    'name'  	=> env('DS_CLIENT_USER_NAME'),
                    'role_name' => 'captain']
                ),
                $client->templateRole(
                    ['email' 	=> 'ironman@example.com',
                        'name'  	=> 'tony stark',
                        'role_name' => 'sub_user']
                ),
            ],
            'documents' => [
                $client->document([
                    'document_id' => '1',
                    'document_base64' => $img,
                    'name' => 'document_1.pdf'
                ])
            ]
        ]);

        $result = $client->envelopes->createEnvelope($envelopeDefinition,$envelopeOptions);

        var_dump($result);
    }

    public function testAddRecipient()
    {
        $host = "https://demo.docusign.net/restapi";

        $client = new DocuSign\Rest\Client([
            'username'       => env('DS_CLIENT_USER_NAME'),
            'password'       => env('DS_CLIENT_PASSWORD'),
            'integrator_key' => env('DS_CLIENT_ID'),
            'host'           => $host
        ]);

        $signer = $client->recipients([
            'signers' => [
                $client->signer([
                    "email"=>"spiderman@example.com",
                    "name"=>"Petar Parker",
                    "recipient_id"=>"6",
                    "routing_order"=>"4"
                ])
            ]
        ]);

        $result = $client->envelopes->createRecipient(
            '8b53fcd4-aeb0-489f-9933-5c176a6f363f',
            $signer
        );

        var_dump($result);exit();
    }
}
