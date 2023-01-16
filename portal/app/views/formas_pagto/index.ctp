<div class = 'form-procurar'>
	<?= $this->element('/filtros/formas_pagto') ?>
</div>
<div class='actionbar-right'>
	<?php echo $this->Html->link('<i class="icon-plus icon-white"></i>', array('controller' => 'formas_pagto', 'action' => 'incluir'), array('escape' => false, 'class' => 'btn btn-success', 'title' =>'Cadastrar Nova Forma de Pagamento'));?>
</div>
<div>&nbsp;</div>
<div class='lista'></div>
