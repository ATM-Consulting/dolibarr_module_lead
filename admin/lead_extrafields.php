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

/**
 * \file lead/admin/lead_extrafields.php
 * \ingroup agenda
 * \brief Page to setup extra fields of lead
 */

// Dolibarr environment
$res = @include '../../main.inc.php'; // From htdocs directory
if (! $res) {
	$res = @include '../../../main.inc.php'; // From "custom" directory
}
require_once DOL_DOCUMENT_ROOT . '/core/class/extrafields.class.php';
require_once '../lib/lead.lib.php';

if (! $user->admin)
	accessforbidden();

$langs->load("admin");
$langs->load("other");
$langs->load("agenda");
$langs->load("lead@lead");

$extrafields = new ExtraFields($db);
$form = new Form($db);

// List of supported format
$tmptype2label = ExtraFields::$type2label;
$type2label = array(
	''
);
foreach ($tmptype2label as $key => $val)
	$type2label[$key] = $langs->trans($val);

$action = GETPOST('action', 'alpha');
$attrname = GETPOST('attrname', 'alpha');
$elementtype = 'lead'; // Must be the $table_element of the class that manage extrafield

if (! $user->admin)
	accessforbidden();

	/*
 * Actions
 */
if (file_exists(DOL_DOCUMENT_ROOT . '/core/admin_extrafields.inc.php'))
	require_once DOL_DOCUMENT_ROOT . '/core/admin_extrafields.inc.php';

if (file_exists(DOL_DOCUMENT_ROOT . '/core/actions_extrafields.inc.php'))
	require_once DOL_DOCUMENT_ROOT . '/core/actions_extrafields.inc.php';

	/*
 * View
 */

$textobject = $langs->transnoentitiesnoconv("Module103111Name");

llxHeader('', $langs->trans("LeadSetup"));

$linkback = '<a href="' . DOL_URL_ROOT . '/admin/modules.php">' . $langs->trans("BackToModuleList") . '</a>';
print load_fiche_titre($langs->trans("LeadSetup"), $linkback, 'tools');
print "<br>\n";

// Configuration header
$head = leadAdminPrepareHead();
print dol_get_fiche_head($head, 'attributes', $langs->trans("Module103111Name"), -1, "lead@lead");

print $langs->trans("DefineHereComplementaryAttributes", $langs->transnoentitiesnoconv("Module103111Name")) . '<br>' . "\n";
print '<br>';

// Load attribute_label
$extrafields->fetch_name_optionals_label($elementtype);

print "<table summary=\"listofattributes\" class=\"noborder\" width=\"100%\">";

print '<tr class="liste_titre">';
print '<td>' . $langs->trans("Label") . '</td>';
print '<td>' . $langs->trans("AttributeCode") . '</td>';
print '<td>' . $langs->trans("Type") . '</td>';
print '<td align="right">' . $langs->trans("Size") . '</td>';
print '<td align="center">' . $langs->trans("Unique") . '</td>';
print '<td align="center">' . $langs->trans("Required") . '</td>';
print '<td width="80">&nbsp;</td>';
print "</tr>\n";

$var = True;
if(version_compare(DOL_VERSION, 17, '<') > 0) $TExtrafieldsTypes = $extrafields->attribute_type;
else if(!empty($extrafields->attributes['lead']['type'])) $TExtrafieldsTypes = $extrafields->attributes['lead']['type'];
if(!empty($TExtrafieldsTypes)) {
	foreach($TExtrafieldsTypes as $key => $value) {
		$var = ! $var;
		print "<tr ".$bc[$var].">";
		print "<td>".(version_compare(DOL_VERSION, 17, '<') > 0 ? $extrafields->attribute_label[$key] : $extrafields->attributes['lead']['label'][$key])."</td>\n";
		print "<td>".$key."</td>\n";
		print "<td>".(version_compare(DOL_VERSION, 17, '<') > 0 ? $extrafields->attribute_type[$key] : $extrafields->attributes['lead']['type'][$key])."</td>\n";
		print '<td align="right">'.(version_compare(DOL_VERSION, 17, '<') > 0 ? $extrafields->attribute_size[$key] : $extrafields->attributes['lead']['size'][$key])."</td>\n";
		print '<td align="center">'.yn(version_compare(DOL_VERSION, 17, '<') > 0 ? $extrafields->attribute_unique[$key] : $extrafields->attributes['lead']['unique'][$key])."</td>\n";
		print '<td align="center">'.yn(version_compare(DOL_VERSION, 17, '<') > 0 ? $extrafields->attribute_required[$key] : $extrafields->attributes['lead']['required'][$key])."</td>\n";
		print '<td align="right"><a href="'.$_SERVER["PHP_SELF"].'?action=edit&attrname='.$key.'">'.img_edit().'</a>';
		print "&nbsp; <a href=\"".$_SERVER["PHP_SELF"]."?action=delete&attrname=$key\">".img_delete()."</a></td>\n";
		print "</tr>";
	}
}

print "</table>";

print dol_get_fiche_end(-1);

// Buttons
if ($action != 'create' && $action != 'edit') {
	print '<div class="tabsAction">';
	print "<a class=\"butAction\" href=\"" . $_SERVER["PHP_SELF"] . "?action=create\">" . $langs->trans("NewAttribute") . "</a>";
	print "</div>";
}

/* ************************************************************************* */
/*                                                                            */
/* Creation d'un champ optionnel											  */
/*                                                                            */
/* ************************************************************************** */

if ($action == 'create') {
	print "<br>";
	print load_fiche_titre($langs->trans('NewAttribute'));

	require DOL_DOCUMENT_ROOT . '/core/tpl/admin_extrafields_add.tpl.php';
}

/* ************************************************************************* */
/*                                                                            */
/* Edition d'un champ optionnel                                               */
/*                                                                            */
/* ************************************************************************** */
if ($action == 'edit' && ! empty($attrname)) {
	print "<br>";
	print load_fiche_titre($langs->trans("FieldEdition", $attrname));

	require DOL_DOCUMENT_ROOT . '/core/tpl/admin_extrafields_edit.tpl.php';
}

llxFooter();

$db->close();
