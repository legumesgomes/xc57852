<?php


/*
Plugin Name: GA4 Integration
Plugin URI: https://bio6.click/admin/plugins.php?page=ga4_integration
Description: Integra YOURLS com o Google Analytics 4 (GA4) usando a API oficial do Google.
Version: 1.0
Author: bio6.click
*/




// ============================

// CONFIGURAÇÕES DO PLUGIN

// ============================



// ID da propriedade GA4 (numérico, encontrado nas configurações do GA4)

define('GA4_PROPERTY_ID', '506375596');



// Caminho para o JSON da conta de serviço do Google

define('GA4_KEY_FILE', __DIR__ . '/gen-lang-client-0497638250-b71ef9ae2bb4.json');



// ============================

// INTEGRAÇÃO COM GOOGLE CLIENT

// ============================



require_once __DIR__ . '/vendor/autoload.php';



function ga4_get_client() {

    $client = new Google_Client();

    $client->setAuthConfig(GA4_KEY_FILE);

    $client->addScope('https://www.googleapis.com/auth/analytics.readonly');

    return $client;

}



function ga4_get_service() {

    $client = ga4_get_client();

    return new Google\Analytics\Data\V1beta\BetaAnalyticsDataClient([

        'credentials' => GA4_KEY_FILE,

    ]);

}



// ============================

// FUNÇÃO: BUSCAR CLIQUES DE UM LINK

// ============================



function ga4_get_clicks($shorturl) {

    try {

        $service = ga4_get_service();



        $request = [

            'property' => 'properties/' . GA4_PROPERTY_ID,

            'dateRanges' => [

                ['startDate' => '30daysAgo', 'endDate' => 'today'],

            ],

            'metrics' => [

                ['name' => 'eventCount'],

            ],

            'dimensions' => [

                ['name' => 'linkUrl'],

            ],

            'dimensionFilter' => [

                'filter' => [

                    'fieldName' => 'linkUrl',

                    'stringFilter' => ['value' => $shorturl],

                ],

            ],

        ];



        $response = $service->runReport($request);



        if (count($response->getRows()) > 0) {

            return $response->getRows()[0]->getMetricValues()[0]->getValue();

        } else {

            return 0;

        }

    } catch (Exception $e) {

        error_log("GA4 API error: " . $e->getMessage());

        return null;

    }

}



// ============================

// HOOK PARA MOSTRAR CLIQUES NO YOURLS

// ============================



yourls_add_filter('table_add_row', 'ga4_show_clicks', 10, 6);



function ga4_show_clicks($row, $keyword, $url, $title, $ip, $clicks, $timestamp = '') {

    $shorturl = yourls_link($keyword);

    $ga4_clicks = ga4_get_clicks($shorturl);



    $row .= '<td class="ga4-clicks">';

    if ($ga4_clicks !== null) {

        $row .= '<span class="text-blue-400">GA4: ' . intval($ga4_clicks) . ' cliques</span>';

    } else {

        $row .= '<span class="text-gray-400">GA4: N/A</span>';

    }

    $row .= '</td>';



    return $row;

}

