<?php
if(!isset($contatos)) $contatos = array();?>
<div class='row-fluid inline'>
	<table class='table table-striped veiculos'>
		<thead>
			<th class='input-large'>Nome</th>
			<th class='input-large'>Tipo</th>
			<th class='input-large'>Tipo Retorno</th>
			<th class='input-large'>Contato</th>
			<th></th>
		</thead>
		<tbody>
			<?php $xx=0; foreach($contatos as $c): ?>
			<tr class='tablerow-input' >
				<td><?php echo $this->BForm->input('ProprietarioContato.codigo',array('name' => "data[ProprietarioContato][codigo][]",'value'=>$c['ProprietarioContato']['codigo'],'type'=>'hidden')); ?>
					<?php echo $this->BForm->input('ProprietarioContato.nome',array('id'=>'nome', 'name' => "data[ProprietarioContato][nome][]",'value'=>$c['ProprietarioContato']['nome'])); ?></td>
				<td ><?php echo $this->BForm->input('ProprietarioContato.tipo',array('id'=>'tipo', 'name' => "data[ProprietarioContato][tipo][]","empty" => "Tipo","options" => $tipoContato,'value'=>$c['ProprietarioContato']['codigo_tipo_contato'])); ?>
					<div id="contato-<?php echo $xx; ?>" class="help-block error-message"><?php echo @$this->validationErrors['ProprietarioContato']['tipo'][$xx]?></div> 
				</td>
                   
				<td><?php echo $this->BForm->input('ProprietarioContato.tipo_retorno',array('id'=>'tipo_retorno', 'name' => "data[ProprietarioContato][tipo_retorno][]","empty" => "Retorno","options" => $tipoRetorno,'value'=>$c['ProprietarioContato']['codigo_tipo_retorno'])); ?>
                <div id="contato-<?php echo $xx; ?>" class="help-block error-message"><?php echo "<font color='#B94A48'>".@$this->validationErrors['ProprietarioContato']['tipo_retorno'][$xx]."</font>"?></div>
				</td>
				<td><?php echo $this->BForm->input('ProprietarioContato.contato',array('id'=>'contato', 'name' => "data[ProprietarioContato][contato][]",'value'=>$c['ProprietarioContato']['descricao'],'type'=>'text')); ?></td>
				<td><a href="javascript:void(0)" class="btn" onclick="remove_contato(jQuery(this).parent().parent())"><i class="icon-minus"></i></a></td>
			</tr>
			 <?php $xx++;endforeach; ?>

			<tr class='tablerow-input'>
				<?php echo $this->BForm->input('ProprietarioContato.codigo',array('name' => "data[ProprietarioContato][codigo][]",'value'=>'0','type'=>'hidden')); ?>
				<td><?php echo $this->BForm->input('ProprietarioContato.nome',array('id'=>'nome', 'name' => "data[ProprietarioContato][nome][]")); ?></td>
				<td ><?php echo $this->BForm->input('ProprietarioContato.tipo',array('id'=>'tipo', 'name' => "data[ProprietarioContato][tipo][]","empty" => "Tipo","options" => $tipoContato)); ?>
                </td>
				<td><?php echo $this->BForm->input('ProprietarioContato.tipo_retorno',array('id'=>'tipo_retorno', 'name' => "data[ProprietarioContato][tipo_retorno][]","empty" => "Retorno","options" => $tipoRetorno)); ?></td>
				<td><?php echo $this->BForm->input('ProprietarioContato.contato',array('id'=>'contato', 'name' => "data[ProprietarioContato][contato][]",'type'=>'text')); ?></td>

                
				<td><a href="javascript:void(0)" class="btn btn-success" onclick="adiciona_contato(jQuery(this).parent().parent())"><i class="icon-plus icon-white"></i></a></td>
			</tr>
         
			
		</tbody>
	</table>
</div>
<script>
	<?/*php foreach($this->validationErrors['ProprietarioContato']['tipo'] as $k=>$v): ?>
		document.getElementById('contato-<?php echo $k; ?>').style.color = '#B94A48';
		if (document.getElementById('tipo').value==''){
		  document.getElementById('tipo').style.border= '1px solid #B94A48';
		  document.getElementById('tipo').style.color='#B94A48';
		}
		if (document.getElementById('tipo_retorno').value==''){
		document.getElementById('tipo_retorno').style.border= '1px solid #B94A48';
		document.getElementById('tipo_retorno').style.color='#B94A48';
		}
	<?php endforeach; */?>
</script>