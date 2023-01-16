<?php if( isset($dados) && !empty($dados) ): ?>

    <table class='table table-striped'>
        <thead>
            <th class="input-mini">Codigo</th>
            <th class="input-xlarge">Título</th>
            <th class="input-medium">Data Inicial</th>
            <th class="input-medium">Data Final</th>            
            <th class="numeric input-mini">Ações</th>
        </thead>
        <tbody>
            
            <?php foreach($dados as $value): ?>

                <tr>
                    <td><?php echo $value['MensagemDeAcesso']['codigo']; ?></td>
                    <td><?php echo $value['MensagemDeAcesso']['titulo']; ?></td>
                    <td><?php echo $value['MensagemDeAcesso']['data_inicial']; ?></td>
                    <td><?php echo $value['MensagemDeAcesso']['data_final']; ?></td>                    
                    <td class="numeric">
                        <?php  
                            echo $html->link('', array('action' => 'editar', $value['MensagemDeAcesso']['codigo']), array('class' => 'icon-edit', 'title' => 'Editar'));
                            echo '&nbsp;&nbsp;';
                            echo $html->link('', 'javascript:void(0)', array('class' => 'icon-trash', 'title' => 'Excluir', 'onclick' => "javascript:excluir_mensagem({$value['MensagemDeAcesso']['codigo']})"));
                        ?>
                    </td>
                </tr>

            <?php endforeach; ?>
            
        </tbody>
    </table>    
    <?php 
    echo $this->Javascript->codeBlock("
    function excluir_mensagem(codigo) {
        if (confirm('Deseja realmente excluir essa mensagem?'))
            location.href = '/portal/mensagens_de_acessos/excluir/' + codigo;
    }
    "); ?>
<?php else:?>
    <div class="alert alert-warning">Não foi encontrado nenhum registro.</div>
<?php endif; ?>