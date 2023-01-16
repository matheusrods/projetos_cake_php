<?php
class AbsenteismoController extends AppController {
    public $name = 'Absenteismo';
    public $helpers = array('BForm', 'Html', 'Ajax', 'Highcharts');
    var $uses = array('Absenteismo','Atestado', 'GrupoEconomicoCliente');

    public function beforeFilter() {
        parent::beforeFilter();
    }


	public function index() {

    	$this->pageTitle = 'Relatório - Sintético de Absenteísmo';
    	$this->data['Atestado'] = $this->Filtros->controla_sessao($this->data, 'Atestado');

  		$lista_unidades = array();
  		$lista_cargos = array();
  		$lista_setores = array();
  		$lista_funcionarios = array();
  		
  		$codigo_unidade = isset($this->data['Atestado']['codigo_unidade']) ? $this->data['Atestado']['codigo_unidade'] : '';

  		if($this->BAuth->user('codigo_cliente')) {
  			$codigo_unidade = $this->BAuth->user('codigo_cliente');
  		}

  		$codigo_funcionario = isset($this->data['Atestado']['codigo_funcionario']) ? $this->data['Atestado']['codigo_funcionario'] : '';
  		$codigo_setor = isset($this->data['Atestado']['codigo_setor']) ? $this->data['Atestado']['codigo_setor'] : '';
  		$codigo_cargo = isset($this->data['Atestado']['codigo_cargo']) ? $this->data['Atestado']['codigo_cargo'] : '';
  		$codigo_cliente = isset($this->data['Atestado']['codigo_cliente']) ? $this->data['Atestado']['codigo_cliente'] : '';

      $data_inicio_mes = '01/'.date('m/Y');
      $this->data['Atestado']['data_inicio'] = isset($this->data['Atestado']['data_inicio']) ? $this->data['Atestado']['data_inicio'] : $data_inicio_mes ;
      $this->data['Atestado']['data_fim'] =  isset($this->data['Atestado']['data_fim']) ? $this->data['Atestado']['data_fim'] :  date('d/m/Y');

  		$this->set(compact('lista_funcionarios', 'lista_setores', 'lista_cargos', 'lista_unidades', 'lista_status', 'codigo_unidade', 'codigo_cliente', 'codigo_cargo', 'codigo_setor', 'codigo_funcionario'));
  		$this->set('codigo_grupo_economico', (isset($codigo_grupo_economico) ? $codigo_grupo_economico : ''));

	}

	public function retorna_codigo_grupo_economico() {
  		
		/***************************************************
		 * validacao adicionado para evitar o cliente de 
		 * burlar o acesso e ver dados de outros clientes;
		 ***************************************************/
  		if(!is_null($this->BAuth->user('codigo_cliente'))) {
  			$codigo_unidade = $this->BAuth->user('codigo_cliente');
  		} else {
  			$codigo_unidade = $this->params['form']['codigo_unidade'];
  		}
  		
  		$dados_grupo_economico = $this->GrupoEconomicoCliente->find('first', array('conditions' => array('GrupoEconomicoCliente.codigo_cliente' => $codigo_unidade), 'recursive' => '-1', 'fields' => 'GrupoEconomicoCliente.codigo_grupo_economico'));
  		 
  		echo json_encode(array('codigo_grupo_economico' => $dados_grupo_economico['GrupoEconomicoCliente']['codigo_grupo_economico']));
  		exit;
  	}


	public function sub_filtro_cliente_funcionario($codigo_grupo_economico, $codigo_unidade) {
  		
  		/***************************************************
  		 * validacao adicionado para evitar o cliente de
  		 * burlar o acesso e ver dados de outros clientes;
  		 ***************************************************/
  		if(!is_null($this->BAuth->user('codigo_cliente'))) {
       
        $dados_cliente = $this->GrupoEconomicoCliente->retorna_dados_cliente($this->BAuth->user('codigo_cliente'));
        $codigo_grupo_economico = $dados_cliente['GrupoEconomicoCliente']['codigo_grupo_economico'];
  			// $dados_grupo_economico = json_decode($this->retorna_codigo_grupo_economico());
  			// $codigo_grupo_economico = $dados_grupo_economico['codigo_grupo_economico'];
  		}

    	$this->data['Atestado'] = $this->Filtros->controla_sessao($this->data, 'Atestado');
    	
		if(isset($codigo_grupo_economico) && $codigo_grupo_economico) {
			$lista_unidades = $this->GrupoEconomicoCliente->retorna_lista_de_unidades_de_um_grupo_economico($codigo_grupo_economico);
			$lista_cargos = $this->GrupoEconomicoCliente->listaCargos($codigo_grupo_economico);
			$lista_setores = $this->GrupoEconomicoCliente->listaSetores($codigo_grupo_economico);
			$lista_funcionarios = $this->GrupoEconomicoCliente->listaFuncionarios($codigo_grupo_economico);
			
    	} else {
    		$lista_unidades = array();
    		$lista_cargos = array();
    		$lista_setores = array();
    		$lista_funcionarios = array();
    	}
    	
    	$codigo_funcionario = isset($this->data['Atestado']['codigo_funcionario']) ? $this->data['Atestado']['codigo_funcionario'] : '';
    	$codigo_setor = isset($this->data['Atestado']['codigo_setor']) ? $this->data['Atestado']['codigo_setor'] : '';
    	$codigo_cargo = isset($this->data['Atestado']['codigo_cargo']) ? $this->data['Atestado']['codigo_cargo'] : '';
    	$codigo_cliente = isset($this->data['Atestado']['codigo_cliente']) ? $this->data['Atestado']['codigo_cliente'] : '';
    	
       $data_inicio_mes = '01/'.date('m/Y');
      $this->data['Atestado']['data_inicio'] = isset($this->data['Atestado']['data_inicio']) ? $this->data['Atestado']['data_inicio'] : $data_inicio_mes ;
      $this->data['Atestado']['data_fim'] =  isset($this->data['Atestado']['data_fim']) ? $this->data['Atestado']['data_fim'] :  date('d/m/Y');
    	$this->set(compact('lista_funcionarios', 'lista_setores', 'lista_cargos', 'lista_unidades', 'codigo_unidade', 'codigo_cliente', 'codigo_cargo', 'codigo_setor', 'codigo_funcionario', 'codigo_grupo_economico'));
    }

    public function listagem($codigo_grupo_economico = null) {

      $this->layout = 'ajax';

      /***************************************************
       * validacao para evitar o cliente de
       * burlar o acesso e ver dados de outros clientes;
       ***************************************************/
      if(!is_null($this->BAuth->user('codigo_cliente'))) {
         $dados_cliente = $this->GrupoEconomicoCliente->retorna_dados_cliente($this->BAuth->user('codigo_cliente'));
        $codigo_grupo_economico = $dados_cliente['GrupoEconomicoCliente']['codigo_grupo_economico'];
      }

  		$filtros = $this->Filtros->controla_sessao($this->data, 'Atestado');


      if (!empty($filtros['codigo_unidade']))
      {

      $conditions= $this->Atestado->converteFiltroEmCondition($filtros);
      //$conditions['ClienteFuncionario.ativo'] = 1;


      if(is_numeric($codigo_grupo_economico) && empty($conditions['ClienteFuncionario.codigo_cliente'])) {
        
        $conditions[] = array('ClienteFuncionario.codigo_cliente IN (SELECT codigo_cliente FROM grupos_economicos_clientes WHERE codigo_grupo_economico = '.$codigo_grupo_economico.' )');
      }
      
      $dados_grafico = $this->Absenteismo->relatorio_absenteismo_analitico($conditions);

      if(!empty($dados_grafico[0][0]['com_atestado'])){
  
          $func_com_atestado =  $dados_grafico[0][0]['com_atestado'];
          $func_sem_atestado =  $dados_grafico[0][0]['sem_atestado'];
          $func_total =  $dados_grafico[0][0]['total_funcionarios'];

/*        $com_atestado = ($func_com_atestado / $func_total ) * 100;
          $sem_atestado = ($func_sem_atestado / $func_total ) * 100;
          $com_atestado = round($com_atestado,2);
          $sem_atestado = round($sem_atestado,2);
*/

          $series_pizza[] = array('name' => "'Sem atestado'", 'values' => $func_sem_atestado);
          $series_pizza[] = array('name' => "'Com atestado'", 'values' => $func_com_atestado);
   
      } else {
        $series_pizza = array();
      }
    } else {
       $series_pizza = array();
    }

      $this->set(compact('series_pizza', 'codigo_grupo_economico'));
    }

    public function exportar($codigo_grupo_economico) {


      $dias_semana = array(1 => 'Domingo', 2 => 'Segunda-feira', 3 => 'Terça-feira', 4 => 'Quarta-feira', 5 => 'Quinta-feira', 6 => 'Sexta-feira', 7 => 'Sábado');
      
      /***************************************************
       * validacao para evitar o cliente de
       * burlar o acesso e ver dados de outros clientes;
       ***************************************************/
      if(!is_null($this->BAuth->user('codigo_cliente'))) {
         $dados_cliente = $this->GrupoEconomicoCliente->retorna_dados_cliente($this->BAuth->user('codigo_cliente'));
        $codigo_grupo_economico = $dados_cliente['GrupoEconomicoCliente']['codigo_grupo_economico'];
      }


      $this->Atestado->virtualFields = array (
        'razao_empresa' => '(SELECT razao_social from cliente c JOIN grupos_economicos ge ON ge.codigo_cliente = c.codigo Where ge.codigo = GrupoEconomicoCliente.codigo_grupo_economico)',
        'dia_semana' => 'DATEPART(weekday,Atestado.data_afastamento_periodo)',
        'dias_afastado' => 'DATEDIFF(day,Atestado.data_afastamento_periodo, Atestado.data_retorno_periodo)',
        'horas_afastado' => 'DATEDIFF(hour,Atestado.hora_afastamento, Atestado.hora_retorno)',
        'nexo' => '(SELECT count(*) from cid_cnae Where codigo_cid = Cid.codigo AND codigo_cnae = Cnae.codigo)',
        'endereco_funcionario' => "(SELECT TOP 1 CONCAT(uvw.endereco_tipo,' ',uvw.endereco_logradouro,', ',fe.numero,' ',fe.complemento, ' - ', uvw.endereco_bairro, ' - ', uvw.endereco_cidade, '/', uvw.endereco_estado) from funcionarios_enderecos fe JOIN uvw_endereco uvw ON fe.codigo_endereco = uvw.endereco_codigo where fe.codigo_funcionario = Funcionario.codigo ORDER BY codigo)",
        'endereco_unidade' => "(SELECT TOP 1 UPPER(CONCAT(ce.logradouro, ', ', ce.numero, ' ', ce.complemento, ' - ', ce.bairro, ' - ', ce.cidade, '/', ce.estado_abreviacao)) from cliente_endereco ce where ce.codigo_cliente = Cliente.codigo AND ce.codigo_tipo_contato = 2 ORDER BY codigo)",
        'endereco_atestado' => "(CASE WHEN LEN(Atestado.endereco) > 0 THEN CONCAT(Atestado.endereco,', ',Atestado.numero,' ',Atestado.complemento, ' - ', Atestado.bairro, ' - ', EnderecoCidade.descricao, '/', EnderecoEstado.descricao) ELSE NULL END)",
        'dist_unidade' => "(SELECT TOP 1 CASE WHEN (Atestado.latitude IS NOT NULL AND latitude IS NOT NULL) THEN  RHHealth.publico.distancia_dois_pontos(Atestado.latitude,Atestado.longitude,latitude,longitude) ELSE NULL END from cliente_endereco where codigo_cliente = Cliente.codigo AND codigo_tipo_contato = 2 ORDER BY codigo)",
        'dist_funcionario' => "(SELECT TOP 1 CASE WHEN (Atestado.latitude IS NOT NULL AND latitude IS NOT NULL) THEN  RHHealth.publico.distancia_dois_pontos(Atestado.latitude,Atestado.longitude,latitude,longitude) ELSE NULL END from funcionarios_enderecos where codigo_funcionario = Funcionario.codigo ORDER BY codigo)"
        );

      $filtros = $this->Filtros->controla_sessao($this->data, 'Atestado');
      $options['conditions'] = $this->Atestado->converteFiltroEmCondition($filtros);
      if(is_numeric($codigo_grupo_economico)) {
        
        $options['recursive'] = '-1';
        $options['conditions']['GrupoEconomicoCliente.codigo_grupo_economico'] = $codigo_grupo_economico;
      }

      $options['fields'] = array(
                  'razao_empresa',
                  'Cliente.nome_fantasia',
                  'Cliente.codigo_documento',
                  'Funcionario.nome',
                  'Setor.descricao',
                  'Cargo.descricao',
                  'ClienteFuncionario.matricula',
                  'Funcionario.cpf',
                  'Funcionario.rg',
                  'Atestado.data_inclusao',
                  'Atestado.data_afastamento_periodo',
                  'Atestado.data_retorno_periodo',
                  'Atestado.hora_afastamento',
                  'Atestado.hora_retorno',
                  'dia_semana',
                  'dias_afastado',
                  'horas_afastado',
                  'Atestado.afastamento_em_dias',
                  'Atestado.afastamento_em_horas',
                  'MotivoAfastamento.descricao',
                  'Esocial.descricao', 
                  'Atestado.restricao',
                  'Medico.nome',
                  'Medico.numero_conselho',
                  'Medico.conselho_uf',
                  'Cid.codigo_cid10',
                  'Cid.descricao',
                  'Cliente.cnae',
                  'Cnae.descricao',
                  'nexo',
                  'endereco_funcionario',
                  'endereco_unidade',
                  'TipoLocalAtendimento.descricao',
                  'Atestado.cep',
                  'endereco_atestado',
                  'dist_funcionario',
                  'dist_unidade'
                  );


      $options['joins'] = array(
          array(
            'table' => 'cliente_funcionario',
            'alias' => 'ClienteFuncionario',
            'type' => 'INNER',
            'conditions' => 'ClienteFuncionario.codigo = Atestado.codigo_cliente_funcionario'
          ),
          array(
            'table' => 'funcionarios',
            'alias' => 'Funcionario',
            'type' => 'INNER',
            'conditions' => 'Funcionario.codigo = ClienteFuncionario.codigo_funcionario'
        ),  
        array(
            'table' => 'funcionario_setores_cargos' ,
            'alias' => 'FuncionarioSetorCargo',
            'type' => 'INNER',
            'conditions' => array (
                        "FuncionarioSetorCargo.codigo = (Select TOP 1 codigo from funcionario_setores_cargos Where codigo_cliente_funcionario = ClienteFuncionario.codigo AND ((data_fim = '' OR data_fim IS NULL) OR (data_fim is not null AND ClienteFuncionario.ativo = 0)) ORDER by codigo DESC)"
                      )
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
            'table' => 'grupos_economicos_clientes',
            'alias' => 'GrupoEconomicoCliente',
            'type' => 'INNER',
            'conditions' => 'GrupoEconomicoCliente.codigo_cliente = FuncionarioSetorCargo.codigo_cliente' 
        ),  
        array(
            'table' => 'cliente',
            'alias' => 'Cliente',
            'type' => 'INNER',
            'conditions' => 'GrupoEconomicoCliente.codigo_cliente = Cliente.codigo'
        ),  
        array(
            'table' => 'motivos_afastamento',
            'alias' => 'MotivoAfastamento',
            'type' => 'INNER',
            'conditions' => 'MotivoAfastamento.codigo = Atestado.codigo_motivo_licenca'
        ),  
        array(
            'table' => 'medicos',
            'alias' => 'Medico',
            'type' => 'INNER',
            'conditions' => 'Medico.codigo = Atestado.codigo_medico'
        ),  
        array(
            'table' => 'esocial',
            'alias' => 'Esocial',
            'type' => 'LEFT',
            'conditions' => array(
                      'Esocial.codigo = Atestado.codigo_motivo_esocial',
                      'Esocial.tabela' => 18
                    )
        ),  
        array(
            'table' => 'atestados_cid',
            'alias' => 'AtestadoCid' ,
            'type' => 'LEFT',
            'conditions' => 'AtestadoCid.codigo_atestado = Atestado.codigo'
        ),  
        array(
            'table' => 'cid',
            'alias' => 'Cid',
            'type' => 'LEFT',
            'conditions' => 'Cid.codigo = AtestadoCid.codigo_cid'
        ),  
        array(
            'table' => 'cnae',
            'alias' => 'Cnae' ,
            'type' => 'LEFT',
            'conditions' => 'Cnae.cnae = Cliente.cnae'
        ),
        array(
            'table' => 'tipos_locais_atendimento',
            'alias' => 'TipoLocalAtendimento' ,
            'type' => 'LEFT',
            'conditions' => 'TipoLocalAtendimento.codigo = Atestado.codigo_tipo_local_atendimento'
        ),
        array(
            'table' => 'endereco_cidade',
            'alias' => 'EnderecoCidade' ,
            'type' => 'LEFT',
            'conditions' => 'EnderecoCidade.codigo = Atestado.codigo_cidade'
        ),
        array(
            'table' => 'endereco_estado',
            'alias' => 'EnderecoEstado' ,
            'type' => 'LEFT',
            'conditions' => 'EnderecoEstado.codigo = Atestado.codigo_estado'
        )
        );

      $options['order'] = array('Cliente.nome_fantasia', 'Funcionario.nome');

      $dados = $this->Atestado->find('all', $options);


      $nome_arquivo = date('YmdHis').'_absenteismo.csv';
        
      ob_clean();
      header('Content-Encoding: UTF-8');
      header('Content-type: text/csv; charset=UTF-8');
      header(sprintf('Content-Disposition: attachment; filename="%s"', $nome_arquivo));
      header('Pragma: no-cache');
      $texto_header =  "Empresa;Unidade;CNPJ;Funcionário;Setor;Cargo;Matrícula;CPF;RG;Data inclusão atestado;Data início atestado;Data final atestado;Horário Inicial Atestado;Horário Final Atestado;Dia da Semana;Quantidade de dias afastados;Quantidade de horas afastadas;Motivo da Licença; Motivo da Licença (Tabela 18 - e-Social);Restrição para o retorno;Nome do médico;CRM;UF;CID10;Nome CID10;CNAE;Descrição CNAE;Nexo;Endereço do Funcionário;Endereço da Unidade;Local de Atendimento;CEP;Endereço;Distância do endereço do funcionário(Km);Distância do endereço da unidade(Km) \n";
       echo utf8_decode($texto_header);
        
      if(!empty($dados)) {
        foreach ($dados as $dado) {

          $datas_iguais = ($dado['Atestado']['data_afastamento_periodo'] == $dado['Atestado']['data_retorno_periodo']);
          $data_inclusao = DateTime::createFromFormat('d/m/Y H:i:s', $dado['Atestado']['data_inclusao']);  
          $hora_afastamento = new DateTime($dado['Atestado']['hora_afastamento']); 
          $hora_retorno = new DateTime($dado['Atestado']['hora_retorno']); 
                  

              $hora_afastamento->diff($hora_retorno);
            $valida_hora = ($dado['Atestado']['hora_afastamento'] < $dado['Atestado']['hora_retorno']);


            $afastado = $hora_afastamento->diff($hora_retorno);
          

            if(strlen($afastado->h) <  2){
              $hf = str_pad($afastado->h, 2, "0", STR_PAD_LEFT); 
            } else {
              $hf = $afastado->h;
            }

            if(strlen($afastado->i) < 2){
              $mf = str_pad($afastado->i, 2, "0", STR_PAD_LEFT); 
            } else {
              $mf = $afastado->i;
            }

            $afastado_formatado = $hf.":".$mf;

            $dias_afastado = $dado['Atestado']['dias_afastado'];
            $horas_afastado = $dado['Atestado']['horas_afastado'];

            if(!$datas_iguais || ($datas_iguais && ($afastado->h == 0 || $afastado->i == 0))){
              $dias_afastado += 1;
            }

            if(!$datas_iguais && ($afastado->h > 0 || $afastado->i > 0)){
              $minutos = ($afastado->h * 60) + $afastado->i;
              $tempo_minutos = $minutos * $dias_afastado;
              $h = $tempo_minutos / 60;
              $i = $tempo_minutos % 60;

              if($h > 24) {
                $h = $h -24;
                $dias_afastado += 1;
              }

            if(strlen($h) <  2){
              $hf = str_pad($h, 2, "0", STR_PAD_LEFT); 
            } else {
              $hf = $h;
            }

            if(strlen($i) < 2){
              $mf = str_pad($i, 2, "0", STR_PAD_LEFT); 
            } else {
              $mf = $i;
            }

              $afastado_formatado = $hf.":".$mf;
              //$horas_afastado = $horas_afastado * $dias_afastado;
          }


          $linha  =  $dado['Atestado']['razao_empresa'].';';
          $linha .=  $dado['Cliente']['nome_fantasia'].';';
          $linha .=  $dado['Cliente']['codigo_documento'].';';
          $linha .=  $dado['Funcionario']['nome'].';';
          $linha .=  $dado['Setor']['descricao'].';';
          $linha .=  $dado['Cargo']['descricao'].';';
          $linha .=  $dado['ClienteFuncionario']['matricula'].';';
          $linha .=  $dado['Funcionario']['cpf'].';';
          $linha .=  $dado['Funcionario']['rg'].';';
          $linha .=  $data_inclusao->format('d/m/Y').';';
          $linha .=  $dado['Atestado']['data_afastamento_periodo'].';';
          $linha .=  $dado['Atestado']['data_retorno_periodo'].';';
          $linha .=  $hora_afastamento->format('H:i').';';
          $linha .=  $hora_retorno->format('H:i').';';
          $linha .=  $dias_semana[$dado['Atestado']['dia_semana']].';';
          //$linha .=  $dado['Atestado']['dias_afastado'].';';
         //$linha .=  $dado['Atestado']['afastamento_em_horas'].';';
           $linha .=  ((!$datas_iguais || ($datas_iguais && ($afastado->h == 0 && $afastado->i == 0))) ? $dias_afastado : " - ").';';
          // $linha .=  ($datas_iguais ? $dado['Atestado']['afastamento_em_horas'] : "").';';
          $linha .=  (( $valida_hora) && ($afastado->h > 0 || $afastado->i > 0) ? $afastado_formatado : " - ").';';
          $linha .=  $dado['MotivoAfastamento']['descricao'].';';
          $linha .=  $dado['Esocial']['descricao'].';';
          $linha .=  $dado['Atestado']['restricao'].';';
          $linha .=  $dado['Medico']['nome'].';';
          $linha .=  $dado['Medico']['numero_conselho'].';';
          $linha .=  $dado['Medico']['conselho_uf'].';';
          $linha .=  $dado['Cid']['codigo_cid10'].';';
          $linha .=  $dado['Cid']['descricao'].';';
          $linha .=  $dado['Cliente']['cnae'].';';
          $linha .=  $dado['Cnae']['descricao'].';';
          $linha .=  ($dado['Atestado']['nexo'] == 1 ? 'S' : 'N').';';
          $linha .=  $dado['Atestado']['endereco_funcionario'].';';
          $linha .=  $dado['Atestado']['endereco_unidade'].';';
          $linha .=  $dado['TipoLocalAtendimento']['descricao'].';';
          $linha .=  $dado['Atestado']['cep'].';';
          $linha .=  $dado['Atestado']['endereco_atestado'].';';
          $linha .=  $dado['Atestado']['dist_funcionario'].';';
          $linha .=  $dado['Atestado']['dist_unidade'].';';
          $linha .=  "\n";
          echo utf8_decode($linha);
        }
      }
      
      exit;

    }
    
}