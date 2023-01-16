<div class='actionbar-right'>
  <?php echo $this->Html->link('<i class="icon-plus icon-white"></i> Incluir', array( 'controller' => $this->name, 'action' => 'incluir', $this->passedArgs[0], $this->passedArgs[1]), array('escape' => false, 'class' => 'btn btn-success', 'title' =>'Cadastrar Apontamento', 'onclick' => "return open_dialog(this, 'Adicionar Apontamento', 950)"));?>
</div>
<table class="table table-condensed table-striped" >
    <thead>
        <th>N° Artigo</th> 
        <th>Descrição</th>
        <th>Data</th>
        <th>Local</th>
        <th>Inquérito</th>
        <th>Processo</th>
        <th>Observações</th> 
        <th>Prestador</th> 
        <th>Situação Processo</th> 
        <th>Data de Inclusão</th> 
        <th>Usuário</th> 
        <th></th>
        <th></th>
    </thead>
    <tbody>
     <?php if (!empty($ficha_scorecard_artigo_criminal)): ?>
        <?php foreach ($ficha_scorecard_artigo_criminal as $art): ?>
            <tr>   
                <td><?= $art['ArtigoCriminal']['nome'] ?></td>
                <td><?= $art['ArtigoCriminal']['descricao'] ?></td>
                <td><?= $art['FichaScorecardArtCriminal']['data_ocorrencia'] ?></td>
                <td><?= $art['FichaScorecardArtCriminal']['local_ocorrencia'] ?></td>
                <td><?= $art['FichaScorecardArtCriminal']['inquerito'] ?></td>
                <td><?= $art['FichaScorecardArtCriminal']['processo'] ?></td>
                <td><?= $art['FichaScorecardArtCriminal']['observacao'] ?></td>
                <td><?= $art['IPrestador']['nome'] ?></td>
                <td><?= $art['SituacaoProcesso']['descricao'] ?></td>
                <td><?= $art['FichaScorecardArtCriminal']['data_inclusao'] ?></td>
                <td><?= $art['Usuario']['apelido'] ?></td>
                <td class='action-icon'><?php echo $this->Html->link('', array( 'controller' => $this->name, 'action' => 'editar', $this->passedArgs[0], $this->passedArgs[1],$art['FichaScorecardArtCriminal']['codigo']), array( 'class'=>'icon-edit', 'title' =>'Editar Apontamento', 'onclick' => "return open_dialog(this, 'Editar Apontamento', 950)"));?></td>
                <td class='action-icon'><?php echo $this->Html->link('', array('controller' => $this->name, 'action' => 'excluir', $art['FichaScorecardArtCriminal']['codigo'],$this->passedArgs[1]), array('onclick' => 'return confirm("Confirma a exclusão de '.$art['ArtigoCriminal']['nome'] .'?")' ,'class' => 'icon-trash evt-excluir-servico', 'title' => 'Excluir')); ?></td>

            </tr>
        <?php endforeach ?>
    <?php endif ?>
    </tbody>
</table>
<?= $this->Buonny->link_js('autocomplete') ?>