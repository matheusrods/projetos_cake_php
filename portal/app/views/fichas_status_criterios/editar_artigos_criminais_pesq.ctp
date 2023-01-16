<?php echo $this->Buonny->link_css('fichas_scorecard'); ?>
<ul class="nav nav-tabs">
	<li ><a href="#pesquisa" data-toggle="tab">Pesquisa</a></li>
	<li><a href="#historico" data-toggle="tab">Histórico Ocorrências</a></li>
	<li><a href="#historicocpf" data-toggle="tab">Histórico Profissional</a></li>
	<li><a href="#historicorma" data-toggle="tab">Histórico RMA</a></li>  
	<li><a href="#historicosinistro" data-toggle="tab">Sinistro</a></li> 
	<li><a href="#historicosocioeco" data-toggle="tab">Situação Sócio Economica</a></li>
    <li class="active"><a href="#historicoapontamentos" data-toggle="tab">Histórico Apontamentos</a></li>
	<li><a href="#ficha" data-toggle="tab">Dados da Ficha</a></li>

	
</ul>
<div class="tab-content tabbable">
	<div class="tab-pane" id="pesquisa" style="width: 100%; height:100%; align: center; overflow-x:hidden; overflow-y:auto"> 
		<?php echo $this->BForm->create('FichaStatusCriterio', array('url' => array('controller' => 'fichas_status_criterios', 'action' => 'editar', $this->passedArgs[0]))); ?>

		<?php echo $this->element('/fichas_status_criterios/cabecalho_ficha'); ?>		
		<?php if(!empty($observacao_supervisor) && !isset($observacao_supervisor)): ?>
			<div class="alert alert-block alert-info">
				<h5>Observação do supervisor</h5>
				<?php echo $observacao_supervisor; ?>
			</div>
		<?php endif; ?>		
		<div id='perguntas'>
			<?php echo $this->element('/fichas_status_criterios/lista_criterios', array('disabled'=>false)); ?>
		</div>		
		<div class='form-actions'>
			<?php echo $this->BForm->submit('Concluir Pesquisa', array('div' => false, 'id'=>'concluir','class' => 'btn btn-primary')); ?>
			<?php  //echo $this->BForm->submit('Deixar Pendente', array('div' => false, 'id'=>'pendente','class' => 'btn btn-danger', 'name'=>'pendente')); ?>

			<?php echo $this->Html->link('Salvar Pendente', array('controller'=>'fichas_status_criterios','action' => 'pendente',$this->passedArgs[0]), array('class' => 'btn btn-danger', 'id'=>'pendente')); ?>

			<?php //echo $html->link('Voltar', array('controller' => 'fichas_scorecard', 'action' => 'fichas_a_pesquisar'), array('class' => 'btn','id'=>'button')); ?>
		</div>
		<?php echo $this->BForm->end(); ?>
		<div class='dialog' style="display:none">
			<?php echo $this->BForm->input('FichaStatusCriterio.observacaodialog', array('div'=>false, 'maxlength' => 256,'class' => 'input-large', 'placeholder' => false, 'label' =>false , 'type' => 'textarea', 'style' =>'width:97%; height:150px; line-height:20px; overflow:hidden;', 'id' => 'textareaobs')) ?>this->data)
		</div>
		<?php echo $this->BForm->end(); ?>
	</div>

	<!--
	<div class="tab-pane" id="contatos">
	   <?php //echo $this->element('/fichas_scorecard/formulario_ficha'); ?>
	</div>
    -->
    
	<div class="tab-pane" id="historicocpf">
	   <?php echo $this->element('/fichas_scorecard/historico_profissional'); ?>
	</div> 
	<!--<div class="tab-pane" id="#historicorma">teste rma
	   <?php //echo $this->element('/fichas_scorecard/historico_rma'); ?>
	</div> 
	<div class="tab-pane" id="#historicoapontamentos">teste apontamentos tera que ser feito o insert
	   <?php //echo $this->element('/fichas_scorecard/historico_rma'); ?>
	</div> -->
	
	<!--<div class="tab-pane" id="#historicosocioeco">teste socio economico
	   <?php //echo $this->element('/fichas_scorecard/historico_rma'); ?>
	</div> -->
	<div class="tab-pane" id="historicorma" >
        
      <?php echo $this->element('fichas_scorecard/historico_rma'); ?>
	</div>	
	<div class="tab-pane" id="historicosinistro">
	   <?php echo $this->element('/fichas_scorecard/historico_sinistro'); ?>
	</div> 
	<div class="tab-pane" id="historicosocioeco" >
       <?php echo $this->element('/fichas_scorecard/historico_socioeco'); ?>
	</div>	
	<div class="tab-pane" id="historico">
	   <?php echo $this->element('/fichas_scorecard/historico_ocorrencia'); ?>
	</div> 
	<div class="tab-pane active" id="historicoapontamentos">
	   <?php echo $this->element('/fichas_scorecard/incluir_apontamentos'); ?>
	</div> 
	<div class="tab-pane" id="ficha">
		<?php echo $this->element('/fichas_scorecard/formulario_ficha'); ?>
	</div>
	
	 
</div>

<?php $this->addScript($this->Buonny->link_js('solicitacoes_monitoramento')) ?>
<?php $this->addScript($this->Buonny->link_js('fichas_scorecard')) ?>
<?php echo $this->Javascript->codeBlock("
	$(document).ready(function() {
		setup_desabilita_formulario_pesquisa();
		setup_exibir_observacao_criterio();	
		setup_sinalizar_criterios_insuficientes();
		sinalizar_criterios_insuficientes();
		$('#FichaStatusCriterioEditarForm').submit(function() {
			var retorno = true;	
			$('#pesquisa select').each(function(){ 
				if (!$(this).val() && $(this).parent().parent().find('input[id$=\"Opcional\"]').val() == 0){
					$(this).parent().addClass('error');
					retorno = false;
				}
			}); 
			if(!retorno){
				flashMessage('Responda todos os critérios obrigatórios', 'error');
			}else{
				if($('#pesquisa div.select.warning').length > 0){
					alert('A ficha será salva como incompleta, pois há critérios obrigatórios definidos como insufiente.')
				}
			}
			return retorno;
		});
		$('div#lista_contatos :input').removeAttr('disabled', 'disabled');
		$('div#btn_salvar :input').removeAttr('disabled', 'disabled');
		$('.btn-limpar:button').hide();
		$('#btn-profissional').hide();
		$('#btn-proprietario0').hide();
		$('#btn-proprietario1').hide();
		$('#btn-proprietario2').hide();
		$('.remove-contato-profissional').hide();		
	});
");
?>
 