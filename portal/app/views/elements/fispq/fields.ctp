<div class="well">
	<div class="span4">
		<div class="row-fluid inline">
			 <?php echo $this->BForm->input('nome_produto', array('type' => 'text', 'label' => 'Nome Produto (*)', 'class' => 'input-xlarge')); ?>
		</div>
		<div class="row-fluid inline">		 
			 <?php echo $this->BForm->input('data_publicacao', array('type' => 'text', 'label' => 'Data Publicação', 'class' => 'input-large data')); ?>
		</div>
		<div class="row-fluid inline">		 
			 <?php echo $this->BForm->input('numero_documento', array('type' => 'text', 'label' => 'Número Produto', 'class' => 'input-xlarge')); ?>
		</div>
		<div class="row-fluid inline">		 
			 <?php echo $this->BForm->input('versao_revisao', array('type' => 'text', 'label' => 'Versão Revisão', 'class' => 'input-xlarge')); ?>
		</div>
		<div class="row-fluid inline">		 
			 <?php echo $this->BForm->input('numero_cas', array('type' => 'text', 'label' => 'Número CAS', 'class' => 'input-xlarge')); ?>
		</div>
		<div class="row-fluid inline">		 
			 <?php echo $this->BForm->input('identificacao_produto', array('type' => 'textarea', 'label' => 'Identificação Produto', 'class' => 'input-xlarge')); ?>
		</div>
		<div class="row-fluid inline">		 
			 <?php echo $this->BForm->input('link_fispq', array('type' => 'textarea', 'label' => 'Link Fispq', 'class' => 'input-xlarge')); ?>
		</div>	
		<div class="row-fluid inline">		 
			<?php echo $this->BForm->input('codigo_fornecedor', array('options' => $array_unidade, 'empty' => '--- Selecione ---', 'label' => 'Unidade', 'class' => 'input-xlarge')); ?>
		</div>
		<div class="row-fluid inline">		
			<?php echo $this->BForm->input('dados_fornecedor', array('type' => 'textarea', 'label' => 'Dados Fornecedor', 'class' => 'input-xlarge')); ?>
		</div>
		<div class="row-fluid inline">		
			<?php echo $this->BForm->input('identificacao_perigos', array('type' => 'textarea', 'label' => 'Identificação de Perigos', 'class' => 'input-xlarge')); ?>
		</div>					
	</div>
	
	<div class="span4">
		<div class="row-fluid inline">		
			<?php echo $this->BForm->input('medidas_primeiros_socorros', array('type' => 'textarea', 'label' => 'Medidas de Primeiros Socorros', 'class' => 'input-xlarge')); ?>
		</div>
		<div class="row-fluid inline">		
			<?php echo $this->BForm->input('medidas_combate_incendio', array('type' => 'textarea', 'label' => 'Medidas de Combate de Incêndio', 'class' => 'input-xlarge')); ?>
		</div>
		<div class="row-fluid inline">		
			<?php echo $this->BForm->input('medidas_controle_derramamento_vazamento', array('type' => 'textarea', 'label' => 'Medidas Controla Derramamento / Vazamento', 'class' => 'input-xlarge')); ?>
		</div>
		<div class="row-fluid inline">		
			<?php echo $this->BForm->input('manuseio_armazenamento', array('type' => 'textarea', 'label' => 'Manuseio de Armazenamento', 'class' => 'input-xlarge')); ?>
		</div>
		<div class="row-fluid inline">		
			<?php echo $this->BForm->input('controle_exposicao', array('type' => 'textarea', 'label' => 'Controle de Exposição', 'class' => 'input-xlarge')); ?>
		</div>
		<div class="row-fluid inline">		
			<?php echo $this->BForm->input('propriedades_fisico_quimicas', array('type' => 'textarea', 'label' => 'Propriedades Físico Químicas', 'class' => 'input-xlarge')); ?>
		</div>		
	</div>
	
	<div class="span3">
		<div class="row-fluid inline">		
			<?php echo $this->BForm->input('estabilidade_reatividade', array('type' => 'textarea', 'label' => 'Estabilidade Reatividade', 'class' => 'input-xlarge')); ?>
		</div>
		<div class="row-fluid inline">		
			<?php echo $this->BForm->input('informacoes_toxicologicas', array('type' => 'textarea', 'label' => 'Informações Toxicologicas', 'class' => 'input-xlarge')); ?>
		</div>	
		<div class="row-fluid inline">		
			<?php echo $this->BForm->input('informacoes_ecologicas', array('type' => 'textarea', 'label' => 'Informações Ecológicas', 'class' => 'input-xlarge')); ?>
		</div>
		<div class="row-fluid inline">		
			<?php echo $this->BForm->input('consideracao_tratamento', array('type' => 'textarea', 'label' => 'Consideração de Tratamento', 'class' => 'input-xlarge')); ?>
		</div>
		<div class="row-fluid inline">		
			<?php echo $this->BForm->input('informacoes_transporte', array('type' => 'textarea', 'label' => 'Informações de Transporte', 'class' => 'input-xlarge')); ?>
		</div>
		<div class="row-fluid inline">		
			<?php echo $this->BForm->input('regulamento', array('type' => 'textarea', 'label' => 'Regulamento', 'class' => 'input-xlarge')); ?>
		</div>
		<div class="row-fluid inline">	
			<?php echo $this->BForm->input('outras_informacoes', array('type' => 'textarea', 'label' => 'Outras Informações', 'class' => 'input-xlarge')); ?>
		</div>
	</div>	
	<div class="clear"></div>
</div>

	<div class="well">
		<div class="row-fluid inline">
			<h3> Riscos </h3>
			<div class="span5">
				<?php echo $this->BForm->input('riscos_opcional', array('options' => $array_opcoes, 'multiple' => 'multiple', 'size' => '15', 'id' => 'riscos_opcional', 'label' => 'Riscos', 'class' => 'multiselect','style' => 'width: 400px;')); ?>
			</div>
			<div class="span1">
				<br /><br /><br /><br /><br />
				<img src="/portal/img/associa.gif" style="cursor: pointer" onclick="associa(this, '#riscos_opcional', '#riscos_selecionados');"/>
				<br />
				<img src="/portal/img/desassocia.gif" style="cursor: pointer" onclick="desassocia(this, '#riscos_opcional', '#riscos_selecionados');"/>
			</div>
			<div class="span4">
				<?php echo $this->BForm->input('riscos_selecionados', array('options' => $array_selecionados, 'multiple' => 'multiple', 'size' => '15', 'id' => 'riscos_selecionados', 'label' => 'Selecionados', 'class' => 'multiselect','style' => 'width: 35400px0px;')); ?>		
			</div>
		</div>
		<div class="row-fluid inline">
			
			<h3> Empresas que acessam a FISPQ</h3>
			<div class="span5">
				<?php echo $this->BForm->input('empresas_opcao', array('options' => $array_empresas, 'multiple' => 'multiple', 'size' => '15', 'id' => 'empresas_opcao', 'label' => 'Empresas', 'class' => 'input-xlarge multiselect','style' => 'width: 400px;')); ?>
			</div>
			<div class="span1">
				<br /><br /><br /><br /><br />
				<img src="/portal/img/associa.gif" style="cursor: pointer" onclick="associa(this, '#empresas_opcao', '#empresas_selecionadas');"/>
				<br />
				<img src="/portal/img/desassocia.gif" style="cursor: pointer" onclick="desassocia(this, '#empresas_opcao', '#empresas_selecionadas');"/>
			</div>
			<div class="span4">
				<?php echo $this->BForm->input('empresas_que_acessam', array('options' => $empresas_que_acessam, 'multiple' => 'multiple', 'size' => '15', 'id' => 'empresas_selecionadas', 'label' => 'Empresas Selecionadas', 'class' => 'multiselect','style' => 'width: 400px;')); ?>		
			</div>
		</div>		
				
		<div class="clear"></div>	
	</div>
	
<div class='form-actions'>
	 <?php // echo $this->BForm->submit('Salvar', array('div' => false, 'class' => 'btn btn-primary')); ?>
	 
	 <a href="javascript:void(0);" class="btn btn-primary" onclick="seleciona_riscos(); seleciona_empresas();">Salvar</a>
	 <?= $html->link('Voltar', array('controller' => 'fispq', 'action' => 'index'), array('class' => 'btn')); ?>
</div>

<?php echo $this->Javascript->codeBlock('
	$(document).ready(function(){
		setup_mascaras();
		setup_datepicker();
	});
	
	function seleciona_riscos() {
		$("#riscos_selecionados option").prop("selected", "true");
	}
	
	function seleciona_empresas() {
		$("#empresas_selecionadas option").prop("selected", "true");
		
		if ( $("#FispqIncluirForm").length ) {
			$("#FispqIncluirForm").submit();
		} else {
			$("#FispqEditarForm").submit();
		}
	}	
	
	function associa(element, opcao, selecionado) {
		$(opcao + " :selected").each(function(i, selected) {
			$(selecionado).prepend("<option value=\""+ $(selected).val() +"\">" + $(selected).html() + "</option>");
			$(selected).remove();
		});
		
		$(selecionado + " option").prop("selected", "true");
	}
	
	function desassocia(element, opcao, selecionado) {
		$(selecionado + " :selected").each(function(i, selected){
			$(opcao).prepend("<option value=\""+ $(selected).val() +"\">" + $(selected).html() + "</option>");
			$(selected).remove();
		});
		
		$(selecionado + " option").prop("selected", "true");
	}		
'); ?>