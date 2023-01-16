<div class = 'form-procurar'>
	<?= $this->element('/filtros/pos_categorias') ?>
</div>
<div class='actionbar-right'>
	    <?php if(isset($this->data['Matriz']['codigo']) && !empty($this->data['Matriz']['codigo'])): ?>
        <?= $this->Html->link('<i class="icon-plus icon-white"></i>', array(
		    'controller' => 'pos_categorias', 
		    'action' => 'incluir',
        $this->data['Matriz']['codigo'],
            ), array(
                'escape' => false, 
                'class' => 'btn btn-success', 
                'title' =>'Cadastrar Categorias de Observação',
                'style' => 'margin-bottom: 10px;'
                )
			);?>
        <?php endif;?>
 </div>
<div class='lista'></div>

<div class="well">
    <?php echo $html->link('Voltar', array('controller' => 'pos_categorias', 'action' => 'buscar_clientes'), array('class' => 'btn')); ?>
</div>
