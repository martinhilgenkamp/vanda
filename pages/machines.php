<?php
require_once("inc/class/class.machines.php");
require_once("inc/class/class.user.php");
require_once("inc/class/class.option.php");

$mm = new MachineManager;
$um = new UserManager;
$om = new OptionManager();

$users = $um->listUsers();
$options = $om->getAllOptions()[0];

$machinecount = $options->MachineCount;
$machines = $mm->getAllMachines();
$object = null;
$machines = $mm->fillUnusedMachines($machines, $machinecount-1, $object);

?>

<div class="navigation-header"><a href="index.php?page=productie" class="ui-button ui-corner-all ui-widget pageswitch" style="position: absolute"><< Productie</a></div>
<div class="clr"></div>
<h1>Machine registratie</h1>

<div id="machineform-wrapper">
	<form id="machineform" name="machineform"  method="post" target="_blank">
		<ul class="machine-header">
			<li>Gereed</li>
		    <li>Operator</li>
			<li>Kwaliteit</li>
			<li>Machine</li>
		</ul>
		<?php
		foreach($machines as $machine){
		?>			
			<ul>
				<li>
					<input type="text" class="ui-widget ui-state-default ui-corner-all machine-input-text" disabled='true' value="<?= date(' H:i:s', strtotime($machine->datum));?>">
				</li>
				<li>
					
                   <!--  <input type="text" class="ui-widget ui-state-default ui-corner-all machine-input-text" name="operator<?= $machine->machine ?>" id="input_operator<?= $machine->machine ?>" value="<?= $machine->persoon ?>"> -->
                    
                    <select name="operator<?= $machine->machine ?>" id="input_operator<?= $machine->machine ?>" class="ui-widget ui-state-default ui-corner-all machine-input-text">
                        <?php
                            foreach ($users as $user) {
                        ?>
                            <option value="<?= $user->username ?>" <?= $machine->persoon == $user->username ? 'selected' : '' ?>><?= $user->username ?></option>";
                        <?php 
                         }
                        ?>
                    </select>
                </li>
				<li>
                    <input type="text" class="ui-widget ui-state-default ui-corner-all machine-input-text" name="kwalitet<?= $machine->machine ?>" id="input_kwaliteit<?= $machine->machine ?>" value="<?= $machine->kwaliteit;?>">
                </li>
                <input type="button" class="ui-button ui-corner-all ui-widget machinebutton"  id="machine<?= $machine->machine ?>" name="machine<?= $machine->machine ?>" value="Machine<?= $machine->machine ?>">
			</ul>
		<?php } ?>
		<input type="hidden" name="task" id="input_task" value="add" />
	</form>
</div>
<?php
?>