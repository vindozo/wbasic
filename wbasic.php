<?php
/* 
    Проект: WBASIC - Акроним Web Beginner's All-purpose Symbolic Instruction Code.
            Высокоуровневый язык программирования применяемый для разработки серверных веб-приложений.
            Философия языка - это должно быть просто для начинающих.
    Версия: 1.0 beta
    Начало разработки: 24.03.2017
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

$_WEB = array( // перепаковка массива $_SERVER, т.к. в нем очень много элементов с префиском "PHP_"
			'URI' => $_SERVER['SCRIPT_URI'], 'SCHEME' => $_SERVER['REQUEST_SCHEME'], 'HOST' => $_SERVER['HTTP_HOST'], 'URL' => $_SERVER['SCRIPT_URL'], 'SCRIPT' => $_SERVER['SCRIPT_NAME'],
			'SERVER' => $_SERVER['SERVER_SOFTWARE'], 'IP' => $_SERVER['REMOTE_ADDR'], 
			'BROWSER' => isset( $_SERVER['HTTP_USER_AGENT'] ) ? $_SERVER['HTTP_USER_AGENT'] : '',
			'LANGUAGE' => isset( $_SERVER['HTTP_ACCEPT_LANGUAGE'] ) ? $_SERVER['HTTP_ACCEPT_LANGUAGE'] : '',
			'REFERER' => isset( $_SERVER['HTTP_REFERER'] ) ? $_SERVER['HTTP_REFERER'] : '',            
			'AUTH' => isset( $_SERVER['PHP_AUTH_DIGEST'] ) ? $_SERVER['PHP_AUTH_DIGEST'] : '', 
			'LOGIN' => isset( $_SERVER['PHP_AUTH_USER'] )? $_SERVER['PHP_AUTH_USER'] : '', 
			'PASSWORD' => isset( $_SERVER['PHP_AUTH_PW'] )? $_SERVER['PHP_AUTH_PW'] : '',
			'REQUEST_TIME' => $_SERVER['REQUEST_TIME_FLOAT'], 
		);
$is_session_start = false; // признак старта сессии
$def_key = array(); // массив предопределенных функций. все остальное переменные
$script_filename = isset($_SERVER['REDIRECT_SCRIPT_URL']) ? $_SERVER['REDIRECT_SCRIPT_URL'] : '/index.bas' ;
$include_path = __DIR__; // базовая директория, откуда ищутся все файлы
$script_file = realpath( $include_path . $script_filename );
header('X-Powered-By: WBASIC/1.0');
$script_line = 0;
$error_log = '';
$error_debug_mode = false; // режим отладки
$debug_for_client = ''; // для какого клиента показывать (IP или COOKIE)
$error_bas_code = array();
$error_php_code = '';
$debug_time_code = array();
$debug_vars = array();

register_shutdown_function('shutdown');
ob_start();
if ( $script_file === false ) {
    $code = '';
    error( 'FILEOPEN', 'No script file' );
} else $code = run();

$error_php_code = $code;
eval($code);

function shutdown() { 
// конец обработки файла или возникновение ошибок компиляции.
	global $error_log, $error_debug_mode, $script_filename, $script_line, $error_php_code, $error_bas_code, $debug_time_code, $debug_vars;
	// а произошла ли ошибка компиляции?
	$error =  error_get_last();
    if( ($error['type'] & 341) !== 0 ) $bg_color_error = 'debug_fatal'; else $bg_color_error = 'debug_warning';
	if( !is_null( $error ) ) {
		$script_line = $error['line'] - 1;
		$types = array_flip( array_slice( get_defined_constants(true)['Core'], 1, 15, true ) );
		error ( $types[$error['type']].' (OUTFILE)', $error['message'] ); 
	} else $bg_color_error = 'debug_noerror';
    // но мы все равно попытаемся вывести все что есть
	$out = ob_get_contents();
	ob_end_clean();
	// вначале высветим ошибки, если это разрешено
	if( $error_debug_mode ) { echo '
<!DOCTYPE html>
<html lang="ru-RU">
<head>
    <meta charset="UTF-8" />
    <title>Debug mode</title>
</head>
<style>
    body.debug_error {margin:0; padding:0;background: #555;}
    .debug_error {padding:5px 10px;margin:0 0 0 200px;font:12px monospace; min-height:60px}
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
    .debug_code_bas{margin-top:5px}
</style>
<script>
debug = {}
debug.hide = function(id){
    var e = document.querySelector(id);
    if(/debug_code_hide/.test(e.className)){ e.className = e.className.replace(/\sdebug_code_hide/,"") } else { e.className = e.className+" debug_code_hide"}
    return false;
}
</script>
<body class=debug_error>
    <div class=' . $bg_color_error . '>
        <div class=debug_title>
            <b>DEBUG MODE ON</b><br>SHOW FOR IP: '.$_SERVER['REMOTE_ADDR'].'<br>GENERATE TIME: '.number_format ( microtime(true) - $_SERVER['REQUEST_TIME_FLOAT'] , 6 , '.' , ' ' ).' sec.
        </div>
        <a href="#debug-vars" class="debug_file debug_file_vars" onclick=\'debug.hide("#debug_code_vars")\'><i>A=</i>STOPVARS</a>
        <a href="#debug-time" class="debug_file debug_file_time" onclick=\'debug.hide("#debug_timelog")\'><i>0:0</i>TIMELOG</a>
        <a href="#" class="debug_file debug_file_bas" onclick=\'debug.hide(".debug_code")\' ><i>BAS</i>CODEFILE</a>
        <a href="#debug-php" class="debug_file" onclick=\'debug.hide("#debug_code_php")\' ><i>PHP</i>OUTFILE</a>
        <pre class=debug_error >' . ($error_log == '' ? "\r\n\r\nNO ERRORS.\r\nEXCELLENT BASIC CODE!" : $error_log) . '</pre>
    </div>';
    // код на бейсике
    foreach( $error_bas_code as $script_file => $bas_code_file){
        echo '<div class="debug_code debug_code_bas debug_code_hide" id="debug_code_'.md5($script_file).'"><b><a href="#" onclick=\'return debug.hide("#debug_code_'.md5($script_file).'")\' >—</a> ' . $script_file . '</b><ul class=debug_code><li type="1">'.implode('</li><li type="1">', $bas_code_file).'</li></ul></div>';
    }        
    // все что откомпилировалось, для извращенцев
    echo '<a name="debug-php"></a><div class="debug_code debug_code_hide" id="debug_code_php"><b><a href="#" onclick=\'return debug.hide("#debug_code_php")\'>—</a> OUTFILE</b><ul class=debug_code>';

    foreach( explode(PHP_EOL, $error_php_code) as $php_code_file){
        $php_code_file = explode('/*debug_timer*/', $php_code_file);
        echo '<li type="1">';
        $php_code_file_even = true;
        foreach($php_code_file as $php_code_string) {
            if($php_code_file_even) echo $php_code_string;
            $php_code_file_even = !$php_code_file_even;
        }
        echo '</li>'.PHP_EOL;
    }    
    echo '</ul></div>';
    // таймлог для оптимизаторов
    echo '<a name="debug-time"></a><div class="debug_code debug_timelog debug_code_hide" id="debug_timelog"><b><a href="#" onclick=\'return debug.hide("#debug_timelog")\' >—</a> TIMELOG</b><ul class=debug_code>';
    $first_time = 0;
    foreach( $debug_time_code as $bas_code_file){
        // выделим время исполнения кода 
        $bas_code_file = explode (':', $bas_code_file); 
        $debug_time = array_shift($bas_code_file); 
        if($first_time == 0) $first_time = $debug_time; // для сброса времени в ноль на первой строке
        $debug_time = number_format($debug_time - $first_time, 6); 
        $bas_code_file = implode(':', $bas_code_file);
        echo '<li type="1"><span class=debug_timer>'.$debug_time.' sec.</span>'.$bas_code_file.'</li>';
    }  
    echo '</ul></div>';
    // все переменки переданные в STOP
    echo '<a name="debug-vars"></a><div class="debug_code debug_timelog debug_code_hide" id="debug_code_vars"><b><a href="#" onclick=\'return debug.hide("#debug_code_vars")\'>—</a> STOPVARS</b><ul class=debug_code>';

    foreach( $debug_vars as $debug_var_name => $debug_var){
        echo '<li style="list-style-type:none"><span class=debug_timer title = "' . htmlentities($debug_var_name, ENT_QUOTES) . '">'.(strlen($debug_var_name) > 20 ? substr($debug_var_name, 0, 10) . '..' . substr($debug_var_name, -10) : $debug_var_name) .'</span>';
        echo json_encode($debug_var, 0777);
        echo '</li>'.PHP_EOL;
    }    
    echo '</ul></div>';

	// затем все что выводилось в браузер
	if($out !== '') echo '<a name="debug-print"></a><div class="debug_code" id="debug_print"><b><a href="#" onclick=\'return debug.hide("#debug_print")\'>—</a> PRINT</b><div class=debug_print>'.$out.'</div></div>';
    // затем все остальное
    echo '
</body>
</html>
';
    } else echo $out;
}

function error ($type, $message){
// ведем лог ошибок - его внешний вид задается здесь
	global $error_log, $script_filename, $script_line ;
	$error_log .= 'ERROR: ' . $type . PHP_EOL . "MESSAGE: " .$message . PHP_EOL . "FILE: " . $script_filename . PHP_EOL . "LINE: " . ( $script_line + 1 ) . PHP_EOL;
}

function run () {
// запуск скрипта на выполнение
	global $error_debug_mode, $include_path, $script_file, $script_line, $def_key, $error_bas_code, $debug_for_client;
	// читаем файл, удаляя переводы строк. пустые строки не игнорируем, чтобы правильно указывать номер строки для ошибок синтаксиса
	$bas_code_file = file( $script_file );
	$error_bas_code[str_replace($include_path, '', $script_file)] = $bas_code_file;
	$php_code_file = ''; // поскольку это компилятор, здесь будет накапливаться выходной код на php
    $long_buffer = ''; // буфер переноса строки.
    $long_string_begin = false; $long_string_continue = false; // признак переноса длинной строки.
	// обход строк, если файл не пустой. поскольку счет строк встроен в цикл, удаляем слева и справа пробелы, табуляцию, переводы строк и пропускаем пустые строки
	if( count( $bas_code_file ) > 0 ) foreach( $bas_code_file as $script_line => $bas_code_string ) if( ($bas_code_string = trim($bas_code_string)) != '' ){
        if($long_string_begin) { // строка только начата, а уже есть признак длинной строки
            $long_string_continue = true; $long_string_begin = false;
        } elseif(substr($bas_code_string, -1) == '"' && $bas_code_string !== '"'){ // возможно, это признак длинной строки
            if(((substr_count ($bas_code_string, '"') - substr_count ($bas_code_string, '\"') + substr_count ($bas_code_string, '\\\"')) % 2) !== 0){
               $long_string_begin = true; // получилось нечетное значение кавычек в строке с учетом экранирования - да, это длинная строка
            }
        }
        if($long_string_continue){
            if( $bas_code_string == '"' ) { // длинная строка завершена
                $long_string_continue = false;
                $php_code_file .= 'EOT;' . PHP_EOL ;
            } else $php_code_file .= $bas_code_file[$script_line];
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
			$label_code_string = (is_numeric($array_math[1]) ? '_'.$array_math[1] : varname_transliterate( $array_math[1] )) . ': ' ; // и преобразовали метку в латинцу и вставили в выходной код
		}
		
		// проход второй - лексика.
		// Лексический анализатор - разбиваем строку на атомарный массив констант, переменных и операторов.
		$multiple_commands = array(array()); // в одной строке может быть несколько комманд, разделенных {;} вне кавычек, здесь будут отрезки кода
		$multiple_commands_count = 0;
		$open_parenthesis = 0; $close_parenthesis = 0; // для подсчета ()[] в строке
		// делим строку на отрезки текста в кавычках и без, т.к. текст внутри кавычек не интерпретируется
		preg_match_all('/"((?<=\\\)"|[^"])*"|([^"]+)/', $bas_code_string, $array_math, PREG_SET_ORDER);
		if( count( $array_math ) > 0 ) foreach( $array_math as $array_math_strings ) {
			if( count($array_math_strings) > 2) { // найден отрезок вне кавычек, его надо разобрать
				$rem_half = explode( "'", $array_math_strings[0] ); // разделим строку на до и после коментария
				foreach ( explode( ';', $rem_half[0] ) as $multiple_level => $multiple_command) { 
					if( $multiple_level > 0) {
						$multiple_commands_count++; // есть еще уровень (новая команда в строке), добавим счетчик команд в строке
						//проверим сложились ли скобки до начала новой команды
						if($open_parenthesis !== $close_parenthesis) error ( 'WHAT?', 'how many "(" <-' . $open_parenthesis . ' ? ' . $close_parenthesis . '-> ")"' ); 
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
		if($open_parenthesis !== $close_parenthesis) error ( 'WHAT?', 'how many "(" <-' . $open_parenthesis . ' ? ' . $close_parenthesis . '-> ")"' ); 
		
	//	var_export($multiple_commands);
		// проход третий - парсер.
		// парсер атомарного массива разбирает входящий поток и формирует выходной код.
		foreach( $multiple_commands as $array_atoms ) {
            $tree_command = array( 0 => array ( // Ветви комманд для if then .. else .., for
                'php_code_string' => '', // массив выходного кода - каждому типу команд своя ветка строк
                'command' => '', // команда - определяется по первому атому
                'var_eq' => '' // переменная до знака равенства или имя функции
            ));        
            $tree_counter = 0; // Счетчик ветвей
            $if_atom_value = ''; $if_atom_num = 0; $if_code_string = ''; 
            $else_atom_value = ''; $else_atom_num = 0; $else_code_string = '';
            $for_num = 0; $do_while = true; $my_script_file = ''; 
			if( count($array_atoms) > 0 ) foreach( $array_atoms as $atom_num => $array_atom) { // $atom_num - порядковый номер
				foreach( $array_atom as $atom_type => $atom_value) break; // $atom_type - тип и $atom_value значение атома
                $atom_value_upper = strtoupper($atom_value);
                // строчная форма команды IF
                if($atom_type == 'CONST$' && $atom_value_upper == 'THEN') {
                    $if_atom_num = $atom_num + 1;
                    $if_atom_value = $atom_value;
                    $if_code_string = $tree_command[$tree_counter]['php_code_string'];
                    $tree_command[$tree_counter]['php_code_string'] = '';
                    continue;
                }
                if($atom_type == 'CONST$' && $atom_value_upper == 'ELSE') {
                    $else_atom_num = $atom_num + 1;
                    $else_atom_value = $atom_value;
                    $else_code_string = $tree_command[$tree_counter]['php_code_string'];
                    $tree_command[] = array ( 'php_code_string' => '', 'command' => '', 'var_eq' => '' );
                    $tree_counter++;
                    continue;
                }
                // циклы
                if($atom_type == 'CONST$' && ( $atom_value_upper == 'FOR' || $atom_value_upper == 'FOREACH')) {
                    $for_num = $atom_num + 1;
                    continue;
                }    
                // разбивка команды на части
                if($atom_type == 'CONST$' && in_array($atom_value_upper, array('TO', 'STEP', 'IN', 'AS' ))) {
                    $tree_command[] = array ( 'php_code_string' => '', 'command' => strtoupper($atom_value), 'var_eq' => '' );
                    $tree_counter++;
                    continue;
                }
                if($atom_type == 'CONST$' && in_array($atom_value_upper, array('WHILE', 'UNTIL' ))) {
                    if($atom_value_upper == 'UNTIL' ) $do_while = false;
                    continue;
                }
                $atom_num = $atom_num - ($else_atom_num > 0 ? $else_atom_num : $if_atom_num ) - $for_num;
				if( $atom_num == 0 && ($atom_type == 'CONST$' || $atom_type == 'CHAR')) { // выделим и определим команду
					$atom_value = strtoupper($atom_value);
					switch($atom_value) {
						case 'REM'; //коментарии
						case '?'; case 'PRINT'; case 'INPUT';case 'OUTPUT'; //вывод и ввод данных
						case 'LET'; case 'DECLARE'; //управление переменными
						case 'GOTO'; case 'GOSUB'; case 'SUB'; case 'RETURN'; case 'END'; //переходы и функции
						case 'IF'; case 'ELSE'; case 'ELSEIF'; //условие
						case 'FOR';	case 'NEXT'; case 'EXIT'; case 'CONTINUE'; //циклы
                        case 'DO';	case 'LOOP'; //циклы
						case 'INCLUDE';	case 'DEBUG'; case 'STOP'; //команды компилятору
							$tree_command[$tree_counter]['command'] = $atom_value;
						break;
					default;
						 $tree_command[$tree_counter]['command'] = '=VAR:'; // видимо присвоение переменной
					break;
					}
				} elseif($atom_num == 0 && substr($atom_type, 0 , 3 ) == 'VAR') $tree_command[$tree_counter]['command'] = '=' . $atom_type; // видимо присвоение переменной с типом
				if( $atom_type == 'CHAR' && $atom_num > 0 ) {
                        if($atom_value == '{') $tree_command[$tree_counter]['php_code_string'] .= ' array( ';
                        elseif($atom_value == '}') $tree_command[$tree_counter]['php_code_string'] .= ' ) ';
                        elseif($atom_value == ':') $tree_command[$tree_counter]['php_code_string'] .= ' => ';
						else $tree_command[$tree_counter]['php_code_string'] .=  $atom_value == '&' ? '.' : $atom_value; // складываем строки как VB через &
				}
				if( $atom_type == 'CONST#') {
						$tree_command[$tree_counter]['php_code_string'] .=  $atom_value;
				}
				if( $atom_type == 'CONST$') {
						if( ( substr($tree_command[$tree_counter]['command'], 0 , 1 ) == '=' || $tree_command[$tree_counter]['command'] == 'SUB' ) && $atom_num < 2 ){
                            $tree_command[$tree_counter]['var_eq'] = varname_transliterate( $atom_value );
                            if( !in_array($tree_command[$tree_counter]['var_eq'], $def_key) ) $tree_command[$tree_counter]['var_eq'] = '$'. $tree_command[$tree_counter]['var_eq'];
                            else { // это функция и она есть в массиве ключевых слов
                                $key = array_search($tree_command[$tree_counter]['var_eq'], $def_key); // получим ее ключ
                                if(!is_int($key)) $tree_command[$tree_counter]['var_eq'] = $key; // если ключ не просто индекс, то там эквивалент функции из DECLARE, перешьем имя
                            }
						}
						if($atom_num > 0) {
							$var = varname_transliterate( $atom_value );
							if($tree_command[$tree_counter]['command'] == 'SUB' && $atom_num == 1){
								if( in_array($var, $def_key) ) error ( 'WHAT?', 'Redefine ' . $tree_command[$tree_counter]['command'] . $var ); 
								else {
									$tree_command[$tree_counter]['php_code_string'] .= $var;
									$def_key[] = $var;
								}
							} else {
								if( in_array($var, $def_key) ) { // это функция и она есть в массиве ключевых слов
                                    $key = array_search($var, $def_key); // получим ее ключ
                                    if(!is_int($key)) $tree_command[$tree_counter]['php_code_string'] .= $key; // если ключ не просто индекс, то там эквивалент функции из DECLARE, перешьем имя
                                    else $tree_command[$tree_counter]['php_code_string'] .= $var;  
                                } else $tree_command[$tree_counter]['php_code_string'] .= 
                                ( !in_array($var, array('TRUE', 'FALSE', 'NULL', 'AND', 'OR', 'NOT', 'XOR', 'ARRAY', 'IIF')) ? '$' : '') . // это операторы а не переменые
                                ( $var == 'NOT' ? ' ! ' : ( $var == 'ARRAY'  ? ' varname_change_key' : ( in_array($var, array('TRUE', 'FALSE', 'NULL', 'AND', 'OR', 'XOR')) ? ' ' . $var . ' ' : $var ) ) ); // и они не должны слипаться с цифрами
							}
						}
				}
				if( substr($atom_type, 0, 3) == 'VAR') {
					if( substr($tree_command[$tree_counter]['command'], 0 , 1 ) == '=' && $atom_num < 2 )
						$tree_command[$tree_counter]['var_eq'] = '$' . varname_transliterate(substr( $atom_value, 0, strlen($atom_value) - 1));
					else 
						$tree_command[$tree_counter]['php_code_string'] .= str_replace( array( '$', '#', '%', '&', '@'), array('(string)', '(int)', '(float)', '(boolean)', '(array)'), substr( $atom_value, -1) ) // преобразователи типа
						. '$' . varname_transliterate(substr( $atom_value, 0, strlen($atom_value) - 1)); // + знак $ + имя переменной
				}
				if( $atom_type == 'STRING') {
						$tree_command[$tree_counter]['php_code_string'] .= $atom_value;
				}	
			}
            // отработка команд
            // односекционные команды
            foreach($tree_command as $three_key => $three_item) {
                $php_code_string = &$tree_command[$three_key]['php_code_string'];
                $command = &$tree_command[$three_key]['command'];
                $var_eq = &$tree_command[$three_key]['var_eq'];
            
                if($command == 'REM') $php_code_string = ''; 
                if($command == 'PRINT' || $command == '?') $php_code_string = 'echo ' . $php_code_string ;
                if($command == 'INPUT') {
                    $vars = explode (',', $php_code_string);
                    if( strpos( $vars[0] , '"') !== false ) $var_source = array_shift ($vars);
                    else $var_source = '"GET"';
                    $php_code_string = 'list(' . str_replace( array('(string)', '(int)', '(float)', '(boolean)', '(array)'), '', implode(',', $vars)) . ')=com_input(' . $var_source . ",array('" . implode("','", $vars) . "'))";
                } 
                if($command == 'OUTPUT') {
                    $vars = explode (',', $php_code_string);
                    if( strpos( $vars[0] , '"') !== false ) $var_source = array_shift ($vars);
                    else $var_source = '"SESSION"';
                    $php_code_string = 'com_output(' . $var_source . ($var_source != 'SESSION' ? ",array('" . implode("','", $vars) . "')," : 'array(),' ) .'array('. implode(',', $vars) . "))";
                }
                if(substr($command, 0 , 1 ) == '=' ) {
                    $php_code_string =  $var_eq  
                                        . ( substr($php_code_string, 0, 1) != '=' ? '' : '=' ) 
                                        . ( str_replace( array( '$', '#', '%', '&', '@', ':' ), array('(string)(', '(int)(', '(float)(', '(boolean)(', '(array)(', ''), substr( $command, -1) ) ) 
                                        . ( substr($php_code_string, 0, 1) != '=' ? $php_code_string : substr($php_code_string, 1) )
                                        . (substr( $command, -1) == ':' ? '' : ')');
                } 
                if($command == 'LET') {
                    if ( strpos( $php_code_string , '=') !== false ) $php_code_string = 'static ' . $var_eq  .  $php_code_string;
                    else $php_code_string = 'global ' .  $php_code_string;
                } 

                if($command == 'SUB') $php_code_string = 'function ' . ( strpos( $php_code_string , '(' ) === false ? $php_code_string . '()' : $php_code_string ). '{' . $var_eq . '=func_get_args()' .
                    ( $error_debug_mode ? '/*debug_timer*/; $GLOBALS[\'debug_time_code\'][] = (microtime(true)-$_SERVER[\'REQUEST_TIME_FLOAT\']).":'. htmlentities($bas_code_string, ENT_QUOTES) . '";/*debug_timer*/ ' : '') ;
                if($command == 'END') { // универсальный конец
                    if($php_code_string != '' ) $php_code_string = 'return '. $php_code_string .'; }'; // специальный конец для SUB
                    else $php_code_string = '}' ;
                }
                if($command == 'GOSUB') { // изменяет строку кода так, чтобы можно было не декларировать подпрограммы заранее
                    $php_code_string = substr( $php_code_string, 1 ) . ( strpos( $php_code_string , '(' ) === false ? '()' : '');
                }
                if($command == 'RETURN') {
                    $php_code_string = 'return ' . $php_code_string;
                }
                if($command == 'DEBUG') {
                    com_debug(trim( $php_code_string, '"'));
                    $php_code_string = '';
                }    
                if($command === 'STOP') {
                    $vars = explode (',', $php_code_string);
                    $php_code_string = 'com_die(array(' . implode(",", $vars) . "),array('" . implode("','", $vars) . "'))";
                }
                if($command == 'INCLUDE') {
                    $my_script_file = $script_file; $my_script_line = $script_line; // сохраним указатели компилятора
                    $script_filename = trim( $php_code_string, '"');
                    $script_file = realpath( $include_path . $script_filename );
                    if ( $script_file === false || substr( $script_file, 0, strlen($include_path) ) !== $include_path) { // ошибка, файл не найден
                        $script_file = $my_script_file;
                        error( 'FILEOPEN', 'No script file' );
                    } else {
                        $php_code_file .= $error_debug_mode ? ' /*debug_timer*/ $GLOBALS[\'debug_time_code\'][] = (microtime(true)-$_SERVER[\'REQUEST_TIME_FLOAT\']).":'. htmlentities($bas_code_string, ENT_QUOTES) . '";/*debug_timer*/ ' : '';
                        $php_code_file .= run(); // рекурсивный вызов компилятора
                        $script_file = $my_script_file; $script_line = $my_script_line; // восстановим указатели компилятора
                        $php_code_string = ''; 
                    }    
                }                
                if($command == 'IF') {
                    $php_code_string = 'if(' . $php_code_string . '){'; // многострочная
                }
                if($command === 'ELSEIF') {
                    $php_code_string = '} elseif(' . $php_code_string.'){';
                }
                if($command === 'NEXT') {
                    $php_code_string = 'endfor';
                }
                if($command === 'EXIT') {
                    $php_code_string = 'break '.$php_code_string;
                }
                if($command === 'CONTINUE') {
                    $php_code_string = 'continue '.$php_code_string;
                }
                
                if($command === 'DO') {
                    if($php_code_string == '') $php_code_string = 'do { ';
                    else $php_code_string = 'while '.($do_while ? '('. $php_code_string .')' : '(!('. $php_code_string .'))').'{';
                }
                if($command === 'LOOP') {
                    if($php_code_string == '') $php_code_string = '}';
                    else $php_code_string = '} while '.($do_while ? '('. $php_code_string .')' : '(!('. $php_code_string .'))');
                }                
                unset($php_code_string, $command, $var_eq);
            
            }
//            var_export($tree_command);
            // многосекционные команды
            if($for_num > 0) { // была команда for или foreach
                $var_eq = $tree_command[0]['var_eq']; // изменяемая в цикле переменка
                $in = false; $to = ''; $step = '1'; // выделим из веток команд TO, STEP
                foreach($tree_command as $tree_item){
                    if($tree_item['command'] == 'IN') $in = $tree_item['php_code_string'];
                    if($tree_item['command'] == 'TO') $to = $tree_item['php_code_string'];
                    if($tree_item['command'] == 'STEP') $step = $tree_item['php_code_string'];
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
                // дополнительная обработка команды IF c несколькими ветками (IF команда многосекционная команда по дефолту)
                } elseif( count($tree_command) > 1 ) { // обнаружена вторая ветвь комманд, которую делает команда ELSE
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
				$php_code_file .= $label_code_string . ( $error_debug_mode && $my_script_file == '' ? ' /*debug_timer*/ $GLOBALS[\'debug_time_code\'][] = (microtime(true)-$_SERVER[\'REQUEST_TIME_FLOAT\']).":'. htmlentities($bas_code_string, ENT_QUOTES) . '";/*debug_timer*/ ' : '' ) . $php_code_string . ( $long_string_begin ? '<<<\'EOT\'' : ';' ) . PHP_EOL;
				$label_code_string = '';
			} 
		}
	}
	return $php_code_file;
}


// для использования кирилических имен в переменных, функциях и метках. непроизносимые символы заменены на {X}
// array_structure = false отключает преобразователи a.b => a['b']
function varname_transliterate($string, $array_structure = true) {
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

function varname_change_key($arr) {
	$ret = array();
    foreach ($arr as $key => $var) {
        $ret[varname_transliterate( str_replace(array('-', '.' ), '_', $key) , false) ] = is_array ($var) ? varname_change_key($var) : $var;
    }
    return $ret;
}

function com_debug($param){
    global $error_debug_mode, $debug_for_client;
    $debug_for_client = $param;
    if( filter_var ($debug_for_client, FILTER_VALIDATE_IP) === false ) { // COOKIE
        if( isset($_COOKIE[$debug_for_client]) ) $error_debug_mode = true;
    } else { // IP
        if( $debug_for_client == $_SERVER['REMOTE_ADDR'] ) $error_debug_mode = true;
    }
}

// чтение переменок из окружения вебсервера
function com_input($source, $vars) {
	$arr = array();
	$source = strtoupper( trim($source, '"' ) ); // определим источник данных и загрузим его массив
	switch($source) {
		case 'GET';	$arr_source = varname_change_key ( $_GET, CASE_UPPER); break;
		case 'POST'; $arr_source = varname_change_key ( $_POST, CASE_UPPER); break;
		case 'COOKIE'; $arr_source = varname_change_key ( $_COOKIE, CASE_UPPER); break;
		case 'FILE'; $arr_source = varname_change_key ( $_FILE, CASE_UPPER); break;
		case 'SESSION'; 
            if( !$is_session_start ) { 
                session_start(); 
                $is_session_start = true; 
            };
            $arr_source = varname_change_key ( $_SESSION, CASE_UPPER); 
            break;
		case 'HEADER'; $arr_source = varname_change_key ( apache_request_headers(), CASE_UPPER); break;
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

function com_die( $vars, $names){
    global $debug_vars;
    foreach($names as $key => $var) {
        $var = explode('$', $var );
        if(isset($var[1])) $debug_vars[$var[1]] = $vars[$key];
        else $debug_vars[$var[0]] = $vars[$key];
    }
    exit;
}
function com_declare( $vars ){
    global $def_key;
    $def_key = array_merge ($def_key, $vars );
}
function IIF( $expression, $true_part = '', $false_part = ''){
    return $expression ? $true_part : $false_part;
}
