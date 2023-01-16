<h3>Corpo Clínico</h3>
<div class='actionbar-right'>
    <?php echo $this->Html->link('<i class="icon-plus icon-white"></i>', array('controller' => 'medicos', 'action' => 'buscar_medico', $codigo_fornecedor), array('escape' => false, 'class' => 'btn btn-success dialog_medicos_fornecedor', 'title' =>'Cadastrar Novos Médicos'));?>
</div>
<div id="fornecedor-medico-lista" class="grupo"></div>
