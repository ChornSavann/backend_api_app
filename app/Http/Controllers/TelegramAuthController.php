<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User; 
use Illuminate\Support\Str;

class TelegramAuthController extends Controller
{
    // public function verifyAndLogin(Request $request)
    // {
    //     $request->validate([
    //         'initData' => 'required|string',
    //     ]);

    //     $initData = $request->input('initData');
    //     $botToken = env('TELEGRAM_BOT_TOKEN');

    //     // 1. ផ្ទៀងផ្ទាត់ความถูกต้องຂອງ initData ជាមួយ Bot Token
    //     if (!$this->validateTelegramInitData($initData, $botToken)) {
    //         return response()->json([
    //             'status' => false,
    //             'message' => 'Unauthorized: Invalid Telegram Data'
    //         ], 401);
    //     }

    //     // 2. Parse ទិន្នន័យ user ពី initData string
    //     parse_str($initData, $parsedData);
    //     if (!isset($parsedData['user'])) {
    //         return response()->json([
    //             'status' => false,
    //             'message' => 'User data not found in initData'
    //         ], 400);
    //     }

    //     $userData = json_decode(urldecode($parsedData['user']), true);
    //     $telegramId = $userData['id'] ?? null;
    //     $firstName = $userData['first_name'] ?? '';
    //     $lastName = $userData['last_name'] ?? '';
    //     $username = $userData['username'] ?? 'user_' . $telegramId;

    //     if (!$telegramId) {
    //         return response()->json([
    //             'status' => false,
    //             'message' => 'Invalid Telegram ID'
    //         ], 400);
    //     }

    //     // 3. ស្វែងរក ឬបង្កើតសមាជិកថ្មីក្នុង Database ស្វ័យប្រវត្តិ
    //     $customer = User::firstOrCreate(
    //         ['telegram_id' => $telegramId],
    //         [
    //             'name' => trim("$firstName $lastName"),
    //             'username' => $username,
    //             'membership_no' => 'MEM-' . strtoupper(Str::random(6)), // បង្កើតលេខកូដសមាជិកស្វ័យប្រវត្តិ
    //             'password' => bcrypt(Str::random(16)), // Random password ព្រោះ Login ຜ່ານ Telegram
    //             'role_id' => 2, // 🎯 ຕາມគោលការណ៍ដែលបងធ្លាប់កំណត់ role_id = 2 សម្រាប់ external registration
    //             'points' => 0,
    //         ]
    //     );

    //     // 4. បង្កើត API Token (Laravel Sanctum) សម្រាប់ប្រើប្រាស់បន្ត
    //     $token = $customer->createToken('TelegramMiniAppToken')->plainTextToken;

    //     return response()->json([
    //         'status' => true,
    //         'message' => 'Login ជាមួយ Telegram ជោគជ័យ',
    //         'token' => $token,
    //         'data' => [
    //             'id' => $customer->id,
    //             'name' => $customer->name,
    //             'membership_no' => $customer->membership_no,
    //             'points' => $customer->points,
    //         ]
    //     ]);
    // }
public function verifyAndLogin(Request $request)
{
    $request->validate([
        'initData' => 'required|string',
    ]);

    $initData = $request->input('initData');
    $botToken = env('TELEGRAM_BOT_TOKEN');

    $telegramId = null;
    $firstName = 'Test';
    $lastName = 'User';
    $username = 'test_user';

    // 🛠️ BYPASS សម្រាប់ Test លើ Postman (ຖ້າ initData មានពាក្យว่า "test_mode" ຫລือ "Postman")
    if (str_contains($initData, 'test_mode') || str_contains($initData, 'AAH...')) {
        $telegramId = '999999999'; // Telegram ID ក្លែងក្លាយសម្រាប់ Test
        $firstName = 'Chorn';
        $lastName = 'Savann';
        $username = 'chorn_savann';
    } else {
        // 1. ផ្ទៀងផ្ទាត់ความถูกต้องຂອງ initData ជាមួយ Bot Token តាមស្តង់ដារពិតប្រាកដ
        if (!$this->validateTelegramInitData($initData, $botToken)) {
            return response()->json([
                'status' => false,
                'message' => 'Unauthorized: Invalid Telegram Data'
            ], 401);
        }

        // 2. Parse ទិន្នន័យ user ពី initData string ធម្មតា
        parse_str($initData, $parsedData);
        if (!isset($parsedData['user'])) {
            return response()->json([
                'status' => false,
                'message' => 'User data not found in initData'
            ], 400);
        }

        $userData = json_decode(urldecode($parsedData['user']), true);
        $telegramId = $userData['id'] ?? null;
        $firstName = $userData['first_name'] ?? '';
        $lastName = $userData['last_name'] ?? '';
        $username = $userData['username'] ?? 'user_' . $telegramId;
    }

    if (!$telegramId) {
        return response()->json([
            'status' => false,
            'message' => 'Invalid Telegram ID'
        ], 400);
    }

    // 3. ស្វែងរក ឬបង្កើតសមាជិកថ្មីក្នុង Database ស្វ័យប្រវត្តិ
    $customer = User::firstOrCreate(
        ['telegram_id' => $telegramId],
        [
            'name' => trim("$firstName $lastName"),
            'username' => $username,
            'membership_no' => 'MEM-' . strtoupper(Str::random(6)),
            'password' => bcrypt(Str::random(16)),
            'role_id' => 2,
            'points' => 1250, // ដាក់ពិន្ទុសម្រាប់តេស្ត
        ]
    );

    // 4. បង្កើត API Token (Laravel Sanctum)
    $token = $customer->createToken('TelegramMiniAppToken')->plainTextToken;

    return response()->json([
        'status' => true,
        'message' => 'Login ជាមួយ Telegram ជោគជ័យ (Test Mode)',
        'token' => $token,
        'data' => [
            'id' => $customer->id,
            'name' => $customer->name,
            'membership_no' => $customer->membership_no,
            'points' => $customer->points,
        ]
    ]);
}
    /**
     * មុខងារសម្រាប់ Hash ផ្ទៀងផ្ទាត់ initData តាមស្តង់ដារ Telegram Security
     */
    private function validateTelegramInitData(string $initData, string $token): bool
    {
        parse_str($initData, $params);
        if (!isset($params['hash'])) {
            return false;
        }

        $hash = $params['hash'];
        unset($params['hash']);
        
        // តម្រៀប Key តាមអក្ខរក្រម (Alphabetical order)
        ksort($params);
        $dataCheckString = '';
        foreach ($params as $key => $value) {
            $dataCheckString .= "$key=$value\n";
        }
        $dataCheckString = rtrim($dataCheckString, "\n");

        $secretKey = hash_hmac('sha256', $token, 'WebAppData', true);
        $calculatedHash = hash_hmac('sha256', $dataCheckString, $secretKey);

        return hash_equals($calculatedHash, $hash);
    }
}