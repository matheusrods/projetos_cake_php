<table class="table table-striped">
    <thead>		
        <tr> 
            <th>Entrada</th>
            <th>Saída</th>
            <th></th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($escala as $key => $dados ):?>
        <tr>		
            <td><?= $dados['UsuarioEscala']['data_entrada'] ?></td>
            <td><?= $dados['UsuarioEscala']['data_saida'] ?></td>
            <td>
                <?php echo $html->link('', 'javascript:void(0)', array('class' => 'icon-trash excluir-usuario-escala', 'title' => 'Excluir', 'onclick' => "excluir_usuario_escala({$dados['UsuarioEscala']['codigo']});")) ?>
            </td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>
<?php echo $this->Javascript->codeBlock("

    function excluir_usuario_escala( codigo ){
        if (confirm('Deseja realmente excluir ?')){
            jQuery.ajax({
                type: 'POST',
                url: baseUrl + 'usuarios_escalas/excluir/' + codigo + '/' + Math.random(),
                success: function(data) {
                    carrega_escala(".$codigo_usuario.");                    
                }
            });
        }
        function carrega_escala(codigo_usuario){
            var div = $('#escalas');
            bloquearDiv(div);
            $.get(baseUrl + 'usuarios_escalas/carrega_usuario_escala/'+codigo_usuario+'/'+Math.random(),function(data){
                div.html(data);
                div.unblock();
            });
        }
    }", false);?>