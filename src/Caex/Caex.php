<?php 
namespace Caex;

use SoapClient;

class Caex
{
    private $user;
    private $key;
    public $production = false;
    public $CodigoCredito="";
    public $tipoServicio="1";
    private $sandbox_service = "http://wsqa.caexlogistics.com:1880/wsCAEXLogisticsSB/wsCAEXLogisticsSB.asmx?wsdl";
    private $prod_service = "https://ws.caexlogistics.com/wsCAEXLogisticsSB/wsCAEXLogisticsSB.asmx?wsdl";
    private $client;
    function __construct($user, $key, $CodigoCredito ='') {
        $this->user = $user;
        $this->key = $key;
        $this->CodigoCredito = $CodigoCredito;
        if($this->production){
            $this->client = new SoapClient($this->prod_service);
        }else {
            $this->client = new SoapClient($this->sandbox_service,[
                'trace'=>1
            ]);
        }
    }

    /**
     * @return array
     * Obtiene el listado de departamentos
     */
    public function ObtenerDepartamentos()
    {
        $params = [
            "Autenticacion" => [
                "Login"=>$this->user,
                "Password"=>$this->key
            ]
        ];
        $response = $this->client->__soapCall('ObtenerListadoDepartamentos', array($params))->ResultadoObtenerDepartamentos->ListadoDepartamentos->Departamento;
        if($response==null){
            $response = [];
        }
        return $response;
    }

    /**
     * @return array
     * Obtiene el listado de municipios
     */
    public function ObtenerMunicipios()
    {
        $params = [
            "Autenticacion" => [
                "Login"=>$this->user,
                "Password"=>$this->key
            ]
        ];
        $response = $this->client->__soapCall('ObtenerListadoMunicipios', array($params))->ResultadoObtenerMunicipios->ListadoMunicipios;
        if($response==null){
            $response = [];
        }else{
            $response = $response->Municipio;
        }
        return $response;
    }

    /**
     * @return array
     * Obtiene el listado de poblados
     */
    public function ObtenerPoblados()
    {
        $params = [
            "Autenticacion" => [
                "Login"=>$this->user,
                "Password"=>$this->key
            ]
        ];
        $mis_poblados = [];
        $response = $this->client->__soapCall('ObtenerListadoPoblados', array($params))->ResultadoObtenerPoblados->ListadoPoblados;
        if($response==null){
            return [];
        }else{
            return $response->Poblado;
        }
    }

    /**
     * @return array
     * Obtiene el listado de piezas que maneja cargo expreso
     */
    public function ObtenerTiposPiezas()
    {
        $params = [
            "Autenticacion" => [
                "Login"=>$this->user,
                "Password"=>$this->key
            ]
        ];
        $response = $this->client->__soapCall('ObtenertiposPiezas', array($params))->ResultadoObtenerPiezas->ListadoPiezas;
        if($response==null){
            $response = [];
        }
        return $response;
    }

    /**
     * @return array
     * Obtiene el precio de envio del paquete
     */
    public function ObtenerTarifaEnvio($pobladoDestino, $CodigoPieza, $PesoTotal,$tipoServicio=false)
    {
        $PesoTotal = ceil($PesoTotal);
        $CodigoCredito = $this->CodigoCredito;
        if(!$tipoServicio){
            $tipoServicio = $this->tipoServicio;
        }
        $params = [
            "Autenticacion" => [
                "Login"=>$this->user,
                "Password"=>$this->key
            ],
            "DatosEnvio"=>[
                "CodigoPobladoDestino"=>$pobladoDestino,
                "CodigoPieza"=>$CodigoPieza,
                "TipoServicio"=>$tipoServicio,
                "PesoTotal"=>$PesoTotal,
                "CodigoCredito"=>$CodigoCredito
            ]
        ];
        $response = $this->client->__soapCall('ObtenerTarifaEnvio', array($params))->ResultadoObtenerTarifa;
        if($response==null){
            $response = [];
        }
        return $response;
    }

    /**
     * @return array
     * Genera la Guia para que Cargo Expreso recoja el paquete
     */
    public function GenerarGuia($Datosguia)
    {
     
        foreach($Datosguia['Piezas'] as $key => $pieza){
            $Datosguia['Piezas'][$key]['PesoPieza'] = ceil($pieza['PesoPieza']);
        }
        if(!isset($Datosguia["CodigoCredito"])){
            if(!empty($this->CodigoCredito)){
                $Datosguia["CodigoCredito"] = $this->CodigoCredito;
            };
            
        }
        if(!isset($Datosguia["TipoServicio"])){
            $Datosguia["TipoServicio"] = $this->tipoServicio;
        }
        $params = [
            "Autenticacion" => [
                "Login"=>$this->user,
                "Password"=>$this->key
            ],
            "ListaRecolecciones"=>[
                "DatosRecoleccion"=>[
                    $Datosguia
                ],
            ]
        ];
        $response = $this->client->__soapCall('GenerarGuia', array($params))->ResultadoGenerarGuia->ListaRecolecciones->DatosRecoleccion;
        if($response==null){
            $response = [];
        }
        return $response;
    }

    /**
     * @return array
     * Obtiene el precio de envio del paquete
     */
    public function AnularGuia($numeroGuia)
    {
    
        $params = [
            "Autenticacion" => [
                "Login"=>$this->user,
                "Password"=>$this->key
            ],
            "NumeroGuia"=>$numeroGuia,
        ];
        $response = $this->client->__soapCall('AnularGuia', array($params));
        if($response==null){
            $response = [];
        }
        return $response;
    }

    public function hello()
    {
        return 'Hello World, Caex!'.$this->user;
    }
}