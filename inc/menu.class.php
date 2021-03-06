<?php

/**
 * FusionInventory
 *
 * Copyright (C) 2010-2016 by the FusionInventory Development Team.
 *
 * http://www.fusioninventory.org/
 * https://github.com/fusioninventory/fusioninventory-for-glpi
 * http://forge.fusioninventory.org/
 *
 * ------------------------------------------------------------------------
 *
 * LICENSE
 *
 * This file is part of FusionInventory project.
 *
 * FusionInventory is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * FusionInventory is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with FusionInventory. If not, see <http://www.gnu.org/licenses/>.
 *
 * ------------------------------------------------------------------------
 *
 * This file is used to manage the menu of plugin FusionInventory.
 *
 * ------------------------------------------------------------------------
 *
 * @package   FusionInventory
 * @author    David Durieux
 * @copyright Copyright (c) 2010-2016 FusionInventory team
 * @license   AGPL License 3.0 or (at your option) any later version
 *            http://www.gnu.org/licenses/agpl-3.0-standalone.html
 * @link      http://www.fusioninventory.org/
 * @link      https://github.com/fusioninventory/fusioninventory-for-glpi
 *
 */

if (!defined('GLPI_ROOT')) {
   die("Sorry. You can't access directly to this file");
}

/**
 * Manage the menu of plugin FusionInventory.
 */
class PluginFusioninventoryMenu extends CommonGLPI {

   /**
    * Get name of this type by language of the user connected
    *
    * @param integer $nb number of elements
    * @return string name of this type
    */
   static function getTypeName($nb=0) {
      return 'FusionInventory';
   }



   /**
    * Check if can view item
    *
    * @return boolean
    */
   static function canView() {
      $can_display = FALSE;
      $profile = new PluginFusioninventoryProfile();

      foreach ($profile->getAllRights() as $right) {
         if (Session::haveRight($right['field'], READ)) {
            $can_display = TRUE;
            break;
         }
      }
      return $can_display;
   }



   /**
    * Check if can create an item
    *
    * @return boolean
    */
   static function canCreate() {
      return FALSE;
   }



   /**
    * Get the menu name
    *
    * @return string
    */
   static function getMenuName() {
      return self::getTypeName();
   }



   /**
    * Get additional menu options and breadcrumb
    *
    * @global array $CFG_GLPI
    * @return array
    */
   static function getAdditionalMenuOptions() {
      global $CFG_GLPI;

      $elements = array(
          'iprange'                    => 'PluginFusioninventoryIPRange',
          'config'                     => 'PluginFusioninventoryConfig',
          'task'                       => 'PluginFusioninventoryTask',
          'timeslot'                   => 'PluginFusioninventoryTimeslot',
          'unmanaged'                  => 'PluginFusioninventoryUnmanaged',
          'inventoryruleimport'        => 'PluginFusioninventoryInventoryRuleImport',
          'inventoryruleentity'        => 'PluginFusioninventoryInventoryRuleEntity',
          'inventoryrulelocation'      => 'PluginFusioninventoryInventoryRuleLocation',
          'collectrule'                => 'PluginFusioninventoryCollectRule',
          'inventorycomputerblacklist' => 'PluginFusioninventoryInventoryComputerBlacklist',
          'configsecurity'             => 'PluginFusioninventoryConfigSecurity',
          'credential'                 => 'PluginFusioninventoryCredential',
          'credentialip'               => 'PluginFusioninventoryCredentialIp',
          'collect'                    => 'PluginFusioninventoryCollect',
          'deploypackage'              => 'PluginFusioninventoryDeployPackage',
          'deploymirror'               => 'PluginFusioninventoryDeployMirror',
          'deploygroup'                => 'PluginFusioninventoryDeployGroup',
          'ignoredimportdevice'        => 'PluginFusioninventoryIgnoredimportdevice',
          'ruledictionnarycomputerarch'=> 'PluginFusioninventoryRuleDictionnaryComputerArch'
      );
      $options = array();

      $options['menu']['title'] = self::getTypeName();
      $options['menu']['page']  = self::getSearchURL(false);
      if (Session::haveRight('plugin_fusioninventory_configuration', READ)) {
         $options['menu']['links']['config']  = PluginFusioninventoryConfig::getFormURL(false);
      }
      foreach ($elements as $type => $itemtype) {
         $options[$type] = array(
              'title' => $itemtype::getTypeName(),
              'page'  => $itemtype::getSearchURL(false));
         $options[$type]['links']['search'] = $itemtype::getSearchURL(false);
         if ($itemtype::canCreate()) {
            if ($type != 'ignoredimportdevice') {
               $options[$type]['links']['add'] = $itemtype::getFormURL(false);
            }
         }
         if (Session::haveRight('plugin_fusioninventory_configuration', READ)) {
            $options[$type]['links']['config']  = PluginFusioninventoryConfig::getFormURL(false);
         }
      }
      // hack for config
      $options['config']['page'] = PluginFusioninventoryConfig::getFormURL(False);

      // Add icon for import package
      $img = Html::image($CFG_GLPI["root_doc"] . "/plugins/fusioninventory/pics/menu_import.png",
                                      array('alt' => __('Import', 'fusioninventory')));
      $options['deploypackage']['links'][$img] = '/plugins/fusioninventory/front/deploypackage.import.php';
      // Add icon for clean unused deploy files
      $img = Html::image($CFG_GLPI["root_doc"] . "/plugins/fusioninventory/pics/menu_cleanfiles.png",
                                      array('alt' => __('Clean unused files', 'fusioninventory')));
      $options['deploypackage']['links'][$img] = '/plugins/fusioninventory/front/deployfile.clean.php';

      // Add icon for documentation
      $img = Html::image($CFG_GLPI["root_doc"] . "/plugins/fusioninventory/pics/books.png",
                                      array('alt' => __('Import', 'fusioninventory')));
      $options['menu']['links'][$img] = '/plugins/fusioninventory/front/documentation.php';

      $options['agent'] = array(
           'title' => PluginFusioninventoryAgent::getTypeName(),
           'page'  => PluginFusioninventoryAgent::getSearchURL(false),
           'links' => array(
               'search' => PluginFusioninventoryAgent::getSearchURL(false)
           ));
      if (Session::haveRight('plugin_fusioninventory_configuration', READ)) {
         $options['agent']['links']['config']  = PluginFusioninventoryConfig::getFormURL(false);
      }
      return $options;
   }


   /**
    * Display the menu of plugin FusionInventory
    *
    * @global array $CFG_GLPI
    * @param string $type
    */
   static function displayMenu($type = "big") {
      global $CFG_GLPI;

      if (PLUGIN_FUSIONINVENTORY_OFFICIAL_RELEASE != 1) {
         echo "<center>";
         echo "<a href='http://forge.fusioninventory.org/projects/fusioninventory-for-glpi/".
                 "wiki/Beta_test'>";
         echo "<img src='".$CFG_GLPI['root_doc']."/plugins/fusioninventory/pics/beta.png'/></a>";
         echo "&nbsp;<a href='https://www.transifex.com/projects/p/FusionInventory/resource/".
                 "plugin-fusioninventory-084/'>";
         echo "<img src='".$CFG_GLPI['root_doc']."/plugins/fusioninventory/pics/Translate.png'/>".
                 "</a>";
         echo "<H1>Version '".PLUGIN_FUSIONINVENTORY_REALVERSION."'</H1></center><br/>\n";
      }

      $pfEntity = new PluginFusioninventoryEntity();
      if (strlen($pfEntity->getValue('agent_base_url', 0))<10
              && !strstr($_SERVER['PHP_SELF'], 'front/config.form.php')) {
         echo "<div class='msgboxmonit msgboxmonit-red'>";
         print "<center><a href=\"".$CFG_GLPI['root_doc']."/front/entity.form.php?id=0&forcetab=PluginFusioninventoryEntity$0\">";
         print __('The server needs to know the URL the agents use to access the server. Please '.
                 'configure it in the General Configuration page.', 'fusioninventory');
         print "</a></center>";
         echo "</div>";
         exit;
      }

      // Check if cron GLPI running
      $cronTask = new CronTask();
      $cronTask->getFromDBbyName('PluginFusioninventoryTask', 'taskscheduler');
      if ($cronTask->fields['lastrun'] == ''
              OR strtotime($cronTask->fields['lastrun']) < strtotime("-3 day")) {
         $message = __('GLPI cron not running, see ', 'fusioninventory');
         $message .= " <a href='http://fusioninventory.org/documentation/fi4g/cron.html'>".__('documentation', 'fusioninventory')."</a>";
         Html::displayTitle($CFG_GLPI['root_doc']."/pics/warning.png", $message, $message);
      }

      $width_status = 0;

      echo "<div align='center' style='height: 35px; display: inline-block; width: 100%; margin: 0 auto;'>";
      echo "<table width='100%'>";

      echo "<tr>";
      echo "<td align='center'>";

      echo "<table>";
      echo "<tr>";
      echo "<td>";

      /*
       * General
       */
      $a_menu = array();
      if (Session::haveRight('plugin_fusioninventory_agent', READ)) {
         $a_menu[0]['name'] = __('Agents management', 'fusioninventory');
         $a_menu[0]['pic']  = $CFG_GLPI['root_doc']."/plugins/fusioninventory/pics/menu_agents.png";
         $a_menu[0]['link'] = Toolbox::getItemTypeSearchURL('PluginFusioninventoryAgent');
      }

      if (Session::haveRight('plugin_fusioninventory_group', READ)) {
         $a_menu[2]['name'] = __('Groups of computers', 'fusioninventory');
         $a_menu[2]['pic']  = $CFG_GLPI['root_doc']."/plugins/fusioninventory/pics/menu_group.png";
         $a_menu[2]['link'] = $CFG_GLPI['root_doc']."/plugins/fusioninventory/front/deploygroup.php";
      }

      if (Session::haveRight('config', UPDATE) || Session::haveRight('plugin_fusioninventory_configuration', UPDATE)) {
         $a_menu[3]['name'] = __('General configuration', 'fusioninventory');
         $a_menu[3]['pic']  = $CFG_GLPI['root_doc']."/plugins/fusioninventory/pics/menu_agents.png";
         $a_menu[3]['link'] = $CFG_GLPI['root_doc'].
                                 "/plugins/fusioninventory/front/config.form.php";
      }

      if (!empty($a_menu)) {
         $width_status = PluginFusioninventoryMenu::htmlMenu(__('General', 'fusioninventory'),
                                                             $a_menu,
                                                             $type,
                                                             $width_status);
      }

      /*
       * Tasks
       */
      $a_menu = array();
      if (Session::haveRight('plugin_fusioninventory_task', READ)) {
         //$a_menu[1]['name'] = __('Task management', 'fusioninventory')." (".__s('Summary').")";
         //$a_menu[1]['pic']  = $CFG_GLPI['root_doc']."/plugins/fusioninventory/pics/menu_task.png";
         //$a_menu[1]['link'] = $CFG_GLPI['root_doc'].
         //                        "/plugins/fusioninventory/front/tasksummary.php";

         $a_menu[2]['name'] = __('Task management', 'fusioninventory');
         $a_menu[2]['pic']  = $CFG_GLPI['root_doc']."/plugins/fusioninventory/pics/menu_task.png";
         $a_menu[2]['link'] = Toolbox::getItemTypeSearchURL('PluginFusioninventoryTask');

         $a_menu[3]['name'] = __('Monitoring / Logs', 'fusioninventory');
         $a_menu[3]['pic']  = $CFG_GLPI['root_doc'].
                                 "/plugins/fusioninventory/pics/menu_runningjob.png";
         $a_menu[3]['link'] = Toolbox::getItemTypeSearchURL('PluginFusioninventoryTaskJob');
      }

      if (Session::haveRight('plugin_fusioninventory_importxml', CREATE)) {
         $a_menu[0]['name'] = __('Import agent XML file', 'fusioninventory');
         $a_menu[0]['pic']  = $CFG_GLPI['root_doc'].
                                 "/plugins/fusioninventory/pics/menu_importxml.png";
         $a_menu[0]['link'] = $CFG_GLPI['root_doc'].
                                 "/plugins/fusioninventory/front/inventorycomputerimportxml.php";
      }

      if (Session::haveRight("plugin_fusioninventory_collect", READ)) {
         $a_menu[11]['name'] = __('Computer information', 'fusioninventory');
         $a_menu[11]['pic']  = $CFG_GLPI['root_doc']."/plugins/fusioninventory/pics/menu_task.png";
         $a_menu[11]['link'] = Toolbox::getItemTypeSearchURL('PluginFusioninventoryCollect');
      }

      if (Session::haveRight('plugin_fusioninventory_task', READ)) {
         $a_menu[12]['name'] = __('Time slot', 'fusioninventory');
         $a_menu[12]['pic']  = $CFG_GLPI['root_doc']."/plugins/fusioninventory/pics/menu_timeslot.png";
         $a_menu[12]['link'] = Toolbox::getItemTypeSearchURL('PluginFusioninventoryTimeslot');
      }

      if (!empty($a_menu)) {
         $width_status = PluginFusioninventoryMenu::htmlMenu(__('Tasks', 'fusioninventory'),
                                                             $a_menu,
                                                             $type,
                                                             $width_status);
      }

      /*
       * Rules
       */
      $a_menu = array();

      if (Session::haveRight('plugin_fusioninventory_ruleimport', READ)) {
         $a_menu[1]['name'] = __('Equipment import and link rules', 'fusioninventory');
         $a_menu[1]['pic']  = $CFG_GLPI['root_doc']."/plugins/fusioninventory/pics/menu_rules.png";
         $a_menu[1]['link'] = Toolbox::getItemTypeSearchURL(
                    'PluginFusioninventoryInventoryRuleImport'
                 );
      }

      if (Session::haveRight('plugin_fusioninventory_ignoredimportdevice', READ)) {
         $a_menu[2]['name'] = __('Ignored import devices', 'fusioninventory');
         $a_menu[2]['pic']  = $CFG_GLPI['root_doc']."/plugins/fusioninventory/pics/menu_rules.png";
         $a_menu[2]['link'] = Toolbox::getItemTypeSearchURL(
                    'PluginFusioninventoryIgnoredimportdevice'
                 );
      }

      if (Session::haveRight('plugin_fusioninventory_ruleentity', READ)) {
         $a_menu[3]['name'] = __('Computer entity rules', 'fusioninventory');
         $a_menu[3]['pic']  = $CFG_GLPI['root_doc']."/plugins/fusioninventory/pics/menu_rules.png";
         $a_menu[3]['link'] = $CFG_GLPI['root_doc'].
                                 "/plugins/fusioninventory/front/inventoryruleentity.php";
      }

      if (Session::haveRight('plugin_fusioninventory_rulelocation', READ)) {
         $a_menu[4]['name'] = __('Computer location rules', 'fusioninventory');
         $a_menu[4]['pic']  = $CFG_GLPI['root_doc']."/plugins/fusioninventory/pics/menu_rules.png";
         $a_menu[4]['link'] = $CFG_GLPI['root_doc'].
                                 "/plugins/fusioninventory/front/inventoryrulelocation.php";
      }

      if (Session::haveRight("plugin_fusioninventory_rulecollect", READ)) {
         $a_menu[5]['name'] = __('Computer information rules', 'fusioninventory');
         $a_menu[5]['pic']  = $CFG_GLPI['root_doc']."/plugins/fusioninventory/pics/menu_rules.png";
         $a_menu[5]['link'] = $CFG_GLPI['root_doc'].
                                 "/plugins/fusioninventory/front/collectrule.php";
      }

      if (Session::haveRight('plugin_fusioninventory_blacklist', READ)) {
         $a_menu[6]['name'] = _n('Blacklist', 'Blacklists', 1);
         $a_menu[6]['pic']  = $CFG_GLPI['root_doc'].
                                 "/plugins/fusioninventory/pics/menu_blacklist.png";
         $a_menu[6]['link'] = $CFG_GLPI['root_doc'].
                                 "/plugins/fusioninventory/front/inventorycomputerblacklist.php";
      }

      if (!empty($a_menu)) {
         $width_status = PluginFusioninventoryMenu::htmlMenu(__('Rules', 'fusioninventory'),
                                                             $a_menu,
                                                             $type,
                                                             $width_status);
      }

      /*
       * Network
       */
      $a_menu = array();

      if (Session::haveRight('plugin_fusioninventory_iprange', READ)) {
         $a_menu[] = array(
            'name' => __('IP Ranges', 'fusioninventory'),
            'pic'  => $CFG_GLPI['root_doc'].
                                 "/plugins/fusioninventory/pics/menu_rangeip.png",
            'link' => Toolbox::getItemTypeSearchURL('PluginFusioninventoryIPRange')
         );
      }

      if (Session::haveRight('plugin_fusioninventory_credentialip', READ)) {
         $a_menu[] = array(
            'name' => __('Remote devices to inventory (VMware)', 'fusioninventory'),
            'pic'  => $CFG_GLPI['root_doc'].
                                 "/plugins/fusioninventory/pics/menu_credentialips.png",
            'link' => Toolbox::getItemTypeSearchURL('PluginFusioninventoryCredentialip')
         );
      }

      if (Session::haveRight('plugin_fusioninventory_configsecurity', READ)) {
         $a_menu[] = array(
            'name' => __('SNMP authentication', 'fusioninventory'),
            'pic'  => $CFG_GLPI['root_doc'].
                                 "/plugins/fusioninventory/pics/menu_authentification.png",
            'link' => $CFG_GLPI['root_doc'].
                                 "/plugins/fusioninventory/front/configsecurity.php"
         );
      }

      if (Session::haveRight('plugin_fusioninventory_credential', READ)) {
         $a_menu[] = array(
            'name' => __('Authentication for remote devices (VMware)', 'fusioninventory'),
            'pic'  => $CFG_GLPI['root_doc'].
                                 "/plugins/fusioninventory/pics/menu_authentification.png",
            'link' => Toolbox::getItemTypeSearchURL('PluginFusioninventoryCredential')
         );
      }

      if (Session::haveRight('plugin_fusioninventory_task', READ)) {
         $a_menu[] = array(
            'name' => __('Discovery status', 'fusioninventory'),
            'pic'  =>   $CFG_GLPI['root_doc'].
                           "/plugins/fusioninventory/pics/menu_discovery_status.png",
            'link' =>   $CFG_GLPI['root_doc'].
                           "/plugins/fusioninventory/front/statediscovery.php"
         );

         $a_menu[] = array(
               'name' => __('Network inventory status', 'fusioninventory'),
               'pic' =>    $CFG_GLPI['root_doc'].
                              "/plugins/fusioninventory/pics/menu_inventory_status.png",
               'link' =>   $CFG_GLPI['root_doc'].
                              "/plugins/fusioninventory/front/stateinventory.php",
         );
      }

      if (Session::haveRight('plugin_fusioninventory_model', READ)) {
         $a_menu[] = array(
            'name' => __('SNMP models creation', 'fusioninventory'),
            'pic'  => $CFG_GLPI['root_doc']."/plugins/fusioninventory/pics/menu_constructmodel.png",
            'link' => $CFG_GLPI['root_doc']."/plugins/fusioninventory/front/constructmodel.php"
         );
      }

      if (!empty($a_menu)) {
         $width_status = PluginFusioninventoryMenu::htmlMenu(__('Networking', 'fusioninventory'),
                                                             $a_menu,
                                                             $type,
                                                             $width_status);
      }

      /*
       * Deploy
       */
      $a_menu = array();

      if (Session::haveRight('plugin_fusioninventory_package', READ)) {
         $a_menu[] =array(
            'name' => __('Package management', 'fusioninventory'),
            'pic'  => $CFG_GLPI['root_doc'].
                                 "/plugins/fusioninventory/pics/menu_package.png",
            'link' => $CFG_GLPI['root_doc'].
                                 "/plugins/fusioninventory/front/deploypackage.php"
         );
      }

      if (Session::haveRight('plugin_fusioninventory_deploymirror', READ)) {
         $a_menu[1]['name'] = __('Mirror servers', 'fusioninventory');
         $a_menu[1]['pic']  = $CFG_GLPI['root_doc']."/plugins/fusioninventory/pics/menu_files.png";
         $a_menu[1]['link'] = $CFG_GLPI['root_doc']."/plugins/fusioninventory/front/deploymirror.php";
      }

      if (!empty($a_menu)) {
         $width_status = PluginFusioninventoryMenu::htmlMenu(__('Deploy', 'fusioninventory'),
                                                             $a_menu,
                                                             $type,
                                                             $width_status);
      }

      /*
       * Guide
       */
      $a_menu = array();

         $a_menu[] = array(
            'name' => "FI> ".__('Computer inv.', 'fusioninventory'),
            'pic'  => "",
            'link' => $CFG_GLPI['root_doc']."/plugins/fusioninventory/front/menu_inventory.php"
         );

         $a_menu[] = array(
            'name' => "FI> ".__('SNMP inv.', 'fusioninventory'),
            'pic'  => "",
            'link' => $CFG_GLPI['root_doc']."/plugins/fusioninventory/front/menu_snmpinventory.php"
         );

      if (!empty($a_menu)) {
         $width_status = PluginFusioninventoryMenu::htmlMenu(__('Guide', 'fusioninventory'),
                                                             $a_menu,
                                                             $type,
                                                             $width_status);
      }


      echo "</td>";
      echo "</tr>";
      echo "</table>";

      echo "</td>";
      echo "</tr>";
      echo "</table>";
      echo "</div><br/><br/><br/>";
   }



   /**
    * Menu for computer inventory
    *
    * @global array $CFG_GLPI
    */
   static function displayMenuInventory() {
      global $CFG_GLPI;

      echo "<table class='tab_cadre_fixe'>";
      echo "<tr class='tab_bg_1'>";
      echo "<th colspan='2'>";
      echo __('Statistics', 'fusioninventory')." / ".__('Number of computer inventories of last hours', 'fusioninventory');
      echo "</th>";
      echo "</tr>";
      $dataInventory = PluginFusioninventoryInventoryComputerStat::getLastHours(23);
      echo "<tr class='tab_bg_1' height='280'>";
      echo "<td colspan='2' height='280'>";
      self::showChartBar('nbinventory', $dataInventory, '', 940);
      echo "</td>";
      echo "</tr>";

      echo "<tr class='tab_bg_1'>";
      echo "<th colspan='2'>";
      echo __('This is the steps to configure FusionInventory plugin for computer inventory', 'fusioninventory');
      echo "</th>";
      echo "</tr>";

      $a_steps = array(
          array(
              'text' => __('Configure frequency of agent contact (and so each inventory)', 'fusioninventory'),
              'url'  => $CFG_GLPI['root_doc'].
                                 "/plugins/fusioninventory/front/config.form.php?forcetab=PluginFusioninventoryConfig$0"
          ),
          array(
              'text' => __('Configure inventory options', 'fusioninventory'),
              'url'  => $CFG_GLPI['root_doc'].
                                 "/plugins/fusioninventory/front/config.form.php?forcetab=PluginFusioninventoryConfig$1"
          ),
          array(
              'text' => __('Define rules for entity', 'fusioninventory'),
              'url'  => $CFG_GLPI['root_doc'].
                                 "/plugins/fusioninventory/front/inventoryruleentity.php"
          ),
          array(
              'text' => __('Define rules for location', 'fusioninventory'),
              'url'  => $CFG_GLPI['root_doc'].
                                 "/plugins/fusioninventory/front/inventoryrulelocation.php"
          ),
          array(
              'text' => __('Define rules for import : merge and create new computer (CAUTION: same rules for SNMP inventory)', 'fusioninventory'),
              'url'  => $CFG_GLPI['root_doc'].
                                 "/plugins/fusioninventory/front/inventoryruleimport.php"
          )
      );

      $i = 1;
      foreach ($a_steps as $data) {
         echo "<tr class='tab_bg_1'>";
         echo "<th width='25'>";
         echo $i.".";
         echo "</th>";
         echo "<td>";
         echo '<a href="'.$data['url'].'" target="_blank">'.$data['text'].'</a>';
         echo "</td>";
         echo "</tr>";
         $i++;
      }
      echo "</table>";
   }



   /**
    * Menu for SNMP inventory
    *
    * @global array $CFG_GLPI
    */
   static function displayMenuSNMPInventory() {
      global $CFG_GLPI;

      echo "<table class='tab_cadre_fixe'>";
      echo "<tr class='tab_bg_1'>";
      echo "<th colspan='2'>";
      echo __('Statistics', 'fusioninventory');
      echo "</th>";
      echo "</tr>";
      $networkequipment = countElementsInTable('glpi_plugin_fusioninventory_networkequipments');
      $printer    = countElementsInTable('glpi_plugin_fusioninventory_printers');

      $dataSNMP = array();
      $dataSNMP[] = array(
          'key' => 'NetworkEquipments (SNMP) : '.$networkequipment,
          'y'   => $networkequipment,
          'color' => '#3d94ff'
      );
      $dataSNMP[] = array(
          'key' => 'Printers (SNMP) : '.$printer,
          'y'   => $printer,
          'color' => '#3dff7d'
      );
      echo "<tr class='tab_bg_1' height='100'>";
      echo "<td colspan='2' height='220'>";
      self::showChart('snmp', $dataSNMP);
      echo "</td>";
      echo "</tr>";

      echo "<tr class='tab_bg_1'>";
      echo "<th colspan='2'>";
      echo __('This is the steps to configure FusionInventory plugin for SNMP inventory (swicth, router, network printer)', 'fusioninventory');
      echo "</th>";
      echo "</tr>";

      $a_steps = array(
          array(
              'text' => __('Configure SNMP credentials', 'fusioninventory'),
              'url'  => $CFG_GLPI['root_doc'].
                                 "/plugins/fusioninventory/front/configsecurity.php"
          ),
          array(
              'text' => __('Define rules for import : merge and create new devices (CAUTION: same rules for computer inventory)', 'fusioninventory'),
              'url'  => $CFG_GLPI['root_doc'].
                                 "/plugins/fusioninventory/front/inventoryruleimport.php"
          ),
          array(
              'text' => __('`Network Discovery`, used to discover the devices on the network', 'fusioninventory'),
              'url'  => "",
              'title'=> TRUE
          ),
          array(
              'text' => __('Define IP Ranges of your network + related SNMP authentication', 'fusioninventory'),
              'url'  => $CFG_GLPI['root_doc'].
                                 "/plugins/fusioninventory/front/iprange.php"
          ),
          array(
              'text' => __('Define an agent allowed to discover the network', 'fusioninventory'),
              'url'  => $CFG_GLPI['root_doc'].
                                 "/plugins/fusioninventory/front/config.form.php?forcetab=PluginFusioninventoryAgentmodule$1"
          ),
          array(
              'text' => __('Create a new Task with discovery module and the agent defined previously', 'fusioninventory'),
              'url'  => $CFG_GLPI['root_doc'].
                                 "/plugins/fusioninventory/front/task.php"
          ),
          array(
              'text' => __('If you have devices not typed, import them from unmanaged devices', 'fusioninventory'),
              'url'  => $CFG_GLPI['root_doc'].
                                 "/plugins/fusioninventory/front/unmanaged.php"
          ),
          array(
              'text' => __('`Network Inventory`, used to complete inventory the discovered devices', 'fusioninventory'),
              'url'  => "",
              'title'=> TRUE
          ),
          array(
              'text' => __('Define an agent allowed to inventory the network by SNMP', 'fusioninventory'),
              'url'  => $CFG_GLPI['root_doc'].
                                 "/plugins/fusioninventory/front/config.form.php?forcetab=PluginFusioninventoryAgentmodule$1"
          ),
          array(
              'text' => __('Create a new Task with network inventory module and the agent defined previously', 'fusioninventory'),
              'url'  => $CFG_GLPI['root_doc'].
                                 "/plugins/fusioninventory/front/task.php"
          ),
      );

      $i = 1;
      foreach ($a_steps as $data) {
         echo "<tr class='tab_bg_1'>";
         if (isset($data['title'])
                 && $data['title']) {
            echo "<th colspan='2'>";
            echo $data['text'];
            echo "</th>";
         } else {
            echo "<th width='25'>";
            echo $i.".";
            echo "</th>";
            echo "<td>";
            if ($data['url'] == '') {
               echo $data['text'];
            } else {
               echo '<a href="'.$data['url'].'" target="_blank">'.$data['text'].'</a>';
            }
            echo "</td>";
            $i++;
         }
         echo "</tr>";
      }
      echo "</table>";
   }



   /**
    * Display menu in html
    *
    * @global array $CFG_GLPI
    * @param string $menu_name
    * @param array $a_menu
    * @param string $type
    * @param integer $width_status
    * @return integer
    */
   static function htmlMenu($menu_name, $a_menu = array(), $type = "big", $width_status=300) {
      global $CFG_GLPI;

      $width_max = 1250;

      $width = 180;

      if (($width + $width_status) > $width_max) {
         $width_status = 0;
         echo "</td>";
         echo "</tr>";
         echo "</table>";
         echo "<table>";
         echo "<tr>";
         echo "<td valign='top'>";
      } else {
         echo "</td>";
         echo "<td valign='top'>";
      }
      $width_status = ($width + $width_status);

      echo "<table class='tab_cadre' style='position: relative; z-index: 30;'
         onMouseOver='document.getElementById(\"menu".$menu_name."\").style.display=\"block\"'
         onMouseOut='document.getElementById(\"menu".$menu_name."\").style.display=\"none\"'>";

      echo "<tr>";
      echo "<th colspan='".count($a_menu)."' nowrap width='".$width."'>
         <img src='".$CFG_GLPI["root_doc"]."/pics/deplier_down.png' />
         &nbsp;".str_replace("FusionInventory ", "", $menu_name)."&nbsp;
         <img src='".$CFG_GLPI["root_doc"]."/pics/deplier_down.png' />
      </th>";
      echo "</tr>";

      echo "<tr class='tab_bg_1' id='menu".$menu_name."' style='display:none; position: relative; z-index: 30;'>";
      echo "<td>";
      echo "<table>";
      foreach ($a_menu as $menu_id) {
         echo "<tr>";
         $menu_id['pic'] = str_replace("/menu_", "/menu_mini_", $menu_id['pic']);
         echo "<th>";
         if (!empty($menu_id['pic'])) {
            echo "<img src='".$menu_id['pic']."' width='16' height='16'/>";
         }
         echo "</th>";
         echo "<th colspan='".(count($a_menu) - 1)."' width='".($width - 40)."' style='text-align: left'>
                  <a href='".$menu_id['link']."'>".$menu_id['name']."</a></th>";
         echo "</tr>";
      }
      echo "</table>";

      echo "</td>";
      echo "</tr>";
      echo "</table>";

      return $width_status;
   }



   /**
    * Display the board (graph / stats on FusionInventory plugin page)
    *
    * @global object $DB
    */
   static function board() {
      global $DB;

      // FI Computers
      $fusionComputers    = 0;
      $restrict_entity    = getEntitiesRestrictRequest(" AND", 'comp');
      $query_fi_computers = "SELECT COUNT(comp.`id`) as nb_computers
                             FROM glpi_computers comp
                             LEFT JOIN glpi_plugin_fusioninventory_inventorycomputercomputers fi_comp
                               ON fi_comp.`computers_id` = comp.`id`
                             WHERE comp.`is_deleted`  = '0'
                               AND comp.`is_template` = '0'
                               AND fi_comp.`id` IS NOT NULL
                               $restrict_entity";
      $res_fi_computers = $DB->query($query_fi_computers);
      if ($data_fi_computers = $DB->fetch_assoc($res_fi_computers)) {
         $fusionComputers = $data_fi_computers['nb_computers'];
      }

      // All Computers
      $allComputers    = countElementsInTableForMyEntities('glpi_computers',
                                              "`is_deleted`='0' AND `is_template`='0'");

      $dataComputer = array();
      $dataComputer[] = array(
          'key' => __('FusionInventory computers', 'fusioninventory').' : '.$fusionComputers,
          'y'   => $fusionComputers,
          'color' => '#3dff7d'
      );
      $dataComputer[] = array(
          'key' => __('Other computers', 'fusioninventory').' : '.($allComputers - $fusionComputers),
          'y'   => ($allComputers - $fusionComputers),
          'color' => "#dedede"
      );


      // SNMP
      $networkequipment = 0;
      $restrict_entity  = getEntitiesRestrictRequest(" AND", 'net');
      $query_fi_net = "SELECT COUNT(net.`id`) as nb_net
                             FROM glpi_networkequipments net
                             LEFT JOIN glpi_plugin_fusioninventory_networkequipments fi_net
                               ON fi_net.`networkequipments_id` = net.`id`
                             WHERE net.`is_deleted`  = '0'
                               AND net.`is_template` = '0'
                               AND fi_net.`id` IS NOT NULL
                               $restrict_entity";
      $res_fi_net = $DB->query($query_fi_net);
      if ($data_fi_net = $DB->fetch_assoc($res_fi_net)) {
         $networkequipment = $data_fi_net['nb_net'];
      }

      $printer         = 0;
      $restrict_entity = getEntitiesRestrictRequest(" AND", 'printers');
      $query_fi_printers = "SELECT COUNT(printers.`id`) as nb_printers
                             FROM glpi_printers printers
                             LEFT JOIN glpi_plugin_fusioninventory_printers fi_printer
                               ON fi_printer.`printers_id` = printers.`id`
                             WHERE printers.`is_deleted`  = '0'
                               AND printers.`is_template` = '0'
                               AND fi_printer.`id` IS NOT NULL
                               $restrict_entity";
      $res_fi_printers = $DB->query($query_fi_printers);
      if ($data_fi_printers = $DB->fetch_assoc($res_fi_printers)) {
         $printer = $data_fi_printers['nb_printers'];
      }

      $dataSNMP = array();
      $dataSNMP[] = array(
          'key' => __('NetworkEquipments (SNMP)', 'fusioninventory').' : '.$networkequipment,
          'y'   => $networkequipment,
          'color' => '#3d94ff'
      );
      $dataSNMP[] = array(
          'key' => __('Printers (SNMP)', 'fusioninventory').' : '.$printer,
          'y'   => $printer,
          'color' => '#3dff7d'
      );


      // switches ports
      $allSwitchesPortSNMP = 0;
      $restrict_entity     = getEntitiesRestrictRequest(" AND", 'networkports');
      $query_fi_networkports = "SELECT COUNT(networkports.`id`) as nb_networkports
                             FROM glpi_networkports networkports
                             LEFT JOIN glpi_plugin_fusioninventory_networkports fi_networkports
                               ON fi_networkports.`networkports_id` = networkports.`id`
                             WHERE networkports.`is_deleted`  = '0'
                               AND fi_networkports.`id` IS NOT NULL
                               $restrict_entity";
      $res_fi_networkports = $DB->query($query_fi_networkports);
      if ($data_fi_networkports = $DB->fetch_assoc($res_fi_networkports)) {
         $allSwitchesPortSNMP = $data_fi_networkports['nb_networkports'];
      }


      $query = "SELECT networkports.`id` FROM `glpi_networkports` networkports
              LEFT JOIN `glpi_plugin_fusioninventory_networkports`
                 ON `glpi_plugin_fusioninventory_networkports`.`networkports_id` = networkports.`id`
              LEFT JOIN glpi_networkports_networkports
                  ON (`networkports_id_1`=networkports.`id`
                     OR `networkports_id_2`=networkports.`id`)
              WHERE `glpi_plugin_fusioninventory_networkports`.`id` IS NOT NULL
                  AND `glpi_networkports_networkports`.`id` IS NOT NULL
                  $restrict_entity";
      $result = $DB->query($query);
      $networkPortsLinked = $DB->numrows($result);

      $dataPortL = array();
      $dataPortL[] = array(
          'key' => __('SNMP switch network ports linked', 'fusioninventory').' : '.$networkPortsLinked,
          'y'   => $networkPortsLinked,
          'color' => '#3dff7d'
      );
      $dataPortL[] = array(
          'key' => __('SNMP switch network ports not linked', 'fusioninventory').' : '.($allSwitchesPortSNMP - $networkPortsLinked),
          'y'   => ($allSwitchesPortSNMP - $networkPortsLinked),
          'color' => '#dedede'
      );

      // Ports connected at last SNMP inventory
      $networkPortsConnected = 0;
      $restrict_entity     = getEntitiesRestrictRequest(" AND", 'networkports');
      $query_fi_networkports = "SELECT COUNT(networkports.`id`) as nb_networkports
                             FROM glpi_networkports networkports
                             LEFT JOIN glpi_plugin_fusioninventory_networkports fi_networkports
                               ON fi_networkports.`networkports_id` = networkports.`id`
                             WHERE networkports.`is_deleted`  = '0'
                               AND (fi_networkports.`ifstatus`='1'
                                    OR fi_networkports.`ifstatus`='up')
                               and fi_networkports.`id` IS NOT NULL
                               $restrict_entity";
      $res_fi_networkports = $DB->query($query_fi_networkports);
      if ($data_fi_networkports = $DB->fetch_assoc($res_fi_networkports)) {
         $networkPortsConnected = $data_fi_networkports['nb_networkports'];
      }

      $dataPortC = array();
      $dataPortC[] = array(
          'key' => __('Ports connected', 'fusioninventory').' : '.$networkPortsConnected,
          'y'   => $networkPortsConnected,
          'color' => '#3dff7d'
      );
      $dataPortC[] = array(
          'key' => __('Ports not connected', 'fusioninventory').' : '.($allSwitchesPortSNMP - $networkPortsConnected),
          'y'   => ($allSwitchesPortSNMP - $networkPortsConnected),
          'color' => '#dedede'
      );


      // Number of computer inventories in last hour, 6 hours, 24 hours
      $dataInventory = PluginFusioninventoryInventoryComputerStat::getLastHours();

      // Deploy
      $restrict_entity = getEntitiesRestrictRequest(" AND", 'glpi_plugin_fusioninventory_taskjobs');
      $query = "SELECT `plugin_fusioninventory_tasks_id`
                FROM glpi_plugin_fusioninventory_taskjobs
                WHERE method LIKE '%deploy%'
                  $restrict_entity
                GROUP BY `plugin_fusioninventory_tasks_id`";
      $result = $DB->query($query);
      $a_tasks = array();
      while ($data=$DB->fetch_array($result)) {
         $a_tasks[] = $data['plugin_fusioninventory_tasks_id'];
      }
      $pfTask = new PluginFusioninventoryTask();
      $data = $pfTask->getJoblogs($a_tasks);

      $dataDeploy = array();
      $dataDeploy[0] = array(
          'key' => __('Prepared and waiting', 'fusioninventory'),
          'y'   => 0,
          'color' => '#efefef'
      );
      $dataDeploy[1] = array(
          'key' => __('Running', 'fusioninventory'),
          'y'   => 0,
          'color' => '#aaaaff'
      );
      $dataDeploy[2] = array(
          'key' => __('Successfull', 'fusioninventory'),
          'y'   => 0,
          'color' => '#aaffaa'
      );
      $dataDeploy[3] = array(
          'key' => __('In error', 'fusioninventory'),
          'y'   => 0,
          'color' => '#ff0000'
      );
      foreach ($data['tasks'] as $lev1) {
         foreach ($lev1['jobs'] as $lev2) {
            foreach ($lev2['targets'] as $lev3) {
               $dataDeploy[2]['y'] += count($lev3['counters']['agents_success']);
               $dataDeploy[3]['y'] += count($lev3['counters']['agents_error']);
               $dataDeploy[0]['y'] += count($lev3['counters']['agents_prepared']);
               $dataDeploy[1]['y'] += count($lev3['counters']['agents_running']);
            }
         }
      }
      for ($k=0; $k<4; $k++) {
         $dataDeploy[$k]['key'] .= " : ".$dataDeploy[$k]['y'];
      }


      echo "<div class='fi_board'>";
      self::showChart('computers', $dataComputer);
      self::showChartBar('nbinventory', $dataInventory,
                         __('Number of computer inventories of last hours', 'fusioninventory'));
      self::showChart('deploy', $dataDeploy, __('Deployment', 'fusioninventory'));
      self::showChart('snmp', $dataSNMP);
      self::showChart('ports', $dataPortL);
      self::showChart('portsconnected', $dataPortC);
      echo "</div>";

   }



   /**
    * Display chart
    *
    * @param string $name
    * @param array $data list of data for the chart
    * @param string $title
    */
   static function showChart($name, $data, $title='&nbsp;') {
      echo "<div class='fi_chart donut'>";
      echo "<h2 class='fi_chart_title'>$title</h2>";
      echo '<svg id="'.$name.'"></svg>';
      echo Html::scriptBlock("$(function() {
         statHalfDonut('".$name."', '".json_encode($data)."');
      });");
      echo "</div>";
   }



   /**
    * Display chart bar
    *
    * @param string $name
    * @param array $data list of data for the chart
    * @param string $title
    * @param integer $width
    */
   static function showChartBar($name, $data, $title='', $width=370) {
      echo "<div class='fi_chart bar'>";
      echo "<h2 class='fi_chart_title'>$title</h2>";
      echo '<svg id="'.$name.'"></svg>';
      echo Html::scriptBlock("$(function() {
         statBar('".$name."', '".json_encode($data)."');
      });");
      echo "</div>";
   }
}
