<?php if(!empty($codigo_cliente)) : ?>

    <?php 
    //verificacao para saber se multicliente onde precisa selecionar uma empresa
    $disabled = '';
    $msg = 'Cadastrar Regra';
    $href = "href='/portal/pda_config_regra/incluir_pda_regra/{$codigo_cliente}'";
    if(strpos($codigo_cliente,",")) {
        // $disabled = " disabled='disabled' style='pointer-events: none; cursor: default;'";
        $disabled = " disabled='disabled'";
        $href = '';
        $msg = "Necessário filtrar um dos clientes liberados.";
    }
    ?>
    <div class='actionbar-right'>
        <a <?php echo $href; ?> class="btn btn-success"  title="<?php echo $msg; ?>" <?php echo $disabled; ?>>
            <i class="icon-plus icon-white"></i> Incluir
        </a>
    </div>
<?php endif; ?>
<?php if(isset($dados_config) && count($dados_config)) : ?>
    <?php 
        echo $paginator->options(array('update' => 'div.lista')); 
        $total_paginas = $this->Paginator->numbers(); 
    ?>
        
    <table class="table table-striped">
        <thead>
            <tr>
                <th><?php echo 'Cód. Cliente' ?></th>
                <th><?php echo 'Razão Social' ?></th>
                <th><?php echo 'Nome Fantasia' ?></th>
                <th><?php echo 'Ferramenta' ?></th>
                <th><?php echo 'Descrição' ?></th>
                <th><?php echo 'Tema' ?></th>
                <th class="acoes" style="width:75px">Ações</th>
            </tr>
        </thead>
        <tbody>
            <?php
            foreach ($dados_config as $dados): ?>
                <tr>
                    <td><?php echo $dados['Cliente']['codigo'] ?></td>
                    <td><?php echo $dados['Cliente']['razao_social'] ?></td>
                    <td><?php echo $dados['Cliente']['nome_fantasia'] ?></td>
                    <td><?php echo $dados['PosFerramenta']['descricao'] ?></td>
                    <td><?php echo $dados['PdaConfigRegra']['descricao'] ?></td>
                    <td><?php echo $dados['PdaTema']['descricao'] ?></td>
                    <td>
                        <?php echo $this->Html->link('', array('action' => 'editar_pda_regra', $dados['PdaConfigRegra']['codigo'] ), array('class' => 'icon-edit ', 'title' => 'Editar')); ?>
                    </td>
                </tr>
            <?php endforeach ?>
        </tbody>
        <tfoot>
            <tr>
                <td colspan = "10"><strong>Total</strong> <?php echo $this->Paginator->params['paging']['PdaConfigRegra']['count']; ?></td>
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

<?php echo $this->Javascript->codeBlock('
    function atualizaLista(){
        var div = jQuery("div.lista");
        bloquearDiv(div);
        div.load(baseUrl + "pda_config_regra/listagem_pda_regra/" + Math.random());
    }   
    '); 
?>