<?php foreach($modelos as $codigo => $descricao): ?>
	<li>
		<?php echo $html->link('', 
					'javascript:void(0)', 
					array('class' => 'dele-modelo icon-trash', "style" => "float:right", "mviacodigo" => $codigo)) ?>
		<span  style="float:right">&nbsp;</span>
		<?php echo $html->link('', 
					array('controller' => 'ModelosViagens', 'action' => 'visualizar', $codigo, rand()), 
					array('class' => 'view-modelo icon-eye-open', "style" => "float:right", 'onclick' => 'return open_dialog(this, "Visualizar Modelo", 600)')) ?>
		<?php echo $html->link(substr($descricao,0,20), 
					'javascript:void(0)', 
					array('class' => 'load-modelo', "mviacodigo" => $codigo)) ?>
	</li>
<?php endforeach; ?>