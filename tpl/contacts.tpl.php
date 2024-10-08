<?php
/* 
 * Copyright (C) 2014-2016 Florian HENRY <florian.henry@atm-consulting.fr>
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 */
if (! class_exists('Contact')) {
	require DOL_DOCUMENT_ROOT . '/contact/class/contact.class.php';
}
if (! class_exists('FormCompany')) {
	require DOL_DOCUMENT_ROOT . '/core/class/html.formcompany.class.php';
}

$module = $object->element;

$permission = $user->rights->$module->write;

$formcompany = new FormCompany($db);
$companystatic = new Societe($db);
$contactstatic = new Contact($db);
$userstatic = new User($db);

?>

<!-- BEGIN PHP TEMPLATE CONTACTS -->
<div class="tagtable centpercent noborder allwidth">

<?php if ($permission) { ?>
	<form class="tagtr liste_titre">
		<div class="tagtd"><?php echo $langs->trans("Source"); ?></div>
		<div class="tagtd"><?php echo $langs->trans("Company"); ?></div>
		<div class="tagtd"><?php echo $langs->trans("Contacts"); ?></div>
		<div class="tagtd"><?php echo $langs->trans("ContactType"); ?></div>
		<div class="tagtd">&nbsp;</div>
		<div class="tagtd">&nbsp;</div>
	</form>

	<?php $var=false; ?>


	<form class="tagtr impair"
		action="<?php echo $_SERVER["PHP_SELF"].'?id='.$object->id; ?>"
		method="POST">
		<input type="hidden" name="token"
			value="<?php echo $_SESSION['newtoken']; ?>" /> <input type="hidden"
			name="id" value="<?php echo $object->id; ?>" /> <input type="hidden"
			name="action" value="addcontact" /> <input type="hidden"
			name="source" value="internal" />
		<div class="nowrap tagtd"><?php echo img_object('','user').' '.$langs->trans("Users"); ?></div>
		<div class="tagtd"><?php echo getDolGlobalString('MAIN_INFO_SOCIETE_NOM'); ?></div>
		<div class="tagtd maxwidthonsmartphone"><?php echo $form->select_dolusers($user->id, 'userid', 0, (! empty($userAlreadySelected)?$userAlreadySelected:null), 0, null, null, 0, 56); ?></div>
		<div class="tagtd maxwidthonsmartphone"><?php echo $formcompany->selectTypeContact($object, '', 'type','internal'); ?></div>
		<div class="tagtd">&nbsp;</div>
		<div class="tagtd" align="right">
			<input type="submit" class="button"
				value="<?php echo $langs->trans("Add"); ?>">
		</div>
	</form>

	<?php $var=!$var; ?>

	<form class="tagtr pair"
		action="<?php echo $_SERVER["PHP_SELF"].'?id='.$object->id; ?>"
		method="POST">
		<input type="hidden" name="token"
			value="<?php echo $_SESSION['newtoken']; ?>" /> <input type="hidden"
			name="id" value="<?php echo $object->id; ?>" /> <input type="hidden"
			name="action" value="addcontact" /> <input type="hidden"
			name="source" value="external" />
		<div class="tagtd nowrap"><?php echo img_object('','contact').' '.$langs->trans("ThirdPartyContacts"); ?></div>
		<?php if ($conf->use_javascript_ajax && getDolGlobalString('COMPANY_USE_SEARCH_TO_SELECT')) { ?>
		<div class="tagtd nowrap maxwidthonsmartphone">
			<?php
		$events = array();
		$events[] = array(
			'method' => 'getContacts',
			'url' => dol_buildpath('/core/ajax/contacts.php', 1),
			'htmlname' => 'contactid',
			'params' => array(
				'add-customer-contact' => 'disabled'
			)
		);
		print $form->select_thirdparty_list($object->socid, 'socid', '', 1, 0, 0, $events);
		?>
		</div>
		<div class="tagtd maxwidthonsmartphone">
			<?php $nbofcontacts=$form->selectcontacts($object->socid, '', 'contactid'); ?>
		</div>
		<?php } else { ?>
		<div class="tagtd maxwidthonsmartphone">
			<?php $selectedCompany = isset($_GET["newcompany"])?$_GET["newcompany"]:$object->socid; ?>
			<?php $selectedCompany = $formcompany->selectCompaniesForNewContact($object, 'id', $selectedCompany, 'newcompany'); ?>
		</div>
		<div class="tagtd maxwidthonsmartphone">
			<?php $nbofcontacts=$form->selectcontacts($selectedCompany, '', 'contactid'); ?>
		</div>
		<?php } ?>
		<div class="tagtd maxwidthonsmartphone">
			<?php $formcompany->selectTypeContact($object, '', 'type','external'); ?>
		</div>
		<div class="tagtd">&nbsp;</div>
		<div class="tagtd" align="right">
			<input type="submit" id="add-customer-contact" class="button"
				value="<?php echo $langs->trans("Add"); ?>"
				<?php if (! $nbofcontacts) echo ' disabled="disabled"'; ?>>
		</div>
	</form>

<?php } ?>

	<form class="tagtr liste_titre">
		<div class="tagtd"><?php echo $langs->trans("Source"); ?></div>
		<div class="tagtd"><?php echo $langs->trans("Company"); ?></div>
		<div class="tagtd"><?php echo $langs->trans("Contacts"); ?></div>
		<div class="tagtd"><?php echo $langs->trans("ContactType"); ?></div>
		<div class="tagtd" align="center"><?php echo $langs->trans("Status"); ?></div>
		<div class="tagtd">&nbsp;</div>
	</form>

	<?php $var=true; ?>

	<?php
	foreach (array(
		'internal',
		'external'
	) as $source) {
		$tab = $object->liste_contact(- 1, $source);
		$num = count($tab);
		
		$i = 0;
		while ($i < $num) {
			$var = ! $var;
			?>

	<form class="tagtr <?php echo $var?"pair":"impair"; ?>">
		<div class="tagtd" align="left">
			<?php if ($tab[$i]['source']=='internal') echo $langs->trans("User"); ?>
			<?php if ($tab[$i]['source']=='external') echo $langs->trans("ThirdPartyContact"); ?>
		</div>
		<div class="tagtd" align="left">
			<?php
			if ($tab[$i]['socid'] > 0) {
				$companystatic->fetch($tab[$i]['socid']);
				echo $companystatic->getNomUrl(1);
			}
			if ($tab[$i]['socid'] < 0) {
				echo getDolGlobalString('MAIN_INFO_SOCIETE_NOM');
			}
			if (! $tab[$i]['socid']) {
				echo '&nbsp;';
			}
			?>
		</div>
		<div class="tagtd">
			<?php
			if ($tab[$i]['source'] == 'internal') {
				$userstatic->id = $tab[$i]['id'];
				$userstatic->lastname = $tab[$i]['lastname'];
				$userstatic->firstname = $tab[$i]['firstname'];
				echo $userstatic->getNomUrl(1);
			}
			if ($tab[$i]['source'] == 'external') {
				$contactstatic->id = $tab[$i]['id'];
				$contactstatic->lastname = $tab[$i]['lastname'];
				$contactstatic->firstname = $tab[$i]['firstname'];
				echo $contactstatic->getNomUrl(1);
			}
			?>
		</div>
		<div class="tagtd"><?php echo $tab[$i]['libelle']; ?></div>
		<div class="tagtd" align="center">
			<?php if ($object->statut >= 0) echo '<a href="'.$_SERVER['PHP_SELF'].'?id='.$object->id.'&amp;action=swapstatut&amp;ligne='.$tab[$i]['rowid'].'">'; ?>
			<?php
			if ($tab[$i]['source'] == 'internal') {
				$userstatic->id = $tab[$i]['id'];
				$userstatic->lastname = $tab[$i]['lastname'];
				$userstatic->firstname = $tab[$i]['firstname'];
				// echo $userstatic->LibStatut($tab[$i]['status'],3);
			}
			if ($tab[$i]['source'] == 'external') {
				$contactstatic->id = $tab[$i]['id'];
				$contactstatic->lastname = $tab[$i]['lastname'];
				$contactstatic->firstname = $tab[$i]['firstname'];
				echo $contactstatic->LibStatut($tab[$i]['status'], 3);
			}
			?>
			<?php if ($object->statut >= 0) echo '</a>'; ?>
		</div>
		<div class="tagtd nowrap" align="center">
			<?php if ($permission) { ?>
				&nbsp;<a
				href="<?php echo $_SERVER["PHP_SELF"].'?id='.$object->id.'&amp;action=deletecontact&amp;lineid='.$tab[$i]['rowid']; ?>"><?php echo img_delete(); ?></a>
			<?php } ?>
		</div>
	</form>

<?php $i++; ?>
<?php } } ?>

</div>
<!-- END PHP TEMPLATE CONTACTS -->
