<div class = 'form-procurar'>
	<div class="well">
<?php echo $this->BForm->create('ObjetivoComercialExcecao', array('type' => 'post' ,'url' => array('controller' => 'objetivos_comerciais','action' => 'incluir_excecao')));?>
<div class="row-fluid inline">
	<?php echo $this->Buonny->input_codigo_cliente_dados($this,array(
		'razao_social' => 'ObjetivoComercialExcecaoRazaoSocial','gestor' => 'ObjetivoComercialExcecaoCodigoGestor1',),'codigo_cliente', null, true, 'ObjetivoComercialExcecao') ?>
    <?php echo $this->BForm->input("razao_social", array('label' => 'Razão Social', 'class' => 'input-xxlarge', 'readonly'=>true)) ?>
    <?php echo $this->BForm->input('codigo_produto', array('type' => 'select', 'options' => $produtos, 'class' => 'input-large', 'label' => 'Produtos','empty' => 'Selecione o produto')); ?>
</div>
<div class="row-fluid inline">
    <?php echo $this->BForm->input('codigo_gestor1', array('type' => 'select', 'options' => $gestores, 'class' => 'input-large', 'label' => 'Gestor 1','empty' => 'Selecione o getor')); ?>
    <?php echo $this->BForm->input('percentagem_gestor1', array('class' => 'input-mini numeric', 'label' => '%')); ?>
    <?php echo $this->BForm->input('codigo_gestor2', array('type' => 'select', 'options' => $gestores, 'class' => 'input-large', 'label' => 'Gestor 2','empty' => 'Selecione o getor')); ?>
    <?php echo $this->BForm->input('percentagem_gestor2', array('class' => 'input-mini numeric', 'label' => '%')); ?>
    <div class="input" style="width:300px;padding-top:30px">
    	<?php echo $this->BForm->input('comissao_gestor', array('label' => 'Zerar Comissão', 'type' => 'checkbox')) ?>
    </div>	
</div>
<div class="row-fluid inline">
    <span class="label label-info">Gestor do produto:</span>
    <div class="row-fluid inline">
        <?php echo $this->BForm->input('ObjetivoComercialExcecao.gestor_produto', array('type' => 'radio', 'options' => array(1 => 'Gestor 1',2 => 'Gestor 2'), 'default' => 1, 'legend' => false, 'label' => array('class' => 'radio inline input-medium'))) ?>
    </div>
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
		var gestor2 = $("#ObjetivoComercialExcecaoCodigoGestor2");
		var radioGestor1 = $("#ObjetivoComercialExcecaoGestorProduto1");
		var radioGestor2 = $("#ObjetivoComercialExcecaoGestorProduto2");
		var gestorDoProduto = 1;

		if(gestor2.val() == ""){
			radioGestor2.attr("disabled", true);
			gestor2.val("");
			radioGestor1.prop( "checked", true );			
		}

		campo1.blur(function(){		
			if(campo1.val() > 100){
				campo1.val(100);
				campo2.val(0);
			}else if(campo1.val() == ""){
				campo1.val(0);
				campo2.val(100);
			}else{
				campo2.val(100-campo1.val());
			}
		});

		campo2.blur(function(){		
			if(campo2.val() > 100){
				campo2.val(100)
				campo1.val(0);
			}else if(campo2.val() == ""){
				campo2.val(0);
				campo1.val(100);
			}else{
				campo1.val(100-campo2.val());
			}
		});		

		gestor2.blur(function(){			
			radioGestor2.removeAttr("disabled");			
			if(gestorDoProduto == 2){
				radioGestor1.prop( "checked", false );
				radioGestor2.prop( "checked", true );
			}
			if(gestor2.val() == ""){
				radioGestor2.attr("disabled", true);
				gestor2.val("");
				radioGestor1.prop( "checked", true );
			}else{
				soma = parseFloat(campo1.val()) + parseFloat(campo2.val());			
				if(soma != 100){
					if($("#ObjetivoComercialExcecaoComissaoGestor").is(":checked")){
						campo2.val(0);
						campo1.val(0);
					}else{
						campo2.val(0);
						campo1.val(100);
					}
				}	
			}			
		});

		$("#ObjetivoComercialExcecaoComissaoGestor").click(function(){		
			if($("#ObjetivoComercialExcecaoComissaoGestor").is(":checked")){
				$("#ObjetivoComercialExcecaoPercentagemGestor1").val(0);
				$("#ObjetivoComercialExcecaoPercentagemGestor1").attr("disabled", true);
				$("#ObjetivoComercialExcecaoPercentagemGestor2").val(0);
				$("#ObjetivoComercialExcecaoPercentagemGestor2").attr("disabled", true);
				radioGestor2.attr("disabled", true);
				gestor2.val("");
				radioGestor1.prop( "checked", true );						
			}else{
				$("#ObjetivoComercialExcecaoPercentagemGestor1").removeAttr("disabled");				
				$("#ObjetivoComercialExcecaoPercentagemGestor2").removeAttr("disabled");
			}
		});
	});
');
?>