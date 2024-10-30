<?php

namespace Jobsearch\Helper;

use Jobsearch\SQL;

class Migration
{
   private $versions = [];

   public function __construct(){
       $this->versions = $this->get_migration();
   }

   private function get_migration(){
       global $wpdb;
       $migration = [];
       $request = "SELECT sName FROM " . TDB_TABLE_MIGRATION;
       $exec =  $wpdb->get_results($request);

       foreach ($exec as $ligneResult) {
           $migration[$ligneResult->sName] = $ligneResult->sName;
       }

       return $migration;
   }

   public function migrate(){
       $version = new Version();
       $sql = new SQL();

       // check migration and update
       if(!isset($this->versions['20191130'])){
           $version->tdb_version_20191130($sql);
       }
       if(!isset($this->versions['20220224'])){
           $version->tdb_version_20220224($sql);
       }

       if(!isset($this->versions['20220412'])){
           $version->tdb_version_20220412($sql);
       }
   }
}
