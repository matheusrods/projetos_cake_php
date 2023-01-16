<div class="form-procurar">
	<?= $this->element('/filtros/clientes_visualizar') ?>
</div>
<?php  if( isset($permissao) && $permissao == 1):?>
	<br/>
		<div class='well'>
		   	<span class="pull-right">
		   		<strong>	
		   			<?php echo $this->Html->link("<i class='cus-page-white-excel'></i>", array( 'controller' => $this->name, 'action' => 'visualizar_clientes', $this->passedArgs[0] ='export'), array('escape' => false, 'title' =>'Exportar para Folhamatic'));?>
		   		</strong>
			</span>
		</div>
	<br/>
<?php endif?>
<div id="lista-clientes-visualizar"></div>