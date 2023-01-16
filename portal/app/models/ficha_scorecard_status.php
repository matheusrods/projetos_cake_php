<?php
class FichaScorecardStatus extends AppModel {
    var $name     = 'FichaScorecardStatus';
    var $useTable = false;
    //Novos valores de status
    const CADASTRADA   = 1;//No Antigo era o 1
    const A_PESQUISAR  = 2;//No Antigo era o 2
    const EM_PESQUISA  = 3;//No Antigo era o 2
    const PENDENTE     = 4;//No Antigo era o 5
    const A_APROVAR    = 5;//No Antigo era o 3
    const EM_APROVACAO = 6;//No Antigo era o 3
    const FINALIZADA   = 7;//No Antigo era o 4
    const RENOVADA     = 8;//No Antigo era o 8
    
    var $descricoes    = array(
        self::CADASTRADA   => 'Ficha Cadastrada',
        self::A_PESQUISAR  => 'Pesquisar',
        self::EM_PESQUISA  => 'Em Pesquisa',
        self::PENDENTE     => 'Pendente',
        self::A_APROVAR    => 'Aprovar',
        self::EM_APROVACAO => 'Pendente',
        self::FINALIZADA   => 'Finalizada',
        self::RENOVADA     => 'Renovada'        
    );

    public static function descricao($status) {
        return ClassRegistry::init('FichaScorecardStatus')->descricoes[$status];
    }
}