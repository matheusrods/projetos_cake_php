<div class = 'form-procurar'>
	<div class="well">
<?php echo $this->BForm->create('ObjetivoComercialExcecao', array('type' => 'post' ,'url' => array('controller' => 'objetivos_comerciais','action' => 'incluir')));?>
<div class="row-fluid inline">
	<?php echo $this->Buonny->input_codigo_cliente_dados($this,array(
		'razao_social' => 'ObjetivoComercialExcecaoRazaoSocial','gestor' => 'ObjetivoComercialExcecaoCodigoGestor1',),'codigo_cliente', null, true, 'ObjetivoComercialExcecao') ?>
    <?php echo $this->BForm->input("razao_social", array('label' => 'Razão Social', 'class' => 'input-xxlarge', 'readonly'=>true)) ?>
    <?php echo $this->BForm->input('codigo_produto', array('type' => 'select', 'options' => $produtos, 'class' => 'input-large', 'label' => 'Produtos','empty' => 'Selecione o produto')); ?>
</div>
<div class="row-fluid inline">
    <?php echo $this->BForm->input('codigo_gestor1', array('type' => 'select', 'options' => $gestores, 'class' => 'input-large', 'label' => 'Gestor','empty' => 'Selecione o getor')); ?>
    <?php echo $this->BForm->input('percentagem_gestor1', array('class' => 'input-mini numeric', 'label' => '%')); ?>
    <?php echo $this->BForm->input('codigo_gestor2', array('type' => 'select', 'options' => $gestores, 'class' => 'input-large', 'label' => 'Gestor','empty' => 'Selecione o getor')); ?>
    <?php echo $this->BForm->input('percentagem_gestor2', array('class' => 'input-mini numeric', 'label' => '%')); ?>
</div>
<div class="form-actions">
	<?php echo $this->BForm->submit('Salvar', array('div' => false, 'class' => 'btn btn-primary')); ?>
	<?= $html->link('Voltar', array('action' => 'excecoes_cliente'), array('class' => 'btn')); ?>
</div>
<?php echo $this->BForm->end(); ?>
<?php echo $this->Javascript->codeBlock('
	$(document).ready(function() {
		var campo1 = $("#ObjetivoComercialExcecaoPercentagemGestor1");
		var campo2 = $("#ObjetivoComercialExcecaoPercentagemGestor2");

		campo1.blur(function(){
			if(campo1.val() > 100){
				campo1.val(100)
				campo2.val(0);
			}else{
				campo2.val(100-campo1.val());
			}
		});

		campo2.blur(function(){
			if(campo2.val() > 100){
				campo2.val(100)
				campo1.val(0);
			}else{
				campo1.val(100-campo2.val());
			}
		});
	});
');
?>