<div class='well'>
    <strong>Código: </strong><?php echo $this->Html->tag('span', $this->data['Cliente']['codigo']); ?>
    <strong>Cliente: </strong><?php echo $this->Html->tag('span', $this->data['Cliente']['razao_social']); ?>
</div>
<div class='lista'>
    <table class="table table-striped">
        <thead>
            <tr>
                <th class="input">Unidades</th>
                <th>Bairros</th>
                <th>Cidade</th>
                <th>Estado</th>
                <th>Credenciado</th>
                <th>Status</th>
                <th>Ações</th>
            </tr>
        </thead>
        <tbody>
            <?php
            // PD-154
            $Configuracao = &ClassRegistry::init('Configuracao');
            $codigo_servico_ppra = $Configuracao->getChave('CODIGO_ORDEM_SERVICO_PPRA');

            foreach($grupos_economicos as $dados): ?>
                <tr>
                    <td><?php echo $dados['Unidade']['codigo']."-".$dados['Unidade']['razao_social'];?></td>
                    <td><?php echo $dados['VClienteEndereco']['cliente_endereco_bairro'];?></td>
                    <td><?php echo $dados['VClienteEndereco']['cliente_endereco_cidade'];?></td>
                    <td><?php echo $dados['VClienteEndereco']['cliente_endereco_estado_abreviacao'];?></td>
                    <td><?php echo $dados['Fornecedor']['nome'];?></td>
                    <td>
	                    <?php switch ($dados['OrdemServico']['status_ordem_servico']) {
	                        case '1':
	                            $status = "Execução";
	                            $style = "btn btn-warning";
	                            $title = "Preencher PGR";
	                            $destino = $this->Html->link('', array('action' => 'editar'), array('class' => 'icon-wrench', 'title' => 'Preencher PGR')); 
	                            break;
	                        case '2':
	                            $status = "Recebido";
	                            $style = "btn btn-warning";
	                            $destino = $this->Html->link('', array('action' => 'editar'), array('class' => 'icon-wrench', 'title' => 'Preencher PGR')); 
	                            break;
	                        case '3':
	                            $status = "Finalizado";
	                            $style = "btn btn-success";
	                            $destino = "";
	                            break;
                            case '5':
                                $status = "Processando";
                                $style = "btn btn-warning";
                                $destino = "";
                                break;
	                        default:
	                            $status = "Pendente";
	                            $style = "btn btn-danger";
	                            $destino = $this->Html->link('', array('action' => 'localizar_credenciado', $dados['Unidade']['codigo'], $codigo_servico_ppra), array('class' => 'icon-wrench', 'title' => 'Localizar Fornecedor')); 
	                            break;
	                    }
						?>
                        <?php echo $html->link($status, 'javascript:void(0);', array('class' => $style));?></td>
                    <td><?php echo $destino;?></td>
                </tr>
        <?php endforeach ?>
        </tbody>
    </table>
</div>
<div class='form-actions well'>
    <?php echo $html->link('Voltar', array('controller' => 'clientes_implantacao', 'action' => 'index'), array('class' => 'btn')); ?>
    <?php echo $html->link('Concluido', array('controller' => 'clientes_implantacao', 'action' => 'atualiza_status_ppra', $this->data['Cliente']['codigo'], 'ppra', 'C' ), array('class' => 'btn btn-success')); ?>
</div>