<?php

namespace App\Services;

class IOSPushService
{
    protected $apnsHost;
    protected $apnsPort = 2195;
    protected $apnsCert;
    protected $apnsPassphrase;

    public function __construct()
    {
        // $this->apnsHost = config('app.env') === 'production'
        //     ? 'gateway.push.apple.com'        // Live
        //     : 'gateway.sandbox.push.apple.com'; // Dev/Sandbox
        $this->apnsHost  =  'gateway.push.apple.com';
        $this->apnsCert = asset('SMESummit2025.pem');
        $this->apnsPassphrase = env('IOS_PUSH_PASSPHRASE', '');
    }

    public function send($deviceToken, $title, $message)
    {
        $payload = [
            'aps' => [
                'alert' => [
                    'title' => $title,
                    'body'  => $message,
                ],
                'sound' => 'default',
                'badge' => 1,
            ],
        ];

        $body = json_encode($payload);

        $ctx = stream_context_create();
        stream_context_set_option($ctx, 'ssl', 'local_cert', $this->apnsCert);
        if (!empty($this->apnsPassphrase)) {
            stream_context_set_option($ctx, 'ssl', 'passphrase', $this->apnsPassphrase);
        }

        $fp = stream_socket_client(
            'ssl://' . $this->apnsHost . ':' . $this->apnsPort,
            $err,
            $errstr,
            60,
            STREAM_CLIENT_CONNECT | STREAM_CLIENT_PERSISTENT,
            $ctx
        );

        if (!$fp) {
            \Log::error("APNs connection failed: $err $errstr");
            return false;
        }

        $msg = chr(0) . pack('n', 32) . pack('H*', $deviceToken) . pack('n', strlen($body)) . $body;

        $result = fwrite($fp, $msg, strlen($msg));
        fclose($fp);

        return $result ? true : false;
    }
}
