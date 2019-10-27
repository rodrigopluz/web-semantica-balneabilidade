<?php

// chart.js - exemplo de grafico
header('Cache-Control: no-cache');
header('Access-Control-Allow-Origin: *');
header('Content-type: application/json; charset="utf-8"', true);

$city = $_POST['city'];
$local = $_POST['local'];
$year = $_POST['year'];
$beach = str_replace(' ', '_', strtolower($_POST['beach']));

$layout = curl_init();

curl_setopt($layout, CURLOPT_URL,'https://balneabilidade.ima.sc.gov.br/relatorio/historico');
curl_setopt($layout, CURLOPT_POST, 1);
curl_setopt($layout, CURLOPT_RETURNTRANSFER, true);
curl_setopt($layout, CURLOPT_POSTFIELDS,'municipioID='. $city .'&localID='. $local .'&ano='. $year .'&redirect=true');
curl_setopt($layout, CURLOPT_SSL_VERIFYHOST, false);
curl_setopt($layout, CURLOPT_SSL_VERIFYPEER, false);

$response = curl_exec($layout);
$doc = new DOMDocument();
$doc->loadHTML($response);
$tables = $doc->getElementsByTagName('table');

$bathings = [];
foreach ($tables as $key => $table) {
    $points = [];
    
    if ($key % 2 != 0) {
        /**
         * monta o array dos pontos de coleta
         */
        $labels = $table->getElementsByTagName('label');
        foreach ($labels as $label) {
            $point = explode(': ', $label->textContent);
            $title = str_replace(" ", "_", preg_replace("/&([a-z])[a-z]+;/i", "$1", htmlentities(trim($point[0]))));
            $value = $point[1];

            $points[$title] = $value;
        }
    } else {    
        /**
         * monta o array das linhas de coleta
         */
        $collect = [];
        $lines = $table->getElementsByTagName('tr');
        foreach ($lines as $line) {
            $cells = $line->getElementsByTagName('td');
            foreach ($cells as $cell) {
                $cellule = $cell->getAttribute('class');
                if ($cellule != null) $collect[$cellule] = $cell->textContent;
            }

            $points[] = array_filter($collect);
        }
    }

    $bathings[$key] = array_filter($points);
}

echo json_encode(array_filter($bathings));

/** script para criar arquivo em formato html com o nome da praia selecionada */
$html = $response;
$file_html = fopen('assets/file-beach/html/'. $beach .'.html', 'w');
fwrite($file_html, $html);
fclose($file_html);

/** script para criar arquivo em formato json com o nome da praia selecionada */
$json = json_encode(array_filter($bathings), JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
$file_json = fopen('assets/file-beach/json/'. $beach .'.json', 'a');
fwrite($file_json, $json);
fclose($file_json);
