<?php echo $this->Html->tag('option', 'Bairro', array('value' => '')) ?>
<?php foreach ($this->data as $key => $bairro): ?>
    <?php $options = array('value' => $key) ?>
    <?php if (count($this->data) == 1): ?>
        <?php $options = array_merge($options, array('selected' => 'selected')) ?>
    <?php endif; ?>
    <?php echo $this->Html->tag('option', $bairro, $options)?>
<?php endforeach; ?>