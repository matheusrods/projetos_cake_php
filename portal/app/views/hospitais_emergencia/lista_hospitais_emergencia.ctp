<div class='actionbar-right'>
    <?php echo $this->Html->link('<i class="icon-plus icon-white"></i> Incluir', array( 'controller' => $this->name, 'action' => 'incluir', $codigo_cliente, $codigo_unidade), array('escape' => false, 'class' => 'btn btn-success', 'title' =>'Cadastrar Hospital Emergência'));?>
</div>
    <?php if(!empty($dados_hospitais)):?>
        <table class="table table-striped">
            <thead>
                <tr>
                    <th class="input-mini">Codigo</th>
                    <th>Nome do Hospital</th>
                    <th>CEP</th>
                    <th>Endereco</th>
                    <th>Número</th>
                    <th>Complemento</th>
                    <th>Bairro</th>
                    <th>Estado</th>
                    <th>Cidade</th>
                    <th class="acoes" style="width:52px">Ações</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($dados_hospitais as $dados): ?>
                    <tr>
                        <td class="input-mini"><?php echo $dados['HospitaisEmergencia']['codigo'] ?></td>
                        <td><?php echo $dados['HospitaisEmergencia']['nome'] ?></td>
                        <td><?php echo $dados['HospitaisEmergencia']['cep'] ?></td>
                        <td><?php echo $dados['HospitaisEmergencia']['logradouro'] ?></td>
                        <td><?php echo $dados['HospitaisEmergencia']['numero'] ?></td>
                        <td><?php echo $dados['HospitaisEmergencia']['complemento'] ?></td>
                        <td><?php echo $dados['HospitaisEmergencia']['bairro'] ?></td>
                        <td><?php echo $dados['HospitaisEmergencia']['estado'] ?></td>
                        <td><?php echo $dados['HospitaisEmergencia']['cidade'] ?></td>
                        <td style="width:50px;">
                            <?php echo $this->Html->link('', array('controller' => 'hospitais_emergencia', 'action' => 'editar', $dados['HospitaisEmergencia']['codigo'], $codigo_cliente, $codigo_unidade), array('class' => 'icon-edit', 'title' => 'Editar hospital de Emergência')); ?>
                        </td>
                    </tr>
                <?php endforeach ?>
            </tbody>
            <tfoot>
                <tr>
                    <td colspan = "15"><strong>Total</strong> <?php echo $this->Paginator->params['paging']['HospitaisEmergencia']['count']; ?></td>
                </tr>
            </tfoot>    
        </table>
    <?php else:?>
        <div class="alert">Nenhum dado foi encontrado.</div>
    <?php endif;?>
    
<div class='form-actions well'>
	<?php echo $html->link('Voltar', array('controller' => 'hospitais_emergencia', 'action' => 'index'), array('class' => 'btn btn-default')); ?>
</div>
  