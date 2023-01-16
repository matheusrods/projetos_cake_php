<h4>Enviados</h4>
        <table class="table table-striped">
            <thead>
                <tr>
                  <th class="input-xxlarge">Documento</th>
                  <th>Arquivo</th>
                  <th>Data de Validade</th>
                  <th></th>
                  <th></th>
                </tr>
            </thead>
            <tbody>
                <?php if(!empty($documentos_enviados)): ?>    
                    <?php foreach ($documentos_enviados as $enviados): ?>
                        <tr>
                            <?php if($enviados['FornecedorDocumento']['validade'] == "OK"):
                                $validade = '<span class="badge badge-empty badge-success"><i class="icon-white icon-ok-sign"></i> OK</span>';
                            else:
                                $validade = '<span class="badge badge-empty badge-important"><i class="icon-white icon-ok-sign"></i> Vencido</span>';
                            endif; ?>

                            <td class="input-xxlarge"><?php echo $enviados['TipoDocumento']['descricao'] ?></td>
                            <td>
                                <?php 
                                    //declara o codigo do documento como o codigo do fornecedor arquivo
                                    // $enviados[$key]['codigo_documento'] = $doc['FornecedorDocumento']['codigo_fornecedor'];
                                    
                                    $link = "";

                                    if(!empty($enviados['FornecedorDocumento']['diretorio_file_server'])) {
                                        $link = $enviados['FornecedorDocumento']['diretorio_file_server'];
                                    } else {
                                        
                                        //codigo da pasta que esta o arquivo
                                        $file = $_SERVER['DOCUMENT_ROOT'].'/portal/app/webroot/files/documentacao/'.$enviados['FornecedorDocumento']['codigo_fornecedor'].'/'. $enviados['FornecedorDocumento']['caminho_arquivo'];
                                        
                                        //verifica se o arquivo existe
                                        if(file_exists($file)) {
                                            //monta o link 
                                            $link = '/files/documentacao/' . $enviados['FornecedorDocumento']['codigo_fornecedor'] . '/' . $enviados['FornecedorDocumento']['caminho_arquivo'];
                                        }
                                        else {
                                            //caminho do arquivo
                                            $file = $_SERVER['DOCUMENT_ROOT'].'/portal/app/webroot/files/documentacao/'.$enviados['PropostaCredenciamento']['codigo'].'/'.$enviados['FornecedorDocumento']['caminho_arquivo'];
                                            //verifica se o caminho existe
                                            if(file_exists($file)) {
                                                //seta o novo codigo de onde o arquivo existe
                                                $link = '/files/documentacao/' . $enviados['PropostaCredenciamento']['codigo'] . '/' . $enviados['FornecedorDocumento']['caminho_arquivo'];
                                            }
                                        } //fim is file fonecedor
                                    }

                            
                                    $imprime = $this->Html->link($enviados['FornecedorDocumento']['caminho_arquivo'], $link, array('target'=>'_blank'));
                                    // if(empty($link)) {
                                    //     $imprime = "Arquivo indisponível!";
                                    // }

                                    echo $imprime; 
                                ?>
                                    
                                
                            </td>
                            <td>
                                <?php if(
                                    $enviados['FornecedorDocumento']['codigo_tipo_documento'] == 32 OR 
                                    $enviados['FornecedorDocumento']['codigo_tipo_documento'] == 30 OR
                                    $enviados['FornecedorDocumento']['codigo_tipo_documento'] == 29 OR
                                    $enviados['FornecedorDocumento']['codigo_tipo_documento'] == 36 OR
                                    $enviados['FornecedorDocumento']['codigo_tipo_documento'] == 42
                                ):  ?>
                                    <?php echo $enviados['FornecedorDocumento']['data_validade'] ?>
                                <?php endif;  ?>
                            </td>
                            <td><?php echo $validade;?></td>
                            <td><?php echo $this->Html->link('', 'javascript:void(0)', array('onclick' => 'excluirFornecedorDocumento('.$enviados['FornecedorDocumento']['codigo'].');', 'class' => 'icon-trash ', 'title' => 'Excluir Documento')); ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php else :?>
                    <tr>
                        <td colspan="5"><div>Nenhum dado foi encontrado.</div></td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>

<?php echo $this->Javascript->codeBlock("
    $(document).ready(function(){
        setup_time();
        setup_mascaras();
    });
    ");
?>