<?php

namespace App\Services;
 
use Pushok\AuthProvider\Token;
use Pushok\Client;
use Pushok\Notification;
use Pushok\Payload;
use Pushok\Payload\Alert;


class IOSPushService
{
     public static function sendNotification($deviceToken, $title, $body)
    {
        $pemFilePath =  storage_path('app/certs/SMESummit2025.pem');
        $pemFilePath =  asset('SMESummit2025.pem');
        // Your passphrase (if any)
        $passphrase = ''; 
        $alert = Alert::create()->setTitle($title)->setBody($body);
        $payload = Payload::create()->setAlert($alert);
        $payload->setSound('default');
        $payload->setBadge(1);
 
        $notification = new Notification($payload, $deviceToken);
        $client = new Client(
            Token::create([
                'key_id' => '3Q22UA87BA',
                'team_id' => 'MCVPUT2HX2',
                'app_bundle_id' => 'com.canadianSME.app',
                'private_key_path' => $pemFilePath,
                'private_key_secret' => $passphrase,
            ]),
            Client::PRODUCTION_URL
        );

        $client->addNotification($notification);
        $responses = $client->push(); // Send notifications
 
        foreach ($responses as $response) {
            if ($response->getStatusCode() === 200) {
                info('APNs notification sent successfully');
            } else {
                info('APNs error: ' . $response->getReasonPhrase());
            }
        }
    }    
}
