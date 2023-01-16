<?php echo $this->Html->tag('option', 'Selecione uma Cidade', array('value' => '')) ?>
<?php foreach ($this->data['cidades'] as $key => $cidade): ?>
    <?php $options = array('value' => $key) ?>
    <?php if ($key == $this->data['default']): ?>
        <?php $options = array_merge($options, array('selected' => 'selected')) ?>
    <?php endif; ?>
    <?php echo $this->Html->tag('option', $cidade, $options)?>
<?php endforeach; ?>