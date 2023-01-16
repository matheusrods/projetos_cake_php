<?php if( $ja_renovou == true) : ?>
<center>
<h3>Atenção</h3>
<h4>Vossa Empresa já efetuou o processo de RENOVAÇÃO AUTOMÁTICA neste mês.<br/>
    Qualquer dúvida, favor contatar-nos pelo fone: (11) 3443-2359. </h4>
</center>
<?php else : ?>
    <?php if(count($listagem) > 0 ): ?>     
    <center>
    <p>
    <h3>RENOVAÇÃO AUTOMÁTICA</h3>
    <h4>ATENÇÃO<br/>
    Favor assinalar apenas os profissionais que <span style="color: red">não serão renovados</span>!</h4></p>
    </center>
    <p>
    Para sua maior comodidade e segurança, a renovação automática somente ocorrerá nas respectivas datas de vencimentos.<br/>
    <?php if(!empty($authUsuario['Usuario']['codigo_cliente'])) : ?>
        Abaixo listamos os profissionais cujos cadastros vencerão no mês Seguinte</strong>.
    <?php else: ?>    
        Abaixo listamos os profissionais cujos cadastros vencerão em <strong><?php echo $this->data['RenovacaoAutomatica']['dias_renovacao']?> dias</strong>.
    <?php endif; ?>
    </p>
    <?php echo $this->BForm->create('RenovacaoAutomatica', array('url' => array('controller' => 'renovacoes_automaticas', 'action' => 'incluir', $this->data['RenovacaoAutomatica']['codigo_cliente'] ))); ?>
    <table class="table table-striped">
        <thead>
            <tr>
                <th class='input-mini'></th>
                <th class='input-medium'>Nome</th>
                <th class='input-medium'>CPF</th>
                <th class='input-medium'>Tipo</th>
                <th class='input-small'>Vencimento</th>
            </tr>
        </thead>
        <tbody>
            <?php $total = 0; ?>

            <?php foreach($listagem as $ficha ): ?>
            <tr>
                <td>
                    <input type="checkbox" name="data[RenovacaoAutomatica][excluir_vinculo][]" value="<?php echo $ficha['ProfissionalLog']['codigo_profissional']; ?>"/>
                </td>
                <td><?php echo $ficha['ProfissionalLog']['nome']; ?></td>
                <td><?php echo $buonny->documento($ficha['ProfissionalLog']['codigo_documento']);?></td>
                <td><?php echo $ficha['ProfissionalTipo']['descricao']; ?></td>
                <td><?php Comum::converteData( $ficha['FichaScorecard']['data_validade'] );?></td>
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
        <? if( !empty($this->data['RenovacaoAutomatica']['codigo_cliente']) && $listagem ) : ?>
        <div class='form-actions'>
        <p><strong>Observação:</strong> Solicitamos, caso haja alteração nos dados cadastrais de algum profissional acima listado, o envio de sua respectiva ficha devidamente atualizada. Pelo não recebimento das fichas, entenderemos que não houveram alterações nos dados já informados e efetuaremos a atualização com base nos dados anteriores enviados.</p>
        <p><strong>"O não envio desta relação implicará na renovação automática de todos os profissionais acima listados"</strong></p>
            <?php echo $this->BForm->hidden("RenovacaoAutomatica.codigo_cliente") ?> 
            <?php echo $this->BForm->hidden("RenovacaoAutomatica.dias_renovacao") ?> 
            <?php echo $this->BForm->submit('Salvar', array('div' => false, 'class' => 'btn btn-primary', 'name'=>'salva_renovacao')); ?>    
        </div>
        <?php endif; ?>
    <?php echo $this->BForm->end(); ?>
    <?php endif; ?>
<?php endif; ?>