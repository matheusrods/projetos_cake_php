<?php echo $this->element('viagens/cliente') ?>
<?php echo $this->BForm->create('TVcenViagemChecklistEntrada', array('action' => 'post', 'url' => array('controller' => 'Viagens','action' => 'inicio_viagem',$cliente['Cliente']['codigo'],$filtros['placa'],$filtros['checklist_dias_validos'])));?>
	<?php echo $this->element('viagens/checklist_entrada_resumo') ?>
	<?php echo $this->element('viagens/fotos_checklist_entrada') ?>
	<br>
	<?php echo $html->link('Concluir Inclusão de Fotos', array('controller' =>'Viagens', 'action' => 'inicio_viagem'), array('class' => 'btn btn-success')) ;?>
<?php echo $this->BForm->end() ?>
