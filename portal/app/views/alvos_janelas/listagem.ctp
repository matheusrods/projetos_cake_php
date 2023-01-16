<?php 
if(!empty($codigo_cliente_pjur) && !empty($referencia)): ?>
	<?php
	    $message = $this->Buonny->flash();
	    $session->delete('Message.flash');    
	    if (!empty($message)) {
	        echo "<div class='message'>". $message ."</div>";
	        echo $this->Javascript->codeBlock("jQuery('div.message').html('".$message."'); jQuery('div.message').delay(4000).animate({opacity:0,height:0,margin:0}, function(){ jQuery(this).slideUp() })");
	    }    
	?>
	<?php if (count($janelas) > 0): ?>
		<table class='table table-striped'>
			<thead>
				<th>Janela Início</th>
				<th>Janela Fim</th>
				<th class="input-mini"></th>
			</thead>

			<tbody>
			
				<?php foreach ($janelas as $janela): ?>
				<tr>
					<td><?php echo $janela['TCajaConfAlvoJanela']['caja_janela_inicio'] ?></td>
					<td><?php echo $janela['TCajaConfAlvoJanela']['caja_janela_fim'] ?></td>
					<td class="pagination-centered">
						<?php echo $this->Html->link('', array(
						'action' => 'excluir', $janela['TCajaConfAlvoJanela']['caja_codigo'], rand()), array('title' => 'Remover Janela', 'class' => 'icon-trash'));?>
					</td>
				</tr>
			<?php endforeach ?>			
			
			
		</tbody>
		</table>
	<?php else:  ?>
		<div class="alert">
        	Nenhum registro encontrado.
    	</div>
	<?php endif; ?>
		<?php echo $this->Javascript->codeBlock('
			$(function(){
				$(".lista-janelas a.icon-trash").click(function(){
					var div = jQuery("div.lista-janelas");
            		bloquearDiv(div);
					if(confirm("Deseja remover este registro?")){
						$.ajax({
							url:$(this).attr("href"),
							dataType: "html",
							success: function(data){
								atualizaListaConfiguracaoAlvoJanela();
							}
						});						
					}
					return false;
				});
			});
		');
		?>
<?php else: ?>	
	<div class="alert">
    	Nenhum registro encontrado.
	</div>
<?php endif; ?>