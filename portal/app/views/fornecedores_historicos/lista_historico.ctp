<table class="table table-striped">
  <thead>
    <tr>
        <th style="width:120px">Data</th>
        <th class="input-xxlarge">Observação</th>
        <th>Arquivo</th>
        <th class="input-medium">Usuário</th>
    </tr>
  </thead>
  <?php if(!empty($historicos)):?>
  <tbody>
	<?php foreach ($historicos as $historico): ?>
	  <tr>
	       <td><?php echo $historico['FornecedorHistorico']['data_inclusao'] ?></td>
           <td class="input-xxlarge"><?php echo $historico['FornecedorHistorico']['observacao'] ?></td>
	       <td>
                <?php if(!empty($historico['FornecedorHistorico']['caminho_arquivo'])): ?>
                    <a href="https://api.rhhealth.com.br<?php echo $historico['FornecedorHistorico']['caminho_arquivo']; ?>" target="_blank" class="icon-file btn-anexos visualiza_anexo" title='Visualizar Anexo'></a>
                <?php endif; ?>
            </td>
	      <td class="input-medium"><?php echo $historico['Usuario']['apelido'] ?></td>
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