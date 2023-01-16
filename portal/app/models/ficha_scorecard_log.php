<?php
class FichaScorecardLog extends AppModel {

    var $name = 'FichaScorecardLog';
    var $tableSchema = 'informacoes';
    var $databaseTable = 'dbTeleconsult';
    var $useTable = 'ficha_scorecard_log';
    var $primaryKey = 'codigo';
    var $displayField = '';
    var $actsAs = array('Secure');
    var $foreignKeyLog = 'codigo_ficha_scorecard';
    var $belongsTo = array(
        'FichaScorecard' => array(
            'class' => 'FichaScorecard',
            'foreignKey' => 'codigo_ficha_scorecard'
        )
    );
    
    public  function parametros_fichas_a_pesquisar($filtros){
      $conditions = array();
      if(isset($filtros['codigo_cliente']) && !empty($filtros['codigo_cliente'])){
        $conditions['Cliente.codigo'] = $filtros['codigo_cliente'];
		  }
      if(isset($filtros['codigo_ficha_scorecard']) && !empty($filtros['codigo_ficha_scorecard'])){
        $conditions['FichaScorecardLog.codigo_ficha_scorecard'] = $filtros['codigo_ficha_scorecard'];
      }
      if(isset($filtros['usuario_apelido']) && !empty($filtros['usuario_apelido'])){
        $conditions['Usuario.apelido'] = $filtros['usuario_apelido'];
      }
      if(isset($filtros['profissional_log_codigo_documento']) && !empty($filtros['profissional_log_codigo_documento'])){
        $conditions['ProfissionalLog.codigo_documento'] = preg_replace('/[^\d]+/', '', $filtros['profissional_log_codigo_documento']);
		  }		
      if(isset($filtros['codigo_profissional_tipo']) && $filtros['codigo_profissional_tipo'] != null ){
  			if($filtros['codigo_profissional_tipo'] == ProfissionalTipo::CARRETEIRO)
  				$conditions['ProfissionalTipo.codigo'] = ProfissionalTipo::CARRETEIRO;
  			else if($filtros['codigo_profissional_tipo'] == ProfissionalTipo::OUTROS)
  				$conditions['ProfissionalTipo.codigo <>'] = ProfissionalTipo::CARRETEIRO;
  		}
  		if(isset($filtros['codigo_status']) && $filtros['codigo_status'] != null ){
  			$conditions['FichaScorecardLog.codigo_status'] = $filtros['codigo_status'];
  		}
  		if(isset($filtros['data_inclusao_inicio']) && $filtros['data_inclusao_inicio'] != null ){
  			$conditions['FichaScorecardLog.data_inclusao >='] = AppModel::dateToDbDate2($filtros['data_inclusao_inicio']).' 00:00:00';
  		}
  		if(isset($filtros['data_inclusao_fim']) && $filtros['data_inclusao_fim'] != null ){
        $conditions['FichaScorecardLog.data_inclusao <='] = AppModel::dateToDbDate2($filtros['data_inclusao_fim']).' 23:59:59';
  		}		

      // debug( $conditions );
  		$fields= array(
  		    'FichaScorecardLog.codigo',
  		    'FichaScorecardLog.codigo_status',
  		    'FichaScorecardLog.acao_sistema',
  		    'FichaScorecardLog.codigo_ficha_scorecard',
  		    'FichaScorecardLog.codigo_usuario_responsavel',
  		    'Usuario.apelido',
  		    'ProfissionalLog.codigo_documento',
  		    'FichaScorecardLog.data_inclusao',
  		    'ProfissionalTipo.descricao',
  		    'UsuarioAlteracao.apelido'
  		);
		
      $this->bindModel(
			array('belongsTo' => array(
				'Cliente' => array(
					'foreignKey' => false,
					'conditions' => 'FichaScorecardLog.codigo_cliente = Cliente.codigo'
				),
				'Seguradora' => array(
					'foreignKey' => false,
					'conditions' => 'Cliente.codigo_seguradora = Seguradora.codigo'
				),
				'ProfissionalLog' => array(
					'foreignKey' => false,
					'conditions' => 'FichaScorecardLog.codigo_profissional_log = ProfissionalLog.codigo'
				),
				'ProfissionalTipo' => array(
					'foreignKey' => false,
					'conditions' => 'ProfissionalTipo.codigo = FichaScorecardLog.codigo_profissional_tipo'
				),
  			'Usuario' => array(
  				'foreignKey' => false,
  				'conditions' => 'Usuario.codigo = FichaScorecardLog.codigo_usuario_responsavel'
  			),
        'UsuarioAlteracao' => array(
          'className'  => 'Usuario',
          'foreignKey' => false,
          'conditions' => 'UsuarioAlteracao.codigo = FichaScorecardLog.codigo_usuario_alteracao'
        ),        
			)), false
		);
		
		$limit = 50;	    
	    $order = array('FichaScorecardLog.codigo','FichaScorecardLog.data_inclusao');
		return compact('conditions', 'fields', 'limit','order');
    }
    
    public function listarUltimaFicha($returnQuery = false){
      $group = array( 'FichaScorecardLog.codigo_ficha_scorecard' );
      $fields = array('max(FichaScorecardLog.codigo) AS codigo');
      $findType = ($returnQuery ? 'sql' : 'all');
      $maxCodigo = $this->find($findType, compact('fields', 'group'));
      $conditions = array("FichaScorecardLog.codigo IN ({$maxCodigo})");
      $fields = array('FichaScorecardLog.codigo_ficha_scorecard','FichaScorecardLog.codigo_usuario','FichaScorecardLog.data_inclusao');
		  return $this->find($findType, compact('conditions','fields'));
    }

    public function carregarFichaCompleta( $conditions ){    
      $this->bindModel(
      array(
        'belongsTo'=>array(
          'Cliente'=>array('foreignKey'=>'codigo_cliente'),
          'Usuario'=>array('foreignKey'=>'codigo_usuario_responsavel'),
          'ProfissionalLog'=>array('foreignKey'=>'codigo_profissional_log'),
          'Profissional'=>array('foreignKey'=>FALSE, 'conditions' =>array('Profissional.codigo = ProfissionalLog.codigo_profissional') ),
          'ProfissionalEnderecoLog'=>array('foreignKey'=>'codigo_profissional_endereco_log'),
          'VEndereco'=>array('foreignKey'=>false, 'conditions'=>'ProfissionalEnderecoLog.codigo_endereco = VEndereco.endereco_codigo'),
          'EnderecoCidadeOrigem' => array('className'=>'EnderecoCidade', 'foreignKey' => 'codigo_endereco_cidade_carga_origem'),
          'EnderecoCidadeDestino' => array('className'=>'EnderecoCidade', 'foreignKey' => 'codigo_endereco_cidade_carga_destino'),
        ),
        'hasMany'=>array(
          'FichaScorecardRetorno'=>array('foreignKey'=>'codigo_ficha_scorecard'),
          'FichaScProfContatoLog'=>array('foreignKey'=>'codigo_ficha_scorecard'),
          'FichaScorecardVeiculo'=>array('foreignKey'=>'codigo_ficha_scorecard'),
          'FichaScorecardQuestaoResp' => array('foreignKey'=>'codigo_ficha_scorecard'),
        )
      )
    );
    $dados_ficha = $this->find('first', compact('conditions'));
    $this->VeiculoLog = ClassRegistry::init('VeiculoLog');
    foreach( $dados_ficha['FichaScorecardVeiculo'] as $key=> $ficha_scorecard_veiculo ){      
      $this->VeiculoLog->bindModel(array(
          'belongsTo' => array(
            'VeiculoModelo'  => array('foreignKey' => 'codigo_veiculo_modelo'),
            'EnderecoCidade' => array('foreignKey' => 'codigo_cidade_emplacamento'),
            'EnderecoEstado' => array('foreignKey' => false, 'conditions'=>'EnderecoCidade.codigo_endereco_estado = EnderecoEstado.codigo'),
          ),
      ));
      $veiculo = $this->VeiculoLog->find('first', array('conditions' =>
        array('VeiculoLog.codigo'=>$ficha_scorecard_veiculo['codigo_veiculo_log'])
      ));
      $dados_ficha['FichaScorecardVeiculo'][$key] = $veiculo;
    }
    return $dados_ficha;
  }

 public function buscarPontuacao( $codigo_ficha ) {
    $this->bindModel(array('belongsTo'=>array('ParametroScore'=>array('foreignKey'=>'codigo_parametro_score'))));
    $ficha = $this->find('first', array('conditions'=>array('FichaScorecardLog.codigo'=>$codigo_ficha)));    
    $i = 0;
    $pontos = 0;
    if(!empty($ficha['ParametroScore']['pontos'])){
      do{
        $i++;
        $pontos++;
      }while($i*20 < $ficha['ParametroScore']['pontos']);
    }
    
    $data = array(
      'total_pontos' 			=> $ficha['FichaScorecardLog']['total_pontos'],
      'percentual_pontos' 		=> $ficha['FichaScorecardLog']['percentual_pontos'],
      'nivel' 					=> empty($ficha['ParametroScore']['nivel']) 	? 'Motorista NÃO Habilitado!' : $ficha['ParametroScore']['nivel'],
      'valor' 					=> empty($ficha['ParametroScore']['valor']) 	? 0 : $ficha['ParametroScore']['valor'],
      'codigo_parametro_score' 	=> empty($ficha['ParametroScore']['codigo']) 	? 0 : $ficha['ParametroScore']['codigo'],
      'tipo_profissional' 		=> $ficha['FichaScorecardLog']['codigo_profissional_tipo'],
      'pontos' 					=> $pontos
    );
    return $data;
  }

}