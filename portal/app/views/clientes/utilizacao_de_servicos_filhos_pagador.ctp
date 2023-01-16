    <div class='well'>
        <strong>Código: </strong><?= $cliente['Cliente']['codigo'] ?> - 
        <strong>Cliente: </strong><?= $cliente['Cliente']['razao_social'] ?><br>
        <strong>Produto: </strong><?= $produto['Produto']['descricao'] ?>
    </div>

    <div class='row-fluid' style='overflow-x:auto'>
        <table class="table table-striped table-bordered">
            <thead>
                <tr><th class='input-small'><?= $this->Html->link('Serviço', 'javascript:void(0)') ?></th>
                    <th class='input-small numeric'><?= $this->Html->link('Quantidade', 'javascript:void(0)') ?></th>
                    <th class='input-small numeric'><?= $this->Html->link('Valor', 'javascript:void(0)') ?></th>
                </tr>
            </thead>
            <tbody>

                <?php if ($utilizacoes_assinatura): ?>
                    <?php foreach($utilizacoes_assinatura as $utilizacao): ?>
                        <tr>
                            <td><?= $utilizacao[0]['servico_descricao'] ?></td>
                            <td class="numeric"><?= $utilizacao[0]['qtd'] > 0 ?  number_format($utilizacao[0]['qtd'],0) : '' ?></td>
                            <td class="numeric"><?= $utilizacao[0]['valor_assinatura'] > 0 ? number_format($utilizacao[0]['valor_assinatura'],2,',','.') : '0,00' ?></td>
                            
                        </tr>
                    <?php endforeach; ?>
                <?php endif ?>
            </tbody>
            <tfoot>
                  
            </tfoot>
        </table>
    </div>
    <?php $this->addScript($this->Buonny->link_css('tablesorter')); ?>
    <?php $this->addScript($this->Buonny->link_js('jquery.tablesorter.min')); ?>