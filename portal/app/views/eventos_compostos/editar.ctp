<?php echo $this->BForm->create('TEcomEventoComposto', array('type' => 'post' ,'url' => array('controller' => 'eventos_compostos','action' => 'editar',$this->data['TEcomEventoComposto']['ecom_codigo'],rand())));?>
	<div class="well">
		<strong>Cliente: </strong><?= DbbuonnyGuardianComponent::converteClienteGuardianEmBuonny($this->data['TPjurPessoaJuridica']['pjur_pess_oras_codigo']);?> - <?= $this->data['TPjurPessoaJuridica']['pjur_razao_social']?>
	</div>
	<div class="row-fluid inline">
        <?php echo $this->BForm->hidden('ecom_codigo'); ?>
        <?php echo $this->BForm->input('ecom_descricao', array('label' => 'Descrição','class' => 'inline input-large')); ?>
		<?php echo $this->BForm->input('ecom_sequencial', array('empty' => 'Selecione o evento','label' => 'Evento Sequencial','options' => $sequencial, 'class' => 'inline input-small')); ?>
        <?php echo $this->BForm->input('ecom_minutos_abrangencia', array('label' => 'Abrangência (Minutos)','class' => 'inline numeric just-number input-small')); ?>
        <?php echo $this->BForm->hidden('TPjurPessoaJuridica.pjur_pess_oras_codigo'); ?>
	</div>
	<?php $contagem = count($this->data['TCeveComposicaoEvento']['ceve_espa_codigo']);?>
	<?php foreach ($this->data['TCeveComposicaoEvento']['ceve_espa_codigo'] as $key => $valor) :?>
		<div class="row-fluid inline">
			<?php echo $this->BForm->input('TCeveComposicaoEvento.ceve_espa_codigo', array('label' => false,'empty' => 'Selecione o evento','name' => "data[TCeveComposicaoEvento][ceve_espa_codigo][]",'options' => $eventos, 'class' => 'inline input-xlarge','value' => $valor) ); ?>
			<?php if(($contagem - 1) == $key):?>
				<?php echo $this->Html->link('<i class="icon-plus icon-white"></i>', 'javascript:void(0)',array('class' => 'btn btn-success', 'escape' => false, 'onclick' => "adiciona_evento(jQuery(this).parent(),this)")); ?>
			<?php else:?>
				<?php echo $this->Html->link('<i class="icon-minus"></i>', 'javascript:void(0)',array('class' => 'btn', 'escape' => false, 'onclick' => "remove_evento(jQuery(this).parent(),this)")); ?>
			<?php endif;?>
		</div>
	<?php endforeach;?>

	<div id="inputs_adicionais"></div>

	<div class="form-actions">
		<?php echo $this->BForm->submit('Salvar', array('div' => false, 'class' => 'btn btn-success')); ?>
		<?php echo $html->link('Voltar',array('action'=>'index') , array('class' => 'btn')); ?>
	</div>
<?php echo $this->BForm->end(); ?>
<?php echo $this->Javascript->codeBlock("
		$(document).ready(function(){
			setup_mascaras();
		});
		function adiciona_evento(div_inteiro,botao_mais){
			$( div_inteiro ).clone().appendTo( '#inputs_adicionais');
			remove_evento(botao_mais);	
			$( div_inteiro ).append( '<a class=\\'btn\\' onclick=\\'remove_evento(jQuery(this).parent())\\' href=\\'javascript:void(0)\\'><i class=\\'icon-minus\\'></i></a>');
		}
		function remove_evento(div_inteiro) {
			console.log(div_inteiro)
			jQuery(div_inteiro).remove();
		}
");?>