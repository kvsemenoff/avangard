<?php
/**
 * Основные параметры WordPress.
 *
 * Скрипт для создания wp-config.php использует этот файл в процессе
 * установки. Необязательно использовать веб-интерфейс, можно
 * скопировать файл в "wp-config.php" и заполнить значения вручную.
 *
 * Этот файл содержит следующие параметры:
 *
 * * Настройки MySQL
 * * Секретные ключи
 * * Префикс таблиц базы данных
 * * ABSPATH
 *
 * @link https://codex.wordpress.org/Editing_wp-config.php
 *
 * @package WordPress
 */

// ** Параметры MySQL: Эту информацию можно получить у вашего хостинг-провайдера ** //
/** Имя базы данных для WordPress */
define('DB_NAME', 'avangard5_admin');

/** Имя пользователя MySQL */
define('DB_USER', 'root');

/** Пароль к базе данных MySQL */
define('DB_PASSWORD', '');

/** Имя сервера MySQL */
define('DB_HOST', 'localhost');

/** Кодировка базы данных для создания таблиц. */
define('DB_CHARSET', 'utf8');

/** Схема сопоставления. Не меняйте, если не уверены. */
define('DB_COLLATE', '');


// define('DB_NAME', 'avangard5_admin');


// define('DB_USER', 'avangard5_admin');

// define('DB_PASSWORD', '_mxRmW6T');

// define('DB_HOST', 'avangard5.mysql');

// define('DB_CHARSET', 'utf8');

// define('DB_COLLATE', '');

/**#@+
 * Уникальные ключи и соли для аутентификации.
 *
 * Смените значение каждой константы на уникальную фразу.
 * Можно сгенерировать их с помощью {@link https://api.wordpress.org/secret-key/1.1/salt/ сервиса ключей на WordPress.org}
 * Можно изменить их, чтобы сделать существующие файлы cookies недействительными. Пользователям потребуется авторизоваться снова.
 *
 * @since 2.6.0
 */
define('AUTH_KEY',         '(fEi; c4yo7O+mW6-_/JZUTB.1Nw{LR9_K[~PB8}?{CE0Tu+8pP@-FSp<CE^Ic;M');
define('SECURE_AUTH_KEY',  'z@>.s/PDSZmyS82*L!Cw0v,+7*SAg&b?B>3tg<^10|i67lQL)Y3{6y<;@o#J}4%%');
define('LOGGED_IN_KEY',    '[{VsFYJ^bzX:obFsCK8$OMc%L*+%|;MlN_W*()t)>i.J<yz|0v)tAO{mhwQ!TKXn');
define('NONCE_KEY',        '{fxJ]HGb)VbdS+<oJ@R@ITg+8-Wkr.ffxf@cL9-dI]V7Ll$?8!)@~{M9_CihCq<V');
define('AUTH_SALT',        '(?V-gU$y-2Tyv&Nb1[aP,}21rjrc-tDl6M[e4GXRV.B={|y@+ckA<i?4,}$xY:FR');
define('SECURE_AUTH_SALT', 'sojC3x}d0wl1cmC|99ezRK!GdSHQ^<N+7FEfwYx|nk#j3g3y2]$qF<2SV$.{-!wY');
define('LOGGED_IN_SALT',   'BF3UR-S5a|lqh1[RH=t)o{ZYx-t^+wrjOCqC(BjEJs4HRxF9J1+5>)u<;.5G*9YX');
define('NONCE_SALT',       '4.+Fn>IMW^:?=pgb@_NLb~,d9Z4;xN7sXhrmsXt2W+#$MasgSQ+;MWC&h^t``A-k');

/**#@-*/

/**
 * Префикс таблиц в базе данных WordPress.
 *
 * Можно установить несколько сайтов в одну базу данных, если использовать
 * разные префиксы. Пожалуйста, указывайте только цифры, буквы и знак подчеркивания.
 */
$table_prefix  = 'wp_';

/**
 * Для разработчиков: Режим отладки WordPress.
 *
 * Измените это значение на true, чтобы включить отображение уведомлений при разработке.
 * Разработчикам плагинов и тем настоятельно рекомендуется использовать WP_DEBUG
 * в своём рабочем окружении.
 * 
 * Информацию о других отладочных константах можно найти в Кодексе.
 *
 * @link https://codex.wordpress.org/Debugging_in_WordPress
 */
define('WP_DEBUG', true);

/* Это всё, дальше не редактируем. Успехов! */

/** Абсолютный путь к директории WordPress. */
if ( !defined('ABSPATH') )
	define('ABSPATH', dirname(__FILE__) . '/');

/** Инициализирует переменные WordPress и подключает файлы. */
require_once(ABSPATH . 'wp-settings.php');
