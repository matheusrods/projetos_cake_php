<style type="text/css">
	#grupos_exposicao label, input, button, select, textarea, .detalhes_grupos_exposicao  label, input, button, select, textarea{font-size: 11px;}
	#grupos_exposicao_riscos label, input, button, select, textarea, span {font-size: 11px;}
	#grupos_exposicao_riscos thead tr, tfoot tr {background: #EFEFEF;}
	#grupos_exposicao_riscos thead tr td{text-align: center;}
	.input-mini {width:70px;}
	.radio_epi{float: left; margin-right: 0px; width: 25px;}
	.radio_epc{float: left; margin-right: 0px; width: 25px;}
	.radio_acaliacao{float: left; margin-right: 0px; width: 25px;}
	.checkbox input[type="checkbox"]{margin: 5px 3px 0 20px !important}
	#grupos_exposicao_riscos td.fontes_geradoras div.detalhes{border:1px solid #00f;}
	#grupos_exposicao_riscos td.epi div.detalhes{border:1px solid #3ef;}
	#grupos_exposicao_riscos td.epc div.detalhes{border:1px solid #ff0;}
	#grupos_exposicao_riscos td.class_efeitos_criticos div.detalhes{border:1px solid #ff0;}
	.linhas{border:1px solid #ddd;}
	#grupos_exposicao div.tipo_setor_cargo {float:left;}
	#grupos_exposicao div.tipo_setor_cargo label{margin-right: 10px; text-align: left; width: 60px;}
	td.tipo_avaliacao {float: left; width: 140px; border-left: 0px;}
	td.campo_avaliacao {float:left;min-width: 300px;}
	td.campo_avaliacao div.control-group{float:left; margin-right: 5px;}
	.listagem_fonte_geradora tr.linhas td div input, .listagem_epi tr.linhas td div input,  .listagem_epc tr.linhas td div input{font-size:11px;}
	.listagem_fonte_geradora tr.linhas td div div.help-block, .listagem_epi tr.linhas td div div.help-block, .listagem_epc tr.linhas td div div.help-block{font-size:11px;}
	.listagem_efeitos_criticos tr.linhas td div input, .listagem_epi tr.linhas td div input,  .listagem_epc tr.linhas td div input{font-size:11px;}
	.listagem_efeitos_criticos tr.linhas td div div.help-block, .listagem_epi tr.linhas td div div.help-block, .listagem_epc tr.linhas td div div.help-block{font-size:11px;}
	#descricao_cargo_ghe textarea {font-size: 11px;}
	#descricao_cargo_ghe thead tr {background: #EFEFEF;}
	#descricao_cargo_ghe thead tr th {font-size: 11px; text-align:left;}
	#descricao_cargo_ghe tbody tr td{text-align: left;}
	tr.linhas td div.radio{padding-left:0px}
	legend{display: block;width: 100%;padding: 0;margin-bottom: 10px;font-size: 11px;line-height: inherit;color: #333;border: 0;border-bottom: none;}
	.font-size-11{font-size: 11px !important;}
	.control-group.input.clear.checkbox label{position: relative;top: -3px;}
	.clear{clear: both;}
	.blocked{float: left; width: 100%;}
	.font-size-11{font-size: 11px;}
	.mw215{min-width: 215px;}
	.mw212{min-width: 212px;}
	.mw216{min-width: 216px;}
	.w30{width: 30px;}
	.w60{width: 60px;}
	.w470{width: 470px;}
	.wfull{width: 100%}
	.avaliacao_calor{float: left;width: 100%; min-width: 443px;}
	.tabela_scroll{
		font-size: 10px; 
		margin-bottom: 0px;
	}
	.container_tabela_scroll{
		overflow-x: scroll;
		margin-top: 48px;
		margin-bottom: 20px;
	}
#grupos_exposicao_riscos table.listagem_epi tr.linhas td div fieldset{width:50px}
#grupos_exposicao_riscos table.listagem_epi tr.linhas td div fieldset legend{margin:0px;}
#grupos_exposicao_riscos table.listagem_epi tr.linhas td div fieldset label{font-size: 10px; float: left; clear: both; margin-bottom:0px}
#grupos_exposicao_riscos table.listagem_epc tr.linhas td div fieldset{width:50px}
#grupos_exposicao_riscos table.listagem_epc tr.linhas td div fieldset legend{margin:0px;}
#grupos_exposicao_riscos table.listagem_epc tr.linhas td div fieldset label{font-size: 10px; float: left; clear: both; margin-bottom:0px}
</style>

<div class="modal fade" id="modal_pcmso" data-backdrop="static" style="top: 25%; left: 50%; width: 45%;"></div>

<?php
	

	if( isset($setor_) ){
		$setor = $setor_;
	} else {
		$setor = isset( $this->data['ClienteSetor']['codigo_setor']) ? $this->data['ClienteSetor']['codigo_setor'] : '' ;
		$setor_ = '';
	}

	if( isset($cargo_) ) {
		$cargo = $cargo_;
	} else {
		$cargo = isset( $this->data['GrupoExposicao']['codigo_cargo']) ? $this->data['GrupoExposicao']['codigo_cargo'] : '' ;
		$cargo_ = '';
	}

?>

<?php echo $this->BForm->hidden('edit_mode', array('value' => $edit_mode));?>
<?php 
if(empty($edit_mode)):
	//INCLUIR
	$readonly = false;
	$disabled = false;
	$codigo_funcionario = '';
	$options = array();
	$descricao_tipo_setor_cargo = '';
	if(isset($this->data['GrupoExposicao'])):
		if($this->data['GrupoExposicao']['descricao_tipo_setor_cargo'] == 1):
			$descricao_tipo_setor_cargo = 1;
		elseif($this->data['GrupoExposicao']['descricao_tipo_setor_cargo'] == 2):
			$descricao_tipo_setor_cargo = 2;
		endif;

		if(isset($this->data['GrupoExposicao']['codigo_funcionario']) && !empty($this->data['GrupoExposicao']['codigo_funcionario'])):
			$options = $funcionario;
		$codigo_funcionario = $this->data['GrupoExposicao']['codigo_funcionario'];
		elseif((isset($this->data['ClienteSetor']['codigo_setor']) && !empty($this->data['ClienteSetor']['codigo_setor'])) && (isset($this->data['GrupoExposicao']['codigo_cargo']) && !empty($this->data['GrupoExposicao']['codigo_cargo']))):
			$options = $funcionario;
		$codigo_funcionario = '';
		else:
			$options = array();
		$codigo_funcionario = '';
		endif;
	else:
		$descricao_tipo_setor_cargo = '';
		$codigo_funcionario = '';
		$options = array();
	endif;
	
	if(!empty($cargo) && !empty($setor)):
		$descricao_tipo_setor_cargo = 1;
	endif;

else:
	//EDITAR
	$readonly = 'readonly';
	$disabled = true;

	if(isset($this->data['GrupoExposicao']['codigo_grupo_homogeneo']) && !empty($this->data['GrupoExposicao']['codigo_grupo_homogeneo'])):
		$descricao_tipo_setor_cargo = 2;
	else:
		$descricao_tipo_setor_cargo = 1; 
	endif;

	if(isset($this->data['GrupoExposicao']['codigo_funcionario']) && !empty($this->data['GrupoExposicao']['codigo_funcionario'])):
		$options = $funcionario;
		$codigo_funcionario = $this->data['GrupoExposicao']['codigo_funcionario'];
	else:
		$options = array();
		$codigo_funcionario = '';
	endif;

endif;?>

		<div id="grupos_exposicao">
			<div class="span6" style="margin-left:0px;width:627px;">
				<div class='well detalhes_grupos_exposicao'>
					<div class='row-fluid inline'>
						<?php echo $this->BForm->hidden('GrupoExposicao.codigo', array('value' =>  !empty($this->data['GrupoExposicao']['codigo'])? $this->data['GrupoExposicao']['codigo'] : ''));?>	
						<?php echo $this->BForm->input('Matriz.razao_social', array('label' => 'Empresa', 'class' => 'input-xxlarge', 'style' => 'width: 500px', 'readonly' => 'readonly'));?>
					</div>
					<div class='row-fluid inline'>
						<?php echo $this->BForm->input('Unidade.razao_social', array('label' => 'Unidade', 'class' => 'input-xxlarge', 'style' => 'width: 500px', 'readonly' => 'readonly'));?>
						<?php echo $this->BForm->hidden('ClienteSetor.codigo', array('value' => empty($this->data['ClienteSetor']['codigo'])? '': $this->data['ClienteSetor']['codigo']));?>
						<?php echo $this->BForm->hidden('ClienteSetor.codigo_cliente_alocacao', array('value' => $this->data['Unidade']['codigo']));?>
					</div>
					<div class='row-fluid inline'>
						<label for="GrupoExposicaoDescricaoTipoSetorCargo">Tipo</label>
						<?php if($edit_mode == 1):?>
							<?php echo $this->BForm->input('GrupoExposicao.descricao_tipo_setor_cargo', array('legend' => false, 'options' => array('1' => 'Individual', '2' => 'GHE'), 'value' => $descricao_tipo_setor_cargo, 'type' => 'radio','before' => '<div class="tipo_setor_cargo">','after' => '</div>', 'hiddenField' => false, 'onclick'=> 'selecionaTipoSetorCargo(this);', 'disabled' => $disabled, 'div' => array('style' => ' width: 450px') ));?>
							<?php echo $this->BForm->input('GrupoExposicao.descricao_tipo_setor_cargo', array('type' => 'hidden', 'value' => $descricao_tipo_setor_cargo));?>
						<?php else:?>
							<?php if($disabled == false):?>
								<?php echo $this->BForm->input('GrupoExposicao.descricao_tipo_setor_cargo', array('legend' => false, 'options' => array('1' => 'Individual', '2' => 'GHE'), 'value' => $descricao_tipo_setor_cargo, 'type' => 'radio','before' => '<div class="tipo_setor_cargo">','after' => '</div>', 'hiddenField' => false, 'onclick'=> 'selecionaTipoSetorCargo(this);', 'disabled' => $disabled, 'div' => array('style' => ' width: 450px' )));?>

								<?php 
								if(!empty($descricao_tipo_setor_cargo)) {
									echo $this->BForm->input('GrupoExposicao.descricao_tipo_setor_cargo', array('type' => 'hidden', 'value' => $descricao_tipo_setor_cargo));
								}
								?>
							<?php else:?>
								<?php echo $this->BForm->input('GrupoExposicao.descricao_tipo_setor_cargo', array('type' => 'hidden', 'value' => $descricao_tipo_setor_cargo));?>
							<?php endif;?>
						<?php endif;?>
					</div>
					<div class="setor_cargo">
						<div class='row-fluid inline'>
							<label for="ClienteSetorCodigoSetor">Setor</label>
							<?php echo 	$this->BForm->input('ClienteSetor.codigo_setor', 
											array(	'options' => $lista_setor, 
													'empty' => 'Selecione', 
													'label' => false, 
													'class' => 'input-large bselect2 js-setor', 
													'style' => 'width: 500px', 
													'disabled' => $disabled, 
													'onchange' => 'carregaCaracteristica($(this).val()); carregaFuncionario();', 
													'value' => ( $setor ? $setor : '')
												)
										);?>
						</div>

						<div class='row-fluid inline'>
							<label for="GrupoExposicaoCodigoCargo">Cargo</label>
							<?php echo $this->BForm->input('GrupoExposicao.codigo_cargo', 
												array(	'options' => $lista_cargo, 
														'empty' => 'Selecione', 
														'label' => false, 
														'class' => 'input-large bselect2 js-cargo cod_cargo', 
														'onchange'=>'carregaCargo(); carregaFuncionario();', 
														'style' => 'width: 500px', 
														'disabled' => $disabled, 
														'value' => ( $cargo ? $cargo : '')
													)

												);?>
							<a href="#" class="input-small icon-eye-open" title="Visualizar Cargo" onclick="mostra_detalhes_cargo();"></a>
							<div id="detalhes_cargo"></div>
						</div>
						<div class='row-fluid inline'>
							<label for="GrupoExposicaoCodigoFuncionario">Funcionário</label>
							<div id="campo_funcionario">
								<?php echo $this->BForm->input('GrupoExposicao.codigo_funcionario', array('label' => false, 'class' => 'input-xlarge bselect2', 'style' => 'width: 505px', 'disabled' => $disabled, 'value' => $codigo_funcionario, 'options' => $options, 'empty' => ''));?>
								<?php echo $this->BForm->hidden('GrupoExposicao.codigo_funcionario_hidden', array('value' => $codigo_funcionario));?>
							</div>
							<div id="carregando_funcionario" style="display: none;">
								<img src="/portal/img/ajax-loader.gif" border="0" />
							</div>
						</div>
						<?php if($edit_mode == 1):?>
							<?php echo $this->BForm->hidden('ClienteSetor.codigo_setor', array('value' => $this->data['ClienteSetor']['codigo_setor']));?>
							<?php echo $this->BForm->hidden('GrupoExposicao.codigo_cargo', array('value' => $this->data['GrupoExposicao']['codigo_cargo']));?>
						<?php endif;?>
					</div>
					<div class="setor_cargo_ghe">
						<div class='row-fluid inline'>
							<label for="GrupoExposicaoCodigoGrupoHomogeneo">GHE</label>
							<?php echo $this->BForm->input('GrupoExposicao.codigo_grupo_homogeneo', array('options' => $ghe, 'empty' => 'Selecione', 'label' => false, 'class' => 'input-xxlarge bselect2', 'style' => 'width: 510px', 'readonly' => $readonly, 'onchange'=>'carregaSetorGrupoHomogeneo(this); carregaDescricaoAtivdades();'));?>
						</div>
						<div class='row-fluid inline'>
							<label for="GrupoExposicaoCodigoCargo">Funcionário Entrevistado:</label>
							<?php echo $this->BForm->input('GrupoExposicao.funcionario_entrevistado', array('options' => $funcionarios, 'empty' => 'Selecione', 'label' => false, 'class' => 'input-xlarge bselect2', 'style' => 'width: 505px', 'value' => $dados_grupo_exposicao['GrupoExposicao']['funcionario_entrevistado'], 'onchange' => 'verificaOutros()'));?>
						</div>		
						<div class="row-fluid inline div_outros">
						<?php echo $this->BForm->input('Outros', array('label' => 'Outro:','class' => 'input-xlarge', 'value' => $dados_grupo_exposicao['GrupoExposicao']['funcionario_entrevistado_terceiro']))?>
						</div>			
					</div>
					<div class='row-fluid inline'>
						<div class="span6">

							<?php echo $this->BForm->input('GrupoExposicao.data_documento', array('label' => 'Data da Vistoria', 'class' => 'input-medium  data', 'type' => 'text'));?>
						</div>
						<div class="span6">
							<?php echo $this->BForm->input('GrupoExposicao.data_inicio_vigencia', array('label' => 'Data de início de Vigência', 'class' => 'input-medium  data', 'type' => 'text'));?>
						</div>
					</div>
					<div class="row-fluid inline">
						<?php echo $this->BForm->input('GrupoExposicao.codigo_medico', array('label' => 'Profissional responsável', 'class' => 'input-xxlarge bselect2', 'style' => 'width: 505px', 'options' => $profissionais, 'empty' => 'Selecione', 'value' => $dados_grupo_exposicao['GrupoExposicao']['codigo_medico'])) ?>

					</div>

					
					<?php if(!empty($atribuicoes)): ?>
						<?php foreach($atribuicoes as $id => $atribuicao): 
							
							$checked = ($edit_mode == 1 && isset($atribuicoes_grupo[$id])) ? true : false;

 					 		echo $this->BForm->label($id, $this->BForm->checkbox('atribuicoes.'.$id, array('value' => $id, 'hiddenField' => false, 'checked' => $checked)).$atribuicao, array('class' => 'checkbox inline input-xlarge', 'escape'=>'pull-left margin-left-20')); 
						endforeach; ?>

					<?php endif; ?>
					<div class="setor_cargo">
					</div>
				</div>
			</div>	
			<div class="span6" style="margin-left: 10px;width:530px;">
				<div class='well detalhes_grupos_exposicao'>
					<div class="row-fluid inline">
						<div class="control-group input" style="width: 485px; text-align: center;">
							<h5>Características do Setor</h5>
						</div>
					</div>
					<div id="caracteristicas">
						<div class='row-fluid inline'>
							<?php echo $this->BForm->input('ClienteSetor.pe_direito', array('label' => 'Pé direito', 'class' => 'input-medium', 'options' => $pe_direito, 'empty' => 'Selecione', 'default' => ''));?>
							<?php echo $this->BForm->input('ClienteSetor.cobertura', array('label' => 'Cobertura', 'class' => 'input-medium', 'options' => $cobertura, 'empty' => 'Selecione', 'default' => ''));?>
						</div>
						<div class='row-fluid inline'>
							<?php echo $this->BForm->input('ClienteSetor.iluminacao', array('label' => 'Iluminação', 'class' => 'input-medium', 'options' => $iluminacao, 'empty' => 'Selecione', 'default' => ''));?>
							<?php echo $this->BForm->input('ClienteSetor.estrutura', array('label' => 'Estrutura', 'class' => 'input-medium', 'options' => $estrutura, 'empty' => 'Selecione', 'default' => ''));?>
						</div>
						<div class='row-fluid inline'>
							<?php echo $this->BForm->input('ClienteSetor.ventilacao', array('label' => 'Ventilação', 'class' => 'input-medium', 'options' => $ventilacao, 'empty' => 'Selecione', 'default' => ''));?>
							<?php echo $this->BForm->input('ClienteSetor.piso', array('label' => 'Piso', 'class' => 'input-medium', 'options' => $piso, 'empty' => 'Selecione', 'default' => ''));?>
						</div>	
					</div>	
					<div id="carregando_caracteristicas" style="display: none; text-align: center; vertical-align: middle;">
						<img src="/portal/img/ajax-loader.gif" border="0" />
					</div>
				</div>
			</div>
			<div style="float:left; width:100%;">
				<div class=" detalhes_grupos_exposicao descricao_cargo">
					<div class="setor_cargo well">
						<div id="dados_cargo">
							<div class='row-fluid inline'>
								<div class="control-group input text" style="margin-bottom: 0;">
									<label for="GrupoExposicaoDescricaoAtividade">Descrição das Atividades</label>
									<?php echo $this->BForm->input('descricao_atividade', array('type' => 'textarea', 'label' => false, 'div' => false, 'style' => 'width: 1000px; height: 60px;'));?>
								</div>
							</div>
						</div>

					</div>
					<div class="setor_cargo_ghe">
						<div id="nao_encontrado" style="display:none;">
							<h5>Descrição das Atividades</h5>
							<div class="alert">Nenhum dados foi encontrado.</div>
						</div>
						<div id="exibe_descricao_ghe" style="display:none;">
							<?php echo $this->Html->link('Descrição das Atividades', 'javascript:void(0)', array('escape' => false, 'class' => 'btn btn-warning', 'title' =>'Descrição das Atividades', 'onclick' => "exibe_descricao_cargos()"));?>
						</div>
						<div id="dados_cargo_ghe" style="display:none;">

							<table  class="table table-bordered well" id="descricao_cargo_ghe">
								<thead>
									<tr>
										<th style="text-align:center">SETOR</th>
										<th style="text-align:center">CARGO</th>
										<th style="text-align:center">DESCRIÇÃO ATIVIDADES</th>
									</tr>
								</thead>
								<tbody>
									<?php if(isset($this->data['GrupoHomDetalhe']) && !empty($this->data['GrupoHomDetalhe'])):?>
										<?php foreach ($this->data['GrupoHomDetalhe'] as $linha => $dados):?>
											<?php echo $this->BForm->hidden('GrupoHomDetalhe.'.$linha.'.codigo', array('value' => $dados['codigo']));?>
											<tr>
												<td><?php echo $dados['Setor']['descricao'];?>
													<?php echo $this->BForm->hidden('GrupoHomDetalhe.'.$linha.'.codigo_setor_ghe', array('value' => $dados['Setor']['codigo']));?>
												</td>
												<td><?php echo $dados['Cargo']['descricao'];?>
													<?php echo $this->BForm->hidden('GrupoHomDetalhe.'.$linha.'.codigo_cargo_ghe', array('value' => $dados['Cargo']['codigo']));?>
												</td>
												<td>
													<?php echo $this->BForm->input('GrupoHomDetalhe.'.$linha.'.descricao_atividade_ghe', array('type' => 'textarea', 'label' => false, 'div' => false, 'style' => 'height: 40px; width: 98%; margin-bottom: 0px;', 'value' => $this->data['GrupoHomDetalhe'][$linha]['descricao_atividade_ghe']));?>
												</td>
											</tr>
										<?php endforeach?>
									<?php else:?>
										<?php if(isset($grupo_homogeneo) && !empty($grupo_homogeneo)):?>
											<?php foreach ($grupo_homogeneo as $linha => $dados):?>
												<?php echo $this->BForm->hidden('GrupoHomDetalhe.'.$linha.'.codigo', array('value' => $dados['GrupoHomDetalhe']['codigo']));?>
												<tr>
													<td><?php echo $dados['Setor']['descricao'];?>
														<?php echo $this->BForm->hidden('GrupoHomDetalhe.'.$linha.'.codigo_setor_ghe', array('value' => $dados['Setor']['codigo']));?>
													</td>
													<td><?php echo $dados['Cargo']['descricao'];?>
														<?php echo $this->BForm->hidden('GrupoHomDetalhe.'.$linha.'.codigo_cargo_ghe', array('value' => $dados['Cargo']['codigo']));?>
													</td>
													<td>
														<?php echo $this->BForm->input('GrupoHomDetalhe.'.$linha.'.descricao_atividade_ghe', array('type' => 'textarea', 'label' => false, 'div' => false, 'style' => 'height: 40px; width: 98%; margin-bottom: 0px;', 'value' => $dados['GrupoHomDetalhe']['descricao_atividade_ghe']));?>
													</td>
												</tr>
											<?php endforeach?>
										<?php endif;?>
									<?php endif;?>
								</tbody>
							</table>
						</div>
					</div>
					<div id="carregando_cargo" style="display: none; text-align: center; vertical-align: middle;">
						<img src="/portal/img/ajax-loader.gif" border="0" />
					</div>
				</div>
			</div>
			<div style="clear:both"></div>
		</div>
		<?php //TERMINO GRUPO_EXPOSICAO?>

		<?php //INICIO GRUPO_EXPOSICAO_RISCO?>

		<?php //ADICIONAR NOVO RISCO?>
		<div class='actionbar-right' style="float:right; margin-top: 5px; margin-bottom:10px;">
			<a href="javascript:void(0)" onclick="modal_visualizar_pcmso('<?php echo $this->data['Unidade']['codigo']; ?>','<?php echo $setor; ?>','<?php echo $cargo ?>','<?php echo $codigo_funcionario?>', 1);">Visualizar PCMSO</a>
			<?php echo $this->Html->link('<i class="icon-plus icon-white"></i>', 'javascript:void(0)', array('escape' => false, 'class' => 'btn btn-success', 'title' =>'Cadastrar Riscos - Grupos Exposição', 'onclick' => "exibe_campos_riscos()"));?>
		</div>

		<div class="container_tabela_scroll">		
		<table class="table table-bordered tabela_scroll" id="grupos_exposicao_riscos">
			<thead>
				<tr>
					<td rowspan="3" style="vertical-align: middle;"><strong>AGENTES / SUBSTÂNCIAS</strong></td>
					<td rowspan="3" style="vertical-align: middle;"><strong>FONTES GERADORAS DA EXPOSIÇÃO</strong></td>
					<td rowspan="3" style="vertical-align: middle;"><strong>EFEITO CRÍTICO</strong></td>
					<td rowspan="3" style="vertical-align: middle;"><strong>MEIO DE PROPAGAÇÃO</strong></td>
					<td rowspan="3" style="vertical-align: middle; "><strong>AVALIAÇÃO AMBIENTAL</strong></td>
					<td colspan="5"><strong>EXPOSIÇÃO OCUPACIONAL</strong></td>
					<td colspan="2"><strong>TECNOLOGIA E PROTEÇÃO</strong></td>
					<td rowspan="3" style="vertical-align: middle; "><strong>MEDIDAS DE CONTROLE EXISTENTES</strong></td>
					<td rowspan="3" style="vertical-align: middle; "><strong>MEDIDAS DE CONTROLE RECOMENDADA</strong></td>
					<td rowspan="3" style="vertical-align: middle; ">AÇÕES</td>
				</tr>
				<tr>
					<td colspan="3">EXPOSIÇÃO</td>
					<td rowspan="2"><span style="transform: rotate(270g);">POTENCIAL DE DANO</span></td>
					<td rowspan="2">GRAU DE RISCO</td>
					<td>INDIVIDUAL</td>
					<td>COLETIVA</td>
				</tr>	
				<tr>
					<td style="text-align:center;">FREQUÊNCIA DE EXPOSIÇÃO</td>
					<td style="text-align:center;">INTENSIDADE</td>
					<td style="text-align:center;">EXPOSIÇÃO RESULTANTE</td>
					<td style="text-align:center;">E/R | DESCRIÇÃO</td>
					<td style="text-align:center;">E/R | DESCRIÇÃO</td>				
				</tr>	
			</thead>
			<tbody>
				<?php if(isset($this->data['GrupoExposicaoRisco'])): ?>

					<?php if(isset($this->data['GrupoExposicaoRisco']['k'])):?>
						<?php unset($this->data['GrupoExposicaoRisco']['k']);?>
					<?php endif;?>

					<?php foreach ($this->data['GrupoExposicaoRisco'] as $key => $dados) :?>

						<tr class="campos_riscos">
							<td>
								<?php echo $this->BForm->hidden('GrupoExposicaoRisco.'.$key.'.codigo', array('class' => 'risco'));?>		

								<?php //GRUPO DE RISCO?>
								<?php //GRUPO DE RISCO ENVIADO. TELA EDITAR NORMAL; NAO ENVIADO: TELA COM ERRO?>
								<?php if(isset($this->data['GrupoExposicaoRisco'][$key]['GrupoRisco']['codigo']) && !empty($this->data['GrupoExposicaoRisco'][$key]['GrupoRisco']['codigo'])):?>
									<?php if($edit_mode==1):?>
										<?php $valor_grupo_risco = $this->data['GrupoExposicaoRisco'][$key]['GrupoRisco']['codigo'];?>
									<?php else:?>
										<?php $valor_grupo_risco = $this->data['GrupoExposicaoRisco'][$key]['codigo_grupo_risco']; ?>
									<?php endif;?>
								<?php else:?>
									<?php $valor_grupo_risco = array();?>
								<?php endif;?>

								<?php echo $this->BForm->input('GrupoExposicaoRisco.'.$key.'.codigo_grupo_risco', array('options' => ($edit_mode ? $grupo_risco[$key] : $grupo_risco) , 'value' => $valor_grupo_risco, 'label' => 'Agente', 'class' => 'input-small risco', 'onchange' => 'carregaRiscoPorGrupo(this)'));?>

								<?php //RISCO?>
								<?php //RISCO ENVIADO. TELA EDITAR NORMAL; NAO ENVIADO: TELA COM ERRO?>
								<?php if(isset($this->data['GrupoExposicaoRisco'][$key]['Risco']) && !empty($this->data['GrupoExposicaoRisco'][$key]['Risco'])):?>
									<?php $valor_risco = $this->data['GrupoExposicaoRisco'][$key]['Risco']['codigo'];?>							
								<?php else:?>
									<?php $valor_risco = array();?>
								<?php endif;?>
								<?php echo $this->BForm->input('GrupoExposicaoRisco.'.$key.'.codigo_risco', array('options' => ($edit_mode ? $option_subs_carregado[$valor_grupo_risco] : $option_subs_carregado) ,'value' => $valor_risco,'label' => 'Substâncias', 'class' => 'input-xlarge substancias risco codigo_risco','onchange'=>'carregaDadosRisco(this);'));?>

								<div id="carregando_<?=$key;?>" class="carregando" style="display: none;">
									<img src="/portal/img/ajax-loader.gif" border="0" />
								</div>			    
							</td>

							<?php //FONTES_GERADORAS?>
							<td class="fontes_geradoras" style="vertical-align: top; padding: 0px;">
								<div class="detalhes_<?=$key;?>" style="width: 100%;">
									<div style="float: right;">
										<div class='actionbar-right'>
											<?php echo $this->Html->link('<i class="icon-plus icon-white" style="margin-right: 0;"></i>', array('controller' => 'fontes_geradoras', 'action' => 'buscar_fonte_geradora',$key,$this->data['GrupoExposicaoRisco'][$key]['codigo_risco']), array('escape' => false, 'class' => 'btn btn-success dialog_fontes_geradoras risco', 'title' =>'Selecionar Fontes Geradoras', 'style'=> 'padding: 0 5px;'));?>
										</div>
									</div>
									<div style="float: left; width: 100%;">
										<table id="listagem_fonte_geradora_<?=$key;?>" class="listagem_fonte_geradora" style="width:200px;">
											<?php if(isset($this->data['GrupoExposicaoRisco'][$key]['GrupoExpRiscoFonteGera'])):?>
												<?php foreach ($this->data['GrupoExposicaoRisco'][$key]['GrupoExpRiscoFonteGera'] as $key_fonte_geradora => $dados_fonte_geradora):?>
													<?php $codigo_fonte_geradora = $dados_fonte_geradora['codigo_fontes_geradoras'];?>
													<?php $nome_fonte_geradora = $dados_fonte_geradora['nome'];?>
													<tr class="linhas">
														<td style="width:180px;">
															<?php echo $this->BForm->hidden('GrupoExposicaoRisco.'.$key.'.GrupoExpRiscoFonteGera.'.$key_fonte_geradora.'.codigo_fontes_geradoras', array('class' => 'codigo_fonte_geradora risco', 'value' => $codigo_fonte_geradora));?>
															<?php echo $this->BForm->input('GrupoExposicaoRisco.'.$key.'.GrupoExpRiscoFonteGera.'.$key_fonte_geradora.'.nome', array('class' => 'risco input-medium', 'type' => 'text', 'value' => $nome_fonte_geradora, 'label'=> false, 'readonly'=> true, 'style' => 'width:130px'));?>		
														</td>
														<td style="width:20px;">
															<?php echo $this->Html->link('', 'javascript:void(0)', array('class' => 'icon-trash', 'title' => 'Excluir', 'onclick' => 'excluirFonteGeradora(this)'))?>
														</td>
													</tr>
												<?php endforeach;?>
											<?php endif;?>
										</table>
									</div>
								</div>
							</td>
							<?php //FIM FONTES_GERADORAS?>

							<?php //EFEITOS CRITICOS?>
							<td class="class_efeitos_criticos" style="vertical-align: top; padding: 0px;">
								<div class="detalhes_<?=$key;?>" style="width: 100%;">
									<div style="float: right;">
										<div class='actionbar-right'>
											<?php echo $this->Html->link('<i class="icon-plus icon-white" style="margin-right: 0;"></i>', array('controller' => 'riscos_atributos_detalhes', 'action' => 'buscar_efeitos_criticos',$key,$this->data['GrupoExposicaoRisco'][$key]['codigo_risco']), array('escape' => false, 'class' => 'btn btn-success dialog_efeitos_criticos risco', 'title' =>'Selecionar Efeitos Críticos', 'style'=> 'padding: 0 5px;'));?>
										</div>
									</div>
									<div style="float: left; width: 100%;">
										<table id="listagem_efeitos_criticos_<?=$key;?>" class="listagem_efeitos_criticos" style="width:200px;">
											<?php if(isset($this->data['GrupoExposicaoRisco'][$key]['EfeitoCritico'])):?>
												<?php foreach ($this->data['GrupoExposicaoRisco'][$key]['EfeitoCritico'] as $key_efeito_critico => $dados_efeito_critico):?>
													<?php $codigo_efeito_critico = $dados_efeito_critico['codigo'];?>
													<?php $descricao_efeito_critico = $dados_efeito_critico['descricao'];?>
													<tr class="linhas">
														<td style="width:180px;">
															<?php echo $this->BForm->hidden('GrupoExposicaoRisco.'.$key.'.GrupoExpEfeitoCritico.'.$key_efeito_critico.'.codigo_efeito_critico', array('class' => 'codigo_efeito_critico risco', 'value' => $codigo_efeito_critico));?>
															<?php echo $this->BForm->input('GrupoExposicaoRisco.'.$key.'.GrupoExpEfeitoCritico.'.$key_efeito_critico.'.descricao', array('class' => 'risco input-medium', 'type' => 'text', 'value' => $descricao_efeito_critico, 'label'=> false, 'readonly'=> true, 'style' => 'width:130px'));?>		
														</td>
														<td style="width:20px;">
															<?php echo $this->Html->link('', 'javascript:void(0)', array('class' => 'icon-trash', 'title' => 'Excluir', 'onclick' => 'excluirEfeitoCritico(this)'))?>
														</td>
													</tr>
												<?php endforeach;?>
											<?php endif;?>
										</table>
									</div>
								</div>
							</td>
							<!-- <td>
								<div class="detalhes_<?=$key;?>" style="width: 100%;">
									<?php //echo $this->BForm->input('GrupoExposicaoRisco.'.$key.'.codigo_efeito_critico', array('label' => '&nbsp;', 'class' => 'input-small risco', 'options' => $array_efeito, 'empty' => 'Selecione'));?>
								</div>
							</td> -->
							<?php //FIM EFEITOS CRITICOS?>
							<td>
								<div class="detalhes_<?=$key;?>" style="width: 100%;">
									<?php echo $this->BForm->input('GrupoExposicaoRisco.'.$key.'.codigo_risco_atributo', array('label' => '&nbsp;', 'class' => 'input-small risco', 'options' => $meios_exposicao, 'empty' => 'Selecione'));?>
								</div>
							</td>
							<?php //MEDIÇÃO?>
							<td>
								<div class="detalhes_<?=$key;?>" class="wfull">
									<table id="avaliacao_<?=$key;?>">
										<tr style="display: flex;">
											<td class="tipo_<?=$key;?> tipo_avaliacao"  style="padding-top: 0px">
												<?php if(isset($dados['codigo_tipo_medicao']) && !empty($this->data['GrupoExposicaoRisco'][$key]['codigo_tipo_medicao'])):?>
													<?php $codigo_tipo_medicao = $this->data['GrupoExposicaoRisco'][$key]['codigo_tipo_medicao'];?>
													<?php if($this->data['GrupoExposicaoRisco'][$key]['codigo_tipo_medicao'] == 2):?>
														<?php $display = 'none';?>
													<?php else:?>
														<?php $display = 'block';?>
													<?php endif;?>
												<?php else:?>
													<?php $codigo_tipo_medicao = "";?>
													<?php $display = 'none';?>
												<?php endif;?>

												<?php echo $this->BForm->input('GrupoExposicaoRisco.'.$key.'.codigo_tipo_medicao', array('class' => 'input-mini risco', 'legend' => false,'style' => 'margin-left: 0px; margin-top: 2px;    margin-right: 5px;', 'div' => false,'hiddenField' => false,	'type' => 'radio',  'options' => array(1 => 'Quantitativo',2 => 'Qualitativo'), 'onclick'=> 'selecionaTipoAvaliacao(this);', 'label' => array('class' => 'radio_avaliacao inline','style' => 'font-size:11px;'),'value' => $codigo_tipo_medicao));?>

												<?php if(isset($dados['Risco']['risco_caracterizado_por_ruido']) && $dados['Risco']['risco_caracterizado_por_ruido']) { ?>
													<div class="margin-top-13">
														<?php echo $this->BForm->input('GrupoExposicaoRisco.'.$key.'.dosimetria', array('type' => 'checkbox', 'hiddenField' => false)) ?>
														<?php echo $this->BForm->input('GrupoExposicaoRisco.'.$key.'.avaliacao_instantanea', array('type' => 'checkbox', 'label' => 'Avaliação instantânea', 'hiddenField' => false)) ?>
													</div>
													<?php } ?>
												</td>

												<td class="campos_aval_<?=$key;?> campo_avaliacao"  style="padding-top: 0px">
													<?php if($dados['Risco']['risco_caracterizado_por_calor']) { ?>
														<div id="avaliacao_calor_<?=$key;?>" class="avaliacao_calor">
															<div class="pull-left mw212 margin-bottom-5">
																<div class="pull-left font-size-11 margin-right-10 margin-top-30 w60">
																	<strong>Descanso: </strong>
																</div>	
																<div>
																	<?php echo $this->BForm->input('GrupoExposicaoRisco.'.$key.'.descanso_tbn', array('div' => 'pull-left margin-right-10', 'label' => 'TBN', 'class' => 'w30 just-number numeric')) ?>

																	<?php echo $this->BForm->input('GrupoExposicaoRisco.'.$key.'.descanso_tbs', array('div' => 'pull-left margin-right-10', 'label' => 'TBS', 'class' => 'w30 just-number numeric')) ?>

																	<?php echo $this->BForm->input('GrupoExposicaoRisco.'.$key.'.descanso_tg', array('div' => 'pull-left', 'label' => 'TG', 'class' => 'w30 just-number numeric')) ?>
																</div>
																<div class="clear"></div>
																<div>
																	<?php echo $this->BForm->input('GrupoExposicaoRisco.'.$key.'.descanso_no_local', array('class' => 'input-mini risco', 'legend' => false, 'div' => false,'hiddenField' => false,	'type' => 'radio',  'options' => array(1 => 'No local', 0 => 'Fora do local'), 'label' => array('class' => 'inline pull-left margin-left-0 margin-top-5 margin-right-10'), 'value' => $dados['descanso_no_local'])); ?>
																</div> 
															</div>
															<div class="pull-left mw216 margin-left-10">
																<div class="pull-left font-size-11 margin-right-10 margin-top-30 w60">
																	<strong>Trabalho: </strong>
																</div>	
																<div>
																	<?php echo $this->BForm->input('GrupoExposicaoRisco.'.$key.'.trabalho_tbn', array('div' => 'pull-left margin-right-10', 'label' => 'TBN', 'class' => 'w30 just-number numeric')) ?>

																	<?php echo $this->BForm->input('GrupoExposicaoRisco.'.$key.'.trabalho_tbs', array('div' => 'pull-left margin-right-10', 'label' => 'TBS', 'class' => 'w30 just-number numeric')) ?>

																	<?php echo $this->BForm->input('GrupoExposicaoRisco.'.$key.'.trabalho_tg', array('div' => 'pull-left margin-right-10', 'label' => 'TG', 'class' => 'w30 just-number numeric')) ?>
																</div>
																<div class="clear margin-top-15"></div>
																<div class="pull-left font-size-11 margin-right-10 padding-top-5">
																	<strong>Carga Solar: </strong>
																</div>
																<div class="pull-left">
																	<?php echo $this->BForm->input('GrupoExposicaoRisco.'.$key.'.carga_solar', array('class' => 'input-mini risco', 'legend' => false, 'div' => false,'hiddenField' => false,	'type' => 'radio',  'options' => array(1 => 'Com', 0 => 'Sem'), 'label' => array('class' => 'inline pull-left margin-left-0 margin-top-5 margin-right-10'), 'value' => $dados['carga_solar']));?>
																</div>
															</div>
														</div>
														<?php } else { ?>
															<?php echo $this->BForm->input('GrupoExposicaoRisco.'.$key.'.codigo_tecnica_medicao', array('label' => 'Unidade de Medida', 'class' => 'input-mini risco','options' => $unidades_medida, 'empty' => 'Selecione'));?>
															<?php echo $this->BForm->input('GrupoExposicaoRisco.'.$key.'.valor_maximo', array('label' => 'Limite de Tolerância', 'class' => 'input-mini risco'));?>
															<?php echo $this->BForm->input('GrupoExposicaoRisco.'.$key.'.valor_medido', array('label' => 'Valor Medido', 'class' => 'input-mini risco moeda3 just-number numeric'));?>
															<?php echo $this->BForm->input('GrupoExposicaoRisco.'.$key.'.codigo_tec_med_ppra', array('label' => 'Tecnicas de Medição', 'class' => 'input-mini risco','options' => $tecnicas_medicao, 'escape' => false));?>
															<?php } ?>
														</td>
													</tr>
												</table>
											</div>								
											<div class="clear"></div>

										</td>
										<?php //FIM MEDIÇÃO?>
							<td>
								<div class="detalhes_<?=$key;?>" style="width: 250px;">
									<div class="control-group input select pull-left" >
										<?php echo $this->BForm->input('GrupoExposicaoRisco.'.$key.'.tempo_exposicao', array('label' => 'Tipo', 'div' =>false, 'class' => 'input-mini risco','options' => $tempo_exposicao, 'empty' => 'Selecione', 'onchange' => 'carregaResultante(this)', 'style' => ' margin-right:10px;'));?>
									</div>
									<div class="control-group input text pull-left" >
										<?php echo $this->BForm->input('GrupoExposicaoRisco.'.$key.'.minutos_tempo_exposicao', array('label' => 'Tempo de Exposição(MIN)', 'div' =>false, 'class' => 'input-mini risco just-number numeric', '	style' => 'width:40px; margin-right:10px;'));?>
									</div>
									<div class="control-group input text pull-left" >
										<?php echo $this->BForm->input('GrupoExposicaoRisco.'.$key.'.jornada_tempo_exposicao', array('label' => 'Jornada de Trabalho', 'div' =>false, 'class' => 'input-mini risco just-number numeric', '	style' => 'width:40px;margin-right:10px;'));?>
									</div>
									<?php if(isset($dados['Risco']['risco_caracterizado_por_calor']) && $dados['Risco']['risco_caracterizado_por_calor']) { ?>
									<div class="control-group input text pull-left" >
										<?php echo $this->BForm->input('GrupoExposicaoRisco.'.$key.'.descanso_tempo_exposicao', array('label' => 'Descanso', 'div' =>false, 'class' => 'input-mini risco just-number numeric', 'style' => 'width:40px;'));?>
									</div>
								<?php } ?>
								</div>			
							</td>
							<td>
								<div class="detalhes_<?=$key;?>" style="width: 100%;">
									<?php echo $this->BForm->input('GrupoExposicaoRisco.'.$key.'.intensidade', array('label' => '&nbsp;', 'class' => 'input-mini risco','options' => $intensidade, 'empty' => 'Selecione', 'onchange' => 'carregaResultante(this)'));?>
								</div>
							</td>
							<?php //RESULTANTES?>
							<td>
								<div class="detalhes_<?=$key;?>" style="width: 100%;">
									<div class="control-group input select">
										<div id="exibe_resultante_<?=$key;?>">
											<?php if(isset($this->data['GrupoExposicaoRisco'][$key]['resultante']) && !empty($this->data['GrupoExposicaoRisco'][$key]['resultante'])):?>
												<?php if(isset($this->data['GrupoExposicaoRisco'][$key]['resultante']['Resultante']) && !empty($this->data['GrupoExposicaoRisco'][$key]['resultante']['Resultante'])):?>
													<?php $resultante_dados = array($this->data['GrupoExposicaoRisco'][$key]['resultante']['Resultante']['codigo'] => $this->data['GrupoExposicaoRisco'][$key]['resultante']['Resultante']['descricao']);?>
												<?php else:?>
													<?php $resultante_dados = array($this->data['GrupoExposicaoRisco'][$key]['Resultante']['codigo'] => $this->data['GrupoExposicaoRisco'][$key]['Resultante']['descricao']);?>
												<?php endif;?>
											<?php else:?>
												<?php $resultante_dados = array();?>
											<?php endif;?>
											<?php echo $this->BForm->input('GrupoExposicaoRisco.'.$key.'.resultante', array('label' => '&nbsp;', 'class' => 'input-mini risco','options' => $resultante_dados, 'readonly' => 'readonly','onchange' => 'carregaGrauRisco(this)'));?>
										</div>
										<div id="carregando_resultante_<?=$key;?>" style="display: none;">
											<img src="/portal/img/ajax-loader.gif" border="0" />
										</div>
									</div>
								</div>
							</td>
							<?php //FIM RESULTANTES?>

							<td>
								<div class="detalhes_<?=$key;?>" style="width: 100%;">
									<div id="exibe_dano_<?=$key;?>">
										<?php echo $this->BForm->input('GrupoExposicaoRisco.'.$key.'.dano', array('label' => '&nbsp;', 'class' => 'input-mini risco','options' => $dano, 'empty' => 'Selecione', 'onchange' => 'carregaGrauRisco(this)'));?>
									</div>
									<div id="carregando_dano_<?=$key;?>" style="display: none;">
										<img src="/portal/img/ajax-loader.gif" border="0" />
									</div>
								</div>
							</td>

							<?php //GRAU DE RISCO?>
							<td> 
								<div class="detalhes_<?=$key;?>" style="width: 100%;">
									<?php if(isset($this->data['GrupoExposicaoRisco'][$key]['grau_risco']) && !empty($this->data['GrupoExposicaoRisco'][$key]['grau_risco'])):?>
										<?php if(isset($this->data['GrupoExposicaoRisco'][$key]['grau_risco']['GrauRisco']) && !empty($this->data['GrupoExposicaoRisco'][$key]['grau_risco']['GrauRisco'])):?>
											<?php $grau_risco_dados = array($this->data['GrupoExposicaoRisco'][$key]['grau_risco']['GrauRisco']['codigo'] => $this->data['GrupoExposicaoRisco'][$key]['grau_risco']['GrauRisco']['descricao']);?>
										<?php else:?>
											<?php $grau_risco_dados = array($this->data['GrupoExposicaoRisco'][$key]['GrauRisco']['codigo'] => $this->data['GrupoExposicaoRisco'][$key]['GrauRisco']['descricao']);?>
										<?php endif;?>
									<?php else:?>
										<?php $grau_risco_dados = array();?>
									<?php endif;?>
									<div id="exibe_grau_risco_<?=$key;?>">
										<?php echo $this->BForm->input('GrupoExposicaoRisco.'.$key.'.grau_risco', array('label' => '&nbsp;', 'class' => 'input-mini risco', 'options' => $grau_risco_dados, 'readonly' => 'readonly'));?>
									</div>
									<div id="carregando_grau_risco_<?=$key;?>" style="display: none;">
										<img src="/portal/img/ajax-loader.gif" border="0" />
									</div>	
								</div>
							</td>
							<?php //FIM GRAU DE RISCO?>
							<?php //EPI?>
							<td class="epi" style="vertical-align: top; padding: 0px;">
								<div class="detalhes_<?=$key;?>" style="width: 100%;">
									<div style="float: right;">
										<div class='actionbar-right'>
											<?php echo $this->Html->link('<i class="icon-plus icon-white" style="margin-right: 0;"></i>', array('controller' => 'epi', 'action' => 'buscar_epi',$key, $this->data['GrupoExposicaoRisco'][$key]['codigo_risco']), array('escape' => false, 'class' => 'btn btn-success dialog_epi risco', 'title' =>'Selecionar EPI', 'style'=> 'padding: 0 5px;'));?>
										</div>
									</div>
									<div style="float: left; width: 100%;">
										<table id="listagem_epi_<?=$key;?>"  class="listagem_epi" style="width:720px;">
											<?php if(isset($this->data['GrupoExposicaoRisco'][$key]['GrupoExposicaoRiscoEpi'])) { ?>

												<?php foreach ($this->data['GrupoExposicaoRisco'][$key]['GrupoExposicaoRiscoEpi'] as $key_epi => $dados_epi) { ?>

													<?php $epi_codigo = $this->data['GrupoExposicaoRisco'][$key]['GrupoExposicaoRiscoEpi'][$key_epi]['codigo'];?>

													<?php $codigo_epi = $dados_epi['codigo_epi'];?>
													<?php $nome_epi = $dados_epi['nome'];?>
													<?php $numero_ca = $dados_epi['numero_ca'];?>
													<?php $data_validade_ca = $dados_epi['data_validade_ca']; ?>
													<?php $atenuacao = $dados_epi['atenuacao']; ?>
													<?php $epi_eficaz = $dados_epi['epi_eficaz']; ?>
													<?php $med_protecao = $dados_epi['med_protecao']; ?>
													<?php $cond_functo = $dados_epi['cond_functo']; ?>
													<?php $uso_epi = $dados_epi['uso_epi']; ?>
													<?php $prz_valid = $dados_epi['prz_valid']; ?>
													<?php $periodic_troca = $dados_epi['periodic_troca']; ?>
													<?php $higienizacao = $dados_epi['higienizacao']; ?>

													<?php if(isset($this->data['GrupoExposicaoRisco'][$key]['GrupoExposicaoRiscoEpi'][$key_epi]['controle'])) { ?>

														<?php if(!empty($this->data['GrupoExposicaoRisco'][$key]['GrupoExposicaoRiscoEpi'][$key_epi]['controle'])) { ?>

															<?php $controle = $this->data['GrupoExposicaoRisco'][$key]['GrupoExposicaoRiscoEpi'][$key_epi]['controle']; ?>

														<?php } else { ?>

															<?php $controle = ""; ?>

														<?php } ?>

													<?php } else { ?>

														<?php $controle = ""; ?>

													<?php } ?>

													<tr class="linhas">
														<td style="font-size: 10px;  padding: 5px 0 0 5px; width:90px;">				
															<?php echo $this->BForm->input('GrupoExposicaoRisco.'.$key.'.GrupoExposicaoRiscoEpi.'.$key_epi.'.controle', array('class' => 'input-mini risco mrecomendado_epi', 'legend' => 'Recomendação','style' => 'margin-left: 0px; margin-top: 2px', 'hiddenField' => false,	'type' => 'radio',  'options' => array(1 => 'Existente',2 => 'Recomendado'), 'label' => array('class' => 'radio_epi inline','style' => 'width: 90px;'), 'onclick' => 'mudar_recomentado(this)', 'value' => $controle));?>
														</td>
														<td style="padding: 5px; padding-bottom: 0px;">
															<?php echo $this->BForm->hidden('GrupoExposicaoRisco.'.$key.'.GrupoExposicaoRiscoEpi.'.$key_epi.'.codigo', array('class' => 'codigo_epi risco', 'value' => $epi_codigo));?>

															<?php echo $this->BForm->hidden('GrupoExposicaoRisco.'.$key.'.GrupoExposicaoRiscoEpi.'.$key_epi.'.codigo_epi', array('class' => 'codigo_epi risco', 'value' => $codigo_epi));?>

															<?php echo $this->BForm->input('GrupoExposicaoRisco.'.$key.'.GrupoExposicaoRiscoEpi.'.$key_epi.'.nome', array('class' => 'risco input-medium', 'type' => 'text', 'value' => $nome_epi, 'label'=> 'Descrição', 'div' => 'control-group input text pull-left', 'readonly'=> true, 'style' => 'width:230px; margin-right: 10px;'));?>

															<?php echo $this->BForm->input('GrupoExposicaoRisco.'.$key.'.GrupoExposicaoRiscoEpi.'.$key_epi.'.numero_ca', array('class' => 'risco input-medium just-number numeric', 'maxlength' => '4', 'type' => 'text', 'value' => $numero_ca, 'label'=> 'CA', 'div' => 'control-group input text pull-left esconder'.$key.'linha'.$key_epi.' ', 'style' => 'width:60px; margin-right: 10px;'));?>

															<?php echo $this->BForm->input('GrupoExposicaoRisco.'.$key.'.GrupoExposicaoRiscoEpi.'.$key_epi.'.data_validade_ca', array('class' => 'risco input-medium data', 'type' => 'text', 'value' => $data_validade_ca, 'label'=> 'Validade', 'div' => 'control-group input text pull-left esconder'.$key.'linha'.$key_epi.'', 'style' => 'width:100px; margin-right: 0px;'));?>		

															<?php echo $this->BForm->input('GrupoExposicaoRisco.'.$key.'.GrupoExposicaoRiscoEpi.'.$key_epi.'.atenuacao', array('class' => 'risco input-medium just-number numeric', 'type' => 'text', 'value' => $atenuacao, 'label'=> 'Atenuação', 'div' => 'control-group input text pull-left esconder'.$key.'linha'.$key_epi.'', 'style' => 'width:60px;  margin-right: 10px;'));?>

															<?php echo $this->BForm->input('GrupoExposicaoRisco.'.$key.'.GrupoExposicaoRiscoEpi.'.$key_epi.'.epi_eficaz', array('class' => 'input-mini risco', 'legend' => 'Eficaz?',
																'div' => array('class' => 'control-group input radio pull-left esconder'.$key.'linha'.$key_epi.''), 'value' => $epi_eficaz,
																'style' => 'margin-left: 0px; margin-top: 2px', 'hiddenField' => false, 	'type' => 'radio',  'options' => array(1 => 'Sim',0 => 'Não'),'label' => array('class' => 'radio_epi inline ','style' => 'width:40px;')));?>

															<?php echo $this->BForm->input('GrupoExposicaoRisco.'.$key.'.GrupoExposicaoRiscoEpi.'.$key_epi.'.med_protecao', array('class' => 'input-mini risco', 'legend' => 'Foi tentada a implementação de medidas de proteção coletiva, de caráter administrativo ou de organização, optando-se pelo EPI por inviabilidade técnica, insuficiência ou interinidade, ou ainda em caráter complementar ou emergencial?',
																'div' => array('class' => 'control-group input radio pull-left esconder'.$key.'linha'.$key_epi.''), 'value' => $med_protecao,
																'style' => 'margin-left: 0px; margin-top: 2px', 'hiddenField' => false, 	'type' => 'radio',  'options' => array(1 => 'Sim',0 => 'Não'),'label' => array('class' => 'radio_epi inline ','style' => 'width:588px;')));?>

															<?php echo $this->BForm->input('GrupoExposicaoRisco.'.$key.'.GrupoExposicaoRiscoEpi.'.$key_epi.'.cond_functo', array('class' => 'input-mini risco', 'legend' => 'Foram observadas as condições de funcionamento do EPI ao longo do tempo, conforme especificação técnica do fabricante nacional ou importador, ajustadas às condições de campo?',
																'div' => array('class' => 'control-group input radio pull-left esconder'.$key.'linha'.$key_epi.''), 'value' => $cond_functo,
																'style' => 'margin-left: 0px; margin-top: 2px', 'hiddenField' => false, 	'type' => 'radio',  'options' => array(1 => 'Sim',0 => 'Não'),'label' => array('class' => 'radio_epi inline ','style' => 'width:588px;')));?>

															<?php echo $this->BForm->input('GrupoExposicaoRisco.'.$key.'.GrupoExposicaoRiscoEpi.'.$key_epi.'.uso_epi', array('class' => 'input-mini risco', 'legend' => 'Foi observado o uso ininterrupto do EPI ao longo do tempo, conforme especificação técnica do fabricante nacional ou importador, ajustadas às condições de campo?',
																'div' => array('class' => 'control-group input radio pull-left esconder'.$key.'linha'.$key_epi.''), 'value' => $uso_epi,
																'style' => 'margin-left: 0px; margin-top: 2px', 'hiddenField' => false, 	'type' => 'radio',  'options' => array(1 => 'Sim',0 => 'Não'),'label' => array('class' => 'radio_epi inline ','style' => 'width:588px;')));?>

															<?php echo $this->BForm->input('GrupoExposicaoRisco.'.$key.'.GrupoExposicaoRiscoEpi.'.$key_epi.'.prz_valid', array('class' => 'input-mini risco', 'legend' => 'Foi observado o prazo de validade do CA no momento da compra do EPI?',
																'div' => array('class' => 'control-group input radio pull-left esconder'.$key.'linha'.$key_epi.''), 'value' => $prz_valid, 'style' => 'margin-left: 0px; margin-top: 2px', 'hiddenField' => false, 	'type' => 'radio',  'options' => array(1 => 'Sim',0 => 'Não'),'label' => array('class' => 'radio_epi inline ','style' => 'width:588px;')));?>

															<?php echo $this->BForm->input('GrupoExposicaoRisco.'.$key.'.GrupoExposicaoRiscoEpi.'.$key_epi.'.periodic_troca', array('class' => 'input-mini risco', 'legend' => 'É observada a periodicidade de troca definida pelo fabricante nacional ou importador e/ou programas ambientais, comprovada mediante recibo assinado pelo usuário em época própria?',
																'div' => array('class' => 'control-group input radio pull-left esconder'.$key.'linha'.$key_epi.''), 'value' => $periodic_troca, 'style' => 'margin-left: 0px; margin-top: 2px', 'hiddenField' => false, 	'type' => 'radio',  'options' => array(1 => 'Sim',0 => 'Não'),'label' => array('class' => 'radio_epi inline ','style' => 'width:588px;')));?>

															<?php echo $this->BForm->input('GrupoExposicaoRisco.'.$key.'.GrupoExposicaoRiscoEpi.'.$key_epi.'.higienizacao', array('class' => 'input-mini risco', 'legend' => 'É observada a higienização conforme orientação do fabricante nacional ou importador?', 'div' => array('class' => 'control-group input radio pull-left esconder'.$key.'linha'.$key_epi.''), 'value' => $higienizacao,
																'style' => 'margin-left: 0px; margin-top: 2px', 'hiddenField' => false, 	'type' => 'radio',  'options' => array(1 => 'Sim',0 => 'Não'),'label' => array('class' => 'radio_epi inline ','style' => 'width:588px;')));?>
														</td>
														<td style="padding: 5px; padding-bottom: 0px; padding-top: 30px;">
															<?php echo $this->Html->link('', 'javascript:void(0)', array('class' => 'icon-trash', 'title' => 'Excluir', 'onclick' => 'excluirEpi(this)'))?>
														</td>
													</tr>
												<?php } ?>
											<?php } ?>
										</table>
									</div>	
								</div>	
							</td>
							<?php //FIM EPI?>
							<?php //EPC?>
							<td class="epc" style="vertical-align: top;padding: 0px;">
								<div class="detalhes_<?=$key;?>" style="width: 100%;">
									<div style="float: right;">
										<div class='actionbar-right'>
											<?php echo $this->Html->link('<i class="icon-plus icon-white" style="margin-right: 0;"></i>', array('controller' => 'epc', 'action' => 'buscar_epc', $key, $this->data['GrupoExposicaoRisco'][$key]['codigo_risco']), array('escape' => false, 'class' => 'btn btn-success dialog_epc risco', 'title' =>'Selecionar EPC', 'style'=> 'padding: 0 5px;'));?>
										</div>
									</div>
									<div style="float: left; width: 100%;">
										<table id="listagem_epc_<?=$key;?>" class="listagem_epc" style="width:270px;">
											<?php if(isset($this->data['GrupoExposicaoRisco'][$key]['GrupoExposicaoRiscoEpc'])):?>
												<?php foreach ($this->data['GrupoExposicaoRisco'][$key]['GrupoExposicaoRiscoEpc'] as $key_epc => $dados_epc):?>
													<?php $epc_codigo = $this->data['GrupoExposicaoRisco'][$key]['GrupoExposicaoRiscoEpc'][$key_epc]['codigo'];?>

													<?php if(isset($this->data['GrupoExposicaoRisco'][$key]['GrupoExposicaoRiscoEpc'][$key_epc]['controle'])):?>
														<?php $controle = $this->data['GrupoExposicaoRisco'][$key]['GrupoExposicaoRiscoEpc'][$key_epc]['controle'];?>
													<?php else:?>
														<?php $controle = "" ;?>
													<?php endif;?>

													<?php if(isset($this->data['GrupoExposicaoRisco'][$key]['GrupoExposicaoRiscoEpc'][$key_epc]['epc_eficaz'])):?>
														<?php $epc_eficaz = $this->data['GrupoExposicaoRisco'][$key]['GrupoExposicaoRiscoEpc'][$key_epc]['epc_eficaz'];?>
													<?php else:?>
														<?php $epc_eficaz = "" ;?>
													<?php endif;?>

													<?php $codigo_epc = $dados_epc['codigo_epc'];?>
													<?php $nome_epc = $dados_epc['nome'];?>

													<tr class="linhas">
														<td style="font-size: 10px;  padding: 5px 0 0 5px; width:90px;">
															<?php echo $this->BForm->input('GrupoExposicaoRisco.'.$key.'.GrupoExposicaoRiscoEpc.'.$key_epc.'.controle', array('class' => 'input-mini risco', 'legend' => 'Recomendação','style' => 'margin-left: 0px; margin-top: 2px', 'hiddenField' => false,	'type' => 'radio', 'class' => 'recomendacao_epi', 'data-codigo' => $key, 'options' => array(1 => 'Existente', 2 => 'Recomendado'),'label' => array('class' => 'radio_epc inline','style' => 'width: 90px;'),'value' => $controle));?>
														</td>
														<div class="eficacia_epi">
															<td style="padding: 5px; padding-bottom: 0px;"  id="epc_eficaz_<?= $key ?>">
																<?php echo $this->BForm->input('GrupoExposicaoRisco.'.$key.'.GrupoExposicaoRiscoEpc.'.$key_epc.'.epc_eficaz', array('class' => 'input-mini risco', 'legend' => 'Eficaz ?','style' => 'margin-left: 0px; margin-top: 2px', 'hiddenField' => false,	'type' => 'radio', 'options' => array(1 => 'Sim',0 => 'Não'),'label' => array('class' => 'radio_epc inline','style' => 'width: 90px;'),'value' => $epc_eficaz));?>
															</td>
														</div>
														<td style="padding: 5px; padding-bottom: 0px;">
															<?php echo $this->BForm->hidden('GrupoExposicaoRisco.'.$key.'.GrupoExposicaoRiscoEpc.'.$key_epc.'.codigo', array('class' => 'codigo_epc risco', 'value' => $epc_codigo));?>

															<?php echo $this->BForm->hidden('GrupoExposicaoRisco.'.$key.'.GrupoExposicaoRiscoEpc.'.$key_epc.'.codigo_epc', array('class' => 'codigo_epc risco', 'value' => $codigo_epc));?>		
															<?php echo $this->BForm->input('GrupoExposicaoRisco.'.$key.'.GrupoExposicaoRiscoEpc.'.$key_epc.'.nome', array('class' => 'risco input-medium', 'type' => 'text', 'value' => $nome_epc, 'label'=> 'Descrição', 'readonly'=> true, 'style' => 'width:130px'));?>		
														</td>
														<td style="padding: 5px; padding-bottom: 0px; padding-top:">
															<?php echo $this->Html->link('', 'javascript:void(0)', array('class' => 'icon-trash', 'title' => 'Excluir', 'onclick' => 'excluirEpc(this)'))?>
														</td>
													</tr>
												<?php endforeach;?>
											<?php endif;?>
										</table>
									</div>
								</div>	
							</td>
							<?php //FIM EPC?>
							
							<?php //INICIO MEDIDAS DE CONTROLE?>
							<td>
								<div class="detalhes_<?=$key;?>" style="width: 100%;">
									<?php echo $this->BForm->input('GrupoExposicaoRisco.'.$key.'.medidas_controle', array('type' => 'textarea', 'label' => '&nbsp;', 'style' => 'width: 340px; height: 80px;', 'class' => 'input-xlarge risco'));?>
								</div>
							</td>
							<td>
								<div class="detalhes_<?=$key;?>" style="width: 100%;">
									<?php echo $this->BForm->input('GrupoExposicaoRisco.'.$key.'.medidas_controle_recomendada', array('type' => 'textarea', 'label' => '&nbsp;', 'style' => 'width: 340px; height: 80px;', 'class' => 'input-xlarge risco'));?>
								</div>
							</td>
							<?php //FIM MEDIDAS DE CONTROLE?>

							<?php //EXCLUIR GRUPO RISCO JÁ CADASTRADO?>
							<td>
								<div id="excluir_risco_load_<?php echo $this->data['GrupoExposicaoRisco'][$key]['codigo']; ?>" class="carregando" style="display: none;">
									<img src="/portal/img/ajax-loader.gif" border="0" />
								</div>
								<?php if(isset($this->data['GrupoExposicaoRisco'][$key]['codigo']) && !empty($this->data['GrupoExposicaoRisco'][$key]['codigo'])):?>
									<?php echo $this->Html->link('', 'javascript:void(0)', array('onclick' => 'excluirGrupoExposicaoRiscoCadastrado('.@$this->data['GrupoExposicaoRisco'][$key]['codigo'].', '.@$this->data['GrupoExposicaoRisco'][$key]['codigo_grupo_exposicao'].', this);', 'class' => "icon-trash excluir_<?php echo @$this->data['GrupoExposicaoRisco'][$key]['codigo']; ?>", 'title' => 'Excluir Risco'));?>
								<?php else:?>
									<?php echo $this->Html->link('', 'javascript:void(0)', array('class' => 'icon-trash', 'title' => 'Excluir', 'onclick' => 'excluirGrupoExposicaoRisco(this)'))?>
								<?php endif;?>
							</td>
							<?php //FIM EXCLUIR GRUPO RISCO JÁ CADASTRADO?>
						</tr>
					<?php endforeach;?>

				<?php endif;?>
			</tbody>
		</table>
		</div>
		<div class='well detalhes_grupos_exposicao'>
			<div class='row-fluid inline'>
				<div class="control-group input text" style="margin-bottom: 0;">
					<label for="GrupoExposicaoRiscoObservacao" style="width: 85px">Observações</label>
					<?php echo $this->BForm->input('GrupoExposicao.observacao', array('type' => 'textarea', 'label' => false, 'div' => false, 'style' => 'width: 990px; height: 60px;'));?>
				</div>
			</div>
		</div>
		<div class='form-actions btn_salvar'>
			<?php echo $this->BForm->submit('Salvar', array('div' => false, 'class' => 'btn btn-primary '));?>
			<?= $html->link('Voltar', 
				( Comum::UrlOrigem() ? Comum::UrlOrigem()->data : array('controller' => 'grupos_exposicao', 'action' => 'index', (isset($this->data['Unidade']['codigo']) && !empty($this->data['Unidade']['codigo']) ? $this->data['Unidade']['codigo'] : $dados_cliente['Unidade']['codigo'])) ), 
				array('class' => 'btn') );  ?>
		</div>


		<div style="display:none;">
			<table id="modelo_campos">
				<tr class="campos_riscos">
					<td>
						<?php echo $this->BForm->input('GrupoExposicaoRisco.k.codigo', array('class' => 'risco', 'type' => 'hidden'));?>		
						<?php echo $this->BForm->input('GrupoExposicaoRisco.k.codigo_grupo_risco', array('options' => $grupo_risco_inclusao, 'label' => 'Agente', 'class' => 'input-small risco', 'empty' => 'Selecione', 'onchange'=>'carregaRiscoPorGrupo(this)'));?>

						<div class="control-group input select">
							<div id="btn_k" class="btn_input">
								<?php echo $this->BForm->input('GrupoExposicaoRisco.k.codigo_risco', array('options' => array(), 'empty' => 'Selecione', 'label' => 'Substâncias', 'div' => false, 'class' => 'input-small substancias risco codigo_risco', 'onchange'=>'carregaDadosRisco(this);'));?>
							</div>
							<div id="carregando_k" class="carregando" style="display: none;">
								<img src="/portal/img/ajax-loader.gif" border="0" />
							</div>
						</div>
					</td>
					<td class="fontes_geradoras" style="vertical-align: top; padding: 0px;">
						<div class="detalhes_k" style="width: 100%;">
							<div style="float: right;">
								<div class='actionbar-right'>
									<?php echo $this->Html->link('<i class="icon-plus icon-white" style="margin-right: 0;"></i>', array('controller' => 'fontes_geradoras', 'action' => 'buscar_fonte_geradora','k','codigo_risco'), array('escape' => false, 'class' => 'btn btn-success dialog_fontes_geradoras risco', 'title' =>'Selecionar Fontes Geradoras', 'style'=> 'padding: 0 5px;'));?>
								</div>
							</div>
							<div style="float: left; width: 100%;">
								<table id="listagem_fonte_geradora_k" style="width:200px;">
								</table>
							</div>
						</div>
					</td>
					<td class="efeitos_criticos" style="vertical-align: top; padding: 0px;" >
						<div class="detalhes_k" style="width: 100px;">

							<div style="float: right;">
								<div class='actionbar-right'>
									<?php echo $this->Html->link('<i class="icon-plus icon-white" style="margin-right: 0;"></i>', array('controller' => 'riscos_atributos_detalhes', 'action' => 'buscar_efeitos_criticos','k','codigo_risco'), array('escape' => false, 'class' => 'btn btn-success dialog_efeitos_criticos risco', 'title' =>'Selecionar Efeitos Críticos', 'style'=> 'padding: 0 5px;'));?>
								</div>
							</div>
							<div style="float: left; width: 100%;">
								<table id="listagem_efeitos_criticos_k" style="width:200px;">									
								</table>
							</div>

							<?php //echo $this->BForm->input('GrupoExposicaoRisco.k.codigo_efeito_critico', array('label' => '&nbsp;', 'class' => 'input-small risco', 'options' => $array_efeito, 'empty' => 'Selecione'));?>
						</div>
					</td>
					<td>
						<div class="detalhes_k" style="width: 100px;">
							<?php echo $this->BForm->input('GrupoExposicaoRisco.k.codigo_risco_atributo', array('label' => '&nbsp;', 'class' => 'input-small risco', 'options' => $meios_exposicao, 'empty' => 'Selecione'));?>
						</div>
					</td>
					<td>
						<div class="detalhes_k" class="wfull">
							<table id="avaliacao_k">
								<tr style="display: flex;">
									<td class="tipo_k tipo_avaliacao" style="padding-top: 0px">
										<?php echo $this->BForm->input('GrupoExposicaoRisco.k.codigo_tipo_medicao', array('class' => 'input-mini risco', 'legend' => false,'style' => 'margin-left: 0px; margin-top: 2px', 'div' => false,'hiddenField' => false,	'type' => 'radio',  'options' => array(1 => 'Quantitativo',2 => 'Qualitativo'), 'onclick'=> 'selecionaTipoAvaliacao(this);', 'label' => array('class' => 'radio_avaliacao inline','style' => 'font-size:11px;')));?>

										<div class="overhide hide" style="margin-top:13px;">
											<?php echo $this->BForm->input('GrupoExposicaoRisco.k.dosimetria', array('type' => 'checkbox', 'hiddenField' => false, 'class' => 'dosimetria')) ?>
											<?php echo $this->BForm->input('GrupoExposicaoRisco.k.avaliacao_instantanea', array('type' => 'checkbox', 'label' => 'Avaliação instantânea', 'hiddenField' => false, 'class' => 'avaliacao_instantanea')) ?>
										</div>

									</td>
									<td class="campos_aval_k campo_avaliacao" style="padding-top: 0px">

									<div class="avaliacao_comum">
										<div class="control-group input select">
											<?php echo $this->BForm->input('GrupoExposicaoRisco.k.codigo_tecnica_medicao', array('label' => 'Unidade de Medida', 'class' => 'input-small risco','options' => $unidades_medida, 'empty' => 'Selecione', 'div' => false));?>
										</div>
										<div class="control-group input text">
											<?php echo $this->BForm->input('GrupoExposicaoRisco.k.valor_maximo', array('label' => 'Limite de Tolerância', 'class' => 'input-mini risco', 'div' => false));?>
										</div>
										<div class="control-group input text">
											<?php echo $this->BForm->input('GrupoExposicaoRisco.k.valor_medido', array('label' => 'Valor Medido', 'class' => 'input-mini risco moeda3 just-number numeric', 'div' => false));?>
										</div>
										<div class="control-group input select">
											<?php echo $this->BForm->input('GrupoExposicaoRisco.k.codigo_tec_med_ppra', array('label' => 'Tecnicas de Medição', 'class' => 'input-small risco', 'options' => $tecnicas_medicao, 'selected' => $last_tecnicas_medicao, 'div' => false));?>
										</div>
									</div>

									<div id="avaliacao_calor_k" class="avaliacao_calor overhide hide">
										<div class="pull-left mw212 margin-bottom-5">
											<div class="pull-left font-size-11 margin-right-10 margin-top-30 w60">
												<strong>Descanso: </strong>
											</div>	
											<div>
												<?php echo $this->BForm->input('GrupoExposicaoRisco.k.descanso_tbn', array('div' => 'pull-left margin-right-10', 'label' => 'TBN', 'class' => 'w30 reverse just-number numeric')) ?>

												<?php echo $this->BForm->input('GrupoExposicaoRisco.k.descanso_tbs', array('div' => 'pull-left margin-right-10', 'label' => 'TBS', 'class' => 'w30 reverse just-number numeric')) ?>

												<?php echo $this->BForm->input('GrupoExposicaoRisco.k.descanso_tg', array('div' => 'pull-left', 'label' => 'TG', 'class' => 'w30 reverse just-number numeric')) ?>
											</div>
											<div class="clear"></div>
											<div>
												<?php echo $this->BForm->input('GrupoExposicaoRisco.k.descanso_no_local', array('class' => 'input-mini risco', 'legend' => false, 'div' => false,'hiddenField' => false,	'type' => 'radio',  'options' => array(1 => 'No local', 0 => 'Fora do local'), 'label' => array('class' => 'inline pull-left margin-left-0 margin-top-5 margin-right-10')));?>
											</div>
										</div>
										<div class="pull-left mw216 margin-left-10">
											<div class="pull-left font-size-11 margin-right-10 margin-top-30 w60">
												<strong>Trabalho: </strong>
											</div>	
											<div>
												<?php echo $this->BForm->input('GrupoExposicaoRisco.k.trabalho_tbn', array('div' => 'pull-left margin-right-10', 'label' => 'TBN', 'class' => 'w30 reverse just-number numeric')) ?>

												<?php echo $this->BForm->input('GrupoExposicaoRisco.k.trabalho_tbs', array('div' => 'pull-left margin-right-10', 'label' => 'TBS', 'class' => 'w30 reverse just-number numeric')) ?>

												<?php echo $this->BForm->input('GrupoExposicaoRisco.k.trabalho_tg', array('div' => 'pull-left margin-right-10', 'label' => 'TG', 'class' => 'w30 reverse just-number numeric')) ?>
											</div>
											<div class="clear margin-top-15"></div>
											<div class="pull-left font-size-11 margin-right-10 padding-top-5">
												<strong>Carga Solar: </strong>
											</div>
											<div class="pull-left">
												<?php echo $this->BForm->input('GrupoExposicaoRisco.k.carga_solar', array('class' => 'input-mini risco', 'legend' => false, 'div' => false,'hiddenField' => false,	'type' => 'radio',  'options' => array(1 => 'Com', 0 => 'Sem'), 'label' => array('class' => 'inline pull-left margin-left-0 margin-top-5 margin-right-10')));?>
											</div>
										</div>
									</div>

								</td>
								</tr>
							</table>
						</div>
					</td>
					<td>
						<div class="detalhes_k" style="width: 250px;">
							<div class="control-group input select pull-left">
								<?php echo $this->BForm->input('GrupoExposicaoRisco.k.tempo_exposicao', array('label' => 'Tipo', 'div' =>false, 'class' => 'input-mini risco','options' => $tempo_exposicao, 'empty' => 'Selecione', 'onchange' => 'carregaResultante(this)', 'style' => ' margin-right:10px;'));?>
							</div>
							<div class="control-group input text pull-left">
								<?php echo $this->BForm->input('GrupoExposicaoRisco.k.minutos_tempo_exposicao', array('label' => 'Tempo de Exposição(MIN)', 'div' =>false, 'class' => 'input-mini risco just-number numeric', '	style' => 'width:40px;  margin-right:10px;'));?>
							</div>
							<div class="control-group input text pull-left">
								<?php echo $this->BForm->input('GrupoExposicaoRisco.k.jornada_tempo_exposicao', array('label' => 'Jornada de Trabalho', 'div' =>false, 'class' => 'input-mini risco just-number numeric', 'style' => 'width:40px; margin-right:10px;'));?>
							</div>
							<div class="control-group input text pull-left hide overhide-input">
								<?php echo $this->BForm->input('GrupoExposicaoRisco.k.descanso_tempo_exposicao', array('label' => 'Descanso', 'div' =>false, 'class' => 'input-mini risco just-number numeric hide', '	style' => 'width:40px;'));?>
							</div>
						</div>
					</td>
					<td>
						<div class="detalhes_k" style="width: 80px;">
							<?php echo $this->BForm->input('GrupoExposicaoRisco.k.intensidade', array('label' => '&nbsp;', 'class' => 'input-mini risco','options' => $intensidade, 'empty' => 'Selecione', 'onchange' => 'carregaResultante(this)'));?>
						</div>
					</td>
					<td>
						<div class="detalhes_k" style="width: 80px;">
							<div class="control-group input select">
								<div id="exibe_resultante_k">
									<?php echo $this->BForm->input('GrupoExposicaoRisco.k.resultante', array('label' => '&nbsp;', 'class' => 'input-mini risco','options' => array(), 'readonly' => 'readonly', 'onchange' => 'carregaGrauRisco(this)'));?>
								</div>
								<div id="carregando_resultante_k" style="display: none;">
									<img src="/portal/img/ajax-loader.gif" border="0" />
								</div>
							</div>
						</div>
					</td>
					<td>
						<div class="detalhes_k" style="width: 80px;">
							<div id="exibe_dano_k">
								<?php echo $this->BForm->input('GrupoExposicaoRisco.k.dano', array('label' => '&nbsp;', 'class' => 'input-mini risco','options' => $dano, 'empty' => 'Selecione', 'onchange' => 'carregaGrauRisco(this)'));?>
							</div>
							<div id="carregando_dano_k" style="display: none;">
								<img src="/portal/img/ajax-loader.gif" border="0" />
							</div>	
						</div>
					</td>
					<td>
						<div class="detalhes_k" style="width: 80px;">
							<div id="exibe_grau_risco_k">
								<?php echo $this->BForm->input('GrupoExposicaoRisco.k.grau_risco', array('label' => '&nbsp;', 'class' => 'input-mini risco', 'options' => array(), 'readonly' => 'readonly'));?>
							</div>
							<div id="carregando_grau_risco_k" style="display: none;">
								<img src="/portal/img/ajax-loader.gif" border="0" />
							</div>
						</div>
					</td>	

					<td class="epi" style="vertical-align: top; padding: 0px;">
						<div class="detalhes_k" style="width: 100%;">
							<div style="float: right;">
								<div class='actionbar-right'>
									<?php echo $this->Html->link('<i class="icon-plus icon-white" style="margin-right: 0;"></i>', array('controller' => 'epi', 'action' => 'buscar_epi','k', 'codigo_risco'), array('escape' => false, 'class' => 'btn btn-success dialog_epi risco', 'title' =>'Selecionar EPI', 'style'=> 'padding: 0 5px;'));?>
								</div>
							</div>
							<div style="float: left; width: 100%;">
								<table id="listagem_epi_k"  class="listagem_epi" style="width:720px;">
								</table>
							</div>	
						</div>	
					</td>
					<td class="epc" style="vertical-align: top;padding: 0px;">
						<div class="detalhes_k" style="width: 100%;">
							<div style="float: right;">
								<div class='actionbar-right'>
									<?php echo $this->Html->link('<i class="icon-plus icon-white" style="margin-right: 0;"></i>', array('controller' => 'epc', 'action' => 'buscar_epc', 'k', 'codigo_risco'), array('escape' => false, 'class' => 'btn btn-success dialog_epc risco', 'title' =>'Selecionar EPC', 'style'=> 'padding: 0 5px;'));?>
								</div>
							</div>
							<div style="float: left; width: 100%;">
								<table id="listagem_epc_k" class="listagem_epc" style="width:270px;">
								</table>
							</div>
						</div>	
					</td>
					<td style="padding: 0px; vertical-align: top;">
						<div class="detalhes_k" style="width: 350px;">
							<?php echo $this->BForm->input('GrupoExposicaoRisco.k.medidas_controle', array('type' => 'textarea', 'label' => '&nbsp;', 'style' => 'width: 340px; height: 80px;', 'class' => 'input-xlarge risco'));?>
						</div>
					</td>
					<td style="padding: 0px; vertical-align: top;">
						<div class="detalhes_k" style="width: 350px;">
							<?php echo $this->BForm->input('GrupoExposicaoRisco.k.medidas_controle_recomendada', array('type' => 'textarea', 'label' => '&nbsp;', 'style' => 'width: 340px; height: 80px;', 'class' => 'input-xlarge risco'));?>
						</div>
					</td>
					<td><?php echo $this->Html->link('', 'javascript:void(0)', array('class' => 'icon-trash', 'title' => 'Excluir', 'onclick' => 'excluirGrupoExposicaoRisco(this)'))?></td>
				</tr>	
			</table>
		</div>

		<div style="display:none;">
			<table id="modelo_fonte_geradora">
				<tr class="linhas">
					<td style="width:180px;">
						<?php echo $this->BForm->input('GrupoExposicaoRisco.k.GrupoExpRiscoFonteGera.x.codigo_fontes_geradoras', array('class' => 'codigo_fonte_geradora risco', 'type' => 'hidden'));?>	
						<?php echo $this->BForm->input('GrupoExposicaoRisco.k.GrupoExpRiscoFonteGera.x.nome', array('class' => 'risco input-medium', 'type' => 'text','label'=> false, 'readonly'=> true, 'style' => 'width:130px'));?>		
					</td>
					<td style="width:20px;"><?php echo $this->Html->link('', 'javascript:void(0)', array('class' => 'icon-trash', 'title' => 'Excluir', 'onclick' => 'excluirFonteGeradora(this)'))?></td>
				</tr>
				<table>
				</div>

		<div style="display:none;">
			<table id="modelo_efeitos_criticos">
				<tr class="linhas">
					<td style="width:180px;">
						<?php echo $this->BForm->input('GrupoExposicaoRisco.k.GrupoExpEfeitoCritico.x.codigo_efeito_critico', array('class' => 'codigo_efeito_critico risco', 'type' => 'hidden'));?>	
						<?php echo $this->BForm->input('GrupoExposicaoRisco.k.GrupoExpEfeitoCritico.x.descricao', array('class' => 'risco input-medium', 'type' => 'text','label'=> false, 'readonly'=> true, 'style' => 'width:130px'));?>		
					</td>
					<td style="width:20px;"><?php echo $this->Html->link('', 'javascript:void(0)', array('class' => 'icon-trash', 'title' => 'Excluir', 'onclick' => 'excluirEfeitoCritico(this)'))?></td>
				</tr>
			<table>
		</div>

		<div style="display:none;">
			<table id="modelo_epi" >
				<tr class="linhas">
					<td style="font-size: 10px; padding: 5px 0 0 5px; width:90px;">
						<?php echo $this->BForm->input('GrupoExposicaoRisco.k.GrupoExposicaoRiscoEpi.x.controle', array('class' => 'input-mini risco', 'legend' => 'Recomendação','style' => 'margin-left: 0px; margin-top: 2px','hiddenField' => false,	'type' => 'radio',  'options' => array(1 => 'Existente',2 => 'Recomendado'),'label' => array('class' => 'radio_epi inline','style' => 'width: 90px;'), 'onclick' => 'mudar_recomentado(this)'));?>
					</td>
					<td style="padding: 5px; padding-bottom: 0px;">
						<?php echo $this->BForm->hidden('GrupoExposicaoRisco.k.GrupoExposicaoRiscoEpi.x.codigo_epi', array('class' => 'codigo_epi risco'));?>		

						<?php echo $this->BForm->input('GrupoExposicaoRisco.k.GrupoExposicaoRiscoEpi.x.nome', array('class' => 'risco input-medium descricao.k.linha.x', 'type' => 'text','label'=> 'Descrição', 'div' => 'control-group input text pull-left',  'readonly'=> true, 'style' => 'width:230px;  margin-right: 10px;'));?>	
						
						<?php echo $this->BForm->input('GrupoExposicaoRisco.k.GrupoExposicaoRiscoEpi.x.numero_ca', array('class' => 'risco input-medium just-number numeric', 'maxlength' => '4', 'type' => 'text', 'label'=> 'CA', 'div' => 'control-group input text pull-left esconder.k.linha.x', 'style' => 'width:60px; margin-right: 10px;'));?>

						<?php echo $this->BForm->input('GrupoExposicaoRisco.k.GrupoExposicaoRiscoEpi.x.data_validade_ca', array('class' => 'risco input-medium', 'type' => 'text', 'label'=> 'Validade', 'div' => 'control-group input text pull-left esconder.k.linha.x',  'style' => 'width:100px; margin-right: 0px;'));?>

						<?php echo $this->BForm->input('GrupoExposicaoRisco.k.GrupoExposicaoRiscoEpi.x.atenuacao', array('class' => 'risco input-medium just-number numeric', 'type' => 'text', 'label'=> 'Atenuação', 'div' => 'control-group input text pull-left esconder.k.linha.x', 'style' => 'width:60px;  margin-right: 10px;'));?>

						<?php echo $this->BForm->input('GrupoExposicaoRisco.k.GrupoExposicaoRiscoEpi.x.epi_eficaz', array('class' => 'input-mini risco', 'legend' => 'Eficaz?', 'div' => array('class' => 'control-group input radio pull-left esconder.k.linha.x'),'style' => 'margin-left: 0px; margin-top: 2px', 'hiddenField' => false, 	'type' => 'radio',  'options' => array(1 => 'Sim',0 => 'Não'),'label' => array('class' => 'radio_epi inline','style' => 'width: 40px;')));?>

						<?php echo $this->BForm->input('GrupoExposicaoRisco.k.GrupoExposicaoRiscoEpi.x.med_protecao', array('class' => 'input-mini risco', 'legend' => 'Foi tentada a implementação de medidas de proteção coletiva, de caráter administrativo ou de organização, optando-se pelo EPI por inviabilidade técnica, insuficiência ou interinidade, ou ainda em caráter complementar ou emergencial?', 'div' => array('class' => 'control-group input radio pull-left esconder.k.linha.x'),'style' => 'margin-left: 0px; margin-top: 2px', 'hiddenField' => false, 	'type' => 'radio',  'options' => array(1 => 'Sim',0 => 'Não'),'label' => array('class' => 'radio_epi inline','style' => 'width: 588px;')));?>

						<?php echo $this->BForm->input('GrupoExposicaoRisco.k.GrupoExposicaoRiscoEpi.x.cond_functo', array('class' => 'input-mini risco', 'legend' => 'Foram observadas as condições de funcionamento do EPI ao longo do tempo, conforme especificação técnica do fabricante nacional ou importador, ajustadas às condições de campo?', 'div' => array('class' => 'control-group input radio pull-left esconder.k.linha.x'),'style' => 'margin-left: 0px; margin-top: 2px', 'hiddenField' => false, 	'type' => 'radio',  'options' => array(1 => 'Sim',0 => 'Não'),'label' => array('class' => 'radio_epi inline','style' => 'width: 588px;')));?>

						<?php echo $this->BForm->input('GrupoExposicaoRisco.k.GrupoExposicaoRiscoEpi.x.uso_epi', array('class' => 'input-mini risco', 'legend' => 'Foi observado o uso ininterrupto do EPI ao longo do tempo, conforme especificação técnica do fabricante nacional ou importador, ajustadas às condições de campo?', 'div' => array('class' => 'control-group input radio pull-left esconder.k.linha.x'),'style' => 'margin-left: 0px; margin-top: 2px', 'hiddenField' => false, 	'type' => 'radio',  'options' => array(1 => 'Sim',0 => 'Não'),'label' => array('class' => 'radio_epi inline','style' => 'width: 588px;')));?>

						<?php echo $this->BForm->input('GrupoExposicaoRisco.k.GrupoExposicaoRiscoEpi.x.prz_valid', array('class' => 'input-mini risco', 'legend' => 'Foi observado o prazo de validade do CA no momento da compra do EPI?', 'div' => array('class' => 'control-group input radio pull-left esconder.k.linha.x'),'style' => 'margin-left: 0px; margin-top: 2px', 'hiddenField' => false, 	'type' => 'radio',  'options' => array(1 => 'Sim',0 => 'Não'),'label' => array('class' => 'radio_epi inline','style' => 'width: 588px;')));?>

						<?php echo $this->BForm->input('GrupoExposicaoRisco.k.GrupoExposicaoRiscoEpi.x.periodic_troca', array('class' => 'input-mini risco', 'legend' => 'É observada a periodicidade de troca definida pelo fabricante nacional ou importador e/ou programas ambientais, comprovada mediante recibo assinado pelo usuário em época própria?', 'div' => array('class' => 'control-group input radio pull-left esconder.k.linha.x'),'style' => 'margin-left: 0px; margin-top: 2px', 'hiddenField' => false, 	'type' => 'radio',  'options' => array(1 => 'Sim',0 => 'Não'),'label' => array('class' => 'radio_epi inline','style' => 'width: 588px;')));?>

						<?php echo $this->BForm->input('GrupoExposicaoRisco.k.GrupoExposicaoRiscoEpi.x.higienizacao', array('class' => 'input-mini risco', 'legend' => 'É observada a higienização conforme orientação do fabricante nacional ou importador?', 'div' => array('class' => 'control-group input radio pull-left esconder.k.linha.x'),'style' => 'margin-left: 0px; margin-top: 2px', 'hiddenField' => false, 	'type' => 'radio',  'options' => array(1 => 'Sim',0 => 'Não'),'label' => array('class' => 'radio_epi inline','style' => 'width: 588px;')));?>
						

					</td>
					<td style="padding: 5px; padding-bottom: 0px; padding-top: 30px;"><?php echo $this->Html->link('', 'javascript:void(0)', array('class' => 'icon-trash', 'title' => 'Excluir', 'onclick' => 'excluirEpi(this)'))?></td>
				</tr>
			<table>
		</div>

		<div style="display:none;">
			<table id="modelo_epc">
				<tr class="linhas">
					<td style="font-size: 10px;  padding: 5px 0 0 5px; width:90px;">
						<?php echo $this->BForm->input('GrupoExposicaoRisco.k.GrupoExposicaoRiscoEpc.x.controle', array('class' => 'input-mini risco', 'legend' => 'Recomendação','style' => 'margin-left: 0px; margin-top: 2px', 'hiddenField' => false, 'type' => 'radio',  'options' => array(1 => 'Existente',2 => 'Recomendado'),'label' => array('class' => 'radio_epc inline','style' => 'width: 90px;')));?>
					</td>
					<td style="padding: 5px; padding-bottom: 0px;">
						<?php echo $this->BForm->hidden('GrupoExposicaoRisco.k.GrupoExposicaoRiscoEpc.x.codigo_epc', array('class' => 'codigo_epc risco'));?>		
						<?php echo $this->BForm->input('GrupoExposicaoRisco.k.GrupoExposicaoRiscoEpc.x.nome', array('class' => 'risco input-medium', 'type' => 'text','label'=> 'Descrição', 'readonly'=> true, 'style' => 'width:130px'));?>	
					</td>
					<td style="padding: 5px; padding-bottom: 0px; padding-top:30px;"><?php echo $this->Html->link('', 'javascript:void(0)', array('class' => 'icon-trash', 'title' => 'Excluir', 'onclick' => 'excluirEpc(this)'))?></td>
				</tr>
			<table>
		</div>

		<?php echo $this->Javascript->codeBlock('

			$(document).ready(function(){
				
				setup_mascaras();
				setup_datepicker();

				$(".modal").css("z-index", "-1");
				$(".modal").css("width", "35%");

				jQuery(".div_outros").hide();

				if($("#GrupoExposicaoFuncionarioEntrevistado").val() == 0){
					jQuery(".div_outros").show();
				}

				if ($("#GrupoExposicaoDescricaoTipoSetorCargo1").is(":checked")){
					$(".setor_cargo").css("display", "block");
					$(".setor_cargo_ghe").css("display", "none");
					$("#GrupoExposicaoCodigoGrupoHomogeneo").val("");
				}
				else if ($("#GrupoExposicaoDescricaoTipoSetorCargo2").is(":checked")){
					$(".descricao_cargo").css("display", "block");
					$(".setor_cargo").css("display", "none");
					$(".setor_cargo_ghe").css("display", "block");
					if($("#GrupoExposicaoCodigoGrupoHomogeneo").val() != null){
						$("#exibe_descricao_ghe").css("display", "block");
					}
					else{
						$("#exibe_descricao_ghe").css("display", "none");
					}
					$("#ClienteSetorCodigoSetor").val("");
					$("#GrupoExposicaoCodigoCargo").val("");	
					$("#GrupoExposicaoCodigoFuncionario").val("");
				}
				else{
					$(".descricao_cargo").css("display", "none");
					$(".setor_cargo").css("display", "none");
					$(".setor_cargo_ghe").css("display", "none");
					$("#ClienteSetorCodigoSetor").val("");
					$("#GrupoExposicaoCodigoCargo").val("");
					$("#GrupoExposicaoCodigoFuncionario").val("");
				}

				$.each($("#grupos_exposicao_riscos .campos_riscos"), function(id, dados) {
					$.each($(dados).find("#avaliacao_"+id+" td.tipo_"+id), function(key, dados_aval) {
						if ($(dados_aval).find("input#GrupoExposicaoRisco"+id+"CodigoTipoMedicao1").is(":checked")) {
							var codigotipomedicao1 = $(dados_aval).find("input#GrupoExposicaoRisco"+id+"CodigoTipoMedicao1");
							if($(codigotipomedicao1).val()== 1){
								$(".campos_aval_"+id).show();	
								$("#avaliacao_"+id);
							}
							else{
								$(".campos_aval_"+id).hide();	
								$("#avaliacao_"+id);	
							}
						}
						else{
							$(".campos_aval_"+id).hide();
							$("#avaliacao_"+id);
						}
					});
				});

				$.each($(".mrecomendado_epi"), function(id,value) {
					if ($(this).is(":checked")) {
						mudar_recomentado(this);
					}
				});


				if($("#GrupoExposicaoEditMode").val() == "" && $("#grupos_exposicao_riscos tr.campos_riscos").length == 0){
					$("#grupos_exposicao_riscos .campos_riscos").css("display", "none");
				}

				$(document).on("click", ".dialog_fontes_geradoras", function(e) {
					e.preventDefault();
					open_dialog(this, "Fontes Geradoras", 960, undefined, undefined, undefined, $(this), "grupo_exposicao_risco");
					$(this).html("<i class=\"icon-plus icon-white\" style=\"margin-right: 0;\"></i>");
				});

				$(document).on("click", ".dialog_epi", function(e) {
					e.preventDefault();
					open_dialog(this, "EPI", 960, undefined, undefined, undefined, $(this), "grupo_exposicao_risco");
					$(this).html("<i class=\"icon-plus icon-white\" style=\"margin-right: 0;\"></i>");
				});

				$(document).on("click", ".dialog_epc", function(e) {
					e.preventDefault();
					open_dialog(this, "EPC", 960, undefined, undefined, undefined, $(this), "grupo_exposicao_risco");
					$(this).html("<i class=\"icon-plus icon-white\" style=\"margin-right: 0;\"></i>");
				});

				$(document).on("click", ".dialog_efeitos_criticos", function(e) {
					e.preventDefault();
					open_dialog(this, "Efeitos Críticos", 960, undefined, undefined, undefined, $(this), "grupo_exposicao_risco");
					$(this).html("<i class=\"icon-plus icon-white\" style=\"margin-right: 0;\"></i>");
				});

				if($("#GrupoExposicaoDescricaoTipoSetorCargo").val() != "") {
					carregaFuncionario();
				}

				$(".recomendacao_epi").each(function(indice){
					var id = $(this).prop("id");		
					var data2 = $(this).data("codigo");
					var condicao_epi = $("#epc_eficaz_" + data2);
					condicao_epi.addClass("hide");					

					if($("#"+id).prop("checked")) {
						var data1 = $(this).data("codigo");
				        var eficacia_epc = $("#epc_eficaz_" + data1);

			        	if($(this).val() == 1 ) {        		
			        		eficacia_epc.removeClass("hide");
			        		eficacia_epc.addClass("show");
			        	} else {
			        		eficacia_epc.addClass("hide");
			        	}      
					}
				});

				$(document).on("change", ".recomendacao_epi", function(e) {
					e.preventDefault();
        			var data = $(this).data("codigo");
        			var eficaz_epi = $("#epc_eficaz_" + data);
        			eficaz_epi.addClass("hide");        			

        			if($(this).val() == 1 ){
		        		eficaz_epi.removeClass("hide");
		        		eficaz_epi.addClass("show");
        			}

        			if($(this).val() == 2 ){
		        		eficaz_epi.removeClass("show");
		        		eficaz_epi.addClass("hide");		  
		        		eficaz_epi.removeAttr("checked");
        			}
				});
			});

			function verificaOutros(){
				este = $(this)
				if($("#GrupoExposicaoFuncionarioEntrevistado").val() == 0){
					jQuery(".div_outros").show();
				} else {
					$("#GrupoExposicaoOutros").val("");
					jQuery(".div_outros").hide();
				}
			}

			$(".js-setor").change(function() {
				este = $(this);
				este.parents(".setor_cargo").find(".js-cargo").html("<option value=\'\'>Carregando...</option>");
				$.ajax({
				url: baseUrl + "cargos/obtem_cargos_por_ajax",
				type: "POST",
				dataType: "html",
				data: { codigo_cliente: $("#ClienteSetorCodigoClienteAlocacao").val(),
				 codigo_setor: this.value }
				})
				.done(function(response) {
				if(response) {
				este.parents(".setor_cargo").find(".js-cargo").html(response);
				}
			})
			});

			function selecionaTipoSetorCargo(elemento){
				var tipo = $(elemento).val();
				$(".descricao_cargo").css("display", "block");

				if(tipo == 1){
					$(".setor_cargo").css("display", "block");
					$(".setor_cargo_ghe").css("display", "none");
					$("#GrupoExposicaoCodigoGrupoHomogeneo").val("");
					$("#descricao_cargo_ghe").find("tbody td").remove();
				}
				else{
					$(".setor_cargo").css("display", "none");
					$(".setor_cargo_ghe").css("display", "block");

					$("#ClienteSetorCodigoSetor").val("");
					$("#GrupoExposicaoCodigoCargo").val("");
					$("#descricao_cargo_ghe").find("tbody td").remove();			
				}
			}

			function selecionaTipoAvaliacao(elemento){
				var tipo = $(elemento).val();
				var linha = $(elemento).attr("id").substr(19,1);

				if(tipo == 1){
					$(".campos_aval_"+linha).show();
					$("#avaliacao_"+linha);
				}
				else{
					$(".campos_aval_"+linha).hide();	
					$("#avaliacao_"+linha);
				}
			}

			function carregaSetorGrupoHomogeneo(elemento){

				if($(elemento).attr("id") == "GrupoExposicaoCodigoGrupoHomogeneo" && $(elemento).val() != ""){
					$.ajax({
						type: "POST",
						url: baseUrl + "grupos_homogeneos/retornaGrupoHomogeneo/" + Math.random(),
						data: {codigo : $(elemento).val()},
						dataType: "json",
						success: function(dados){
							$("#GrupoExposicaoCodigoGrupoHomogeneo").parent().find(".error-message").remove();
							if(dados == 0){
								$("#GrupoExposicaoCodigoGrupoHomogeneo").parent().addClass("error")
								$("#GrupoExposicaoCodigoGrupoHomogeneo").parent().append("<div class=\'help-block error-message\' style=\'color: #b94a48;font-size: 11px;\'>Grupo Homogêneo não cadastrado.</div>");
							}
							else if(dados == 2){
								$("#GrupoExposicaoCodigoGrupoHomogeneo").parent().addClass("error")
								$("#GrupoExposicaoCodigoGrupoHomogeneo").parent().append("<div class=\'help-block error-message\' style=\'color: #b94a48;font-size: 11px;\'>Setores/Cargos não cadastrados.</div>");
							}
							else{
							$("#GrupoExposicaoCodigoCargo").val(dados.GrupoHomDetalhe.codigo_cargo);
							carregaCaracteristica(dados.GrupoHomDetalhe.codigo_setor);
						}
						}
					});
				}
			}

			function carregaCaracteristica(codigo_setor){
				var codigo_cliente = $("#ClienteSetorCodigoClienteAlocacao").val();
				var campos = $("#caracteristicas");
				var carregando_caracteristicas = $("#carregando_caracteristicas");
				var codigo_grupo_homogeneo;

				if ($("#GrupoExposicaoDescricaoTipoSetorCargo1").is(":checked")){
					codigo_grupo_homogeneo = null;
				}
				else if ($("#GrupoExposicaoDescricaoTipoSetorCargo2").is(":checked")){
					if($("#GrupoExposicaoCodigoGrupoHomogeneo").val() != ""){
						codigo_grupo_homogeneo = $("#GrupoExposicaoCodigoGrupoHomogeneo").val();
					}
				}
				else{
					codigo_grupo_homogeneo = null;
					$(".setor_cargo").css("display", "none");
					$(".setor_cargo_ghe").css("display", "none");
				}

				$.ajax({
					type: "POST",
					url: baseUrl + "clientes_setores/buscar_caracteristicas/" + Math.random(),
					data: {codigo_cliente: codigo_cliente, codigo_setor: codigo_setor, codigo_grupo_homogeneo: codigo_grupo_homogeneo},
					dataType: "json",
					beforeSend: function(){
						campos.hide();
						carregando_caracteristicas.show();
					},
					success: function(dados){
						if(dados != false){

							$("#ClienteSetorPeDireito").val(dados.ClienteSetor.pe_direito);

							$("#ClienteSetorCobertura").val(dados.ClienteSetor.cobertura);

							$("#ClienteSetorIluminacao").val(dados.ClienteSetor.iluminacao);

							$("#ClienteSetorVentilacao").val(dados.ClienteSetor.ventilacao);

							$("#ClienteSetorPiso").val(dados.ClienteSetor.piso);
							
							$("#ClienteSetorEstrutura").val(dados.ClienteSetor.estrutura);
						}
						else{
							$("#ClienteSetorPeDireito").val("");
							$("#ClienteSetorCobertura").val("");
							$("#ClienteSetorIluminacao").val("");
							$("#ClienteSetorVentilacao").val("");
							$("#ClienteSetorPiso").val("");
							$("#ClienteSetorEstrutura").val("");
						}
					},
					complete: function(){
						campos.show();
						carregando_caracteristicas.hide();
					},
				});
			}

			function exibe_campos_riscos(acao){

				var key = $("#grupos_exposicao_riscos .campos_riscos").length;

				$("#modelo_campos tr.campos_riscos").clone().appendTo("#grupos_exposicao_riscos").show().each(function(index, elemento){

					$.each($(elemento).find("label"), function(id, dados_label) {
						$(dados_label).attr("for", $(dados_label).attr("for").replace("K", key));
					});

					$.each($(elemento).find("input[type=\'hidden\']"), function(id, dados_hidden) {
						$(dados_hidden).attr("name", $(dados_hidden).attr("name").replace("[k]", "["+ key +"]"));
						$(dados_hidden).attr("id", $(dados_hidden).attr("id").replace("K", key));
					});

					$.each($(elemento).find(".risco"), function(id, dados) {
						if($(dados).get(0).tagName != "A"){
							$(dados).attr("name", $(dados).attr("name").replace("[k]", "["+ key +"]"));
							$(dados).attr("id", $(dados).attr("id").replace("K", key));
						}
						else{
							$(dados).attr("href", $(dados).attr("href").replace("k", key));

						}

					});

					$.each($(elemento).find(".dosimetria"), function(id, dados) {
						$(dados).attr("name", $(dados).attr("name").replace("[k]", "["+ key +"]"));
						$(dados).attr("id", $(dados).attr("id").replace("K", key));
					});

					$.each($(elemento).find(".reverse"), function(id, dados) {
						$(dados).attr("name", $(dados).attr("name").replace("[k]", "["+ key +"]"));
						$(dados).attr("id", $(dados).attr("id").replace("K", key));
					});

					$.each($(elemento).find(".avaliacao_instantanea"), function(id, dados) {
						$(dados).attr("name", $(dados).attr("name").replace("[k]", "["+ key +"]"));
						$(dados).attr("id", $(dados).attr("id").replace("K", key));
					});

					$.each($(elemento).find(".carregando"), function(id, div) {
						$(div).attr("id", $(div).attr("id").replace("k", key));
					});

					$.each($(elemento).find(".btn_input"), function(id, btn_input) {
						$(btn_input).attr("id", $(btn_input).attr("id").replace("k", key));
					});


					$.each($(elemento).find("#listagem_fonte_geradora_k"), function(id, fonte_geradora) {
						$(fonte_geradora).attr("id", $(fonte_geradora).attr("id").replace("k", key));

						if(acao == "E"){
							$(fonte_geradora).find("input").attr("name", $(fonte_geradora).find("input").attr("name").replace("[k]", "["+ key +"]"));
							$(fonte_geradora).find("input").attr("id", $(fonte_geradora).find("input").attr("id").replace("K", key));
						}

					});

					$.each($(elemento).find("#listagem_efeitos_criticos_k"), function(id, efeitos_criticos) {
						$(efeitos_criticos).attr("id", $(efeitos_criticos).attr("id").replace("k", key));
					});

					$.each($(elemento).find("#listagem_epi_k"), function(id, epi) {
						$(epi).attr("id", $(epi).attr("id").replace("k", key));
					});

					$.each($(elemento).find("#listagem_epc_k"), function(id, epc) {
						$(epc).attr("id", $(epc).attr("id").replace("k", key));
					});

					$.each($(elemento).find(".detalhes_k"), function(id, detalhe) {
						$(detalhe).attr("class", $(detalhe).attr("class").replace("k", key));
						$(detalhe).css("display","none");
					});

					$.each($(elemento).find("#carregando_resultante_k"), function(id, carrega_resultante) {
						$(carrega_resultante).attr("id", $(carrega_resultante).attr("id").replace("k", key));
					});

					$.each($(elemento).find("#exibe_resultante_k"), function(id, exibe_resultante) {
						$(exibe_resultante).attr("id", $(exibe_resultante).attr("id").replace("k", key));
					});

					$.each($(elemento).find("#carregando_dano_k"), function(id, carrega_dano) {
						$(carrega_dano).attr("id", $(carrega_dano).attr("id").replace("k", key));
					});

					$.each($(elemento).find("#exibe_dano_k"), function(id, exibe_dano) {
						$(exibe_dano).attr("id", $(exibe_dano).attr("id").replace("k", key));
					});

					$.each($(elemento).find("#carregando_grau_risco_k"), function(id, carrega_grau_risco) {
						$(carrega_grau_risco).attr("id", $(carrega_grau_risco).attr("id").replace("k", key));
					});

					$.each($(elemento).find("#exibe_grau_risco_k"), function(id, exibe_grau_risco) {
						$(exibe_grau_risco).attr("id", $(exibe_grau_risco).attr("id").replace("k", key));
					});

					$.each($(elemento).find("#avaliacao_k"), function(id, avaliacao) {
						$(avaliacao).attr("id", $(avaliacao).attr("id").replace("k", key));
					});

					$.each($(elemento).find(".tipo_k"), function(id, tipo) {
						$(tipo).attr("class", $(tipo).attr("class").replace("k", key));
					});

					$.each($(elemento).find(".campos_aval_k"), function(id, campos_aval) {
						$(campos_aval).attr("class", $(campos_aval).attr("class").replace("k", key));
					});

				});

			}

			function carregaRiscoPorGrupo(elemento){ //RETORNA TODOS DOS RISCOS DO GRUPO SELECIONADO	
				$(elemento).parents(".campos_riscos").find("input.risco").prop("checked", false);
				$(".campos_aval_"+linha).hide();

				var codigo_grupo_risco = $(elemento);
				var linha = $(codigo_grupo_risco).attr("id");
				linha = linha.replace(/[^\d]+/g,"");

				var btn = $("#grupos_exposicao_riscos").find("#btn_"+linha);
				var carregando = $("#grupos_exposicao_riscos").find("#carregando_"+linha);

				$.ajax({
					type: "POST",
					url: baseUrl + "riscos/buscar_risco_por_grupo/" + codigo_grupo_risco.val() + "/" + Math.random(),
					dataType: "json",
					beforeSend: function(){
						btn.hide();
						carregando.show();
					},
					success: function(data){
						$("#GrupoExposicaoRisco"+linha+"CodigoRisco").children().remove();
						$("#GrupoExposicaoRisco"+linha+"CodigoRisco").append("<option value=\'\'>Selecione</option>");

						$.each(data, function(id, dados) {

							$("#GrupoExposicaoRisco"+linha+"CodigoRisco").append("<option value=\'" + dados.Risco.codigo + "\'>" +dados.Risco.nome_agente + "</option>");
						});				
					},
					complete: function(){
						btn.show();
						carregando.hide();
					},
				});
			}

			function carregaDadosRisco(elemento){
				$(elemento).parents(".campos_riscos").find("input.risco").prop("checked", false);
				$(".campos_aval_"+linha).hide();
				var codigo_risco = $(elemento);

				var linha = $(codigo_risco).attr("id").substr(19,1);
				$("#grupos_exposicao_riscos").find("#carregando_"+linha).clone().appendTo($("#grupos_exposicao_riscos").find("div.detalhes_"+linha));

				var carregando_detalhes = $("#grupos_exposicao_riscos").find("div.detalhes_"+linha+" #carregando_"+linha);

				$.ajax({
					type: "POST",
					url: baseUrl + "riscos/retorna_risco_por_grupo/" + codigo_risco.val() + "/" + Math.random(),
					dataType: "json",
					beforeSend: function(){
						$("#grupos_exposicao_riscos").find("div.detalhes_"+linha).find(".risco").hide()

						$(carregando_detalhes).show();
					},
					success: function(data){
						$.each(data, function(id, dados) {

							if(dados.Risco.risco_caracterizado_por_ruido) {
								$("td.tipo_"+linha).find(".overhide").removeClass("hide");
							} else {
								$("td.tipo_"+linha).find(".overhide").addClass("hide").find("input[type=\'checkbox\']").prop("checked", false);
							}

							if(dados.Risco.risco_caracterizado_por_calor) {

								$("td.campos_aval_"+linha).find(".overhide").removeClass("hide");
								$("td.campos_aval_"+linha).parents(".campos_riscos").find(".overhide-input").removeClass("hide");
								$("td.campos_aval_"+linha).find(".avaliacao_comum").addClass("hide");
							} else {
								$("td.campos_aval_"+linha).parents(".campos_riscos").find(".overhide-input").addClass("hide").find("input").val("");
								$("td.campos_aval_"+linha).find(".overhide").addClass("hide").find("input[type=\'checkbox\']").prop("checked", false);
								$("td.campos_aval_"+linha).find(".avaliacao_comum").removeClass("hide");
							}

							$("#GrupoExposicaoRisco"+linha+"CodigoEfeitoCritico").val(dados.Risco.codigo_class_efeito);
							$("#GrupoExposicaoRisco"+linha+"CodigoRiscoAtributo").val(dados.Risco.codigo_risco_atributo);

							$(".detalhes_"+linha).show();
							$("#grupos_exposicao_riscos").find("div.detalhes_"+linha).find(".risco").show();

							$(".detalhes_"+linha).find("a.dialog_fontes_geradoras").attr("href", baseUrl + "fontes_geradoras/buscar_fonte_geradora/"+linha+"/"+dados.Risco.codigo);

							$(".detalhes_"+linha).find("a.dialog_epi").attr("href", baseUrl + "epi/buscar_epi/"+linha+"/"+dados.Risco.codigo);

							$(".detalhes_"+linha).find("a.dialog_epc").attr("href", baseUrl + "epc/buscar_epc/"+linha+"/"+dados.Risco.codigo);

							$(".detalhes_"+linha).find("a.dialog_efeitos_criticos").attr("href", baseUrl + "riscos_atributos_detalhes/buscar_efeitos_criticos/"+linha+"/"+dados.Risco.codigo);

							var qtd_linhas = $("#grupos_exposicao_riscos .campos_riscos").length;

							$.each($(".detalhes_"+linha).find("#avaliacao_"+linha+" td.tipo_"+linha+" input"), function(key, dados_aval) {
								if ($(dados_aval).is(":checked")) {
									if($(dados_aval).val()== 0){
										$(".campos_aval_"+linha).show();	
										$("#avaliacao_"+linha);
									}
									else{
										$(".campos_aval_"+linha).hide();
										$("#avaliacao_"+linha);	
									}
								}
								else{
									$(".campos_aval_"+linha).hide();
									$("#avaliacao_"+linha);
								}
							});

						});
						setup_mascaras();

					},
					complete: function(){
						$(carregando_detalhes).hide();
						$(carregando_detalhes).remove();
						$("#grupos_exposicao_riscos").find("div.detalhes_"+linha).find(".risco").show();
					},
				});	
			}

			function excluirFonteGeradora(elemento){
				$(elemento).parent().parent().remove();
			}

			function excluirEfeitoCritico(elemento){
				$(elemento).parent().parent().remove();
			}

			function excluirEpi(elemento){
				$(elemento).parent().parent().remove();
			}

			function excluirEpc(elemento){
				$(elemento).parent().parent().remove();
			}

			function excluirGrupoExposicaoRisco(elemento){
				$(elemento).parent().parent().remove();
			}

			function excluirGrupoExposicaoRiscoCadastrado(codigo_grupo_exposicao_risco, codigo_grupo_exposicao, elemento){	 				
				if (confirm("Deseja realmente excluir ?")){

					$("#excluir_risco_load_"+codigo_grupo_exposicao_risco).show();
					$(".btn_salvar").hide();
					$(".excluir_"+codigo_grupo_exposicao_risco).css("display", "none");

					$.ajax({
						type: "POST",        
						url: baseUrl + "grupos_exposicao_riscos/excluir/" + codigo_grupo_exposicao_risco +  "/" + codigo_grupo_exposicao + "/" + Math.random(),        
						dataType : "json",
						success : function(data){ 
							if(data == 1){
								$(elemento).parent().parent().remove();
							}
							else{
								alert("Não foi possível excluir, tente novamente.");
							}
						},
						complete: function(){
							$(".btn_salvar").show();							
						},
					}); 
				}
				return false;
			}

			function carregaCargo()  {
				var codigo_cargo = $("#GrupoExposicaoCodigoCargo").val();

				var dados_cargo = $("#dados_cargo");
				var carregando_cargo = $("#carregando_cargo");

				$.ajax({
					type: "POST",
					url: baseUrl + "cargos/busca_descricao_atividades/" + codigo_cargo + "/" + Math.random(),
					dataType: "json",
					beforeSend: function(){
						dados_cargo.hide();
						carregando_cargo.show();
					},
					success: function(data){
						$("#GrupoExposicaoDescricaoAtividade").val(data.Cargo.descricao_cargo);
					},
					complete: function(){
						dados_cargo.show();
						carregando_cargo.hide();
					},
				});
			}

			function carregaResultante(elemento){

				var linha = $(elemento).attr("id").substr(19,1);

				var v_tempo = $("#GrupoExposicaoRisco"+linha+"TempoExposicao").val();
				var v_intensidade = $("#GrupoExposicaoRisco"+linha+"Intensidade").val();

				var carregando_detalhes = $("#grupos_exposicao_riscos").find("#carregando_"+linha);

				var exibe_resultante = $("#exibe_resultante_"+linha);
				var carregando_resultante = $("#carregando_resultante_"+linha);

				var exibe_dano = $("#exibe_dano_"+linha);
				var carregando_dano = $("#carregando_dano_"+linha);

				var exibe_grau_risco = $("#exibe_grau_risco_"+linha);
				var carregando_grau_risco = $("#carregando_grau_risco_"+linha);

				if(v_tempo != "" && v_intensidade != ""){
					$.ajax({
						type: "POST",
						url: baseUrl + "grupos_exposicao/carrega_resultante/" + Math.random(),
						dataType: "json",
						data:{"tempo": v_tempo, "intensidade": v_intensidade},
						beforeSend: function(){
							exibe_resultante.hide();
							exibe_dano.hide();
							exibe_grau_risco.hide();
							carregando_resultante.show();
							carregando_dano.show();
							carregando_grau_risco.show();
						},
						success: function(data){
							$("#GrupoExposicaoRisco"+linha+"Resultante").children().remove();

							$.each(data, function(id, dados) {
								$("#GrupoExposicaoRisco"+linha+"Resultante").append("<option value=\'" + dados.codigo + "\'>" +dados.descricao + "</option>");
							});
						},
						complete: function(){
							exibe_resultante.show();
							exibe_dano.show();
							exibe_grau_risco.show();
							carregando_resultante.hide();
							carregando_dano.hide();
							carregando_grau_risco.hide();
						}
					});
				}

			}

			function carregaGrauRisco(elemento){

				var linha = $(elemento).attr("id").substr(19,1);
				var v_dano = $("#GrupoExposicaoRisco"+linha+"Dano").val();
				var v_resultante = $("#GrupoExposicaoRisco"+linha+"Resultante").val();

				var exibe_grau_risco = $("#exibe_grau_risco_"+linha);
				var carregando_grau_risco = $("#grupos_exposicao_riscos").find("#carregando_grau_risco_"+linha);
				if(v_dano != "" && v_resultante != ""){


					$.ajax({
						type: "POST",
						url: baseUrl + "grupos_exposicao/carrega_dano/" + Math.random(),
						dataType: "json",
						data:{"dano": v_dano, "resultante": v_resultante},
						beforeSend: function(){
							exibe_grau_risco.hide();
							carregando_grau_risco.show();
						},
						success: function(data){

							$("#GrupoExposicaoRisco"+linha+"GrauRisco").children().remove();

							$.each(data, function(id, dados) {
								$("#GrupoExposicaoRisco"+linha+"GrauRisco").append("<option value=\'" + dados.codigo + "\'>" +dados.descricao + "</option>");
							});
						},
						complete: function(){
							exibe_grau_risco.show();
							carregando_grau_risco.hide();
						}
					});
				}
			}

			function carregaFuncionario(){
				var codigo_setor = $("#ClienteSetorCodigoSetor").val();
				var codigo_cargo = $("#GrupoExposicaoCodigoCargo").val();
				var codigo_cliente = $("#ClienteSetorCodigoClienteAlocacao").val();
				$("#GrupoExposicaoCodigoFuncionario").html("<option value=\'\'>Selecione</option>");

				var campo_funcionario = $("#campo_funcionario");
				var carregando_funcionario = $("#carregando_funcionario");

				var codigo_funcionario_selected = "'.$codigo_funcionario.'";

				if(codigo_setor != "" && codigo_cargo != ""){
					$.ajax({
						type: "POST",
						url: baseUrl + "funcionarios/carrega_funcionario/" + Math.random(),
						dataType: "json",
						data:{"codigo_cliente": codigo_cliente, "codigo_setor": codigo_setor, "codigo_cargo": codigo_cargo },
						beforeSend: function(){
							campo_funcionario.hide();
							carregando_funcionario.show();
						},
						success: function(data){

							if(data != ""){

								var id_funcionario = $("#GrupoExposicaoCodigoFuncionario");

								id_funcionario.children().remove();
								id_funcionario.append("<option value=\'\'>Selecione</option>");

								$.each(data, function(id, dados) {

									if(id == codigo_funcionario_selected) {
										id_funcionario.append("<option value=\'" + id + "\' selected=\'selected\' >" + dados + "</option>");
									}
									else {
										id_funcionario.append("<option value=\'" + id + "\' >" + dados + "</option>");
									}

								});

								
							}
						},
						complete: function(){
							campo_funcionario.show();
							carregando_funcionario.hide();
						}
					});
				}
			}

			function carregaDescricaoAtivdades(){
				var codigo_grupo_homogeneo = $("#GrupoExposicaoCodigoGrupoHomogeneo").val();

				var setor_cargo_ghe = $(".setor_cargo_ghe");

				var carregando_cargo = $("#carregando_cargo");
				$("#descricao_cargo_ghe").find("tbody td").remove()

				$("#exibe_descricao_ghe").css("display", "none");
				$("#dados_cargo_ghe").css("display", "none");

				var resultado = "";
				if(codigo_grupo_homogeneo == ""){
					$("#descricao_cargo_ghe").find("tbody td").remove()
				}
				else{
					$.ajax({
						type: "POST",
						url: baseUrl + "grupos_homogeneos/retornaDetalhesGrupoHomogeneo/" + codigo_grupo_homogeneo + "/" + Math.random(),
						dataType: "json",	
						beforeSend: function(){
							carregando_cargo.show();
						},            
						success: function(data){

							if(data != 0){

								$("#nao_encontrado").css("display","none")
								$("#exibe_descricao_ghe").css("display", "block");

								$.each(data, function(id, dados) {
									resultado="<tr>";
									resultado +="<td>"+dados.Setor.descricao+"<input type=\'hidden\' id=\'GrupoHomDetalheCodigoSetorGhe\' value=\'"+dados.Setor.codigo+"\' name=\'data[GrupoHomDetalhe]["+id+"][codigo_setor_ghe]\'></td>";
									resultado +="<td>"+dados.Cargo.descricao+"<input type=\'hidden\' id=\'GrupoHomDetalheCodigoCargoGhe\' value=\'"+dados.Cargo.codigo+"\' name=\'data[GrupoHomDetalhe]["+id+"][codigo_cargo_ghe]\'></td>";
									resultado +="<td>";
									resultado +="<textarea id=\'GrupoHomDetalheDescricaoAtividadeGhe\' rows=\'6\' cols=\'30\' style=\'height: 40px; width: 98%; margin-bottom: 0px;\' name=\'data[GrupoHomDetalhe]["+id+"][descricao_atividade_ghe]\'>'.$this->data["GrupoHomDetalhe"]['+id+']['descricao_atividade_ghe'].'</textarea>";
									resultado +="<input type=\'hidden\' id=\'GrupoHomDetalheCodigo\' name=\'data[GrupoHomDetalhe]["+id+"][codigo]\' value=\'"+dados.GrupoHomDetalhe.codigo+"\'>";
									resultado +="</td>";
									resultado +="</tr>";

									$("#descricao_cargo_ghe").append(resultado);
								});
							}
							else{
								$("#nao_encontrado").css("display","block")
								$("#exibe_descricao_ghe").css("display", "none");
							}
						},
						complete: function(){
							carregando_cargo.hide();
						}
					});
				}
			}

			function exibe_descricao_cargos(){

				if($("#dados_cargo_ghe").is(":visible") == true){
					$("#dados_cargo_ghe").css("display", "none");
				}
				else{	
					$("#dados_cargo_ghe").css("display", "block");
				}
			}

			function mudar_recomentado(rec){

				//separa para pegar a linha e a sub_linha
				var id = rec.id;
				var split_1 = id.split("GrupoExposicaoRisco");
				var split_2 = split_1[2].split("Controle");
				var split_3 = split_2[0].split("Epi");

				var linha = split_1[1]; //pega a linha
				var sub_linha = split_3[1]; //pega a sub_linha

				//verifica se eh recomendado
				if($(rec).val() == "2") {
					//esconde os campos
					$(".esconder"+linha+"linha"+sub_linha).hide();
					$(".descricao"+linha+"linha"+sub_linha).removeClass("input-medium");
					$(".descricao"+linha+"linha"+sub_linha).addClass("input-xxlarge");
				} else {
					//apresenta os campos
					$(".esconder"+linha+"linha"+sub_linha).show();
					$(".descricao"+linha+"linha"+sub_linha).removeClass("input-xxlarge");
					$(".descricao"+linha+"linha"+sub_linha).addClass("input-medium");
					
				}//fim if

			}//fim muda_recomentado

	        $(document).on("click", ".substancias", function(){
			   $(this).select2();
			});

			function modal_visualizar_pcmso(codigo_unidade,setor,cargo,funcionario,mostra) {
				if(mostra) {

					var div = jQuery("div#modal_pcmso");
					bloquearDiv(div);
					div.load(baseUrl + "grupos_exposicao/modal_pcmso_pendente/" + codigo_unidade + "/" + setor + "/" + cargo + "/" + funcionario + "/" + Math.random());

					$("#modal_pcmso").css("z-index", "1050");
					$("#modal_pcmso").modal("show");

				} else {
					$(".modal").css("z-index", "-1");
					$("#modal_pcmso").modal("hide");
				}
			}


		');?>

<?php echo $javascript->link('pesquisa.js'); ?>
<?php echo $javascript->link('comum.js'); ?>
<?php echo $javascript->codeblock('setup_mascaras();'); ?>
<?php echo $javascript->codeblock('setup_datepicker();'); ?>

<?php if(!empty($visualizar_gge) && $visualizar_gge) : ?>
	<script type="text/javascript">
	jQuery(document).ready(function(){
		jQuery("input, select, textarea").attr("disabled", "disabled");
		jQuery(".icon-plus").parent().remove();
		jQuery(".icon-trash").remove();
		jQuery("input[type='submit']").remove();
		$("input").datepicker().datepicker('disable');
	});
	</script>
<?php endif; ?>