<div class = 'form-procurar'>
	<?= $this->element('/filtros/pos_configuracoes') ?>
</div>
<div class='actionbar-right'>
	    <?php if(isset($this->data['Matriz']['codigo']) && !empty($this->data['Matriz']['codigo'])): ?>
        <?= $this->Html->link('<i class="icon-plus icon-white"></i>', array(
		    'controller' => 'pos_configuracoes', 
		    'action' => 'incluir',
        $this->data['Matriz']['codigo'],
            ), array(
                'escape' => false, 
                'class' => 'btn btn-success', 
                'title' =>'Cadastrar Configurações POS')
			);?>
        <?php endif;?>
 </div>
<div class='lista'></div>
