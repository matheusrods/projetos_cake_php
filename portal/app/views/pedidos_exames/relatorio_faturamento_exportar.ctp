</div>
<table class="table" style="width: inherit;">
	<thead>
		<tr>

			<?php 
			//monta as colunas
			foreach ($campos as $key => $desc_coluna) { 
				echo "<th>".$desc_coluna."</th>";
			}//fim foreach colunas
			?>
		</tr>
	</thead>
	<tbody>
		<?php
		
		$conversao = array(
			'á' => 'a','à' => 'a','ã' => 'a','â' => 'a', 'é' => 'e',
 			'ê' => 'e', 'í' => 'i', 'ï'=>'i', 'ó' => 'o', 'ô' => 'o', 'õ' => 'o', "ö"=>"o",
 			'ú' => 'u', 'ü' => 'u', 'ç' => 'c', 'ñ'=>'n', 'Á' => 'A', 'À' => 'A', 'Ã' => 'A',
 			'Â' => 'A', 'É' => 'E', 'Ê' => 'E', 'Í' => 'I', 'Ï'=>'I', "Ö"=>"O", 'Ó' => 'O',
 			'Ô' => 'O', 'Õ' => 'O', 'Ú' => 'U', 'Ü' => 'U', 'Ç' =>'C', 'Ñ'=>'N'
 		);

		if(!empty($dados)) {
			foreach ($dados as $key => $dado) { 

				echo "<tr>";
					//monta as colunas
					foreach ($campos as $index_col => $desc_coluna) { 
						echo "<td>". iconv("UTF-8", "ISO-8859-1", utf8_encode(strtoupper(strtr($dado[0][$index_col], $conversao))))."</td>\n";
					}//fim foreach colunas

				echo "</tr>";
			}
		}
		?>
	</tbody>
</table>

<div>
<div class="margin-top-15 margin-bottom-20">
	 <?php //echo $this->Html->link('', 'javascript:void(0)', array('class' => 'icon-trash', 'title' => 'Excluir', 'onclick' => "excluir_area_atuacao({$area_atuacao['AreaAtuacao']['codigo']})")) ?>
	<?php //echo $this->Html->link('Voltar', array('action' => 'relatorio_faturamento'), array('class' => 'btn btn-default'));; ?>
	
	<?php //echo $this->Html->link('Voltar', 'javascript:void(0)', array('class' => 'btn btn-default', 'onclick' => goBack())) ?>
	<a href="javascript:void(0);" onclick="goBack();" class="btn btn-default">Voltar</a>
</div>
<?php echo $this->Javascript->codeBlock('
$("#myTab a").click(function (e) {
e.preventDefault();
$(this).tab("show");
})
'); ?>

<script type="text/javascript">
	function goBack() {
  		window.history.back();
	}
</script>
