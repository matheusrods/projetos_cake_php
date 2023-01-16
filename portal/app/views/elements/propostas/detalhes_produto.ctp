<div id="divDetalhesProduto<?=$codigo_produto?>">
	<h5><?=$dados_produto[$codigo_produto]['Produto']['descricao']?></h5>
	<div class="well">
		<? echo $this->element('propostas/perguntas_produto'); ?>
		<? echo $this->element('propostas/servicos_produto'); ?>
	</div>
</div>

<?php echo $this->Javascript->codeBlock('
    jQuery(document).ready(function(){
       setup_mascaras();
    });', false); 
?>    
