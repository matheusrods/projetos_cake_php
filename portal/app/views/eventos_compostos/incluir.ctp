<?php echo $this->BForm->create('TEcomEventoComposto',array('url' => array('controller' => 'EventosCompostos','action' => 'incluir'), 'type' => 'POST')) ?>

	<?php 	
		if(!empty($this->data['TCeveComposicaoEvento']) && count($this->data['TCeveComposicaoEvento']) > 0):
			$listar = $this->data['TCeveComposicaoEvento'];
			$contagem = count($listar['ceve_espa_codigo']);
		endif;
	?>

	<div class='row-fluid inline'>
		<?php echo $this->Buonny->input_codigo_cliente($this, 'ecom_pess_oras_codigo', 'Cliente', 'Cliente', 'TEcomEventoComposto' ); ?>
        <?php echo $this->BForm->input('ecom_descricao', array('label' => 'Descrição','class' => 'inline input-large')); ?>
		<?php echo $this->BForm->input('ecom_sequencial', array('empty' => 'Selecione o campo','label' => 'Evento Sequencial','options' => $sequencial, 'class' => 'inline input-medium')); ?>
        <?php echo $this->BForm->input('ecom_minutos_abrangencia', array('label' => 'Abrangência (Minutos)','class' => 'inline numeric just-number input-small')); ?>
        <?php echo $this->BForm->hidden('ecom_status', array('value' => 'S')); ?>
	</div>
	<?php if(isset($listar) && !empty($listar)):?>
		<?php foreach ($listar['ceve_espa_codigo'] as $key => $lista) :?>
			<div class='row-fluid inline'>
				<?php if(empty($lista)):?>
					<div class="control-group input select required error">
						<?php echo $this->BForm->input('TCeveComposicaoEvento.ceve_espa_codigo', array('label' => false,'empty' => 'Selecione o evento','options' => $eventos,'value' => $lista, 'class' => 'inline input-xlarge form-error','name' => "data[TCeveComposicaoEvento][ceve_espa_codigo][$key]",)); ?>
					</div>
				<?php else:?>
					<?php echo $this->BForm->input('TCeveComposicaoEvento.ceve_espa_codigo', array('label' => false,'empty' => 'Selecione o evento','options' => $eventos,'value' => $lista, 'class' => 'inline input-xlarge','name' => "data[TCeveComposicaoEvento][ceve_espa_codigo][$key]",)); ?>
				<?php endif;?>	
				<?php if(($contagem - 1) == $key):?>
					<?php echo $this->Html->link('<i class="icon-plus icon-white"></i>', 'javascript:void(0)',array('class' => 'btn btn-success', 'escape' => false, 'onclick' => "adiciona_evento(jQuery(this).parent(),this)")); ?>
				<?php else:?>
					<?php echo $this->Html->link('<i class="icon-minus"></i>', 'javascript:void(0)',array('class' => 'btn', 'escape' => false, 'onclick' => "remove_evento(jQuery(this).parent(),this)")); ?>
				<?php endif;?>
			</div>
		<?php endforeach?>
	<?php else:?>
		<div class='row-fluid inline'>
			<?php echo $this->BForm->input('TCeveComposicaoEvento.ceve_espa_codigo', array('label' => false,'empty' => 'Selecione o evento','options' => $eventos, 'class' => 'inline input-xlarge','name' => "data[TCeveComposicaoEvento][ceve_espa_codigo][]",)); ?>
			<?php echo $this->Html->link('<i class="icon-plus icon-white"></i>', 'javascript:void(0)',array('class' => 'btn btn-success', 'escape' => false, 'onclick' => "adiciona_evento(jQuery(this).parent(),this)")); ?>
		</div>
	<?php endif?>	
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
			jQuery(div_inteiro).remove();
		}

		
");?>