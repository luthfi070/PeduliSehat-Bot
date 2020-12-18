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

    $decoded = json_decode($response, true);
    $array = array($decoded);    
    if(is_array($data['events'])){

        class replyList{
            public static function menu(){
                $app->post('/webhook', function (Request $request, Response $response) use ($channel_secret, $bot, $httpClient, $pass_signature) {
                    $flexTemplate = file_get_contents("../flexMessageGroup.json"); // template flex message
                    $result = $httpClient->post(LINEBot::DEFAULT_ENDPOINT_BASE . '/v2/bot/message/reply', [
                        'replyToken' => $event['replyToken'],
                        'messages'   => [
                            [ 
                                'type'     => 'flex',
                                'altText'  => 'Test Flex Message',
                                'contents' => json_decode($flexTemplate)
                            ]
                        ],
                    ]);
                }
            }
        }

        foreach ($data['events'] as $event)
        {
            if($event['source']['type'] == 'group' or $event['source']['type'] == 'room'){
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
                }
                else{
                    replyList::menu();
                }    
            }else{
                if ($event['type'] == 'message')
                {
                    for($x = 0; $x < count($nama); $x++){
                        if($event['message']['text'] == $nama[$x]){
                            $result = $bot->replyText($event['replyToken'], $nama[$x] . "baik dechh");
                            
                            $response->getBody()->write(json_encode($result->getJSONDecodedBody()));
                            return $response
                                ->withHeader('Content-Type', 'application/json')
                                ->withStatus($result->getHTTPStatus());
                        }
                    }
                    if($event['message']['type'] == 'text')
                    {
                        if($event['message']['text'] == "/mulai")
                        {
                            $result = $bot->replyText($event['replyToken'], "cringeee");
    
                            $response->getBody()->write(json_encode($result->getJSONDecodedBody()));
                            return $response
                                ->withHeader('Content-Type', 'application/json')
                                ->withStatus($result->getHTTPStatus());
                        }
                        else if($event['message']['text'] == "hitomi"){
    
                            $result = $bot->replyText($event['replyToken'], "istrinya luthfi bukan");
    
                            $response->getBody()->write(json_encode($result->getJSONDecodedBody()));
                            return $response
                                ->withHeader('Content-Type', 'application/json')
                                ->withStatus($result->getHTTPStatus());    
    
                            $packageId = 1;
                            $stickerId = 3;
                            $stickerMessageBuilder = new StickerMessageBuilder($packageId, $stickerId);
                            $bot->replySticker($replyToken, 1, 3);
                        }
                        // else{
                        //     $result = $bot->replyText($event['replyToken'], $event['message']['text'] . " " . "kaga ada disini");
    
                        //     // or we can use replyMessage() instead to send reply message
                        //     // $textMessageBuilder = new TextMessageBuilder($event['message']['text']);
                        //     // $result = $bot->replyMessage($event['replyToken'], $textMessageBuilder);
                            
                        //     $response->getBody()->write(json_encode($result->getJSONDecodedBody()));
                        //     return $response
                        //         ->withHeader('Content-Type', 'application/json')
                        //         ->withStatus($result->getHTTPStatus());
                        // }
                    }
                }
            }
        }
    }
});
$app->run();
?>