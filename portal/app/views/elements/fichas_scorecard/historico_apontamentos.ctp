<?php $codigo_ficha = empty($codigo_ficha) ? '' : $codigo_ficha; 
//debug($ficha_scorecard_artigo_criminal);
?>

<div class='actionbar-right'>
  <?php echo $this->Html->link('Incluir', array('action' => 'incluir_artigos_criminais_pesq',$codigo_ficha, rand()), array('title' => 'Incluir', 'class' => 'btn btn-success'));?>
</div>
<?php 
if (!empty($this->data['Profissional']['codigo_documento'])){
?>

<table  class="table table-condensed table-striped" >
<tr><th style="background-color:#DBEAF9;" class="input-large" colspan="13" align="center" >Artigo Criminal do Profissional CPF: <?php echo strtoupper(COMUM::formatarDocumento($this->data['Profissional']['codigo_documento'])); ?></th>
</tr>
<?php
if (!empty($ficha_scorecard_artigo_criminal)){
?>
<tr><th style="background-color:#DDDDDD" class="input-large" >N° Artigo</th> 
    <th style="background-color:#DDDDDD" class="input-large">Descrição</th>
    <th style="background-color:#DDDDDD" class="input-large" >Data</th>
    <th style="background-color:#DDDDDD" class="input-large" >Local</th>
    <th style="background-color:#DDDDDD" class="input-large">Inquérito</th>
    <th style="background-color:#DDDDDD" class="input-large">Processo</th>
    <th style="background-color:#DDDDDD" class="input-large">Observações</th> 
    <th style="background-color:#DDDDDD" class="input-large">Prestador</th> 
    <th style="background-color:#DDDDDD" class="input-large">Situação Processo</th> 
    <th style="background-color:#DDDDDD" class="input-large">Data de Inclusão</th> 
    <th style="background-color:#DDDDDD" class="input-large">Usuário</th> 
    <th style="background-color:#DDDDDD" class="input-large"></th>
    <th style="background-color:#DDDDDD" class="input-large"></th>
</tr>
    <?php foreach ($ficha_scorecard_artigo_criminal as $art) {
            //debug($art);
            print "<tr>";  
            print "<td>".$art['ArtigoCriminal']['nome']."</td>";
            print "<td>".$art['ArtigoCriminal']['descricao']."</td>";
            print "<td>".$art['FichaScorPesArtCriminal']['data_ocorrencia']."</td>";
            print "<td>".$art['FichaScorPesArtCriminal']['local_ocorrencia']."</td>";
            print "<td>".$art['FichaScorPesArtCriminal']['inquerito']."</td>";
            print "<td>".$art['FichaScorPesArtCriminal']['processo']."</td>";
            print "<td>".$art['FichaScorPesArtCriminal']['observacao']."</td>";
            print "<td>".$art['Prestador']['nome']."</td>";
            print "<td>".$art['SituacaoProcesso']['descricao']."</td>";
            print "<td>".$art['FichaScorPesArtCriminal']['data_inclusao']."</td>";
            print "<td>".$art['Usuario']['apelido']."</td>";
      ?>
        <td>
            <?php echo $html->link('', array('controller' => 'fichas_status_criterios', 'action' => 'editar_artigo_criminal_pesq', $codigo_ficha,$art['FichaScorPesArtCriminal']['codigo']), array('class' => 'icon-edit dialog', 'title' => 'editar')) ?>
        </td> 
        <td>   
            
            <?php echo $html->link('', array('controller' => 'fichas_status_criterios', 'action' => 'excluir_artigo_criminal_pesquisa',$art['FichaScorPesArtCriminal']['codigo'],$codigo_ficha), array('class' => 'icon-trash', 'title' => 'Excluir Artigo Criminal do Profissional ?'), 'Confirma exclusão?'); ?>
        </td>   
      <?php       
            print "</tr>";
         }
}else {
       
        print "<tr>";  
        print "<td colsopan='5'>Profissional sem ocorrências de artigos criminais.</td>";
        print "</tr>";
}
}
?>
</table>



<?php  
 //debug($proprietario_bi_negativacao);
 //debug($proprietario_car_negativacao);
 //debug($proprietario_vei_negativacao);
 
?>


