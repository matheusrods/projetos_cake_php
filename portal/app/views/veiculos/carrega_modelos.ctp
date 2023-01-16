<?php echo $this->Html->tag('option', 'Selecione um Modelo', array('value' => '')) ?>
<?php foreach ($this->data['modelos'] as $key => $modelo): ?>
    <?php $options = array('value' => $key) ?>
    <?php if ($key == $this->data['default']): ?>
        <?php $options = array_merge($options, array('selected' => 'selected')) ?>
    <?php endif; ?>
    <?php echo $this->Html->tag('option', $modelo, $options)?>
<?php endforeach; ?>