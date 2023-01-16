 <div class='well'>
 	<?= $log_integracao['LogIntegracao']['arquivo'] ?>
 	<?= $log_integracao['LogIntegracao']['data_inclusao'] ?>
 	<?= highlight_string($log_integracao['LogIntegracao']['retorno']) ?>
</div>
<?php $conteudo = @unserialize($log_integracao['LogIntegracao']['conteudo']); ?>
<?php if($conteudo !== FALSE): ?>
	<pre>
		<?php print_r($conteudo); ?>
	</pre>
<?php else: ?>
	<pre>
		<?= htmlentities($log_integracao['LogIntegracao']['conteudo']) ?>
	</pre>
<?php endif; ?>