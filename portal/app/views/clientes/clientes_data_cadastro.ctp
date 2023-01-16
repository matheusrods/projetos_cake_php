<div class="form-procurar">
    <?php echo $this->element('/filtros/clientes_data_cadastro'); ?>
</div>

<div class="evt-navbar" style="display:none">
    <ul class="nav nav-tabs">
    <!--  <li class="active"><a href="#clientes_dados_gerais" data-toggle="tab">Clientes</a></li>-->
      <li class="active"><a href="#clientes_produtos" data-toggle="tab">Produtos</a></li>
      <li><a href="#clientes_contatos" data-toggle="tab">Contatos</a></li>
      <li><a href="#clientes_enderecos" data-toggle="tab">Endereços</a></li>
    </ul>

    <div class="actionbar-right">
        <a href="#" class="btn evt-botao-voltar" title="Voltar a listagem completa">
            <i class="icon-arrow-left"></i>
        </a>
    </div>
</div>

<div class="lista_clientes tab-pane active" id="clientes_dados_gerais"></div>

<div class="tab-content">
    <div class="active lista_produtos tab-pane" id="clientes_produtos"></div>
    <div class="lista_contatos tab-pane" id="clientes_contatos"></div>
    <div class="lista_enderecos tab-pane" id="clientes_enderecos"></div>
</div>

<?php echo $this->Javascript->codeBlock("
    $(document).ready(function(){
        $('#limpar-filtro').bind('click', function(){
            bloquearDiv(jQuery('.form-horizontal'));
            $('.form-procurar').load(baseUrl + '/filtros/limpar/model:ClienteData/element_name:clientes_data_cadastro/' + Math.random())
        });
        
        $('a.evt-botao-voltar').bind('click', function() {
            $('table tr.evt-carregar-dado').off('click', '**');
            atualizaListaClientesDataCadastro();
            $('div.evt-navbar, div.tab-content').hide();
            $('tr.evt-carregar-dado, a').show();
        });

        $(document).on('click', 'a.evt-carregar-dado', function() {
            var codigo_cliente = $(this).parent().parent().prop('id');
            $(this).parents().siblings('tr').hide();
            $(this).hide();
            $('.evt-navbar, div.tab-content').show();
            
            atualizaListaProdutosClientesDataCadastro(codigo_cliente);
            atualizaListaContatosClientesDataCadastro(codigo_cliente);
            atualizaListaEnderecosClientesDataCadastro(codigo_cliente);
        });

});", false);
?>