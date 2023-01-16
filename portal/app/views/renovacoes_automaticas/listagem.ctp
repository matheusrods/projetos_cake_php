<?if( $listagem ): ?>
<?php echo $this->BForm->create('RenovacaoAutomatica', array('url' => array('controller' => 'renovacoes_automaticas', 'action' => 'atualizar' ))); ?>
<table class="table table-striped">
    <thead>
        <tr>
            <th class='input-mini'>Não Renovar</th>
            <th class='input-medium'>Nome</th>
            <th class='input-medium'>CPF</th>
            <th class='input-medium'>Tipo do Profissional</th>
            <th class='input-small'>Vencimento</th>
            <th class='input-small'>Renovar</th>
            <th class='input-small'>Processado</th>
            <th class='input-small'>Data inclusão</th>
            <th class='input-small'>Usuário</th>
        </tr>
    </thead>
    <tbody>
    <?php $total = 0; ?>    
        <?php foreach($listagem as $key => $ficha ): ?>
        <tr>
            <td>
                <?php echo $this->BForm->input('RenovacaoAutomatica.codigo.'.$ficha['RenovacaoAutomatica']['codigo'], 
                    array(
                        'type' =>'checkbox', 
                        'label'=>false, 
                        'checked' =>($ficha['RenovacaoAutomatica']['renovar'] != TRUE ? TRUE : FALSE)
                )); ?>
            </td>
            <td><?php echo $ficha['ProfissionalLog']['nome']; ?></td>
            <td><?php echo $buonny->documento($ficha['ProfissionalLog']['codigo_documento']);?></td>
            <td><?php echo $ficha['ProfissionalTipo']['descricao']; ?></td>
            <td><?php echo $ficha['RenovacaoAutomatica']['data_validade_ficha'];?></td>
            <td><?php echo $ficha['RenovacaoAutomatica']['renovar']    ? 'Sim': 'Não';?></td>
            <td><?php echo $ficha['RenovacaoAutomatica']['processado'] ? 'Sim': 'Não';?></td>
            <td><?php echo $ficha['RenovacaoAutomatica']['data_inclusao'];?></td>
            <td><?php echo $ficha['Usuario']['apelido'];?></td>
        </tr>            
        <?php $total++; ?>
        <?php endforeach; ?>
    
    </tbody>
    <thead>
        <tr>
            <th colspan="10">Total de Profissionais: <?php echo $total; ?></th>
        </tr>
    </thead>
</table>
<center>
    <? if( !empty($this->data['RenovacaoAutomatica']['codigo_cliente']) && $listagem ) : ?>    
    <div class='form-actions'>
        <?php echo $this->BForm->hidden("RenovacaoAutomatica.codigo_cliente") ?> 
        <?php echo $this->BForm->hidden("RenovacaoAutomatica.dias_renovacao") ?> 
        <?php echo $this->BForm->submit('Salvar', array('div' => false, 'class' => 'btn btn-primary', 'name'=>'salva_renovacao')); ?>
    </div>
    </center>
    <?php endif; ?>
<?php echo $this->BForm->end(); ?>
<?php endif; ?>