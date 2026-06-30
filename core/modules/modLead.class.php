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
 * \defgroup	lead	Lead module
 * \brief		Lead module descriptor.
 * \file		core/modules/modLead.class.php
 * \ingroup	lead
 * \brief		Description and activation file for module Lead
 */
include_once DOL_DOCUMENT_ROOT . "/core/modules/DolibarrModules.class.php";

/**
 * Description and activation class for module Lead
 */
class modLead extends DolibarrModules
{

	/**
	 * Constructor.
	 * Define names, constants, directories, boxes, permissions
	 *
	 * @param DoliDB $db
	 */
	public function __construct($db)
	{
		global $langs, $conf;

		$this->db = $db;

		// Id for module (must be unique).
		// Use a free id here
		// (See in Home -> System information -> Dolibarr for list of used modules id).
		$this->numero = 103111;
		$this->editor_name = 'ATM Consulting';
		$this->editor_url = 'https://www.atm-consulting.fr';
		// Key text used to identify module (for permissions, menus, etc...)
		$this->rights_class = 'lead';

		// Family can be 'crm','financial','hr','projects','products','ecm','technic','other'
		// It is used to group modules in module setup page
		$this->family = "ATM Consulting";
		// Module label (no space allowed)
		// used if translation string 'ModuleXXXName' not found
		// (where XXX is value of numeric property 'numero' of module)
		$this->name = preg_replace('/^mod/i', '', get_class($this));
		// Module description
		// used if translation string 'ModuleXXXDesc' not found
		// (where XXX is value of numeric property 'numero' of module)
		$this->description = "Description of module Lead";
		// Possible values for version are: 'development', 'experimental' or version
		$this->version = '2.8.2';
		// Key used in llx_const table to save module status enabled/disabled
		// (where MYMODULE is value of property name of module in uppercase)
		$this->const_name = 'MAIN_MODULE_' . strtoupper($this->name);
		// Where to store the module in setup page
		// (0=common,1=interface,2=others,3=very specific)
		$this->special = 0;
		// Name of image file used for this module.
		// If file is in theme/yourtheme/img directory under name object_pictovalue.png
		// use this->picto='pictovalue'
		// If file is in module/img directory under name object_pictovalue.png
		// use this->picto='pictovalue@module'
		$this->picto = 'module.svg@lead'; // mypicto@lead

		$this->module_parts = array(
			'models' => 1,
			'hooks' => array('commonobject','commcard','propalcard','contractcard','ordercard','searchform','invoicecard', 'thirdpartycard'),
		);

		// Data directories to create when module is enabled.
		$this->dirs = array(
			'/lead',
			'/lead/stats'
		);


		// Url to the file with your last numberversion of this module
		require_once __DIR__ . '/../../class/techatm.class.php';
		$this->url_last_version = \lead\TechATM::getLastModuleVersionUrl($this);

		// Config pages. Put here list of php pages
		// stored into lead/admin directory, used to setup module.
		$this->config_page_url = array(
			"admin_lead.php@lead"
		);

		// Dependencies
		// List of modules id that must be enabled if this module is enabled
		$this->depends = array();
		// List of modules id to disable if this one is disabled
		$this->requiredby = array();
		// Minimum version of PHP required by module
		$this->phpmin = array(
			7,
			0
		);
		// Minimum version of Dolibarr required by module
		$this->need_dolibarr_version = array(
			17,
			0
		);
		$this->langfiles = array(
			"lead@lead"
		);

		$this->const = array(
			0 => array(
				'LEAD_ADDON',
				'chaine',
				'mod_lead_simple',
				'Numbering lead rule',
				0,
				'current',
				1
			),
			1 => array(
				'LEAD_UNIVERSAL_MASK',
				'chaine',
				'',
				'Numbering lead rule',
				0,
				'current',
				1
			),
			2 => array(
				'LEAD_NB_DAY_COSURE_AUTO',
				'chaine',
				'30',
				'Numbering lead rule',
				0,
				'current',
				1
			),
			3 => array(
				'LEAD_GRP_USER_AFFECT',
				'chaine',
				'',
				'User Group that can affected',
				0,
				'current',
				1
			),
			4 => array(
				'LEAD_FORCE_USE_THIRDPARTY',
				'yesno',
				'1',
				'force LEad to use customer',
				0,
				'current',
				1
			),
			5 => array(
				'LEAD_ALLOW_MULIPLE_LEAD_ON_CONTRACT',
				'yesno',
				'0',
				'Allow to attach several leads to a single contract',
				0,
				'current',
				1
			)
		);

		// Array to add new pages in new tabs
		// Example:
		$this->tabs = array(
			'thirdparty:+tabLead:Module103111Name:lead@lead:$user->hasRight("lead", "read"):/lead/lead/list.php?socid=__ID__',
		);

		// Dictionnaries
		if (! isModEnabled('lead')) {
			$conf->lead = (object) array();
			$conf->lead->enabled = 0;
		}

		$this->dictionnaries = array(
			'langs' => 'lead@lead',
			'tabname' => array(
				$db->prefix() . "c_lead_status",
				$db->prefix() . "c_lead_type"
			),
			'tablib' => array(
				"LeadStatusDict",
				"LeadTypeDict"
			),
			'tabsql' => array(
				'SELECT f.rowid as rowid, f.code, f.label, f.active FROM ' . MAIN_DB_PREFIX . 'c_lead_status as f',
				'SELECT f.rowid as rowid, f.code, f.label, f.active FROM ' . MAIN_DB_PREFIX . 'c_lead_type as f'
			),
			'tabsqlsort' => array(
				'code ASC',
				'code ASC'
			),
			'tabfield' => array(
				"code,label",
				"code,label"
			),
			'tabfieldvalue' => array(
				"code,label",
				"code,label"
			),
			'tabfieldinsert' => array(
				"code,label",
				"code,label"
			),
			'tabrowid' => array(
				"rowid",
				"rowid"
			),
			'tabcond' => array(
				'isModEnabled(\'lead\')',
				'isModEnabled(\'lead\')'
			)
		);

		// Boxes
		// Add here list of php file(s) stored in core/boxes that contains class to show a box.
		$this->boxes = array(); // Boxes list
		$r = 0;
		// Example:

		$this->boxes[$r][1] = "box_lead_current@lead";
		$r ++;
		$this->boxes[$r][1] = "box_lead_late@lead";
		/*
		 * $this->boxes[$r][1] = "myboxb.php"; $r++;
		 */

		// Permissions
		$this->rights = array(); // Permission array used by this module
		$r = 0;
		$this->rights[$r][0] = 1031111;
		$this->rights[$r][1] = 'See Leads';
		$this->rights[$r][3] = 0;
		$this->rights[$r][4] = 'read';
		$r ++;

		$this->rights[$r][0] = 1031112;
		$this->rights[$r][1] = 'Update Leads';
		$this->rights[$r][3] = 0;
		$this->rights[$r][4] = 'write';
		$r ++;

		$this->rights[$r][0] = 1031113;
		$this->rights[$r][1] = 'Delete Leads';
		$this->rights[$r][3] = 0;
		$this->rights[$r][4] = 'delete';
		$r ++;

		$this->rights[$r][0] = 1031114;
		$this->rights[$r][1] = 'Export Leads';
		$this->rights[$r][3] = 0;
		$this->rights[$r][4] = 'export';
		$r ++;

		// $r++;
		// Main menu entries
		$this->menus = array(); // List of menus to add
		$r = 0;

		$this->menu[$r] = array(
			'fk_menu' => 0,
			'type' => 'top',
			'titre' => 'Module103111Name',
			'mainmenu' => 'lead',
			'leftmenu' => '0',
			'url' => '/lead/index.php',
			'langs' => 'lead@lead',
			'position' => 100,
			'enabled' => '$user->hasRight("lead", "read")',
			'perms' => '$user->hasRight("lead", "read")',
			'target' => '',
			'user' => 0
		);
		$r ++;

		$this->menu[$r] = array(
			'fk_menu' => 'fk_mainmenu=lead',
			'type' => 'left',
			'titre' => 'Module103111Name',
			'leftmenu' => 'Module103111Name',
			'url' => '/lead/lead/list.php',
			'langs' => 'lead@lead',
			'position' => 100+$r,
			'enabled' => '$user->hasRight("lead", "read")',
			'perms' => '$user->hasRight("lead", "read")',
			'target' => '',
			'user' => 0
		);
		$r ++;

		$this->menu[$r] = array(
			'fk_menu' => 'fk_mainmenu=lead,fk_leftmenu=Module103111Name',
			'type' => 'left',
			'titre' => 'LeadCreate',
			'url' => '/lead/lead/card.php?action=create',
			'langs' => 'lead@lead',
			'position' => 100+$r,
			'enabled' => '$user->hasRight("lead", "write")',
			'perms' => '$user->hasRight("lead", "write")',
			'target' => '',
			'user' => 0
		);
		$r ++;

		$this->menu[$r] = array(
			'fk_menu' => 'fk_mainmenu=lead,fk_leftmenu=Module103111Name',
			'type' => 'left',
			'titre' => 'LeadList',
			'url' => '/lead/lead/list.php',
			'langs' => 'lead@lead',
			'position' => 100+$r,
			'enabled' => '$user->hasRight("lead", "read")',
			'perms' => '$user->hasRight("lead", "read")',
			'target' => '',
			'user' => 0
		);
		$r ++;

		$this->menu[$r] = array(
			'fk_menu' => 'fk_mainmenu=lead,fk_leftmenu=Module103111Name',
			'type' => 'left',
			'titre' => 'LeadListCurrent',
			'url' => '/lead/lead/list.php?viewtype=current',
			'langs' => 'lead@lead',
			'position' => 100+$r,
			'enabled' => '$user->hasRight("lead", "read")',
			'perms' => '$user->hasRight("lead", "read")',
			'target' => '',
			'user' => 0
		);
		$r ++;

		$this->menu[$r] = array(
			'fk_menu' => 'fk_mainmenu=lead,fk_leftmenu=Module103111Name',
			'type' => 'left',
			'titre' => 'LeadListMyLead',
			'url' => '/lead/lead/list.php?viewtype=my',
			'langs' => 'lead@lead',
			'position' => 100+$r,
			'enabled' => '$user->hasRight("lead", "read")',
			'perms' => '$user->hasRight("lead", "read")',
			'target' => '',
			'user' => 0
		);
		$r ++;

		$this->menu[$r] = array(
			'fk_menu' => 'fk_mainmenu=lead,fk_leftmenu=Module103111Name',
			'type' => 'left',
			'titre' => 'LeadListLate',
			'url' => '/lead/lead/list.php?viewtype=late',
			'langs' => 'lead@lead',
			'position' => 100+$r,
			'enabled' => '$user->hasRight("lead", "read")',
			'perms' => '$user->hasRight("lead", "read")',
			'target' => '',
			'user' => 0
		);
		$r ++;

		// Exports
		$r = 0;
		$r ++;
		$this->export_code [$r] = $this->rights_class . '_' . $r;
		$this->export_label [$r] = 'ExportDataset_lead';
		$this->export_icon [$r] = 'lead@lead';
		$this->export_permission [$r] = array (
				array (
						"lead",
						"export"
				)
		);
		$this->export_fields_array [$r] = array (
				'l.rowid' => 'Id',
				'l.ref' => 'Ref',
				'l.ref_ext' => 'LeadRefExt',
				'l.ref_int' => 'LeadRefInt',
				'so.nom' => 'Company',
				'dictstep.code' => 'LeadStepCode',
				'dictstep.label' => 'LeadStepLabel',
				'dicttype.code' => 'LeadTypeCode',
				'dicttype.label' => 'LeadTypeLabel',
				'l.date_closure' => 'LeadDeadLine',
				'l.amount_prosp' => 'LeadAmountGuess',
				'l.description' => 'LeadDescription',
		);
		$this->export_TypeFields_array [$r] = array (
				'l.rowid' => 'Text',
				'l.ref' => 'Text',
				'l.ref_ext' => 'Text',
				'l.ref_int' => 'Text',
				'so.nom' => 'Text',
				'dictstep.code' => 'Text',
				'dictstep.label' => 'Text',
				'dicttype.code' => 'Text',
				'dicttype.label' => 'Text',
				'l.date_closure' => 'Date',
				'l.amount_prosp' => 'Numeric',
				'l.description' => 'Text',
		);
		$this->export_entities_array [$r] = array (
				'l.rowid' => 'lead@lead',
				'l.ref' => 'lead@lead',
				'l.ref_ext' => 'lead@lead',
				'l.ref_int' => 'lead@lead',
				'so.nom' => 'company',
				'dictstep.code' => 'lead@lead',
				'dictstep.label' => 'lead@lead',
				'dicttype.code' => 'lead@lead',
				'dicttype.label' => 'lead@lead',
				'l.date_closure' => 'lead@lead',
				'l.amount_prosp' => 'lead@lead',
				'l.description' => 'lead@lead',
		);

		$this->export_sql_start [$r] = 'SELECT DISTINCT ';
		$this->export_sql_end [$r] = ' FROM ' . MAIN_DB_PREFIX . 'lead as l';
		$this->export_sql_end [$r] .=  " LEFT JOIN " . MAIN_DB_PREFIX . "societe as so ON so.rowid=l.fk_soc";
		$this->export_sql_end [$r] .=  " LEFT JOIN " . MAIN_DB_PREFIX . "user as usr ON usr.rowid=l.fk_user_resp";
		$this->export_sql_end [$r] .=  " LEFT JOIN " . MAIN_DB_PREFIX . "c_lead_status as dictstep ON dictstep.rowid=l.fk_c_status";
		$this->export_sql_end [$r] .=  " LEFT JOIN " . MAIN_DB_PREFIX . "c_lead_type as dicttype ON dicttype.rowid=l.fk_c_type";
		$this->export_sql_end [$r] .=  " LEFT JOIN " . MAIN_DB_PREFIX . "lead_extrafields as extra ON extra.fk_object=l.rowid";
		$this->export_sql_end [$r] .= ' WHERE l.entity IN (' . getEntity("lead", 1) . ')';

		$keyforselect='lead'; $keyforelement='lead@lead'; $keyforaliasextra='extra';
		include DOL_DOCUMENT_ROOT.'/core/extrafieldsinexport.inc.php';

		//Export propal not linked with lead
		$r ++;
		$propalTotalSqlCol = 'p.total_ttc';
		$this->export_code [$r] = $this->rights_class . '_' . $r;
		$this->export_label [$r] = 'ExportDataset_leadPropal';
		$this->export_icon [$r] = 'lead@lead';
		$this->export_permission [$r] = array (
				array (
						"lead",
						"export"
				)
		);
		$this->export_fields_array [$r] = array (
				'p.rowid' => 'Id',
				'p.ref' => 'Ref',
				'so.nom' => 'Company',
				$propalTotalSqlCol => 'TotalTTC',
				'p.fk_statut' => 'Status',
		);
		$this->export_TypeFields_array [$r] = array (
				'p.rowid' => 'Text',
				'p.ref' => 'Text',
				'so.nom' => 'Text',
				$propalTotalSqlCol => 'Numeric',
				'p.fk_statut' => 'Status',
		);
		$this->export_entities_array [$r] = array (
				'p.rowid' => 'propal',
				'p.ref' => 'propal',
				'so.nom' => 'company',
				$propalTotalSqlCol => 'propal',
				'p.fk_statut' => 'propal',
		);

		$this->export_sql_start [$r] = 'SELECT DISTINCT ';
		$this->export_sql_end [$r] = ' FROM ' . MAIN_DB_PREFIX . 'propal as p';
		$this->export_sql_end [$r] .=  " INNER JOIN " . MAIN_DB_PREFIX . "societe as so ON so.rowid=p.fk_soc";
		$this->export_sql_end [$r] .= ' WHERE so.entity IN (' . getEntity("societe", 1) . ')';
		$this->export_sql_end [$r] .= '  AND p.rowid NOT IN (SELECT t.fk_source FROM ' . MAIN_DB_PREFIX . 'element_element as t WHERE t.sourcetype=\'propal\' AND t.targettype=\'lead\')';

	}

	/**
	 * Function called when module is enabled.
	 * The init function add constants, boxes, permissions and menus
	 * (defined in constructor) into Dolibarr database.
	 * It also creates data directories
	 *
	 * @param string $options Enabling module ('', 'noboxes')
	 * @return int if OK, 0 if KO
	 */
	public function init($options = '')
	{
		$sql = array();
		$result = $this->loadTables();

		return $this->_init($sql, $options);
	}

	/**
	 * Function called when module is disabled.
	 * Remove from database constants, boxes and permissions from Dolibarr database.
	 * Data directories are not deleted
	 *
	 * @param string $options Enabling module ('', 'noboxes')
	 * @return int if OK, 0 if KO
	 */
	public function remove($options = '')
	{
		$sql = array();

		return $this->_remove($sql, $options);
	}

	/**
	 * Create tables, keys and data required by module
	 * Files llx_table1.sql, llx_table1.key.sql llx_data.sql with create table, create keys
	 * and create data commands must be stored in directory /lead/sql/
	 * This function is called by this->init
	 *
	 * @return int if KO, >0 if OK
	 */
	private function loadTables()
	{
		return $this->_load_tables('/lead/sql/');
	}
}
