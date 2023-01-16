<table class='table table-striped tablesorter'>
    <thead>
        <th><?php echo $this->Html->link('Cód.', 'javascript:void(0)', array('title' => 'Código do Centro de Custo')) ?></th>
        <th><?php echo $this->Html->link('Centro de Custo', 'javascript:void(0)', array('title' => 'Descrição do Centro de Custo')) ?></th>
    </thead>

<?php if(!empty($dados)){ ?>
    <?php foreach ($dados as $item): ?>
        <tr>
            <td class="input-mini codCcusto"><?php echo $this->Html->link($item['CentroCusto']['codigo'], 'javascript:void(0)', array('title' => 'Clique para selecionar') ) ?></td>
            <td><?php echo $item['CentroCusto']['descricao'] ?></td>
        </tr>
    <?php endforeach ?>
<?php }else{ ?>
        <tr>
            <td colspan="2">Nenhum centro de custo encontrado.</td>
        </tr>
<?php } ?>
</table>
<?php echo $this->Javascript->codeBlock("jQuery(document).ready(function() {
    $('tr td.codCcusto a').click(function(){
        var codigo = $(this).text();
        $('#TranpagCcusto').val(codigo);
        $('#TranpagCcusto-search > span.ccusto').attr('id', codigo);
        $('#TranpagSubCodigo-search > span.ccusto').attr('id', codigo);
        $('#TranpagCodigoConta-search > span.ccusto').attr('id', codigo);
        close_dialog();
    })
})"); ?>