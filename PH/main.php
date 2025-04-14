<?php
header('Content-Type: application/json');

// Getting the request type (region/province/city/barangay) and optional parent ID
$type = $_GET['type'] ?? null;
$parentId = $_GET['parent_id'] ?? null;

// Map each type to its corresponding JSON file
$basePath = __DIR__ . '/json/';
$fileMap = [
    'region' => 'table_region.json',
    'province' => 'table_province.json',
    'city' => 'table_municipality.json',
    'barangay' => 'table_barangay.json'
];

// This will stop if the request type is invalid
if (!isset($fileMap[$type])) {
    echo json_encode(['error' => 'Invalid type']);
    exit;
}

// Checking if the corresponding JSON file exists
$filePath = $basePath . $fileMap[$type];
if (!file_exists($filePath)) {
    echo json_encode(['error' => 'File not found']);
    exit;
}

// Load and decode the JSON data
$data = json_decode(file_get_contents($filePath), true);

if ($parentId) {
    $data = array_filter($data, function ($item) use ($type, $parentId) {
        if ($type === 'province') return $item['region_id'] == $parentId;
        if ($type === 'city') return $item['province_id'] == $parentId;
        if ($type === 'barangay') return $item['municipality_id'] == $parentId;
        return true;
    });
    $data = array_values($data);
}

// Always return the ID and Name
$normalized = [];
foreach ($data as $item) {
    if ($type === 'city') {
        $id = $item['municipality_id'] ?? null;
        $name = $item['municipality_name'] ?? null;
    } elseif ($type === 'barangay') {
        $id = $item['barangay_id'] ?? null;
        $name = $item['barangay_name'] ?? null;
    } elseif ($type === 'province') {
        $id = $item['province_id'] ?? null;
        $name = $item['province_name'] ?? null;
    } else { // region
        $id = $item['region_id'] ?? null;
        $name = $item['region_name'] ?? null;
    }

    if ($id && $name) {
        $normalized[] = ['id' => $id, 'name' => $name];
    }
}

// Send final JSON response
echo json_encode($normalized);
