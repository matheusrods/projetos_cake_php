<script src="/portal/js/underscore-131.js"></script>
<script src="/portal/js/backbone-091.js"></script>
<script src="/portal/js/gerenciar_clientes_produtos.js"></script>
<link href='/portal/css/gerenciar_clientes_produtos.css' rel='stylesheet' type='text/css'></link>


<?php echo $html->link("<< voltar para dados do cliente", array(
                'controller' => 'clientes', 
                'action' => 'editar',
                $codigo_cliente,
          ),array(
               'class' => 'link_voltar',
          )) ?>

<div class="gerenciar_produtos_title">
    <a href="#novo_produto/<?php echo $codigo_cliente;?>" class="gerenciar_produtos_title_novo gerenciar_produtos_novo">+ novo produto</a>    
    <h1>Produtos do cliente</h1>
</div>

<div class="gerenciar_produtos_wrapper">
    <div class="gerenciar_produtos_esq">
        <?php echo $this->element('gerenciar_clientes_produtos/produtos', array(
            'produtos' => $cliente_produto_servico_profissionais
        ));
        ?>      
    </div>
   
    <div class="gerenciar_produtos_dir">
    </div>
</div> 

<script language="javascript">
    new RouteGerenciarClientesProdutos();
    Backbone.history.start();
</script>