<?php

namespace Validator;

use InvalidArgumentException;
use Repository\TokensAutorizadosRepository;
use Service\UsuariosService;
use Util\ConstantesGenericasUtil;
use Util\JsonUtil;

class RequestValidator
{
    private $request;
    private array $dadosRequest = [];
    private object $tokensAutorizadosRepository;

    const GET = 'GET';
    const DELETE = 'DELETE';
    const POST = 'POST';
    const PUT = 'PUT';
    const USUARIOS = 'USUARIOS';



    public function __construct($request)
    {
        $this->request = $request;
        $this->tokensAutorizadosRepository = new TokensAutorizadosRepository();

    }

    /**
     * @return string
     */
    public function processarRequest()
    {
        $retorno = ConstantesGenericasUtil::MSG_ERRO_TIPO_ROTA;
        if (in_array($this->request['metodo'], ConstantesGenericasUtil::TIPO_REQUEST, true)) {
            $retorno = $this->direcionarRequest();
        }
        return $retorno;
    }

    private function direcionarRequest()
    {
        if ($this->request['metodo'] !== self::GET && $this->request['metodo'] !== self::DELETE) {
            $this->dadosRequest = JsonUtil::tratarCorpoRequisicaoJson();
        }
        $this->tokensAutorizadosRepository->validarToken(getallheaders()['Authorization']);
        
        $metodo = $this->request['metodo'];
        return call_user_func(array($this, $metodo));
    }

    private function get(){
        $retorno = ConstantesGenericasUtil::MSG_ERRO_TIPO_ROTA;
        if (in_array($this->request['rota'], ConstantesGenericasUtil::TIPO_GET, 'strict')) {
            switch ($this->request['rota']){
                case self::USUARIOS:
                    $usuariosService = new UsuariosService($this->request);
                    $retorno = $usuariosService->validarGet();
                    break;
                default:
                    throw new InvalidArgumentException(ConstantesGenericasUtil::MSG_ERRO_RECURSO_INEXISTENTE);
                
            }
        }
        return $retorno;
    }

    private function delete(){
        $retorno = ConstantesGenericasUtil::MSG_ERRO_TIPO_ROTA;
        if (in_array($this->request['rota'], ConstantesGenericasUtil::TIPO_DELETE, 'strict')) {
            switch ($this->request['rota']){
                case self::USUARIOS:
                    $usuariosService = new UsuariosService($this->request);
                    $retorno = $usuariosService->validarDelete();
                    break;
                default:
                    throw new InvalidArgumentException(ConstantesGenericasUtil::MSG_ERRO_RECURSO_INEXISTENTE);
                
            }
        }
        return $retorno;
    }

    private function post(){
        $retorno = ConstantesGenericasUtil::MSG_ERRO_TIPO_ROTA;
        if (in_array($this->request['rota'], ConstantesGenericasUtil::TIPO_POST, 'strict')) {
            switch ($this->request['rota']){
                case self::USUARIOS:
                    $usuariosService = new UsuariosService($this->request);
                    $usuariosService->setDadosCorpoRequest($this->dadosRequest);
                    $retorno = $usuariosService->validarPost();
                    break;
                default:
                    throw new InvalidArgumentException(ConstantesGenericasUtil::MSG_ERRO_RECURSO_INEXISTENTE);
                
            }
        }
        return $retorno;
    }

    private function put(){
        $retorno = ConstantesGenericasUtil::MSG_ERRO_TIPO_ROTA;
        if (in_array($this->request['rota'], ConstantesGenericasUtil::TIPO_PUT, 'strict')) {
            switch ($this->request['rota']){
                case self::USUARIOS:
                    $usuariosService = new UsuariosService($this->request);
                    $usuariosService->setDadosCorpoRequest($this->dadosRequest);
                    $retorno = $usuariosService->validarPut();
                    break;
                default:
                    throw new InvalidArgumentException(ConstantesGenericasUtil::MSG_ERRO_RECURSO_INEXISTENTE);
                
            }
        }
        return $retorno;
    }
}
