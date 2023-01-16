<h3>Horários</h3>
<div class='actionbar-right'>
    <?php echo $this->Html->link('<i class="icon-plus icon-white"></i>', array('controller' => 'fornecedores_horarios', 'action' => 'incluir', $codigo_fornecedor), array('escape' => false, 'class' => 'btn btn-success dialog_horarios', 'title' =>'Cadastrar Novos Horários'));?>
</div>
<div id="fornecedor-horario-lista" class="grupo"></div>