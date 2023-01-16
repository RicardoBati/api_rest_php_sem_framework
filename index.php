<?php

use Util\ConstantesGenericasUtil;
use Util\JsonUtil;
use Util\RotasUtil;
use Validator\RequestValidator;

include 'bootstrap.php';


try {
    $requestValidator = new RequestValidator(RotasUtil::getRotas());
    $retorno = $requestValidator->processarRequest();

    $jsonUtil = new JsonUtil();
    $jsonUtil->processarArrayParaRetornar($retorno);

} catch (\Throwable $th) {
    echo json_encode([
        ConstantesGenericasUtil::TIPO => ConstantesGenericasUtil::TIPO_ERRO,
        ConstantesGenericasUtil::RESPOSTA => $th->getMessage()
    ], JSON_UNESCAPED_UNICODE);
    exit;
}
