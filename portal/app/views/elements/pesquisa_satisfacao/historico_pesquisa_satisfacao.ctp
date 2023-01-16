<table class='table table-striped' style="max-width:none;">
    <thead>
        <th class="input-medium">Data da Pesquisa</th>
        <th>Produto</th>
        <th class="input-mini">Status</th>
        <th class='action-icon'>&nbsp</th>
    </thead>
    <tbody>
        <?php foreach ($hitorico_pesquisas as $key => $dado):?>
            <?php $descricao_status = (!empty($dado['PesquisaSatisfacao']['codigo_status_pesquisa']) ? $status_pesquisa[$dado['PesquisaSatisfacao']['codigo_status_pesquisa']] : 'Sem pesquisa');?>
            <?php $cor_status = (!empty($dado['PesquisaSatisfacao']['codigo_status_pesquisa']) ? $cor_status_pesquisa[$dado['PesquisaSatisfacao']['codigo_status_pesquisa']] : '');?>
            <tr>
                <td><?= $dado['PesquisaSatisfacao']['data_pesquisa'];?></td>
                <td><?= ($dado['PesquisaSatisfacao']['codigo_produto'] == '1') ? 'Teleconsult' : 'Buonnysat';?></td>
                <td><?= "<span class='badge-empty badge badge-$cor_status' title='$descricao_status'></span>"?></td>                  
                <td class="action-icon">
                    <?php if(!empty($dado['PesquisaSatisfacao']['codigo_status_pesquisa'])):?>
                        <?php echo $this->Html->link('<i class="icon-search"></i>', array(
                        'action' => 'pesquisa_realizada',
                        $dado['PesquisaSatisfacao']['codigo']
                    ), array('escape' => false,'title' =>'Pesquisa','onclick' => "return open_dialog(this,'Pesquisa Realizada', 575)"));?>
                    <?php endif;?> 
                </td>
            </tr>
        <?php endforeach ?>
    </tbody>
</table>