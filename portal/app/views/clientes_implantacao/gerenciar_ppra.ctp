<div class='well'>
<?php 
    echo $bajax->form('GerenciarPPRA', array('autocomplete' => 'off', 'url' => array('controller' => 'clientes_implantacao', 'action' => 'index_ppra_ext', ), 'divupdate' => '.form-procurar'), false);
    echo $this->Buonny->input_codigo_cliente($this, 'codigo_cliente', 'Cliente', false,'GerenciarPPRA');
    echo $this->BForm->submit('Buscar', array('div' => false, 'class' => 'btn'));
    echo $this->BForm->end();
?>
</div>
<div style="float:right">
    <?php echo $html->link('Importar Dados PGR', array('controller' => 'importar', 'action' => 'importar_ppra', $this->data['Cliente']['codigo']), array('class' => 'btn btn-warning', 'title' => 'Importar PGR')); ?>
</div> 
<div class='lista'>
    <table class="table table-striped">
        <thead>
            <tr>
                <th>Código Cliente</th>
                <th>Razão Social</th>
                <th>Nome Fantasia</th>
                <th>Bairros</th>
                <th>Cidade</th>
                <th>Estado</th>
                <th>Funcionários Alocados</th>
                <th>Credenciado</th>
                <th>Status</th>
                <th>Ações</th>
            </tr>
        </thead>
        <tbody>
        <?php 
            foreach($lista_clientes_grupo as $dados) : 
            $print = false;

            //pega o codigo do gpra
            $codigo_gpra = (!empty($dados[0]['Gpra_codigo'])) ? $dados[0]['Gpra_codigo'] : '';
            $gpra_data_incio_vigencia = $dados[0]['vigencia'];

            if(isset($dados[0]['OrdemServico_status'])) {
            	switch ($dados[0]['OrdemServico_status']) {
            		case '1':
            			$status = "Execução";
            			$class = "badge badge-empty badge-info";
            			$destino = "";
            			break;
            		case '2':
            			$status = "Recebido";
            			$class = "badge badge-empty badge-info";
            			$destino = "";
            			break;
            		case '3':
            			$status = "Finalizado";
            			$class = "badge badge-empty badge-success";
            			$destino = "";
                        
                        $print = true;

            			break;
                    case '5':
                    
                        $status = "Processando";
                        $class = "badge badge-empty badge-warning";
                        $destino = "";

                        break;
            		default:
            			$status = "Pendente";
            			$class = "badge badge-empty badge-important";
            			$destino = $this->Html->link('', array('controller' => 'clientes_implantacao', 'action' => 'localizar_credenciado',  $dados[0]['Unidade_codigo'], $codigo_servico_ppra), array('class' => 'icon-search', 'title' => 'Localizar Fornecedor'));
            			break;
            	}            	
            } else {
            	$status = "Pendente";
            	$class = "badge badge-empty badge-important";
            	$destino = $this->Html->link('', array('controller' => 'clientes_implantacao', 'action' => 'localizar_credenciado',  $dados[0]['Unidade_codigo'], $codigo_servico_ppra), array('class' => 'icon-search', 'data-toggle' => 'tooltip', 'title' => 'Localizar Credenciado'));            	
            }           
            ?>
                <tr>
                    <td><?php echo $dados[0]['Unidade_codigo'];?></td>
                    <td><?php echo $dados[0]['Unidade_razao_social'];?></td>
                    <td><?php echo $dados[0]['Unidade_nome_fantasia'];?></td>
                    <td><?php echo $dados[0]['ClienteEndereco_bairro'];?></td>
                    <td><?php echo $dados[0]['ClienteEndereco_cidade'];?></td>
                    <td><?php echo $dados[0]['ClienteEndereco_estado_abrevia'];?></td>
                    <td>
                        <?php 
                            if(isset($qtd_funcionarios[$dados[0]['Unidade_codigo']])){
                                echo $qtd_funcionarios[$dados[0]['Unidade_codigo']];
                            }
                        ?>  
                    </td>
                    <td><?php echo isset($dados[0]['Fornecedor_razao_social']) ? $dados[0]['Fornecedor_razao_social'] : '---';?></td>
                    <td>
                        <?php echo $this->Html->link($status, 'javascript:void(0);', array('class' => $class, 'style' => 'cursor:default'));?></td>
                    <td>

                        <?php if($dados[0]['OrdemServico_status'] != 3): ?>

                            <?php if(!empty($dados[0]['Fornecedor_codigo'])): ?>

                                <?php echo $this->Html->link('', array('controller' => 'grupos_exposicao', 'action' => 'index', $dados[0]['Unidade_codigo']), array('class' => 'icon-wrench', 'data-toggle' => 'tooltip', 'title' => 'Gerenciar Grupo Exposição')); ?> &nbsp;

                            <?php else: ?>

                                <?php echo $this->Html->link('', array('controller' => 'clientes_implantacao', 'action' => 'localizar_credenciado', $dados[0]['Unidade_codigo'],$codigo_servico_ppra), array('class' => 'icon-wrench', 'data-toggle' => 'tooltip', 'title' => 'Gerenciar Grupo Exposição')); ?> &nbsp;

                            <?php endif; ?>
                                
                        <?php else: ?>
                            
                            <?php echo $this->Html->link('', array('controller' => 'grupos_exposicao', 'action' => 'index', $dados[0]['Unidade_codigo'], ), array('class' => 'input-small icon-eye-open', 'data-toggle' => 'tooltip', 'title' => 'Visualizar Gerenciar Grupo Exposição')); ?> &nbsp;
                            <?php echo $this->Html->link('', array('controller' => 'clientes_implantacao', 'action' => 'localizar_credenciado', $dados[0]['Unidade_codigo'],$codigo_servico_ppra,'ppra'), array('class' => 'icon-retweet', 'data-toggle' => 'tooltip', 'title' => 'Gerar nova Versão')); ?> &nbsp;

                        <?php endif; ?>

                        <?php if($dados[0]['OrdemServico_status'] == 5): ?>
                            <?php echo $this->Html->link('', array('controller' => 'prevencao_riscos_ambientais', 'action' => 'editar',  $this->data['Cliente']['codigo'], $dados[0]['Unidade_codigo'], $codigo_gpra), array('class' => 'icon-file', 'data-toggle' => 'tooltip', 'title' => 'Programa de Prevenção de Riscos Ambientais')); ?> &nbsp;
                        <?php endif;?>


                        <?php if($print) { ?>
                            <?php echo $this->Html->link('', 'javascript:void(0)', array('class' => 'icon-print', 'data-toggle' => 'tooltip', 'title' => 'Imprimir relatório PGR','onclick' => 'alertDataVigencia('.$dados[0]['Unidade_codigo'].','.$gpra_data_incio_vigencia.')')); ?> &nbsp;
                        <?php } else { ?>
                            <span class="icon-print opacity" data-toggle="tooltip" title="Opção indisponível">&nbsp;</span> &nbsp;
                        <?php } ?>
                        
                        <?php echo $this->Html->link('', array('controller' => 'grupos_homogeneos', 'action' => 'index', $dados[0]['Unidade_codigo'],'implantacao'), array('class' => 'icon-addgrupo', 'data-toggle' => 'tooltip', 'title' => 'Cadastrar Grupos Homogêneos', 'style' => 'color: #000; text-decoration: none;')); ?> &nbsp;

                        <?php echo $destino;?>
                    </td>
                </tr>
        <?php endforeach ?>
        </tbody>
    </table>
</div>
<?php if(!isset($ppra_ext)): ?>
<div class='form-actions well'>
    <?php echo $html->link('Concluido', array('controller' => 'clientes_implantacao', 'action' => 'atualiza_status', $this->data['Cliente']['codigo'], 'ppra', 'C' ), array('class' => 'btn btn-primary')); ?>
    <?php echo $html->link('Voltar', array('controller' => 'clientes_implantacao', 'action' => 'index'), array('class' => 'btn')); ?>
</div>
<?php endif; ?>
<script type="text/javascript">
    $(document).ready(function() {
                
        alertDataVigencia = function(codigo_unidade, data_vigencia){
            // console.log(data_vigencia);
            if(data_vigencia == 0) {
                swal("Importante","Favor inserir a data de vigência!", "error");
                return false;
            }

            //funcao para imprimir o relatorio com setor+cargo quando não tiver funcionário o mesmo ou imprimir o seto+cargo mesmo não tendo funcionários alocados.            
            verifica_setor_cargo_funcionario(codigo_unidade);
            
            return false;


            // var url = baseUrl + "/grupos_exposicao/imprimir_relatorio/"+codigo_unidade;
            // window.location.href = url;            
        };

        /**
         * [verifica_seto_cargo_funcionario description]
         *
         * funcao para apresentar o swall com a opcao de sim/nao
         * @param  {[type]} codigo_unidade [description]
         * @return {[type]}                [description]
         */
        verifica_setor_cargo_funcionario = function(codigo_unidade) {
            
            swal({
              title: "Imprimir documento com Setor e Cargo sem Funcionários?",
              text: "Setores e Cargos sem Funcionários sair no relatório!",
              type: "warning",
              showCancelButton: true,
              confirmButtonClass: "btn-danger",
              confirmButtonText: "Sim",
              cancelButtonText: "Não",
              closeOnConfirm: true,
              closeOnCancel: true
            },
            function(isConfirm) {
                if (isConfirm) {
                    //sim para imprimir os setores e cargos
                    var url = baseUrl + "/grupos_exposicao/imprimir_relatorio/"+codigo_unidade+"/0";
                    window.location.href = url;                    
                } 
                else {
                    //não para não imprimir setores e cargos que não tenha funcionarios
                    var url = baseUrl + "/grupos_exposicao/imprimir_relatorio/"+codigo_unidade+"/1";
                    window.location.href = url;
                }
            });

        }//fim verifica_seto_cargo_funcionario(codigo_unidade)

    });
</script>