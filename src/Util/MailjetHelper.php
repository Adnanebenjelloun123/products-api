<?php

/**
 * Created by PhpStorm.
 * Author: Abdeljabar Taoufikallah
 * Company: Share Conseil
 * Date: 12/17/21
 * Time: 4:00 PM
 */

namespace App\Util;

use Mailjet\Client;
use Mailjet\Resources;

class MailjetHelper
{
    private $mailjetClient;
    private $fromName;
    private $fromEmail;

    public function __construct($key, $secret, $fromName, $fromEmail)
    {
        //dd([$key, $secret]);
        $this->mailjetClient = new Client($key, $secret, true, ['version' => 'v3.1']);
        $this->fromName = $fromName;
        $this->fromEmail = $fromEmail;
    }

    public function send($to, $subject, $message) {
        $body = [
            'Messages' => [
                [
                    'From' => [
                        'Email' => $this->fromEmail,
                        'Name' => $this->fromName
                    ],
                    'To' => [
                        [
                            'Email' => $to['email'],
                            'Name' => $to['name']
                        ]
                    ],
                    'Subject' => $subject,
                    'HTMLPart' => $message['html'],
                    'TextPart' => $message['text']
                ]
            ]
        ];

        try {
            $response = $this->mailjetClient->post(Resources::$Email, [
                'body' => $body
            ]);

            return $response;
        } catch (\Exception $exception) {
            return null;
        }
    }

    public function sendMultiple($tos, $subject, $message) {
        $body = ['Messages' => [
            [
                'From' => [
                    'Email' => $this->fromEmail,
                    'Name' => $this->fromName
                ],
                'To' => $tos,
                'Subject' => $subject,
                'HTMLPart' => $message['html'],
                'TextPart' => $message['text']
            ]
        ]];

        $response = $this->mailjetClient->post(Resources::$Email, [
            'body' => $body
        ]);

        return $response;
    }

    public function sendBatch($messages) {
        $response = $this->mailjetClient->post(Resources::$Email, [
            'body' => [
                'Messages' => $messages
            ]
        ]);

        return $response;
    }

}