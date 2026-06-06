<?php

use App\Models\User;
use App\Models\IncompleteOrder;
use App\Http\Controllers\Api\LeadController;
use App\Http\Controllers\Admin\FraudCheckerController;
use App\Http\Middleware\CheckBlocked;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

// Clean up old test data if any
IncompleteOrder::where('phone', '01711223344')->delete();
User::where('phone', '01711223344')->delete();

echo "=== 1. Testing LeadController::store ===\n";
$request = Request::create('/api/v1/leads/save', 'POST', [
    'phone' => '01711223344',
    'name' => 'Test Lead Customer',
    'total' => 150.50,
    'device' => 'Mobile App',
    'os' => 'Android',
    'cart_items' => [['id' => 1, 'qty' => 2, 'product_type' => 'admin']]
]);

$controller = new LeadController();
$response = $controller->store($request);
$data = json_decode($response->getContent(), true);

if (isset($data['success']) && $data['success'] === true && isset($data['id'])) {
    echo "✔ Success! Lead saved with ID: " . $data['id'] . "\n";
    $lead = IncompleteOrder::find($data['id']);
    if ($lead && $lead->phone === '01711223344' && $lead->estimated_total == 150.50) {
        echo "✔ Lead data verified in database successfully.\n";
    } else {
        echo "✖ Lead data verification in database failed!\n";
    }
} else {
    echo "✖ LeadController::store failed: " . print_r($data, true) . "\n";
}

echo "\n=== 2. Testing FraudCheckerController::checkIpBlocked for blocked user ===\n";
// Create a blocked user
$blockedUser = User::create([
    'name' => 'Blocked Test User',
    'email' => 'blocked_test@example.com',
    'phone' => '01711223344',
    'password' => bcrypt('password123'),
    'role' => 'customer',
    'status' => 'active',
    'is_blocked' => true
]);

$fraudController = new FraudCheckerController();

// Test check by phone query param
$requestBlockedPhone = Request::create('/check-ip-blocked', 'GET', ['phone' => '01711223344']);
$responseBlockedPhone = $fraudController->checkIpBlocked($requestBlockedPhone);
$dataBlockedPhone = json_decode($responseBlockedPhone->getContent(), true);

if (isset($dataBlockedPhone['blocked']) && $dataBlockedPhone['blocked'] === true && isset($dataBlockedPhone['data']['location'])) {
    echo "✔ Success! Detected blocked user via phone query param. Location data: " . $dataBlockedPhone['data']['location'] . "\n";
} else {
    echo "✖ Failed to detect blocked user via phone query param: " . print_r($dataBlockedPhone, true) . "\n";
}

// Test check for unblocked phone
$requestUnblockedPhone = Request::create('/check-ip-blocked', 'GET', ['phone' => '01700000000']);
$responseUnblockedPhone = $fraudController->checkIpBlocked($requestUnblockedPhone);
$dataUnblockedPhone = json_decode($responseUnblockedPhone->getContent(), true);

if (isset($dataUnblockedPhone['blocked']) && $dataUnblockedPhone['blocked'] === false) {
    echo "✔ Success! Unblocked user via phone returned blocked=false.\n";
} else {
    echo "✖ Failed: Unblocked user phone returned blocked=true: " . print_r($dataUnblockedPhone, true) . "\n";
}

echo "\n=== 3. Testing CheckBlocked Middleware JSON Response ===\n";
// Log in the blocked user using Sanctum guard
auth('sanctum')->setUser($blockedUser);

$middleware = new CheckBlocked();
$midRequest = Request::create('/api/user/profile', 'GET');
$midRequest->headers->set('Accept', 'application/json');

$middlewareResponse = $middleware->handle($midRequest, function($req) {
    return response()->json(['success' => true, 'message' => 'Passed middleware']);
});

$midData = json_decode($middlewareResponse->getContent(), true);

if ($middlewareResponse->getStatusCode() === 403 && isset($midData['blocked']) && $midData['blocked'] === true) {
    echo "✔ Success! Middleware blocked request and returned 403 JSON response.\n";
} else {
    echo "✖ Middleware test failed! Status: " . $middlewareResponse->getStatusCode() . ", Content: " . $middlewareResponse->getContent() . "\n";
}

// Clean up
$blockedUser->delete();
IncompleteOrder::where('phone', '01711223344')->delete();
echo "=== Test Cleanup Done ===\n";
