<?php
class FornecedoresCapacidadeAgendaController extends AppController {
    public $name = 'FornecedoresCapacidadeAgenda';
    
    var $uses = array(
    	'FornecedorCapacidadeAgenda',
    	'FornecedorGradeAgenda',
    	'Fornecedor',
		'VEndereco',
		'FornecedorEndereco',
		'FornecedorHorario',
		'EnderecoEstado',
		'Endereco',
		'EnderecoCidade',
    	'Usuario'
	);
    
    public function beforeFilter() {
        parent::beforeFilter();
        $this->BAuth->allow('obtem_horarios_para_bloqueio', 'salvar_horarios', 'exclui_horario', 'grava_agenda');
    }
    
    function retorna_estados($data){
        $estados = $this->EnderecoEstado->retorna_estados();
        
        if(isset($this->data['FornecedorCapacidadeAgenda']['estado']) && $this->data['FornecedorCapacidadeAgenda']['estado']) {
             $cidades = array('' => 'Selecione o Estado Primeiro') + $this->EnderecoCidade->find('list', array('conditions' => array('codigo_endereco_estado' => $this->data['FornecedorCapacidadeAgenda']['estado']), 'fields' => array('codigo', 'descricao'),'order' => 'descricao'));
        } else {
            $cidades = array('' => 'Selecione o Estado Primeiro');
        }

        $this->set(compact('estados', 'cidades'));
    }

    function index() {        
        $this->data['FornecedorCapacidadeAgenda'] = $this->Filtros->controla_sessao($this->data, $this->FornecedorCapacidadeAgenda->name);
        $this->retorna_estados($this->data);
    }
    
    function listagem($destino) {
		ini_set('memory_limit','2G');
		set_time_limit(0);
        $this->layout = 'ajax';
        $filtros = $this->Filtros->controla_sessao($this->data, $this->FornecedorCapacidadeAgenda->name);
        $conditions = $this->FornecedorCapacidadeAgenda->converteFiltroEmCondition($filtros);
        
        $fields = array(
            'Fornecedor.codigo',
            'Fornecedor.nome',
            'Fornecedor.razao_social',
            'Fornecedor.codigo_documento',
            'Fornecedor.ativo',
            'FornecedorEndereco.cidade',
            'FornecedorEndereco.estado_descricao'
        );

        $joins  = array(
            array(
              'table' => $this->Usuario->databaseTable.'.'.$this->Usuario->tableSchema.'.'.$this->Usuario->useTable,
              'alias' => 'Usuario',
              'type' => 'LEFT',
              'conditions' => 'Usuario.codigo_fornecedor = Fornecedor.codigo',
            ),
            array(
              'table' => $this->FornecedorEndereco->databaseTable.'.'.$this->FornecedorEndereco->tableSchema.'.'.$this->FornecedorEndereco->useTable,
              'alias' => 'FornecedorEndereco',
              'type' => 'LEFT',
              'conditions' => 'FornecedorEndereco.codigo_fornecedor = Fornecedor.codigo',
            )
        );  
        
        $order = array('Fornecedor.codigo DESC','Fornecedor.razao_social ASC');
		
		// echo "<pre>".$this->Fornecedor->find('sql', compact('conditions', 'joins'))."</pre>";die;
        $this->paginate['Fornecedor'] = array(
            'fields' => $fields,
            'conditions' => $conditions,
            'joins' => $joins,
            'limit' => 50,
            'order' => $order,
        );
        
        $fornecedores = $this->paginate('Fornecedor');
        $this->set(compact('fornecedores'));
    }
    
    public function agenda_por_exame($codigo_fornecedor) {
    	$this->pageTitle = 'Agenda do Prestador';
    	$dados_fornecedor = $this->Fornecedor->find('first', array('conditions' => array('Fornecedor.codigo' => $codigo_fornecedor), 'fields' => array('Fornecedor.codigo', 'Fornecedor.razao_social', 'Fornecedor.codigo_documento'), 'recursive' => -1));
    	$dados_fornecedor['Fornecedor']['codigo_documento'] = Comum::formatarDocumento($dados_fornecedor['Fornecedor']['codigo_documento']);
    	
    	$options['fields'] = array(
    		'DISTINCT FornecedorCapacidadeAgenda.codigo_lista_de_preco_produto_servico',
			'Servico.descricao',
			'ListaDePrecoProdutoServico.codigo',
			'ListaDePrecoProdutoServico.valor',
			'ListaDePreco.codigo_fornecedor',
    		'FornecedorCapacidadeAgenda.ativo'
    	);
    	
    	$options['joins']  = array(
			array(
    			'table' => 'listas_de_preco_produto_servico',
    			'alias' => 'ListaDePrecoProdutoServico',
    			'type' => 'INNER',
    			'conditions' => 'ListaDePrecoProdutoServico.codigo = FornecedorCapacidadeAgenda.codigo_lista_de_preco_produto_servico',
    		),
    		array(
    			'table' => 'servico',
    			'alias' => 'Servico',
    			'type' => 'INNER',
    			'conditions' => 'Servico.codigo = ListaDePrecoProdutoServico.codigo_servico',
    		),
    		array(
    			'table' => 'listas_de_preco_produto',
    			'alias' => 'ListaDePrecoProduto',
    			'type' => 'INNER',
    			'conditions' => 'ListaDePrecoProduto.codigo = ListaDePrecoProdutoServico.codigo_lista_de_preco_produto',
    		),
    		array(
    			'table' => 'listas_de_preco',
    			'alias' => 'ListaDePreco',
    			'type' => 'INNER',
    			'conditions' => 'ListaDePreco.codigo = ListaDePrecoProduto.codigo_lista_de_preco',
    		)    			
    	);
    			
    	$options['conditions'] = array('FornecedorCapacidadeAgenda.codigo_fornecedor' => $codigo_fornecedor);
    	$resultado_exames = $this->FornecedorCapacidadeAgenda->find('all', $options);
    	
    	$horarios_atendimento = $this->__horario_atendimento($codigo_fornecedor);
    	$this->set(compact('dados_fornecedor', 'resultado_exames', 'horarios_atendimento', 'codigo_fornecedor'));
    }
    
    public function gera_grade() {
    	
    	$codigo_lista_de_preco_produto_servico = $this->data['ListaDePrecoProdutoServico']['codigo'];
    	$codigo_fornecedor = $this->data['Fornecedor']['codigo'];
    	
    	$horas_disponiveis = array();
    	$dias_semana = $this->__dias_semana();
    	$lista_horario = $this->__retorna_combo_horario_minuto();
    	$quantide_intervalos = count($this->data['FornecedorCapacidadeAgenda']);

    	$horario_formatado = array();
    	
    	unset($_SESSION['capacidade_por_dia'][$codigo_fornecedor][$codigo_lista_de_preco_produto_servico]);
    	unset($_SESSION['horas_disponiveis'][$codigo_fornecedor][$codigo_lista_de_preco_produto_servico]);
    	unset($_SESSION['horario_disponiveis_agenda'][$codigo_fornecedor][$codigo_lista_de_preco_produto_servico]);
    	
    	// adiciona faixa da agenda
    	foreach($this->data['FornecedorCapacidadeAgenda'] as $key => $item) {
    		
   			$dias_da_semana = $this->__retorna_dia_semana($item['dias_semana']);
   			
   			foreach($dias_da_semana as $k_dia => $dia) {
    				
   				$hora_inicio = $item['hora_inicio'] . str_pad($item['minuto_inicio'], 2, 0, STR_PAD_LEFT);
   				$hora_fim = $item['hora_fim'] . str_pad($item['minuto_fim'], 2, 0, STR_PAD_LEFT);
   				
   				$array_parametros =  array(
   					'dia_semana' => $k_dia,
   					'capacidade' => $item['quantidade_medico'],
   					'codigo_fornecedor' => $codigo_fornecedor,
   					'ativo' => '1',
   					'codigo_lista_de_preco_produto_servico' => $codigo_lista_de_preco_produto_servico,
   					'hora_inicio' => $hora_inicio,
   					'hora_fim' => $hora_fim,
   					'tempo_consulta' => $lista_horario['tempo_consulta'][$item['tempo_atendimento']],
  					'qtd_medico' => $item['quantidade_medico']
   				);    				
   				
   				$_SESSION['capacidade_por_dia'][$codigo_fornecedor][$codigo_lista_de_preco_produto_servico][$k_dia][$hora_inicio] = $array_parametros;
    				
   				for($hora = 0; $hora <= 23; $hora++) {
   					if(($hora >= $item['hora_inicio']) && ($hora <= $item['hora_fim'])) {
   						if(($hora == $item['hora_inicio']) && $item['minuto_inicio'] != 0) {
   							$tempo_hora = 60 - $item['minuto_inicio'];
   							$horas_disponiveis[$k_dia][$hora] = floor ($tempo_hora / $lista_horario['tempo_consulta'][$item['tempo_atendimento']]) * $item['quantidade_medico'];
   						} else if(($hora == $item['hora_fim']) && $item['minuto_fim'] != 0) {
   							$horas_disponiveis[$k_dia][$hora] = floor ($item['minuto_fim'] / $lista_horario['tempo_consulta'][$item['tempo_atendimento']]) * $item['quantidade_medico'];
   						} else {
   							$horas_disponiveis[$k_dia][$hora] = (60 / $lista_horario['tempo_consulta'][$item['tempo_atendimento']]) * $item['quantidade_medico'];
   						}
   					}
   				}
   				
   			}
    	}
    	
    	$_SESSION['horas_disponiveis'][$codigo_fornecedor][$codigo_lista_de_preco_produto_servico] = $horas_disponiveis;
    	$this->set(compact('dias_da_semana', 'horas_disponiveis', 'lista_horario', 'dias_semana', 'quantide_intervalos', 'codigo_fornecedor', 'codigo_lista_de_preco_produto_servico'));
    }
    
    function gera_agenda() {
    	
    	$codigo_fornecedor = $this->params['data']['Fornecedor']['codigo'];
    	$codigo_lista_de_preco_produto_servico = $this->params['data']['ListaDePrecoProdutoServico']['codigo'];

    	// limpa agenda!
    	unset($_SESSION['horario_disponiveis_agenda'][$codigo_fornecedor]);
    	
		ksort($_SESSION['capacidade_por_dia'][$codigo_fornecedor][$codigo_lista_de_preco_produto_servico]);
    	foreach($_SESSION['capacidade_por_dia'][$codigo_fornecedor][$codigo_lista_de_preco_produto_servico] as $k_dia => $faixa) {
    		
			ksort($faixa);
			foreach($faixa as $param) {
				
				ksort($_SESSION['horas_disponiveis'][$codigo_fornecedor][$codigo_lista_de_preco_produto_servico][$k_dia]);
	    		foreach($_SESSION['horas_disponiveis'][$codigo_fornecedor][$codigo_lista_de_preco_produto_servico][$k_dia] as $k_hora => $antiga_capacidade) {

	    			$hora_inicio = (int) substr((str_pad($param['hora_inicio'], 4, 0, STR_PAD_LEFT)), 0, 2);
	    			$minuto_inicio = ($k_hora == $hora_inicio) ? (int) substr((str_pad($param['hora_inicio'], 4, 0, STR_PAD_LEFT)), 2, 2) : 0;
	    			
	    			$hora_fim = (int) substr((str_pad($param['hora_fim'], 4, 0, STR_PAD_LEFT)), 0, 2);
	    			$minuto_fim = ($k_hora == $hora_fim) ?  (int) substr((str_pad($param['hora_fim'], 4, 0, STR_PAD_LEFT)), 2, 2) : 60;
	    			
	    			if(!empty($this->params['data']['FornecedorCapacidadeAgenda'][$k_dia][$k_hora]['capacidade'])) {
	    				if($param['tempo_consulta'] != 0) {
                            if((($minuto_fim - $minuto_inicio) / ($param['tempo_consulta'] / $param['qtd_medico'])) != $this->params['data']['FornecedorCapacidadeAgenda'][$k_dia][$k_hora]['capacidade']) {
                                $novo_tempo = ($minuto_fim - $minuto_inicio) / ($this->params['data']['FornecedorCapacidadeAgenda'][$k_dia][$k_hora]['capacidade'] / $param['qtd_medico']);

                                if($novo_tempo < $param['tempo_consulta']) {
                                    $param['tempo_consulta'] = $novo_tempo;
                                }
                            }
                            for($minuto = $minuto_inicio; $minuto < $minuto_fim; $minuto = ($minuto + $param['tempo_consulta'])) {
                                if(($k_hora >= $hora_inicio) && ($k_hora <= $hora_fim) && ($minuto_fim - $minuto) >= $param['tempo_consulta']) {
                                    $horario_formatado[$k_dia][$k_hora][$minuto] = $param['qtd_medico'];

                                    $array_insere = array(
                                        'dia_semana' => $k_dia,
                                        'hora' => $k_hora . str_pad($minuto, 2, 0, STR_PAD_LEFT),
                                        'capacidade_simultanea' => $param['qtd_medico'],
                                        'tempo_consulta' => $param['tempo_consulta'],
                                        'codigo_fornecedor' => $codigo_fornecedor,
                                        'codigo_lista_de_preco_produto_servico' => $codigo_lista_de_preco_produto_servico,
                                        'ativo' => '1'
                                        );

                                    $_SESSION['horario_disponiveis_agenda'][$codigo_fornecedor][$codigo_lista_de_preco_produto_servico][] = $array_insere;
                                }
                            }                   
                        }
                    }
                }
			}
    	}
    	
    	ksort($horario_formatado);
    	$dias_semana = $this->__dias_semana();
    	
    	$this->set(compact('horario_formatado', 'codigo_fornecedor', 'codigo_lista_de_preco_produto_servico', 'dias_semana'));
    }
    
    function grava_agenda($codigo_fornecedor, $codigo_lista_de_preco_produto_servico) {
    	$this->layout = 'ajax';

    	try {
    
    		$this->FornecedorGradeAgenda->query("BEGIN TRANSACTION");
    
    		// deleta agenda: (fornecedor / exame)
    		$this->FornecedorCapacidadeAgenda->deleteAll(array('codigo_fornecedor' => $codigo_fornecedor, 'codigo_lista_de_preco_produto_servico' => $codigo_lista_de_preco_produto_servico));
    		$this->FornecedorGradeAgenda->deleteAll(array('codigo_fornecedor' => $codigo_fornecedor, 'codigo_lista_de_preco_produto_servico' => $codigo_lista_de_preco_produto_servico));
    
    		foreach($_SESSION['capacidade_por_dia'][$codigo_fornecedor][$codigo_lista_de_preco_produto_servico] as $key => $faixa) {
    			foreach($faixa as $k => $insere_FornecedorCapacidadeAgenda) {
    				$this->FornecedorCapacidadeAgenda->incluir($insere_FornecedorCapacidadeAgenda);
    			}
    		}
    
    		foreach($_SESSION['horario_disponiveis_agenda'][$codigo_fornecedor][$codigo_lista_de_preco_produto_servico] as $key => $insere_FornecedorGradeAgenda) {
    			$this->FornecedorGradeAgenda->incluir($insere_FornecedorGradeAgenda);
    		}
    
    		$this->BSession->setFlash('save_success');
    		$this->FornecedorGradeAgenda->commit();
    		print "1";
    
    	} catch(Exception $e) {
    		$this->BSession->setFlash('save_error');
    		$this->FornecedorGradeAgenda->rollback();
    		print "0";
    	}
    	exit;
    }    
    
    function editar_status($codigo_fornecedor, $codigo_lista_preco_produto_servico, $status){
    	
    	$this->layout = 'ajax';
    	if(!is_numeric($codigo_fornecedor)){
    		print 0;
    		exit;
    	}
    	
    	$codigo_fornecedor = trim($codigo_fornecedor);
    	$status= ($status == 0) ? 1 : 0;
    	
    	$lista_parametros = $this->FornecedorCapacidadeAgenda->find('all', array('conditions' => array('codigo_fornecedor' => $codigo_fornecedor, 'codigo_lista_de_preco_produto_servico' => $codigo_lista_preco_produto_servico)));
    	$lista_grade = $this->FornecedorGradeAgenda->find('all', array('conditions' => array('codigo_fornecedor' => $codigo_fornecedor, 'codigo_lista_de_preco_produto_servico' => $codigo_lista_preco_produto_servico)));

    	try {
    		
    		$this->FornecedorCapacidadeAgenda->query('BEGIN TRANSACTION');
    		foreach($lista_parametros as $key => $item) {
    			$item['FornecedorCapacidadeAgenda']['ativo'] = $status;
    			$this->FornecedorCapacidadeAgenda->atualizar($item);
    		}
    		
    		foreach($lista_grade as $key => $item) {
    			$item['FornecedorGradeAgenda']['ativo'] = $status;
    			$this->FornecedorGradeAgenda->atualizar($item);
    		}
    		
    		$this->FornecedorCapacidadeAgenda->commit();
    		print "1";
    	} catch(Exception $e) {
    		$this->FornecedorCapacidadeAgenda->rollback();
    		print "0";
    	}
    	
    	exit;
    }
    
    public function editar($codigo_fornecedor, $codigo_lista_preco_produto_servico) {
    	$dados_fornecedor = $this->Fornecedor->find('first', array('conditions' => array('Fornecedor.codigo' => $codigo_fornecedor), 'fields' => array('Fornecedor.codigo', 'Fornecedor.razao_social', 'Fornecedor.codigo_documento'), 'recursive' => -1));
    	$dados_fornecedor['Fornecedor']['codigo_documento'] = Comum::formatarDocumento($dados_fornecedor['Fornecedor']['codigo_documento']);
    	
    	$horarios_atendimento = $this->__grade_parametro($codigo_fornecedor, $codigo_lista_preco_produto_servico);
    	$horarios_atendimento = $this->__agrupa_array_por_periodo($horarios_atendimento);
    	$dias_semana = $this->__dias_semana();
    	
    	$lista_horario = $this->__retorna_combo_horario_minuto();
    	$lista_horario_aux = array_flip($lista_horario['tempo_consulta']);
    	
    	
    	foreach($horarios_atendimento as $key => $item) {
    		$horarios_atendimento[$key]['FornecedorCapacidadeAgenda']['tempo_consulta'] = $lista_horario_aux[str_pad($item['FornecedorCapacidadeAgenda']['tempo_consulta'], 2, 0, STR_PAD_LEFT)];
    		$horarios_atendimento[$key]['FornecedorCapacidadeAgenda']['hora_inicio'] = str_split(sprintf("%04s", $item['FornecedorCapacidadeAgenda']['hora_inicio']), 2);
    		$horarios_atendimento[$key]['FornecedorCapacidadeAgenda']['hora_fim'] = str_split(sprintf("%04s", $item['FornecedorCapacidadeAgenda']['hora_fim']), 2);
    		$horarios_atendimento[$key]['FornecedorCapacidadeAgenda']['dias_semana'] = $this->__dias_atendimento($item['FornecedorCapacidadeAgenda']['dias_semana']);    		
    	}
    	
    	$lista_horarios_disponiveis = $this->__horarios_servico($codigo_fornecedor, $codigo_lista_preco_produto_servico);
    	
    	$horas_capacidade = array();
    	foreach($lista_horarios_disponiveis as $key => $campo) {
    		$hora = (int) substr(str_pad($campo['FornecedorGradeAgenda']['hora'], 4, 0, STR_PAD_LEFT), 0, 2);
    		if(!isset($horas_capacidade[$campo['FornecedorGradeAgenda']['dia_semana']][$hora])) {
    			$horas_capacidade[$campo['FornecedorGradeAgenda']['dia_semana']][$hora] = 0;
    		}
    		$horas_capacidade[$campo['FornecedorGradeAgenda']['dia_semana']][$hora] = $horas_capacidade[$campo['FornecedorGradeAgenda']['dia_semana']][$hora] + $campo['FornecedorGradeAgenda']['capacidade_simultanea'];
    	}

    	$this->set('dias_semana', $dias_semana);
    	$this->set('lista_horario', $lista_horario);
    	$this->set('codigo_fornecedor', $codigo_fornecedor);
    	$this->set('codigo_lista_de_preco_produto_servico', $codigo_lista_preco_produto_servico);
    	$this->set('dados_fornecedor', $dados_fornecedor);
    	$this->set('horas_disponiveis', $horas_capacidade);
    	$this->set('dias_semana', $this->__dias_semana());
    	$this->set('horarios_atendimento', $horarios_atendimento);

        $this->loadModel('Fadb');
        $datas_bloqueadas = $this->Fadb->find('all', array(
            'conditions' => array(
                'Fadb.codigo_fornecedor' => $codigo_fornecedor,
                'Fadb.codigo_lista_de_preco_produto_servico' => $codigo_lista_preco_produto_servico
                ),
            'fields' => array(
                'Fadb.codigo',
                'Fadb.data',
                'Fadb.horarios',
                'Fadb.bloqueado_dia_inteiro',
                ),
            'order' => 'Fadb.data ASC'
            )
        );
    	$this->set(compact('datas_bloqueadas'));   
    	
    	$this->set('lista_servicos', $this->FornecedorCapacidadeAgenda->retornaServico($codigo_lista_preco_produto_servico));
    }
    
    private function __agrupa_array_por_periodo($horarios_atendimento) {
    	
    	$array_organiza = array();
    	$param = array( '0' => 'dom', '1' => 'seg', '2' => 'ter', '3' => 'qua', '4' => 'qui', '5' => 'sex', '6' => 'sab' );
    	 
    	foreach($horarios_atendimento as $key => $item) {
    		if(isset($array_organiza[$item['FornecedorCapacidadeAgenda']['hora_inicio']][$item['FornecedorCapacidadeAgenda']['hora_fim']]['FornecedorCapacidadeAgenda']['dias_semana'])) {
    			$array_organiza[$item['FornecedorCapacidadeAgenda']['hora_inicio']][$item['FornecedorCapacidadeAgenda']['hora_fim']]['FornecedorCapacidadeAgenda']['dias_semana'] = $array_organiza[$item['FornecedorCapacidadeAgenda']['hora_inicio']][$item['FornecedorCapacidadeAgenda']['hora_fim']]['FornecedorCapacidadeAgenda']['dias_semana'] . "," . $param[$item['FornecedorCapacidadeAgenda']['dia_semana']];
    		} else {
    			$array_organiza[$item['FornecedorCapacidadeAgenda']['hora_inicio']][$item['FornecedorCapacidadeAgenda']['hora_fim']] = $item;
    			$array_organiza[$item['FornecedorCapacidadeAgenda']['hora_inicio']][$item['FornecedorCapacidadeAgenda']['hora_fim']]['FornecedorCapacidadeAgenda']['dias_semana'] = $param[$item['FornecedorCapacidadeAgenda']['dia_semana']];
    		}
    	}
    	 
    	$horarios_atendimento = array();
    	foreach($array_organiza as $k => $hora_inicio) {
    		foreach($hora_inicio as $key => $item) {
    			$horarios_atendimento[] = $item;
    		}
    	}
    	
    	return $horarios_atendimento;
    } 
    
    
    public function incluir($codigo_fornecedor) {
    	
    	$dados_fornecedor = $this->Fornecedor->find('first', array('conditions' => array('Fornecedor.codigo' => $codigo_fornecedor), 'fields' => array('Fornecedor.codigo', 'Fornecedor.razao_social', 'Fornecedor.codigo_documento'), 'recursive' => -1));
    	$dados_fornecedor['Fornecedor']['codigo_documento'] = Comum::formatarDocumento($dados_fornecedor['Fornecedor']['codigo_documento']);
    	
    	$horarios_atendimento = $this->__horario_atendimento($codigo_fornecedor);
    	
    	foreach($horarios_atendimento as $k => $horario) {
    		$horarios_atendimento[$k]['FornecedorHorario']['hora_inicio'] = str_split(sprintf("%04s", $horario['FornecedorHorario']['de_hora']), 2);
    		$horarios_atendimento[$k]['FornecedorHorario']['hora_fim'] = str_split(sprintf("%04s", $horario['FornecedorHorario']['ate_hora']), 2);
    		$horarios_atendimento[$k]['FornecedorHorario']['dias_semana'] = $this->__dias_atendimento($horario['FornecedorHorario']['dias_semana']);
    	}

   		$this->set('lista_servicos', array('' => 'SELECIONAR O EXAME') + $this->FornecedorCapacidadeAgenda->listaServicosPorFornecedor($codigo_fornecedor));
    	$this->set('horarios_atendimento', $horarios_atendimento);
    	$this->set('lista_horario', $this->__retorna_combo_horario_minuto());
    	$this->set('dados_fornecedor', $dados_fornecedor);
    	$this->set('codigo_fornecedor', $codigo_fornecedor);
    }

    public function obtem_horarios_para_bloqueio()
    {
        $this->autoRender = false;
        $week_days = array(
            'Sun' => 7,
            'Mon' => 1,
            'Tue' => 2,
            'Wed' => 3,
            'Thu' => 4,
            'Fri' => 5,
            'Sat' => 6
            );
        $nomes_dias_semana = array(
            1 => 'Segunda-feira',
            2 => 'Terça-feira',
            3 => 'Quarta-feira',
            4 => 'Quinta-feira',
            5 => 'Sexta-feira',
            6 => 'Sábado',
            7 => 'Domingo',
            );
        $data = $_POST['data'];
        $data = explode('/', $data);
        $data = $data[2].'-'.$data[1].'-'.$data[0]; //inverte a data
        $week_day = date('D', strtotime($data));
        $dia_da_semana = $week_days[$week_day];
        $horarios_atendimento = $this->FornecedorCapacidadeAgenda->find('all', array(
            'conditions' => array(
                'codigo_fornecedor' => $_POST['codigo_fornecedor'], 
                'codigo_lista_de_preco_produto_servico' => $_POST['codigo_lista_preco_produto_servico'],
                'dia_semana' => $dia_da_semana,
                'ativo' => 1
                ),
            'fields' => array(
                'FornecedorCapacidadeAgenda.hora_inicio',
                'FornecedorCapacidadeAgenda.hora_fim',
                'FornecedorCapacidadeAgenda.tempo_consulta',
                )
            )
        );
        $horarios = array();
        // monta o array com os horarios disponiveis do fornecedor
        foreach ($horarios_atendimento as $key => $horario) {
            while($horario['FornecedorCapacidadeAgenda']['hora_inicio'] <= $horario['FornecedorCapacidadeAgenda']['hora_fim']) {
                $horarios[] = $horario['FornecedorCapacidadeAgenda']['hora_inicio'];
                $horario['FornecedorCapacidadeAgenda']['hora_inicio'] = (int)($horario['FornecedorCapacidadeAgenda']['hora_inicio'] + $horario['FornecedorCapacidadeAgenda']['tempo_consulta']);
                if(strlen($horario['FornecedorCapacidadeAgenda']['hora_inicio']) < 4) {
                    $minutos = (int)substr($horario['FornecedorCapacidadeAgenda']['hora_inicio'], 1, 2);
                } else {
                    $minutos = (int)substr($horario['FornecedorCapacidadeAgenda']['hora_inicio'], 2, 2);
                }
                if($minutos >= 60) {
                    $horario['FornecedorCapacidadeAgenda']['hora_inicio'] = $horario['FornecedorCapacidadeAgenda']['hora_inicio'] + 100 - 60;
                }
            }
        }

        //========================================================
        $html = '';
        if(!empty($horarios)) { // monta o html para o ajax
            $html .= '<div ><span class="color-blue selecionar-todos-horarios pointer">+ Selecionar todos os horários</span></div>';
            $html .= '<div>';
            foreach ($horarios as $key => $value) {
                if(strlen($value) < 4) {
                    $value = '0' . (String)$value;
                }
                $html .= '<span class="cube cancel-date pull-left" data-value="'.$value.'">'.substr($value, 0, 2).':'.substr($value, 2, 2).'</span>';
            }
            $html .= '</div>';
        } else {
            $html .= '<h5>Não há horários disponíveis para esta data.</h5>';
        }
        return json_encode(array('html' => $html, 'dia_semana' => $nomes_dias_semana[$dia_da_semana]));
    }

    public function salvar_horarios()
    {
        $date = DateTime::createFromFormat('d/m/Y', $_POST['data']);
        $this->autoRender = false;
        $this->loadModel('Fadb');
        $count = $this->Fadb->find('count', array(
            'conditions' => array(
                'Fadb.codigo_fornecedor' => $_POST['codigo_fornecedor'],
                'Fadb.codigo_lista_de_preco_produto_servico' => $_POST['codigo_lista_preco_produto_servico'],
                'Fadb.data' => $date->format('Y-m-d'),
                )
            )
        );
        if($count > 0) {
            return json_encode(array('error' => true, 'message' => 'Esta data já está bloqueada no sistema. Para Alterá-la, remova o bloqueio de data atual selecionando a opção "Datas bloqueadas".'));
        }
        $data['Fadb']['data']                                      = $date->format('Y-m-d');
        if($_POST['diaInteiro'] == 'true') {
            $data['Fadb']['bloqueado_dia_inteiro']                 = 1;
        } else {
            $data['Fadb']['horarios']                              = json_encode($_POST['horarios']);
        }
        $data['Fadb']['codigo_fornecedor']                         = $_POST['codigo_fornecedor'];
        $data['Fadb']['codigo_lista_de_preco_produto_servico']     = $_POST['codigo_lista_preco_produto_servico'];
        $data['Fadb']['ativo']                                     = 1;
       if($this->Fadb->save($data)) {
            return json_encode(array('error' => false, 'codigo' => $this->Fadb->id));
       } else {
            return json_encode(array('error' => true, 'message' => 'Ouve uma falha na gravação do arquivo, tente novamente'));
       }
    }

    public function exclui_horario()
    {
        $this->autoRender = false;
        $this->loadModel('Fadb');
        if($this->Fadb->delete(array(
            'Fadb.codigo' => $_POST['codigo'], 
            'Fabd.codigo_fornecedor' =>  $_POST['codigo_fornecedor'], 
            'Fadb.codigo_lista_de_preco_produto_servico' => $_POST['codigo_lista_preco_produto_servico']))) {
            return true;
        } else {
            return false;
        }
    }
    
    private function __dias_atendimento($dias) {
    	$dias_disponiveis = array_flip(explode(",", $dias));
    	
    	return array(
    		'dom' => isset($dias_disponiveis['dom']) ? '1' : '0',
    		'seg' => isset($dias_disponiveis['seg']) ? '1' : '0',
    		'ter' => isset($dias_disponiveis['ter']) ? '1' : '0',
    		'qua' => isset($dias_disponiveis['qua']) ? '1' : '0',
    		'qui' => isset($dias_disponiveis['qui']) ? '1' : '0',
    		'sex' => isset($dias_disponiveis['sex']) ? '1' : '0',
    		'sab' => isset($dias_disponiveis['sab']) ? '1' : '0'
    	);
    }
    
    private function __retorna_dia_semana($dias) {
    	$param = array(
    		'dom' => '0',
    		'seg' => '1',
    		'ter' => '2',
    		'qua' => '3',
    		'qui' => '4',
    		'sex' => '5',
    		'sab' => '6'
    	);
    	 
    	$retorno = array();
    	foreach($dias as $k => $temp) {
    		if($temp) {
    			$retorno[$param[strtolower($k)]] = $temp;
    		}
    	}
    	 
    	return $retorno;
    }
    
    private function __retorna_combo_horario_minuto() {
    	for($h=0; $h <= 23; $h++)
    		$horas[$h] =str_pad($h, 2, 0, STR_PAD_LEFT);
    	
    	for($m=0; $m <= 59; $m = $m+5)
    		$minutos[$m] =str_pad($m, 2, 0, STR_PAD_LEFT);
    	
    	$tempo_consulta = array();
    	foreach($minutos as $k => $minuto) {
    		if(($minuto != 0) && ((60 % $minuto) == 0)) {
    			$tempo_consulta[] = $minuto;
    		}
    	}
    	$tempo_consulta[] = 60;
    	
		return array('horas' => $horas, 'minutos' => $minutos, 'tempo_consulta' => $tempo_consulta);    	
    }
    
    private function __horario_atendimento($codigo_fornecedor) {
    	return $this->FornecedorHorario->find('all', array('conditions' => array('codigo_fornecedor' => $codigo_fornecedor)));
    }
    
    private function __grade_parametro($codigo_fornecedor, $codigo_lista_preco_produto_servico) {
    	return $this->FornecedorCapacidadeAgenda->find('all', array('conditions' => array('codigo_fornecedor' => $codigo_fornecedor, 'codigo_lista_de_preco_produto_servico' => $codigo_lista_preco_produto_servico)));
    }
    
    private function __horarios_servico($codigo_fornecedor, $codigo_lista_preco_produto_servico) {
    	return $this->FornecedorGradeAgenda->find('all', array('conditions' => array('codigo_fornecedor' => $codigo_fornecedor, 'codigo_lista_de_preco_produto_servico' => $codigo_lista_preco_produto_servico)));
    }    
    
    private function __dias_semana() {
    	return array( 0 => 'DOM', 1 => 'SEG', 2 => 'TER', 3 => 'QUA', 4 => 'QUI', 5 => 'SEX', 6 => 'SAB');
    }    


    
}