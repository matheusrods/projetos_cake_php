<?php
class LogIntegracao extends AppModel {
    var $name = 'LogIntegracao';
    var $tableSchema = 'dbo';
    var $databaseTable = 'RHHealth';
    var $useTable = 'logs_integracoes';
    var $primaryKey = 'codigo';
    var $actsAs = array('Secure');

    function converteFiltrosEmConditions($filtros) {
    	$conditions = array();
    	if (isset($filtros['codigo_cliente']) && !empty($filtros['codigo_cliente'])) 
    		$conditions['LogIntegracao.codigo_cliente'] = $filtros['codigo_cliente'];
    	if (isset($filtros['arquivo']) && !empty($filtros['arquivo'])) 
    		$conditions['LogIntegracao.arquivo LIKE'] = '%'.$filtros['arquivo'].'%';
        if (isset($filtros['hora_inicial']) && !empty($filtros['hora_inicial']) && $filtros['hora_inicial'] <> '__:__')
            $filtros['data_inicial'] .= ' '.$filtros['hora_inicial'];
        else
            $filtros['data_inicial'] .= ' 00:00:00';

        if (isset($filtros['hora_final']) && !empty($filtros['hora_final']) && $filtros['hora_final'] <> '__:__')
            $filtros['data_final'] .= ' '.$filtros['hora_final'].':59';
        else
            $filtros['data_final'] .= ' 23:59:59';

    	if ((isset($filtros['data_inicial']) && !empty($filtros['data_inicial'])) && (isset($filtros['data_final']) && !empty($filtros['data_final'])))
    		$conditions['LogIntegracao.data_inclusao BETWEEN ? AND ?'] = array(AppModel::dateToDbDate2($filtros['data_inicial']),AppModel::dateToDbDate2($filtros['data_final']));
    	
        if (isset($filtros['status']) && $filtros['status'] != '') 
            $conditions['LogIntegracao.status'] = $filtros['status'];

        if (isset($filtros['descricao']) && !empty($filtros['descricao'])) 
            $conditions['LogIntegracao.descricao LIKE'] = '%'.$filtros['descricao'].'%';

        if (isset($filtros['tipo_operacao']) && !empty($filtros['tipo_operacao'])) 
            $conditions['LogIntegracao.tipo_operacao'] = $this->deParaTipoOperacaoLog($filtros['tipo_operacao']);

        if (isset($filtros['sistema_origem']) && !empty($filtros['sistema_origem'])) 
            $conditions['LogIntegracao.sistema_origem'] = $filtros['sistema_origem'];

        if (isset($filtros['placa']) && !empty($filtros['placa'])) {
            $conditions['LogIntegracao.placa_cavalo'] = str_replace('-', '', $filtros['placa']);
        }
        if (isset($filtros['numero_pedido']) && !empty($filtros['numero_pedido'])) 
            $conditions['LogIntegracao.numero_pedido'] = $filtros['numero_pedido'];

        if (isset($filtros['loadplan']) && !empty($filtros['loadplan'])) 
            $conditions['LogIntegracao.loadplan'] = $filtros['loadplan'];

        if (isset($filtros['cpf']) && !empty($filtros['cpf'])) {
            $conditions['LogIntegracao.cpf_motorista'] = str_replace('-', '', $filtros['cpf']);
        }

        return $conditions;
    }

    function converteFiltrosIntegracaoEmConditions($filtros){
        $conditions = array();

        if( !empty($filtros['data_inicio']) && !empty($filtros['data_fim']) ){
            $data_inicio = AppModel::dateToDbDate($filtros["data_inicio"].' 00:00:00');
            $data_fim = AppModel::dateToDbDate($filtros["data_fim"].' 23:59:59');
            $conditions[] = "LogIntegracao.data_inclusao BETWEEN '".$data_inicio."' AND '".$data_fim."'";
        }

        if( !empty($filtros['arquivo']) ){
            $conditions['arquivo like'] = '%'.$filtros['arquivo'].'%'; 
        }

        if( !empty($filtros['conteudo']) ){
            $conditions['conteudo like'] = '%'.$filtros['conteudo'].'%';
        }

        if( !empty($filtros['sistema_origem']) ){
            $conditions['sistema_origem like'] = '%'.$filtros['sistema_origem'].'%';
        }

        return $conditions;
    }

    function montaOptionsSitemaOrigem(){
        $this->virtualFields = array(
            "sistema_origem" => "DISTINCT(sistema_origem)"
        );

        return $this->find('list',array('fields' => array('sistema_origem','sistema_origem')));
    }

    function listarArquivos($filtros){
        $conditions = array();

        if (isset($filtros['hora_inicial']) && !empty($filtros['hora_inicial']))
            $data_inicial = $filtros['data_inicial'].' '.$filtros['hora_inicial'];
        else
            $data_inicial = $filtros['data_inicial'] .= ' 00:00:00';

        if (isset($filtros['hora_final']) && !empty($filtros['hora_final']) && $filtros['hora_final'] <> '__:__')
            $data_final = $filtros['data_final'].' '.$filtros['hora_final'];
        else
            $data_final = $filtros['data_final'] .= ' 23:59:59';       

        $conditions['data_inclusao BETWEEN ? AND ?'] = array(AppModel::dateToDbDate2($data_inicial),AppModel::dateToDbDate2($data_final));
        $conditions['status'] = $filtros['status'];

        return $this->find('list',array('fields'=>'arquivo','conditions'=>$conditions));
    }

    function detalhesIntegracoes($filtros){
        $result = $this->find('all',array(
            'fields' => array(
                "SUM( CASE WHEN LogIntegracao.status = 0 THEN 1 ELSE 0 END ) AS integrada",
                "SUM( CASE WHEN LogIntegracao.status = 1 THEN 1 ELSE 0 END ) AS nao_integrada",
                "SUM( CASE WHEN LogIntegracao.tipo_operacao = 'I' THEN 1 ELSE 0 END ) AS inclusao",
                "SUM( CASE WHEN LogIntegracao.tipo_operacao = 'A' THEN 1 ELSE 0 END ) AS alteracao",
                "SUM( CASE WHEN LogIntegracao.tipo_operacao = 'C' THEN 1 ELSE 0 END ) AS cancelamento",
            ),
            'conditions' => $this->converteFiltrosEmConditions($filtros),
        ));

        return $result;
    }

    public function deParaTipoOperacaoLog($tipo){
        switch ($tipo) {
            case 'P': return 'I';
                break;
            case 'R': return 'A';
                break;          
            default: return $tipo;
                break;
        }
    }

    public function listarSistemaOrigem(){
        $sistema_origem = array();
        $origens = $this->find('all',array('fields'=>'DISTINCT sistema_origem', 'order' => 'sistema_origem'));
        foreach ($origens as $key => $value) {
            $sistema_origem[$value['LogIntegracao']['sistema_origem']] = $value['LogIntegracao']['sistema_origem'];
        }
        return $sistema_origem;
    }

    public function carregarPorLoadplan($loadplan){
    	$conditions = array('loadplan' => $loadplan);
    	
    	$order 		= array('data_inclusao DESC');
    	return $this->find('first',compact('conditions','order'));
    }

    public function verificarIntegracaoGpa(){
        $fields = array(
            "LogIntegracao.data_inclusao",
            "LogIntegracao.sistema_origem",
            "CASE 
                WHEN MAX(LogIntegracao.data_inclusao) >= (GETDATE() - '00:30') 
                THEN 'funcionando' 
                ELSE 'fora' 
            END AS status",
        );
        $group = array(
            "LogIntegracao.data_inclusao",
            "LogIntegracao.sistema_origem",
        );
        $conditions = array(
            'LogIntegracao.sistema_origem' => 'SmGpa_FTP',
        );
        $order = array(
            'LogIntegracao.data_inclusao DESC',
        );

        return $this->find('first', compact('conditions','fields','group','order'));
    }

    public function FiltrosConditions($filtros){
        $conditions = array();

        if( !empty($filtros['data_inicio']) && !empty($filtros['data_fim']) ){
            $data_inicio = AppModel::dateToDbDate($filtros["data_inicio"].' 00:00:00');
            $data_fim = AppModel::dateToDbDate($filtros["data_fim"].' 23:59:59');
            $conditions[] = "LogIntegracao.data_inclusao BETWEEN '".$data_inicio."' AND '".$data_fim."'";
        }

        if( !empty($filtros['arquivo']) ){
            $conditions['arquivo like'] = '%'.$filtros['arquivo'].'%'; 
        }

        if( !empty($filtros['conteudo']) ){
            $conditions['conteudo like'] = '%'.$filtros['conteudo'].'%';
        }

        if( !empty($filtros['sistema_origem']) ){
            $conditions['sistema_origem like'] = '%'.$filtros['sistema_origem'].'%';
        }

        if( !empty($filtros['empresa']) ){
            $conditions['MultiEmpresa.codigo'] = $filtros['empresa'];
        }

        if( !empty($filtros['codigo_cliente']) ){
            $conditions['ClienteFuncionario.codigo_cliente'] = $filtros['codigo_cliente'];
        }

        if( !empty($filtros['codigo_cliente_alocacao']) ){
            $conditions['FuncionarioSetorCargo.codigo_cliente_alocacao'] = $filtros['codigo_cliente_alocacao'];
        }

        if( !empty($filtros['codigo_setor']) ){
            $conditions['Setor.codigo'] = $filtros['codigo_setor'];
        }

        if( !empty($filtros['codigo_cargo']) ){
            $conditions['Cargo.codigo'] = $filtros['codigo_cargo'];
        }

        if( !empty($filtros['codigo_funcionario']) ){
            $conditions['Funcionario.codigo'] = $filtros['codigo_funcionario'];
        }

        if( !empty($filtros['cpf_funcionario']) ){
             $conditions['Funcionario.cpf like'] = '%'.$filtros['cpf_funcionario'].'%';
        }

        if( !empty($filtros['nome_funcionario']) ){
             $conditions['Funcionario.nome like'] = '%'.$filtros['nome_funcionario'].'%';
        }

        if( !empty($filtros['tabela']) ){

            if($filtros['tabela'] == 'certificado'){
                $conditions['LogIntegracao.arquivo like'] = '%API_TECNOSPEED_CERTIFICADO%';                
            } else {
                $conditions['IntEsocialTipoEvento.codigo'] = $filtros['tabela'];                
            }
        }

        if( !empty($filtros['certificado_descricao']) ){
            $conditions['IntEsocialCertificado.nome_arquivo like'] = '%'.$filtros['certificado_descricao'].'%';
        }

        return $conditions;
    }

    public function getLogIntegracaoEsocial(array $conditions = array(), $pagination = false){

        $fields = array(
            'LogIntegracao.codigo',
            'LogIntegracao.data_inclusao',
            'LogIntegracao.arquivo',
            'LogIntegracao.descricao',
            'LogIntegracao.sistema_origem',
            'Cliente.nome_fantasia',
            'UsuarioInclusao.nome',
            'IntEsocialCertificado.codigo',
            'IntEsocialCertificado.nome_arquivo',
            'IntEsocialTipoEvento.codigo',
            'IntEsocialTipoEvento.descricao',
            ' CASE WHEN LogIntegracao.arquivo = \'API_TECNOSPEED_CERTIFICADO\' THEN \'Certificado Digital\'
            ELSE [IntEsocialTipoEvento].descricao end as arquivo'
        );

        $joins = array(
            array(
                'table' => 'int_esocial_eventos',
                'alias' => 'IntEsocialEventos',
                'type' => 'INNER',
                'conditions' => 'LogIntegracao.foreign_key = IntEsocialEventos.codigo'
            ),
            array(
                'table' => 'cliente',
                'alias' => 'Cliente',
                'type' => 'INNER',
                'conditions' => 'IntEsocialEventos.codigo_cliente = Cliente.codigo'
            ),
            array(
                'table' => 'funcionario_setores_cargos',
                'alias' => 'FuncionarioSetorCargo',
                'type' => 'INNER',
                'conditions' => 'IntEsocialEventos.codigo_funcionario_setor_cargo = FuncionarioSetorCargo.codigo'
            ),
            array(
                'table' => 'setores',
                'alias' => 'Setor',
                'type' => 'INNER',
                'conditions' => 'Setor.codigo = FuncionarioSetorCargo.codigo_setor'
            ),
            array(
                'table' => 'cargos',
                'alias' => 'Cargo',
                'type' => 'INNER',
                'conditions' => 'Cargo.codigo = FuncionarioSetorCargo.codigo_cargo'
            ),
            array(
                'table' => 'cliente_funcionario',
                'alias' => 'ClienteFuncionario',
                'type' => 'INNER',
                'conditions' => 'FuncionarioSetorCargo.codigo_cliente_funcionario = ClienteFuncionario.codigo AND IntEsocialEventos.codigo_cliente_funcionario = ClienteFuncionario.codigo'
            ),
            array(
                'table' => 'funcionarios',
                'alias' => 'Funcionario',
                'type' => 'INNER',
                'conditions' => 'Funcionario.codigo = ClienteFuncionario.codigo_funcionario'
            ),
            array(
                'table' => 'int_esocial_tipo_evento',
                'alias' => 'IntEsocialTipoEvento',
                'type' => 'INNER',
                'conditions' => 'IntEsocialTipoEvento.codigo = IntEsocialEventos.codigo_int_esocial_tipo_evento'
            ),
            array(
                'table' => 'multi_empresa',
                'alias' => 'MultiEmpresa',
                'type' => 'INNER',
                'conditions' => 'MultiEmpresa.codigo = IntEsocialEventos.codigo_empresa'
            ),
            array(
                'table' => 'usuario',
                'alias' => 'UsuarioInclusao',
                'type' => 'LEFT',
                'conditions' => 'LogIntegracao.codigo_usuario_inclusao = UsuarioInclusao.codigo'
            ),
            array(
                'table' => 'int_esocial_certificado',
                'alias' => 'IntEsocialCertificado',
                'type' => 'LEFT',
                'conditions' => 'IntEsocialCertificado.codigo = IntEsocialEventos.codigo_int_esocial_certificado'
            ),
        );

        $order = 'LogIntegracao.data_inclusao DESC';

        if($pagination){
            $paginate = array(
                'fields' => $fields,
                'joins' => $joins,
                'conditions' => $conditions,
                'limit' => 50,
                'order' => $order,
                'recursive' => -1
            );          
            return $paginate;
        } else {
            return $this->find('sql', array('joins' => $joins, 'fields' => $fields, 'conditions' => $conditions));
        }
    }//fim

    public function getLogIntegracao(array $conditions = array(), $pagination = false){

        $fields = array(
            'LogIntegracao.codigo','LogIntegracao.data_inclusao','LogIntegracao.arquivo','LogIntegracao.descricao','LogIntegracao.sistema_origem','Cliente.nome_fantasia','UsuarioInclusao.nome'
        );

        $joins = array(
            array(
                'table' => 'cliente',
                'alias' => 'Cliente',
                'type' => 'LEFT',
                'conditions' => 'LogIntegracao.codigo_cliente = Cliente.codigo'
            ),
            array(
                'table' => 'usuario',
                'alias' => 'UsuarioInclusao',
                'type' => 'LEFT',
                'conditions' => 'LogIntegracao.codigo_usuario_inclusao = UsuarioInclusao.codigo'
            ),
        );

        $order = 'LogIntegracao.data_inclusao DESC';

        if($pagination){
            $paginate = array(
                'fields' => $fields,
                'joins' => $joins,
                'conditions' => $conditions,
                'limit' => 50,
                'order' => $order,
                'recursive' => -1
            );          
            return $paginate;
        } else {
            return $this->find('sql', array('joins' => $joins, 'fields' => $fields, 'conditions' => $conditions));
        }
    }
}
?>