<table class="table table-striped">
  <thead>
    <tr>
        <th style="width:120px">Data</th>
        <th>Arquivo</th>
        <th>Usuário</th>
    </tr>
  </thead>
  <?php if(!empty($historicos)):?>
  <tbody>
	<?php foreach ($historicos as $historico): ?>
	  <tr>
	      <td><?php echo $historico['TipoRetorno']['descricao'] ?></td>
	      <td><?php echo $historico ?></td>
	      <td><?php echo $historico['TipoContato']['descricao'] ?></td>
	  </tr>
	<?php endforeach; ?>
	</tbody>
	<?php else:?>
            <tr>
                <td colspan="5">
                    <div>Nenhum dado foi encontrado.</div>
                </td>
            </tr>    
        <?php endif;?>
</table>
<?php echo $this->Javascript->codeBlock("
    $(document).ready(function(){
        setup_time();
        setup_mascaras();
    });

    // function excluirFornecedorContato(codigo){
    //     if (confirm('Deseja realmente excluir ?')){
    //         $.ajax({
    //             type: 'POST',        
    //             url: baseUrl + 'fornecedores_contatos/excluir/' + codigo +  '/' + Math.random(),        
    //             dataType : 'json',
    //             success : function(data){ 
    //                atualizaFornecedorHistorico();
    //             },
    //             error : function(error){
    //                 console.log(error);
    //             }
    //         }); 
    //     }
    // }
    function atualizaFornecedorHistorico(){
		var div = jQuery('#fornecedor-historico-lista');
		bloquearDiv(div);
		div.load(baseUrl + 'fornecedores_historicos/lista_historico/".$codigo_fornecedor."/' + Math.random());
	}
    ") 
?>