<table class='table table-striped sinistro tablesorter'>
	<thead>
	<?php foreach($servicos as $codigo_servico => $descricao ):?>
		<th class='input-small numeric '><?=$this->Html->link($descricao, 'javascript:void(0)') ?></th>
	<?php endforeach; ?>
	</thead>
	<tbody>
		<tr>
		<?php foreach($servicos as $codigo_servico => $descricao ):?>
			<td class="numeric">
			<?php
				$qtde = 0;
				foreach($dados_teleconsult as $key => $dados ):
					if( $dados['TipoOperacao']['codigo_servico'] == $codigo_servico ){
						$qtde = $dados[0]['qtd'];
						break;
					}
				endforeach;
				echo $qtde;
			?>
			</td>
		<?php endforeach; ?>
		</tr>
	</tbody>	
</table>
<?php echo $this->Javascript->codeBlock("jQuery('table.sinistro').tablesorter()")?>