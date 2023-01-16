<div class="tab-content">
	<h4>Liberação por Ip's</h4>
	<div class='actionbar-right'>
	    <?php echo $this->Html->link('Incluir', array(
	    		'controller' => 'usuarios_ips',
	            'action' => 'incluir', $this->data['Usuario']['codigo']), 
	            array(
	                'onclick' => 'return open_dialog(this, "Adicionar IP", 560)', 
	                'title' => 'Adicionar IP de Liberação', 
	                'class' => 'btn btn-success',
	            )
	        );
	    ?>
	</div>
	<div class="listaIps"></div>
</div>
<?php // $this->addScript($this->Buonny->link_js('comum.js')); ?>
<?php echo $this->Javascript->codeBlock('
    $(function() {
        setup_time();
        setup_mascaras();
    });
');?>