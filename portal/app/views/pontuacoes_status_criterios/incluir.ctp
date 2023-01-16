</br></br>
<div class="criterios form">
	<?php echo $this->BForm->create('PontuacoesStatusCriterio', array('autocomplete' => 'off', 'url' => array('controller' => 'pontuacoes_status_criterios', 'action' => 'incluir'))); ?>		
	<div class="well">
	 	<div class='row-fluid inline'>
			<?php echo $this->Buonny->input_codigo_cliente($this, 'codigo_cliente', 'Cliente', false,'PontuacoesStatusCriterio') ?>			
			<?php echo $this->BForm->input('codigo_seguradora',array('label' => false, 'empty' => 'Selecione uma Seguradora','options' => $list_seguradora,'class'=>'input-large'));?>
		</div>
    	<div class='row-fluid inline'>
			<?php echo $this->BForm->input('codigo_criterio',array('empty' => 'Selecione um Critério','options' => $criterios,'class'=>'input-large','label' => 'Critério'));?>     
			<?php echo $this->BForm->input('codigo_status_criterio',array('empty' => 'Selecione um Status','options' => $status,'class'=>'input-large','label' => 'Status'));?>		
			<span style="display:none" id = 'qtd'>
				<?php echo $this->BForm->input('qtd_ate',array('placeholder'=>'Qtd Até','maxlength'=>'false','class'=>'input-mini'));?>
			</span>	
			<?php echo $this->BForm->input('pontos',array('placeholder'=>'Pontos','class' => 'input-mini numeric','maxlength'=>'false'));?>
			<!--
			<?php echo $this->BForm->input('insuficiente',array('class' => 'input-medium ', 'label'=>'Insuficiente', 'type'=>'select', 'options'=>array(1=>'Sim', 0=>'Não'), 'selected'=>0) );?>
			<?php echo $this->BForm->input('divergente',array('class' => 'input-medium ', 'label'=>'Divergente', 'type'=>'select', 'options'=>array(1=>'Sim', 0=>'Não'), 'selected'=>0) );?>
			-->
			<div class="control-group input checkbox">
				<label>&nbsp</label>
				<?php echo $this->BForm->input('insuficiente',array('class' => 'input-mini numeric', 'label'=>'Insuficiente', 'type'=>'checkbox'));?>
				<?php echo $this->BForm->input('divergente',array('class' => 'input-mini numeric', 'label'=>'Divergente', 'type'=>'checkbox'));?>					
				<?php echo $this->BForm->input('opcional',array('class' => 'input-mini numeric ', 'label'=>'Opcional', 'type'=>'checkbox'));?>
			</div>			
		</div>

		<div class='row-fluid inline' id="checkboxes">
			<span class="label label-info">Tipo profissional</span>
			<span class='pull-right'>
				<?= $html->link('Desmarcar todas', 'javascript:void(0)', array('onclick' => 'desmarcarTodos("checkboxes")')) ?>
				<?= $html->link('Marcar todas', 'javascript:void(0)', array('onclick' => 'marcarTodos("checkboxes")')) ?>
			</span>
			<?php echo $this->BForm->input('PontuacaoSCProfissional.codigo_profissional_tipo', 
					array('label'=>false, 
					'options'=>$profissionais_tipos, 
					'multiple'=>'checkbox', 
					'class' => 'checkbox inline input-xlarge')); ?>
		</div>
	</div>
	<div class='form-actions'>
	    <?php echo $this->BForm->submit('Salvar', array('div' => false, 'class' => 'btn btn-primary')); ?>
	    <?= $html->link('Voltar', array('action' => 'index'), array('class' => 'btn')); ?>
	</div>
	<?php echo $this->BForm->end(); ?>
</div>
<?php echo $this->Javascript->codeBlock('
	 jQuery(document).ready(function(){
   		setup_mascaras();
   	});', 
    false
  ); 
?>
<?php echo $this->Javascript->codeBlock("	
		$(function() {
			carregarConsultaQtdTexto('#PontuacoesStatusCriterioCodigoCriterio','#qtd');			
			$('div#checkboxes').find('input[type=checkbox]').prop('checked', true);
			function carregarConsultaQtdTexto(element_cod,element_qtd){
				var retorno = $(element_qtd);
				if($(element_cod).val()){
					$.ajax({
						url: baseUrl + 'StatusCriterios/lista_qtd_texto/' + $(element_cod).val() + '/' + Math.random(),
						dataType: 'json',
						success: function(data){
							if(data){
								if(data[0].Criterio.controla_qtd)
									retorno.show();
								else
									retorno.hide();
							} else {
								retorno.hide();
							}
						},
					});					
				} else {
					retorno.hide();
				}
			}

			$('#PontuacoesStatusCriterioCodigoCriterio').change(function(){
				carregarStatusCriterio('#PontuacoesStatusCriterioCodigoCriterio','#PontuacoesStatusCriterioCodigoStatusCriterio','#qtd'); 
				carregarConsultaQtdTexto('#PontuacoesStatusCriterioCodigoCriterio','#qtd');
				function carregarConsultaQtdTexto(element_cod,element_qtd){
					var retorno = $(element_qtd);
					if($(element_cod).val()){
						$.ajax({
							url: baseUrl + 'StatusCriterios/lista_qtd_texto/' + $(element_cod).val() + '/' + Math.random(),
							dataType: 'json',
							success: function(data){
								if(data){
									if(data[0].Criterio.controla_qtd)
										retorno.show();
									else
										retorno.hide();
								} else {
									retorno.hide();
								}
							},
						});						
					} else {
						retorno.hide();
					}
				}
			});
		});", 
	false);
?>