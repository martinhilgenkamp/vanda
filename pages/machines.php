<a href="index.php?page=productie" class="ui-button ui-corner-all ui-widget" style="position: absolute"><< Productie</a>
<h1>Machine registratie</h1>

<?php
require_once("inc/class/class.machines.php");

$mm = new MachineManager;

$machines = 8;		
?>

<div id="machineform-wrapper">
	<form id="machineform" name="machineform"  method="post" target="_blank">
		<ul class="machine-header">
			<li>Operator</li>
			<li>Kwaliteit</li>
			<li>Machine</li>
		</ul>
		<?php
		for ($i = 1; $i <= $machines; $i++) {
		?>			
			<ul>
				<?php 
				echo "<li><input type=\"text\" class=\"ui-widget ui-state-default ui-corner-all machine-input-text\" name=\"operator".$i."\" id=\"input_operator".$i."\" value=\"".$mm->getLastPersoon($i)."\"></li>";
				echo "<li><input type=\"text\" class=\"ui-widget ui-state-default ui-corner-all machine-input-text\" name=\"kwalitet".$i."\" id=\"input_kwaliteit".$i."\" value=\"".$mm->getLastKwaliteit($i)."\"></li>";
				echo '<input type="button" class="ui-button ui-corner-all ui-widget machinebutton"  id="machine'.$i.'" name="machine'.$i.'" value="Machine'.$i.'">';
				?>
			</ul>
		<?php } ?>
		<input type="hidden" name="task" id="input_task" value="add" />
	</form>
</div>