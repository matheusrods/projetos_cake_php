<?php echo $this->Html->tag('option', 'Escolha a Cidade!', array('value' => '')) ?>
<?php foreach ($this->data as $key => $cidade): ?>
    <?php $options = array('value' => $key) ?>
    <?php if (count($this->data) == 1): ?>
        <?php $options = array_merge($options, array('selected' => 'selected')) ?>
    <?php endif; ?>
    <?php echo $this->Html->tag('option', $cidade, $options)?>
<?php endforeach; ?>