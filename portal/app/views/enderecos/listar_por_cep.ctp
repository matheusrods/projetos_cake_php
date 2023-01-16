<?php echo $this->Html->tag('option', 'Selecione um endereço', array('value' => '')) ?>
<?php if($this->data): ?>
<?php foreach ($this->data as $key => $endereco): ?>
    <?php $options = array('value' => $key) ?>
    <?php if (count($this->data) == 1): ?>
        <?php $options = array_merge($options, array('selected' => 'selected')) ?>
    <?php endif; ?>
    <?php echo $this->Html->tag('option', $endereco, $options)?>
<?php endforeach; ?>
<?php endif; ?>