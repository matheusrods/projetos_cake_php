<?php
class Chamado extends AppModel
{
    public $name = 'Chamado';
    public $tableSchema = 'dbo';
    public $databaseTable = 'RHHealth';
    public $useTable = 'chamados';
    public $primaryKey = 'codigo';
    // public $actsAs = array('Secure','Loggable' => array('foreign_key' => 'codigo_chamados'));
    public $actsAs = array('Secure');

    public $validate = array(
        'descricao' => array(
            'rule' => 'notEmpty',
            'message' => 'Informe o descrição.',
            'required' => true
        ),
        'codigo_chamado_tipo' => array(
            'rule' => 'notEmpty',
            'message' => 'Informe o tipo do chamado',
            'required' => true
        ),
        'codigo_cliente' => array(
            'rule' => 'notEmpty',
            'message' => 'Informe o cliente',
            'required' => true
        ),
        'codigo_chamado_status' => array(
            'rule' => 'notEmpty',
            'message' => 'Informe o status do chamado',
            'required' => true
        ),
    );
    
    function getByCodigo($codigo) {
        $fields = array(
            'Chamado.codigo',
            'Chamado.descricao',
            'Chamado.codigo_chamado_tipo',
            'Chamado.codigo_cliente',
            'Chamado.codigo_usuario_inclusao',
            'Chamado.codigo_usuario_alteracao',
            'Chamado.data_inclusao',
            'Chamado.data_alteracao',
            'Chamado.codigo_chamado_status',
            'Chamado.responsavel',
            'Chamado.descricao_levantamento',

            'Cliente.codigo',
            'Cliente.razao_social',
        );

        $conditions = array('Chamado.codigo' => $codigo);
        
        $joins  = array(
            array(
                'table' => 'RHHealth.dbo.cliente',
                'alias' => 'Cliente',
                'type' => 'LEFT',
                'conditions' => 'Cliente.codigo = Chamado.codigo_cliente',
            )
        );

        $chamado = $this->find('first', 
            array(
                'fields' => $fields,
                'conditions' => $conditions,
                'joins' => $joins,
                // 'limit' => 50,
                // 'order' => 'Chamado.codigo',
            )
        );

        if(empty($chamado)){
            return array();
        }

        $chamado['Chamado']['codigo_cliente_name'] = $chamado['Cliente']['razao_social'];

        return $chamado;
	}

    public function getListaChamados($filtros = null)
    {
        $fields = array(
            'Chamado.codigo',
            'Chamado.descricao',
            'Chamado.codigo_chamado_tipo',
            'Chamado.responsavel',
            'Chamado.descricao_levantamento',
            'ChamadoTipo.codigo',
            'ChamadoTipo.descricao',
            'ChamadoStatus.codigo',
            'ChamadoStatus.descricao',
            'Responsavel.codigo',
            'Responsavel.nome'
        );

        $conditions = $this->converteFiltroEmCondition($filtros);
        
        $joins  = array(
            array(
              'table' => 'RHHealth.dbo.chamados_tipo',
              'alias' => 'ChamadoTipo',
              'type' => 'LEFT',
              'conditions' => 'ChamadoTipo.codigo = Chamado.codigo_chamado_tipo',
            ),
            array(
              'table' => 'RHHealth.dbo.chamados_status',
              'alias' => 'ChamadoStatus',
              'type' => 'LEFT',
              'conditions' => 'ChamadoStatus.codigo = Chamado.codigo_chamado_status',
            ),
            array(
                'table' => 'RHHealth.dbo.usuario',
                'alias' => 'Responsavel',
                'type' => 'LEFT',
                'conditions' => 'Responsavel.codigo = Chamado.responsavel',
            ),
        );

        $chamados = array(
            'fields' => $fields,
            'conditions' => $conditions,
            'joins' => $joins,
            'limit' => 50,
            'order' => 'Chamado.codigo desc',
        );

        return $chamados;
    }

    public function converteFiltroEmCondition($data)
    {
        $conditions = array();

        if (!empty($data['codigo'])) {
            $conditions['Chamado.codigo'] = $data['codigo'];
        }

        if (!empty($data ['descricao'])) {
            $conditions ['Chamado.descricao LIKE'] = '%' . $data ['descricao'] . '%';
        }

        if (!empty($data['codigo_chamado_tipo'])) {
            $conditions['Chamado.codigo_chamado_tipo'] = $data['codigo_chamado_tipo'];
        }

        if (!empty($data['codigo_cliente'])) {
            $conditions['Chamado.codigo_cliente'] = $data['codigo_cliente'];
        }

        if (!empty($data['codigo_usuario_inclusao'])) {
            $conditions['Chamado.codigo_usuario_inclusao'] = $data['codigo_usuario_inclusao'];
        }

        if (!empty($data['codigo_usuario_alteracao'])) {
            $conditions['Chamado.codigo_usuario_alteracao'] = $data['codigo_usuario_alteracao'];
        }

        if (!empty($data['data_alteracao'])) {
            $conditions['Chamado.data_alteracao'] = $data['data_alteracao'];
        }

        if (!empty($data['codigo_chamado_status'])) {
            $conditions['Chamado.codigo_chamado_status'] = $data['codigo_chamado_status'];
        }

        return $conditions;
    }

    public function incluir($dados) {
		try {
            $this->query('begin transaction');

            $chamadoStatusModel = ClassRegistry::init('ChamadoStatus');
            $LevantamentoChamadoModel = ClassRegistry::init('LevantamentoChamado');
            $LevantamentoChamadoStatusModel = ClassRegistry::init('LevantamentoChamadoStatus');
            $processoModel = ClassRegistry::init('Processo');
            
            $dados['Chamado']['codigo_chamado_status'] = ChamadoStatus::ABERTO;
            $cadastroChamado = parent::incluir($dados['Chamado']);            
			if(!$cadastroChamado) {
				throw new Exception('Não incluiu o chamado.');
            }

            //Define dados para inserir em LevantamentosChamado
            $dadosLevantamentoChamado = array();
            $dadosLevantamentoChamado['codigo_chamado'] = $this->id;
            $dadosLevantamentoChamado['codigo_cliente'] = $cadastroChamado['Chamado']['codigo_cliente'];
            $dadosLevantamentoChamado['codigo_usuario_inclusao'] = $cadastroChamado['Chamado']['codigo_usuario_inclusao'];
            $dadosLevantamentoChamado['data_inclusao'] = $cadastroChamado['Chamado']['data_inclusao'];
            $dadosLevantamentoChamado['observacao'] = $cadastroChamado['Chamado']['descricao'];
            $dadosLevantamentoChamado['codigo_levantamento_chamado_status'] = LevantamentoChamadoStatus::NAO_INICIADO;
            $dadosLevantamentoChamado['descricao'] = $cadastroChamado['Chamado']['descricao_levantamento'];

            if(!$LevantamentoChamadoModel->incluir($dadosLevantamentoChamado)) {
				throw new Exception('Não incluiu o levantamento do chamado.');
            }

            //Define dados para inserir em Processos
            $dadosProcessos = array();
            $dadosProcessos['codigo_levantamento_chamado'] = $LevantamentoChamadoModel->id;
            $dadosProcessos['codigo_cliente'] = $cadastroChamado['Chamado']['codigo_cliente'];
            $dadosProcessos['codigo_usuario_inclusao'] = $cadastroChamado['Chamado']['codigo_usuario_inclusao'];
            $dadosProcessos['data_inclusao'] = $cadastroChamado['Chamado']['data_inclusao'];

            if(!$processoModel->incluir($dadosProcessos)) {
				throw new Exception('Não incluiu o processo.');
            }
            
			$this->commit();
			return true;
		} catch(Exception $e) {
			$this->rollback();
			return false;			
		}
	}
}
