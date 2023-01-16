<h3>Unidades Relacionadas:</h3>
    
    <?php if(!empty($clientes)):?>
        <table class="table table-striped">
            <thead>
                <tr>
                    <th class="input-mini">Código</th>
                    <th>Razão Social</th>
                    <th>CNPJ</th>
                    <th>Cidade</th>
                    <th>Estado</th>
                    <th class='input-mini'>Ações</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($clientes as $cliente): ?>
                <tr>
                    <td class="input-mini"><?= $cliente['Cliente']['codigo'] ?></td>
                    <td><?php echo $cliente['Cliente']['razao_social'] ?></td>
                    <td><?php echo $buonny->documento($cliente['Cliente']['codigo_documento']) ?></td>
                    <td><?php echo $cliente['ClienteEndereco']['cidade'] ?></td>
                    <td><?php echo $cliente['ClienteEndereco']['estado_abreviacao'] ?></td>
                    <td>
                        <?php echo $this->Html->link('', 'javascript:void(0)',array('class' => 'icon-trash', 'escape' => false, 'title'=>'Remover Unidade','onclick' => "excluir_unidades('{$cliente['UsuarioUnidade']['codigo']}')")); ?>
                    </td>
                </tr>
                <?php endforeach; ?>        
            </tbody>
        </table>
    <?php else:?>
        <div class="alert">Nenhum dado foi encontrado.</div>
    <?php endif;?> 

<?php echo $this->Javascript->codeBlock('
jQuery(document).ready(function(){
    $("[data-toggle=\"tooltip\"]").tooltip();
    $(document).on("click", ".dialog_cliente_usuario", function(e) {
        e.preventDefault();
        open_dialog(this, "Clientes", 990);
    });
});
		
function excluir_unidades(codigo){
		
	$.ajax({
		type: "POST",        
        url: "/portal/usuarios/usuario_unidade_excluir",
        dataType : "json",
        data: {
        	"codigo": codigo 
		},
		success : function(retorno){
			if(retorno) {
				atualizaListaUnidades();
			}
        },
		error : function(error){
        	alert("Erro! Não é possível excluir a Unidade!");
    	}
	});		
}		
		
function atualizaListaUnidades() {		
    var div = jQuery("div#usuario_unidades_lista");
		
    bloquearDiv(div);
    div.load(baseUrl + "usuarios/usuarios_unidades_listagem/'.$codigo_usuario.'/" + Math.random());
}   
');
?>