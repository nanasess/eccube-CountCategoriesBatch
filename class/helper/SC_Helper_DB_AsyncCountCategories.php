<?php
require_once realpath(dirname( __FILE__)) . '/../../define.php';
require_once CLASS_REALDIR . 'helper/SC_Helper_DB.php';

class SC_Helper_DB_AsyncCountCategories extends SC_Helper_DB {
    function sfCountCategory($objQuery = NULL, $is_force_all_count = false) {
        $batch = realpath(dirname( __FILE__)) . '/../../batch/count_categories_batch.php';
        if (file_exists($batch)) {
            exec(PHP_EXEC_PATH . ' ' . $batch . ' > /dev/null &');
        } else {
            GC_Utils_Ex::gfPrintLog('Can\'t find count_categories_batch.php');
        }
    }

    public function sfCountCategoryImpl() {
        parent::sfCountCategory();
    }
}