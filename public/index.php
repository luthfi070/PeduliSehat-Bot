<?php
require __DIR__ . '/../vendor/autoload.php';
 
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Factory\AppFactory;
 
use \LINE\LINEBot;
use \LINE\LINEBot\HTTPClient\CurlHTTPClient;
use \LINE\LINEBot\MessageBuilder\MultiMessageBuilder;
use \LINE\LINEBot\MessageBuilder\TextMessageBuilder;
use \LINE\LINEBot\MessageBuilder\StickerMessageBuilder;
use \LINE\LINEBot\SignatureValidator as SignatureValidator;
 
$pass_signature = true;
 
// set LINE channel_access_token and channel_secret
$channel_access_token = "A2j4q7c+x00KV/RwGio0ylX4aCNrWTxV86HqSw2K16OKRd3yWVw+ksM8koot/T6A4EWkz2wFG9a2bi07DJDh50fzwrvhGbwTsYyzgSiggebIv4I0b9WdRYQOMeRQEdbPy+KGspqq4QN0IE/iswzjkwdB04t89/1O/w1cDnyilFU=";
$channel_secret = "8bca49b7dd8edf205dcb7822056ee606";
 
// inisiasi objek bot
$httpClient = new CurlHTTPClient($channel_access_token);
$bot = new LINEBot($httpClient, ['channelSecret' => $channel_secret]);
 
$app = AppFactory::create();
$app->setBasePath("/public");
 
$app->get('/', function (Request $request, Response $response, $args) {
    $response->getBody()->write("Hello World!");
    return $response;
});
 
// buat route untuk webhook
$app->post('/webhook', function (Request $request, Response $response) use ($channel_secret, $bot, $httpClient, $pass_signature) {
    // get request body and line signature header
    $body = $request->getBody();
    $signature = $request->getHeaderLine('HTTP_X_LINE_SIGNATURE');
 
    // log body and signature
    file_put_contents('php://stderr', 'Body: ' . $body);
 
    if ($pass_signature === false) {
        // is LINE_SIGNATURE exists in request header?
        if (empty($signature)) {
            return $response->withStatus(400, 'Signature not set');
        }
 
        // is this request comes from LINE?
        if (!SignatureValidator::validateSignature($body, $channel_secret, $signature)) {
            return $response->withStatus(400, 'Invalid signature');
        }
    }
    
// kode aplikasi nanti disini


    $data = json_decode($body, true);
    $nama = array("neta", "silmy", "danar", "dana", "yuzza", "made");
    //Api Indonesia
    $curl = curl_init();

    curl_setopt_array($curl, array(
      CURLOPT_URL => 'https://api.kawalcorona.com/indonesia/',
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_ENCODING => '',
      CURLOPT_MAXREDIRS => 10,
      CURLOPT_TIMEOUT => 0,
      CURLOPT_FOLLOWLOCATION => true,
      CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
      CURLOPT_CUSTOMREQUEST => 'GET',
    ));
     
    $response = curl_exec($curl);
     
    curl_close($curl);
    //end of Api Indonesia

    //Api Provinsi 
    $curlProv = curl_init();

    curl_setopt_array($curlProv, array(
        CURLOPT_URL => 'https://api.kawalcorona.com/indonesia/provinsi/',
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'GET',
      ));

    $responseProv = curl_exec($curlProv);

    curl_close($curlProv);
    //end of Api Provinsi

    $decoded = json_decode($response, true);
    $array = array($decoded);

    $decodedProv = json_decode($responseProv, true);
    $arrayPRov = array($decodedProv);

    if(is_array($data['events'])){
        foreach ($data['events'] as $event)
        {
            if($event['source']['type'] == 'group' or $event['source']['type'] == 'room' or $event['source']['type'] == 'user'){
                if($event['message']['text'] == "/mulai"){
                    $flexTemplateMenu = file_get_contents("../flexMessageMenu.json"); // template flex message
                    $result = $httpClient->post(LINEBot::DEFAULT_ENDPOINT_BASE . '/v2/bot/message/reply', [
                        'replyToken' => $event['replyToken'],
                        'messages'   => [
                            [ 
                                'type'     => 'flex',
                                'altText'  => 'Test Flex Message',
                                'contents' => json_decode($flexTemplateMenu)
                            ]
                        ],
                    ]);
                }
                else if($event['message']['text'] == '/onBoarding'){
                    $res = $bot->replyText($event['replyToken'],
                    " Bot ni adalah bot yang berguna untuk memantau perkembangan virus covid 19 atau dikenal sebagai corona di Indonesia, berikut beberapa hal yang bisa anda lakukan di bot ini : 
- Lihat jumlah positif di Indonesia
- Lihat jumlah pasien yang sudah sembuh di Indonesia
- Lihat jumlah pasien yang sudah meninggal di Indonesia
- Lihat perkembangan covid 19 di suatu provinsi
- Lihat perkembangan covid 19 di suatu negara");
                }else if($event['message']['text'] == '/positif'){          
                    $res = $bot->replyText($event['replyToken'], "Jumlah pasien positif di Indonesia saat ini : " . $array[0][0]["positif"]);
                }else if($event['message']['text'] == '/sembuh'){
                    $res = $bot->replyText($event['replyToken'], "Jumlah pasien sembuh di Indonesia saat ini : " . $array[0][0]["sembuh"]);
                }else if($event['message']['text'] == '/meninggal'){
                    $res = $bot->replyText($event['replyToken'], "Jumlah pasien meninggal di Indonesia saat ini : " . $array[0][0]["meninggal"]);
                }else if($event['message']['text'] == '/provinsi'){
                    $flexTemplateMenu = file_get_contents("../flexMessagePulau.json"); // template flex message
                    $result = $httpClient->post(LINEBot::DEFAULT_ENDPOINT_BASE . '/v2/bot/message/reply', [
                        'replyToken' => $event['replyToken'],
                        'messages'   => [
                            [ 
                                'type'     => 'flex',
                                'altText'  => 'Test Flex Message',
                                'contents' => json_decode($flexTemplateMenu)
                            ]
                        ],
                    ]);
                }else if($event['message']['text'] == '/jawa'){
                    $flexTemplateMenu = file_get_contents("../flexMessageJawa.json"); // template flex message
                    $result = $httpClient->post(LINEBot::DEFAULT_ENDPOINT_BASE . '/v2/bot/message/reply', [
                        'replyToken' => $event['replyToken'],
                        'messages'   => [
                            [ 
                                'type'     => 'flex',
                                'altText'  => 'Test Flex Message',
                                'contents' => json_decode($flexTemplateMenu)
                            ]
                        ],
                    ]);
                }else if($event['message']['text'] == '/sulawesi'){
                    $flexTemplateMenu = file_get_contents("../flexMessageSulawesi.json"); // template flex message
                    $result = $httpClient->post(LINEBot::DEFAULT_ENDPOINT_BASE . '/v2/bot/message/reply', [
                        'replyToken' => $event['replyToken'],
                        'messages'   => [
                            [ 
                                'type'     => 'flex',
                                'altText'  => 'Test Flex Message',
                                'contents' => json_decode($flexTemplateMenu)
                            ]
                        ],
                    ]);
                }else if($event['message']['text'] == '/kalimantan'){
                    $flexTemplateMenu = file_get_contents("../flexMessageKalimantan.json"); // template flex message
                    $result = $httpClient->post(LINEBot::DEFAULT_ENDPOINT_BASE . '/v2/bot/message/reply', [
                        'replyToken' => $event['replyToken'],
                        'messages'   => [
                            [ 
                                'type'     => 'flex',
                                'altText'  => 'Test Flex Message',
                                'contents' => json_decode($flexTemplateMenu)
                            ]
                        ],
                    ]);
                }else if($event['message']['text'] == '/sumatera'){
                    $flexTemplateMenu = file_get_contents("../flexMessageSumatera.json"); // template flex message
                    $result = $httpClient->post(LINEBot::DEFAULT_ENDPOINT_BASE . '/v2/bot/message/reply', [
                        'replyToken' => $event['replyToken'],
                        'messages'   => [
                            [ 
                                'type'     => 'flex',
                                'altText'  => 'Test Flex Message',
                                'contents' => json_decode($flexTemplateMenu)
                            ]
                        ],
                    ]);
                }else if($event['message']['text'] == '/papua'){
                    $flexTemplateMenu = file_get_contents("../flexMessagePapua.json"); // template flex message
                    $result = $httpClient->post(LINEBot::DEFAULT_ENDPOINT_BASE . '/v2/bot/message/reply', [
                        'replyToken' => $event['replyToken'],
                        'messages'   => [
                            [ 
                                'type'     => 'flex',
                                'altText'  => 'Test Flex Message',
                                'contents' => json_decode($flexTemplateMenu)
                            ]
                        ],
                    ]);
                }else if($event['message']['text'] == '/maluku'){
                    $flexTemplateMenu = file_get_contents("../flexMessageMaluku.json"); // template flex message
                    $result = $httpClient->post(LINEBot::DEFAULT_ENDPOINT_BASE . '/v2/bot/message/reply', [
                        'replyToken' => $event['replyToken'],
                        'messages'   => [
                            [ 
                                'type'     => 'flex',
                                'altText'  => 'Test Flex Message',
                                'contents' => json_decode($flexTemplateMenu)
                            ]
                        ],
                    ]);
                }else{
                    for($i = 0; $i < 35; $i++){
                        if(in_array($event['message']['text'], $arrayPRov[0][$i]['attributes'])){
                            $res = $bot->replyText($event['replyToken'],
"Provinsi " . $arrayPRov[0][$i]['attributes']['Provinsi'] . "\n" .
"Kasus Positif         : " . $arrayPRov[0][$i]['attributes']['Kasus_Posi'] . "\n" .
"Kasus Sembuh     : " . $arrayPRov[0][$i]['attributes']['Kasus_Semb'] . "\n" .
"Kasus Meninggal : " . $arrayPRov[0][$i]['attributes']['Kasus_Meni']);
                        }else{
                            if($i == 33){
                                if($event['source']['type'] === 'group' or $event['source']['type'] === 'room'){
                                    if($event['message']['text'] != null){
                                        $res = $bot->replyText($event['replyToken'], "Maaf, " . $event['message']['text'] . " tidak ada dalam keyword kami, silahkan ketik /mulai");
                                    }else{
                                        $flexTemplateMenu = file_get_contents("../flexMessageGroup.json"); // template flex message
                                        $result = $httpClient->post(LINEBot::DEFAULT_ENDPOINT_BASE . '/v2/bot/message/reply', [
                                            'replyToken' => $event['replyToken'],
                                            'messages'   => [
                                                [ 
                                                    'type'     => 'flex',
                                                    'altText'  => 'Test Flex Message',
                                                    'contents' => json_decode($flexTemplateMenu)
                                                ]
                                            ],
                                        ]);                                   
                                    }
                                }else{
                                    if($event['message']['text'] != null){
                                        $res = $bot->replyText($event['replyToken'], "Maaf, " . $event['message']['text'] . " tidak ada dalam keyword kami, silahkan ketik /mulai");
                                    }else{
                                        $flexTemplateMenu = file_get_contents("../flexMessagePersonal.json"); // template flex message
                                        $result = $httpClient->post(LINEBot::DEFAULT_ENDPOINT_BASE . '/v2/bot/message/reply', [
                                            'replyToken' => $event['replyToken'],
                                            'messages'   => [
                                                [ 
                                                    'type'     => 'flex',
                                                    'altText'  => 'Test Flex Message',
                                                    'contents' => json_decode($flexTemplateMenu)
                                                ]
                                            ],
                                        ]);
                                    }
                                }
                            }else{
                                continue;
                            }
                        } 
                    }  
                }    
            }
        }
    }
});
$app->run();
?>