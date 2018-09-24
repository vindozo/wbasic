<?php
/* 
    Проект: WBASIC - Акроним Web Beginner's All-purpose Symbolic Instruction Code.
            Высокоуровневый язык программирования применяемый для разработки серверных веб-приложений.
            Философия языка - это должно быть просто для начинающих.
    Версия: 1.3 
    Начало разработки: 24.03.2017, текущей версии: 20.09.2018, релиз текущей версии: 20.09.2018
    Лицензия: ISC
    Авторские права: (c) 2017-2027, Верига Алексей, vindozo@gmail.com
    Разрешается использование, копирование, модификация и/или распространение данного программного обеспечения для любых
    целей за плату или бесплатно, при условии сохранения отметки об авторских правах выше
    и включении данного разрешения во все копии.

    ЭТО ПРОГРАММНОЕ ОБЕСПЕЧЕНИЕ ПРЕДОСТАВЛЯЕТСЯ "КАК ЕСТЬ" И АВТОР ОТКАЗЫВАЕТСЯ ОТ ЛЮБЫХ ГАРАНТИЙ,
    СВЯЗАННЫХ С ДАННЫМ ПРОГРАММНЫМ ОБЕСПЕЧЕНИЕМ, ВКЛЮЧАЯ ВСЕ ПОДРАЗУМЕВАЕМЫЕ ГАРАНТИИ
    ТОВАРНОЙ ПРИГОДНОСТИ И СООТВЕТСТВИЯ ЦЕЛЯМ ИСПОЛЬЗОВАНИЯ. АВТОР НЕ НЕСЁТ ОТВЕТСТВЕННОСТИ ЗА
    ЛЮБОЙ СПЕЦИАЛЬНЫЙ, ПРЯМОЙ, КОСВЕННЫЙ ИЛИ СПРОВОЦИРОВАННЫЙ УЩЕРБ, А ТАКЖЕ ЛЮБОЙ УЩЕРБ
    ИЗ-ЗА НЕВОЗМОЖНОСТИ ИСПОЛЬЗОВАНИЯ, ПОТЕРИ ДАННЫХ ИЛИ ПРИБЫЛИ,
    ПОД ДЕЙСТВИЕМ ВЗЯТЫХ ОБЯЗАТЕЛЬСТВ, НЕБРЕЖНОСТИ ИЛИ ДРУГОГО ВРЕДОНОСНОГО ДЕЙСТВИЯ, ПРОИСХОДЯЩЕГО БЕЗ
    ИЛИ В СВЯЗИ С ИСПОЛЬЗОВАНИЕМ ИЛИ ВЫПОЛНЕНИЕМ ДАННОГО ПРОГРАММНОГО ОБЕСПЕЧЕНИЯ.    
*/

error_reporting(E_ALL);
DEFINE ('CYRILLIC',	'АӘӒӐБВГҒҐЃДЂЕЄЁӖЖӁҖӜЗҘӞИЇЙКҚҠЛЉМНЊҢҤОӨӦПРСҪТЌЋУҰЎӮӰӲФХҺҲЦЧҶЏШЩЪЫӸЬЭЮЯ' ); // полная кирилица для регулярок
mb_internal_encoding('UTF-8'); mb_regex_encoding('UTF-8'); setlocale (LC_ALL, 'ru_RU' );
$_WEB = array( // перепаковка массива $_SERVER, т.к. в нем очень много элементов с префиском "PHP_"
	'SCHEME' => isset( $_SERVER['REQUEST_SCHEME'] )? $_SERVER['REQUEST_SCHEME'] : '', 
	'HOST' => isset( $_SERVER['HTTP_HOST'] ) ? $_SERVER['HTTP_HOST'] : '', 
	'PORT' => isset( $_SERVER['SERVER_PORT'] ) ? $_SERVER['SERVER_PORT'] : '', 
	'SCRIPT' => isset($_SERVER['SCRIPT_NAME'] ) ? $_SERVER['SCRIPT_NAME'] : '',
	'SERVER' => isset( $_SERVER['SERVER_SOFTWARE'] ) ? $_SERVER['SERVER_SOFTWARE'] : '', 
	'SERVER_IP' => isset( $_SERVER['SERVER_ADDR'] ) ? $_SERVER['SERVER_ADDR'] : '', 
	'IP' => isset($_SERVER['REMOTE_ADDR'] ) ? $_SERVER['REMOTE_ADDR'] : '', 
	'BROWSER' => isset( $_SERVER['HTTP_USER_AGENT'] ) ? $_SERVER['HTTP_USER_AGENT'] : '',
	'LANGUAGE' => isset( $_SERVER['HTTP_ACCEPT_LANGUAGE'] ) ? $_SERVER['HTTP_ACCEPT_LANGUAGE'] : '',
	'REFERER' => isset( $_SERVER['HTTP_REFERER'] ) ? $_SERVER['HTTP_REFERER'] : '',            
	'AUTH' => isset( $_SERVER['PHP_AUTH_DIGEST'] ) ? $_SERVER['PHP_AUTH_DIGEST'] : '', 
	'LOGIN' => isset( $_SERVER['PHP_AUTH_USER'] )? $_SERVER['PHP_AUTH_USER'] : '', 
	'PASSWORD' => isset( $_SERVER['PHP_AUTH_PW'] )? $_SERVER['PHP_AUTH_PW'] : '',
	'REQUEST_TIME' => isset( $_SERVER['REQUEST_TIME_FLOAT']) ? $_SERVER['REQUEST_TIME_FLOAT'] : '', 
);
if (!empty($_SERVER['SCRIPT_URL'])) $_WEB['URL'] = $_SERVER['SCRIPT_URL'];
elseif ( isset($_SERVER['REQUEST_URI'])) { $_WEB['URL'] = parse_url($_SERVER['REQUEST_URI']); $_WEB['URL'] = $_WEB['URL']['path']; }
else $_WEB['URL'] = '';
$_WEB += array( // теперь в русские названия, транслитом, кроме HOST, PORT, SERVER, SERVER_IP, IP, LOGIN
	'ADRES' => $_WEB['URL'],
	'SHEMA' => $_WEB['SCHEME'], 
	'SKRIPT' => $_WEB['SCRIPT'],
	'BRAUZER' => $_WEB['BROWSER'],
	'YAZIK' => $_WEB['LANGUAGE'],
	'REFER' => $_WEB['REFERER'],
	'AVTORIZACIYA' => $_WEB['AUTH'],
	'PAROL' => $_WEB['PASSWORD'],
	'VREMYA' => $_WEB['REQUEST_TIME'],
);

$is_session_start = false; // признак старта сессии для команд работы с сессиями

$def_key = array( // массив предопределенных функций, для переопределения функций и команды DECLARE
  'ASC' => 'com_ASC', 'KOD' => 'com_ASC', 
  'CHR' => 'com_CHR',
  'LEN' => 'mb_strlen',
  'MID' => 'com_MID',
  'LEFT' => 'com_LEFT',
  'RIGHT' => 'com_RIGHT',
  'TRIM' => 'com_TRIM',
  'LTRIM' => 'com_LTRIM',
  'RTRIM' => 'com_RTRIM',
  'UCASE' => 'mb_strtoupper',
  'LCASE' => 'mb_strtolower',
  'INSTR' => 'com_INSTR',
  'INSTRREV' => 'com_INSTRREV',
  'SPLIT' => 'com_SPLIT',
  'JOIN' => 'com_JOIN',
  'VAL' => 'com_VAL',
  'STR' => 'com_STR',
  'REPLACE' => 'com_REPLACE',
  'CRC32' => 'com_CRC32',
  'DECODE' => 'com_decode',
  'ENCODE' => 'com_ENCODE',
  'CONCAT' => 'com_CONCAT',
  'MERGE' => 'array_merge_recursive',
  'UNSHIFT' => 'array_unshift',
  'SHIFT' => 'array_shift',
  'POP' => 'array_pop',
  'PUSH' => 'array_push',
  'SPLICE' => 'array_splice',
  'SLICE' => 'array_slice',
  'DIFF' => 'array_diff',
  'INTERSECT' => 'array_intersect',
  'UNIQUE' => 'com_UNIQUE',
  'REVERSE' => 'array_reverse',
  'RANDOMISE' => 'com_RANDOMISE',
  'INDEXOF' => 'com_INDEXOF',
  'LASTINDEXOF' => 'com_LASTINDEXOF',
  'GETVALUE' => 'com_GETVALUE',
  'GETKEY' => 'com_GETKEY',
  'SETVALUE' => 'com_SETVALUE',
  'DIM' => 'com_DIM',
  'JSON' => 'com_JSON',
  'SORT' => 'com_SORT',
  'LBOUND' => 'com_LBOUND',
  'UBOUND' => 'com_UBOUND',
  'KEYS' => 'com_KEYS',
  'SIN' => 'com_SIN',
  'COS' => 'com_COS',
  'TAN' => 'com_TAN',
  'CTG' => 'com_CTG',
  'SEC' => 'com_SEC',
  'COSEC' => 'com_COSEC',
  'ASIN' => 'com_ASIN',
  'ACOS' => 'com_ACOS',
  'ATAN' => 'com_ATAN',
  'ACTG' => 'com_ACTG',
  'ASEC' => 'com_ASEC',
  'ACOSEC' => 'com_ACOSEC',
  'NOTATION' => 'com_NOTATION',
  'CEILING' => 'ceil',
  'MOD' => 'com_MOD',
  'DIV' => 'com_DIV',
  'RANDOMIZE' => 'mt_srand',
  'RND' => 'com_rand',
  'PLURAL' => 'com_PLURAL',
  'LOC' => 'frell',
  'SEEK' => 'fseek',
  'EOF' => 'feof',
  'LOCK' => 'com_LOCK',
  'UNLOCK' => 'com_UNLOCK',
  'GET' => 'com_GET',
  'PUT' => 'com_PUT',
  'READ' => 'com_READ',
  'WRITE' => 'com_WRITE',
  'FILEEXISTS' => 'com_FILEEXISTS',
  'FILEDATETIME' => 'com_FILEDATETIME',
  'FILELEN' => 'com_FILELEN',
  'FILEATTR' => 'com_FILEATTR',
  'KILL' => 'com_KILL',
  'NAME' => 'com_NAME',
  'FILECOPY' => 'com_FILECOPY',
  'RMDIR' => 'com_RMDIR',
  'MKDIR' => 'com_MKDIR',
  'DIRLEN' => 'com_DIRLEN',
  'DIRSPACE' => 'com_DIRSPACE',
  'DIR' => 'com_DIR',
  'TIMER' => 'com_TIMER',
  'NOW' => 'time',
  'DATE' => 'com_DATE',
  'DATEDIFF' => 'com_DATEDIFF',
  'QUERY' => 'com_QUERY',
  'QUOTE' => 'com_QUOTE',
  'LASTINSERID' => 'com_LASTINSERID',
  'COLOR' => 'com_COLOR',
  'POINT' => 'com_POINT',
);
//if( $_SERVER['REMOTE_ADDR'] == '87.251.187.34') {var_export(array_flip ($def_key));die();}
$script_filename = isset($_SERVER['REDIRECT_SCRIPT_URL']) ? $_SERVER['REDIRECT_SCRIPT_URL'] : '/index.bas' ; //скрипт по умолчанию
$include_path = __DIR__; // базовая директория, откуда ищутся все файлы
$cache_dir = sys_get_temp_dir().'/cache_'; // директория и префикс файлов для кеширования скомпилированных программ
$cache_cron_trash = false; // если в этой директории старые файлы автоматом удаляются, то поставте true, иначе уборкой займется wbasic
header('X-Powered-By: WBASIC/1.2'); // заменяем заголовок с php на wbasic
$debug_line = 0; // текущая строка
$debug_log = ''; // лог ошибок
$debug_mode = false; // режим отладки
$debug_client = ''; // для какого клиента показывать (IP или COOKIE)
$debug_bas = array(); // код на бейсике
$debug_php = array(); // код на php
$debug_time = array(); // таймлог
$debug_vars = array(); // переменки stop
$last_rnd = 0; // последнее случайное число
$db_connect = array(); // коннекторы баз данных
$db_use = false; // текущий коннектор
$gd_connect = array(); // коннекторы графических экранов
$gd_use = false; // текущий коннектор
$gd_color = 0; // текущий цвет
$gd_coord = array(0,0); // текущие координаты
$gd_font = NULL; // текущий шрифт
$gd_size = NULL; // текущий размер шрифта
$gd_angle = NULL; // текущий угол шрифта

register_shutdown_function('com_shutdown');
ob_start();
eval(com_compile($script_filename));

// окончание обработки
function com_shutdown() { 
// конец обработки файла или возникновение ошибок компиляции.
	global $debug_log, $debug_mode, $script_filename, $debug_line, $debug_php, $debug_bas, $debug_time, $debug_vars, $debug_client;
	// а произошла ли ошибка компиляции?
	$error =  error_get_last();
    if( ($error['type'] & 341) !== 0 ) $bg_color_error = 'debug_fatal'; else $bg_color_error = 'debug_warning';
	if( !is_null( $error ) ) {
		$debug_line = $error['line'] - 1;
		$types = array_flip( array_slice( get_defined_constants(true)['Core'], 1, 15, true ) );
		com_error( $types[$error['type']].' (OUTFILE)', $error['message'] ); 
	} else $bg_color_error = 'debug_noerror';
    // но мы все равно попытаемся вывести все что есть
	$out = ob_get_contents();
	ob_end_clean();
	// вначале высветим ошибки, если это разрешено
	if( $debug_mode ) { echo '
<!DOCTYPE html>
<html lang="ru-RU">
<head>
    <meta charset="UTF-8" />
    <title>Debug mode</title>
</head>
<style>
    body.debug_error {margin:0; padding:0;background: #555;}
    .debug_error {padding:5px 10px;margin:0 210px 5px 200px;font:12px monospace; height: 53px; overflow: auto;}
    .debug_title {position:absolute; padding:10px;}
    .debug_title b {font:bold 15px monospace;}
    .debug_noerror .debug_title {color:#7487af}
    .debug_fatal{background:#f44;color:#fff;}
    .debug_warning{background:#ff7;color:#000;}
    .debug_noerror{background:#213c72;color:#000;}
    .debug_file{display: block; width:40px; height:45px;right:10px; top:10px; position:absolute; text-decoration: none; color: #770; text-align: center; font:9px Consolas,monospace;}
    .debug_file i{display:block; border-radius: 0 50% 0 0;padding: 10px 0; font:15px Consolas,monospace;border: 1px solid #770;}
    .debug_fatal a.debug_file {color:#500;}
    .debug_fatal a.debug_file i{border-color:#000;color:#500;}
    .debug_noerror a.debug_file {color:#7487af;}
    .debug_noerror a.debug_file i{border-color:#7487af;color:#7487af;}
    a:hover.debug_file {color:#550;}
    a:hover.debug_file i{border-color:#ff0;color:#ff0;background-color:#000}
    .debug_fatal a:hover.debug_file {color:#500;}
    .debug_fatal a:hover.debug_file i{border-color:#000;color:#f00;background-color:#000}
    .debug_noerror a:hover.debug_file {color:#7487af;}
    .debug_noerror a:hover.debug_file i{border-color:#000;color:#7487af;background-color:#000}    
    body.debug_error div a:active.debug_file, body.debug_error div a:active.debug_file i{color:#fff;}
    a.debug_file_bas {right:60px}
    a.debug_file_time {right:110px}
    a.debug_file_vars {right:160px}
    .debug_code {padding:5px 15px;margin:0px;font:12px monospace;}
    .debug_code b {background: #ddd; padding: 5px 15px; border-radius: 0px 30px 0 0;}
    ul.debug_code{background: #ddd; padding-left: 60px; padding-right: 5px; border-radius: 0 15px 0 0;} 
    .debug_code li{background: #fff; padding-left: 5px;}
    .debug_code li:first-child{border-radius: 0 15px 0 0;}
    .debug_print{background: #ddd; padding: 10px; border-radius: 0 15px 0 0;}
    ul.debug_time {background:#0f0;padding-left:60px;} .debug_time li{background: #7f7; padding-left: 5px;}
    .debug_code a {position: relative;top: 0px;left: -10px;color:#000;background-color: #aaa;padding: 1px 5px;border-radius: 50%;text-decoration: none;}
    .debug_code a:hover {background-color: #000;color:#aaa}
    .debug_code_hide .debug_code, .debug_code_hide .debug_print{display:none}
    .debug_code_hide b {border-radius: 0px 30px 30px 0; display: inline-block;}
    .debug_timelog ul.debug_code{padding-left: 165px;}
    .debug_timelog ul.debug_code span{position:absolute;margin-left: -160px;}
    .debug_timer {border-radius: 0px 30px 30px 0; display: inline-block;}
    .debug_out {background:#fff;color:#000;margin:10px}
</style>
<script>
function debug_toggle(id){
    var e = document.querySelector(id);
    if(/debug_code_hide/.test(e.className)){ e.className = e.className.replace(/\sdebug_code_hide/,"") } else { e.className = e.className+" debug_code_hide"}
    return false;
}
</script>
<body class=debug_error>
    <div class=' . $bg_color_error . '>
        <div class=debug_title>
            <b>DEBUG MODE ON</b><br>SHOW FOR '.($debug_client == '' ? ' ALL ' : ( filter_var ($debug_client, FILTER_VALIDATE_IP) === false ? ' COOKIE: '.$debug_client : 'IP: '.$_SERVER['REMOTE_ADDR'])).'
            <br>GENERATE TIME: '.number_format ( microtime(true) - $_SERVER['REQUEST_TIME_FLOAT'] , 6 , '.' , ' ' ).' sec.
        </div>
        <a href="#debug-vars" class="debug_file debug_file_vars" onclick=\'debug_toggle("#debug_code_vars")\'><i>A=</i>STOPVARS</a>
        <a href="#debug-time" class="debug_file debug_file_time" onclick=\'debug_toggle("#debug_timelog")\'><i>0:0</i>TIMELOG</a>
        <a href="#" class="debug_file debug_file_bas" onclick=\'debug_toggle(".debug_code")\' ><i>BAS</i>CODEFILE</a>
        <a href="#debug-php" class="debug_file" onclick=\'debug_toggle(".debug_code_php")\' ><i>PHP</i>OUTFILE</a>
        <pre class=debug_error >' . ($debug_log == '' ? "\r\n\r\nNO ERRORS.\r\nEXCELLENT BASIC CODE!" : $debug_log) . '</pre>
    </div>';
    // код на бейсике
    foreach( $debug_bas as $script_file => $bas_code_file){
        echo '<div class="debug_code debug_code_bas debug_code_hide" id="debug_code_'.md5($script_file).'"><b><a href="#" onclick=\'return debug_toggle("#debug_code_'.md5($script_file).'")\' >—</a> ' . $script_file . '</b><ul class=debug_code>';
        foreach($bas_code_file as $bas_code_string) echo '<li type="1">' . htmlentities($bas_code_string, ENT_QUOTES) . '</li>';
        echo '</ul></div>';
    }        
    // все что откомпилировалось, для "суровых мужиков"
    foreach( $debug_php as $script_file => $php_code){
        echo '<a name="debug-php"></a><div class="debug_code debug_code_php debug_code_hide" id="debug_code_php_'.md5($script_file).'"><b><a href="#" onclick=\'return debug_toggle("#debug_code_php_'.md5($script_file).'")\'>—</a> OUTFILE ' . $script_file . '</b><ul class=debug_code>';
        foreach( explode(PHP_EOL, $php_code) as $php_code_file){
            $php_code_file = explode('/*debug_timer*/', $php_code_file);
            echo '<li type="1">';
            $php_code_file_even = true;
            foreach($php_code_file as $php_code_string) {
                if($php_code_file_even) echo htmlentities($php_code_string, ENT_QUOTES);
                $php_code_file_even = !$php_code_file_even;
            }
            echo '</li>'.PHP_EOL;
        }    
        echo '</ul></div>';
    }
    // таймлог для оптимизаторов
    echo '<a name="debug-time"></a><div class="debug_code debug_timelog debug_code_hide" id="debug_timelog"><b><a href="#" onclick=\'return debug_toggle("#debug_timelog")\' >—</a> TIMELOG</b><ul class=debug_code>';
    $first_time = 0;
    foreach( $debug_time as $bas_code_file){
        // выделим время исполнения кода 
        $bas_code_file = explode (':', $bas_code_file); 
        $time = array_shift($bas_code_file); 
        if($first_time == 0) $first_time = $time; // для сброса времени в ноль на первой строке
        $time = number_format($time - $first_time, 6); 
        $bas_code_file = implode(':', $bas_code_file);
        echo '<li type="1"><span class=debug_timer>'.$time.' sec.</span>'.$bas_code_file.'</li>';
    }  
    echo '</ul></div>';
    // все переменки переданные в STOP
    echo '<a name="debug-vars"></a><div class="debug_code debug_timelog debug_code_hide" id="debug_code_vars"><b><a href="#" onclick=\'return debug_toggle("#debug_code_vars")\'>—</a> STOPVARS</b><ul class=debug_code>';

    foreach( $debug_vars as $debug_var_name => $debug_var){
        echo '<li style="list-style-type:none"><span class=debug_timer title = "' . htmlentities($debug_var_name, ENT_QUOTES) . '">'.(strlen($debug_var_name) > 20 ? substr($debug_var_name, 0, 10) . '..' . substr($debug_var_name, -10) : $debug_var_name) .'</span>';
        echo json_encode($debug_var, JSON_NUMERIC_CHECK | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        echo '</li>'.PHP_EOL;
    }    
    echo '</ul></div>';

	// затем все что выводилось в браузер
	if($out !== '') echo '<a name="debug-print"></a><div class="debug_code" id="debug_print"><b><a href="#" onclick=\'return debug_toggle("#debug_print")\'>—</a> PRINT</b><div class=debug_print>'.htmlentities($out, ENT_QUOTES).'</div></div>';
    // затем все остальное
    echo '
</body>
</html>
';
    } else echo $out;
}

// ведем лог ошибок - его внешний вид задается здесь
function com_error ($type, $message){
	global $debug_log, $script_filename, $debug_line ;
	$debug_log .= 'ERROR: ' . $type . PHP_EOL . "MESSAGE: " .$message . PHP_EOL . "FILE: " . $script_filename . PHP_EOL . "LINE: " . ( $debug_line + 1 ) . PHP_EOL;
}

// компилятор скрипта 
function com_compile ($file) {
	global $debug_mode, $include_path, $script_file, $debug_line, $def_key, $debug_bas, $debug_php, $debug_client, $cache_dir, $cache_cron_trash;
    $my_script_file = $script_file; $my_script_line = $debug_line; // сохраним указатели компилятора
    $php_code_file = ''; // поскольку это компилятор, здесь будет накапливаться выходной код на php
	// читаем файл, удаляя переводы строк. пустые строки не игнорируем, чтобы правильно указывать номер строки для ошибок синтаксиса
    $script_file = realpath( $include_path . DIRECTORY_SEPARATOR . ltrim( $file, '\\/' ));
    if ( $script_file === false || substr( $script_file, 0, strlen($include_path) ) !== $include_path) { // ошибка, файл не найден
        $script_file = $my_script_file;
        com_error( 'FILEOPEN', 'No script file' );
        return '';
    }
    $cache_script_file = stat($script_file);
    $cache_script_file = $cache_dir.abs(crc32($script_file)).'_'.date('dMHis', $cache_script_file[9]).'.wbc';
    if(file_exists($cache_script_file) && !$debug_mode) { // найден откомпилированый файл в кеше и не DEBUG режим
        $script_file = $my_script_file;
        $php_code_file = file_get_contents($cache_script_file);
        return $php_code_file; // вернем данные из него
    } // иначе нам придется компилировать файл
    if(!$cache_cron_trash){ // сборка мусора, т.к. все равно тормозим, а cron не убирает старые файла
        foreach (glob($cache_dir.'*.wbc') as $filename) { //сборка мусора нужна, т.к. удаленные файлы застревают в кэше надолго
            $filename_stat = stat($filename);
            if( $filename_stat[9] < strtotime('-1 month')) unlink($filename);
        }
    }
    foreach (glob($cache_dir.abs(crc32($script_file)).'*.wbc') as $filename) unlink($filename); // удалим предыдущую запись этого файла в кэше 

	$bas_code_file = file( $script_file );
	$debug_bas[str_replace($include_path, '', $script_file)] = $bas_code_file;
    $long_buffer = ''; // буфер переноса строки.
    $long_string_begin = false; $long_string_continue = false; // признак переноса длинной строки.
	// обход строк, если файл не пустой. поскольку счет строк встроен в цикл, удаляем слева и справа пробелы, табуляцию, переводы строк и пропускаем пустые строки
	if( count( $bas_code_file ) > 0 ) foreach( $bas_code_file as $debug_line => $bas_code_string ) if( ($bas_code_string = trim($bas_code_string)) != '' ){
        if($long_string_begin) { // строка только начата, а уже есть признак длинной строки
            $long_string_continue = true; $long_string_begin = false;
        } elseif(substr($bas_code_string, -1) == '"' && $bas_code_string !== '"'){ // возможно, это признак длинной строки
            if(((substr_count ($bas_code_string, '"') - substr_count ($bas_code_string, '\"') /*+ substr_count ($bas_code_string, '\\\"')*/) % 2) !== 0){
               $long_string_begin = true; // получилось нечетное значение кавычек в строке с учетом экранирования - да, это длинная строка
            }
        }
        if($long_string_continue){
            if( $bas_code_string == '"' ) { // длинная строка завершена
                $long_string_continue = false;
                $php_code_file .= 'EOT;' . PHP_EOL ;
            } else $php_code_file .= $bas_code_file[$debug_line];
            continue;
        }    
        if( strpbrk( substr($bas_code_string, -1), ',{(') !== false ) {  // , и ( и { в конце строки переносят вычисления на следующую строку.
            $long_buffer .= $bas_code_string;
            continue;
        } elseif( $long_buffer !== '' ) { // нет символа переноса строки в конце, но буфер не пустой
            $bas_code_string = $long_buffer . $bas_code_string;
            $long_buffer = '';
        }
		$php_code_string = ''; // здесь будет накапливаться строка кода на php
		$label_code_string = ''; // здесь будет накапливаться строка кода на php
		// проход первый - метки.
		// выделяем метку строки из начала строки кода.
		// метка - целое число больше 0 или строка {A-ZА-Я_} заканчивающаяся на {:}
		if( preg_match('/^([0-9]+(?=\s+)|[a-z' . CYRILLIC . '0-9_]+(?=:))/ui', $bas_code_string, $array_math ) ){
			$bas_code_string = trim( substr ( $bas_code_string, strlen( $array_math[1] ) + 1 ) ); // перевели курсор за метку
			$label_code_string = (is_numeric($array_math[1]) ? '_'.$array_math[1] : com_transliterate( $array_math[1] )) . ': ' ; // и преобразовали метку в латинцу и вставили в выходной код
		}
		
		// проход второй - лексика.
		// Лексический анализатор - разбиваем строку на атомарный массив констант, переменных и операторов.
		$multiple_commands = array(array()); // в одной строке может быть несколько комманд, разделенных {;} вне кавычек, здесь будут отрезки кода
		$multiple_commands_count = 0;
		$open_parenthesis = 0; $close_parenthesis = 0; // для подсчета ()[] в строке
		// делим строку на отрезки текста в кавычках и без, т.к. текст внутри кавычек не интерпретируется
		preg_match_all('/"((?<=\\\)"|[^"])*"|([^"]+)/u', $bas_code_string, $array_math, PREG_SET_ORDER);
		if( count( $array_math ) > 0 ) foreach( $array_math as $array_math_strings ) {
			if( count($array_math_strings) > 2) { // найден отрезок вне кавычек, его надо разобрать
				$rem_half = explode( "'", $array_math_strings[0] ); // разделим строку на до и после коментария
				foreach ( explode( ';', $rem_half[0] ) as $multiple_level => $multiple_command) { 
					if( $multiple_level > 0) {
						$multiple_commands_count++; // есть еще уровень (новая команда в строке), добавим счетчик команд в строке
						//проверим сложились ли скобки до начала новой команды
						if($open_parenthesis !== $close_parenthesis) com_error( 'WHAT?', 'how many "(" <-' . $open_parenthesis . ' ? ' . $close_parenthesis . '-> ")"' ); 
						$open_parenthesis = 0; $close_parenthesis = 0; // сбросим расчет скобок, т.к. пошла новая строка
					}
					if( trim($multiple_command) != '' ) {
						// делим строку на атомы
						preg_match_all('/[0-9a-z' . CYRILLIC . '_\.]+((\$|%|#|&|@)|(?=\W*))|\W/ui', $multiple_command, $multiple_command_math, PREG_SET_ORDER );
						if( count( $multiple_command_math ) > 0 ) foreach($multiple_command_math as $array_math_strings) {
							// теперь рассортируем атомы на VAR$ VAR# VAR% VAR& VAR@ CONST$ CONST# CHAR
							if( count($array_math_strings) == 3) $multiple_commands[ $multiple_commands_count ][] = array ( 'VAR'.$array_math_strings[1] => $array_math_strings[0] );
							elseif( count($array_math_strings) == 2 ) $multiple_commands[ $multiple_commands_count ][] = array ( 'CONST'.( is_numeric( $array_math_strings[0] ) ? '#' : '$' ) => $array_math_strings[0] );
							elseif( count($array_math_strings) == 1 && trim($array_math_strings[0]) !== '') {
								$multiple_commands[ $multiple_commands_count ][] = array ( 'CHAR' => $array_math_strings[0] );
								if( strpos('([', $array_math_strings[0] ) !== false ) $open_parenthesis++; //считаем скобки
								if( strpos(')]', $array_math_strings[0] ) !== false ) $close_parenthesis++;
							}	
						}
						
					}
				}
				if( count( $rem_half ) > 1 ) break; // есть коментарий? досрочно закончим цикл прохода
			} else { // или отрезок внутри кавычек. это также атом STRING
				$multiple_commands[ $multiple_commands_count ][] = array ( 'STRING' => $array_math_strings[0] );
			}
		}
		if($open_parenthesis !== $close_parenthesis) com_error( 'WHAT?', 'how many "(" <-' . $open_parenthesis . ' ? ' . $close_parenthesis . '-> ")"' ); 
		
		//var_export($multiple_commands);
		// проход третий - парсер.
		// парсер атомарного массива разбирает входящий поток и формирует выходной код.
		foreach( $multiple_commands as $array_atoms ) {
            $tree_command = array( 0 => array ( // Ветви комманд для if then .. else .., for
                'php_code_string' => '', // массив выходного кода - каждому типу команд своя ветка строк
                'command' => '', // команда - определяется по первому атому
                'var_eq' => '' // переменная до знака равенства или имя функции
            ));        
            $tree_counter = 0; // Счетчик ветвей
            $double_eq = 0;
            $if_atom_value = ''; $if_atom_num = 0; $if_code_string = ''; 
            $else_atom_value = ''; $else_atom_num = 0; $else_code_string = '';
            $open_is = false; $for_num = 0; $do_while = true; $my_script_file = ''; 
			if( count($array_atoms) > 0 ) foreach( $array_atoms as $atom_num => $array_atom) { // $atom_num - порядковый номер
				foreach( $array_atom as $atom_type => $atom_value) break; // $atom_type - тип и $atom_value значение атома
                $atom_value_upper = strtoupper($atom_value);
                // строчная форма команды IF
                if($atom_type == 'CONST$' && $atom_value_upper == 'THEN') {
                    $if_atom_num = $atom_num + 1;
                    $if_atom_value = $atom_value;
                    $if_code_string = $tree_command[$tree_counter]['php_code_string'];
                    $tree_command[$tree_counter]['php_code_string'] = '';
                    $double_eq = 0;
                    continue;
                }
                if($atom_type == 'CONST$' && $atom_value_upper == 'ELSE') {
                    $else_atom_num = $atom_num + 1;
                    $else_atom_value = $atom_value;
                    $else_code_string = $tree_command[$tree_counter]['php_code_string'];
                    $tree_command[] = array ( 'php_code_string' => '', 'command' => '', 'var_eq' => '' );
                    $tree_counter++;
                    $double_eq = 0;
                    continue;
                }
                // OPEN файлы
                if($atom_type == 'CONST$' && $atom_value_upper == 'OPEN') $open_is = true;
                // циклы
                if($atom_type == 'CONST$' && ( $atom_value_upper == 'FOR' || $atom_value_upper == 'FOREACH')) {
                    if($open_is) { // в конструкции OPEN тоже есть FOR
                        $tree_command[] = array ( 'php_code_string' => '', 'command' => strtoupper($atom_value), 'var_eq' => '' );
                        $tree_counter++;
                    } else $for_num = $atom_num + 1;
                    continue;
                }    
                // проверка типов
                if($atom_type == 'CONST$' && in_array($atom_value_upper, array( 'TYPEOF', 'IS', 'ISNOT', 'ISSET', 'EMPTY'))) continue;
                if($atom_type == 'CONST$' && isset($array_atoms[$atom_num-1]) && isset($array_atoms[$atom_num-1]['CONST$'])   
                    && in_array(strtoupper($array_atoms[$atom_num-1]['CONST$']), array('IS', 'ISNOT')) ) continue; 
                // разбивка команды на части
                if($atom_type == 'CONST$' && $tree_command[$tree_counter]['command'] != 'PSET' & in_array($atom_value_upper, array('TO', 'STEP', 'IN', 'AS' ))) {
                    $tree_command[] = array ( 'php_code_string' => '', 'command' => strtoupper($atom_value), 'var_eq' => '' );
                    $tree_counter++;
                    continue;
                }
                if($atom_type == 'CONST$' && in_array($atom_value_upper, array('WHILE', 'UNTIL' ))) {
                    if($atom_value_upper == 'UNTIL' ) $do_while = false;
                    continue;
                }
                $atom_num = $atom_num - ($else_atom_num > 0 ? $else_atom_num : $if_atom_num ) - $for_num; // порядковый номер в новой ветке
                $atom_num_norm = $atom_num + ($else_atom_num > 0 ? $else_atom_num : $if_atom_num ) + $for_num; // порядковый номер в общем списке
				if( $atom_num == 0 && ($atom_type == 'CONST$' || $atom_type == 'CHAR')) { // выделим и определим команду
					$atom_value = strtoupper($atom_value);
					switch($atom_value) {
						case 'REM'; //коментарии
						case '?'; case 'PRINT'; case 'INPUT'; case 'OUTPUT'; //вывод и ввод данных
						case 'LET'; case 'DECLARE'; case 'ERASE'; case 'DIM'; //управление переменными
						case 'GOTO'; case 'GOSUB'; case 'SUB'; case 'RETURN'; case 'END'; //переходы и функции
						case 'IF'; case 'ELSE'; case 'ELSEIF'; //условие
						case 'NEXT'; case 'EXIT'; case 'CONTINUE'; //циклы
                        case 'DO';	case 'LOOP'; //циклы
                        case 'SCREEN'; case 'COLOR'; case 'LINE'; case 'PSET'; case 'CIRCLE'; case 'PAINT'; //графика
                        case 'SLEEP'; //время
                        case 'OPEN'; case 'CLOSE';  case 'READ'; case 'WRITE';  case 'GET'; case 'PUT'; case 'LOCK'; case 'UNLOCK'; //файлы
                        case 'CONNECT'; case 'DISCONNECT'; case 'USE'; case 'TRANSACTION'; case 'COMMIT'; case 'ROLLBACK';//базы
						case 'INCLUDE';	case 'DEBUG'; case 'STOP'; //команды компилятору
							$tree_command[$tree_counter]['command'] = $atom_value;
						break;
					default;
						 $tree_command[$tree_counter]['command'] = '=VAR:'; // видимо присвоение переменной
					break;
					}
				} elseif($atom_num == 0 && substr($atom_type, 0 , 3 ) == 'VAR') $tree_command[$tree_counter]['command'] = '=' . $atom_type; // видимо присвоение переменной с типом
				if( $atom_type == 'CHAR' && $atom_num > 0 ) {
                        $logic_eq = false;
                        if($atom_value == '=') {
                            if(isset($array_atoms[$atom_num_norm+1]) && isset($array_atoms[$atom_num_norm+1]['CHAR']) && strpos('<>=', $array_atoms[$atom_num_norm+1]['CHAR']) !== false) $logic_eq = true;
                            if(isset($array_atoms[$atom_num_norm-1]) && isset($array_atoms[$atom_num_norm-1]['CHAR']) && strpos('<>', $array_atoms[$atom_num_norm-1]['CHAR']) !== false) $logic_eq = true;
                            $double_eq++;
                            if(in_array($tree_command[$tree_counter]['command'], array('IF', 'DO', 'LOOP'))) $double_eq++;
                            if(substr($tree_command[$tree_counter]['command'], 0 , 1 ) != '=' || (isset($array_atoms[1]['CHAR']) && $array_atoms[1]['CHAR'] == '(')) $double_eq++;
                            if(in_array($tree_command[$tree_counter]['command'], array('SUB', 'LET'))) $double_eq = 0;
                        }
                        if($atom_value == '{') $tree_command[$tree_counter]['php_code_string'] .= ' array( ';
                        elseif($atom_value == '}') $tree_command[$tree_counter]['php_code_string'] .= ' ) ';
                        elseif($atom_value == ':') $tree_command[$tree_counter]['php_code_string'] .= ' => ';
                        elseif($atom_value == '^') $tree_command[$tree_counter]['php_code_string'] .= ' ** ';
                        elseif($atom_value == '\\') $tree_command[$tree_counter]['php_code_string'] .= ' %';
                        elseif($atom_value == '=' && $double_eq > 1 && !$logic_eq) $tree_command[$tree_counter]['php_code_string'] .= '==';                        
						else $tree_command[$tree_counter]['php_code_string'] .=  $atom_value == '&' ? '.' : $atom_value; // складываем строки как VB через &
				}
				if( $atom_type == 'CONST#') {
						$tree_command[$tree_counter]['php_code_string'] .=  $atom_value;
				}
				if( $atom_type == 'CONST$') {
                        
                        if(isset($array_atoms[$atom_num_norm+1]) && isset($array_atoms[$atom_num_norm+1]['CHAR']) && $array_atoms[$atom_num_norm+1]['CHAR'] == '(') $open_bracket = true; else $open_bracket = false; // дальше будет открытая скобка?
                        if(isset($array_atoms[$atom_num_norm+2]) && isset($array_atoms[$atom_num_norm+1]['CONST$']) && isset($array_atoms[$atom_num_norm+2]['CONST$'])  
                            && in_array(strtoupper($array_atoms[$atom_num_norm+1]['CONST$']), array('IS', 'ISNOT')) // это оператор проверки типа?
                            && in_array(strtoupper($array_atoms[$atom_num_norm+2]['CONST$']), array('BOOLEAN', 'INTEGER', 'DOUBLE', 'STRING', 'ARRAY', 'NULL', 'NOTHING' )) 
                        ) $is = (strtoupper($array_atoms[$atom_num_norm+1]['CONST$'])=='IS' ? (strtoupper($array_atoms[$atom_num_norm+2]['CONST$']) == 'NOTHING' ? '!is' : 'is') : (strtoupper($array_atoms[$atom_num_norm+2]['CONST$']) == 'NOTHING' ? 'is' : '!is'))
                            . str_replace( array('BOOLEAN', 'INTEGER', 'DOUBLE', 'STRING', 'ARRAY', 'NULL', 'NOTHING' ), array('_bool', '_int', '_float', '_string', '_array', '_null', 'set' ), strtoupper($array_atoms[$atom_num_norm+2]['CONST$'])); 
                        elseif(isset($array_atoms[$atom_num_norm+1]) && isset($array_atoms[$atom_num_norm+1]['CONST$']) 
                            && in_array(strtoupper($array_atoms[$atom_num_norm+1]['CONST$']), array('ISSET', 'EMPTY')) // это оператор проверки существования переменки или данных?
                        ) $is = strtolower($array_atoms[$atom_num_norm+1]['CONST$']); 
                        elseif(isset($array_atoms[$atom_num_norm-1]) && isset($array_atoms[$atom_num_norm-1]['CONST$']) 
                            && in_array(strtoupper($array_atoms[$atom_num_norm-1]['CONST$']), array('ISSET', 'EMPTY')) // это оператор проверки существования переменки или данных?
                        ) $is = strtolower($array_atoms[$atom_num_norm-1]['CONST$']); 
                        else $is = '';
						if( ( substr($tree_command[$tree_counter]['command'], 0 , 1 ) == '=' || $tree_command[$tree_counter]['command'] == 'SUB' ) && $atom_num < 2 ){
                            $tree_command[$tree_counter]['var_eq'] = com_transliterate( $atom_value );
                            if( !isset($def_key[$tree_command[$tree_counter]['var_eq']]) && !$open_bracket) $tree_command[$tree_counter]['var_eq'] = '$'. $tree_command[$tree_counter]['var_eq'];
                            else { // это функция и она есть в массиве ключевых слов
                                if( array_key_exists ($tree_command[$tree_counter]['var_eq'], $def_key) ) $tree_command[$tree_counter]['var_eq'] = $def_key[$tree_command[$tree_counter]['var_eq']]; // если ключ не просто индекс, то там эквивалент функции из DECLARE, перешьем имя
                            }
						}
						if($atom_num > 0) {
							$var = com_transliterate( $atom_value );
							if($tree_command[$tree_counter]['command'] == 'SUB' && $atom_num == 1){
								if( array_key_exists($var, $def_key) ) com_error( 'WHAT?', 'Redefine ' . $tree_command[$tree_counter]['command'] .' '. $var ); 
								else {
									$tree_command[$tree_counter]['php_code_string'] .= $var;
									$def_key[$var] = $var;
								}
							} else {
								if( (array_key_exists($var, $def_key) || $open_bracket) && !in_array($var, array('NOT', 'ARRAY'))) { // это функция и она есть в массиве ключевых слов
                                    if(array_key_exists($var, $def_key)) $tree_command[$tree_counter]['php_code_string'] .= $def_key[$var]; // если ключ не просто индекс, то там эквивалент функции из DECLARE, перешьем имя
                                    else $tree_command[$tree_counter]['php_code_string'] .= $var;  
                                } else $tree_command[$tree_counter]['php_code_string'] .= 
                                ( !in_array($var, array('DEG', 'PI', 'NONE', 'NOTHING', 'ANY', 'TRUE', 'FALSE', 'NULL', 'AND', 'OR', 'NOT', 'XOR', 'ARRAY', 'IIF')) && $is == '' ? '$' : '') . // это операторы а не переменые
                                ( $var == 'DEG' ? ' FALSE ' : ( $var == 'PI' ? M_PI : ( $var == 'NONE' ? ' -1 ' : ( $var == 'NOTHING' ? ' NULL ' : ( $var == 'ANY' ? ' TRUE ' : ( $var == 'NOT' ? ' ! ' : ( $var == 'ARRAY'  ? ' com_change_key' : ( in_array($var, array('TRUE', 'FALSE', 'NULL', 'AND', 'OR', 'XOR')) ? ' ' . $var . ' ' : ($is == '' ? $var : $is .'($' . $var . ')' ) ) ) ) ) ) ) ) ); // и они не должны слипаться с цифрами
							}
						}
				}
				if( substr($atom_type, 0, 3) == 'VAR') {
					if( substr($tree_command[$tree_counter]['command'], 0 , 1 ) == '=' && $atom_num < 2 )
						$tree_command[$tree_counter]['var_eq'] = '$' . com_transliterate(substr( $atom_value, 0, strlen($atom_value) - 1));
					else 
						$tree_command[$tree_counter]['php_code_string'] .= str_replace( array( '$', '#', '%', '&', '@'), array('(string)', '(int)', '(float)', '(boolean)', '(array)'), substr( $atom_value, -1) ) // преобразователи типа
						. '$' . com_transliterate(substr( $atom_value, 0, strlen($atom_value) - 1)); // + знак $ + имя переменной
				}
				if( $atom_type == 'STRING') {
						$tree_command[$tree_counter]['php_code_string'] .= $atom_value;
				}	
			}
            // отработка команд
            // односекционные команды
            //var_export($tree_command);
            foreach($tree_command as $three_key => $three_item) {
                $php_code_string = &$tree_command[$three_key]['php_code_string'];
                $command = &$tree_command[$three_key]['command'];
                $var_eq = &$tree_command[$three_key]['var_eq'];
				if (substr($command, 0 , 1 ) == '=' ) {
						$php_code_string =  $var_eq  
                            . ( substr($php_code_string, 0, 1) != '=' ? '' : '=' ) 
							. ( str_replace( array( '$', '#', '%', '&', '@', ':' ), array('(string)(', '(int)(', '(float)(', '(boolean)(', '(array)(', ''), substr( $command, -1) ) ) 
                            . ( substr($php_code_string, 0, 1) != '=' ? $php_code_string : substr($php_code_string, 1) )
                            . (substr( $command, -1) == ':' ? '' : ')');
				} else switch($command) {
					case 'REM':
						$php_code_string = ''; 
						break;
					case 'PRINT'; case '?':
						if(substr($php_code_string,0,3) == 'AT(' ){
                        $php_code_string = substr($php_code_string, 2);
                        preg_match_all('/(?:[^,(]|\([^)]*\))+/', $php_code_string, $php_code_string_math);
                        preg_match('/\((.+)\)/', $php_code_string_math[0][0], $coord);
                        if( isset($php_code_string_math[0][2]) && substr($php_code_string_math[0][2],0,2) == '#$' ) $php_code_string_math[0][2] = '\'' . substr($php_code_string_math[0][2], 2) . '\'';
                        $php_code_string = 'com_text(' . $coord[1] . ',' . $php_code_string_math[0][1] . ',' . (!isset($php_code_string_math[0][2]) ? 'NULL' : $php_code_string_math[0][2] ) . ',' . (!isset($php_code_string_math[0][3]) ? 'NULL' : $php_code_string_math[0][3] ) . ',' . (!isset($php_code_string_math[0][4]) ? 'NULL' : $php_code_string_math[0][4] ). ',' . (!isset($php_code_string_math[0][5]) ? 'NULL' : $php_code_string_math[0][5] ) . ')'; 
						} else $php_code_string = 'echo ' . $php_code_string ;
						break;
					case 'INPUT':
						$vars = explode (',', $php_code_string);
						if( strpos( $vars[0] , '"') !== false ) $var_source = array_shift ($vars);
						else $var_source = '"GET"';
						$php_code_string = 'list(' . str_replace( array('(string)', '(int)', '(float)', '(boolean)', '(array)'), '', implode(',', $vars)) . ')=com_input(' . $var_source . ",array('" . implode("','", $vars) . "'))";
						break;
					case 'OUTPUT':
						$vars = explode (',', $php_code_string);
						if( strpos( $vars[0] , '"') !== false ) $var_source = array_shift ($vars);
						else $var_source = '"SESSION"';
						$php_code_string = 'com_output(' . $var_source . ($var_source != 'SESSION' ? ",array('" . implode("','", $vars) . "')," : 'array(),' ) .'array('. implode(',', $vars) . "))";
						break;
					case 'LET':
						if ( strpos( $php_code_string , '=') !== false ) $php_code_string = 'static ' . $var_eq  .  $php_code_string;
						else $php_code_string = 'global $' .  $php_code_string;
						break;

					case 'SUB':
						$php_code_string = 'function ' . ( strpos( $php_code_string , '(' ) === false ? $php_code_string . '()' : $php_code_string ). '{' . $var_eq . '=func_get_args()' .
						( $debug_mode ? '/*debug_timer*/; $GLOBALS[\'debug_time\'][] = (microtime(true)-$_SERVER[\'REQUEST_TIME_FLOAT\']).":'. htmlentities($bas_code_string, ENT_QUOTES) . '";/*debug_timer*/ ' : '') ;
						break;
					case 'END': // универсальный конец
						if($php_code_string != '' ) $php_code_string = 'return '. $php_code_string .'; }'; // специальный конец для SUB
						else $php_code_string = '}' ;
						break;
					case 'GOSUB': // изменяет строку кода так, чтобы можно было не декларировать подпрограммы заранее
						$php_code_string = substr( $php_code_string, 1 ) . ( strpos( $php_code_string , '(' ) === false ? '()' : '');
						break;
					case 'RETURN':
						$php_code_string = 'return ' . $php_code_string;
						break;
					case 'DEBUG':
						$vars = explode (',', $php_code_string);
						com_debug( $vars );
						$php_code_string = '';
						break;
					case 'STOP':
						$vars = explode (',', $php_code_string);
						$php_code_string = 'com_stop(array(' . implode(",", $vars) . "),array('" . implode("','", $vars) . "'))";
						break;
					case 'ERASE':
						$php_code_string = 'unset(' . $php_code_string. ')';
						break;
					case 'INCLUDE':
						$php_code_string = 'eval(com_compile(' . trim( implode('). com_compile(', explode (',', $php_code_string)), ').' ) .'))';
						break;
					case 'IF':
						$php_code_string = 'if(' . $php_code_string . '){'; // многострочная
						break;
					case 'ELSEIF':
						$php_code_string = '} elseif(' . $php_code_string.'){';
						break;
					case 'NEXT':
						$php_code_string = 'endfor';
						break;
					case 'EXIT':
						$php_code_string = 'break '.$php_code_string;
						break;
					case 'CONTINUE':
						$php_code_string = 'continue '.$php_code_string;
						break;
					case 'DO':
						if($php_code_string == '') $php_code_string = 'do { ';
						else $php_code_string = 'while '.($do_while ? '('. $php_code_string .')' : '(!('. $php_code_string .'))').'{';
						break;
					case 'LOOP':
						if($php_code_string == '') $php_code_string = '}';
						else $php_code_string = '} while '.($do_while ? '('. $php_code_string .')' : '(!('. $php_code_string .'))');
						break;
					case 'SLEEP':
						$php_code_string = 'usleep(' . $php_code_string .' * 1000000)';
						break;
					case 'CLOSE':
						$php_code_string = 'fclose(' . ltrim($php_code_string, '#') .')';
					case 'LOCK':
						$vars = explode(',', $php_code_string);
						$file = ltrim( array_shift($vars), '#');
						$mode = array_shift($vars);
						$mode = strpos( $mode, 'WRITE' ) !== false ? 'TRUE' : (strpos( $mode, 'READ' ) !== false ? 'FALSE' : $mode);
						$php_code_string = 'com_LOCK(' . $file . ',' . $mode .')';
						break;
					case 'UNLOCK':
						$php_code_string = 'com_UNLOCK(' . ltrim($php_code_string, '#') .')';
						break;
					case 'READ':
						$vars = explode(',', $php_code_string);
						$file = ltrim( array_shift($vars), '#');
						$value = array_shift($vars);
						$length = array_shift($vars);
						$php_code_string = $value . '=com_READ(' . $file . ($length == '' ? '' : ',' .$length) . ')';   
						break;
					case 'WRITE':
						$php_code_string = 'com_WRITE(' . ltrim($php_code_string, '#') .')';
						break;
					case 'GET':
						$vars = explode(',', $php_code_string);
						$file = ltrim( array_shift($vars), '#');
						$value = array_shift($vars);                     
						$length = array_shift($vars);
						$php_code_string = $value . '=com_GET(' . $file . ($length == '' ? '' : ','.$length) . ')';   
						break;
					case 'PUT':
						$php_code_string = 'com_PUT(' . ltrim($php_code_string, '#') .')';
						break;
					case 'CONNECT':
						$php_code_string = ltrim( $php_code_string, '$');
						$php_code_string = preg_replace('/^(CUBRID|DBLIB|FIREBIRD|IBM|INFORMIX|MYSQL|ORACLE|ODBC|PGSQL|SQLITE|MSSQL|4D)/ui','"$1", $2',$php_code_string);
						$php_code_string = 'com_connect(' . (strtoupper($php_code_string)=='USE' || $php_code_string=='' ? '' : $php_code_string) .')'; 
						break;
					case 'DISCONNECT':
						$php_code_string = 'com_disconnect(' . $php_code_string .')';
						break;
					case 'USE':
						$php_code_string = 'com_use(' . ltrim( $php_code_string, '#').')';
						break;
					case 'TRANSACTION':
						$php_code_string = '$GLOBALS[\'db_use\'][\'connect\']->beginTransaction()';
						break;
					case 'COMMIT':
						$php_code_string = '$GLOBALS[\'db_use\'][\'connect\']->commit()';
						break;
					case 'ROLLBACK':
						$php_code_string = '$GLOBALS[\'db_use\'][\'connect\']->rollBack()';
						break;
					case 'COLOR':
						if( substr($php_code_string,0,2) == '#$' ) $php_code_string = '\'' . substr($php_code_string, 2) . '\'';
						$php_code_string = 'com_COLOR(' . $php_code_string .')';
						break;
					case 'LINE':
						preg_match_all('/\(((?:(?>[^()]+)|(?R))*)\)/', $php_code_string, $php_code_string_math,  PREG_SET_ORDER, 0);
						$coord = array();
						foreach($php_code_string_math as $math) {
							$php_code_string = str_replace($math[0], '', $php_code_string);
							$coord[] = $math[1];
						}
						$php_code_string = ltrim($php_code_string, '- ');
						$mode = 0;
						if( strtoupper(substr($php_code_string,-4)) == ',$BF' ) {
							$mode = 2;
							$php_code_string = str_replace(',$BF', '', $php_code_string);
						}elseif( strtoupper(substr($php_code_string,-3)) == ',$B' ){
							$mode = 1;
							$php_code_string = str_replace(',$B', '', $php_code_string);
						}
						if( substr($php_code_string,0,3) == ',#$' ) $php_code_string = ',\'' . substr($php_code_string, 3) . '\'';
						$php_code_string = 'com_line(array(' . implode( ',', $coord ) . ')' . ($php_code_string == '' ? 'NULL' : $php_code_string ) . ',' . $mode . ')'; 
						break;
					case 'PSET':
						preg_match('/\(((?:(?>[^()]+)|(?R))*)\)/', $php_code_string, $coord);
						$php_code_string = str_replace($coord[0], '', $php_code_string);
						$coord = $coord[1];
						if( substr($php_code_string,0,3) == ',#$' ) $php_code_string = ',\'' . substr($php_code_string, 3) . '\'';
						$php_code_string = 'com_pset(' . $coord . ($php_code_string == '' ? ',NULL' : $php_code_string ) . ')'; 
						break;
					case 'PAINT':
						preg_match('/\(((?:(?>[^()]+)|(?R))*)\)/', $php_code_string, $coord);
						$php_code_string = str_replace($coord[0], '', $php_code_string);
						$coord = $coord[1];
						if( substr($php_code_string,0,3) == ',#$' ) $php_code_string = ',\'' . substr($php_code_string, 3) . '\'';
						$php_code_string = 'com_paint(' . $coord . ($php_code_string == '' ? ',NULL' : $php_code_string ) . ')'; 
						break;
					case 'CIRCLE':
						preg_match_all('/(?:[^,(]|\([^)]*\))+/', $php_code_string, $php_code_string_math);
						preg_match('/\((.+)\)/', $php_code_string_math[0][0], $coord);
						if( isset($php_code_string_math[0][2]) && substr($php_code_string_math[0][2],0,2) == '#$' ) $php_code_string_math[0][2] = '\'' . substr($php_code_string_math[0][2], 2) . '\'';
						$mode = 0;
						if( isset($php_code_string_math[0][6]) && strtoupper(substr($php_code_string_math[0][6],-2)) == 'BF' ) $mode = 2;
						elseif(isset($php_code_string_math[0][6]) && strtoupper(substr($php_code_string_math[0][6],-1)) == 'B' ) $mode = 1;
						$php_code_string = 'com_circle(' . $coord[1] . ',' . $php_code_string_math[0][1] . ',' . (!isset($php_code_string_math[0][2]) ? 'NULL' : $php_code_string_math[0][2] ) . ',' . (!isset($php_code_string_math[0][3]) ? '0' : $php_code_string_math[0][3] ) . ',' . (!isset($php_code_string_math[0][4]) ? '360' : $php_code_string_math[0][4] ). ',' . (!isset($php_code_string_math[0][5]) ? '1' : $php_code_string_math[0][5] ) .',' . $mode . ')'; 
						break;
				}
                unset($php_code_string, $command, $var_eq);
            }
            //var_export($tree_command);
            // многосекционные команды
            if($for_num > 0) { // была команда for или foreach
                $var_eq = $tree_command[0]['var_eq']; // изменяемая в цикле переменка
                $in = false; $to = ''; $step = '1'; // выделим из веток команд TO, STEP
                foreach($tree_command as $tree_item) 
					switch($tree_item['command']) {
						case 'IN': $in = $tree_item['php_code_string']; break;
						case 'TO': $to = $tree_item['php_code_string']; break;
						case 'STEP': $step = $tree_item['php_code_string']; break;
					}	
                if($in !== false) { // есть ветка in - foreach
                    $php_code_string = 'foreach(' . $in . ' as ';
                    if( $to != '' ) $php_code_string .= $to.' => '; // ключ
                    $php_code_string .= $tree_command[0]['php_code_string'] .'){';
                } else { // нет ветки in - for
                    $php_code_string = 'for(' . $tree_command[0]['php_code_string'] . ';';
                    if( $to == '' ) $php_code_string .= 'true;'; // раз нет предела, то цикл навсегда
                    else $php_code_string .= '(' . $step .')>=0?' . $var_eq .'<=('. $to . '):' . $var_eq .'>('. $to .'); '; // иначе выход по условию
                    $php_code_string .= $var_eq . '='. $var_eq . '+(' . $step .')):'; // и добавим шаг
                }
            } else {
                if($tree_command[0]['command'] == 'DECLARE') { // declare a as b, c as d, e, f..  
                    $as = ''; // накопитель
                    foreach($tree_command as $tree_item){ // AS разрывает цепочку выражения на отрезки
                        if( $as !== '' ) $as .= '=>';
                        $vars = explode(',', $tree_item['php_code_string']);
                        foreach($vars as $num => $var){
                            $var = explode('$', $var);
                            if( isset($var[1]) ) $var = '"' . $var[1] . '"'; // разрешенное имя или что то уже в кавычках
                            else $var = $var[0];
                            $as .= $var . ',';
                        }
                    }
                    $php_code_string = '';
                    $var = array();
                    eval('$var = array('.str_replace(',=>','=>',$as).');');
                    com_declare($var);
                } elseif( $tree_command[0]['command'] == 'DIM' ) {
                    $as = ''; // выделим из веток команд AS
                    foreach($tree_command as $tree_item) if($tree_item['command'] == 'AS') $as = $tree_item['php_code_string'];
                    $var_eq = explode('(', $tree_command[0]['php_code_string']);
                    $php_code_string = '' . $var_eq[0] . '=';
                    if(!isset($var_eq[1])) { // нет скобок, это просто присвоение переменки
                        $php_code_string .= $as == '' ? 'Array()' : $as; // по умолчанию пустой массив;
                    } else {
                        if($var_eq[1] == ')'){ // пустые скобки
                            $php_code_string = '$' . $php_code_string . ( $as == '' || substr(trim($as), 0, 6) != 'array(' ? 'array()' : $as); // по умолчанию пустой массив;
                        } else {
                            $php_code_string = '$' . $php_code_string . 'com_DIM(' . $as . ',' . $var_eq[1];
                        }
                    }
                } elseif( $tree_command[0]['command'] == 'OPEN' ) {
                    $for = ''; $as = ''; $in = ''; $open = ''; // выделим из веток команд FOR, AS, OPEN
                    foreach($tree_command as $tree_item) 
						switch($tree_item['command']) {
							case 'OPEN': $open = $tree_item['php_code_string']; break;
							case 'FOR': $for = $tree_item['php_code_string']; break;
							case 'AS': $as = $tree_item['php_code_string']; break;
							case 'IN': $in = $tree_item['php_code_string']; break;
						}                    
                    $as = ltrim($as, '#'); // fix # - вдруг ктото решит как в vba написать
                    $for = explode(',', trim(str_replace(array('com_', '$'), ' ', $for))); 
                    $lock = ''; $access = 'WRITE';
                    foreach($for as $for_item){
                        if( strpos($for_item, 'LOCK') !== false && strpos($for_item, 'READ') !== false ) $lock = 'READ';
                        if( strpos($for_item, 'LOCK') !== false && strpos($for_item, 'WRITE') !== false ) $lock = 'WRITE';
                        if( strpos($for_item, 'ACCESS') !== false && strpos($for_item, 'READ') !== false ) $access = 'READ';
                    }    
                    $php_code_string = ( $as == '' ? '' : $as . '=' ) . 'com_OPEN(' . $open . ',"' . $for[0] . ',' . $lock . ',' . $access . '"' . ($in != '' ? ',' . $in : '') . ')';

                } elseif( $tree_command[0]['command'] == 'SCREEN' ) {
                    $as = 'NULL'; $in = 'NULL'; $to = 'NULL'; // выделим из веток команд AS, IN, TO
                    foreach($tree_command as $tree_item)
						switch($tree_item['command']) {
							case 'AS': $as = $tree_item['php_code_string']; break;
							case 'IN': $in = $tree_item['php_code_string']; break;
							case 'TO': $to = $tree_item['php_code_string']; break;
						}
                    $to = explode ('$QUALITY', $to);
                    $quality = isset($to[1]) ? $to[1] : 'NULL';
                    $to = preg_replace(array('/^\$(JPG|JPEG)$/ui', '/^\$GIF$/ui', '/^\$PNG$/ui', '/^\$WEBP$/ui', '/^\$BMP$/ui',), array(1,2,3,4,5), $to[0]);
                    $as = ltrim($as, '#'); // fix # - вдруг ктото решит как в vba написать
                    $php_code_string = 'com_screen(' . $as . ',' . $in . ',' . $to . ',' . $quality . (trim($tree_command[0]['php_code_string']) == '' ? '' : ',') . $tree_command[0]['php_code_string'] . ')';
                // дополнительная обработка команды IF c несколькими ветками (IF команда многосекционная команда по дефолту)
                }                elseif( count($tree_command) > 1 ) { // обнаружена вторая ветвь комманд, которую делает команда ELSE
                    if($if_atom_num > 0) $php_code_string = 'if(' . $if_code_string . '){' . $tree_command[0]['php_code_string'] .';}';
                    elseif($else_atom_num > 0) $php_code_string = $else_code_string == '' ? '' : 'if(' . $else_code_string . '){}';
                    else $php_code_string = '';
                    if($tree_command[1]['php_code_string'] == '') $php_code_string .= '}else{';
                    else $php_code_string .= 'else{' . $tree_command[1]['php_code_string'] . ';}';
                } elseif($if_atom_num > 0){ // команда с одной веткой
                    $php_code_string = 'If(' . $if_code_string . '){' . $tree_command[0]['php_code_string'] .';}';
                } else $php_code_string = $tree_command[0]['php_code_string']; // иначе вывод первой ветви команд (односекционной команды)
            }
            
			if ($label_code_string . $php_code_string != '' ) {
				$php_code_file .= $label_code_string . ( $debug_mode && $my_script_file == '' ? ' /*debug_timer*/ $GLOBALS[\'debug_time\'][] = (microtime(true)-$_SERVER[\'REQUEST_TIME_FLOAT\']).":'. htmlentities($bas_code_string, ENT_QUOTES) . '";/*debug_timer*/ ' : '' ) . $php_code_string . ( $long_string_begin ? '<<<\'EOT\'' : ';' ) . PHP_EOL;
				$label_code_string = '';
			} 
		}
	}
    $debug_php[str_replace($include_path, '', $script_file)] = $php_code_file;
    $script_file = $my_script_file; $debug_line = $my_script_line; // восстановим указатели компилятора
    if(!$debug_mode) file_put_contents($cache_script_file, $php_code_file); // в режиме DEBUG кеш выключен
    
	return $php_code_file;
}

// для использования кирилических имен в переменных, функциях и метках. непроизносимые символы заменены на {X}
// array_structure = false отключает преобразователи a.b => a['b']
function com_transliterate($string, $array_structure = true) {
	$keys =  explode( '.', strtr( mb_convert_case( $string, MB_CASE_UPPER, "UTF-8"),
		array(
		'А' => 'A', 'Ә' => 'AE', 'Ӓ' => 'AE', 'Ӑ' => 'AO', 'Б' => 'B', 'В' => 'V', 'Г' => 'G', 'Ғ' => 'G',
		'Ґ' => 'G', 'Ѓ' => 'G', 'Д' => 'D', 'Ђ' => 'D', 'Е' => 'E', 'Є' => 'E', 'Ё' => 'YO', 'Ӗ' => 'JO',
		'Ж' => 'ZH', 'Ӂ' => 'ZH', 'Җ' => 'ZH', 'Ӝ' => 'ZH', 'З' => 'Z', 'Ҙ' => 'Z', 'Ӟ' => 'Z', 'И' => 'I',
		'Ї' => 'Y', 'Й' => 'J', 'К' => 'K', 'Қ' => 'K', 'Ҡ' => 'K', 'Л' => 'L', 'Љ' => 'L', 'М' => 'M',
		'Н' => 'N', 'Њ' => 'N', 'Ң' => 'N', 'Ҥ' => 'N', 'О' => 'O', 'Ө' => 'O', 'Ӧ' => 'YO', 'П' => 'P',
		'Р' => 'R', 'С' => 'S', 'Ҫ' => 'C', 'Т' => 'T', 'Ќ' => 'K', 'Ћ' => 'K', 'У' => 'U', 'Ұ' => 'Y',
		'Ў' => 'Y', 'Ӯ' => 'Y', 'Ӱ' => 'Y', 'Ӳ' => 'Y', 'Ф' => 'F', 'Х' => 'H', 'Һ' => 'H', 'Ҳ' => 'H',
		'Ц' => 'C', 'Ч' => 'CH', 'Ҷ' => 'CH', 'Џ' => 'CH', 'Ш' => 'SH', 'Щ' => 'SCH', 'Ъ' => 'X', 'Ы' => 'I',
		'Ӹ' => 'I', 'Ь' => 'X', 'Э' => 'E', 'Ю' => 'YU', 'Я' => 'YA',		
		)	
	));
	$value = array_shift($keys);
	if(count($keys) > 0) foreach($keys as $key) if($array_structure)
		$value .= "[\"$key\"]";
	else 
		$value .= '.' . $key;
    return $value;
}


// включение дебаг режима
function com_debug($param){
    global $debug_mode, $debug_client;
    foreach($param as $debug_client){
        $debug_client = trim($debug_client, '"');
        if($debug_client == ''){
            $debug_mode = true;
        }else{
            if( filter_var ($debug_client, FILTER_VALIDATE_IP) === false ) { // COOKIE
                if( isset($_COOKIE[$debug_client]) ) $debug_mode = true;
            } else { // IP
                if( $debug_client == $_SERVER['REMOTE_ADDR'] ) $debug_mode = true;
            }
        }
        if($debug_mode) break;
    }
}

// чтение переменок из окружения вебсервера
function com_input($source, $vars) {
	$arr = array();
	$source = strtoupper( trim($source, '"' ) ); // определим источник данных и загрузим его массив
	switch($source) {
		case 'GET';	$arr_source = com_change_key ( $_GET, CASE_UPPER); break;
		case 'POST'; $arr_source = com_change_key ( $_POST, CASE_UPPER); break;
		case 'COOKIE'; $arr_source = com_change_key ( $_COOKIE, CASE_UPPER); break;
		case 'FILE'; $arr_source = com_change_key ( $_FILE, CASE_UPPER); break;
		case 'SESSION'; 
            if( !$is_session_start ) { 
                session_start(); 
                $is_session_start = true; 
            };
            $arr_source = com_change_key ( $_SESSION, CASE_UPPER); 
            break;
		case 'HEADER'; $arr_source = com_change_key ( apache_request_headers(), CASE_UPPER); break;
		case 'RAW'; $arr_source = array(file_get_contents('php://input')); break;
		default; $arr_source = &$GLOBALS['_WEB']; break;
	}
	foreach($vars as $var) {
		$var = explode('$', $var );
		if($source == 'RAW') $var[1] = 0;
		switch($var[0]) {
			case '(int)'; $arr[] = isset($arr_source[$var[1]]) ? (int)$arr_source[$var[1]] : 0; break;
			case '(float)'; $arr[] = isset($arr_source[$var[1]]) ? (float)$arr_source[$var[1]] : 0; break;
			case '(boolean)'; $arr[] = isset($arr_source[$var[1]]) ? (boolean)$arr_source[$var[1]] : false; break;
			case '(array)'; $arr[] = isset($arr_source[$var[1]]) ? (array)$arr_source[$var[1]] : array(); break;
			default; $arr[] = isset($arr_source[$var[1]]) ? $arr_source[$var[1]] : ''; break;
		}
	}
	return $arr;
}

// запись переменок из окружения вебсервера
function com_output($source, $vars, $param) {
    global $is_session_start;
    switch(strtoupper( $source )) {
		case 'COOKIE'; 
            $life_time = false;
            foreach($vars as $key => $var) { // ищем константу управляющую временем жизни куки
                if( strpos( $var , '"') !== false ) $life_time = $param[$key];
                if( strpbrk( $var , '"$') !== false ) $life_time = $param[$key];
            }
        	foreach($vars as $key => $var) {
                $var = explode('$', $var );
                if( isset($var[1]) ) {
                    if( $life_time === false ) setcookie( $var[1], $param[$key], strtotime('+30 minute'), '/' );
                    elseif( is_int($life_time) ) setcookie( $var[1], $life_time <= 0 ? 0 : time() + $life_time, '/' );
                    else setcookie( $var[1], $param[$key], strtotime($param[$key]) === false ? 0 : strtotime($param[$key]), '/' );
                }
            }    
        break;
		case 'SESSION'; 
            if( !$is_session_start ) {
                session_start();
                $is_session_start = true;
            }
        	foreach($vars as $key => $var) {
                $var = explode('$', $var );
                $_SESSION[$var[1]] = $param[$key];
            }
		break;
		default; // по умолчанию считаем что передаются заголовки.
            $replace = true;
            $http_response_code = false;
            foreach($vars as $key => $var) if($var !==''){ // ищем константы дополнительных параметров
                if( $var == 'FALSE') $replace = false; // true и так есть
                if( strpbrk( $var , '"TF$') === false ) $http_response_code = $param[$key]; // это числовой параметр
            }
            if( strtoupper( $source ) != 'HEADER' ) { // вставим в параметры первый элемент, если это не обозначение потока
                array_unshift ($vars, '"' . $source .'"' );
                array_unshift ($param, $source );
            }
            foreach($vars as $key => $var) {
                $out = '';
                if( strpbrk( $var , '"$') !== false ) $out = $param[$key];
                if( $out != '' ) {
                    if($http_response_code === false) header($param[$key], $replace);
                    else header($param[$key], $replace, $http_response_code);
                }
            }
        break;
	}
}

// остановка скрипта
function com_stop( $vars, $names){
    global $debug_vars;
    foreach($names as $key => $var) {
        $var = explode('$', $var );
        if(isset($var[1])) $debug_vars[$var[1]] = $vars[$key];
        else $debug_vars[$var[0]] = $vars[$key];
    }
    exit;
}

// команда DECLARE
function com_declare( $vars ){
    global $def_key;
	$keys = array();
	foreach($vars as $var) $keys[$var] = $var;
    $def_key = array_merge ($def_key, $keys);
}

/*
    ФУНКЦИИ
*/
// UCASE -> mb_strtoupper без изменений
// LCASE -> mb_strtolower без изменений
// LEN -> mb_strlen без изменений

// строчный if
function IIF( $expression, $true_part = '', $false_part = ''){
    return $expression ? $true_part : $false_part;
}
function com_ASC($string = "", $offset = 1) {
    $string = mb_substr($string, $offset, 1);
    $offset = 0;
    $code = ord($string); 
    if ($code >= 128) { // 0xxxxxxx
        if ($code < 224) $bytesnumber = 2; //110xxxxx
        else if ($code < 240) $bytesnumber = 3; //1110xxxx
        else if ($code < 248) $bytesnumber = 4; //11110xxx
        $codetemp = $code - 192 - ($bytesnumber > 2 ? 32 : 0) - ($bytesnumber > 3 ? 16 : 0);
        for ($i = 2; $i <= $bytesnumber; $i++) {
            $offset ++;
            $code2 = ord(substr($string, $offset, 1)) - 128; //10xxxxxx
            $codetemp = $codetemp*64 + $code2;
        }
        $code = $codetemp;
    }
    return $code;
}
function com_CHR($codes = 0) {
    if (is_scalar($codes)) $codes = func_get_args();
    $str= '';
    foreach ($codes as $code) if( is_int($code) ) $str .= mb_convert_encoding('&#'.$code.';', "UTF-8", "HTML-ENTITIES");
    return $str;
}
function com_MID($string = "", $position = 1, $len = NULL) {
    return $position < 1 ? "" : mb_substr ( $string, $position-1, $len );
}
function com_LEFT($string = "", $len = 1) {
    return $len < 1 ? "" : mb_substr ( $string, 0, $len );
}
function com_RIGHT($string = "", $len = 1) {
    return $len < 1 ? "" : mb_substr ( $string, -$len );
}
function com_TRIM($string = "", $char = NULL, $icase = false) {
    return is_null($char) ? trim($string) : preg_replace('/^['.$char.']*(?U)(.*)['.$char.']*$/um' . ($icase ? 'i' : ''), '\\1', $string);
}
function com_RTRIM($string = "", $char = NULL, $icase = false) {
    return is_null($char) ? trim($string) : preg_replace('/^(?U)(.*)['.$char.']*$/um' . ($icase ? 'i' : ''), '\\1', $string);
}
function com_LTRIM($string = "", $char = NULL, $icase = false) {
    return is_null($char) ? trim($string) : preg_replace('/^['.$char.']*(?U)(.*)/um' . ($icase ? 'i' : ''), '\\1', $string);
}
function com_INSTR($string = "", $needle = "", $offset = 1, $icase = false) {
    $pos = $offset == 0 ? false : ($icase ? mb_stripos ( $string, $needle, $offset-1 ) : mb_strpos ( $string, $needle, $offset-1 ));
    return ($pos === false || $string == "" || $needle == "" || $offset > mb_strlen($string) ? 0 : $pos+1);
}
function com_INSTRREV($string = "", $needle = "", $offset = 1, $icase = false) {
    $pos = $offset == 0 ? false : ($icase ? mb_strripos ( $string, $needle, $offset > 0 ? $offset-1 : $offset ) : mb_strrpos ( $string, $needle, $offset > 0 ? $offset-1 : $offset ));
    return ($pos === false || $string == "" || $needle == "" || abs($offset) > mb_strlen($string) ? 0 : $pos+1);
}
function com_SPLIT($string = "", $delimite = " ", $limit = -1, $icase = false) {
    return preg_split ('/'.preg_quote($delimite).'/um'. ($icase ? 'i' : ''), $string, $limit === true ? -1 : $limit );
}
function com_JOIN($string = "", $delimite = " ") {
    return implode( $delimite, $string );
}
function com_VAL($string = "", $round = -1, $up = true) {
    return $round < 0 || $round === true ? floatval($string) : round( floatval($string), $round, $up ? PHP_ROUND_HALF_UP : PHP_ROUND_HALF_DOWN);
}
function com_STR($number = 0, $round = -1, $up = true) {
    return $round < 0 || $round === true ? strval($number) : number_format ( round( $number, $round, $up ? PHP_ROUND_HALF_UP : PHP_ROUND_HALF_DOWN), $round , '.', '');
}
function com_REPLACE($string = "", $search = "", $replace = "", $start = 1, $limit = true, $icase = false) {
    return mb_substr( $string, 0, $start-1) . preg_replace('/'.preg_quote($search).'/um'. ($icase ? 'i' : ''), $replace, mb_substr( $string, $start-1 ) , $limit === true ? -1 : $limit );
}
function com_CRC32( $str ) {
    return hash("crc32b", $str);
}
function com_ENCODE( $str, $flag = "URL", $pass = "") {
	switch( strtoupper($flag) ) {
		case 'OPENSSL': return openssl_encrypt( $str, 'aes128', $pass, 0, substr( md5($pass), -16 ) ); 
		case 'HTML': return htmlspecialchars( $str, ENT_QUOTES | ENT_SUBSTITUTE | ENT_HTML5 );
		case 'URL': return rawurlencode( $str );
		case 'BASE64': return base64_encode ( $str );
		case 'HEX': return bin2hex( $str );
		case 'SLASH': return addslashes( $str ); 
	}
}
function com_DECODE( $str, $flag = "URL", $pass = "") {
	switch( strtoupper($flag) ) {
		case 'SLASH': return stripslashes( $str );
		case 'HEX': return hex2bin( $str );
		case 'BASE64': return base64_decode( $str );
		case 'URL': return rawurldecode( $str );
		case 'HTML': return html_entity_decode( $str, ENT_QUOTES |  ENT_HTML5, 'UTF-8');
		case 'OPENSSL': return openssl_decrypt( $str, 'aes128', $pass, 0,  substr( md5($pass), -16 ) );
    }
} 
/*
    Обработка массивов
*/
// array_merge_recursive => MERGE 
// array_unshift => UNSHIFT
// array_shift => SHIFT
// array_pop => POP
// array_push => PUSH
// array_splice => SPLICE
// array_slice => SLICE
// array_reverse => REVERSE
// count и shuffle без изменений

// ARRAY - замена ключей в массиве на имена по правилам. Аналог array_change_key_case но работает с кирилицей и всегда верхний регистр. Довольно медленная.
function com_change_key($arr) {
	$ret = array();
    foreach ($arr as $key => $var) {
        $ret[com_transliterate( str_replace(array('-', '.' ), '_', $key) , false) ] = is_array ($var) ? com_change_key($var) : $var;
    }
    return $ret;
}

// CONCAT
function com_CONCAT() {
	$args = func_get_args();
	foreach ($args as $key => $val) $args[$key] = array_values($val);
	return call_user_func_array('array_merge', $args);
}

function com_UNIQUE($array) {
  $result = array_map("unserialize", array_unique(array_map("serialize", $array)));
  foreach ($result as $key => $value) {
    if ( is_array($value) ) $result[$key] = com_UNIQUE($value);
  }
  return $result;
}

function com_RANDOMISE($list, $index = false) { 
    if (!is_array($list)) return $list; 
        $random = array(); 
        $keys = array_keys($list); 
        shuffle($keys); 
        $id = 0;
        foreach ($keys as $key){
            if(is_int($key) && !$index) {
                $random[$id] = $list[$key];    
                $id++;
            } else $random[$key] = $list[$key]; 
        }
        return $random; 
} 

function com_INDEXOF($array, $needle, $offset = 0, $limit = NULL, $icase = false) { 
        if (!is_array($array)) return $array; 
        $searh_array = array_slice($array, $offset, $limit, true);
        $result = -1; 
        if(is_string($needle) && $icase) $needle = mb_strtoupper($needle);
        if(is_array($searh_array)) foreach ($searh_array as $key => &$item){
            if( $item == $needle || ($icase && is_string($item) && mb_strtoupper($item) == $needle)) {
                $result = $offset;
                break;
            } 
            $offset++;
        }
        return $result; 
} 

function com_LASTINDEXOF($array, $needle, $offset = 0, $limit = NULL, $icase = false) { 
        if (!is_array($array)) return $array; 
        $searh_array = array_reverse( array_slice($array, $offset, $limit, true), true);
        $result = -1; $offset += count($searh_array)-1;
        if(is_string($needle) && $icase) $needle = mb_strtoupper($needle);
        if(is_array($searh_array)) foreach ($searh_array as $key => &$item){
            if( $item == $needle || ($icase && is_string($item) && mb_strtoupper($item) == $needle)) {
                $result = $offset;
                break;
            } 
            $offset--;
        }
        return $result; 
} 

function com_GETVALUE() {
	$args = func_get_args();
    if (!is_array($args[0])) return NULL; 
    $ar = $args[0];
	foreach ($args as &$index) {
        if(!is_int($index)) continue;
        if(!is_array($ar)) return NULL; 
        $keys = array_keys($ar);
        if(!array_key_exists($index, $keys)) return NULL; 
        $ar = $ar[$keys[$index]];
	}
	return $ar;
}

function com_SETVALUE(&$array) {
	$args = func_get_args();
    if ( !is_array($args[0]) && !array_key_exists( 1, $args ) ) return NULL; 
    $ar =& $array;
	foreach ($args as $key => &$index) {
        if( $key < 2 ) continue;
        if ( !is_array($ar) ) return NULL; 
        $keys = array_keys( $ar );
        if(!array_key_exists($index, $keys)) return NULL; 
        $ar =& $ar[$keys[$index]];
	}
    $result = $ar;
    $ar = $args[1];
    return $result;
}

function com_DIM() {
    $args = func_get_args();
    $result = array_shift ($args);
    $args = array_reverse($args);
    foreach ($args as $index) if(is_int($index)) $result = array_fill(0, $index, $result);
    return $result;
}    

function com_JSON($value, $compact = true){
    if(is_string($value)){ // раскодировать в массив
        if($compact) {
            $value = str_replace(array("\n","\r"),'',$value);
            preg_match_all('/"((?<=\\\\)"|[^"])*"|([^"]+)/u', $value, $array_math, PREG_SET_ORDER);
            $value = '';
            if( count( $array_math ) > 0 ) foreach( $array_math as $array_math_strings ) {
                if( count($array_math_strings) > 2) { // найден отрезок вне кавычек, его надо разобрать
                    $array_math_strings[0] = str_replace('\'','"',$array_math_strings[0]); // заменим одинарные кавычки на двойные
                    $array_math_strings[0] = preg_replace("#(/\*([^*]|[\r\n]|(\*+([^*/]|[\r\n])))*\*+/)|([\s\t]//.*)|(^//.*)#", '', $array_math_strings[0]); //удалим коментарии
                    $array_math_strings[0] = preg_replace('/([{,]+)(\s*)([^"]+?)\s*:/','$1"$3":',$array_math_strings[0]); // добавим кавычки к именам если они пропущены
                    $array_math_strings[0] = preg_replace('/,\s*([\]}])/m', '$1',$array_math_strings[0]); //удалим последнюю запятую перед фигурной скобкой.
                }
                $value .= $array_math_strings[0];
            }
        }
        return json_decode($value, JSON_BIGINT_AS_STRING | JSON_OBJECT_AS_ARRAY);
    }else{ //декодировать в строку
        return json_encode($value, JSON_NUMERIC_CHECK | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | ($compact ? 0 : JSON_PRETTY_PRINT));
    }
}

function com_SORT(&$array, $flag = '', $column = true) {
    $flag = preg_split("/[\s,]+/", strtoupper($flag));
    $sort_order = in_array('DESC',$flag) ? SORT_DESC : SORT_ASC;
    $sort_case = in_array('CASE',$flag) ? SORT_FLAG_CASE  : 0;
    $sort_iskey = in_array('KEY',$flag) ? true : false;
    $sort_flag = SORT_REGULAR;
    $sort_flag = in_array('NUMERIC',$flag) ? SORT_NUMERIC  : $sort_flag;
    $sort_flag = in_array('STRING',$flag) ? SORT_LOCALE_STRING | $sort_case : $sort_flag;
    $sort_flag = in_array('NATURAL',$flag) ? SORT_NATURAL | $sort_case : $sort_flag;
    $sort_key = array();
    foreach ($array as $key => $item) if(is_string($item)) {
        $sort_key[] = $sort_case == 0 ? ($sort_iskey ? $key : $item) :  mb_convert_case( ($sort_iskey ? $key : $item), MB_CASE_UPPER, "UTF-8"); 
    } elseif(is_array($item) && $column !== false ){
        if(isset($item[$column]) && is_string($item[$column])) $sort_key[] = $sort_case == 0 ? ($sort_iskey ? $key : $item[$column]) :  mb_convert_case( ($sort_iskey ? $key : $item[$column]), MB_CASE_UPPER, "UTF-8"); 
        elseif(isset($item[$column])) $sort_key[] = ($sort_iskey ? $key : $item[$column]);
        else return false;
    } else $sort_key[] = $sort_iskey ? $key : $item;

    return array_multisort ($sort_key, $sort_order, $sort_flag, $array);
}   
function com_LBOUND($value){
    return (is_array($value) && !empty($value)) ? min(array_keys($value)) : 0;
}
function com_UBOUND($value, $rad = true){
    return (is_array($value) && !empty($value)) ? max(array_keys($value)) : 0;
}
function com_KEYS($array, $value = NULL, $icase = false){
    if(!$icase) return array_keys($array, $value, true);
    $result = array();
    $value = mb_convert_case( $value, MB_CASE_UPPER, "UTF-8");
    foreach($array as $key => $item) if( $value === $item || (is_string($item) && $value == mb_convert_case( $item, MB_CASE_UPPER, "UTF-8")) ) $result[] = $key;
    return $result;
}
/*
    Числовые функции
*/
function com_SIN($value, $rad = true){
    return $rad ? sin($value) : sin(M_PI * $value / 180);
}
function com_COS($value, $rad = true){
    return $rad ? cos($value) : cos(M_PI * $value / 180);
}
function com_TAN($value, $rad = true){
    return $rad ? tan($value) : tan(M_PI * $value / 180);
}
function com_CTG($value, $rad = true){
    return $rad ? cos($value) / sin($value) : cos(M_PI * $value / 180) / sin(M_PI * $value / 180);
}
function com_SEC( $value ) {
	return $rad ? 1 / cos($value) : 1 / cos(M_PI * $value / 180);
}
function com_COSEC( $value ) {
	return $rad ? 1 / sin($value) : 1 / sin(M_PI * $value / 180);
}
function com_ASIN($value, $rad = true){
    return $rad ? asin($value) : asin($value) * 180 / M_PI;
}
function com_ACOS($value, $rad = true){
    return $rad ? acos($value) : acos($value) * 180 / M_PI;
}
function com_ATAN($value, $rad = true){
    return $rad ? atan($value) : atan($value) * 180 / M_PI;
}
function com_ACTG($value, $rad = true){
    $result = asin( 1 / sqrt( 1 + $value * $value ));
    return $rad ? ($value < 0 ? M_PI - $result : $result) : ($value < 0 ? M_PI - $result : $result) * 180 / M_PI;
}
function com_ASEC( $value ) {
	return $value == 0 ? NULL : acos( 1 / $value );
}
function com_ACOSEC( $value ) {
	return $value == 0 ? NULL : asin( 1 / $value);
}
function com_NOTATION( $value, $basefrom = 10, $baseto = 10){
    return strtoupper( base_convert ($value, $basefrom, $baseto ));
}
function com_MOD( $div1, $div2){
    return is_int($div1) && is_int($div2) ? $div1 % $div2 : fmod($div1, $div2);
}
function com_DIV( $div1, $div2){
    return (int)($div1 / $div2);
}
function com_RAND( $min = NULL, $max = 2147483647){
    global $last_rnd;
    if(is_null($min)) $last_rnd = mt_rand() / mt_getrandmax();
    elseif($min !== -1) $last_rnd = mt_rand($min, $max); 
    return $last_rnd;
}

function com_PLURAL( $n = 0, $ost = array('','','',''), $shownum = true){
    return $n==0 ? array_pop($ost) : ( $shownum ? $n . ' ' : '') . (count($ost) == 2 ? $ost[0] . ( $n != 1 ? 's ' : ' ') : ($n % 10 == 1 && $n % 100 != 11 ? $ost[0] : ($n % 10 >= 2 && $n % 10 <= 4 && ( $n % 100 < 10 || $n % 100 >= 20) ? $ost[1] : $ost[2] )) . ' ') ;
}

/*
    Работа с файлами
*/
// функция вычисления реального пути в от корня сайта. проверяет не вышел ли путь за пределы сайта и если да, не выпускает (возвр. FALSE).
function com_REALPATH($name){
    global $include_path;
    $name = $include_path . '/' . $name;
    $path = str_replace(array('/', '\\'), DIRECTORY_SEPARATOR, $name);
    $parts = array_filter(explode(DIRECTORY_SEPARATOR, $path), 'strlen');
    $absolutes = array();
    foreach ($parts as $part) {
        if ('.' == $part) continue;
        if ('..' == $part) array_pop($absolutes);
        else $absolutes[] = $part;
    }
    $name = DIRECTORY_SEPARATOR . implode(DIRECTORY_SEPARATOR, $absolutes);
    if( substr( $name, 0, strlen($include_path) ) !== $include_path ) return false;
    return $name;
} 
function com_OPEN( $name, $for, $in = NULL ){
    $writeable = '+';
    if ( is_null( parse_url( $name, PHP_URL_SCHEME ) ) ) {
        $name = com_REALPATH($name);
        if($name === false) return false;
    } else $writeable = '';
    $for = explode(',', $for);
    if( $for[2] == 'READ' ) $mode = 0644; else $mode = 0777;
    $lock = $for[1]; $for = $for[0];
    if( strpos($for, 'OUTPUT') !== false ) $for = "wt{$writeable}";
    elseif( strpos($for, 'INPUT') !== false ) $for = "rt{$writeable}";
    elseif( strpos($for, 'APPEND') !== false ) $for = "at{$writeable}";
    elseif( strpos($for, 'BINARY') !== false ) $for = "cb{$writeable}";
    if( strpos($for, 'LOAD') !== false) return file_get_contents( $name );
    elseif( strpos($for, 'RANDOM') !== false) return file( $name, FILE_IGNORE_NEW_LINES);
    elseif( strpos($for, 'CSV') !== false) {
        $csv = file( $name, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES );   
        foreach ($csv as &$item) $item = str_getcsv($item);
        return $csv;
    } elseif( strpos($for, 'INI') !== false) return parse_ini_file( $name, TRUE, INI_SCANNER_TYPED );
    elseif( strpos($for, 'JSON') !== false) return json_decode( file_get_contents( $name ), JSON_BIGINT_AS_STRING | JSON_OBJECT_AS_ARRAY );
    elseif( strpos($for, 'SAVE') !== false) { 
        if( is_array( $in ) ) file_put_contents( $name, json_encode( $in, JSON_NUMERIC_CHECK | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT), LOCK_EX ); 
        else file_put_contents( $name, $in, LOCK_EX );
        chmod( $name, $mode );
    } else { 
        $file = fopen($name, $for);
        if( strpos($for, 'OUTPUT') !== false || strpos($for, 'APPEND') !== false || strpos($for, 'BINARY') !== false) chmod( $name, $mode );
        if( $lock !== '' ) flock($file, $lock == 'READ' ? LOCK_SH : LOCK_EX );
        return $file;
    }
}
function com_LOCK( $file, $mode = false ){
    flock($file, $mode ? LOCK_SH : LOCK_EX );
}
function com_UNLOCK( $file ){
    flock($file, LOCK_UN );
}
function com_GET( $file, $length = 8192 ){
    return fread( $file, $length );
}
function com_PUT( $file, $string, $length = NULL ){
    return is_null($length) ? fwrite( $file, $string ) : fwrite( $file, $string, $length);
}
function com_READ( $file, $length = 8192 ){
    return fgets ( $file, $length );
}
function com_WRITE( $file, $string, $length = NULL ){
    return is_null($length) ? fwrite( $file, $string.PHP_EOL ) : fwrite( $file, $string.PHP_EOL, $length);
}
function com_FILEEXISTS($name){ 
    if ( is_null( parse_url( $name, PHP_URL_SCHEME ) ) ) {
        $name = com_REALPATH($name);
        if($name === false) return false;
        return file_exists($name);
    } else {
        stream_context_set_default( array( 'http' => array( 'method' => 'HEAD' ) ) );
        if(empty(dns_get_record ($name))) return false;
        $headers = get_headers($name);
        if (!is_array($headers)) return false;
        foreach($headers as $header){
            preg_match('/^HTTP\S+\s(\d+)/ui', $header, $math  );
            if( isset($math[1]) && $math[1] == 404 ) return false;
        }
        return true;
    }
    
}
function com_FILELEN($name){
    $name = com_REALPATH($name);
    if($name === false) return false;
    return filesize($name);
}
function com_FILEDATETIME($name, $format = 'c'){
    if ( is_null( parse_url( $name, PHP_URL_SCHEME ) ) ) {
        $name = com_REALPATH($name);
        if($name === false) return false;
        return com_DATE($format, filemtime($name));
    } else {
        stream_context_set_default( array( 'http' => array( 'method' => 'HEAD' ) ) );
        if(empty(dns_get_record ($name))) return false;
        $headers = get_headers($name);
        if (!is_array($headers)) return false;
        foreach($headers as $header){
            preg_match('/^DATE:(.+)(|GMT)/ui', $header, $math );
            if( isset($math[1])) return com_DATE($format, strtotime($math[1]));
        }
        return false;
    }
}
function com_FILEATTR($name = ''){ 
    if ( is_null( parse_url( $name, PHP_URL_SCHEME ) ) ) {
        $name = com_REALPATH($name);
        if($name === false) return false;
        if(file_exists($name)) return array('REALPATH' => is_link($name) ? readlink($name) : $name, 'TYPE' => strtoupper( filetype($name) ), 'READ' => is_readable($name), 'WRITE' => is_writable($name));
        return false;
    } else {
        stream_context_set_default( array( 'http' => array( 'method' => 'HEAD' ) ) );
        if(empty(dns_get_record ($name))) return false;
        $headers = get_headers($name);
        if (!is_array($headers)) return false;
        foreach($headers as $header){
            preg_match('/^HTTP\S+\s(\d+)/ui', $header, $math  );
            if( isset($math[1]) && $math[1] == 404 ) return false;
        }
        return array('REALPATH' => $name, 'TYPE' => 'URL', 'READ' => true, 'WRITE' => false);
    }    
}
function com_KILL($name){
    $name = com_REALPATH($name);
    if($name === false) return false;   
    unlink($name);
}
function com_NAME($old, $new){
    $new = com_REALPATH($new);
    if($new === false) return false;
    if(is_uploaded_file($old)) {
        move_uploaded_file($old, $new);
    } else{
        $old = com_REALPATH($old);
        if($old === false) return false;
        rename($old, $new);
    }
}
function com_FILECOPY($old, $new){
    $new = com_REALPATH($new);
    if($new === false) return false;
    if ( is_null( parse_url( $old, PHP_URL_SCHEME ) ) ) $old = com_REALPATH($old);
    if($old === false) return false;
    if( is_dir($old) ) {
        mkdir( $new );
        $objects = scandir($old);
        if( sizeof($objects) > 0 ) {
            foreach( $objects as $file ) {
                if( $file == "." || $file == ".." ) continue;
                if( is_dir( $path.DIRECTORY_SEPARATOR.$file ) ) com_FILECOPY( $path.DIRECTORY_SEPARATOR.$file, $dest.DIRECTORY_SEPARATOR.$file );
                else copy( $path.DIRECTORY_SEPARATOR.$file, $dest.DIRECTORY_SEPARATOR.$file );
            }
        }
        return true;
    } elseif( is_file($path) ) return copy($path, $dest);
    else return false;
}
function com_RMDIR($dir) { 
    $dir = com_REALPATH($dir);
    if($dir === false) return false;
    $files = array_diff(scandir($dir), array('.', '..') ); 
    foreach ($files as $file) { 
        if (is_dir($dir.DIRECTORY_SEPARATOR.$file)) com_RMDIR($dir.DIRECTORY_SEPARATOR.$file);
        else unlink($dir.DIRECTORY_SEPARATOR.$file); 
    } 
    return rmdir($dir); 
} 
function com_MKDIR($dir) { 
    $dir = com_REALPATH($dir);
    if($dir === false) return false;
    return mkdir($dir, 0777, true);
}
function com_DIRLEN($dir) { 
    $dir = com_REALPATH($dir);
    if($dir === false) return false;
    return disk_total_space ($dir);
}
function com_DIRSPACE($dir) { 
    $dir = com_REALPATH($dir);
    if($dir === false) return false;
    return disk_free_space ($dir);
}
function com_DIR($dir) { 
    $dir = com_REALPATH($dir);
    if($dir === false) return false;
    return glob($dir,GLOB_BRACE);
}
/*
    Работа с датами и временем
*/
function com_TIMER(){
    global $_WEB;
    return number_format  ( microtime(true)-$_WEB['REQUEST_TIME'], 6, '.', '' );
}
function com_DATE($format = 'd.m.Y H:i:s', $date = ''){
    $format=str_replace(array('M','D','ddddd','aaaa','dddd','ddd','dd','d','j#','oooo','mmmm','mmm','mm','m','n#','yyyy','yy','y','hh','h','ss','S','nn','N','ttttt','AM/PM','am/pm','A/P','a/p','AMPM','ww','w','C#','c#','c'),
        array('mm','dd','C#','l','l##','l#','j#','j','d','F','F##','F#','n#','n','m','Y','y','Z','H','G','s','s','i','i','c#','A','a','A','a','A','W','N','d.m.Y','H:i:s','d.m.Y H:i:s'),$format);
    $date=preg_replace(array('/сегодня/umi','/сейчас/umi','/(текущ(ий|его|ему|ими|им|ем|ие|их)|время)/umi','/следующ(ий|его|ему|ими|им|ем|ие|их)/umi','/предыдущ(ий|его|ему|ими|им|ем|ие|их)/umi','/перв(ый|ой|ого|ому|ыми|ым|ом|ые|ых|ые)/umi','/последн(ий|его|ему|ими|им|ем|ие|их|ие)/umi','/(год(ом|ах|ами|ам|ов|у|а|е|ы|)|лет(ом|ах|ами|ам|ов|у|а|е|ы|))/umi','/месяц(ами|ax|а|у|ем|ев|е|ы|)/umi','/д(ень|ню|нём|нем|ни|ней|нями|ням|нях|не|ня)/umi','/час(ом|ах|ами|ам|ов|у|а|е|ы|)/umi','/минут(ой|ах|ами|ам|у|а|е|ы|)/umi','/секунд(ой|ах|ами|ам|у|а|е|ы|)/umi','/Воскресен(ьями|ьям|ьем|ье|ья|ью|ий|ях)/umi','/Понедельник(ом|ах|ами|ам|ов|у|а|е|и|)/umi','/Вторник(ом|ах|ами|ам|ов|у|а|е|и|)/umi','/Сред(ой|ах|ами|ам|у|а|е|ы|)/umi','/Четверг(ом|ах|ами|ам|ов|у|а|е|и|)/umi','/Пятниц(ой|ах|ами|ам|у|а|е|ы|)/umi','/Суббот(ой|ах|ами|ам|у|а|е|ы|)/umi','/Январ(ём|ем|ей|ями|ям|ях|ь|я|ю|е|и)/umi','/Феврал(ём|ем|ей|ями|ям|ях|ь|я|ю|е|и)/umi','/Март(ом|ах|ов|ами|ам|у|а|е|ы|)/umi','/Апрел(ем|ей|ями|ям|ях|ь|я|ю|е|и)/umi','/Ма(ем|ев|ями|ям|ях|й|я|ю|е|и)/umi','/Июн(ем|ей|ями|ям|ях|ь|я|ю|е|и)/umi','/Июл(ем|ей|ями|ям|ях|ь|я|ю|е|и)/umi','/Август(ом|ах|ами|ов|ам|у|а|е|ы|)/umi','/Сентябр(ём|ем|ей|ями|ям|ях|ь|я|ю|е|и)/umi','/Октябр(ём|ем|ей|ями|ям|ях|ь|я|ю|е|и)/umi','/Ноябр(ём|ем|ей|ями|ям|ях|ь|я|ю|е|и)/umi','/Декабр(ём|ем|ей|ями|ям|ях|ь|я|ю|е|и)/umi','/недел(ей|ями|ям|ях|ь|я|ю|е|и)/umi'),
        array(date('d.m.Y'),date('H:i:s'),'now','next','previous','first','last','year','month','day','hour','minute','second','Sunday','Monday','Tuesday','Wednesday','Thursday','Friday','Saturday','January','February','March','April','May','June','July','August','September','October','November','December','week'), $date);
    //return $date;
    $date = date($format, $date == '' ? time() : strtotime($date));
    $date = str_replace(array('Sunday##', 'Monday##', 'Tuesday##', 'Wednesday##', 'Thursday##', 'Friday##', 'Saturday##', 'Sunday##', 'Monday#', 'Tuesday#', 'Wednesday#', 'Thursday#', 'Friday#', 'Saturday#', 'January##', 'February##', 'March##', 'April##', 'May##', 'June##', 'July##', 'August##', 'September##', 'October##', 'November##', 'December##', 'January#', 'February#', 'March#', 'April#', 'May#', 'June#', 'July#', 'August#', 'September#', 'October#', 'November#', 'December#'),
                        array('Воскресенье', 'Понедельник', 'Вторник', 'Среда', 'Четверг', 'Пятница', 'Суббота', 'ВС', 'ПН', 'ВТ', 'СР', 'ЧТ', 'ПТ', 'СБ', 'Январь', 'Февраль', 'Март', 'Апрель', 'Май', 'Июнь', 'Июль', 'Август', 'Сентябрь', 'Октябрь', 'Ноябрь', 'Декабрь', 'ЯНВ', 'ФЕВ', 'МАР', 'АПР', 'МАЙ', 'ИЮН', 'ИЮЛ', 'АВГ', 'СЕН', 'ОКТ', 'НОЯ', 'ДЕК'), $date);
    return $date;
}

function com_DATEDIFF($format = '', $date1 = '', $date2 = '' ){
    $i = date_diff(date_create(com_DATE('Y-m-d H:i:s', $date1)), date_create(com_DATE('Y-m-d H:i:s', $date2)));
    $format=str_replace(array('d#','+','h#','n#','s#','y','m','d','h','n','s','Y#','M#','D#','H#','N#','S#','Y','M','D','H','N','S'), array(
            $i->format('%a'), $i->format('%R'), 
            $i->format('%a')*24 + $i->h, $i->format('%a')*24 + $i->h * 60 + $i->i, $i->format('%a')*24 + $i->h * 60 + $i->i*60 + $i->s,
            $i->format('%y'), $i->format('%m'), $i->format('%d'), $i->format('%h'), $i->format('%i'), $i->format('%s'),
            com_PLURAL($i->format('%y'), array('year','')), com_PLURAL($i->format('%m'), array('month','')), com_PLURAL($i->format('%d'), array('day','')),
            com_PLURAL($i->format('%h'), array('hour','')), com_PLURAL($i->format('%i'), array('minute','')), com_PLURAL($i->format('%s'), array('second','')),
            com_PLURAL($i->format('%y'), array('год','года','лет','')), com_PLURAL($i->format('%m'), array('месяц','месяца','месяцев','')), com_PLURAL($i->format('%d'), array('дней','дня','дней','')),
            com_PLURAL($i->format('%h'), array('час','часа','часов','')), com_PLURAL($i->format('%i'), array('минута','минуты','минут','')), com_PLURAL($i->format('%s'), array('секунда','секунды','секунд','')),
        ), $format);    
    return trim($format);
}

/*
    Работа с базой
*/

function com_connect($driver = '', $dbname = '', $user = '', $pass = '', $host = 'localhost', $port = 0 ){
    global $db_connect;
    $param = array(
        PDO::ATTR_PERSISTENT => true,
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    );
    if( $driver == '') {
        $GLOBALS['db_use']['connect'] = new PDO($GLOBALS['db_use']['dsn'], $GLOBALS['db_use']['user'], $GLOBALS['db_use']['pass'], $param);
    } else {
        $dsn = array();
        if( $host != '' ) $dsn[] = 'host=' . $host;
        if( $port != 0 ) $dsn[] = 'port=' . $port;
        $dsn[] = 'dbname=' . $dbname;
        $dsn[] = 'charset=UTF8';
        $dsn = strtolower(str_ireplace(array('ORACLE','MSSQL','FREETDS'),array('OCI','SQLSRV','DBLIB'),$driver)) . ':' . implode(';', $dsn);
        $db_connect[] = array ('driver' => $driver, 'dbname' => $dbname, 'user' => $user, 'pass' => $pass, 'host' => $host, 'port' => $port, 'dsn' => $dsn, 'connect' => new PDO($dsn, $user, $pass, $param) );
        $GLOBALS['db_use'] =& $db_connect[max(array_keys($db_connect))];
    }
}
function com_disconnect(){
    global $db_use;
    $db_use['connect'] = NULL;
}
function com_use($use = '', $host = '', $port = 0){
    if(is_int($use)) {
        $GLOBALS['gd_use'] =& $gd_connect[$use];
        return true;
    }
    global $db_connect, $db_use;
    if(is_int($host)) { $port = $host; $host = '';}
    foreach($db_connect as $key => $db) {
        if($port == 0) {
            if($host == '') {
                if($use == $db['dbname']){
                    $GLOBALS['db_use'] =& $db_connect[$key];
                    return true;
                }
            } else {
                if($use == $db['dbname'] && $host == $db['host']){
                    $GLOBALS['db_use'] =& $db_connect[$key];
                    return true;
                }
            }
        } else {
            if($host == '') {
                if($use == $db['dbname'] && $port == $db['port']){
                    $GLOBALS['db_use'] =& $db_connect[$key];
                    return true;
                }
            } else {
                if($use == $db['dbname'] && $host == $db['host'] && $port == $db['port']){
                    $GLOBALS['db_use'] =& $db_connect[$key];
                    return true;
                }
            }            
        }
    }
    $db_use = false;
    return false;
}
function com_QUERY($sql = '', $param = false){
    global $db_use;
    $ret = array();
    foreach ($db_use['connect']->query($sql) as $row) $ret[] = $row;
    if( !$param ) return $ret;
    if (count($ret) == 1) $ret = array_shift($ret);
    if (count($ret) == 1) $ret = array_shift($ret);
    if (empty($ret)) $ret = null;
    return $ret;
}
function com_QUOTE($vars = ''){
    global $db_use;
    if( is_array($vars) ) {
        $ret = array();
        foreach ($vars as $var) if(is_numeric($var)) $ret[] = $var;
            elseif(is_bool($var)) $ret[] = $var ? 'TRUE' : 'FALSE'; 
            else $ret[] = $db_use['connect']->quote($var);
        return implode(',', $ret);
    } elseif(is_numeric($vars)) return $var;
    elseif(is_bool($vars)) return $vars ? 'TRUE' : 'FALSE'; 
    else return $db_use['connect']->quote($vars);
}
function com_LASTINSERID($name = NULL){
    global $db_use;
    if( is_null ($name) ) return $db_use['connect']->lastInsertId(); 
    return $db_use['connect']->lastInsertId($name); 
}

/*
    Графика
*/

function com_screen($id = NULL, $file = NULL, $to = NULL, $quality = NULL, $x = NULL, $y = NULL){
    global $gd_connect, $gd_use;

    if(is_null($file) && (is_null($x) || is_null($y))) {  // буфер можно только выбрать
        if(!is_null($id)) $gd_use = $id;
    } else {
        if(is_null($id)) {
            $gd_connect[] = false; 
            $gd_use = max(array_keys($gd_connect));

        } else $gd_use = $id;
    }
    if(is_null($file)) {
        if(!is_null($x) && !is_null($y)) $gd_connect[$gd_use] = imagecreatetruecolor($x, $y);
    } else {
        if(is_null( parse_url( $file, PHP_URL_SCHEME ) )) $file = com_REALPATH($file);
        if($file === false) return false;
        switch (strtoupper( pathinfo($file, PATHINFO_EXTENSION ) )) { 
            case 'GIF' : $gd_connect[$gd_use] = imageCreateFromGif($file); break; 
            case 'JPG': case 'JPEG': $gd_connect[$gd_use] = imageCreateFromJpeg($file); break; 
            case 'PNG' : $gd_connect[$gd_use] = imageCreateFromPng($file); break; 
            case 'BMP' : $gd_connect[$gd_use] = imageCreateFromBmp($file); break; 
            case 'WEBP' : $gd_connect[$gd_use] = imageCreateFromBmp($file); break; 
        }
    }
    if(!is_null($to)) {
        if( is_int($to) ) {
            $tofile = NULL;
            $content = array(1 => 'jpg', 2 => 'gif', 3=> 'png', 4 => 'webp', 5 => 'bmp');
            header('Content-Type: image/' . $content[$to] );
        } else {
            $tofile = com_REALPATH($to);
            if($tofile === false) return false;
            $to = strtoupper( pathinfo($to, PATHINFO_EXTENSION ) );
        }    
        switch ($to) { 
            case 'JPG': case 'JPEG': case 1: imagejpeg($gd_connect[$gd_use], $tofile, $quality == NULL ? 75 : $quality); break; 
            case 'GIF': case 2: imagegif($gd_connect[$gd_use], $tofile); break; 
            case 'PNG': case 3: imagepng($gd_connect[$gd_use], $tofile, $quality == NULL ? 6 : 9 - ($quality < 10 ? 0 : $quality / 10 - 1)); break; 
            case 'WEBP': case 4: imagewebp($gd_connect[$gd_use], $tofile, $quality == NULL ? 75 : $quality); break;             
            case 'BMP': case 5: imagebmp($gd_connect[$gd_use], $tofile); break; 
        }
    }
}
function com_COLOR($color = NULL) {
    global $gd_connect, $gd_use, $gd_color;
    if(is_string($color)) {
        if ($color[0] == '#') $color = substr($color, 1);
        if (strlen($color) == 6) { 
            $gd_color = imagecolorallocate($gd_connect[$gd_use], hexdec( $color[0] . $color[1] ), hexdec( $color[2] . $color[3] ), hexdec( $color[4] . $color[5] ) );
        } elseif (strlen($color) == 3) { 
            $gd_color = imagecolorallocate($gd_connect[$gd_use], hexdec( $color[0] . $color[0] ), hexdec( $color[1] . $color[1] ), hexdec( $color[2] . $color[2] ) );
        }
        return $gd_color;
    } elseif(is_array($color)) {
        if (count($color) == 4) { 
            $gd_color = imagecolorallocatealpha($gd_connect[$gd_use], $color[0], $color[1], $color[2], round (127 * $color[2]) );
        } elseif (count($color) == 3) { 
            $gd_color = imagecolorallocate($gd_connect[$gd_use], $color[0], $color[1], $color[2]);
        }
        return $gd_color;
    } elseif(is_int($color)) {
        $gd_color = $color;
        return $gd_color;
    } elseif(is_null($color)) {
        return $gd_color;
    } 
    return false;
}

function com_PSET($x, $y, $color = NULL, $step = false) {
    global $gd_connect, $gd_use, $gd_color, $gd_coord;
    $gd_coord = array($x, $y);
    if(!is_null($color)) $gd_color = com_COLOR($color);
    imagesetpixel($gd_connect[$gd_use], $x, $y, $gd_color );
    return $gd_color;    
}

function com_POINT($x, $y, $step = false) {
    global $gd_connect, $gd_use, $gd_color, $gd_coord;
    $gd_coord = array($x, $y);
    $gd_color = imagecolorat($gd_connect[$gd_use], $x, $y);
    return $gd_color;
}

function com_line($coord, $color = NULL, $mode = 0) {
    global $gd_connect, $gd_use, $gd_color, $gd_coord;
    if(!is_null($color)) $gd_color = com_COLOR($color);
    if(count($coord) == 2) {
      $coord[] = $gd_coord[0];
      $coord[] = $gd_coord[1];
    }
    if(count($coord) == 4) {
        if( $mode == 0) imageline($gd_connect[$gd_use], $coord[0], $coord[1], $coord[2], $coord[3], $gd_color);
        elseif( $mode == 1) imagerectangle($gd_connect[$gd_use], $coord[0], $coord[1], $coord[2], $coord[3], $gd_color);
        elseif( $mode == 2) imagefilledrectangle($gd_connect[$gd_use], $coord[0], $coord[1], $coord[2], $coord[3], $gd_color);
    } else {
        if( $mode == 0) for($i = 0; $i < count($coord)-2; $i += 2) imageline($gd_connect[$gd_use], $coord[0+$i], $coord[1+$i], $coord[2+$i], $coord[3+$i], $gd_color); // imageopenpolygon php>7.2? 8)
        elseif( $mode == 1) imagepolygon($gd_connect[$gd_use], $coord, count($coord)/2, $gd_color);
        elseif( $mode == 2) imagefilledpolygon ($gd_connect[$gd_use], $coord, count($coord)/2, $gd_color);
    }
}

function com_circle($x, $y, $radius = 0, $color = NULL, $start = 0, $end = 360, $aspect = 1, $mode = 0) {
    global $gd_connect, $gd_use, $gd_color, $gd_coord;
    if(!is_null($color)) $gd_color = com_COLOR($color);
    $gd_coord = array($x, $y);
    if( $mode == 0) imagearc($gd_connect[$gd_use], $x, $y, $radius * 2 * $aspect, $radius * 2 / $aspect, $start, $end, $gd_color);
    elseif( $mode == 1) imagefilledarc($gd_connect[$gd_use], $x, $y, $radius * 2 * $aspect, $radius * 2 / $aspect, $start, $end, $gd_color, IMG_ARC_EDGED | IMG_ARC_NOFILL);
    elseif( $mode == 2) imagefilledarc($gd_connect[$gd_use], $x, $y, $radius * 2 * $aspect, $radius * 2 / $aspect, $start, $end, $gd_color, IMG_ARC_EDGED);
}

function com_paint($x, $y, $color = NULL, $bordercolor = NULL) {
    global $gd_connect, $gd_use, $gd_color, $gd_coord;
    if(!is_null($bordercolor)) $bordercolor = com_COLOR($bordercolor);
    if(!is_null($color)) $gd_color = com_COLOR($color);
    $gd_coord = array($x, $y);
    if(is_null($bordercolor)) imagefill($gd_connect[$gd_use], $x, $y, $gd_color);
    else imagefilltoborder($gd_connect[$gd_use], $x, $y, $bordercolor, $gd_color);
}

function com_text($x, $y, $text = '', $color = NULL, $size = NULL, $angle = NULL, $font = NULL) {
    global $gd_connect, $gd_use, $gd_color, $gd_coord, $gd_size, $gd_angle, $gd_font;
    if(!is_null($color)) $gd_color = com_COLOR($color);
    if(!is_null($size)) $gd_size = $size; else $size = $gd_size;
    if(!is_null($angle)) $gd_angle = $angle; else $angle = $gd_angle;
    if(!is_null($font)) $gd_font = com_REALPATH($font); else $font = $gd_font;
    $gd_coord = array($x, $y);
    if(is_null($font)) imagestring($gd_connect[$gd_use], $gd_size, $x, $y, $text, $gd_color);
    else imagettftext($gd_connect[$gd_use], $gd_size, $gd_angle, $x, $y, $gd_color, $gd_font, $text);
}
