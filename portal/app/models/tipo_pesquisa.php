<?php
class TipoPesquisa extends AppModel {
    
    const PENDENTE = 1;
    const ENCERRADA = 2;
    const CANCELADA = 3;
    const AGUARDANDO_APROVACAO = 4;
    const REGISTRO_BLOQUEADO_PESQUISA = 5;
    const REGISTRO_BLOQUEADO_APROVACAO = 6;

    /**
     * @var TipoPesquisa
     */
    public static $instance;

    /**
     * @return TipoPesquisa
     */
    public static function getInstance()
    {
        if (!isset(self::$instance)) {
            self::$instance = new self;
        }
        return self::$instance;
    }
    
    public $name = 'TipoPesquisa';
    public $useTable = 'tipo_pesquisa';
}
?>