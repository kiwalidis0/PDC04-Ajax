<?php
header('Content-Type: application/json');

// getting the values from the URL (type and parent_id if any)
$type = isset($_GET['type']) ? $_GET['type'] : null;
$parentId = isset($_GET['parent_id']) ? $_GET['parent_id'] : null;

$folderPath = __DIR__ . '/json/';

// matching the type to the correct file names
$fileNames = [
    'region' => 'table_region.json',
    'province' => 'table_province.json',
    'city' => 'table_municipality.json',
    'barangay' => 'table_barangay.json'
];

// checking if the type is valid or invalid (e.g., region, province, etc.)
if (!isset($fileNames[$type])) {
    echo json_encode(['error' => 'Invalid type requested']);
    exit;
}

// the full path for the selected dropdown type
$filePath = $folderPath . $fileNames[$type];

// making sure the file exists
if (!file_exists($filePath)) {
    echo json_encode(['error' => 'Data file not found']);
    exit;
}

// reading the JSON file and turning it into an array
$data = json_decode(file_get_contents($filePath), true);

// If a parent ID goes through, this will filter the data
if ($parentId) {
    $data = array_filter($data, function ($item) use ($type, $parentId) {
        if ($type === 'province') {
            return $item['region_id'] == $parentId;
        } elseif ($type === 'city') {
            return $item['province_id'] == $parentId;
        } elseif ($type === 'barangay') {
            return $item['municipality_id'] == $parentId;
        }
        return true;
    });

    $data = array_values($data);
}

// sending back the list of options 
$result = [];

foreach ($data as $item) {
    if ($type === 'region') {
        $id = $item['region_id'];
        $name = $item['region_name'];
    } elseif ($type === 'province') {
        $id = $item['province_id'];
        $name = $item['province_name'];
    } elseif ($type === 'city') {
        $id = $item['municipality_id'];
        $name = $item['municipality_name'];
    } elseif ($type === 'barangay') {
        $id = $item['barangay_id'];
        $name = $item['barangay_name'];
    }

    // this verifies that both id and name exists before adding them
    if ($id && $name) {
        $result[] = [
            'id' => $id,
            'name' => $name
        ];
    }
}

// returns it in json format
echo json_encode($result);