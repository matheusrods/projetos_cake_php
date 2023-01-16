<?php if(!empty($medicos)):?>
    <?php echo $paginator->options(array('update' => 'div.lista')); ?>
    <table class="table table-striped">
        <thead>
            <tr>
            <th class="input-mini">Código</th>
            <th class="input-xlarge">Nome</th>
            <th class="input-mini">Conselho</th>
            <th class="input-medium">Número do Conselho</th>
            <th class="input-mini">Estado</th>
            <th class="acoes" style="width:75px">Ações</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($medicos as $medico): ?>
            <tr>
                <td class="input-mini"><?php echo $medico['Medico']['codigo'] ?></td>
                <td class="input-xlarge"><?php echo $medico['Medico']['nome'] ?></td>
                <td class="input-mini"><?php echo $medico['ConselhoProfissional']['descricao'] ?></td>
                <td class="input-medium"><?php echo Comum::soNumero($medico['Medico']['numero_conselho']);?></td>
                <td class="input-mini"><?php echo $medico['Medico']['conselho_uf'] ?></td>
                <td class="action-icon">
                    <?php echo $this->Html->link('', 'javascript:void(0)', array('onclick' => 'insereUsuarioConselho('.$codigo_usuario.', '.$medico['Medico']['codigo'].')', 'class' => 'icon-plus ', 'title' => 'Incluir')); ?>
                </td>
            </tr>
        <?php endforeach ?>
    </tbody>
        <tfoot>
            <tr>
                <td colspan = "10"><strong>Total</strong> <?php echo $this->Paginator->params['paging']['Medico']['count']; ?></td>
            </tr>
        </tfoot>    
    </table>
    <div class='row-fluid'>
        <div class='numbers span6'>
            <?php echo $this->Paginator->prev('Página Anterior', null, null, array('class' => 'disabled paginacao_anterior')); ?>
            <?php echo $this->Paginator->numbers(); ?>
            <?php echo $this->Paginator->next('Próxima Página', null, null, array('class' => 'disabled paginacao_proximo')); ?>
        </div>
        <div class='counter span7'>
            <?php echo $this->Paginator->counter(array('format' => 'Página %page% de %pages%')); ?>
            
        </div>
    </div>
    <?php echo $this->Js->writeBuffer(); ?>
<?php else:?>
    <div class="alert">Nenhum dado foi encontrado.</div>
<?php endif;?>

<?php 
echo $this->Javascript->codeBlock("

    $(document).ready(function() {
        $(\"[data-toggle='tooltip']\").tooltip();
    });

    function insereUsuarioConselho(codigo_usuario, codigo_medico){
        $.ajax({
            type: 'POST',
            url: '/portal/usuarios/usuario_conselho_incluir',
            dataType : 'json',
            data: {
                'codigo_usuario': codigo_usuario, 
                'codigo_medico': codigo_medico
            },
            success: function(retorno){
                if(retorno == 0){
                    alert('Erro! Não é possível inlcuir o conselho!');
                }
                else if(retorno == 1){
                    if($('#cliente-usuario-lista div.alert').length > 0){
                        $('#cliente-usuario-lista div.alert').remove();
                    }
		
                    atualizaLista(); 
                    atualizaListaConselho();      
					$('.ui-dialog-titlebar-close').click();
                }
                else{
                    alert('Conselho já cadastrado!');
                }
            },
            error: function(erro){
                alert('Erro! Não é possível incluir o conselho!');
            }
        });
    }

    function atualizaLista() {
        var div = jQuery('div.lista');
        bloquearDiv(div);
        div.load(baseUrl + 'usuarios/buscar_usuario_multi_conselho_listagem/' + Math.random());
    }

    function atualizaListaConselho() {		
        var div = jQuery('div#usuario_multi_conselho');
            
        bloquearDiv(div);
        div.load(baseUrl + 'usuarios/usuario_multi_conselho_listagem/$codigo_usuario/' + Math.random());
    }
");
?>