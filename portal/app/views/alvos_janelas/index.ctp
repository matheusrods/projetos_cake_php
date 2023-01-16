<?php if(!empty($cliente) && !empty($referencia)): 
echo $this->Buonny->flash();
?>

	<div id="cliente" class='well'>
	        <strong>Código: </strong><?= $cliente['Cliente']['codigo'] ?>
	        <strong>Cliente: </strong><?= $cliente['Cliente']['razao_social'] ?><br>
	        <strong>Alvo: </strong><?= $referencia['TRefeReferencia']['refe_descricao'] ?>
	</div>
<div class="form-janelas">
	<?php echo $bajax->form('TCajaConfAlvoJanela',array('url' => array('controller' => 'alvos_janelas', 'action' => 'index',$cliente['Cliente']['codigo'], $referencia['TRefeReferencia']['refe_codigo']),'type' => 'post') ) ?>

	<div class="row-fluid inline">
		<?php echo $this->BForm->input("janela_inicio", array('label' => 'Janela Inicio', 'class' => 'hora input-mini')) ?>
		<?php echo $this->BForm->input("janela_fim", array('label' => 'Janela Fim', 'class' => 'hora input-mini')) ?>
		<div class="control-group input text"><label>&nbsp;</label>
			<?php echo $this->BForm->submit('Incluir', array('div' => false, 'class' => 'btn btn-success')); ?>
		</div>
	</div>

	<?php echo $this->BForm->end(); ?>
</div>
<?php echo $this->Javascript->codeBlock('
	$(document).ready(function(){
		setup_time();
	});
'); ?>

	<div class='lista-janelas'></div>
	<?php echo $this->Javascript->codeBlock('
        function atualizaListaConfiguracaoAlvoJanela(){
            var div = jQuery("div.lista-janelas");
            bloquearDiv(div);            
            div.load(baseUrl + "alvos_janelas/listagem/" + Math.random());
        }
	    $(document).ready(function(){
	    	atualizaListaConfiguracaoAlvoJanela();
	        setup_mascaras();
	    });		
	', false);
    ?>
	
<?php endif; ?>