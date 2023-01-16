<div class='well'>
    <strong>Código: </strong><?php echo $this->Html->tag('span', $cliente['Cliente']['codigo']); ?>
    <strong>Cliente: </strong><?php echo $this->Html->tag('span', $cliente['Cliente']['razao_social']); ?>
</div>
<div class='actionbar-right'>
	<?php echo $this->Html->link('<i class="icon-plus icon-white"></i> Incluir', array( 'controller' => $this->name, 'action' => 'incluir', $this->passedArgs[0]), array('escape' => false, 'class' => 'btn btn-success', 'title' =>'Incluir novos relacionamentos para este cliente', 'onclick' => "return open_dialog(this, 'Incluir Relacionamento', 600)"));?>
</div>
<div id='relacionamentos-cliente'></div>
<?php $this->addScript($this->Buonny->link_js('clientes')) ?>
<?php $this->addScript($this->Javascript->codeBlock("jQuery(document).ready(function(){
        carrega_relacionamentos_cliente(".$this->passedArgs[0].");
    })")) 
?>