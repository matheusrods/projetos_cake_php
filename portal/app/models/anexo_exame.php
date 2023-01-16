<?php
class AnexoExame extends AppModel {

	public $name		   	= 'AnexoExame';
	public $databaseTable 	= 'RHHealth';
	public $tableSchema   	= 'dbo';
	public $useTable	   	= 'anexos_exames';
	public $primaryKey	   	= 'codigo';
	public $actsAs		   	= array('Secure', 'Containable','Loggable' => array('foreign_key' => 'codigo_anexos_exames'));

    public function paginate($conditions, $fields, $order, $limit, $page = 1, $recursive = null, $extra = array() ) {
        $joins = null;
		if (isset($extra['joins']))
			$joins = $extra['joins'];
		if (isset($extra['group']))
			$group = $extra['group'];
		if( isset( $extra['extra']['moderacao'] ) && $extra['extra']['moderacao'] ){
			return $this->moderacao_anexos('all', compact('conditions', 'fields', 'order', 'limit', 'page', 'recursive', 'group', 'joins'));
		}
		return $this->find('all', compact('conditions', 'fields', 'order', 'limit', 'page', 'recursive', 'group', 'joins'));
	}

    public function paginateCount( $conditions = null, $recursive = 0, $extra = array() ) {
        $joins = null;
        if (isset($extra['joins']))
            $joins = $extra['joins'];       

        if( isset( $extra['extra']['moderacao'] ) && $extra['extra']['moderacao']){
            return $this->moderacao_anexos('count', compact('conditions', 'recursive', 'joins'));
        }
        return $this->find('count', compact('conditions', 'recursive', 'joins'));
    }

    function converteFiltroEmCondition($data){
        $conditions = array();
        if (!empty($data['codigo_pedido_exame']))
            $conditions['codigo_pedido'] = $data['codigo_pedido_exame'];
        
        if (!empty($data['codigo_cliente']))
            $conditions['cliente_codigo'] = $data['codigo_cliente'];
        
        if (!empty($data['codigo_fornecedor']))
            $conditions['fornecedor_codigo'] = $data['codigo_fornecedor'];


        if (!empty($data['tipos_status']))
            $conditions['status_arquivo'] = $data['tipos_status'];

        return $conditions;
    }

    public function retorna_anexo_exame($filtros, $type = 'sql'){


        if(!empty($filtros['codigo_anexo'])) {
            $conditions['AnexoExame.codigo'] = $filtros['codigo_anexo'];
        } 


        $conditions['PedidoExame.codigo_status_pedidos_exames <>'] = 5;
        $conditions['AnexoExame.status <>'] = 0;
        //Somente anexos incluídos por usuários do tipo fornecedor
        $conditions['Uperfil.codigo_tipo_perfil'] = 3;
        
        //pega o codigo da empresa
        $codigo_empresa = $_SESSION['Auth']['Usuario']['codigo_empresa'];

        $fields = array(
            'PedidoExame.codigo as codigo_pedido',
            'ItemPedidoExame.codigo as codigo_item_pedido_exame',            
            'AnexoExame.codigo as codigo_anexo',
            'AnexoExame.caminho_arquivo as caminho_arquivo',
            'AnexoExame.status as status_arquivo',
            'CONVERT(CHAR(19), AnexoExame.data_inclusao,121) as data_inclusao',
            'Usuario.nome as usuario_inclusao',
            'Exame.descricao as nome_exame',
            'Cliente.codigo as cliente_codigo',
            'Cliente.razao_social as cliente_razao_social',
            'Funcionario.codigo as funcionario_codigo',
            'Funcionario.nome as funcionario_nome',
            'Fornecedor.codigo as fornecedor_codigo',
            'Fornecedor.razao_social as fornecedor_razao_social',
            'Usuario.apelido as usuario_apelido',
            'Usuario.email as usuario_email',
            '0 as codigo_ficha',
            '0 as ficha_clinica'
         );
        
        $joins  = array(
            array(
                'table' => 'Rhhealth.dbo.itens_pedidos_exames',
                'alias' => 'ItemPedidoExame',
                'type' => 'INNER',
                'conditions' => 'ItemPedidoExame.codigo = AnexoExame.codigo_item_pedido_exame',
            ), 
            array(
                'table' => 'Rhhealth.dbo.pedidos_exames',
                'alias' => 'PedidoExame',
                'type' => 'INNER',
                'conditions' => 'PedidoExame.codigo = ItemPedidoExame.codigo_pedidos_exames',
            ),
            array(
                'table' => 'Rhhealth.dbo.exames',
                'alias' => 'Exame',
                'type' => 'INNER',
                'conditions' => 'Exame.codigo = ItemPedidoExame.codigo_exame',
            ),
            array(
                'table' => 'Rhhealth.dbo.fornecedores',
                'alias' => 'Fornecedor',
                'type' => 'INNER',
                'conditions' => 'Fornecedor.codigo = ItemPedidoExame.codigo_fornecedor',
            ),
            array(
                'table' => 'Rhhealth.dbo.cliente_funcionario',
                'alias' => 'ClienteFuncionario',
                'type' => 'INNER',
                'conditions' => 'ClienteFuncionario.codigo = PedidoExame.codigo_cliente_funcionario',
            ),
            array(
                'table' => 'Rhhealth.dbo.cliente',
                'alias' => 'Cliente',
                'type' => 'INNER',
                'conditions' => 'Cliente.codigo = ClienteFuncionario.codigo_cliente_matricula',
            ),
            array(
                'table' => 'Rhhealth.dbo.funcionarios',
                'alias' => 'Funcionario',
                'type' => 'INNER',
                'conditions' => 'Funcionario.codigo = ClienteFuncionario.codigo_funcionario',
            ),
            array(
                'table' => 'Rhhealth.dbo.usuario',
                'alias' => 'Usuario',
                'type' => 'INNER',
                'conditions' => 'AnexoExame.codigo_usuario_inclusao = Usuario.codigo'
            ),           
            array(
                'table' => 'Rhhealth.dbo.uperfis',
                'alias' => 'Uperfil',
                'type' => 'INNER',
                'conditions' => 'Usuario.codigo_uperfil = Uperfil.codigo'
            )
        );
        if($type == 'sql'){
            return $this->find('sql',array('fields' => $fields, 'conditions' => $conditions, 'joins' => $joins));
        } else {
            return $this->find('all',array('fields' => $fields, 'conditions' => $conditions, 'joins' => $joins));
        }
    }//fim query_anexo_exame


    function moderacao_anexos($type,$conditions = array()){    
        $dbo = $this->getDataSource();

       	$AnexoFichaClinica = ClassRegistry::init('AnexoFichaClinica');

        $query_exames = $this->retorna_anexo_exame($conditions['conditions'],'sql');
        $query_ficha = $AnexoFichaClinica->retorna_anexo_ficha_clinica($conditions['conditions'], 'sql');
        $query = $query_exames.' UNION '.$query_ficha;

		$offset = (isset($conditions['page']) && $conditions['page'] > 1 ? (($conditions['page'] -1) * $conditions['limit']) : null);
      
        $query = $dbo->buildStatement(
            array(
                'table' => "({$query})",
                'alias' => 'Anexos',
                'joins' => null,
                'fields' => array('Anexos.*'),
                'conditions' =>  $conditions['conditions'],
                'order' => (isset($conditions['order']) ? $conditions['order'] : null),
                'limit' => (isset($conditions['limit']) ? $conditions['limit'] : null),
                'offset' => $offset,
                'group' => null,
            )
        , $this);
       
        if($type == 'sql') {
		    return $query;
        } elseif ($type == 'count') {
            $result = $this->query(" SELECT COUNT(*) AS qtd FROM ({$query}) AS base");
            return $result[0][0]['qtd'];
        } else {
            return $this->query($query);
        }
    }

    public function disparaEmail($dados, $assunto, $template, $to, $attachment = null) {

        if(Ambiente::getServidor() != Ambiente::SERVIDOR_PRODUCAO) {
            $to = 'tid@ithealth.com.br';
            $cc = null;
        } else {
            $cc = 'agendamento@rhhealth.com.br';
        }

        if(empty($assunto)){
            $assunto  = 'Exame digitalizado recusado';
        }
                
        if(!empty($dados['tipo'])) {

            //verifica se é ficha clinica ou anexo exame
            if($dados['tipo'] == 'FC') {
                $dados['tipo'] = 'Anexo Ficha Clínica';
            }
            else if($dados['tipo'] == 'AE') {
                $dados['tipo'] = 'Anexo Exame';
            }//fim verificacao tipo de recusa
        }

        App::import('Component', array('StringView', 'Mailer.Scheduler'));

        $this->stringView = new StringViewComponent();
        $this->scheduler = new SchedulerComponent();
        $this->stringView->reset();
        $this->stringView->set('dados', $dados);
        
        $content = $this->stringView->renderMail($template);
        
        return $this->scheduler->schedule($content, array (
            'from' => 'portal@rhhealth.com.br',
            'to' => $to,
            'cc' => $cc,
            'subject' => $assunto
            ));
    }
    /**
     * [alerta_exames_digitalizados description]
     * 
     * metodo para tratar os dados do alerta que irá ser dispardos.
     * 
     * @param  [type] $codigo_anexo_exames [description]
     * @return [type]                      [description]
     */
    public function alerta_exames_digitalizados($codigo)
    {

        //monta os fields
        $fields = array(
            'Cliente.codigo',
            'Cliente.nome_fantasia',
            'Exame.codigo',
            'Exame.descricao',
            'Funcionario.nome',
            'PedidoExame.codigo'
        );

        //monta os joins
        $joins = array(
            array(
                'table' => 'Rhhealth.dbo.itens_pedidos_exames',
                'alias' => 'ItemPedidoExame',
                'type' => 'INNER',
                'conditions' => 'ItemPedidoExame.codigo = AnexoExame.codigo_item_pedido_exame'
            ),
            array(
                'table' => 'Rhhealth.dbo.exames',
                'alias' => 'Exame',
                'type' => 'INNER',
                'conditions' => 'ItemPedidoExame.codigo_exame = Exame.codigo'
            ),
            array(
                'table' => 'Rhhealth.dbo.pedidos_exames',
                'alias' => 'PedidoExame',
                'type' => 'INNER',
                'conditions' => 'ItemPedidoExame.codigo_pedidos_exames = PedidoExame.codigo'
            ),
            array(
                'table' => 'Rhhealth.dbo.funcionarios',
                'alias' => 'Funcionario',
                'type' => 'INNER',
                'conditions' => 'PedidoExame.codigo_funcionario = Funcionario.codigo'
            ),
            array(
                'table' => 'Rhhealth.dbo.cliente',
                'alias' => 'Cliente',
                'type' => 'INNER',
                'conditions' => 'PedidoExame.codigo_cliente = Cliente.codigo'
            ),
        );

        //executa para pegar os dados
        $dados = $this->find('first', array('conditions' => array('AnexoExame.codigo' => $codigo),'joins' => $joins,'fields' => $fields));

        //verifica se existe dados para popular o email
        if(!empty($dados)) {

            App::import('Component', array('StringView'));

            $this->StringView = new StringViewComponent();
            $this->StringView->set('dados', $dados);
            $content = $this->StringView->renderMail('email_disponibilizacao_exame_digitalizado');

            //para o perfil rh(cliente)
            $Configuracao = &ClassRegistry::init('Configuracao');
            if($dados['Exame']['codigo'] == $Configuracao->getChave('INSERE_EXAME_CLINICO')) {

                $alerta = array(
                    'Alerta' => array(
                        'codigo_cliente'     => $dados['Cliente']['codigo'],
                        'descricao'          => "Disponibilização de exames digitalizados",
                        'assunto'            => "Disponibilização de exames digitalizados",
                        'descricao_email'    => $content,
                        'codigo_alerta_tipo' => '33',
                        'model'              => 'AnexoExame',
                        'foreign_key'        => NULL,
                        'email_agendados'    => false,
                        'sms_agendados'      => false
                    ),
                );

                //seta a model de alertas
                $this->Alerta =& ClassRegistry::init('Alerta');            
                $this->Alerta->incluir($alerta);
                
            }

            //para os perfils medicos(cliente) e enfermeiro(cliente)
            $alerta = array(
                'Alerta' => array(
                    'codigo_cliente'     => $dados['Cliente']['codigo'],
                    'descricao'          => "Disponibilização de exames digitalizados",
                    'assunto'            => "Disponibilização de exames digitalizados",
                    'descricao_email'    => $content,
                    'codigo_alerta_tipo' => '34',
                    'model'              => 'AnexoExame',
                    'foreign_key'        => NULL,
                    'email_agendados'    => false,
                    'sms_agendados'      => false
                ),
            );

            //seta a model de alertas
            $this->Alerta =& ClassRegistry::init('Alerta');            
            $this->Alerta->incluir($alerta);
                    

        }//fim dados

    }//fim alerta_exames_digitalizados

}