<?php
class ClienteSetor extends AppModel {

    var $name = 'ClienteSetor';
    var $tableSchema = 'dbo';
    var $databaseTable = 'RHHealth';
    var $useTable = 'clientes_setores';
    var $primaryKey = 'codigo';
	var $actsAs = array('Secure','Containable', 'Loggable' => array('foreign_key' => 'codigo_clientes_setores'));

	var $validate = array(
		'codigo_setor' => array(
            // 'notEmpty' => array(
            //     'rule' => 'notEmpty',
            //     'message' => 'Informe o Setor!'
            // ),
            'validaSetorCargo' => array(
                'rule' => 'validaSetorCargo',
                'message' => 'Setor já cadastrado anteriormente.!',
                'on' => 'create'
            ),
            'validaClienteSetor' => array(
            	'rule' => 'validaClienteSetor',
            	'message' => 'Cliente e Setor já relacionado!',
            	'on' => 'create',
            )
        ),
		'codigo_cliente_alocacao' => array(
			'rule' => 'notEmpty',
			'message' => 'Informe o Cliente'
		)
	);

	/**
	 * [validaClienteSetor description]
	 * 
	 * metodo para valida como is unique codigo cliente e codigo setor
	 * 
	 * @return [type] [description]
	 */
	public function validaClienteSetor() 
	{
		$verifica = $this->find('count', array('conditions' => array('codigo_cliente_alocacao' => $this->data['ClienteSetor']['codigo_cliente_alocacao'], 'codigo_setor' => $this->data['ClienteSetor']['codigo_setor']))) < 1 ;

	    if($verifica == 0){
            return false;
		 }       
		 else{
            return true;        	
		}

	}//fim validaClienteSetor

	function validaSetorCargo() {
		$GrupoExposicao =& ClassRegistry::Init('GrupoExposicao');
		
		$conditions = array(
			'codigo_cliente' => $this->data['ClienteSetor']['codigo_cliente_alocacao'],
			'codigo_setor' => $this->data['ClienteSetor']['codigo_setor'],
			'codigo_cargo' => $this->data['GrupoExposicao']['codigo_cargo']
			);
		
		if(isset($this->data['GrupoExposicao']['descricao_tipo_setor_cargo']) && $this->data['GrupoExposicao']['descricao_tipo_setor_cargo'] == 1){
			//Individual

			//Codigo do Grupo Homogeneo tem que ser nulo.
			if(isset($this->data['GrupoExposicao']['codigo_grupo_homogeneo']) && empty($this->data['GrupoExposicao']['codigo_grupo_homogeneo'])){
				$conditions_ghe = array('codigo_grupo_homogeneo IS NULL');
				$conditions = array_merge($conditions, $conditions_ghe);
			}

			//PPRA Individual Funcionario.
			if(isset($this->data['GrupoExposicao']['codigo_funcionario']) && !empty($this->data['GrupoExposicao']['codigo_funcionario'])){
				$conditions_funcionario = array('GrupoExposicao.codigo_funcionario' => $this->data['GrupoExposicao']['codigo_funcionario']);
				$conditions = array_merge($conditions, $conditions_funcionario);
			}
			else{
				$conditions_funcionario = array('GrupoExposicao.codigo_funcionario IS NULL');
				$conditions = array_merge($conditions, $conditions_funcionario);
			}
		}
		else{
			//GHE

			//Codigo do Grupo Homogeneo não pode ser nulo.
			if(isset($this->data['GrupoExposicao']['codigo_grupo_homogeneo']) && !empty($this->data['GrupoExposicao']['codigo_grupo_homogeneo'])){
				$conditions_ghe = array('codigo_grupo_homogeneo' =>  $this->data['GrupoExposicao']['codigo_grupo_homogeneo']);
				$conditions = array_merge($conditions, $conditions_ghe);
			}

		}

		$joins  = array(
                array(
                    'table' => $GrupoExposicao->databaseTable.'.'.$GrupoExposicao->tableSchema.'.'.$GrupoExposicao->useTable,
                    'alias' => 'GrupoExposicao',
                    'type' => 'LEFT',
                    'conditions' => 'GrupoExposicao.codigo_cliente_setor = ClienteSetor.codigo',
                ),
            );

		$fields = array(
			'GrupoExposicao.codigo', 'GrupoExposicao.codigo_cargo', 'GrupoExposicao.codigo_cliente_setor', 'GrupoExposicao.codigo_grupo_homogeneo', 'GrupoExposicao.codigo_funcionario', 
			'ClienteSetor.codigo','ClienteSetor.codigo_cliente','ClienteSetor.codigo_cliente_alocacao', 'ClienteSetor.codigo_setor'
		);

		$validar = $this->find('first', array('conditions' => $conditions, 'joins' => $joins, 'fields' => $fields));

		if(empty($validar)){
			return true;
		}
		else{
			return false;
		}
    }	

    function cliente_setor_importacao($data){
		$retorno = '';
		if (!isset($data['ClienteSetor']['codigo']) && empty($data['ClienteSetor']['codigo'])) { //INSERE NA TABELA CLIENTES_SETORES

			if(!parent::incluir($data,false)){
				$erro_cliente_setor = '';
                foreach ($this->validationErrors as $key => $value) {
                    $erro_cliente_setor .= utf8_decode($value).'|';
                    $this->validationErrors[$key] = $erro_cliente_setor;
                }
            	$retorno['ClienteSetor'] = $this->validationErrors;
            }
		}
		else{ //ATUALIZA OS DADOS NA TABELA CLIENTES_SETORES
			if(!parent::atualizar($data,false)){
				$erro_cliente_setor = '';
                foreach ($this->validationErrors as $key => $value) {
                    $erro_cliente_setor .= utf8_decode($value).'|';
                    $this->validationErrors[$key] = $erro_cliente_setor;
                }
            	$retorno['ClienteSetor'] = $this->validationErrors;
            }
		}

		if(!empty($this->id)){
		    $dados = $this->find("first", array('conditions' => array('codigo' => $this->id)));
		    if(empty($dados)){
				$retorno['Erro']['ClienteSetor'] = array('codigo_cliente_setor' => 'Cliente/Setor não encontrado!');
			}
			else{
				$retorno['Dados'] = $dados;
			}
	    }
	    return $retorno;
    }
}
