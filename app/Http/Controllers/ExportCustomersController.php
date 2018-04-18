<?php

namespace App\Http\Controllers;

use Illuminate\Routing\Controller as BaseController;

class ExportCustomersController extends BaseController
{
    public function index()
    {
        $data['envialosApiKeys'] = [];

        if(isset($_GET['code'])) {
            $token = $this->getToken($_GET['code']);
            if(!isset($token['error'])) { //!isset($token['error'])

                //$data['tiendaId'] = 789; // $token['user_id'];
                $data['tiendaId'] = $token['user_id'];
                
                //$data['tiendaToken'] = "aklsd233k4oi233kl2"; //$token['access_token'];
                $data['tiendaToken'] = $token['access_token'];

                //$data['tokenType'] = "bearer"; //$token['token_type'];
                $data['tokenType'] = $token['token_type'];


                $tienda =  \App\Models\Tienda::where('tiendaId',$data['tiendaId'])->first();
                if($tienda){
                    $accounts = $tienda->envialoAccounts;
                    $data['envialosApiKeys'] = $accounts ? $accounts : [] ;
                } else {
                    $tienda = new \App\Models\Tienda();
                    $tienda->tiendaId = $data['tiendaId'];
                    $tienda->save();
                }
                
            } else {
                $data['error'] = "Error Token";    
            }

        } else {
            $data['error'] = "Codigo Inexistente";
        }

        return view('exportCustomers.index', $data);
    }

    public function addEnvialoAccount() {
        $data = [
            'tiendaToken' => $_POST['tiendaToken'],
            'tokenType' => $_POST['tokenType'],
            'tiendaId' => $_POST["tiendaId"]
        ];

        return view('exportCustomers.addEnvialoAccount', $data);
    }

    public function main()
    {
        // Consulto en la base de datos el api key de Envialo
        // Envilo Simple  https://app.envialosimple.com/<modulo>/<accion>?APIKey=<api key>&format=<format>
        // Clave API del usuario Env141051mpl3 = 8feb7abce2d6e753c7e545db37678baf6bb2a766b5a9883520bdd03e0b97370d01

        $apikeyEnvialo = $_POST['apikey'];
        $tiendaToken = $_POST['tiendaToken'];
        $tokenType = $_POST['tokenType'] ;
        $tiendaId = $_POST["tiendaId"];

        $data = [];
        $url = "http://app.envialosimple.com/maillist/list/?APIKey=$apikeyEnvialo&format=json"; // Listar Listas

        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30000,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
            CURLOPT_POSTFIELDS => json_encode($data),
            CURLOPT_HTTPHEADER => array(
                // Set here requred headers
                "accept: */*",
                "accept-language: en-US,en;q=0.8",
                "content-type: application/json",
                //"Authentication: {$token}",
                "User-Agent: Export Customers (piero.blunda@donweb.com)",
            ),
        ));

        $exec = curl_exec($curl);
        $err = curl_error($curl);

        curl_close($curl);

        if ($err) {
            $response = json_decode($err, true);
            $data['error'] = "Error en la solicitud";
        } else {
            $response = json_decode($exec, true);
            if(isset($response['root']["ajaxResponse"]['request']['params']['errorCode'])){
                $data['error'] = "El api key no es correcto";
            }else{
                $lists = $response['root']["ajaxResponse"]['list'];
                
                $data = [   'lists' => $lists, 
                            'apikey' => $apikeyEnvialo,
                            'tiendaToken' => $tiendaToken,
                            'tokenType' => $tokenType,
                            'tiendaId' => $tiendaId

                        ];
            }
        }

        return view("exportCustomers.main", $data);
    }
    public function pushContacts()
    {
        $apikeyEnvialo = $_POST['apikey'];
        $tiendaToken = $_POST['tiendaToken'];
        $tokenType = $_POST['tokenType'] ;
        $tiendaId = $_POST["tiendaId"];
        $mailListID = $_POST['mailListID'];

        $tienda = \App\Models\Tienda::where('tiendaId', $tiendaId)->first();
        $account = $tienda->envialoAccounts->where('apikey', $apikeyEnvialo);

        if($account->isEmpty()){
            $account = new \App\Models\EnvialoAccount();
            $account->tienda_id = $tienda->id;
            $account->apikey = $apikeyEnvialo;
            
            $account->save();
        } 


        $url = "https://api.tiendanube.com/v1/{$tiendaId}/customers";
        $accessToken = "{$tokenType} {$tiendaToken}";
        $cutomers = $this->getTiendaContacts($url, "GET", $accessToken);
        if(!isset($cutomers['error'])){
            $count = 0;
            foreach ($cutomers as $custormer) {
                $response = $this->pushContactsService($apikeyEnvialo, $mailListID, $custormer);
                $response ? $count++ : '';
            }
            $data['count'] = $count;
        } else {
            $data['error'] = $token['error_description'];
        }
        

        return view("exportCustomers.pushContacts", $data);
    }

    public function pushContactsService($apiKeyEnvialo, $mailListID, $contact)
    {

        //Create a new contact in the list 1
        $params = array();
        $params['APIKey'] = $apiKeyEnvialo;
        $params['MailListID'] = $mailListID;

        $params['Email'] = $contact['email'];
        $params['CustomField1'] = $contact['name'];

        $baseURL = 'https://app.envialosimple.com';
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $baseURL . '/member/edit/format/json');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($params));
        $jsonResponse = curl_exec($ch);
        $err = curl_error($ch);
        curl_close($ch);
        if ($err) {
            $response = false;
        } else {
            $response = true;
        }

        return $response;

        // Consulto en la base de datos el api key de Envialo
        // Envilo Simple  https://app.envialosimple.com/<modulo>/<accion>?APIKey=<api key>&format=<format>
        // Clave API del usuario Env141051mpl3 = 8feb7abce2d6e753c7e545db37678baf6bb2a766b5a9883520bdd03e0b97370d01 
    }

    public function getToken($code)
    {
        $data = [
            'client_id' => '715',
            'client_secret' => 'hAHe8O3phu66ZVT7Fq60aVcN5DoZIrSiRwSV0zd8BKJsesej',
            'grant_type' => 'authorization_code',
            'code' => "$code",
        ];

        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => "https://www.tiendanube.com/apps/authorize/token",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30000,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => json_encode($data),
            CURLOPT_HTTPHEADER => array(
                // Set here requred headers
                "accept: */*",
                "accept-language: en-US,en;q=0.8",
                "content-type: application/json",
            ),
        ));

        $curlResponse = curl_exec($curl);
        $err = curl_error($curl);

        curl_close($curl);

        if ($err) {
            $response = json_decode($err, true);
        } else {
            $response = json_decode($curlResponse, true);
        }

        return $response;
    }

    private function getTiendaContacts($url, $method, $token, $data = [])
    {

        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30000,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => $method,
            CURLOPT_POSTFIELDS => json_encode($data),
            CURLOPT_HTTPHEADER => array(
                // Set here requred headers
                "accept: */*",
                "accept-language: en-US,en;q=0.8",
                "content-type: application/json",
                "Authentication: {$token}",
                "User-Agent: Export Customers (piero.blunda@donweb.com)",
            ),
        ));

        $exec = curl_exec($curl);
        $err = curl_error($curl);

        curl_close($curl);

        if ($err) {
            $response = json_decode($err, true);
        } else {
            $response = json_decode($exec, true);
        }

        return $response;
    }

}
