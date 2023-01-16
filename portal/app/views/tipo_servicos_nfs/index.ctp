<div class = 'form-procurar'>
	<?= $this->element('/filtros/tipo_servicos_nfs') ?>
</div>
<div class='actionbar-right'>
	<?php echo $this->Html->link('<i class="icon-plus icon-white"></i>', array('controller' => 'tipo_servicos_nfs', 'action' => 'incluir'), array('escape' => false, 'class' => 'btn btn-success', 'title' =>'Cadastrar Tipo Serviços NFS'));?>
</div>
<div class='lista'></div>
