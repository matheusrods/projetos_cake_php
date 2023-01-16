<?php if(isset($reguladores) && count($reguladores) == 0 ): ?>
    <div class='alert alert-warning'><strong>Nenhum Registro encontrado.</strong></div>
<?php elseif(isset($reguladores) && count($reguladores) > 0 ): ?>
    <?php echo $paginator->options(array('update' => 'div.lista')); ?>
    <table class="table table-striped reguladores-table">
        <thead>
            <tr>
                <th>Regulador</th>
                <th class='input-medium'>CPF/CNPJ</th>
                <th>Contato</th>
                <th>Endereço</th>
                <th>Bairro</th>
                <th>Cidade</th>
                <th>CEP</th>            
                <th></th>
                <th></th>            
            </tr>
        </thead>
        <tbody>
            <?php foreach ($reguladores as $regulador): ?>
            <tr class="reguladores-tr" codigo="<?php echo $regulador['Regulador']['codigo'] ?>">
                <td><?= $regulador['Regulador']['nome'] ?></td>
                <td><?= comum::formatarDocumento($regulador['Regulador']['codigo_documento']) ?></td>
                <td><?= $regulador[0]['contato']?></td>
                <td><?= (trim($regulador['ReguladorEndereco']['numero']) != '') ? $regulador['Endereco']['descricao'].','.$regulador['ReguladorEndereco']['numero'] : $regulador['Endereco']['descricao']; ?></td>
                <td><?= $regulador['EnderecoBairro']['descricao'] ?></td>
                <td><?= (trim($regulador['EnderecoEstado']['descricao']) != '' ? $regulador['EnderecoCidade']['descricao'].' - '.$regulador['EnderecoEstado']['descricao'] : $regulador['EnderecoCidade']['descricao']);?></td>
                <td><?= COMUM::formataCEP($regulador['EnderecoCep']['cep']) ?></td>            
                <td class="pagination-centered">
                    <?= $html->link('', array('action' => 'editar', $regulador['Regulador']['codigo']), array('class' => 'icon-edit', 'title' => 'Editar')) ?>
                </td>
                <td>
                    <?php echo $html->link('', array('action' => 'excluir', $regulador['Regulador']['codigo']), array('class' => 'icon-trash', 'title' => 'Excluir'), 'Confirma exclusão?'); ?>
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
    <?php echo $this->Js->writeBuffer(); ?>
<?php endif; ?>