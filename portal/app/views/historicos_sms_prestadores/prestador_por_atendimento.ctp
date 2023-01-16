<div id="historico_sm_prestador">
	<? echo $this->BForm->input('enviar_pr', array('type' => 'checkbox', 'label' => 'Enviar Pronta Resposta', 'class' => 'input-large')); ?>
    <div class="row-fluid inline"> 
		<div id="insere-prestador">
			<?php echo $this->Buonny->input_codigo_prestador($this, 'codigo_prestador', 'Prestador', FALSE, 'HistoricoSm'); 
                  echo $this->BForm->input('nome_prestador', array('label' => false, 'class' => 'input-large', 'readonly' => 'readonly')); 
            ?>
		</div>
	</div>
	<table class="table table-striped table-historico-prestadores">
	  <thead>
	    <tr>
	      <th>Prestador</th>
	      <th>CPF / CNPJ</th>
	      <th>Contato</th>
	      <th>Endereço</th>
	      <th>Bairro</th>
	      <th>Cidade</th>
	      <th>CEP</th>	        
	      <th>&nbsp;</th>
	    </tr>
	  </thead>
	  <tbody>
	  	<?php foreach ($prestadores as $prestador): ?>		  
		  <tr codigo=<?= $prestador['Prestador']['codigo'] ?>>
		      <td><?= $prestador['Prestador']['nome'] ?></td>
		      <td><?= $prestador['Prestador']['codigo_documento'] ?></td>
		      <td><?= $prestador[0]['contato'] ?></td>
		      <td><?= $prestador['Endereco']['descricao'].','.
				$prestador['PrestadorEndereco']['numero'] ?></td>
	 		  <td> <?= $prestador['EnderecoBairro']['descricao'] ?></td>
			  <td> <?= $prestador['EnderecoCidade']['descricao'].'-'.
				$prestador['EnderecoEstado']['descricao'] ?> </td>
			  <td><?= $prestador['EnderecoCep']['cep'] ?></td>			  
		      <td >
            <?php 
            if($prestador['HistoricoSmPrestador']['status'] != HistoricoSmPrestador::FINALIZADO){ ?>
                <?php 

                $nome_prestador = str_replace(array('"', "'"),'',  $prestador['Prestador']['nome']);
                echo $this->Html->link('<i class="icon-ok finalizar-pr"></i>',
                array('action' => 'status_historico_sm_prestador', HistoricoSmPrestador::FINALIZADO,
                	$prestador['Prestador']['codigo'], $codigo_atendimento, $codigo_sm), 
                array('escape' => false, 'title' =>'Finalizar Pronta Resposta', 
                'onclick' => "return open_dialog(this, 'Finalizar Pronta Resposta: {$nome_prestador}', 960)")); ?>
                <?php echo $this->Html->link('<i class="icon-remove cancelar-pr"></i>',
                        array('action' => 'status_historico_sm_prestador', HistoricoSmPrestador::CANCELADO, 
                            $prestador['Prestador']['codigo'], $codigo_atendimento, $codigo_sm), 
                        array('escape' => false, 'title' =>'Cancelar Pronta Resposta', 
                        'onclick' => "return open_dialog(this, 'Cancelar Pronta Resposta: {$nome_prestador}', 960)"));?>
            <?php } ?>

		      </td>
		  </tr>
		<?php endforeach; ?>
	  </tbody>
	</table>
</div>
<?php echo $this->Javascript->codeBlock("

	jQuery(document).ready(function(){
		
		$('#insere-prestador').hide();
		$('#enviar_pr').change(function(){
			if($(this).is(':checked')){
				$('#insere-prestador').show();
			}else{
                $('#insere-prestador').hide();
				$('#HistoricoSmCodigoPrestador').val('');
                $('#nome_prestador').val('');
			}
		});
		$('#HistoricoSmCodigoPrestador').change(function(){       
            codigo = $(this).val();
            //if(!$('.table-historico-prestadores').find('tbody').find('tr[codigo='+codigo+']').length){                
                busca_nome_prestador($(this));
            // }else{                    
            //     $(this).val('');
            //     $('#nome_prestador').val('');                
            //     alert('Prestador já cadastrado para o atendimento');
            // }
        });
        function busca_nome_prestador(codigo){
            $('#nome_prestador').addClass('ui-autocomplete-loading');
            $.ajax({
                type: 'POST',
                url: '/portal/prestadores/busca_por_codigo',
                dataType : 'json',
                data:{'codigo': codigo.val()},
                success : function(data) {                                           
                   $('#nome_prestador').val(data['Prestador']['nome']);
                },
                error : function(data){
                    codigo.val('');
                    $('#nome_prestador').val('');                    
                    alert('Não foi possível incluir prestador. Tente novamente');
                    
                },
                complete : function(){
                    $('#nome_prestador').removeClass('ui-autocomplete-loading');
                }
            }); 
        }
	});");
    if($codigo_prestador > 0){
        echo $this->Javascript->codeBlock("
                jQuery(document).ready(function(){
                    $('#enviar_pr').attr('checked', true);
                    $('#enviar_pr').trigger('change');
                    $('#HistoricoSmCodigoPrestador').val('{$codigo_prestador}');
                    $('#HistoricoSmCodigoPrestador').trigger('change');
                });
            ");
    }
?>