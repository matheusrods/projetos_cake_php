<?php echo $this->BForm->create('Cargo', array('url' => array('controller' => 'cargos', 'action' => 'incluir', $codigo_cliente, $referencia, $terceiros_implantacao))); ?>
<?php echo $this->element('cargos/fields', array('edit_mode' => false)); ?>

<?php if ($referencia == "cargo_implantacao_terceiros" or $terceiros_implantacao == 'terceiros_implantacao'): ?>
    <div class="modal fade" id="modal_editar_cliente" data-backdrop="static" style="width: 85%; left: 8%; top: 15%; margin: 0 auto;">
        <div class="modal-dialog modal-sm" style="position: static;">
            <div class="modal-content">
                <div class="modal-header" style="text-align: center;">
                    <h3>NOVO CARGO:</h3>
                </div>
                <div class="modal-body" >
                	<p>Olá <b><?php echo $this->data['Cliente']['nome_fantasia']; ?></b>, tudo bem?</p>
					<p>Antes de incluir um novo cargo pedimos que realize uma rápida consulta para checar se realmente já não existe este cargo criado com alguma outra nomenclatura ou caractere diferente, por exemplo:</p>
					<ul>
						<li>Assit Administrativo</li>
						<li>Assistente Administrativo</li>
						<li>Assistente Adm</li>
					</ul>                    
                </div>
            </div>
            <div class="modal-footer">
                <div class="right">
                    <a href="javascript:void(0);" onclick="mostra_modal(0);" class="btn btn-danger">FECHAR</a>
                </div>
            </div>                                      
        </div>
    </div>
    <div>
        <label>
            
        </label>
    </div>

    <?php echo $this->Javascript->codeBlock("
    jQuery(document).ready(function() {
       mostra_modal = function(mostra){
            if(mostra == 1){
                $('#modal_editar_cliente').css('z-index', '1050');
                $('#modal_editar_cliente').modal('show');
            }
            else{
                $('#modal_editar_cliente').css('z-index', '-1');
                $('#modal_editar_cliente').modal('hide');
            }
        }

        mostra_modal(1); 

    });
    "); ?>  

<?php
endif; ?>

<?php echo $this->BForm->end(); ?>