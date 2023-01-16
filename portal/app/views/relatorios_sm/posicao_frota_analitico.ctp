<?php if(!empty($dados)): ?>
    
    <?php echo $this->element('/relatorios_sm/listagem_posicao_frota', array('dados'=>$dados)); ?>

<?php else: ?>
    <div class="alert">
		Nenhum registro encontrado.
	</div>
<?php endif; ?>
<?php $this->addScript($this->Buonny->link_js('estatisticas')) ?>
<?php $this->addScript($this->Buonny->link_css('tablesorter')); ?>
<?php $this->addScript($this->Buonny->link_js('jquery.tablesorter.min')); ?>