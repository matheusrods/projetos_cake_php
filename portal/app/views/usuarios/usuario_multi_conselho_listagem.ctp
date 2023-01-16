<h3>Conselhos Relacionados:</h3>

<?php //debug($medicos); die; ?>
    
    <?php if(!empty($medicos)):?>
        <table class="table table-striped">
            <thead>
                <tr>
                    <th class="input-mini">Código</th>
                    <th>Nome</th>
                    <th>Conselho</th>
                    <th>Número do Conselho</th>
                    <th>Estado</th>
                    <th class='input-mini'>Ações</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($medicos as $medico): ?>
                <tr>
                    <td class="input-mini"><?= $medico['Medico']['codigo'] ?></td>
                    <td><?php echo $medico['Medico']['nome'] ?></td>
                    <td><?php echo $medico['ConselhoProfissional']['descricao'] ?></td>
                    <td><?php echo $medico['Medico']['numero_conselho'] ?></td>
                    <td><?php echo $medico['Medico']['conselho_uf'] ?></td>
                    <td>
                        <?php echo $this->Html->link('', 'javascript:void(0)',array('class' => 'icon-trash', 'escape' => false, 'title'=>'Remover Conselho','onclick' => "excluir_conselho('{$medico['UsuarioMultiConselho']['codigo']}')")); ?>
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
        open_dialog(this, "Profissionais", 990);
    });
});
		
function excluir_conselho(codigo){		
	$.ajax({
		type: "POST",        
        url: "/portal/usuarios/usuario_conselho_excluir",
        dataType : "json",
        data: {
        	"codigo": codigo 
		},
		success : function(retorno){
			if(retorno) {
				atualizaListaConselho();
			}
        },
		error : function(error){
        	alert("Erro! Não é possível excluir o conselho!");
    	}
	});		
}		
		
function atualizaListaConselho() {		
    var div = jQuery("div#usuario_multi_conselho");
		
    bloquearDiv(div);
    div.load(baseUrl + "usuarios/usuario_multi_conselho_listagem/'.$codigo_usuario.'/" + Math.random());
}   
');
?>