<?php echo $this->BForm->input('data', array('class' => 'data input-small', 'label' => 'Data','type' => 'text')) ?>
<?php echo $this->Buonny->input_codigo_cliente($this, 'codigo_embarcador','Embarcador', 'Embarcador', 'TEeveEstatisticaEvento') ?>
<?php echo $this->Buonny->input_codigo_cliente($this, 'codigo_transportador','Transportador', 'Transportador', 'TEeveEstatisticaEvento') ?>
<?php echo $this->BForm->input('espa_tipo_evento', array('label' => 'Tipo', 'class' => 'input-medium', 'options'=> array('S' => 'Conforme', 'N' => 'Não Conforme'), 'empty' => 'Todos')); ?>
<?php echo $this->BForm->input('codigo_evento', array('label' => 'Evento', 'multiple' => 'multiple', 'class' => 'input-medium multiselect-evento', 'options'=> $evento, 'style' => 'display:none')); ?>
<?php echo $this->BForm->input('codigo_estacao', array('label' => 'Estação de Rastreamento', 'multiple' => 'multiple', 'class' => 'input-medium multiselect-estacao-rastreamento', 'options'=> $estacao, 'style' => 'display:none')); ?>