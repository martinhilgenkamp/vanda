<?php
require_once("inc/class/class.machines.php");
require_once("inc/class/class.user.php");
require_once("inc/class/class.option.php");

$mm = new MachineManager;
$um = new UserManager;
$om = new OptionManager();

$users = $um->listUsers();
$result = $om->getAllOptions()[0];
$machines = $result->MachineCount;	
?>

<div class="navigation-header"><a href="index.php?page=productie" class="ui-button ui-corner-all ui-widget pageswitch" style="position: absolute"><< Productie</a></div>
<div class="clr"></div>
<h1>Machine registratie</h1>

<div id="machineform-wrapper">
	<form id="machineform" name="machineform"  method="post" target="_blank">
		<ul class="machine-header">
			<li>Operator</li>
			<li>Kwaliteit</li>
			<li>Machine</li>
		</ul>
		<?php
		for ($i = 1; $i <= $machines; $i++) {
            $lastPerson = $mm->getLastPersoon($i);
			$lastKwaliteit = $mm->getLastKwaliteit($i);		
		?>			
			<ul>
				<li>
                   <!--  <input type="text" class="ui-widget ui-state-default ui-corner-all machine-input-text" name="operator<?= $i ?>" id="input_operator<?= $i ?>" value="<?= $mm->getLastPersoon($i) ?>"> -->
                    
                    <select name="operator<?= $i ?>" id="input_operator<?= $i ?>" class="ui-widget ui-state-default ui-corner-all machine-input-text">
                        <?php
                            foreach ($users as $user) {
                        ?>
                            <option value="<?= $user->username ?>" <?= $lastPerson == $user->username ? 'selected' : '' ?>><?= $user->username ?></option>";
                        <?php 
                         }
                        ?>
                    </select>
                </li>
				<li>
                    <input type="text" class="ui-widget ui-state-default ui-corner-all machine-input-text" name="kwalitet<?= $i ?>" id="input_kwaliteit<?= $i ?>" value="<?= $lastKwaliteit;?>">
                </li>
                <input type="button" class="ui-button ui-corner-all ui-widget machinebutton"  id="machine<?= $i ?>" name="machine<?= $i ?>" value="Machine<?= $i ?>">
			</ul>
		<?php } ?>
		<input type="hidden" name="task" id="input_task" value="add" />
	</form>
</div>
<?php
?>