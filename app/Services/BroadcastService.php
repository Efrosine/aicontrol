<?php

namespace App\Services;

use App\Enums\BroadcastDataType;
use App\Models\BroadcastRecipient;
use App\Models\SenderNumber;
use App\Models\CctvDetectionResult;
use App\Models\SocialDetectionResult;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class BroadcastService
{
    /**
     * Send a broadcast message to all eligible recipients
     * 
     * @param SenderNumber $sender
     * @param BroadcastDataType $dataType
     * @param int $resultId
     * @return array
     */
    public function sendBroadcast(SenderNumber $sender, BroadcastDataType $dataType, int $resultId): array
    {
        // Get the detection result based on the data type
        $result = $this->getDetectionResult($dataType, $resultId);

        if (!$result) {
            return [
                'success' => false,
                'message' => 'Detection result not found',
            ];
        }

        // Get eligible recipients based on data type
        $recipients = $this->getEligibleRecipients($dataType);

        if ($recipients->isEmpty()) {
            return [
                'success' => false,
                'message' => 'No eligible recipients found',
            ];
        }

        // Prepare message content based on data type
        $messageContent = $this->prepareMessageContent($dataType, $result);

        // Send message to each recipient
        $successCount = 0;
        $failedCount = 0;
        $messageError = '';

        foreach ($recipients as $recipient) {
            $response = $this->sendWhatsAppMessage($sender, $recipient->phone_no, $messageContent);

            \Log::info('WhatsApp broadcast response', [
                'recipient' => $recipient->name,
                'phone' => $recipient->phone_no,
                'response' => $response,
            ]);
            if ($response['success']) {
                $successCount++;
            } else {
                $failedCount++;
                $messageError = $response['message'] ?? 'Unknown error';
                Log::error('Failed to send WhatsApp broadcast', [
                    'recipient' => $recipient->name,
                    'phone' => $recipient->phone_no,
                    'error' => $response['message'],
                ]);
            }
        }

        return [
            'success' => true,
            'message' => "Broadcast completed: {$successCount} successful, {$failedCount} failed, " . ($messageError ?: 'No errors'),
            'successCount' => $successCount,
            'failedCount' => $failedCount,
        ];
    }

    /**
     * Get the detection result based on data type
     * 
     * @param BroadcastDataType $dataType
     * @param int $resultId
     * @return mixed
     */
    protected function getDetectionResult(BroadcastDataType $dataType, int $resultId)
    {
        return match ($dataType) {
            BroadcastDataType::CCTV => CctvDetectionResult::find($resultId),
            BroadcastDataType::SOCIAL_MEDIA_SCRAPER => SocialDetectionResult::find($resultId),
        };
    }

    /**
     * Get eligible recipients based on data type
     * 
     * @param BroadcastDataType $dataType
     * @return \Illuminate\Database\Eloquent\Collection
     */
    protected function getEligibleRecipients(BroadcastDataType $dataType)
    {
        return match ($dataType) {
            BroadcastDataType::CCTV => BroadcastRecipient::where('receive_cctv', true)->get(),
            BroadcastDataType::SOCIAL_MEDIA_SCRAPER => BroadcastRecipient::where('receive_social', true)->get(),
        };
    }

    /**
     * Prepare message content based on data type and detection result
     * 
     * @param BroadcastDataType $dataType
     * @param mixed $result
     * @return string
     */
    protected function prepareMessageContent(BroadcastDataType $dataType, $result): string
    {
        // Format the message based on the data type
        $content = "AIControl Alert\n\n";

        if ($dataType === BroadcastDataType::CCTV) {
            $content .= "*CCTV Detection Alert*\n";
            $content .= "Time: " . $result->created_at->format('Y-m-d H:i:s') . "\n";
            $content .= "Camera: " . ($result->cctv->name ?? 'Unknown') . "\n";
            // Extract data from the result
            $data = is_array($result->data) ? $result->data : (json_decode($result->data, true) ?? []);

            $detection = $data['detection_type'] ?? 'Unknown';
            $confidence = isset($data['confidence']) ? number_format($data['confidence'] * 100, 2) : 0;

            $content .= "Detection: " . $detection . "\n";
            $content .= "Confidence: " . $confidence . "%\n";

            // Add camera details
            if (isset($result->cctv)) {
                $content .= "Camera: " . $result->cctv->name . "\n";
                $content .= "Location: " . ($result->cctv->location ?? 'Unknown') . "\n";
            }

            // Add image URL if available
            if (!empty($result->snapshoot_url)) {
                $content .= "Image: " . $result->snapshoot_url . "\n";
            } elseif (!empty($data['snapshoot_url'])) {
                $content .= "Image: " . $data['snapshoot_url'] . "\n";
            }
        } else {
            $content .= "*Social Media Alert*\n";
            $content .= "Time: " . $result->created_at->format('Y-m-d H:i:s') . "\n";
            // Extract data from the result
            $data = is_array($result->data) ? $result->data : (json_decode($result->data, true) ?? []);

            $platform = $data['platform'] ?? 'Unknown';
            $accountName = $data['account_name'] ?? 'Unknown';
            $content .= "Platform: " . $platform . "\n";
            $content .= "Account: " . $accountName . "\n";

            // Add detected keywords if available
            if (isset($data['keywords']) && !empty($data['keywords'])) {
                $keywordsStr = is_array($data['keywords']) ? implode(', ', $data['keywords']) : $data['keywords'];
                $content .= "Keywords: " . $keywordsStr . "\n";
            }

            if (isset($data['content'])) {
                $contentText = $data['content'];
                $content .= "Content: " . substr($contentText, 0, 100) . (strlen($contentText) > 100 ? '...' : '') . "\n";
            }

            // Add post URL if available
            if (!empty($data['post_url'])) {
                $content .= "URL: " . $data['post_url'] . "\n";
            } elseif (!empty($result->post_url)) {
                $content .= "URL: " . $result->post_url . "\n";
            }

            // Add detection score if available
            if (isset($data['score'])) {
                $score = is_numeric($data['score']) ? number_format($data['score'] * 100, 2) : $data['score'];
                $content .= "Detection Score: " . $score . "%\n";
            }
        }

        return $content;
    }

    /**
     * Send a WhatsApp message using the API
     * 
     * @param SenderNumber $sender
     * @param string $recipientPhone
     * @param string $messageContent
     * @return array
     */
    protected function sendWhatsAppMessage(SenderNumber $sender, string $recipientPhone, string $messageContent): array
    {
        try {
            $response = Http::withHeaders([
                'Content-Type' => 'application/json'
            ])->post('https://api.watzap.id/v1/send_message', [
                        'api_key' => $sender->api_key,
                        'number_key' => $sender->number_key,
                        'phone_no' => $recipientPhone,
                        'message' => $messageContent,
                    ]);

            \Log::notice('Watzap API response', [
                'recipient' => $recipientPhone,
                'sender' => $sender->number_key,
                'response_code' => $response->status(),
                'if successful' => $response->successful(),
                'response' => $response->body(),
            ]);

            $responseData = $response->json();

            if (
                $response->successful() &&
                !(
                    isset($responseData['status'], $responseData['message']) &&
                    $responseData['status'] === '1002' &&
                    $responseData['message'] === 'Invalid Secret Key'
                )
            ) {
                return [
                    'success' => true,
                    'message' => 'Message sent successfully',
                ];
            } else {
                return [
                    'success' => false,
                    'message' => 'Failed to send message: ' . $response->body(),
                ];
            }
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Error sending message: ' . $e->getMessage(),
            ];
        }
    }
}
