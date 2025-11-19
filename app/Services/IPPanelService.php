<?php

namespace App\Services;

use App\Models\SmsLog;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class IPPanelService
{
    protected $apiKey;
    protected $baseUrl;
    protected $fromNumber;
    protected $patternCode;

    public function __construct()
    {
        $this->apiKey = config('services.ippanel.api_key');
        $this->baseUrl = config('services.ippanel.base_url');
        $this->fromNumber = config('services.ippanel.from_number');
        $this->patternCode = config('services.ippanel.pattern_code');
    }

    /**
     * Universal SMS sending function with logging
     *
     * @param string $type SMS type (webservice, pattern, verification, welcome)
     * @param string $mobile Recipient mobile number
     * @param string|null $message Message content (for webservice)
     * @param array $params Additional parameters
     * @return array ['success' => bool, 'message_id' => string|null, 'error' => string|null]
     */
    public function sendSMS(string $type, string $mobile, ?string $message = null, array $params = []): array
    {
        $formattedMobile = $this->formatMobileNumber($mobile);
        
        // Create log entry
        $smsLog = SmsLog::create([
            'type' => $type,
            'to_number' => $formattedMobile,
            'from_number' => $this->fromNumber,
            'message' => $message,
            'params' => $params,
            'status' => 'pending',
            'user_id' => Auth::id(),
        ]);

        try {
            // Prepare request data based on type
            $requestData = $this->prepareRequestData($type, $formattedMobile, $message, $params);
            
            // Send SMS via IPPanel API
            $response = Http::withHeaders([
                'Authorization' => $this->apiKey,
                'Content-Type' => 'application/json',
            ])->post($this->baseUrl . '/api/send', $requestData);

            // Update log with response
            if ($response->successful()) {
                $data = $response->json();
                $status = $data['meta']['status'] ?? false;
                
                if ($status) {
                    $messageId = $data['data']['message_outbox_ids'][0] ?? null;
                    
                    $smsLog->update([
                        'status' => 'sent',
                        'response' => json_encode($data),
                        'message_outbox_id' => $messageId,
                    ]);

                    return [
                        'success' => true,
                        'message_id' => $messageId,
                        'error' => null,
                    ];
                }
            }

            // Handle failure
            $errorMessage = $response->json()['meta']['message'] ?? 'Unknown error';
            
            $smsLog->update([
                'status' => 'failed',
                'response' => $response->body(),
                'error_message' => $errorMessage,
            ]);

            Log::error('IPPanel SMS Error', [
                'type' => $type,
                'mobile' => $formattedMobile,
                'status' => $response->status(),
                'body' => $response->body(),
            ]);

            return [
                'success' => false,
                'message_id' => null,
                'error' => $errorMessage,
            ];

        } catch (\Exception $e) {
            $smsLog->update([
                'status' => 'failed',
                'error_message' => $e->getMessage(),
            ]);

            Log::error('IPPanel SMS Exception', [
                'type' => $type,
                'mobile' => $formattedMobile,
                'message' => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'message_id' => null,
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Prepare request data based on SMS type
     */
    protected function prepareRequestData(string $type, string $mobile, ?string $message, array $params): array
    {
        switch ($type) {
            case 'pattern':
                return [
                    'sending_type' => 'pattern',
                    'from_number' => $this->fromNumber,
                    'code' => $this->patternCode,
                    'recipients' => [$mobile],
                    'params' => $params,
                ];

            case 'webservice':
            case 'verification':
            case 'welcome':
            default:
                return [
                    'sending_type' => 'webservice',
                    'from_number' => $this->fromNumber,
                    'message' => $message,
                    'params' => [
                        'recipients' => [$mobile],
                    ],
                ];
        }
    }

    /**
     * Send pattern SMS
     *
     * @param string $mobile Recipient mobile number
     * @param array $params Pattern parameters
     * @return bool
     */
    public function sendPattern(string $mobile, array $params): bool
    {
        $result = $this->sendSMS('pattern', $mobile, null, $params);
        return $result['success'];
    }

    /**
     * Send verification code SMS
     *
     * @param string $mobile
     * @param string $code
     * @return bool
     */
    public function sendVerificationCode(string $mobile, string $code): bool
    {
        $result = $this->sendSMS('verification', $mobile, null, ['code' => $code]);
        return $result['success'];
    }

    /**
     * Send welcome SMS with user credentials
     *
     * @param string $mobile
     * @param string $name
     * @param string $password
     * @return bool
     */
    public function sendWelcomeSMS(string $mobile, string $name, string $password): bool
    {
        $message = "کارمانیا توسعه\n\n";
        $message .= "نام: {$name}\n";
        $message .= "موبایل: {$mobile}\n";
        $message .= "رمز عبور: {$password}\n\n";
        $message .= "لطفاً رمز عبور خود را پس از ورود تغییر دهید.";

        $result = $this->sendSMS('welcome', $mobile, $message);
        return $result['success'];
    }

    /**
     * Format mobile number to E.164 format
     *
     * @param string $mobile
     * @return string
     */
    protected function formatMobileNumber(string $mobile): string
    {
        // Remove any spaces or dashes
        $mobile = preg_replace('/[\s\-]/', '', $mobile);
        
        // If starts with 0, replace with +98
        if (substr($mobile, 0, 1) === '0') {
            return '+98' . substr($mobile, 1);
        }
        
        // If starts with 98, add +
        if (substr($mobile, 0, 2) === '98') {
            return '+' . $mobile;
        }
        
        // If already has +, return as is
        if (substr($mobile, 0, 1) === '+') {
            return $mobile;
        }
        
        // Default: assume it's without country code
        return '+98' . $mobile;
    }
}
