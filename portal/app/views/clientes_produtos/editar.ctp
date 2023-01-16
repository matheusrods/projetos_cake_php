<div class="well">
	<strong>Código do cliente:</strong>
	<?php echo $codigo_cliente ?>
	<strong>Produto:</strong>
	<?php echo $this->data['Produto']['descricao']; ?>
	<?php if(isset($this->data['ClienteProdutoLog']['data_inclusao'])): ?>
		<strong>Última alteração:</strong>
		<?php echo $this->data['ClienteProdutoLog']['data_inclusao']; ?>
	<?php endif; ?>
	<?php if(isset($this->data['Usuario'][0])): ?>
		<strong>Usuário:</strong>
		<?php echo $this->data['Usuario'][0] ?>
	<?php endif; ?>
	<?php if(isset($this->data['ClienteProdutoLog']['codigo_motivo_bloqueio'])): ?>
		<strong>Status:</strong>
		<?php echo $this->data['ClienteProdutoLog']['codigo_motivo_bloqueio'] ?>
	<?php endif; ?>
	<?php if(isset($this->data['ClienteProdutoLog']['data_faturamento'])): ?>
		<strong>Faturamento:</strong>
		<?php echo $this->data['ClienteProdutoLog']['data_faturamento'] ?>
	<?php endif; ?>
	<?php if(isset($this->data['ClienteProdutoLog']['valor_premio_minimo'])): ?>
		<strong>Valor Prêmio mínimo:</strong>
		<?php echo $this->data['ClienteProdutoLog']['valor_premio_minimo'] ?>
	<?php endif; ?>
	<?php if(isset($this->data['ClienteProdutoLog']['valor_taxa_corretora'])): ?>
		<strong>Taxa Corretora:</strong>
		<?php echo $this->data['ClienteProdutoLog']['valor_taxa_corretora'] ?>
	<?php endif; ?>
	<?php if(isset($this->data['ClienteProdutoLog']['valor_taxa_bancaria'])): ?>
		<strong>Taxa Bancária:</strong>
		<?php echo $this->data['ClienteProdutoLog']['valor_taxa_bancaria'] ?>
	<?php endif; ?>
</div>
<?php
	echo $this->BForm->create('ClienteProduto', array('url' => array( 'action' => 'editar', $this->data['ClienteProduto']['codigo'], $codigo_cliente)));
?>
<?php echo $this->BForm->input('codigo', array('type' => 'hidden')) ?>
<?php echo $this->BForm->input('codigo_cliente', array('type' => 'hidden', 'value' => $codigo_cliente)) ?>

<div class='row-fluid inline' >
<?php
	if($desativa_campo_status > 1)
		echo $this->BForm->hidden('codigo_motivo_bloqueio');
		echo $this->BForm->input('codigo_motivo_bloqueio', array('label' => 'Status', 'empty' => 'Selecione', 'options' => $motivos, 'class' => 'input-xlarge motivos_bloqueio'));
?>

	<div class='row-fluid inline motivos_cancelamento' style='display:none' >
	<?= $this->BForm->input('codigo_motivo_cancelamento', array('label' => 'Motivo Cancelamento', 'empty' => 'Selecione', 'options' => $motivos_cancelamentos, 'class' => 'input-xlarge', 'id' => 'motivos_cancelamentos'));?>
	</div>
</div>
<?php if (!$visualizar): ?>
<?php echo $this->BForm->input('data_faturamento', array('label' => 'Ativação do produto', 'type' => 'text', 'class' => 'data input-small'));?>

	<div class='row-fluid inline'>
		<?php 
			echo $this->BForm->input('valor_taxa_bancaria', array('label' => 'Taxa Bancária', 'type' => 'text', 'class' => 'numeric moeda input-medium', 'value' => $this->Buonny->moeda($this->data['ClienteProduto']['valor_taxa_bancaria'])));
			echo $this->BForm->input('valor_taxa_corretora', array('label' => 'Taxa Corretora', 'type' => 'text', 'class' => 'numeric moeda input-medium', 'value' => $this->Buonny->moeda($this->data['ClienteProduto']['valor_taxa_corretora'])));
		?>
	</div>

	<div class='row-fluid inline'>
		<?php 
			echo $this->BForm->input('valor_premio_minimo', array('label' => 'Prêmio Mínimo(R$)', 'type' => 'text', 'class' => 'numeric moeda input-medium', 'value' => $this->Buonny->moeda($this->data['ClienteProduto']['valor_premio_minimo'])));
		?>
	</div>

<?php else: ?>
	<?php 
		echo $this->BForm->hidden('valor_premio_minimo', array('label' => 'Prêmio Mínimo(R$)', 'type' => 'text', 'class' => 'numeric moeda input-medium', 'value' => $this->Buonny->moeda($this->data['ClienteProduto']['valor_premio_minimo'])));
		echo $this->BForm->hidden('visualizar', array('label' => false, 'type' => 'text', 'value' => $visualizar));
	?>
<?php endif; ?>

<div class="row-fluid inline pendencia">
	<?php
		$options = array();

		$pendencia_areas = array(
			'juridica' => array(
				'imagem' => 'badge badge-warning',
				'titulo' => "Pendência Jurídica",
			),
			'financeira' => array(
				'imagem' => 'badge badge-important',
				'titulo' => "Pendência Financeira",
			),
			'comercial' =>  array(
				'imagem' => 'badge',
				'titulo' => "Pendência Comercial",
			),	
		);

		if ($pendencia_comercial){
			$options['comercial'] = 'Comercial';
		}
		if ($pendencia_financeira){
			$options['financeira'] = 'Financeira';
		}
		if ($pendencia_juridica){
			$options['juridica'] = 'Jurídica';
		}
	?>
	<?php if(!empty($options)): ?>
		<span class="label label-info">Pendências:</span>
	<?php endif; ?>        
	<div>
		<?php echo $this->BForm->input('pendencias', array('label'=>false, 'options' => $options,'multiple'=>'checkbox', 'class' => 'checkbox inline input-small')); ?>		 
		<?php if((!$pendencia_comercial) && in_array('comercial',!empty($this->data['ClienteProduto']['pendencias']) ? $this->data['ClienteProduto']['pendencias'] : $this->data)):?>
			<span class="badge-empty badge" title="Pendência Comercial"></span>&nbsp;Comercial&nbsp;&nbsp;
		<?php endif;?>
		<?php if((!$pendencia_juridica) && in_array('juridica',!empty($this->data['ClienteProduto']['pendencias']) ? $this->data['ClienteProduto']['pendencias'] : $this->data)):?>
        	<span class="badge-empty badge badge-warning" title="Pendência Jurídica"></span>&nbsp;Jurídica&nbsp;&nbsp;
		<?php endif;?>
		<?php if((!$pendencia_financeira) && in_array('financeira',!empty($this->data['ClienteProduto']['pendencias']) ? $this->data['ClienteProduto']['pendencias'] : $this->data)):?>
        	<span class="badge-empty badge badge-important" title="Pendência Financeira"></span>&nbsp;Financeira&nbsp;&nbsp;
		<?php endif;?>
	</div>
</div>

<div class="form-actions">
	<input type="submit" value="Salvar" class="btn btn-primary" />
	<?php echo $html->link('Voltar', array('action' => $visualizar ? 'assinatura_visualizar':'assinatura'), array('class'=>'btn')); ?>
</div>

<?php echo $this->BForm->end(); ?>

<script type="text/javascript">
$(document).ready(function() {
	setup_mascaras();
	setup_datepicker();

	$(document).on('change', '#ClienteProdutoPendencias0, #ClienteProdutoPendencias1, #ClienteProdutoPendencias2', function(){
		var status_inicial = $('#ClienteProdutoCodigoMotivoBloqueio').val();
		if(!$('#ClienteProdutoPendencias0').is(':checked') && !$('#ClienteProdutoPendencias1').is(':checked') && !$('#ClienteProdutoPendencias2').is(':checked') && status_inicial == 8){
			$('#ClienteProdutoCodigoMotivoBloqueio option:selected').removeAttr('selected');
		$('#ClienteProdutoCodigoMotivoBloqueio option:nth-child(2)').attr('selected','selected');
		}
	});

	var ids = [];
	$.each($(".checkbox input"), function(i,v){
			if($(v).is(':checked')){
				ids.push($(v)); 
			}
		});

	$(".motivos_bloqueio").change(function(){
		if($(this).find("option:selected").val() == 17)
			$(".motivos_cancelamento").show();
		else
			$(".motivos_cancelamento").hide();

		if($(this).find("option:selected").val() == 8 ){	
			$(".checkbox").show();
			$.each($(".checkbox input"), function(){
				if($(this).is(':checked')){
					$(this).click();
				}
			});		
		}

		if($(this).find("option:selected").val() == 1 ){
			$(".checkbox").hide();
			$.each($(".checkbox input"), function(){
				if($(this).is(':checked')){
					$(this).click();
				}
			});
		}else{
			$.each((ids), function(i,v){
				if(!$(v).is(':checked')){
					$(v).click();
				}
			});
		}
	});

	$(".motivos_bloqueio").change();


});
</script>