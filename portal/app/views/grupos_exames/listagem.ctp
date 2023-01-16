<?php if(!empty($grupos_exames)): ?>
    <table class="table table-striped">
        <thead>
            <tr>
                <th>Descricao</th> 
                <th style='width:55px'>Ações</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach($grupos_exames as $codigo_exame => $grupo_exame): ?>
            <tr>
                <td><?php echo $grupo_exame['descricao'] ?></td>
                <td class="pagination-centered">
                    <?php echo $this->Html->link('', '#', array('class' => 'icon-trash','onclick' => 'excluir_exame('.$grupo_exame['codigo_detalhe_grupo_exame'].','.$grupo_exame['codigo_exame'].')','title' => 'Excluir Exame do Grupo')) ?>
                </td>
            </tr>
            <?php endforeach; ?>        
        </tbody>
    </table>
<?php else:?>
    <div class="alert">Nenhum dado foi encontrado.</div>
<?php endif; ?>
<?php echo $this->Javascript->codeBlock('
    function excluir_exame(codigo_detalhe, codigo_exame){
        codigo_cliente = $("#codigo_cliente").val();
        $.ajax({
            type: "POST",
            url: "/portal/grupos_exames/exclui_exames_grupo/" + codigo_detalhe + "/" + codigo_exame + "/" + codigo_cliente,
            beforeSend: function() {},
            sucess: function() {},
            complete: function() {
                var div = jQuery(".lista");
                bloquearDiv(div);
                div.load(baseUrl + "/grupos_exames/listagem/" + Math.random());
            }
        });
    }
', false);
?>