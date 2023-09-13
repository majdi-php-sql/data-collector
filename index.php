<?php
$urls = [
    '#',
    '#',
];

$data = [];

function extractText($text) {
    $lines = preg_split('/\r\n|\r|\n/', $text);

    $columnNames = [];
    $columnData = [];

    foreach ($lines as $line) {
        $words = explode(' ', $line);
        
        if (count($words) >= 2) {
            $columnName = array_shift($words);
            $columnNames[] = $columnName;
            $columnData[] = implode(' ', $words);
        }
    }

    return [
        'columnNames' => $columnNames,
        'columnData' => $columnData,
    ];
}

foreach ($urls as $url) {
    $html = file_get_contents($url);

    if ($html) {
        $subtitlePattern = '/<div class="header-subtitle">(.*?)<\/div>/is';

        preg_match($subtitlePattern, $html, $matches);

        if (isset($matches[1])) {
            $text = trim(strip_tags($matches[1]));
            $result = extractText($text);

            $data[] = [
                'Column Names' => implode(', ', $result['columnNames']),
                'Column Data' => implode(', ', $result['columnData']),
            ];
        }
    }
}

$csvFileName = 'collected_data.csv';

$file = fopen($csvFileName, 'w');

fputcsv($file, ['Column Names', 'Column Data']);

foreach ($data as $row) {
    fputcsv($file, $row);
}

fclose($file);

echo "Data has been successfully collected and exported to $csvFileName.";
?>
