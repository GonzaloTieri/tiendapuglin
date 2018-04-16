<?php

namespace App\Http\Controllers;

use Illuminate\Routing\Controller as BaseController;

class ExportCustomersController extends BaseController
{
    public function index()
    {
        //$ti = new \App\Models\Tienda;
        //$ti->tienda_id = 789;

        //$ti = \App\Models\Tienda::find(1);
        //$ti->save();
        //dd($ti->tienda_id);  
        //$tiendas = \App\Models\Tienda::find(123)->envialoAcounts;
        //$tiendas = \App\Models\EnvialoAcount::find(1);
        //$todoNuevo = new \App\Models\Tienda;
        //$todoNuevo->id = 321;
        //$todoNuevo->envialoAcounts = ['apikey' => 'abc', 'tiendaId'=>'321' ];
        //$todoNuevo->que;
       //var_dump(json_encode($todoNuevo));exit;
        //$data['code'] = 'asd';
        if(isset($_GET['code'])) {
            $data['code'] = $_GET['code'];
            //$token = $this->getToken($data['code']);
            if(true) { //!isset($token['error'])
                $data['tiendaId'] = 1234; // $token['user_id'];
                //$data['tiendaToken'] = $token['access_token'];
                //$data['tokenType'] = $token['token_type'];
                $data['envialosApiKeys'] = [];
                $tienda =  \App\Models\Tienda::find($data['tiendaId']);
                if($tienda){
                    $data['envialosApiKeys'] = $tienda->envialoAcounts;
                }
                
            } else {
                $data['error'] = "Error Token";    
            }

        } else {
            $data['error'] = "Codigo Inexistente";
        }

        return view('exportCustomers.index', $data);
    }

    public function main()
    {
        // Consulto en la base de datos el api key de Envialo
        // Envilo Simple  https://app.envialosimple.com/<modulo>/<accion>?APIKey=<api key>&format=<format>
        // Clave API del usuario Env141051mpl3 = 8feb7abce2d6e753c7e545db37678baf6bb2a766b5a9883520bdd03e0b97370d01
/*
        if(isset($_POST['new'])){
           
            $tienda = \App\Models\Tienda::find($_POST['tiendaId']);
            
            if(empty($tienda)){
                $tienda = new \App\Models\Tienda;
                $tienda->tiendaId = $_POST['tiendaId'];
                
                $rta = $tienda->save();
                dd($tienda);
                dd($rta);
            } else {
                $cuentas = $tienda->envialoAcounts; 
                if(empty($cuentas)) {
                    $newCuenta = new \App\Models\EnvialoAcount;
                    $newCuenta->tiendaId = $_POST['tiendaId'];
                    $newCuenta->$apikey = $_POST['apikey'];

                }
            }
        }

*/



        $apikeyEnvialo = $_POST['apikey'];
        $code = $_POST['code'];
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
                $data = ['lists' => $lists, 'code' => $code, 'apiKeyEnvialo' => $apikeyEnvialo];
            }
        }

        return view("exportCustomers.main", $data);
    }
    public function pushContacts()
    {
        $code = $_POST['code'];
        $mailListID = $_POST['mailListID'];
        $apiKeyEnvialo = $_POST['apiKeyEnvialo'];

        $token = $this->getToken($code);
        if(!isset($token['error'])){
            $url = "https://api.tiendanube.com/v1/{$token['user_id']}/customers";
            $accessToken = "{$token['token_type']} {$token['access_token']}";
            $cutomers = $this->getTiendaContacts($url, "GET", $accessToken);
            if(!isset($cutomers['error'])){
                $count = 0;
                foreach ($cutomers as $custormer) {
                    $response = $this->pushContactsService($apiKeyEnvialo, $mailListID, $custormer);
                    $response ? $count++ : '';
                }
                $data['count'] = $count;
            } else {
                $data['error'] = $token['error_description'];
            }
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
/*
$response = json_decode($jsonResponse, true);

var_dump($response['root']['ajaxResponse']['member']);
exit;
$id = $response['root']['ajaxResponse']['member']['MemberID'];
$email = $response['root']['ajaxResponse']['member']['Email'];
$nombre = $response['root']['ajaxResponse']['member']['CustomFields']['item']['CustomField1']['Values']['Option'][0]['Value'];$apellido = $response['root']['ajaxResponse']['member']['CustomFields']['item']['CustomField2']['Values']['Option'][0]['Value'];

echo "Nuevo Contacto: $id - $email: $nombre $apellido \n";//Subscribe contact to list with ID=2$params = array();$params['APIKey']=$apiKey;$params['MailListsIds']=array(2);$params['MemberIds']=array($id);$ch = curl_init();curl_setopt($ch, CURLOPT_URL, $baseURL.'/member/changestatus/SubscriptionStatus/Subscribed/format/json');curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);curl_setopt($ch, CURLOPT_POST, 1);curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($params));$jsonResponse = curl_exec($ch);curl_close($ch);$response = json_decode($jsonResponse, true);$exito = $response['root']['ajaxResponse']['success'];$count = $response['root']['ajaxResponse']['countSubscribed'];if($exito && $count=1){echo "Contacto suscripto correctamente a la lista 2\n";}else{echo "No se pudo suscribir correctamente a la lista 2\n";print_r($response);exit;}//Unsubscribe contact from list 1$params = array();$params['APIKey']=$apiKey;$params['MailListsIds']=array(1);$params['MemberIds']=array($id);$ch = curl_init();curl_setopt($ch, CURLOPT_URL, $baseURL.'/member/changestatus/SubscriptionStatus/Unsubscribed/format/json');curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);curl_setopt($ch, CURLOPT_POST, 1);curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($params));$jsonResponse = curl_exec($ch);curl_close($ch);$response = json_decode($jsonResponse, true);$exito = $response['root']['ajaxResponse']['success'];$count = $response['root']['ajaxResponse']['countUnsubscribed'];if($exito && $count=1){echo "User successfully unsubscribed from list 1\n";}else{echo "No se pudo desuscribir correctamente de la lista 1\n";print_r($response);exit;}//Look for contact created in step 1 within list 2$params = array();$params['APIKey']=$apiKey;$params['MailListID']=2;$params['filter']='juanperez';$ch = curl_init();curl_setopt($ch, CURLOPT_URL, $baseURL.'/member/listbymaillist/format/json?'.http_build_query($params));curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);$jsonResponse = curl_exec($ch);curl_close($ch);$response = json_decode($jsonResponse, true);foreach ($response['root']['ajaxResponse']['list']['item'] as $item){$tmpid = $item['MemberID'];$tmpemail = $item['Email'];$tmpnombre = $item['CustomFields']['item']['CustomField1']['Values']['Option'][0]['Value'];$tmpapellido = $item['CustomFields']['item']['CustomField2']['Values']['Option'][0]['Value'];echo "El Contacto $tmpid - $tmpemail ($tmpnombre $tmpapellido) esta suscripto a la lista 2\n";}

exit;
// Consulto en la base de datos el api key de Envialo
// Envilo Simple  https://app.envialosimple.com/<modulo>/<accion>?APIKey=<api key>&format=<format>
// Clave API del usuario Env141051mpl3 = 8feb7abce2d6e753c7e545db37678baf6bb2a766b5a9883520bdd03e0b97370d01
 */
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
