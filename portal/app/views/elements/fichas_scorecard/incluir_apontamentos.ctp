<?php echo $this->BForm->create('FichaStatusCriterios', array('type' => 'post' ,'url' => array('controller' => 'fichas_status_criterios','action' => 'salvar_artigo_criminal_pesquisa')));?>

<?php //debug($this->data['FichaStatusCriterios']); ?>

<div>
	<div class="row-fluid inline parent">
	      <h5>Ocorrência</h5> 
	</div>
	<div class="row-fluid inline">
	          
	          <?php echo $this->BForm->hidden('codigo_altera'); ?>
	          <?php echo $this->BForm->hidden('codigo_ficha',array('value'=>$codigo_ficha)); ?>
	          <?php echo $this->BForm->input('numero_artigo',array('class' => 'input-xlarge','label'=>'Número Artigo', "options" => $tipoartigocriminal)); ?>
	          <?php echo $this->BForm->input('data_ocorrencia',array('class' => 'input-small data','type'=>'text', 'label' => 'Data do Fato')); ?>  
	         

	          <?php echo $this->BForm->input('dp', array('class' => 'input-small just-number', 'label' => 'DP')); ?> 

	          <?php echo $this->BForm->input('local', array('class' => 'input-xlarge', 'label' => 'Local')); ?> 

	                       
              <?php echo $this->BForm->input('cidade_origem', array('class' => 'input-large ui-autocomplete-input', 'placeholder' => 'Informe uma Cidade', 'empty' => 'Cidade', 'label' => 'Cidade', 'for' =>'FichaScorecardCodigoEnderecoCidadeCargaOrigem')) ?>
	          <?php echo $this->BForm->input('codigo_endereco_cidade_carga_origem',    array('class' => 'input-large', 'type' => 'hidden', 'empty' => 'Cidade', 'label' => false)) ?> 

	</div>

	<div class="row-fluid inline parent">
	        <h5>Registro</h5> 
	       
	</div>
	<div class="row-fluid inline">
	<?php echo $this->BForm->input('inquerito', array('class' => 'input-xlarge', 'label' => 'Inquérito')); ?> 
	<?php echo $this->BForm->input('data_inquerito', array('class' => 'input-small data', 'label' => 'Data Inquérito')); ?> 
	<?php echo $this->BForm->input('processo', array('class' => 'input-small', 'label' => 'Processo')); ?> 
	<?php echo $this->BForm->input('data_processo', array('class' => 'input-small data', 'label' => 'Data Processo')); ?> 
	</div>

	<div class="row-fluid inline parent">
	        <h5>Local</h5> 
	</div>
	<div class="row-fluid inline">
	
    <?php echo $this->BForm->input('codigo_instituicao',array('class' => 'input-xxlarge','label'=>'Jurisdição', "options" => $tipo_jurisdicao)); ?>

	
	</div>

	<div class="row-fluid inline parent">
	        <h5>Prestador</h5> 
	</div>
	<div class="row-fluid inline">
	
	<?php echo $this->BForm->input('codigo_prestador',array('label'=>'Prestador', "options" => $tipo_prestadores)); ?>

    <?php echo $this->BForm->input('codigo_situacao',array('class' => 'input-xxlarge','label'=>'Situação', "options" => $tiposituacaoprocesso)); ?>

	</div>
	<div class="row-fluid inline">
	 <?php echo $this->BForm->input('ProfissionalNegativacao.observacao',array('class' => 'input-xxlarge ','type'=>'textarea','cols'=>'90', 'label' => 'Observação')); ?>   
	</div>

	<div class="form-actions">
	  <?php echo $this->BForm->submit('Salvar', array('div' => false, 'class' => 'btn btn-primary')); ?>
	  <?= $html->link('Voltar', array('action' => 'index'), array('class' => 'btn')); ?>

	</div>    
	
	<?php echo $this->BForm->end(); ?>


</div>


  <?php echo $this->Javascript->codeBlock('
       jQuery(document).ready(function(){
           setup_datepicker();
        });', false); 
    ?>    


    <?php echo $this->Javascript->codeBlock("
		  	$(function() {
			  $('.ui-autocomplete-input-origem').autocomplete({        
			  source: baseUrl + 'enderecos/autocompletar/',
			  focus: function(){return false;},
			  minLength: 3,
			  select: function( event, ui ) {		      	
			    codigo_cidade   = ui.item.value;
			    cidade_nome     = ui.item.label;
			    codigo_estado   = ui.item.uf_value;
			  	$(this).val( cidade_nome );
			  	$('#FichaStatusCriteriosCodigoEnderecoCidadeCargaOrigem').value =codigo_cidade;
			    return false;
			  }});
			});
		");?>