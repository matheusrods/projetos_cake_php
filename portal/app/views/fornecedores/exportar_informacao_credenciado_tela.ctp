<ul class="nav nav-tabs" id="myTab">
	<li class="active"><a href="#info_credenciado">Informações do Credenciado</a></li>
</ul>

<div class="tab-content" style="width: 4050px;">
	<div class="tab-pane active" id="info_credenciado">
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
				foreach ($dados as $key => $dado) { 

					echo "<tr>";
						//monta as colunas
						foreach ($campos as $index_col => $desc_coluna) { 
							echo "<td>".$dado[0][$index_col]."</td>\n";
						}//fim foreach colunas

					echo "</tr>";
				}
				?>
			</tbody>
		</table>
	</div>
</div>

<div class="margin-top-15 margin-bottom-20">
	<?php echo $this->Html->link('Voltar', array('action' => 'info_credenciado'), array('class' => 'btn btn-default'));; ?>
</div>
<?php echo $this->Javascript->codeBlock('
$("#myTab a").click(function (e) {
e.preventDefault();
$(this).tab("show");
})
'); ?>
