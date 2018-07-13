<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Mockery\Exception;
use SoapClient;
use Illuminate\Support\Facades\Cache;


use PlaceToPay\SDKPSE\Helpers\Helper;
use PlaceToPay\SDKPSE\Helpers\Validate;
//use PlaceToPay\SDKPSE\Cache\Cache;
use PlaceToPay\SDKPSE\Structures\Authentication;

use PlaceToPay\SDKPSE\SDKPSE;
use PlaceToPay\SDKPSE\Structures\PSETransactionRequest;

/**
 * @property SoapClient client
 */
class PagosController extends Controller
{

    public function __construct()
    {
        $this->client = new SoapClient(config('app.pagowsdl'), array('trace' => true));
        $this->client->__setLocation(config('app.pagolocation'));
    }

    public function wahedGetBankList()
    {
        //print("Obtener la lista de los bancos\n");

        $obj = new SDKPSE($this->getConfig());
        $banks = $obj->getBankList();
        return ($banks);
        //var_dump($banks);
    }

    public function getBankList()
    {
        # tiempo de expiracion para cachear los bancos
        $expiration = 86400; // 1 dia
        # key asignado para cachear los bancos
        $keyCache = 'bank_list';
        # Obtener la lista de bancos que estan en la cache
        $cache = new Cache($this->config['cache']);
        $banks = $cache->get($keyCache);

        if ($banks === false) {
            try {
                # Consumir el servicio para obtener las bancos
                $result = $this->client->getBankList($this->auth());
                $banks = $result->getBankListResult->item;
                $cache->add($keyCache, $banks, $expiration);
            } catch (Exception $e) {
                Error::newException(
                    'Error al obtener los bancos'
                );
            }
        }

        return is_array($banks) ? $banks : false;
    }





    public function muestraCosas(){

        //$obj = new PagosController;
        $bancas = $this->wahedGetBankList();
        //$bancas = $this->getBankList();
        $title = 'Iprueba';
        //print gettype($bancas);
        $accounts = $this->accounts_array;
        $documentsType = $this->documentsType_array;
        $arguments = array('auth' => self::authentication());
        $resp = $this->client->__call('getBankList', array($arguments));
        // pueba de datos de bancos
        //echo var_dump($resp);
        //echo var_dump($bancas);
        return view('muestra', compact('title','accounts','documentsType', 'bancas'));
    }


    public function procesaCosas(){
        $accountCode = request('accountCode');
        $bankCode = request('bankCode');
        $request = request();
        $people = $this->person($request);

        $title = 'procesa';
        //print gettype($bancas);
        $procesa = $this->wahedGetTransactionInformation($accountCode,$bankCode,$people);
        $arguments = array('auth' => self::authentication(),'transaction' => ($procesa),   );

        $result = $this->client->__call('createTransaction', array($arguments));
        $responseTransactionResult = $result->createTransactionResult;

        if ($responseTransactionResult->returnCode == "SUCCESS")
        /*{
            $bankUrl = $responseTransactionResult->bankURL;
            $transactionID = $responseTransactionResult->transactionID;


            $expiresAt = now()->addHour(1);
            Cache::put('transactionID', $transactionID, $expiresAt);

            return redirect($bankUrl);
        }*/
        print var_dump($arguments);
        print var_dump($responseTransactionResult);
        return view('procesa', compact('title', 'people'));
    }

    /**
     * Representa la visualización del historial de transacciones en el último día
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function recibeCosas(){

        /**
         * Titulo que tendrá la vista
         * @var String $title */
        $title = 'Result - Place to Pay';

        $arguments = array(
            'auth' => self::authentication(),
            'transactionID' => Cache::get('transactionID'),
        );

        if (is_null($arguments['transactionID']))
        {
            $TransactionHistory = null;
            return view('recibe', compact('title', 'TransactionHistory'));
        }

        $resp = $this->client->__call('getTransactionInformation', array($arguments));

        $resp = $resp->getTransactionInformationResult;

        /**
         * Verificar si en Cache NO se tiene historial de transacciones
         */
        if (!Cache::has('TransactionHistory'))
        {
            /** Se almacena en Cache (por un dia) el registro de las transacciones */
            $expiresAt = now()->addDay(1);
            $TransactionHistory = array((string)$resp->transactionID => $resp);
            Cache::put('TransactionHistory', $TransactionHistory,$expiresAt);
        }
        else
        {
            $TransactionHistory = Cache::get('TransactionHistory');


            if (!array_key_exists((string)$resp->transactionID, $TransactionHistory))
            {
                /** Se almacena en Cache (por un dia) el registro de las transacciones */
                $new_transaction = array((string)$resp->transactionID => $resp);
                $TransactionHistory = $TransactionHistory + $new_transaction;
                $expiresAt = now()->addDay(1);
                Cache::put('TransactionHistory', $TransactionHistory,$expiresAt);
            }
        }

        /** Se obtiene el historial de transacciones */
        $TransactionHistory = Cache::get('TransactionHistory');
        $title = 'recibe';
        return view('recibe', compact('title','TransactionHistory'));


    }


    private function getConfig()
    {
        return array(
            "login" => '6dd490faf9cb87a9862245da41170ff2',
            "tran_key" => '024h1IlD',
            "cache" => array(
                "type" => "memcached",
                "memcached" => array(
                    "host" => "127.0.0.1",
                    "port" => "11211"
                )
            )
        );
    }


    public $accounts_array = array(
        array(
            'accountCode' => 0,
            'accountType' => 'Persona',
        ),
        array(
            'accountCode' => 1,
            'accountType' => 'Empresa',
        ),
    );

    public $documentsType_array = array(
        array(
            'documentCode' => 'CC',
            'documentType' => 'Cédula de ciudanía colombiana',
        ),
        array(
            'documentCode' => 'CE',
            'documentType' => 'Cédula de extranjería',
        ),
        array(
            'documentCode' => 'TI',
            'documentType' => 'Tarjeta de identidad',
        ),
        array(
            'documentCode' => 'PPN',
            'documentType' => 'Pasaporte',
        ),
        array(
            'documentCode' => 'NIT',
            'documentType' => 'Número de identificación tributaria',
        ),
        array(
            'documentCode' => 'SSN',
            'documentType' => 'Social Security Number',
        )
    );

    private function person (Request $request)
    {
        $person = array(
            'document' => $request['document'],
            'documentType' => $request['documentType'],
            'firstName' => $request['firstName'],
            'lastName' => $request['lastName'],
            'company' => $request['company'],
            'emailAddress' => $request['emailAddress'],
            'address' => $request['address'],
            'city' => $request['city'],
            'province' => $request['province'],
            'country' => $request['country'],
            'phone' => $request['phone'],
            'mobile' => $request['mobile'],
        );

        return $person;
    }





    public function wahedGetTransactionInformation($accountCode,$bankCode, $people)
    {
        //print("Obtener la informacion de una transacton\n");

        $obj = new SDKPSE($this->getConfig());

        # Crear una transaccion

        # Obtener el codigo del banco [bankCode]




        $payer = $people;
        $buyer = $people;
        $shipping = $people;

        $transaction = array();
        $transaction["bankCode"] = $bankCode;
        $transaction["bankInterface"] = $accountCode;
        $transaction["returnURL"] = url('/recibe');
        $transaction["reference"] = '2017-011212';
        $transaction["description"] = 'Compra de PC';
        $transaction["language"] = 'ES';
        $transaction["currency"] = 'COP';
        $transaction["totalAmount"] = 15000.3;
        $transaction["taxAmount"] = 20.2;
        $transaction["devolutionBase"] = 0.1;
        $transaction["tipAmount"] = 0.1;
        $transaction["payer"] = $payer;
        //$transaction["buyer"] = $buyer;
        $transaction["shipping"] = $shipping;
        $transaction["ipAddress"] = '10.10.1.12';
        $transaction["userAgent"] =
            'Mozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:50.0)Gecko/20100101 Firefox/50.0';
        //$transaction->additionalData = array();
        return $transaction;
/*
        $result = $obj->createTransaction($transaction);
        $this->assertTrue(gettype($result) == 'object');
        print('transactionID: ' . $result->transactionID . "\n");
        print('sessionID: ' . $result->sessionID . "\n");
        print('returnCode: ' . $result->returnCode . "\n");
        print('bankURL: ' . $result->bankURL . "\n");
        print('responseReasonText: ' . $result->responseReasonText . "\n");

        # Obtener la informacion de la transaccion
        $info = $obj->getTransactionInformation($result->transactionID);
        $this->assertTrue(gettype($info) == 'object');
        $this->assertTrue($info->transactionID == $result->transactionID); */

   }

    private function authentication ()
    {
        $login = config('app.pagologin');
        $tranKey = config('app.pagoKey');

        //  Generación de la semilla
        $seed = date('c');

        $tranKey = sha1($seed.$tranKey);

        $auth = array(
            'login' => $login,
            'tranKey' => $tranKey,
            'seed' => $seed,
        );

        return $auth;
    }
}
