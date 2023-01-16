<div class="row-fluid inline">
    <?php echo $this->Buonny->input_codigo_corretora($this,'codigo_corretora_dialog','Corretora',true,'ConfiguracaoComissaoCorre',null,false) ?>
</div>
<div class="row-fluid inline">
	<?php echo $this->Buonny->input_produto_servico($this,$produtos,$servicos); ?>
</div>
<div class="row-fluid inline">
	<?php echo $this->BForm->input('verificar_preco_unitario', array('label' => 'Verificar Preço Unitário (R$)','type' => 'checkbox','class' => 'verificar_preco_unitario')) ?>
	<?php echo $this->BForm->input('preco_de', array('label' => '&nbsp;', 'placeholder' => 'De', 'type' => 'text', 'class' => 'input-small moeda numeric preco_unitario', 'maxlength' => 13)); ?>
	<?php echo $this->BForm->input('preco_ate', array('label' => '&nbsp;', 'placeholder' => 'Até', 'type' => 'text', 'class' => 'input-small moeda numeric preco_unitario', 'maxlength' => 13)); ?>
</div>
<div class="row-fluid inline">
	<?php echo $this->BForm->input('percentual_impostos', array('label' => 'Impostos (%)', 'type' => 'text', 'class' => 'input-small moeda numeric', 'maxlength' => 6)); ?>
	<?php echo $this->BForm->input('percentual_comissao', array('label' => 'Comissão (%)', 'type' => 'text', 'class' => 'input-small moeda numeric', 'maxlength' => 6)); ?>
</div>
<div class="row-fluid inline">
</div>

<div class="form-actions">
  <?php echo $this->BForm->submit('Salvar', array('div' => false, 'class' => 'btn btn-primary')); ?>
  <?php echo $this->Html->link('Voltar','javascript:close_dialog()', array('class' => 'btn')); ?>
</div>
<?php echo $this->Javascript->codeBlock('
	$(".preco_unitario").parent().hide();
    jQuery(document).ready(function(){
		setup_mascaras("000,##");
		$(".verificar_preco_unitario").change(function(){
			if($(".verificar_preco_unitario").is(":checked")){
				$(".preco_unitario").parent().show();
				$(".verificar_preco_unitario").parent().css({"position":"absolute"});
			}else{
				$(".preco_unitario").parent().hide();
				$(".verificar_preco_unitario").parent().css({"position":"relative"});
			}
		});
		$(".verificar_preco_unitario").change();
    });', false);
?>