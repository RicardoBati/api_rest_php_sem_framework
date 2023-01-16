<?php

namespace Util;

use InvalidArgumentException;

class JsonUtil
{
    public static function tratarCorpoRequisicaoJson()
    {
        try {
            $postJson = json_decode(file_get_contents('php://input'), true);
            
        } catch (\JsonException $th) {
            throw new InvalidArgumentException(ConstantesGenericasUtil::MSG_ERR0_JSON_VAZIO);
        }

        if (is_array($postJson) && count($postJson) > 0) {
            return $postJson;
        }
    }

    public function processarArrayParaRetornar($request)
    {
        $dados = [];
        $dados[ConstantesGenericasUtil::TIPO] = ConstantesGenericasUtil::TIPO_ERRO;


        if ($request) {
            $dados[ConstantesGenericasUtil::TIPO] = ConstantesGenericasUtil::TIPO_SUCESSO; 
            $dados[ConstantesGenericasUtil::RESPOSTA] = $request;
        }

        $this->retornarJson($dados);
    }

    private function retornarJson($array)
    {
        header('Content-Type: application/json');
        header('Cache-Control: no-cache, no-store, must-revalidate');
        header('Access-Control-Allow-Method: GET,POST,PUT,DELETE');

        echo json_encode($array);
        exit;
    }
}

