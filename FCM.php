<?php

class FCMNotifier {

    private $apiUrl = 'https://fcm.googleapis.com/fcm/send';
    private $apiKey; // Your FCM API key

    public function __construct($apiKey) {
        $this->apiKey = $apiKey;
    }

    /**
     * Send a push notification to a specific user.
     *
     * @param string $message The message to be sent.
     * @param string $token The FCM token of the target user.
     */
    public function sendToUser($message, $token) {
        $data = [
            'to' => $token,
            'notification' => ['body' => $message]
        ];

        $this->sendNotification($data);
    }

    /**
     * Send a push notification to a specific topic.
     *
     * @param string $topic The FCM topic to send the message to.
     * @param string $message The message to be sent.
     */
    public function sendToTopic($topic, $message) {
        $data = [
            'to' => '/topics/' . $topic,
            'notification' => ['body' => $message]
        ];

        $this->sendNotification($data);
    }

    /**
     * Send push notifications to multiple users.
     *
     * @param array $tokens An array of FCM tokens.
     * @param string $message The message to be sent.
     */
    public function sendToMultipleUsers($tokens, $message) {
        foreach ($tokens as $token) {
            $this->sendToUser($message, $token);
        }
    }

    /**
     * Internal method to send the actual HTTP request to FCM.
     *
     * @param array $data The data to be included in the FCM request.
     */
    private function sendNotification($data) {
        $headers = [
            'Authorization: key=' . $this->apiKey,
            'Content-Type: application/json'
        ];

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->apiUrl);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));

        $response = curl_exec($ch);

        // Check for cURL errors or FCM response
        if (curl_errno($ch)) {
            echo 'FCM request failed: ' . curl_error($ch);
        } else {
            echo 'FCM response: ' . $response;
        }

        curl_close($ch);
    }
}

// Example usage:
$apiKey = 'your_fcm_api_key';
$notifier = new FCMNotifier($apiKey);

// Send to user
$notifier->sendToUser('Hello User!', 'user_fcm_token');

// Send to topic
$notifier->sendToTopic('news', 'Latest News Update');

// Send to multiple users
$tokens = ['token1', 'token2', 'token3'];
$notifier->sendToMultipleUsers($tokens, 'Greetings to multiple users!');
?>
