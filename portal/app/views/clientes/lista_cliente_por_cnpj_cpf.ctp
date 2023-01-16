<?php 
	if(!$lista){
		echo '<option></option>';
	} else {
		foreach ($lista as $key => $value) {
			echo '<option value='.$key.'>'.$value.'</option>';
		}
	
	}