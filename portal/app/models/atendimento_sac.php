<?php
class AtendimentoSac extends AppModel {

    var $name = 'AtendimentoSac';
    var $tableSchema = 'vendas';
    var $databaseTable = 'dbBuonny';
    var $useTable = 'atendimentos_sac';
    var $displayField = 'observacao';
    var $primaryKey = 'codigo'; 
    var $actsAs = array('Secure');
    var $validate = array(
			'codigo_motivo_atendimento' => array(
				'rule' => 'notEmpty',
				'message' => 'Informe o motivo do atendimento.',
			));

    CONST OPERADOR = 1;
    CONST TRANSPORTADOR = 2;
    CONST EMBARCADOR = 3;
    CONST MOTIVO = 4;
    CONST TECNOLOGIA = 5;

    function listarAgrupamentos() {
        return array(
        	self::OPERADOR => 'Operador',
        	self::TRANSPORTADOR => 'Transportador',
        	self::EMBARCADOR => 'Embarcador',
        	self::MOTIVO => 'Motivo',
        	self::TECNOLOGIA => 'Tecnologia',
        );
    }

    function retornaAgrupamento($codigo){
    	switch ($codigo) {
    		case self::OPERADOR:
    			$retorno = 'Operador';
    			break;
    		case self::TRANSPORTADOR:
    			$retorno = 'Transportador';
    			break;
			case self::EMBARCADOR:
				$retorno = 'Embarcador';
				break;
			case self::MOTIVO:
    			$retorno = 'Motivo';
    			break;
    		case self::TECNOLOGIA:
    			$retorno = 'Tecnologia';
    			break;	
    		default:
    			$retorno = 'Codigo não Encontrado';
    			break;
    	}
    	return $retorno;
    }

    function bindUsuario($reset = TRUE){
		$this->bindModel(array(
			'hasOne' => array(
				'Usuario' => array(
					'foreignKey' => false,
					'conditions' => array("Usuario.codigo = AtendimentoSac.codigo_usuario_inclusao"),
					'type' => 'INNER'
				),
			),			
		),$reset);
	}

	function bindMotivoAtendimento($reset = TRUE){
		$this->bindModel(array(
			'hasOne' => array(
				'MotivoAtendimento' => array(
					'foreignKey' => false,
					'conditions' => array('MotivoAtendimento.codigo = AtendimentoSac.codigo_motivo_atendimento'),
					'type' => 'INNER'
				),
			),			
		),$reset);
	}
	function bindRecebsm($reset = TRUE){
		$this->bindModel(array(
			'hasOne' => array(
				'Recebsm' => array(
					'foreignKey' => false,
					'conditions' => array('Recebsm.SM = AtendimentoSac.codigo_sm'),
					'type' => 'LEFT'
				),
			),			
		),$reset);
	}
	function bindEquipamento($reset = TRUE){
		$this->bindModel(array(
			'hasOne' => array(
				'Equipamento' => array(
					'foreignKey' => false,
					'conditions' => array('Equipamento.Codigo = Recebsm.CodEquipamento'),
					'type' => 'LEFT'
				),
			),			
		),$reset);
	}

	function bindClienteEmbarcador($reset = TRUE){
		$this->bindModel(array(
			'hasOne' => array(
				'ClienteEmbarcador' => array(
					'class' => 'Cliente',
					'foreignKey' => false,
					'conditions' => array('ClienteEmbarcador.Codigo = AtendimentoSac.codigo_cliente_embarcador'),
					'type' => 'LEFT'
				),
			),			
		),$reset);
	}

	function bindClienteTransportador($reset = TRUE){
		$this->bindModel(array(
			'hasOne' => array(
				'ClienteTransportador' => array(
					'class' => 'Cliente',
					'foreignKey' => false,
					'conditions' => array('ClienteTransportador.Codigo = AtendimentoSac.codigo_cliente_transportador'),
					'type' => 'LEFT'
				),
			),			
		),$reset);
	}

	public function converteFiltroEmCondition($dados) {
		$conditions = array();

		if (!empty($dados['AtendimentoSac']['codigo_motivo_atendimento'])) {
			$conditions["MotivoAtendimento.codigo"] = $dados['AtendimentoSac']["codigo_motivo_atendimento"];
		}
		if (!empty($dados['AtendimentoSac']['tecnologia'])){
			if($dados['AtendimentoSac']['tecnologia'] == '-1'){
                $conditions['AtendimentoSac.codigo_tecnologia'] = NULL;
			} else {
				$conditions['AtendimentoSac.codigo_tecnologia'] = $dados['AtendimentoSac']['tecnologia'];
			}
		}
		if (!empty($dados['AtendimentoSac']['codigo_sm'])) {
			$conditions["AtendimentoSac.codigo_sm"] = $dados['AtendimentoSac']["codigo_sm"];
		}
		if (!empty($dados['AtendimentoSac']['codigo_transportador'])) {
			if($dados['AtendimentoSac']['codigo_transportador'] == '-1'){
				$conditions["AtendimentoSac.codigo_cliente_transportador"] = NULL;
			}else{
				$conditions["AtendimentoSac.codigo_cliente_transportador"] = $dados['AtendimentoSac']["codigo_transportador"];
			}
		}
		if (!empty($dados['AtendimentoSac']['codigo_embarcador'])) {
			if($dados['AtendimentoSac']['codigo_embarcador'] == '-1'){
				$conditions["AtendimentoSac.codigo_cliente_embarcador"] = NULL;
			}else{
				$conditions["AtendimentoSac.codigo_cliente_embarcador"] = $dados['AtendimentoSac']["codigo_embarcador"];
			}
		}
		if (!empty($dados['AtendimentoSac']['codigo_usuario_inclusao'])) {
			$conditions["AtendimentoSac.codigo_usuario_inclusao"] = $dados['AtendimentoSac']["codigo_usuario_inclusao"];
		}
		if (!empty($dados['AtendimentoSac']['ramal_encaminhado'])) {
			$conditions["AtendimentoSac.ramal_encaminhado LIKE"] = "%".$dados['AtendimentoSac']["ramal_encaminhado"]."%";
		}
		if (!empty($dados['AtendimentoSac']['observacao'])) {
			$conditions["AtendimentoSac.observacao LIKE"] = "%".$dados['AtendimentoSac']["observacao"]."%";
		}
		if (!empty($dados['AtendimentoSac']['nome_atendente'])) {
			$conditions["Usuario.apelido LIKE"] = "%".$dados['AtendimentoSac']["nome_atendente"]."%";
		}
        if (!empty($dados['AtendimentoSac']['motorista'])) {
            $conditions["Motorista.nome LIKE"] = "%".$dados['AtendimentoSac']["motorista"]."%";
        }
		if (!empty($dados['AtendimentoSac']['placa'])) {
			$conditions["AtendimentoSac.placa LIKE"] = strtoupper(str_replace('-', "", $dados['AtendimentoSac']["placa"]));
		}
		if (!empty($dados['AtendimentoSac']['data_inicial']) || !empty($dados['AtendimentoSac']['data_final'])) {
			$inicio = $dados['AtendimentoSac']['data_inicial'].' 00:00:00';
			$hora_inicial = (!empty($dados['AtendimentoSac']['hora_inicial']) ? $dados['AtendimentoSac']['hora_inicial'] : "00:00:00");
			$hora_final = (!empty($dados['AtendimentoSac']['hora_final']) ? $dados['AtendimentoSac']['hora_final'] : "23:59:59");
			$inicial = AppModel::dateToDbDate2($dados['AtendimentoSac']['data_inicial'] . " " . $hora_inicial.":00");
			$final = AppModel::dateToDbDate2($dados['AtendimentoSac']['data_final'] . " " . $hora_final.":59");

			$conditions['AtendimentoSac.data_inclusao BETWEEN ? AND ?'] = array($inicial,$final);
		}

		return $conditions; 
	}

	public function sintetico($conditions,$agrupamento,$limit = null,$page = null) {
       	$this->bindModel(
            array(
                'hasOne'=>array(
                    'Usuario' => array(
                        'className'  =>  'Usuario',
                        'foreignKey' => false,
                        'conditions' => array("Usuario.codigo = AtendimentoSac.codigo_usuario_inclusao"),
                    ),
                    'MotivoAtendimento' => array(
                        'className'  =>  'MotivoAtendimento',
                        'foreignKey' => false,
                        'conditions' => array('MotivoAtendimento.codigo = AtendimentoSac.codigo_motivo_atendimento'),
                    ),
                    'Recebsm' => array(
                        'className'  =>  'Recebsm',
                        'foreignKey' => false,
                        'conditions' => array('Recebsm.SM = AtendimentoSac.codigo_sm'),    
                    ),
                    'Equipamento' => array(
                        'className'  =>  'Equipamento',
                        'foreignKey' => false,
                        'conditions' => array('Equipamento.Codigo = Recebsm.CodEquipamento'),   
                    ),
                    'ClienteEmbarcador' => array(
                        'className'  =>  'Cliente',
                        'foreignKey' => false,
                        'conditions' => array('ClienteEmbarcador.Codigo = AtendimentoSac.codigo_cliente_embarcador'),   
                    ),
                    'ClienteTransportador' => array(
                        'className'  =>  'Cliente',
                        'foreignKey' => false,
                        'conditions' => array('ClienteTransportador.Codigo = AtendimentoSac.codigo_cliente_transportador'),   
                    ),
            )), false
        ); 
		if ($agrupamento == self::OPERADOR) {
            $fields = array(
            	'Usuario.apelido AS descricao',
            	'Usuario.apelido AS codigo',
                'COUNT(Usuario.apelido) AS qtd',            	
            );
            $group = array(
                'Usuario.apelido',
            );
            $order = 'Usuario.apelido';
        }
   
        if ($agrupamento == self::TRANSPORTADOR) {
            $fields = array(
            	'AtendimentoSac.codigo_cliente_transportador AS codigo',
            	'ClienteTransportador.razao_social AS descricao',
                '(SELECT SUM(
                    CASE WHEN AtendimentoSac.codigo_cliente_transportador IS NULL
                    THEN 1 ELSE 0 END) + count(AtendimentoSac.codigo_cliente_transportador)
                ) AS qtd',            	
            );
            $group = array(
                'AtendimentoSac.codigo_cliente_transportador',
                'ClienteTransportador.razao_social',
            );
            $order = 'ClienteTransportador.razao_social';
        }

     	
     	if ($agrupamento == self::EMBARCADOR) {
            $fields = array(
            	'AtendimentoSac.codigo_cliente_embarcador AS codigo',
            	'ClienteEmbarcador.razao_social AS descricao',
                '(SELECT SUM(
                    CASE WHEN AtendimentoSac.codigo_cliente_embarcador IS NULL
                    THEN 1 ELSE 0 END) + count(AtendimentoSac.codigo_cliente_embarcador)
                ) AS qtd',            	
            );
            $group = array(
                'AtendimentoSac.codigo_cliente_embarcador',
                'ClienteEmbarcador.razao_social',
            );
            $order = 'ClienteEmbarcador.razao_social';
        }
        
        if ($agrupamento == self::MOTIVO) {
            $fields = array(
            	'AtendimentoSac.codigo_motivo_atendimento AS codigo',
            	'MotivoAtendimento.descricao AS descricao',
                'COUNT(AtendimentoSac.codigo_motivo_atendimento) AS qtd',            	
            );
            $group = array(
                'AtendimentoSac.codigo_motivo_atendimento',
                'MotivoAtendimento.descricao',
            );
            $order = 'MotivoAtendimento.descricao';
        }
   
        if ($agrupamento == self::TECNOLOGIA) {
            $fields = array(
            	'Equipamento.Codigo AS codigo',
            	'Equipamento.Descricao AS descricao',
                '(SELECT SUM(
                    CASE WHEN Equipamento.Descricao IS NULL
                    THEN 1 ELSE 0 END) + count(Equipamento.Descricao)
                ) AS qtd',            	
            );
            $group = array(
                'Equipamento.Descricao',
                'Equipamento.Codigo',
            );
            $order = 'Equipamento.Descricao';
        }
        $sintetico = $this->find('sql',array(
        	'fields' => $fields,
        	'order' => $order,
            'group' => $group,
            'conditions' => $conditions,
        ));
        
        return $this->query($sintetico);
        //return $sintetico;
    }

}
