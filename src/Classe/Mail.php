<?php

namespace App\Classe;

use Mailjet\Client;
use Mailjet\Resources;

class Mail
{
    private $api_key = '06621ed986e278db52b4d7312d5587e5';
    private $api_key_secret = 'c4385b31a35082278778577bdc7cdda6';

    public function send($to_email, $to_name, $subject, $content)
    {

// Initialise un nouveau client Mailjet avec les options cURL
        $mj = new Client($this->api_key, $this->api_key_secret, true, ['version' => 'v3.1']);

        $body = [
            'Messages' => [
                [
                    'From' => [
                        'Email' => "bhayat@inforiel.fr",
                        'Name' => "David"
                    ],
                    'To' => [
                        [
                            'Email' => $to_email,
                            'Name' => $to_name
                        ]
                    ],
                    'TemplateID' => 4734886,
                    'TemplateLanguage' => true,
                    'Subject' => $subject,
                    'Variables'=> [
                        'content'=> $content,
                    ]
                ]
            ]
        ];
        $response = $mj->post(Resources::$Email, ['body' => $body]);
        $response->success();

    }

}
