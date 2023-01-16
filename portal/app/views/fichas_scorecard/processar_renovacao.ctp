<?php echo $this->BForm->create(); ?>
<?php echo $this->BForm->end('Executar Renovação'); ?>


<?php if(isset($saida)): ?>
<code>
<?php echo pr($saida); ?>
</code>
<?php endif; ?>