<?php if(isset($prestadores) && count($prestadores) == 0 ){ ?>
<div class='alert alert-warning'><strong>Não há registros encontrados para os critérios pesquisados.</strong></div>
<?php }else if(isset($prestadores) && count($prestadores) > 0 ){ ?>
<?php echo $paginator->options(array('update' => 'div.lista')); ?>
<table class="table table-striped prestadores-table">
    <thead>
        <tr>
            <th><?php echo $this->Paginator->sort('Prestador', 'nome') ?></th>
            <th class='input-medium'><?php echo $this->Paginator->sort('CPF/CNPJ', 'codigo_documento') ?></th>
            <th><?php echo $this->Paginator->sort('Contato', 'contato') ?></th>
            <th><?php echo $this->Paginator->sort('Endereço', 'endereco') ?></th>
            <th><?php echo $this->Paginator->sort('Bairro', 'bairro') ?></th>
            <th><?php echo $this->Paginator->sort('Cidade', 'cidade') ?></th>
            <th><?php echo $this->Paginator->sort('CEP', 'cep') ?></th>            
            <th></th>
            <th></th>            
        </tr>
    </thead>
    <tbody>
        <?php foreach ($prestadores as $prestador): ?>
        <tr class="prestadores-tr" codigo="<?php echo $prestador['Prestador']['codigo'] ?>">
            <td><?= $prestador['Prestador']['nome'] ?></td>
            <td><?= comum::formatarDocumento($prestador['Prestador']['codigo_documento']) ?></td>
            <td><?= str_replace('|', "</BR>", $prestador['Prestador']['contato']) ?></td>
            <td><?= $prestador['Prestador']['endereco']?></td>
            <td><?= (trim($prestador['Prestador']['numero']) != '') ? $prestador['Prestador']['endereco'].','.$prestador['Prestador']['numero'] : $prestador['Prestador']['endereco']; ?></td>
            <td><?= $prestador['Prestador']['bairro'] ?></td>
            <td><?= (trim($prestador['Prestador']['estado']) != '' ? $prestador['Prestador']['cidade'].'-'.$prestador['Prestador']['estado'] : $prestador['Prestador']['cidade']);?></td>
            <td><?= $prestador['Prestador']['cep'] ?></td>            
            <td class="pagination-centered">
                <?= $html->link('', array('action' => 'editar', $prestador['Prestador']['codigo']), array('class' => 'icon-edit', 'title' => 'Editar')) ?>
            </td>
            <td>
                <?php echo $html->link('', array('action' => 'excluir', $prestador['Prestador']['codigo']), array('class' => 'icon-trash', 'title' => 'Excluir'), 'Confirma exclusão?'); ?>
            </td>
        </tr>
        <?php endforeach; ?>        
    </tbody>
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
<?php echo $this->Js->writeBuffer(); 
} ?>