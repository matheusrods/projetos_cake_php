<?php
class TipoPerfil extends AppModel {
    //usuario
    var $name = 'TipoPerfil';
    var $tableSchema = 'dbo';
    var $databaseTable = 'RHHealth';
    var $useTable = 'tipos_perfis';
    var $primaryKey = 'codigo';
    var $actsAs = array('Secure');

    const CREDENCIAMENTO = 1;
    const CLIENTE = 2;
    const FORNECEDOR = 3;
    const INTERNO = 5;
    const TODOSBEM = 9;
    const INTERNOTERCEIROS = 10;

    function listar($sem_tipo_de_sistema = true){
        $fields = array('codigo','descricao');
        $conditions = array();
        if ($sem_tipo_de_sistema) {
            $conditions[] = array('not' => array('codigo' => self::CREDENCIAMENTO));
        }
        $order = array('descricao');
    	return $this->find('list',compact('fields', 'conditions', 'order'));
    }

    function verificaTipoPerfil($usuario){
        $usuario = $usuario['Usuario'];
        return ($usuario['codigo_cliente']) ? TipoPerfil::CLIENTE : TipoPerfil::INTERNO ;
    }
    
    function carregaFiltrosPorTipoPerfil(&$data,$authUsuario,$seguradora = 'seguradora',$corretora = 'corretora',$filial = 'filial'){
        $Seguradora = ClassRegistry::init('Seguradora');
        $Corretora = ClassRegistry::init('Corretora');
        $EnderecoRegiao = ClassRegistry::init('EnderecoRegiao');
        
        if(isset($authUsuario['Usuario'])){
            if(isset($authUsuario['Usuario']['codigo_seguradora']) && !empty($authUsuario['Usuario']['codigo_seguradora'])){
                $data['codigo_'.$seguradora] = $authUsuario['Usuario']['codigo_seguradora'];
                $nomeSeguradora = $Seguradora->carregar($authUsuario['Usuario']['codigo_seguradora']);
                $data['descricao_'.$seguradora] = $nomeSeguradora['Seguradora']['nome'];
            }
            if(isset($authUsuario['Usuario']['codigo_corretora']) && !empty($authUsuario['Usuario']['codigo_corretora'])){
                $data['codigo_'.$corretora] = $authUsuario['Usuario']['codigo_corretora'];
                $nomeCorretora = $Corretora->carregar($authUsuario['Usuario']['codigo_corretora']);
                $data['descricao_'.$corretora] = $nomeCorretora['Corretora']['nome'];
            }
            if(isset($authUsuario['Usuario']['codigo_filial']) && !empty($authUsuario['Usuario']['codigo_filial'])){
                $data['codigo_'.$filial] = $authUsuario['Usuario']['codigo_filial'];
                $nomeFilial = $EnderecoRegiao->carregar($authUsuario['Usuario']['codigo_filial']);
                $data['descricao_'.$filial] = $nomeFilial['EnderecoRegiao']['descricao'];
            }
        }
    }
}
?>