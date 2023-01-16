<?php //debug($clientes)?>

<?php if (!empty($clientes)):?>

    <table class="table table-striped">
        <thead>
        <tr>
            <th class="input-mini">Código</th>
            <th>Razão Social</th>
            <th>Nome Fantasia</th>
            <th >Documento</th>
            <th style="text-align: center" >Ações</th>
        </tr>
        </thead>
        <tbody>
        <?php foreach($clientes as $cliente_dados) :
            $cliente = $cliente_dados['Cliente'];
            ?>
            <tr>
                <td class="input-mini"><?php echo $cliente['codigo'] ?></td>
                <td><?php echo $cliente['razao_social'] ?></td>
                <td><?php echo $cliente['nome_fantasia'] ?></td>
                <td><?php echo $buonny->documento($cliente['codigo_documento']) ?></td>

                <td style="text-align: center">
                    <?php echo $this->Html->link('', array('controller' => 'clientes', 'action' => 'config_criticidade_cliente', $cliente['codigo']), array('class' => 'icon-cog', 'title' => 'Visualizar'));?>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>

<?php else:?>
    <div class="alert">Nenhum dado foi encontrado! Verificar se a configuração da assinatura está ativa.</div>
<?php endif;?>

<?php

echo $this->Javascript->codeBlock("
    function atualizaListaRiscosTipo() {   
        var div = jQuery('div.lista');
        bloquearDiv(div);
        div.load(baseUrl + 'riscos_tipos/listagem/' + Math.random());
    }
    
    function atualizaStatusRiscosTipo(codigo)
    {

        $.ajax({
            type: 'POST',
            url: baseUrl + 'riscos_tipos/editar_status/' + codigo,
            beforeSend: function(){
                bloquearDivSemImg($('div.lista'));  
            },
            success: function(data){           
                if(data == 1){
                    atualizaListaRiscosTipo();
                    $('div.lista').unblock();
                    viewMensagem(2,'Os dados informados foram armazenados com sucesso!');
                } else {
                    atualizaListaRiscosTipo();
                    $('div.lista').unblock();
                    viewMensagem(0,'Não foi possível mudar o status!');
                }
            },
            error: function(erro){
                $('div.lista').unblock();
                viewMensagem(0,'Não foi possível mudar o status!');
            }
        });
    }
    
    function fecharMsg()
    {
        setInterval(
            function(){
                $('div.message.container').css({ 'opacity': '0', 'display': 'none' });
            },
            4000
        );     
    }
    
    function gerarMensagem(css, mens)
    {
        $('div.message.container').css({ 'opacity': '1', 'display': 'block' });
        $('div.message.container').html('<div class=\"alert alert-'+css+'\"><p>'+mens+'</p></div>');
        fecharMsg();
    }
    
    function viewMensagem(tipo, mensagem){
        switch(tipo){
            case 1:
                gerarMensagem('success',mensagem);
                break;
            case 2:
                gerarMensagem('success',mensagem);
                break;
            default:
                gerarMensagem('error',mensagem);
                break;
        }    
    }
");
