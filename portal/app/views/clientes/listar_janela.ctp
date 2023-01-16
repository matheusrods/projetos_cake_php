<?php
    $message = $this->Buonny->flash();
    $session->delete('Message.flash');    
    if (!empty($message)) {
        echo "<div class='message'>". $message ."</div>";
        echo $this->Javascript->codeBlock("jQuery('div.message').html('".$message."'); jQuery('div.message').delay(4000).animate({opacity:0,height:0,margin:0}, function(){ jQuery(this).slideUp() })");
    }    
?>
<?php if(!empty($this->data['Cliente']['codigo_cliente'])): ?>
	<div id="cliente" class='well'>
        <strong>Código: </strong><?= $cliente['Cliente']['codigo'] ?>
        <strong>Cliente: </strong><?= $cliente['Cliente']['razao_social'] ?>
    </div>
	<div class='actionbar-right'>
        <?php echo $this->Html->link('Incluir', array(
                'action' => 'adicionar_janela', 
                $this->data['Cliente']['codigo_cliente'], 
                rand()), 
                array(
                    'onclick' => 'return open_dialog(this, "Adicionar Janela", 560)', 
                    'title' => 'Adicionar Janela', 
                    'class' => 'btn btn-success',
                )
            );
        ?>
    </div>
	<table class='table table-striped'>
		<thead>
			<th>Janela Início</th>
			<th>Janela Fim</th>
			<th class="input-mini"></th>
		</thead>

		<tbody>
			<?php foreach ($janelas as $janela): ?>
			<tr>
				<td><?php echo $janela['TCcjaConfClienteJanela']['ccja_janela_inicio'] ?></td>
				<td><?php echo $janela['TCcjaConfClienteJanela']['ccja_janela_fim'] ?></td>
				<td class="pagination-centered">
					<?php echo $this->Html->link('', array('action' => 'excluir_janela', $this->data['Cliente']['codigo_cliente'], $janela['TCcjaConfClienteJanela']['ccja_codigo'], rand()), array('title' => 'Remover Janela', 'class' => 'icon-trash'));?>
				</td>
			</tr>
		<?php endforeach ?>
	</tbody>
	</table>
	<?php echo $this->Javascript->codeBlock('
		$(function(){
			$(".lista a.icon-trash").click(function(){
				if(confirm("Deseja remover este registro?")){
					$.ajax({
						url:$(this).attr("href"),
						dataType: "html",
						success: function(data){
							atualizaListaConfiguracaoJanela();
						}
					});
					
				}

				return false;
			});
		});

		function atualizaListaConfiguracaoJanela(){
            var div = jQuery("div.lista");
            bloquearDiv(div);
            div.load(baseUrl + "clientes/listar_janela/" + Math.random());
        }
	');
	?>
<?php endif; ?>
