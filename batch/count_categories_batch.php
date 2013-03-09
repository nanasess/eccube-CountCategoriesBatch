<?php
$pwd = realpath(dirname( __FILE__));
require_once $pwd . '/../../../../../html/require.php';

// Webから実行されないように
if ($_SERVER["REQUEST_METHOD"] == "POST" || $_SERVER["REQUEST_METHOD"] == "GET") {
    GC_Utils::gfPrintLog($_SERVER["REQUEST_METHOD"] . " Requests by" . print_r($_REQUEST, true) . ", this script is command line only.");
    header("HTTP/1.1 400 Bad Request");
    exit(1);
}

while (@ob_end_flush());

// ロックファイル. 内容は作成日時
define('COUNT_CATEGORIES_LOCK', $pwd . '/count_categories.lock');

printOut('START count categories batch');
// ロックファイルの存在チェック
if (file_exists(COUNT_CATEGORIES_LOCK)) {
    $mtime = trim(file_get_contents(COUNT_CATEGORIES_LOCK));
    printOut('Lockfile exists: ' . date('Y-m-d H:i:s', $mtime));
    // 作成日時から 1時間以上経過していれば削除
    if ($mtime + (60 * 60) < time()) {
        printOut('unlink lockfile.');
        unlink(COUNT_CATEGORIES_LOCK);
    } else {
        printOut('END count categories not exceed.');
        exit(1);
    }
}

$mtime = time();
printOut('create lockfile: ' . $mtime);
$result = file_put_contents(COUNT_CATEGORIES_LOCK, $mtime, LOCK_EX);
if ($result === false) {
    printOut('END can\'t Lockfile create.');
    exit(1);
}

printOut('Executable sfCountCategory...');
$start = microtime(true);
$objDb = new SC_Helper_DB_AsyncCountCategories();
$objDb->sfCountCategoryImpl();
$end = microtime(true);
printOut('done. => ' . ($end - $start) . ' sec');
if (unlink(COUNT_CATEGORIES_LOCK)) {
    printOut('lockfile deleted.');
} else {
    printOut('Can\'t delete lockfile!');
    exit(1);
}
printOut('END Finished Successful.');
exit(0);


function printOut($msg = null) {
    GC_Utils_Ex::gfPrintLog($msg);
    echo $msg . "\n";
}