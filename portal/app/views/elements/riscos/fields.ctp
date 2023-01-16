<div class="well">
	<?php if(empty($this->edit_mode)): ?>
		<?php echo $this->BForm->hidden('codigo'); ?>
	<?php endif;  ?>
	
 	<div class="span4">
	 	<?php echo $this->BForm->input('codigo_rh', array('label' => 'Codigo do RH', 'class' => 'input-xlarge')); ?>
		<?php echo $this->BForm->input('nome_agente', array('label' => 'Nome Agente (*)', 'class' => 'input-xlarge')); ?>
		<?php echo $this->BForm->input('descricao_ingles', array('label' => 'Nome Agente em Inglês', 'class' => 'input-xlarge')); ?>
		<?php echo $this->BForm->input('codigo_grupo', array('options' => $array_grupo, 'empty' => '--- Selecione ---', 'label' => 'Grupo (*)', 'class' => 'input-xlarge')); ?>
		<?php echo $this->BForm->input('codigo_risco_atributo', array('label' => 'Meio de Propagação', 'class' => 'input-xlarge', 'options' => $array_exposicao, 'empty' => '--- Selecione ---')); ?>
		<?php echo $this->BForm->input('unidade_medida', array('label' => 'Unidade de Medida', 'class' => 'input-xlarge')); ?>		
		<?php echo $this->BForm->input('risco_caracterizado_por_altura', array('type'=>'checkbox','label' => false, 'div' => true, 'label' => 'Risco Caracterizado por Altura', 'class' => 'input-xlarge')); ?>
		<?php echo $this->BForm->input('risco_caracterizado_por_trabalho_confinado', array('type'=>'checkbox','label' => false, 'div' => true, 'label' => 'Risco Caracterizado por Trabalho Confinado', 'class' => 'input-xlarge')); ?>
		<?php echo $this->BForm->input('risco_caracterizado_por_ruido', array('type'=>'checkbox','label' => false, 'div' => true, 'label' => 'Risco Caracterizado por Ruído', 'class' => 'input-xlarge')); ?>
		<?php echo $this->BForm->input('risco_caracterizado_por_calor', array('type'=>'checkbox','label' => false, 'div' => true, 'label' => 'Risco Caracterizado por Calor', 'class' => 'input-xlarge')); ?>
		<?php echo $this->BForm->input('ausencia_de_risco', array('type'=>'checkbox','label' => false, 'div' => true, 'label' => 'Ausência de Risco', 'class' => 'input-xlarge')); ?>
 	</div>
 	
 	<div class="span4">
		<?php echo $this->BForm->input('usa_limite_tolerancia_no_ppra', array('type'=>'checkbox','label' => false, 'div' => true, 'label' => 'Usa Limite de Tolerência do PGR', 'class' => 'input-xlarge')); ?>
		<?php echo $this->BForm->input('considera_medicao_inferior_limite_tolerancia', array('type'=>'checkbox','label' => false, 'div' => true, 'label' => 'Considera Medição Inferior ao Limite de Tolerância', 'class' => 'input-xlarge')); ?>
		<?php echo $this->BForm->input('limite_tolerancia', array('label' => 'Limite de Tolerência', 'class' => 'input-xlarge')); ?>
		<?php echo $this->BForm->input('nivel_acao', array('label' => 'Nível de Ação', 'class' => 'input-xlarge')); ?>
		<?php echo $this->BForm->input('valor_teto', array('type'=>'checkbox','label' => false, 'div' => true, 'label' => 'Valor Teto', 'class' => 'input-xlarge')); ?>

		<div id="mostra_quimico" style="display: none;">	
			<?php echo $this->BForm->input('numero_cas', array('type'=>'text','label' => false, 'div' => true, 'label' => 'Número CAS', 'class' => 'input-xlarge')); ?>
		</div>		
		
		<div class="row"></div>
		<?php echo $this->BForm->input('faixa_conforto_de', array('type'=>'text','label' => false, 'div' => true, 'label' => 'Faixa Conforto (De)', 'class' => 'input-small')); ?>
		<?php echo $this->BForm->input('faixa_conforto_ate', array('type'=>'text','label' => false, 'div' => true, 'label' => 'Faixa Conforto (Até)', 'class' => 'input-small')); ?>
		<?php echo $this->BForm->input('quantidade_casas_decimais', array('type'=>'text','label' => false, 'div' => true, 'label' => 'Quantidade Casas Decimais', 'class' => 'input-large just-number')); ?>
		<?php echo $this->BForm->input('periodicidade_medicao', array('type'=>'text','label' => false, 'div' => true, 'label' => 'Periodicidade da Medição', 'class' => 'input-large just-number')); ?>
 	</div>
 	
 	<div class="span3">
		<?php echo $this->BForm->input('classificacao_efeito', array('options' => $array_efeito, 'empty' => '--- Selecione ---', 'label' => 'Classificação Efeito', 'class' => 'input-xlarge')); ?>
		<?php echo $this->BForm->input('copia_para_empresa_cliente', array('type'=>'checkbox','label' => false, 'div' => true, 'label' => 'Cópia para Empresa Cliente', 'class' => 'input-xlarge')); ?>
		<?php echo $this->BForm->input('pca', array('type'=>'checkbox','label' => false, 'div' => true, 'label' => 'PCA', 'class' => 'input-xlarge')); ?>
		<div class="row"></div>
		<?php echo $this->BForm->input('obs_aso_apto', array('type'=>'textarea','label' => false, 'div' => false, 'label' => 'Observação no ASO Apto', 'class' => 'input-xlarge')); ?>
		<?php echo $this->BForm->input('obs_aso_inapto', array('type'=>'textarea','label' => false, 'div' => false, 'label' => 'Observação no ASO Inapto', 'class' => 'input-xlarge')); ?>
 	</div> 	
 	
 	<div class="clear"></div>
</div>
<div class="well">
	<h4>ESOCIAL</h4> <br />	
	<?php echo $this->BForm->input('codigo_agente_nocivo_esocial', array('label' => 'Código Agente Nocivo (eSocial - Tabela 21)', 'class' => 'input-xlarge')); ?>
	<?php echo $this->BForm->input('fator_risco_esocial', array('label' => 'Fator Risco (eSocial - Tabela 22)', 'class' => 'input-xlarge')); ?>
	<?php echo $this->BForm->input('aponsentadoria_especial_inss_esocial', array('label' => 'Aposentadoria Especial INSS (eSocial - Tabela 23)', 'class' => 'input-xlarge')); ?>
	<?php echo $this->BForm->input('codigo_esocial_24', array('options' => $esocial_tabela_24, 'empty' => '--- Selecione ---', 'label' => 'Agentes Nocivos e Atividades (eSocial - Tabela 24)', 'class' => 'input-xxlarge bselect2')); ?>
</div>

<div class="well">
	<h4>MEDIÇÃO</h4> <br />
	<?php echo $this->BForm->input('usa_nen', array('type'=>'checkbox','label' => false, 'div' => true, 'label' => 'Usa NEN', 'class' => 'input-xlarge')); ?>
	<?php echo $this->BForm->input('nbr_iso', array('type'=>'checkbox','label' => false, 'div' => true, 'label' => 'NBR ISO/CIE 8995-1:2013', 'class' => 'input-xlarge')); ?>
	<?php echo $this->BForm->input('usa_nos', array('type'=>'checkbox','label' => false, 'div' => true, 'label' => 'Usa NOS', 'class' => 'input-xlarge')); ?>
	<?php echo $this->BForm->input('usa_ibutg', array('type'=>'checkbox','label' => false, 'div' => true, 'label' => 'Usa Ibutg', 'class' => 'input-xlarge')); ?>
	<?php echo $this->BForm->input('usa_limite_variavel', array('type'=>'checkbox','label' => false, 'div' => true, 'label' => 'Usa Limite Variável', 'class' => 'input-xlarge')); ?>
	<?php echo $this->BForm->input('usa_silica', array('type'=>'checkbox','label' => false, 'div' => true, 'label' => 'Usa Silica', 'class' => 'input-xlarge', 'onchange' => 'mostra_formula(this);')); ?>
	<div class="row"></div>
	<?php echo $this->BForm->input('formula_silica', array('options' => $array_formula, 'empty' => '--- Selecione ---', 'label' => false, 'class' => 'input-xlarge', 'style' => 'display:none;', 'id' => 'formula_silica')); ?>
	
	<div class="clear"></div>
</div>

<div class="well">
 	<h4>RELATÓRIOS</h4> <br />
	<?php echo $this->BForm->input('aso', array('type'=>'checkbox','label' => false, 'div' => true, 'label' => 'Aso', 'class' => 'input-xlarge')); ?>
	<?php echo $this->BForm->input('convocacao', array('type'=>'checkbox','label' => false, 'div' => true, 'label' => 'Convocação/Pedido de Exame', 'class' => 'input-xlarge')); ?>
	<?php echo $this->BForm->input('nocivo_ppp', array('type'=>'checkbox','label' => false, 'div' => true, 'label' => 'Nocivo - PPP', 'class' => 'input-xlarge')); ?>
	<?php echo $this->BForm->input('ordem_servico', array('type'=>'checkbox','label' => false, 'div' => true, 'label' => 'Ordem de Serviço', 'class' => 'input-xlarge')); ?>
	<?php echo $this->BForm->input('pcmso', array('type'=>'checkbox','label' => false, 'div' => true, 'label' => 'PCMSO', 'class' => 'input-xlarge')); ?>
	<?php echo $this->BForm->input('ppra', array('type'=>'checkbox','label' => false, 'div' => true, 'label' => 'PGR', 'class' => 'input-xlarge', 'onchange' => 'mostra_formula(this);')); ?>
	<div class="row"></div>
	<?php echo $this->BForm->input('observacao', array('type'=>'textarea','label' => false, 'div' => false, 'label' => 'Observação', 'class' => 'input-xlarge')); ?>
	<?php echo $this->BForm->input('orientacoes_medicas', array('type'=>'textarea','label' => false, 'div' => false, 'label' => 'Orientações Médicas', 'class' => 'input-xlarge')); ?>
	
	<div class="clear"></div>
</div>

<div class="well">
	<h4>PERIODICIDADE</h4> (Quando o valor da última Medição for)<br />
	<div id="periodicidade">
		<?php if(!isset($this->data['Periodicidade']) || !count($this->data['Periodicidade'])) : ?>
			<div class='row-fluid inline'>
				<?php echo $this->BForm->input('Periodicidade.0.de', array('type'=>'text','label' => false, 'div' => true, 'placeholder' => 'De', 'label' => 'De', 'class' => 'input-xsmall just-number')); ?>
				<?php echo $this->BForm->input('Periodicidade.0.ate', array('type'=>'text','label' => false, 'div' => true, 'placeholder' => 'Até', 'label' => 'Até', 'class' => 'input-xsmall just-number')); ?>
				<?php echo $this->BForm->input('Periodicidade.0.meses', array('type'=>'text','label' => false, 'div' => true, 'placeholder' => 'A cada (meses)', 'label' => 'A cada (meses)', 'class' => 'input-xsmall just-number')); ?> 
			</div>		
		<?php else : ?>
			<?php foreach($this->data['Periodicidade'] as $key => $campo) : ?>
				<div class='row-fluid inline'>
					<?php echo $this->BForm->input('Periodicidade.' . $key . '.de', array('type'=>'text','label' => false, 'div' => true, 'placeholder' => 'De', 'label' => 'De', 'class' => 'input-xsmall just-number')); ?>
					<?php echo $this->BForm->input('Periodicidade.' . $key . '.ate', array('type'=>'text','label' => false, 'div' => true, 'placeholder' => 'Até', 'label' => 'Até', 'class' => 'input-xsmall just-number')); ?>
					<?php echo $this->BForm->input('Periodicidade.' . $key . '.meses', array('type'=>'text','label' => false, 'div' => true, 'placeholder' => 'A cada (meses)', 'label' => 'A cada (meses)', 'class' => 'input-xsmall just-number')); ?> 
				</div>			
			<?php endforeach; ?>
		<?php endif; ?>
	</div>
	<a href="javascript:void(0);" onclick="addPeriodicidade();" class="btn btn-warning btn-sm right">
    	<span class="glyphicon glyphicon glyphicon-plus"></span> Incluir Periodo
	</a>	
</div>

<div class='form-actions'>
	 <?php echo $this->BForm->submit('Salvar', array('div' => false, 'class' => 'btn btn-primary')); ?>
	 <?= $html->link('Voltar', array('action' => 'index'), array('class' => 'btn')); ?>
</div>

<div id="modelo_periodicidade">
	<div class="row-fluid inline" style="display: none;">
		<?php echo $this->BForm->input('Periodicidade.X.de', array('type'=>'text','label' => false, 'div' => true, 'placeholder' => 'De', 'label' => '', 'class' => 'input-xsmall just-number')); ?> 
		<?php echo $this->BForm->input('Periodicidade.X.ate', array('type'=>'text','label' => false, 'div' => true, 'placeholder' => 'Até', 'label' => '', 'class' => 'input-xsmall just-number')); ?>
		<?php echo $this->BForm->input('Periodicidade.X.meses', array('type'=>'text','label' => false, 'div' => true, 'placeholder' => 'A cada (meses)', 'label' => '', 'class' => 'input-xsmall just-number')); ?>
	</div>
</div>

<?php echo $this->Javascript->codeBlock('
	$(document).ready(function(){
		setup_mascaras();

		$(".bselect2").select2();
		
		$("select[name=\"data[Risco][codigo_grupo]\"]").change(function() {
			if($(this).val() == "2")
				$("#mostra_quimico").show();
			else 
				$("#mostra_quimico").hide();
		});
	});
	
	function mostra_formula(element) {
	
		if($(element).is(":checked") )
			$("#formula_silica").show();
		else
			$("#formula_silica").hide();
	}
	
	function addPeriodicidade() {
		var id = $("#periodicidade .row-fluid").length;
		
		$("#modelo_periodicidade .row-fluid").clone().appendTo("#periodicidade").show().find("input").each(function(index, element){
			$(element).attr("name", $(element).attr("name").replace("X", id));
	    });
	}
	
'); ?>