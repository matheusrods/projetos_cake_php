<div class='well'>
    <strong>Código: </strong><?php echo $this->Html->tag('span', $cliente['Cliente']['codigo']); ?>
    <strong>Cliente: </strong><?php echo $this->Html->tag('span', $cliente['Cliente']['razao_social']); ?>
</div>
<div class='lista'></div>
<?php
echo $this->Javascript->codeBlock("
    jQuery(document).ready(function(){
        atualizaListaClientesProdutosContratos({$cliente['Cliente']['codigo']});
    })
"); 
?>