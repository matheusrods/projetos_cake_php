<?php
class AnexoFichaClinica extends AppModel {

	public $name		   	= 'AnexoFichaClinica';
	public $databaseTable 	= 'RHHealth';
	public $tableSchema   	= 'dbo';
	public $useTable	   	= 'anexos_fichas_clinicas';
	public $primaryKey	   	= 'codigo';
	public $actsAs		   	= array('Secure', 'Containable','Loggable' => array('foreign_key' => 'codigo_ficha_clinica'));

    public function retorna_anexo_ficha_clinica($filtros, $type = 'sql'){
        
        $Configuracao = ClassRegistry::init('Configuracao');


        $configuracao= $Configuracao->find('first',array('fields' => array('valor'), 'conditions' => array('chave' => 'INSERE_EXAME_CLINICO')));

        $codigo_aso = 0;
        if(!empty($configuracao)){
            $codigo_aso = $configuracao['Configuracao']['valor'];
        }
  

        if(!empty($filtros['codigo_anexo'])) {
            $conditions['AnexoFichaClinica.codigo'] = $filtros['codigo_anexo'];
        } 

        $conditions['PedidoExame.codigo_status_pedidos_exames <>'] = 5;
        $conditions['AnexoFichaClinica.status <>'] = 0;
        //Somente anexos incluídos por usuários do tipo fornecedor
        $conditions['Uperfil.codigo_tipo_perfil'] = 3;
        
        //pega o codigo da empresa
        $codigo_empresa = $_SESSION['Auth']['Usuario']['codigo_empresa'];

        $fields = array(
            'PedidoExame.codigo as codigo_pedido',
            'ItemPedidoExame.codigo as codigo_item_pedido_exame',            
            'AnexoFichaClinica.codigo as codigo_anexo',
            'AnexoFichaClinica.caminho_arquivo as caminho_arquivo',
            'AnexoFichaClinica.status as status_arquivo',
            'CONVERT(CHAR(19), AnexoFichaClinica.data_inclusao,121) as data_inclusao',
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
            'AnexoFichaClinica.codigo_ficha_clinica as codigo_ficha',
            '1 as ficha_clinica'
         );
        
        $joins  = array(
        	array(
                'table' => 'Rhhealth.dbo.fichas_clinicas',
                'alias' => 'FichaClinica',
                'type' => 'INNER',
                'conditions' => 'FichaClinica.codigo = AnexoFichaClinica.codigo_ficha_clinica'
            ),
            array(
                'table' => 'Rhhealth.dbo.pedidos_exames',
                'alias' => 'PedidoExame',
                'type' => 'INNER',
                'conditions' => 'FichaClinica.codigo_pedido_exame = PedidoExame.codigo',
            ),
            array(
                'table' => 'Rhhealth.dbo.itens_pedidos_exames',
                'alias' => 'ItemPedidoExame',
                'type' => 'INNER',
                'conditions' => array('ItemPedidoExame.codigo_pedidos_exames = PedidoExame.codigo',
                                'ItemPedidoExame.codigo_exame = '.$codigo_aso),
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
                'conditions' => 'AnexoFichaClinica.codigo_usuario_inclusao = Usuario.codigo'
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
    }//fim query_anexo_ficha_clinica


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
            'Exame.descricao',
            'Funcionario.nome',
            'PedidoExame.codigo'
        );

        //monta os joins
        $joins = array(
            array(
                'table' => 'Rhhealth.dbo.fichas_clinicas',
                'alias' => 'FichaClinica',
                'type' => 'INNER',
                'conditions' => 'FichaClinica.codigo = AnexoFichaClinica.codigo_ficha_clinica'
            ),
            array(
                'table' => 'Rhhealth.dbo.pedidos_exames',
                'alias' => 'PedidoExame',
                'type' => 'INNER',
                'conditions' => 'FichaClinica.codigo_pedido_exame = PedidoExame.codigo'
            ),
            array(
                'table' => 'Rhhealth.dbo.itens_pedidos_exames',
                'alias' => 'ItemPedidoExame',
                'type' => 'INNER',
                'conditions' => 'ItemPedidoExame.codigo_pedidos_exames = PedidoExame.codigo'
            ),
            array(
                'table' => 'Rhhealth.dbo.exames',
                'alias' => 'Exame',
                'type' => 'INNER',
                'conditions' => 'ItemPedidoExame.codigo_exame = Exame.codigo'
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
        $Configuracao = &ClassRegistry::init('Configuracao');
        $dados = $this->find('first', array('conditions' => array('ItemPedidoExame.codigo_exame' =>  $Configuracao->getChave('INSERE_EXAME_CLINICO'),'AnexoFichaClinica.codigo' => $codigo)));

        //verifica se existe dados para popular o email
        if(!empty($dados)) {

            App::import('Component', array('StringView'));

            $this->StringView = new StringViewComponent();
            $this->StringView->set('dados', $dados);
            $content = $this->StringView->renderMail('email_disponibilizacao_exame_digitalizado');
                    
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