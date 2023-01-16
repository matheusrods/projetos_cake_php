<?php if(!empty($dados)): ?>
    
    <?php echo $this->element('/relatorios_sm/listagem_posicao_veiculos', array('posicoes'=>$dados)); ?>

<?php else: ?>
    <div class="alert">
		Nenhum registro encontrado.
	</div>
<?php endif; ?>
<?php $this->addScript($this->Buonny->link_js('estatisticas')) ?>