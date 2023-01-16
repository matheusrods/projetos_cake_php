<?php if($codigo_cliente < 1){ ?>
    <center><h2>Selecione um cliente</h2></center>
<?php } else if($jaRenovou == true) { ?>
<center>
<h3>Atenção</h3>
<h4>Vossa Empresa já efetuou o processo de RENOVAÇÃO AUTOMÁTICA neste mês.<br/>
    Qualquer dúvida, favor contatar-nos pelo fone: (11) 3443-2359. </h4>
</center>
<?php } else { ?>
<center>
<p>
<h3>RENOVAÇÃO AUTOMÁTICA</h3>
<h4>ATENÇÃO<br/>
Favor assinalar apenas os profissionais que <span style="color: red">não serão renovados</span>!</h4></p>
</center>
<p>Para sua maior comodidade e segurança, a renovação automática somente ocorrerá nas respectivas datas de vencimentos.<br/>
Abaixo listamos os profissionais cujos cadastros vencerão no <strong>período de <?php echo $dt_ini; ?> até <?php echo $dt_fim; ?></strong>.</p>
<?php echo $this->BForm->create('FichaScorecard', array('url' => array('controller' => 'fichas_scorecard', 'action' => 'fichas_a_renovar', $codigo_cliente ))); ?>
<table class="table table-striped" style='table-layout:fixed'>
    
    <thead>
        <tr>
            <th style="width:13px"></th>
            <th class='input-medium'>Nome</th>
            <th class='input-medium'>CPF</th>
            <th class='input-medium'>Tipo</th>
            <th class='input-small'>Vencimento</th>
        </tr>
    </thead>
    <tbody>
        <?php $total = 0; ?>
        <?php foreach($fichas as $f): ?>
        <tr>
            <td><input type="checkbox" name="excluir[]" value="<?php echo $f['ProfissionalLog']['codigo_profissional']; ?>"/></td>
            <td><?php echo $f['ProfissionalLog']['nome']; ?></td>
            <td><?php echo $buonny->documento($f['ProfissionalLog']['codigo_documento']);?></td>
            <td><?php echo $f['ProfissionalTipo']['descricao']; ?></td>
            <td><?php Comum::converteData( $f['FichaScorecard']['data_validade'] );?></td>
        </tr>
        <?php $total++; ?>
        <?php endforeach; ?>
    </tbody>
    <thead>
        <tr>
            <th colspan=5>Total de Profissionais: <?php echo $total; ?></th>
        </tr>
    </thead>
</table>
</center>
<div class='form-actions'>
    <table>
        <tr>
            <td colspan=2><strong>Dados para retorno</strong></td>
        </tr>
        <tr>
            <td><?php echo $this->BForm->input("contato",array('class'=>'inline')); ?></td>
            <td><?php echo $this->BForm->input("email",array('class'=>'inline')); ?></td>
        </tr>
    </table>
<p><strong>Observação:</strong> Solicitamos, caso haja alteração nos dados cadastrais de algum profissional acima listado, o envio de sua respectiva ficha devidamente atualizada. Pelo não recebimento das fichas, entenderemos que não houveram alterações nos dados já informados e efetuaremos a atualização com base nos dados anteriores enviados.</p>
<p><strong>"O não envio desta relação implicará na renovação automática de todos os profissionais acima listados"</strong></p>
    <?php echo $this->BForm->submit('Enviar', array('div' => false, 'class' => 'btn btn-primary', 'name'=>'aprovar')); ?>
    <?= $html->link('Voltar', array('controller' => 'fichas_status_criterios', 'action' => 'resultados_pesquisa'), array('class' => 'btn','id'=>'button')); ?>
</div>
<?php echo $this->BForm->end(); ?>
<?php } ?>