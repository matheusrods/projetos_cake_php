<div class="modal-dialog modal-sm" style="position: static;">
	<div class="modal-content">
		<div class="modal-header" style="text-align: center;">
			<h3>Reverte Baixa</h3>
		</div>
		<div class="modal-body" style="min-height: 295px;">
			<div>
				<b>Pedido Número:</b> : <?php echo $itens_pedidos_exames[0]['PedidoExame']['codigo']; ?><br>
				<b>Funcionário:</b> : <?php echo $itens_pedidos_exames[0]['Funcionario']['nome']; ?>
			</div>
			<h5> Baixas lançadas hoje que serão revertidas: </h5>
			<table class="table table-borded">
			    <thead>
			        <tr>
			            <th><input type="checkbox" name="" class='all'></th>
			            <th>Exame</th>
			            <th>Resultado</th>
			            <th>Data da Baixa</th>			            
			        </tr>
			    </thead>
			    <tbody>
			        <?php 
			        	foreach ($itens_pedidos_exames as $item): 

			        		/* Dados Exame */ 
			        		$Exame = $item['Exame'];			        		
			        		$NomeExame = $Exame['descricao'];

			        		$Resultado = "";

			        		/* Dados do Item */ 
			        		$ItemPedidoExameBaixa = $item['ItemPedidoExameBaixa'];			        		

			        		if(!empty($ItemPedidoExameBaixa['resultado'])){
			        			$Resultado = ( $ItemPedidoExameBaixa['resultado'] == 1 ? 'Normal' : 'Alterado' );			  
			        		}

			        		$CodigoItemBaixa = $ItemPedidoExameBaixa['codigo_itens_pedidos_exames'];
			        		$DataBaixa = $ItemPedidoExameBaixa['data_realizacao_exame'];			    
			        ?>
			        <tr>
			            <td><input type="checkbox" class="item-exame" value='<?php echo $CodigoItemBaixa ?>'></td>
			            <td><?php echo $NomeExame ?></td>
			            <td><?php echo $Resultado ?></td>
			            <td><?php echo $DataBaixa ?></td>
			        </tr>
			        <?php endforeach; ?>        
			    </tbody>
			</table>

		</div>
	    <div class="modal-footer">

	    	<H5>Você confirma a reversão da(s) baixa(s) ? </H5>

	    	<div class="right">
				<a href="#" class="btn btn-danger cancel">CANCELAR</a>
				<a href="#" class="btn btn-success save">Reverter Selecionados</a>
			</div>
		</div>
	</div>
</div>

<script type="text/javascript">
$(document).ready(function() {

	// Inicio: Eventos Para botões
	// Fechar Modal
	$('.btn.cancel').unbind('click').click(function(){
		close();
		return false;		
	})
	// Salvar Formulário
	$('.btn.save').unbind('click').click(function(){
		_reverte_baixa( "<?php echo $codigo_pedidos_exames ?>" )
		return false;		
	})
	// Fim: Eventos Para botões

	// Selecionar todos os itens
	$('.all').unbind('click').click(function(){
		var table = $(this).parents('table:eq(0)');
		if( $(this).is(':checked') ){
			table.find('input[type=checkbox]').prop('checked','checkbox');
		} else {
			table.find('input[type=checkbox]').prop('checked','');
		}
	}).click();
	
});

// Fechar Modal
function close(){
	$(".modal").modal("hide");
	$(".modal").html("");
}
	
// Reverter Baixa
function _reverte_baixa(codigo) {

	// Verifica se tem alguma baixa selecionada
	if( !$('.item-exame:checked').length ){
		swal({
		  title: "Atenção!",
		  text: "Nehuma baixa selecionada!",
		  icon: "warning",
		  button: "Ok",
		});
		return false;
	}

	// Pega itens selecionados
	var itens = [];
	$('.item-exame:checked').each(function(){
		itens.push( $(this).val() )
	})

	swal(
        {
        title: "Reverter Baixa",
        text: "Deseja realmente reverter a baixa deste pedido?",
        type: "warning",
        buttons: true,
        dangerMode: true,
        showCancelButton: true,
        }, 
    function(isConfirm) {
          if(isConfirm) {
                
                $.ajax(
                {
                  type: "POST",
                  url: baseUrl + "itens_pedidos_exames_baixa/reverte_baixa/" + codigo + "/" + Math.random(),
                  data: { "itens" : itens } , // Passa os itens que serão revertidos
                    beforeSend: function(){
                     bloquearDiv($("div.lista"));  
                    },
                    success: function(data){
                        if(data == 1){
                            atualizaLista();
                            $("div.lista").unblock();
                            viewMensagem(1,"Baixa de pedido revertida com sucesso!");
                        } else if (data == 0){
                            atualizaLista();
                            $("div.lista").unblock();
                            viewMensagem(0,"Não foi possível reverter a baixa do pedido!");
                        }else{
							atualizaLista();
                            $("div.lista").unblock();
                            swal("Atenção!",
							"Os exames em questão estão vinculados a uma nota fiscal finalizada, favor contatar a equipe de contas médicas:" + data,
							'warning');
						}
                        close()
                    },
                    error: function(erro){
                        $("div.lista").unblock();
                         viewMensagem(0,"Não foi possível reverter a baixa do pedido!");
                         close()
                    }
                });
                
            }
    });  
}

</script>