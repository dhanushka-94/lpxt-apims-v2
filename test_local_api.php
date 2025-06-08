<?php

// Simple script to test the local API
$apiUrl = 'http://127.0.0.1:8000/api/products?api_key=msk-api-5f4dcc3b5aa765d61d8327deb882cf99';

echo "Testing Local API...\n";
echo "URL: $apiUrl\n\n";

// Create context for the request
$context = stream_context_create([
    'http' => [
        'method' => 'GET',
        'header' => 'Accept: application/json',
        'timeout' => 30
    ]
]);

// Make the request
$response = file_get_contents($apiUrl, false, $context);

if ($response === false) {
    echo "❌ Failed to get response from local API\n";
    echo "Error: " . error_get_last()['message'] . "\n";
} else {
    echo "✅ Local API Response:\n";
    
    // Pretty print JSON
    $data = json_decode($response, true);
    if ($data) {
        echo "Success: " . ($data['success'] ? 'true' : 'false') . "\n";
        if (isset($data['data']['data']) && is_array($data['data']['data'])) {
            $products = $data['data']['data'];
            echo "Products Count: " . count($products) . "\n\n";
            
            // Show first product's brand info
            if (count($products) > 0) {
                $firstProduct = $products[0];
                echo "First Product:\n";
                echo "- ID: " . $firstProduct['id'] . "\n";
                echo "- Name: " . $firstProduct['name'] . "\n";
                echo "- Brand: " . json_encode($firstProduct['brand']) . "\n\n";
            }
        }
    } else {
        echo "Raw Response:\n";
        echo $response . "\n";
    }
}

echo "\n=== Testing Live API ===\n";
$liveApiUrl = 'https://api.erpsys.laptopexpert.lk/api/products?api_key=msk-api-5f4dcc3b5aa765d61d8327deb882cf99';
echo "URL: $liveApiUrl\n\n";

// Test live API
$liveContext = stream_context_create([
    'http' => [
        'method' => 'GET',
        'header' => 'Accept: application/json',
        'timeout' => 30
    ]
]);

$liveResponse = file_get_contents($liveApiUrl, false, $liveContext);

if ($liveResponse === false) {
    echo "❌ Failed to get response from live API\n";
    echo "Error: " . error_get_last()['message'] . "\n";
} else {
    echo "✅ Live API Response:\n";
    
    // Pretty print JSON
    $liveData = json_decode($liveResponse, true);
    if ($liveData) {
        echo "Success: " . ($liveData['success'] ? 'true' : 'false') . "\n";
        if (isset($liveData['data']['data']) && is_array($liveData['data']['data'])) {
            $liveProducts = $liveData['data']['data'];
            echo "Products Count: " . count($liveProducts) . "\n";
            
            // Show first product's brand info
            if (count($liveProducts) > 0) {
                $liveFirstProduct = $liveProducts[0];
                echo "First Product:\n";
                echo "- ID: " . $liveFirstProduct['id'] . "\n";
                echo "- Name: " . $liveFirstProduct['name'] . "\n";
                echo "- Brand: " . json_encode($liveFirstProduct['brand']) . "\n";
            }
        }
    } else {
        echo "Raw Response:\n";
        echo $liveResponse . "\n";
    }
}

echo "\n=== Comparison ===\n";
if ($response && $liveResponse) {
    $localData = json_decode($response, true);
    $liveData = json_decode($liveResponse, true);
    
    if ($localData && $liveData && 
        isset($localData['data']['data'][0]['brand']) && 
        isset($liveData['data']['data'][0]['brand'])) {
        
        $localBrand = $localData['data']['data'][0]['brand'];
        $liveBrand = $liveData['data']['data'][0]['brand'];
        
        echo "Local Brand: " . json_encode($localBrand) . "\n";
        echo "Live Brand: " . json_encode($liveBrand) . "\n";
        
        if (json_encode($localBrand) === json_encode($liveBrand)) {
            echo "✅ Brand data matches!\n";
        } else {
            echo "❌ Brand data differs - Live server needs update!\n";
        }
    }
} 