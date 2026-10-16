<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class OneSignalService
{
    protected $appId;
    protected $restKey;

    public function __construct()
    {
        $this->appId = "55deb21e-3e97-42ec-ae42-e263ea24cce0";//env('ONESIGNAL_APP_ID');
        $this->restKey = "os_v2_app_kxplehr6s5bozlsc4jr6ujgm4c3k642r5eyu6jn4lrf2u3cchu2vr4hnjuxir4exe5dr2nzympeyukrblhgocxwj2xzkhibj2cx4jta";//env('ONESIGNAL_REST_KEY');
    }

    private function sendNotification(array $fields)
    {
        try {
            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
                'Authorization' => 'Basic ' . $this->restKey,
            ])->post('https://onesignal.com/api/v1/notifications', $fields);

            return $response->json();
        } catch (\Exception $e) {
            return ['error' => 'Error sending notification: ' . $e->getMessage()];
        }
    }

    public function sendToUser($playerId, $title, $message, $data = [])
    {
        $fields = [
            'app_id' => $this->appId,
            'include_player_ids' => [$playerId],
            'data' => $data,
            'headings' => ['en' => $title],
            'contents' => ['en' => $message],
            'mutable_content' => true,
            'priority' => 10,
        ];

        return $this->sendNotification($fields);
    }

    public function sendToAll($title, $message, $data = [])
    {
        $fields = [
            'app_id' => $this->appId,
            'included_segments' => ['All'],
            'data' => $data,
            'headings' => ['en' => $title],
            'contents' => ['en' => $message],
            'mutable_content' => true,
            'priority' => 10,
        ];

        return $this->sendNotification($fields);
    }
}
