<?php
require __DIR__ . '/../vendor/autoload.php';

$middleware = new \App\Http\Middleware\NormalizeImageUrls();
$request = \Illuminate\Http\Request::create('/api/home-data', 'GET');
$json = '{"image":"/uploads/banner/1781265403_6a2bf3fbc0f81.png?v=1.0.4","logo":"/uploads/generalsetting/1781273017_logo.jpg"}';
$response = new \Illuminate\Http\Response($json, 200, ['Content-Type' => 'application/json']);
$next = function() use ($response) { return $response; };
$res = $middleware->handle($request, $next);
echo "Original: " . $json . "\n";
echo "Normalized: " . $res->getContent() . "\n";
