<?php if (empty($listar)): ?>
    <div class="alert">
        Nenhum registro encontrado.
    </div>
<?php else: 
    echo $this->Paginator->options(array('update' => 'div.lista')); 
    if($tipo_view !== '0'){ 
?>
<div class="well">
    <?php if(!empty($cliente)): ?>
        <strong>Código: </strong><?= $cliente['Cliente']['codigo'] ?>
        <strong>Cliente: </strong><?= $cliente['Cliente']['razao_social'] ?><br>
    <?php endif; ?>
    <strong>Última atualização:</strong> <?php echo date('d/m/Y H:i:s') ?> 
    <span class="pull-right">        
        <?php echo $this->Html->link('<i class="cus-page-white-excel"></i>', array( 'controller' => $this->name, 'action' => $this->action, 'export', $valor, $agrupamento), array('escape' => false, 'title' =>'Exportar para Excel'));?>
    </span>
</div>
<br>
<?php  } ?>
<div class='row-fluid'>
    <table class="table table-striped" style='table-layout:fixed'> 
        <thead>
            <tr>                
            <?php if($tipo_view === '0'){  ?>
                <th class="input-mini"><?php echo $this->Paginator->sort('Cliente', 'Tpecas.codigo_cliente') ?></th>
                <th class="input-medium"><?php echo $this->Paginator->sort('Data Cadastro', 'Tpecas.codigo_cliente') ?></th>
            <?php } ?>
                <th class="input-small"><?php echo $this->Paginator->sort('Local', 'Tpecas.local') ?></th>
                <th class="input-mini"><?php echo $this->Paginator->sort('DN', 'Tpecas.dn') ?></th>
                <th class="input-small"><?php echo $this->Paginator->sort('Transportador', 'Tpecas.transportador') ?></th>                
                <th class="input-medium"><?php echo $this->Paginator->sort('Número Peça', 'Tpecas.numero_peca') ?></th>
                <th class="input-small"><?php echo $this->Paginator->sort('Tipo Caixa', 'Tpecas.tipo_caixa') ?></th>
                <th class="input-small"><?php echo $this->Paginator->sort('Tipo de Avaria da Caixa', 'Tpecas.tipo_caixa_avaria') ?></th>
                <th class="input-small"><?php echo $this->Paginator->sort('Tipo da Peça', 'Tpecas.tipo_peca') ?></th>
                <th class="input-small"><?php echo $this->Paginator->sort('Tipo de Avaria da Peça', 'Tpecas.tipo_peca_avaria') ?></th>
                <th class="input-small"><?php echo $this->Paginator->sort('Destino', 'Tpecas.destino') ?></th>                
                <th class="input-mini"><?php echo $this->Paginator->sort('Data', 'Tpecas.data') ?></th>
                <th class="input-mini"></th>
            </tr>
        </thead>
        <tbody>
            <?php foreach($listar as $relatorio): 
                $relatorio = $relatorio['Tpecas'];
            ?>
                <tr>
                    <!-- <td><?= $relatorio['filename'] ?></td> -->
                    <?php if($tipo_view === '0'){ ?>
                        <td><?= $relatorio['codigo_cliente'] ?></td>
                        <td><?= $relatorio['data_inclusao'] ?></td>
                    <?php } ?>
                    <td><?= $relatorio['local'] ?></td>
                    <td><?= $relatorio['dn'] ?></td>
                    <td><?= $relatorio['transportador'] ?></td>
                    <td>
 					<span id="lista_peca" title="<?= $relatorio['filename'] ?>">
                    <?= $relatorio['numero_peca'] ?>
                    </span>
                    </td>
                    <td><?= $relatorio['tipo_caixa'] ?></td>
                    <td><?= $relatorio['tipo_caixa_avaria'] ?></td>
                    <td><?= $relatorio['tipo_peca'] ?></td>
                    <td><?= $relatorio['tipo_peca_avaria'] ?></td>
                    <td><?= $relatorio['destino'] ?></td>                    
                    <td><?= $relatorio['data'] ?></td>
                    <td>                    
                    <?php 
                    $arquivo = trim($relatorio['filename_pic']);
                    if (!empty($arquivo) && $arquivo != 'Sem Foto'){ 
                        echo $html->link('', '#myModal', array('class' => 'icon-picture link-imagem', 'role' => 'button', 'title' => 'Visualizar',  'data-toggle'=> 'modal', 'data-imagem' => $relatorio['filename_pic'] ));
                    } 
                     if($tipo_view === '0'){ 
                            echo $html->link('', array('action' => 'editar', $relatorio['codigo']), array('class' => 'icon-edit', 'title' => 'Editar'));
                            echo $html->link('', array('action' => 'excluir', $relatorio['codigo']), array('class' => 'icon-trash', 'title' => 'Excluir'), 'Confirma exclusão?'); 
                         } 

                    ?></td>
                </tr>
            <?php endforeach; ?>  
        </tbody>
        <tfoot>
            <tr>
                <th colspan="<?php echo $tipo_view === '0' ? 13 : 12?>">
                Total de Peças: <?php echo number_format($this->Paginator->params['paging']['Tpecas']['count'],0,'','.'); ?></th>
            </tr>
        </tfoot>
        
    </table>

   <div class='row-fluid'>
        <div class='numbers span6'>
            <?php echo $this->Paginator->prev('Página Anterior', null, null, array('class' => 'disabled paginacao_anterior')); ?>
          <?php echo $this->Paginator->numbers(); ?>
            <?php echo $this->Paginator->next('Próxima Página', null, null, array('class' => 'disabled paginacao_proximo')); ?>
        </div>
        <div class='counter span6'>
            <?php echo $this->Paginator->counter(array('format' => 'Página %page% de %pages%')); ?>
        </div>
    </div>
    <?php echo $this->Buonny->link_css('jquery.tablescroll'); ?>
    <?php echo $this->Buonny->link_js('jquery.tablescroll'); ?>
    <?php 
$tipo_view = 'popup';
    echo $this->Javascript->codeBlock("
        jQuery(document).ready(function(){
            $('.horizontal-scroll').tableScroll({width:3000, height:(window.innerHeight-".($tipo_view != 'popup' ? "380" : "220").")});

            $('.numbers a[id^=\"link\"]').bind('click', function (event) { bloquearDiv($('.lista')); });
        });", false);
    ?>
    <?php if($this->layout != 'new_window'): ?>
        <?php echo $this->Js->writeBuffer(); ?>
    <?php endif; ?>
<?php endif; ?>
<div id="myModal" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>    
  </div>
  <div class="modal-body" style="max-height: 700px">
    <img src="" id="imagem">
  </div>  
</div>
<?php $dir = '/portal/files/importacao_transyseg/'; ?>
<?= $javascript->codeBlock('jQuery(window).ready(function($) 
{ 
    $("span" , $(\'#lista_veiculo\')).tooltip();
    $(".link-imagem").click(function(){
        imagem = $(this).data("imagem");

        src = "'.$dir.'"+imagem;
        
        $("#imagem").attr("src", src);
    });
});'
    ) ?>