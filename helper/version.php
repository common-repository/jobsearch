<?php
namespace Jobsearch\Helper;

use Jobsearch\SQL;

class Version
{
    function tdb_version_20191130(SQL $sql){
        $sql->tdb_jb_update_line(TDB_TABLE_PARAM, 'sValue', '/api/public/job/search', 'sName', 'LinkSearch');
        $sql->tdb_jb_update_line(TDB_TABLE_PARAM, 'sValue', '/api/public/job', 'sName', 'LinkDetailJob');
        $sql->tdb_jb_update_line(TDB_TABLE_PARAM, 'sValue', '/api/application', 'sName', 'LinkCreate');
        $sql->tdb_jb_update_line(TDB_TABLE_PARAM, 'sValue', '/api/application', 'sName', 'LinkFile');
        $sql->tdb_jb_update_line(TDB_TABLE_PARAM, 'sValue', '/api/public/job/list-options', 'sName', 'LinkOption');
        $sql->tdb_jb_update_line(TDB_TABLE_PARAM, 'sValue', '/api/public/job/search-options', 'sName', 'LinkSearchOption');
        $sql->tdb_jb_update_line(TDB_TABLE_PARAM, 'sValue', '/api/public/image/job', 'sName', 'LinkImage');

        $sql->tdb_jb_insert_line(TDB_TABLE_MIGRATION, array('sName' => '20191130'),'sName');
    }

    function tdb_version_20220224(SQL $sql){
        $sql->tdb_jb_update_line(TDB_TABLE_PARAM, 'sValue', 'emailRequired', 'sName', 'emailRequired');
        $sql->tdb_jb_update_line(TDB_TABLE_PARAM, 'sValue', 'emailsApply', 'sName', 'emailsApply');

        $sql->tdb_jb_insert_line(TDB_TABLE_MIGRATION, array('sName' => '20220224'),'sName');
    }

    function tdb_version_20220412(SQL $sql){

        $sql->tdb_jb_update_table(TDB_TABLE_APPLY, [], ['nIdApi'], 1);
        $sql->tdb_jb_insert_line(TDB_TABLE_MIGRATION, array('sName' => '20220412'),'sName');
    }
}
