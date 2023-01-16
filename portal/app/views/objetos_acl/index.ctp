<div class="lista">
	<div class='actionbar-right'>
		<?php echo $this->Html->link('<i class="icon-plus icon-white"></i> Incluir', array( 'controller' => 'objetos_acl', 'action' => 'incluir'), array('escape' => false, 'class' => 'btn btn-success', 'title' =>'Cadastrar Novo Objeto'));?>
	</div>

	<table class="table table-striped">
	    <thead>
	        <tr>
	            <th>Descricao</th>
	            <th>Aco String</th>
	            <th style="width:13px"></th>
	            <th style="width:13px"></th>
	            <th style="width:13px"></th>
	            <th style="width:13px"></th>
	            <th style="width:13px"></th>
	            <th style="width:13px"></th>
	        </tr>
	    </thead>
	    <?php echo $this->element('objetos_acl/listagem', array('objetos' => $objetos, 'nivel' => '')) ?>
	</table> 
</div>