<?php
class PosSwtFormRespondido extends AppModel {
	var $name = 'PosSwtFormRespondido';
	var $tableSchema = 'dbo';
	var $databaseTable = 'RHHealth';
	var $useTable = 'pos_swt_form_respondido';
	var $primaryKey = 'codigo';
	var $actsAs = array('Secure', 'Containable', 'Loggable' => array('foreign_key' => 'codigo_form_respondido'));

	
	public function Conditionlistagem_relatorio_swt($data, $tipo = null){
		$conditions = array();
		
		if (!empty($data['codigo_cliente'])) {			
			$conditions['GrupoEconomico.codigo_cliente'] = $data['codigo_cliente']; 
			// $conditions[] = "PosSwtFormRespondido.codigo_cliente_unidade IN (select codigo_cliente from grupos_economicos_clientes where codigo_grupo_economico IN (select codigo from grupos_economicos where codigo_cliente = ". $data['codigo_cliente']."))";
		}

		if (!empty($data['codigo_cliente_alocacao'])) {			
			$conditions['GrupoEconomicoCliente.codigo_cliente'] = $data['codigo_cliente_alocacao']; 
		}

        if(!empty($data['codigo_setor'])) {
            $conditions['Setor.codigo'] = $data['codigo_setor'];
        }

        if(!empty($tipo)){
        	if($tipo == 'analise_swt'){
				//pega o tipo de relatorio 1 swt
				$conditions['PosSwtForm.form_tipo'] = 2;
        	} else {
        		$conditions['PosSwtForm.form_tipo'] = 1;//pega o tipo de relatorio 1 swt
        	}
        }

		if(!empty($data['cliente_opco'])) {
			$conditions['ClienteOpco.codigo'] = $data['cliente_opco'];
		}

		if(!empty($data['cliente_bu'])) {
			$conditions['ClienteBu.codigo'] = $data['cliente_bu'];
		}

		if ($tipo == 'analise_swt'){

			if(!empty($data['id_analise_walk_talk'])){
				$conditions['PosSwtFormRespondido.codigo'] = $data['id_analise_walk_talk'];
			}

			if(!empty($data['id_walk_talk'])) {
				$conditions['PosSwtFormRespondido.codigo_form_respondido_swt'] = $data['id_walk_talk'];
			}			
		} else {
			if(!empty($data['id_walk_talk'])) {
				$conditions['PosSwtFormRespondido.codigo'] = $data['id_walk_talk'];
			}
		}


		if(!empty($data['observador'])) {
			$conditions['PosSwtFormRespondido.codigo_usuario_observador'] = $data['observador'];
		}

		return $conditions;
	}

	public function getPosSwtFormRespondido(array $conditions = array(), $pagination = false, $tipo = null){

		$fields = array(
			'Cliente.codigo',
			'Cliente.razao_social',
			'Cliente.nome_fantasia',
			'Setor.codigo',
			'Setor.descricao',
			'PosSwtFormRespondido.codigo',
			'Usuario.codigo',
			'Usuario.nome',		
			'PosSwtFormRespondido.resultado',
			'UsuarioFacilitador.nome',
			'PosSwtFormRespondido.codigo_form_respondido_swt',
			'PosSwtFormResumo.data_obs',
			'PosSwtFormResumo.hora_obs',
			'PosSwtFormResumo.desc_atividade',
			'PosSwtFormResumo.descricao',
			'ClienteOpco.codigo',
			'ClienteOpco.descricao',
			'ClienteBu.codigo',
			'ClienteBu.descricao'
		);

		$joins = array(
			array(
				'table' => 'pos_swt_form',
				'alias' => 'PosSwtForm',
				'type' => 'INNER',
				'conditions' => array('PosSwtFormRespondido.codigo_form = PosSwtForm.codigo')
			),
			array(
				'table' => 'grupos_economicos_clientes',
				'alias' => 'GrupoEconomicoCliente',
				'type' => 'INNER',
				'conditions' => array('GrupoEconomicoCliente.codigo_cliente = PosSwtFormRespondido.codigo_cliente_unidade')
			),
			array(
				'table' => 'grupos_economicos',
				'alias' => 'GrupoEconomico',
				'type' => 'INNER',
				'conditions' => array('GrupoEconomicoCliente.codigo_grupo_economico = GrupoEconomico.codigo')
			),
			array(
				'table' => 'cliente',
				'alias' => 'Cliente',
				'type' => 'INNER',
				'conditions' => array('GrupoEconomico.codigo_cliente = Cliente.codigo')
			),
			array(
				'table' => 'usuario',
				'alias' => 'Usuario',
				'type' => 'INNER',
				'conditions' => array('PosSwtFormRespondido.codigo_usuario_observador = Usuario.codigo')
			),
			array(
				'table' => 'setores',
				'alias' => 'Setor',
				'type' => 'LEFT',
				'conditions' => array('Setor.codigo = PosSwtFormRespondido.codigo_setor')
			),
			array(
				'table' => 'pos_swt_form_facilitadores',
				'alias' => 'PosSwtFormFacilitador',
				'type' => 'LEFT',
				'conditions' => array('PosSwtFormFacilitador.codigo_form_respondido = PosSwtFormRespondido.codigo')
			),
			array(
				'table' => 'usuario',
				'alias' => 'UsuarioFacilitador',
				'type' => 'LEFT',
				'conditions' => array('UsuarioFacilitador.codigo = PosSwtFormFacilitador.codigo_usuario')
			)
		);

		if ($tipo == 'relatorio_swt') {
			$joins[] = array(
				'table' => 'pos_swt_form_resumo',
				'alias' => 'PosSwtFormResumo',
				'type' => 'INNER',
				'conditions' => array('PosSwtFormResumo.codigo_form_respondido = PosSwtFormRespondido.codigo')
			);
			
		} else if ($tipo == 'analise_swt') {
			$joins[] = array(
				'table' => 'pos_swt_form_resumo',
				'alias' => 'PosSwtFormResumo',
				'type' => 'LEFT',
				'conditions' => array('PosSwtFormRespondido.codigo_form_respondido_swt = PosSwtFormResumo.codigo_form_respondido')
			);
		}

		$joins[] = array(
			'table' => 'cliente_opco',
			'alias' => 'ClienteOpco',
			'type' => 'LEFT',
			'conditions' => array('PosSwtFormResumo.codigo_cliente_opco = ClienteOpco.codigo')
		);
		$joins[] = array(
			'table' => 'cliente_bu',
			'alias' => 'ClienteBu',
			'type' => 'LEFT',
			'conditions' => array('PosSwtFormResumo.codigo_cliente_bu = ClienteBu.codigo')
		);

		if (!$pagination) {
			if ($tipo == 'analise_swt') {
				$joins[] = array(
					'table' => 'pos_swt_form_acao_melhoria',
					'alias' => 'PosSwtAcaoMelhoria',
					'type' => 'LEFT',
					'conditions' => array('PosSwtFormRespondido.codigo_form_respondido_swt = PosSwtAcaoMelhoria.codigo_form_respondido')
				);			
			} else {
				$joins[] = array(
					'table' => 'pos_swt_form_acao_melhoria',
					'alias' => 'PosSwtAcaoMelhoria',
					'type' => 'LEFT',
					'conditions' => array('PosSwtFormRespondido.codigo = PosSwtAcaoMelhoria.codigo_form_respondido')
				);				
			}

			$joins[] = array(
				'table' => 'acoes_melhorias',
				'alias' => 'Acao',
				'type' => 'LEFT',
				'conditions' => array('Acao.codigo = PosSwtAcaoMelhoria.codigo_acao_melhoria')
			);
			$joins[] = array(
				'table' => 'acoes_melhorias_tipo',
				'alias' => 'AcaoMelhoriaTipo',
				'type' => 'LEFT',
				'conditions' => array('Acao.codigo_acoes_melhorias_tipo = AcaoMelhoriaTipo.codigo')
			);
			$joins[] = array(
				'table' => 'pos_criticidade',
				'alias' => 'PosCriticidade',
				'type' => 'LEFT',
				'conditions' => array('PosCriticidade.codigo = Acao.codigo_pos_criticidade')
			);
			$joins[] = array(
				'table' => 'origem_ferramentas',
				'alias' => 'OrigemFerramenta',
				'type' => 'LEFT',
				'conditions' => array('Acao.codigo_origem_ferramenta = OrigemFerramenta.codigo')
			);
			$joins[] = array(
				'table' => 'usuario',
				'alias' => 'UsuarioResponsavel',
				'type' => 'LEFT',
				'conditions' => array('Acao.codigo_usuario_responsavel = UsuarioResponsavel.codigo')
			);
			$fields[] = 'Acao.codigo as codigo_acao_melhoria';
			$fields[] = '\'\' as item_observado';
			$fields[] = 'AcaoMelhoriaTipo.descricao as desc_acao_melhoria_tipo';
			$fields[] = 'PosCriticidade.descricao as pos_critic_descricao';
			$fields[] = 'OrigemFerramenta.descricao as origem';
			$fields[] = 'UsuarioResponsavel.nome as responsavel';
			$fields[] = 'Acao.prazo as prazo';
			$fields[] = 'Acao.descricao_desvio as desc_desvio';
			$fields[] = 'Acao.descricao_acao as desc_acao';
			$fields[] = 'Acao.descricao_local_acao as desc_local_acao';
		}

		if($pagination){
			$paginate = array(
				'fields' => $fields,
				'joins' => $joins,
				'conditions' => $conditions,
				'limit' => 50,
				'order' => array("Cliente.nome_fantasia",'PosSwtFormRespondido.codigo')				
			);		    
			
			return $paginate;
		} else {
			//para a exportacao   
			return $this->find('sql', array('joins' => $joins, 'fields' => $fields, 'conditions' => $conditions, 'order' => array("Cliente.nome_fantasia",'PosSwtFormRespondido.codigo')));
		}
	}

	public function getObservador(){

		$this->Usuario = & ClassRegistry::init('Usuario');

		if( !empty( $_SESSION['Auth']['Usuario']['codigo_cliente']) ){
			$conditions[] = "Usuario.codigo_cliente IN (select codigo_cliente from grupos_economicos_clientes where codigo_grupo_economico IN (select codigo from grupos_economicos where codigo_cliente ". $this->rawsql_codigo_cliente($_SESSION['Auth']['Usuario']['codigo_cliente']) ." ))";
		}

		$conditions[] = "Usuario.ativo = 1";//usuario tem q estar ativo
		$conditions[] = "Uperfil.codigo = 50";//perfil Pos

		$joins = array(
			array(
				'table' => 'uperfis',
				'alias' => 'Uperfil',
				'type' => 'INNER',
				'conditions' => array('Uperfil.codigo = Usuario.codigo_uperfil')
			),
			array(
				'table' => 'pos_swt_form_respondido',
				'alias' => 'PosSwtFormRespondido',
				'type' => 'LEFT',
				'conditions' => array('PosSwtFormRespondido.codigo_usuario_observador = Usuario.codigo')
			),
		);

		$observador = $this->Usuario->find('list',array('fields' => array('Usuario.codigo','Usuario.nome'), 'conditions' => $conditions, 'joins' => $joins));
		
		return $observador;
	}

	public function getPosSwtPerguntasRespostas($codigo_respondido){
		$query = "
			SELECT 
				respondido.codigo as codigo_respondido,
				tit.codigo as codigo_titulo,
				tit.titulo as titulo,
				qest.codigo as codigo_questao,
				qest.questao as questao,
				resp.codigo as codigo_resposta,
				(CASE 
				WHEN resp.resposta = 1 THEN 'Sim'
				WHEN resp.resposta = 3 THEN 'Não se aplica'
				ELSE 'Não'END ) AS resposta,
				crt.codigo as codigo_criticidade,
				crt.descricao AS criticidade,
				resp.motivo AS motivo
			from pos_swt_form_respondido respondido
				inner join pos_swt_form_resposta resp on respondido.codigo = resp.codigo_form_respondido
				inner join pos_swt_form_questao qest on qest.codigo = resp.codigo_form_questao  
				and qest.codigo_form = respondido.codigo_form
				inner join pos_swt_form_titulo tit on respondido.codigo_form = tit.codigo_form
				and qest.codigo_form_titulo = tit.codigo
				left join pos_criticidade crt on crt.codigo = resp.codigo_criticidade
			where respondido.codigo = '".$codigo_respondido."'";


		$dados = $this->query($query);

		return $dados;
	}

	public function getPosSwtRespostas($codigo_respondido, $codigo_cliente){
		$query = "
			SELECT 
				respondido.codigo as codigo_respondido,
				tit.codigo as codigo_titulo,
				tit.titulo as titulo,
				qest.codigo as codigo_questao,
				qest.questao as questao,
				resp.codigo as codigo_resposta,
				(CASE 
				WHEN resp.resposta = 1 THEN 'Sim'
				WHEN resp.resposta = 3 THEN 'Não se aplica'
				ELSE 'Não'END ) AS resposta
			from pos_swt_form_respondido respondido
				inner join pos_swt_form_resposta resp on respondido.codigo = resp.codigo_form_respondido
				inner join pos_swt_form_questao qest on qest.codigo = resp.codigo_form_questao  
				and qest.codigo_form = respondido.codigo_form
				inner join pos_swt_form_titulo tit on respondido.codigo_form = tit.codigo_form
				and qest.codigo_form_titulo = tit.codigo
				left join pos_criticidade crt on crt.codigo = resp.codigo_criticidade
			where respondido.codigo = '".$codigo_respondido."'
			AND qest.codigo_cliente IN (".$codigo_cliente.")
			";

		$dados = $this->query($query);

		$resposta = array();
		foreach($dados AS $resp) {
			foreach($resp as $r){
				// $resposta[$r['codigo_questao']] = Comum::converterEncodingPara($r['resposta'], 'ISO-8859-1');
				$resposta[$r['questao']] = Comum::converterEncodingPara($r['resposta'], 'ISO-8859-1');
			}
		}

		return $resposta;
	}

	public function analiseSwtRespostas($codigo_respondido, $cod_resp_swt, $codigo_cliente){
		$query = "
			SELECT 
				respondido.codigo as codigo_respondido,
				tit.codigo as codigo_titulo,
				tit.titulo as titulo,
				qest.codigo as codigo_questao,
				qest.questao as questao,
				resp.codigo as codigo_resposta,
				(CASE 
				WHEN resp.resposta = 1 THEN 'Sim'
				WHEN resp.resposta = 3 THEN 'Não se aplica'
				ELSE 'Não'END ) AS resposta
			from pos_swt_form_respondido respondido
				inner join pos_swt_form_resposta resp on respondido.codigo = resp.codigo_form_respondido
				inner join pos_swt_form_questao qest on qest.codigo = resp.codigo_form_questao  
				and qest.codigo_form = respondido.codigo_form
				inner join pos_swt_form_titulo tit on respondido.codigo_form = tit.codigo_form
				and qest.codigo_form_titulo = tit.codigo
				left join pos_criticidade crt on crt.codigo = resp.codigo_criticidade
			where respondido.codigo in ('".$codigo_respondido."','".$cod_resp_swt."')
			AND qest.codigo_cliente = '".$codigo_cliente."'
			";


		$dados = $this->query($query);

		$resposta = array();
		foreach($dados AS $resp) {
			foreach($resp as $r){
				$resposta[$r['codigo_questao']] = Comum::converterEncodingPara($r['resposta'], 'ISO-8859-1');
			}
		}

		return $resposta;
	}
}
