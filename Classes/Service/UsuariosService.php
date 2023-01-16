<?php

namespace Service;

use InvalidArgumentException;
use Repository\UsuariosRepository;
use Util\ConstantesGenericasUtil;

class UsuariosService
{
    public const TABELA = 'usuarios';
    public const RECURSOS_GET = ['listar'];
    public const RECURSOS_DELETE = ['deletar'];
    public const RECURSOS_POST = ['cadastrar'];
    public const RECURSOS_PUT = ['atualizar'];


    
    private array $dados;
    private array $dadosCorpoRequest = [];

    /**
     * @var objetc|UsuariosRepository
     */
    private object $usuariosRepository;

    /**
     * UsuarioService constructor
     * @param array $dados
     */
    public function __construct($dados = [])
    {
        $this->dados = $dados;
        $this->usuariosRepository = new UsuariosRepository();
    }

    public function validarGet()
    {
        $retorno = null;
        $recurso = $this->dados['recurso'];
        if (in_array($recurso, self::RECURSOS_GET, 'strict')) {
            $retorno = ($this->dados['id'] > 0 ? $this->getOneByKey() : call_user_func(array ($this, $recurso) ));
        }else{
            throw new InvalidArgumentException(ConstantesGenericasUtil::MSG_ERRO_RECURSO_INEXISTENTE);
        }

        $this->validarRetornoRequest($retorno);
        
        return $retorno;
    }
    
    private function getOneByKey()
    {
        return $this->usuariosRepository->getMySQL()->getOneByKey(self::TABELA, $this->dados['id']);
    }

    private function listar()
    {
        return $this->usuariosRepository->getMySQL()->getAll(self::TABELA);
    }

    public function validarDelete()
    {
        $retorno = null;
        $recurso = $this->dados['recurso'];
        if (in_array($recurso, self::RECURSOS_DELETE, 'strict')) {
            
            if ($this->dados['id'] <= 0) {
                throw new InvalidArgumentException(ConstantesGenericasUtil::MSG_ERRO_ID_OBRIGATORIO);
            }
            $retorno = call_user_func(array ($this, $recurso));

        }else{
            throw new InvalidArgumentException(ConstantesGenericasUtil::MSG_ERRO_RECURSO_INEXISTENTE);
        }

        $this->validarRetornoRequest($retorno);

        return $retorno;
    }

    private function deletar()
    {
        return $this->usuariosRepository->getMySQL()->delete(self::TABELA, $this->dados['id']);
    }

    /**
     * @param $dadosRequest
     */
    public function setDadosCorpoRequest($dadosRequest)
    {
        $this->dadosCorpoRequest = $dadosRequest;
    }

    public function validarPost()
    {
        $retorno = null;
        $recurso = $this->dados['recurso'];
        if (in_array($recurso, self::RECURSOS_POST, 'strict')) {
            $retorno = call_user_func(array ($this, $recurso));
        }else{
            throw new InvalidArgumentException(ConstantesGenericasUtil::MSG_ERRO_RECURSO_INEXISTENTE);
        }

        $this->validarRetornoRequest($retorno);

        return $retorno;
    }

    private function cadastrar()
    {
        [$login,$senha] = [$this->dadosCorpoRequest['login'], $this->dadosCorpoRequest['senha']];
        if ($login && $senha) {
            if ($this->usuariosRepository->insertUser($login,$senha) > 0) {
                $idInserido = $this->usuariosRepository->getMySQL()->getDb()->lastInsertId();
                $this->usuariosRepository->getMySQL()->getDb()->commit();

                return ['id_inserido' => $idInserido];
            }

            $this->usuariosRepository->getMySQL()->getDb()->rollback();

            throw new InvalidArgumentException(ConstantesGenericasUtil::MSG_ERRO_GENERICO);
        }
        throw new InvalidArgumentException(ConstantesGenericasUtil::MSG_ERRO_LOGIN_SENHA_OBRIGATORIO);
    }

    public function validarPut()
    {
        $retorno = null;
        $recurso = $this->dados['recurso'];

        if (in_array($recurso, self::RECURSOS_PUT, 'strict')) {

            if ($this->dados['id'] <= 0) {
                throw new InvalidArgumentException(ConstantesGenericasUtil::MSG_ERRO_ID_OBRIGATORIO);
            }
            $retorno = call_user_func(array ($this, $recurso));

        }else{
            throw new InvalidArgumentException(ConstantesGenericasUtil::MSG_ERRO_RECURSO_INEXISTENTE);
        }

        $this->validarRetornoRequest($retorno);

        return $retorno;
    }

    private function atualizar()
    {
        if ($this->usuariosRepository->updateUser($this->dados['id'], $this->dadosCorpoRequest) > 0) {
            $this->usuariosRepository->getMySQL()->getDb()->commit();
            return ConstantesGenericasUtil::MSG_ATUALIZADO_SUCESSO;
        }

        $this->usuariosRepository->getMySQL()->getDb()->rollback();
        throw new InvalidArgumentException(ConstantesGenericasUtil::MSG_ERRO_NAO_AFETADO);
    }

    private function validarRetornoRequest($request)
    {

        if ($request == null) {
            throw new InvalidArgumentException(ConstantesGenericasUtil::MSG_ERRO_GENERICO);
        }
    }
}
