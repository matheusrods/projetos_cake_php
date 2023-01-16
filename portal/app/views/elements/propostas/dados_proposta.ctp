<h5>Dados da Proposta</h5>
<div class='well'>
  	<div class="row-fluid inline">
      	<?php echo $this->BForm->input('data_inclusao', array('class' => 'input-medium','type'=>'text', 'label' => 'Dt. Proposta', 'readonly'=>true)); ?>
  		<?php echo $this->BForm->input('data_validade', array('label' => 'Data Validade','type'=>'text' ,'class' => ($readonly?'':'data ').'input-small', 'readonly'=>$readonly)) ?>

		<?php echo $this->BForm->input('codigo_status_proposta', array('type'=>'hidden')) ?>  		
		<? if (!empty($this->data['Proposta']['codigo'])): ?>
			<?php echo $this->BForm->input('StatusProposta.descricao', array('type'=>'text', 'class'=>'input-medium', 'label'=>'Status', 'readonly'=>true, 
					//'after'=>($exibe_historico_status ? $html->link('', 'javascript:void(0)', array('class' => 'icon-eye-open', 'title' => 'Visualizar Histórico de Alteração de Status da Proposta', 'style' => 'position: relative;top: -5px;right: -5px;', 'onclick'=>"carrega_historico_status({$this->data['Proposta']['codigo']})"))."&nbsp;&nbsp;&nbsp;&nbsp;" : '')
				 )) ?>
			<? if (!empty($this->data['Proposta']['data_envio'])): ?>
		      	<?php echo $this->BForm->input('data_envio', array('class' => 'input-medium','type'=>'text', 'label' => 'Dt. Envio', 'readonly'=>true)); ?>
			<? endif; ?>
			<? if (!empty($this->data['Proposta']['data_cancelamento'])): ?>
		      	<?php echo $this->BForm->input('data_cancelamento', array('class' => 'input-medium','type'=>'text', 'label' => 'Dt. Cancelamento', 'readonly'=>true)); ?>
				<?php echo $this->BForm->input('codigo_proposta_motivo_cancelamento', array('class' => 'input-large', 'label' => 'Motivo Cancelamento', 'options'=>$motivos_cancelamento,'disabled'=>true)); ?>
			<? else: ?>
				<? if (!empty($this->data['Proposta']['data_finalizacao'])): ?>
			      	<?php echo $this->BForm->input('data_finalizacao', array('class' => 'input-medium','type'=>'text', 'label' => 'Dt. Finalização', 'readonly'=>true)); ?>
				<? endif; ?>
			<? endif; ?>
		<? endif; ?>
  	</div>
	<?php if (!empty($this->data['Proposta']['codigo'])): ?>
	  	<?php if ($this->data['Proposta']['codigo_status_proposta']==StatusProposta::PROPOSTA_REPROVADA || $this->data['Proposta']['codigo_status_proposta']==StatusProposta::NEGADO_DIRETORIA): ?>
	  	<div class="row-fluid inline">
			<?php echo $this->BForm->input('justificativa_rejeicao', array('type'=>'textarea', 'label' => 'Motivo Reprovação', 'class' => 'input-xxlarge', 'readonly' => true)); ?>
	  	</div>
		<?php elseif($this->data['Proposta']['codigo_status_proposta']==StatusProposta::EM_APROVACAO_DIRETORIA || $this->data['Proposta']['codigo_status_proposta']==StatusProposta::EM_APROVACAO_GERENCIA): ?>
			<?php echo $this->BForm->hidden('justificativa_rejeicao'); ?>
	  	<?php endif; ?>
  	<?php endif; ?>
  	<div class="row-fluid inline">
  		<?php echo $this->BForm->input('observacao', array('label' => 'Observações','type'=>'textarea','class' => 'input-xxlarge', 'readonly'=>$readonly)) ?>
  	</div>
	<div class='row-fluid inline' id="checkboxes_produtos">
		<span class="label label-info">Produtos</span>
		<? if (!$readonly): ?>
			<span class='pull-right'>
		  		<?= $html->link('Desmarcar todas', 'javascript:void(0)', array('onclick' => 'desmarcarTodos("checkboxes_produtos"); jQuery("div#checkboxes_produtos").find("input[type=checkbox]").change();')) ?>
		  		<?= $html->link('Marcar todas', 'javascript:void(0)', array('onclick' => 'marcarTodos("checkboxes_produtos"); jQuery("div#checkboxes_produtos").find("input[type=checkbox]").change();')) ?>
			</span>
		<? endif; ?>
		<?php echo $this->BForm->input('PropostaProduto.codigo_produto', 
		  array(
		  	'label'=>false, 
		  	'options'=>$produtos, 
		  	'multiple'=>'checkbox', 
		  	'class' => 'checkbox inline input-xxlarge produto',
		  	'disabled'=>$readonly,
		  )); 
		?>
	</div>
</div>
<?php echo $this->element('propostas/produtos');?>

<?php echo $this->Javascript->codeBlock('
	function carrega_historico_status(codigo_proposta) {
        var link = "/portal/propostas/historico_status/"+codigo_proposta+"/" + Math.random();
        open_dialog(link, "Histórico Alteração de Status de Proposta", 720);
	}
	function reprovar_proposta_interna(codigo_proposta) {
        var link = "/portal/propostas/reprovar_proposta_interna/"+codigo_proposta+"/" + Math.random();
        open_dialog(link, "Reprovar Proposta", 720);
	}

	$(document).ready(function() {
		//
		
	});
');
?>