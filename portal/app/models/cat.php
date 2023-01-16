<?php
class Cat extends AppModel {

    public $name = 'Cat';
    public $tableSchema = 'dbo';
    public $databaseTable = 'RHHealth';
    public $useTable = 'cat';
    public $primaryKey = 'codigo';
    public $actsAs = array('Secure', 'Containable','Loggable' => array('foreign_key' => 'codigo_cat'));
    public $recursive = -1;

    public $emitentes = array(
        1 => 'Empregador',
        2 => 'Cooperativa',
        3 => 'Sindicato de trabalhadores avulsos não portuários',
        4 => 'Órgão Gestor de Mão de Obra',
        5 => 'Empregado',
        6 => 'Dependente do Empregado',
        7 => 'Entidade Sindical competente',
        8 => 'Médico assistente',
        9 => 'Autoridade Pública',
    );

    public $cats = array(
        1 => 'Inicial',
        2 => 'Reabertura',
        3 => 'Comunicação de óbito'
        ); 

    public $estados_civis = array(
        1 => 'Solteiro', 
        2 => 'Casado', 
        3 => 'Separado', 
        4 => 'Divorciado', 
        5 => 'Viúvo', 
        6 => 'Outros'       
        );

    public $filiacoes = array(
        1 => 'Empregado', 
        2 => 'Tra. Avulso', 
        3 => 'Seg. especial', 
        4 => 'Médico Residente'     
        );

    public $areas = array(
        1 => 'Urbana', 
        2 => 'Rural'     
        );

    public $tipos = array(
        1 => 'Típico', 
        2 => 'Doença',   
        3 => 'Trajeto'     
        );

    public $validate = array(
        'codigo_emitente' => array(
            'rule' => 'notEmpty',
            'message' => 'Informe o Emitente',
            'required' => true
            ),
        'tipo_cat_codigo' => array(
            'rule' => 'notEmpty',
            'message' => 'Informe o Tipo de CAT',
            'required' => true
            ),
        'fil_prev_social_codigo' => array(
            'rule' => 'notEmpty',
            'message' => 'Informe a filiação a Previdência Social',
            'required' => true
            ),
        'aposentado' => array(
            'rule' => 'notEmpty',
            'message' => 'Informe se é aposentado',
            'required' => true
            ),
        'area_codigo' => array(
            'rule' => 'notEmpty',
            'message' => 'Informe a área',
            'required' => true
            ),
        'data_acidente' => array(
            'rule' => 'notEmpty',
            'message' => 'Informe a data do acidente',
            'required' => true
            ),
        'hora_acidente' => array(
            'rule' => 'notEmpty',
            'message' => 'Informe a hora do acidente',
            'required' => true
            ),
        'codigo_esocial_24' => array(
            'rule' => 'notEmpty',
            'message' => 'Informe o tipo',
            'required' => true
            ),
        'houve_afastamento' => array(
            'rule' => 'notEmpty',
            'message' => 'Informe se ouve afastamento',
            'required' => true
            ),
        'local_acidente' => array(
            'rule' => 'notEmpty',
            'message' => 'Informe o local do acidente',
            'required' => true
            ),
        'especificacao_local_acidente' => array(
            'rule' => 'notEmpty',
            'message' => 'Especifique o local do acidente',
            'required' => true
            ),
        'codigo_documento' => array(
            'rule' => 'notEmpty',
            'message' => 'Informe o CNPJ',
            'required' => true
            ),
        'uf_documento' => array(
            'rule' => 'notEmpty',
            'message' => 'Informe UF',
            'required' => true
            ),

        'acidentado_cidade' => array(
            'rule' => 'notEmpty',
            'message' => 'Informe a cidade do acidente',
            'required' => true
            ),
        'codigo_esocial_14_15' => array(
            'rule' => 'notEmpty',
            'message' => 'Informe o agente causador',
            'required' => true
            ),
        'resistro_policial' => array(
            'rule' => 'notEmpty',
            'message' => 'Informe se ouve registro policial',
            'required' => true
            ),
        'resistro_policial' => array(
            'rule' => 'notEmpty',
            'message' => 'Informe se ouve morte',
            'required' => true
            ),
        'local' => array(
            'rule' => 'notEmpty',
            'message' => 'Informe o local',
            'required' => true
            ),
        'data' => array(
            'rule' => 'notEmpty',
            'message' => 'Informe a data',
            'required' => true
            ),
        'morte' => array(
            'rule' => 'notEmpty',
            'message' => 'Informe Se houve a morte ou não',
            'required' => true
            ),
         'local' => array(
            'rule' => 'notEmpty',
            'message' => 'Informe o local',
            'required' => true
            ),
         'cep_acidentado' => array(
            'rule' => 'notEmpty',
            'message' => 'Informe o CEP',
            'required' => true
            ),
         'acidentado_endereco' => array(
            'rule' => 'notEmpty',
            'message' => 'Informe o Endereço do acidente',
            'required' => true
            ),
         'acidentado_numero' => array(
            'rule' => 'notEmpty',
            'message' => 'Informe o numero do endereço',
            'required' => true
            ),
         'acidente_estado' => array(
            'rule' => 'notEmpty',
            'message' => 'Informe o Estado do endereço',
            'required' => true
            ), 
         'codigo_pais' => array(
            'rule' => 'notEmpty',
            'message' => 'Informe o Codigo do seu país',
            'required' => true
            ),
         'codigo_esocial_13' => array(
            'rule' => 'notEmpty',
            'message' => 'Informe a Parte do corpo',
            'required' => true
            ),
         'tipo_inscricao' => array(
            'rule' => 'notEmpty',
            'message' => 'Informe o Tipo de Inscrição',
            'required' => true
            ),
        );


    public function converteFiltroEmCondition($data) 
    {
        $conditions = array();
        if (!empty($data['codigo_cliente'])) {
            $conditions['OR']['GrupoEconomico.codigo_cliente'] = $data['codigo_cliente'];
            $conditions['OR']['Cliente.codigo'] = $data['codigo_cliente'];
        }

        if (isset($filtros['codigo_setor']) && !empty($filtros['codigo_setor'])) {
            $conditions['FuncionarioSetorCargo.codigo_setor'] = $filtros['codigo_setor'];
        }
        if (isset($filtros['codigo_cargo']) && !empty($filtros['codigo_cargo'])) {
            $conditions['FuncionarioSetorCargo.codigo_cargo'] = $filtros['codigo_cargo'];
        }
        if (isset($filtros['codigo_funcionario']) && !empty($filtros['codigo_funcionario'])) {
            $conditions['ClienteFuncionario.codigo_funcionario'] = $filtros['codigo_funcionario'];
        }

        if (! empty ( $data ['nome_funcionario'] ))
            $conditions ['Funcionario.nome LIKE'] = '%' . $data ['nome_funcionario'] . '%';

        if (!empty($data['cpf']))
            $conditions['Funcionario.cpf'] = $data['cpf'];

        return $conditions;
    }
    
    public function obtemDadosDoFuncionario($codigo_funcionario,$codigo_cliente,$codigo_funcionario_setor_cargo) 
    {

        $this->Funcionario = ClassRegistry::init('Funcionario');

        $this->Funcionario->virtualFields = array(
            'telefone' => 'SELECT TOP 1 CONCAT(ddd, \'-\', descricao) telefone FROM cliente_contato WHERE codigo_cliente = ClienteFuncionario.codigo_cliente AND codigo_tipo_retorno = 1 AND codigo_tipo_contato = 2 ORDER BY codigo ASC',
            'tel_fun' => 'SELECT TOP 1 CONCAT(ddd, \'-\', descricao) telefone_funcionario FROM funcionarios_contatos WHERE codigo_funcionario = ClienteFuncionario.codigo_funcionario AND codigo_tipo_retorno = 1 AND codigo_tipo_contato = 2 ORDER BY codigo ASC',
       		'cargo' => "(SELECT descricao FROM RHHealth.dbo.cargos where codigo = (SELECT TOP 1 codigo_cargo FROM RHHealth.dbo.funcionario_setores_cargos WHERE codigo = ".$codigo_funcionario_setor_cargo."  AND (data_fim = '' OR data_fim IS NULL )))",
        	'cargo_cbo' => "(SELECT codigo_cbo FROM RHHealth.dbo.cargos where codigo = (SELECT TOP 1 codigo_cargo FROM RHHealth.dbo.funcionario_setores_cargos WHERE codigo = ".$codigo_funcionario_setor_cargo."  AND (data_fim = '' OR data_fim IS NULL ) ORDER BY 1 DESC))"
        );        

        $dados = array(
            'conditions' => array(
                'Funcionario.codigo' => $codigo_funcionario,
                'FuncionarioSetorCargo.codigo_cliente_alocacao' => $codigo_cliente
                ),
            'joins' => array(
                array(
                    'table' => 'RHHealth.dbo.cliente_funcionario',
                    'alias' => 'ClienteFuncionario',
                    'type' => 'INNER',
                    'conditions' => array(
                        'ClienteFuncionario.codigo_funcionario = Funcionario.codigo'
                        )  
                    ),
                 array(
                    'table' => 'RHHealth.dbo.funcionario_setores_cargos',
                    'alias' => 'FuncionarioSetorCargo',
                    'type' => 'INNER',
                    'conditions' => array(
                        'FuncionarioSetorCargo.codigo_cliente_funcionario = ClienteFuncionario.codigo'
                        )  
                    ),
                array(
                    'table' => 'RHHealth.dbo.cliente',
                    'alias' => 'Cliente',
                    'type' => 'INNER',
                    'conditions' => array(
                        'Cliente.codigo = FuncionarioSetorCargo.codigo_cliente_alocacao'
                        )  
                    ),
                array(
                    'table' => 'RHHealth.dbo.cliente_endereco',
                    'alias' => 'ClienteEndereco',
                    'type' => 'INNER',
                    'conditions' => array(
                        'ClienteEndereco.codigo_cliente = Cliente.codigo'
                        )  
                    ),
                array(
                    'table' => 'RHHealth.dbo.funcionarios_enderecos',
                    'alias' => 'FuncionarioEndereco',
                    'type' => 'LEFT',
                    'conditions' => array(
                        'FuncionarioEndereco.codigo_funcionario = Funcionario.codigo'
                        )  
                    ),
                ),
                'fields' => array(
                    'Funcionario.nome',
                    'Funcionario.codigo',
                    'Funcionario.cpf',
                    'Funcionario.telefone',
                    'Funcionario.data_nascimento',
                    'Funcionario.sexo',
                    'Funcionario.estado_civil',
                    'Funcionario.ctps',
                    'Funcionario.ctps_uf',
                    'Funcionario.rg',
                    'Funcionario.rg_data_emissao',
                    'Funcionario.rg_orgao',
                    'Funcionario.rg_uf',
                    'Funcionario.nit',
                    'Funcionario.nome_mae',
                    'Cliente.codigo',
                    'Cliente.razao_social',
                    'Cliente.codigo_documento_real',
                    'Cliente.codigo_documento',
                    'Cliente.cnae',
                    'ClienteEndereco.complemento',
                    'ClienteEndereco.numero',
                    'ClienteEndereco.logradouro',
                    'ClienteEndereco.cidade',
                    'ClienteEndereco.estado_descricao',
                    'ClienteEndereco.bairro',
                    'ClienteEndereco.cep',
                    'FuncionarioEndereco.logradouro',
                    'FuncionarioEndereco.complemento',
                    'FuncionarioEndereco.numero',
                    'FuncionarioEndereco.bairro',
                    'FuncionarioEndereco.cep',
                    'FuncionarioEndereco.cidade',
                    'FuncionarioEndereco.estado_abreviacao',
                    'Funcionario.tel_fun',
					'cargo',
                	'cargo_cbo'                    
                    )
                );

            $registro = $this->Funcionario->find('first',$dados);           

            return  $registro;
        } //fim obtemDadosDoFuncionario

        public function incluir($data)
        {
            $this->Funcionario =& ClassRegistry::init('Funcionario');
            $data['Funcionario']['codigo'] = $data['Cat']['codigo_funcionario'];
            $funcionario['Funcionario'] = $data['Funcionario'];
            unset($data['Funcionario']);
            $this->Funcionario->validate = array(); // elimina a validaçao de dados de Funcionario



            if($this->Funcionario->atualizar($funcionario)) { 
                
                $data['Cat']['acidentado_cidade_ibge'] = null;
                if(isset($data['Cat']['acidentado_cidade'])) {
                    $data['Cat']['acidentado_cidade_ibge'] = $this->buscar_codigo_ibge($data['Cat']['acidentado_cidade']);
                }

                if(parent::incluir($data)) {
                    return true;
                }
            } 
                
            return false;
        }

        public function atualizar($data)
        {
            $this->Funcionario =& ClassRegistry::init('Funcionario');
            
            $data['Funcionario']['codigo'] = $data['Cat']['codigo_funcionario'];
            
            $funcionario['Funcionario'] = $data['Funcionario'];
            
            unset($data['Funcionario']);
            
            $this->Funcionario->validate = array(); // elimina a validaçao de dados de Funcionario
            
            //essa condicao foi colocada, para a rotina da retificacao da CAT, se vier com retirar validate, é para retificar, para o validate nao vetar
            if(!empty($data['Cat']['retirar_validate']) && $data['Cat']['retirar_validate'] == 1){
                $this->validate = array();
            }

            if($this->Funcionario->atualizar($funcionario)) {

                $data['Cat']['acidentado_cidade_ibge'] = null;
                if(isset($data['Cat']['acidentado_cidade'])) {
                    $data['Cat']['acidentado_cidade_ibge'] = $this->buscar_codigo_ibge($data['Cat']['acidentado_cidade']);
                }

                // debug($data);exit;

                if(parent::atualizar($data)) {
                    return true;
                }
                // debug($this->validationErrors);exit;
            } 

            return false;
        }

    /**
     * [buscar_codigo_ibge busca o codigo_ibge]
     * @param  [type] $cidade [description]
     * @return [type]         [description]
     */
    public function buscar_codigo_ibge($acidentado_cidade)
    {

        $this->EnderecoCidade =& ClassRegistry::init('EnderecoCidade');
        $retorno = null;

        $cidade = $this->EnderecoCidade->carregar_cidade_nome_completo($acidentado_cidade, array('codigo','ibge'));

        if(!empty($cidade)) {
            $retorno = $cidade['EnderecoCidade']['ibge'];
        }

        return $retorno;


    }//fim buscar_codigo_ibge

    public function tipos(){
        $this->Esocial = ClassRegistry::init('Esocial');

        $this->Esocial->virtualFields = array('codigo_e_descricao' => "CONCAT(codigo_descricao, ' - ', descricao)");

        $fields = array('codigo', 'codigo_e_descricao');
        $conditions = array('tabela' => 24);
        $order = array('codigo_descricao');


        $retorno = $this->Esocial->find('list', array('fields' => $fields, 'conditions' => $conditions, 'order' => $order));

        return $retorno;

    }//FINAL FUNCTION tipo

    /**
     * [natureza_lesao description]
     * 
     * metodo para pegar os dados do esocial tabela 17
     * 
     * @return [type] [description]
     */
    public function natureza_lesao(){
        
        $this->Esocial = ClassRegistry::init('Esocial');

        $this->Esocial->virtualFields = array('codigo_e_descricao' => "CONCAT(codigo_descricao, ' - ', descricao)");

        $fields = array('codigo', 'codigo_e_descricao');
        $conditions = array('tabela' => 17);
        $order = array('codigo_descricao');

        $retorno = $this->Esocial->find('list', array('fields' => $fields, 'conditions' => $conditions, 'order' => $order));

        return $retorno;

    }//FINAL FUNCTION tipo

    /**
     * [retornaCatFuncionario description]
     * 
     * metodo para pegar os cats do funcionario já emitidos
     * 
     * @param  [type] $codigo_func_setor_cargo [description]
     * @return [type]                          [description]
     */
    public function retornaCatFuncionario($codigo_func_setor_cargo)
    {

        //monta as conditions
        $conditions['FuncionarioSetorCargo.codigo'] = $codigo_func_setor_cargo;
        //seta os relacionamentos
        $joins = array(
            array(
                'table' => 'Rhhealth.dbo.cliente_funcionario',
                'alias' => 'ClienteFuncionario',
                'type' => 'INNER',
                'conditions' => array('Cat.codigo_funcionario = ClienteFuncionario.codigo_funcionario')
            ),
            array(
                'table' => 'Rhhealth.dbo.funcionario_setores_cargos',
                'alias' => 'FuncionarioSetorCargo',
                'type' => 'INNER',
                'conditions' => array('ClienteFuncionario.codigo = FuncionarioSetorCargo.codigo_cliente_funcionario')
            ),
            array(
                'table' => 'Rhhealth.dbo.cliente',
                'alias' => 'Cliente',
                'type' => 'INNER',
                'conditions' => array('Cliente.codigo = FuncionarioSetorCargo.codigo_cliente_alocacao')
            ),
            array(
                'table' => 'Rhhealth.dbo.funcionarios',
                'alias' => 'Funcionario',
                'type' => 'INNER',
                'conditions' => array('Cat.codigo_funcionario = Funcionario.codigo')
            )            
        );

        //retorna os campos para consulta
        $fields = array('Cat.codigo','Funcionario.codigo', 'Funcionario.nome', 'Funcionario.cpf','Cliente.razao_social', 'ClienteFuncionario.matricula', 'FuncionarioSetorCargo.codigo_cliente_alocacao');
        
        //retorna os dados da base
        // debug($this->find('sql', compact('conditions','joins','fields')));exit;
        return $this->find('all', compact('conditions','joins','fields'));

    }//fim retornaCatFuncionario

    public function returnJoinsCat(){
        $joins = array(
                array(
                    'table' => 'RHHealth.dbo.grupos_economicos_clientes',
                    'alias' => 'GrupoEconomicoCliente',
                    'type' => 'INNER',
                    'conditions' => 'Cat.codigo_cliente = GrupoEconomicoCliente.codigo_cliente',
                ),
                array(
                    'table' => 'RHHealth.dbo.grupos_economicos',
                    'alias' => 'GrupoEconomico',
                    'type' => 'INNER',
                    'conditions' => 'GrupoEconomicoCliente.codigo_grupo_economico = GrupoEconomico.codigo',
                ),
        );

        return $joins;
    }

    public function FiltroEmConditionCat($data) 
    {
        //seta a variavel para inicio do metodo
        $conditions = array();

        //verifica se tem valores nos filtros
        if (!empty($data['codigo_cliente'])) {
            $conditions['ClienteFuncionario.codigo_cliente'] = $data['codigo_cliente'];
        }

        if (!empty($data['codigo_cliente_alocacao'])) {
            $conditions['FuncionarioSetorCargo.codigo_cliente_alocacao'] = $data['codigo_cliente_alocacao'];
        }

        if (!empty($data['codigo_cargo'])) {
            $conditions['Cargo.codigo'] = $data['codigo_cargo'];
        }

        if (!empty($data['codigo_setor'])) {
            $conditions['Setor.codigo'] = $data['codigo_setor'];
        }

        if (!empty($data['codigo_funcionario'])) {
            $conditions["Funcionario.codigo"] = $data['codigo_funcionario'];
        }

        if (!empty($data['nome_funcionario'])) {
            $conditions["Funcionario.nome LIKE"] = '%'. $data['nome_funcionario'] . '%';
        }

        if (!empty($data['cpf'])) {
            $conditions["Funcionario.cpf"] = Comum::soNumero($data['cpf']);
        }

        if (!empty($data['codigo_cat'])) {
            $conditions['Cat.codigo'] = $data['codigo_cat'];
        }
        
        //logica para as datas de filtros
        if(!empty($data["data_inicio"])) {
            $data_inicio = AppModel::dateToDbDate($data["data_inicio"].' 00:00:00');
            $data_fim = AppModel::dateToDbDate($data["data_fim"].' 23:59:59');
            $conditions [] = "(Cat.data_inclusao >= '". $data_inicio . "'";
        }//fim if

        if(!empty($data["data_fim"])) {
            $conditions [] = "Cat.data_inclusao <= '" . $data_fim . "')";
        }

        if(!empty($data['bt_filtro'])) {

            switch($data['bt_filtro']) {
                case '1':
                    $conditions[] = 'IntEsocialEvento.codigo_int_esocial_status IS NULL';
                    break;
                default:
                    $conditions['IntEsocialEvento.codigo_int_esocial_status'] = $data['bt_filtro'];
                    break;
            }// fim switch

        }//fim bt_filtro

        
        // die(debug($conditions));
        return $conditions;
        
    } //fim converteFiltroEmCondition
}
