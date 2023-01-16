<?php echo $this->Html->tag('option', 'Estado', array('value' => '')) ?>
<?php foreach ($estados as $key => $estado): ?>
    <?php $options = array('value' => $key) ?>
    <?php if (count($this->data) == 1): ?>
        <?php $options = array_merge($options, array('selected' => 'selected')) ?>
    <?php endif; ?>
    <?php echo $this->Html->tag('option', $estado, $options)?>
<?php endforeach; ?>