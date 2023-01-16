<?php
class Pmps extends AppModel {

	var $name = 'Pmps';
	var $tableSchema = 'dbo';
	var $databaseTable = 'RHHealth';
	var $useTable = 'pcmso_materiais_pronto_socorro';
	var $primaryKey = 'codigo';
	var $actsAs = array('Secure');

	var $validate = array(
		'descricao' => array(
			'rule' => 'notEmpty',
			'message' => 'Informe a Descrição dos Materias de Pronto Socorro!',
		),
	);


	public function submeter(array $data){
	    if(!empty($data['Pmps']['codigo'])){
	        return self::atualiza($data);
        }else{
	        return self::inclui($data);
        }
    }

    private function valida_dados(array $data){
        $this->set($data);
        if(!$this->validates()){
            $errors = $this->invalidFields();
            throw new Exception(join("; ", $errors));
        }
    }

    private function atualiza($data){
        try{
            self::valida_dados($data);
            $return = parent::atualizar($data, false);

            if(isset($this->validationErrors) && !empty($this->validationErrors))
                throw new Exception(join("; ", $this->validationErrors));

            return array(
                'status' => 'success',
                'message' => 'Dados Atualizados com sucesso!',
                'data' => $return,
            );
        }catch(Exception $ex){
            return array(
                'status' => 'error',
                'message' => $ex->getMessage(),
            );
        }
    }

    private function inclui($data){
        try{
            self::valida_dados($data);
            $return = parent::incluir($data, false);

            if(isset($this->validationErrors) && !empty($this->validationErrors))
                throw new Exception(join("; ", $this->validationErrors));

            return array(
                'status' => 'success',
                'message' => 'Dados inseridos com sucesso!',
                'data' => $return,
            );
        }catch(Exception $ex){
            return array(
                'status' => 'error',
                'message' => $ex->getMessage(),
            );
        }
    }

    public function converte_filtro_em_conditions_listagem(array $data = array()){
	    $conditions = array();

	    if(isset($data['codigo_cliente']) && $data['codigo_cliente'] != '')
	        $conditions["GrupoEconomico.codigo_cliente"] = $data['codigo_cliente'];
        if(isset($data['codigo_cliente_alocacao']) && $data['codigo_cliente_alocacao'] != '')
            $conditions['GrupoEconomicoCliente.codigo_cliente'] = $data['codigo_cliente_alocacao'];
	    //$conditions['TipoAcao.codigo_empresa'] = (empty($data['codigo_empresa']) ? $_SESSION['Auth']['Usuario']['codigo_empresa'] : $data['codigo_empresa']);
        $conditions['ClienteMatriz.ativo'] = 1; $conditions['ClienteUnidade.ativo'] = 1;

	    return $conditions;
    }

    public function get_parametros_para_consulta(array $data = array()){
        $fields = array(
            'ClienteMatriz.codigo as codigo_cliente_matriz',
            'ClienteMatriz.razao_social as cliente_matriz',
            'ClienteUnidade.codigo as codigo_cliente_unidade',
            'ClienteUnidade.razao_social as cliente_unidade',
            'Pmps.codigo as codigo_pcmso_material_pronto_socorro',
            'LEFT(Pmps.descricao, 25) + \'...\' as material_pronto_socorro_resumo',
        );
        $joins = array(
            array(
                'table' => 'Rhhealth.dbo.grupos_economicos_clientes',
                'alias' => 'GrupoEconomicoCliente',
                'type' => 'INNER',
                'conditions' => 'GrupoEconomicoCliente.codigo_grupo_economico = GrupoEconomico.codigo',
            ),
            array(
                'table' => 'Rhhealth.dbo.cliente',
                'alias' => 'ClienteMatriz',
                'type' => 'INNER',
                'conditions' => 'GrupoEconomico.codigo_cliente = ClienteMatriz.codigo',
            ),
            array(
                'table' => 'Rhhealth.dbo.cliente',
                'alias' => 'ClienteUnidade',
                'type' => 'INNER',
                'conditions' => 'GrupoEconomicoCliente.codigo_cliente = ClienteUnidade.codigo',
            ),
            array(
                'table' => 'Rhhealth.dbo.pcmso_materiais_pronto_socorro',
                'alias' => 'Pmps',
                'type' => 'LEFT',
                'conditions' => 'Pmps.codigo_cliente_matriz = GrupoEconomico.codigo_cliente AND Pmps.codigo_cliente_unidade = GrupoEconomicoCliente.codigo_cliente',
            ),
        );
        $conditions = self::converte_filtro_em_conditions_listagem($data);
        $limit = 50;
        $order = 'ClienteMatriz.razao_social ASC, ClienteUnidade.razao_social ASC';
        $recursive = -1;
        return compact('fields','joins', 'conditions', 'limit', 'order', 'recursive');
    }

    public function get_por_matriz_unidade($codigo_cliente_matriz, $codigo_cliente_unidade){
        $fields = array(
            'ClienteMatriz.codigo as codigo_cliente_matriz',
            'ClienteUnidade.codigo as codigo_cliente_unidade',
            'Pmps.codigo as codigo',
            'Pmps.descricao as descricao',
        );
        $joins = array(
            array(
                'table' => 'Rhhealth.dbo.grupos_economicos_clientes',
                'alias' => 'GrupoEconomicoCliente',
                'type' => 'INNER',
                'conditions' => 'GrupoEconomicoCliente.codigo_grupo_economico = GrupoEconomico.codigo',
            ),
            array(
                'table' => 'Rhhealth.dbo.cliente',
                'alias' => 'ClienteMatriz',
                'type' => 'INNER',
                'conditions' => 'GrupoEconomico.codigo_cliente = ClienteMatriz.codigo',
            ),
            array(
                'table' => 'Rhhealth.dbo.cliente',
                'alias' => 'ClienteUnidade',
                'type' => 'INNER',
                'conditions' => 'GrupoEconomicoCliente.codigo_cliente = ClienteUnidade.codigo',
            ),
            array(
                'table' => 'Rhhealth.dbo.pcmso_materiais_pronto_socorro',
                'alias' => 'Pmps',
                'type' => 'LEFT',
                'conditions' => 'Pmps.codigo_cliente_matriz = GrupoEconomico.codigo_cliente AND Pmps.codigo_cliente_unidade = GrupoEconomicoCliente.codigo_cliente',
            ),
        );
	    $conditions = array(
            'GrupoEconomico.codigo_cliente' => $codigo_cliente_matriz,
            'GrupoEconomicoCliente.codigo_cliente' => $codigo_cliente_unidade,
        );
	    $GE = ClassRegistry::init('GrupoEconomico');
        $recursive = -1;
	    $return = $GE->find('first', compact('fields', 'joins', 'conditions', 'recursive'));

	    if(empty($return[0]['descricao']) || is_null($return[0]['descricao']))
	        $return[0]['descricao'] = self::get_texto_padrao_mps();

        return array('Pmps' => $return[0]);
    }

    private function get_texto_padrao_mps(){
        $texto = "ÁLCOOL 70% - Frasco de 100 ml \n" .
            "ALGODÃO - Bolas individuais \n" .
            "ATADURA - 2 Rolos (20cm); 1 Rolo (15cm) \n" .
            "BAND­AID - Caixa com 10 unidades \n" .
            "ESPARADRAPO  - Fita Hipoalergênica (Rolo de 5cm X 4,5m) \n" .
            "TERMÔMETRO Coluna de Hg \n" .
            "TESOURA Sem Ponta \n" .
            "SORO FISIOLÓGICO - Frasco de 500ml (Solução Fisiológica NaCl 0,9%) \n" .
            "ÁGUA OXIGENADA 10 volumes - Frasco de 100ml";
	    return $texto;
    }
}
