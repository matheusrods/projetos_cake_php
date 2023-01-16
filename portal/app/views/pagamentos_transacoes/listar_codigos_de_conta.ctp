<table class='table table-striped tablesorter'>
    <thead>
        <th><?php echo $this->Html->link('Cód.', 'javascript:void(0)', array('title' => 'Código da Conta')) ?></th>
        <th><?php echo $this->Html->link('Conta', 'javascript:void(0)', array('title' => 'Descrição da Conta')) ?></th>
    </thead>

<?php if(!empty($dados)){ ?>
    <?php foreach ($dados as $item): ?>
        <tr>
            <td class="input-mini codCcusto"><?php echo $this->Html->link($item['Sbflux']['codigo'], 'javascript:void(0)', array('title' => 'Clique para selecionar') ) ?></td>
            <td><?php echo $item['Sbflux']['descricao'] ?></td>
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
        $('#TranpagCodigoConta').val(codigo);
        $('#TranpagCcusto-search > span.codigo_conta').attr('id', codigo);
        $('#TranpagSubCodigo-search > span.codigo_conta').attr('id', codigo);
        $('#TranpagCodigoConta-search > span.codigo_conta').attr('id', codigo);
        close_dialog();
    })
})"); ?>