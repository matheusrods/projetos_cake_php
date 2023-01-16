<?php if($sucesso): ?>
<?php echo $xml->serialize(compact('sucesso'), array('format' => 'tags')) ?>
<?php else: ?>
<sucesso></sucesso>
<?php endif; ?>
<?php if($erros): ?>
<?php echo $xml->serialize(compact('erros'), array('format' => 'tags')) ?>
<?php else:	 ?>
<erros></erros>
<?php endif; ?>