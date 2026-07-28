<?php

namespace App\Services;

use Kreait\Firebase\Factory;
use Kreait\Firebase\Messaging\CloudMessage;

class FirebaseService
{
    protected $messaging;

    public function __construct()
    {
        // ប្រើផ្លូវទៅកាន់ File JSON ដែលបងបានដាក់ក្នុង storage/app/
        $factory = (new Factory)->withServiceAccount(storage_path('app/firebase-credentials.json'));
        $this->messaging = $factory->createMessaging();
    }

    public function sendNotification($fcmToken, $title, $body)
    {
        // 🌟 រៀបចំ Payload ថ្មីជាទម្រង់ Array ដើម្បីកំណត់កម្រិតបង្ហាញខ្ពស់ (Heads-up/Banner)
        $message = CloudMessage::fromArray([
            'token' => $fcmToken,
            'notification' => [
                'title' => $title,
                'body' => $body,
            ],
            // 🤖 ការកំណត់ពិសេសសម្រាប់ប្រព័ន្ធ Android
            'android' => [
                'priority' => 'high', // បង្ខំឱ្យបាញ់ផ្ញើទៅភ្លាមៗ ទោះទូរស័ព្ទស្ថិតក្នុង Doze Mode ក៏ដោយ
                'notification' => [
                    'channel_id' => 'high_importance_channel', // ⚠️ ត្រូវតែដូចបេះបិទទៅនឹង meta-data ក្នុង AndroidManifest.xml
                    'sound' => 'default',
                    'click_action' => 'FLUTTER_NOTIFICATION_CLICK', // ជំនួយដល់ការចុចលើសំបុត្រដើម្បីបើក App ឡើងវិញ
                ],
            ],
            // 🍎 ការកំណត់ពិសេសសម្រាប់ប្រព័ន្ធ iOS (ផ្ញើទៅជាមួយសំឡេង និងលេខ Badge)
            'apns' => [
                'payload' => [
                    'aps' => [
                        'sound' => 'default',
                        'badge' => 1,
                    ],
                ],
            ],
        ]);

        return $this->messaging->send($message);
    }
}