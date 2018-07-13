<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Mockery\Exception;
use function PHPSTORM_META\type;
use SoapClient;
use Illuminate\Support\Facades\Cache;


use PlaceToPay\SDKPSE\Helpers\Helper;
use PlaceToPay\SDKPSE\Helpers\Validate;
//use PlaceToPay\SDKPSE\Cache\Cache;
use PlaceToPay\SDKPSE\Structures\Authentication;

use PlaceToPay\SDKPSE\SDKPSE;
use PlaceToPay\SDKPSE\Structures\PSETransactionRequest;

/**
 * @property SoapClient clientSOAP
 */
class PagosController extends Controller
{
/*
    // consulta con soap
    public function __construct()
    {
        $this->clientSOAP = new SoapClient(config('app.pagowsdl'), array('trace' => true));
        $this->clientSOAP->__setLocation(config('app.pagolocation'));
    }
*/

    /**
     * $soapClient Contiene la instancia del objeto de \SoapClient
     * @var \SoapClient
     */
    /*
    private $soapClient;

    function __construct($config)
    {
        $this->config = $config;
        $this->soapClient =
            new SoapClient(self::$WSDL, array('encoding' => self::$ENCODING));
    }
*/


    public function wahedGetBankList()
    {
        //print("Obtener la lista de los bancos\n");

        $obj = new SDKPSE($this->getConfig());
        $banks = $obj->getBankList();
        return ($banks);
        //var_dump($banks);
    }






    public function muestraCosas(){

        //$obj = new PagosController;
        $bancas = $this->wahedGetBankList();
        //$bancas = $this->getBankList();
        $title = 'Iprueba';
        //print gettype($bancas);
        $accounts = $this->accounts_array;
        $documentsType = $this->documentsType_array;
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

        return view('showprocesa', compact('title', 'people'));
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
        print("Obtener la informacion de una transacton\n");

        $obj = new SDKPSE($this->getConfig());

        # Crear una transaccion

        # Obtener el codigo del banco [bankCode]




        $payer = $people;
        $buyer = $people;
        $shipping = $people;

        $transaction = new PSETransactionRequest();
        $transaction->bankCode = $bankCode;
        $transaction->bankInterface = $accountCode;
        $transaction->returnURL = '/final';
        $transaction->reference = '2017-011212';
        $transaction->description = 'Se realiza la compra de un pc';
        $transaction->language = 'ES';
        $transaction->currency = 'COP';
        $transaction->totalAmount = 1500000;
        $transaction->taxAmount = 200000;
        $transaction->devolutionBase = 0;
        $transaction->tipAmount = 0;
        $transaction->payer = $payer;
        $transaction->buyer = $buyer;
        $transaction->shipping = $shipping;
        $transaction->ipAddress = '10.10.1.12';
        $transaction->userAgent =
            'Mozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:50.0)Gecko/20100101 Firefox/50.0';
        $transaction->additionalData = array();

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
        $this->assertTrue($info->transactionID == $result->transactionID);
    }
}
