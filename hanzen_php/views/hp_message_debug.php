<?php
	function all_array($array){
		$out = '';
		foreach($array as $name => $val){
			$out .= '<ul>';
			if(is_array($val)){
				$out .= all_array($val);
			}
			else{
				$out .= '<li>'.$name.': '.$val.'</li>';
			}
			$out .= '</ul>';
		}
		return $out;
	}
?>
<div class="container">
<div class="panel panel-danger">
  <div class="panel-heading">Message Debug</div>
  <div class="panel-body">
<?php
	if($is_error){
		echo '<div class="alert alert-danger" role="alert"><h3>is Error: TRUE</h3></div>';
	}
	foreach($msg as $group => $error){
		echo '<div class="panel panel-default"><div class="panel-heading">'.ucwords($group).'</div>';
		echo '<div class="panel-body">';
		if(is_array($error)){
			echo all_array($error);
		}
		echo '</div></div>';
	}
	if(isset($this->session)){
		echo '<div class="panel panel-primary"><div class="panel-heading">Session</div><div class="panel-body">'.all_array($this->session->all_userdata()).'</ul></div></div>';
	}
	?>
  </div>
</div>
</div>