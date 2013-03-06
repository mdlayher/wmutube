<?php //compile 0.12

	function compiledAutoloader($classname) {
		$cname = strtolower($classname);
		switch ($cname) {
			case substr($classname, 0, 3) == 'GF_' or substr($classname, 0, 5) == 'Zend_' or substr($classname, 0, 6) == 'ZendX_' :
				require str_replace('_', '/', $classname) . '.php';
				break;
		}
	}
	spl_autoload_register('compiledAutoloader');



//============ lib/defines.php =======================================================


/**
 * create some shortcut-defines for often-needed strings. after this you can include boot.php
 * @package kata_internal
 */





if (!defined('DS')) {
/**
 * shortcut for / or \ (depending on OS)
 */
	define('DS', DIRECTORY_SEPARATOR);
}
if (!defined('ROOT')) {
	/**
	 * absolute filesystem path to the root directory of this framework
	 */
	define('ROOT',dirname(dirname(__FILE__)).DS);
}
if (!defined('WWW_ROOT')) {
	/**
	 * absolute filesystem path to the webroot-directory of this framework
	 */
	define('WWW_ROOT', ROOT.'webroot'. DS);
}
if (!defined('LIB')) {
	/**
	 * absolute filesystem path to the lib-directory of this framework
	 */
	define('LIB',ROOT.'lib'.DS);
}

/**
 * @ignore
 */
if (php_sapi_name() == 'cli') {
	define('CLI',1);
} else {
	define('CLI',0);
}


/**
 * our error-level constants
 */
define('KATA_DEBUG', 2);
define('KATA_ERROR', 1);
define('KATA_PANIC', 0);

/**
 * some often used constants that should be part of PHP
 */
define('SECOND', 1);
define('MINUTE', 60 * SECOND);
define('HOUR', 60 * MINUTE);
define('DAY', 24 * HOUR);
define('WEEK', 7 * DAY);
define('MONTH', 30 * DAY);
define('YEAR', 365 * DAY);



//============ config/core.php =======================================================


/**
 * framework core configuration
 * @package kata
 */

/**
 * which debug-level to use: 
 * 3 = as 2, but turn off any caching used
 * 2 = show all errors, debug() output and timing for view rendering and query execution
 * 1 = show all errors and debug() output, but be silent otherwise
 * 0 = dont show any error or debug information
 * -1 = als 0, but dont even log KATA_DEBUG
 */
define('DEBUG',1);

/**
 * how many seconds the session-component should wait until it expires a session
 */
define('SESSION_TIMEOUT',3600);

/**
 * the name of the cookie that saves the session token in the uses browser
 */
define('SESSION_COOKIE','SID');

/**
 * which method to use for session storage. can currently be:
 * FILE (=normal filsystem php sessions)
 * CLIENT (=session-data resides as cookie on clients browser)
 * MEMCACHED (=use memcached for session)
 */
define('SESSION_STORAGE','MEMCACHED');

/**
 * salt to use to ensure no one is fiddeling with our session token.
 * is also used some components for having an uniqe caching-identifier
 */
define('SESSION_STRING','kataDefault');

/**
* If a user changes his ip, the session is destroyed (for security reasons)
* kata does an educated guess of the users ip, even if he uses a proxy (AOL,etc)
* If you don't want the session to be destroyed when the user changes ip
* you have to set SESSION_UNSAFE to true. (absolutely not recommended!)
* (http://de.wikipedia.org/wiki/Session_Fixation#des_Dienstanbieters)
*/
define('SESSION_UNSAFE', false);

/**
 * true  = session-cookie is set for the base domain ([*.]example.com)
 * false = session-cookie is set only for whatever subdomain we are 
           under (foo.bar.baz.example.com)
 */
define('SESSION_BASEDOMAIN',false);

/**
 * cache-identifier. prepended to any cache-id to ensure
 * we dont overwrite data from other kata-installations
 */
define('CACHE_IDENTIFIER','kataDefault');

/**
 * use this language for the locale-component. 
 * can be: 2-letter isocode of the language like "de" for germany
 *         VHOST to use the the top-level part or the third-level part of the domain to try to find a suitable language
 *         BROWSER to use the primary language of the users browser.
 * 		   NULL dont do anything automatically, select language yourself via setCode()
 */
define('LANGUAGE','en');

/**
  * set to true if you want your locale-strings auto-h()ed. default ist false.
  */
//define('LANGUAGE_ESCAPE',false);

/**
 * timezone to use, or a strict error will raise its ugly head
 */
define('TZ','America/Detroit');

 /**
 * change locale key behaviour
 * true: __('keyname',array(1,2,3)) when key is 'some text %s bla %s bla %s' (DONT USE!)
 * false: __('keyname',array('url'=>'bla.htm','title'=>'wow!')) when key is 'please visit <a href="%url%">%title%</a>'
 */
define('LANGUAGE_PRINTF',false);

//insert warn message into empty keys
//define('LANGUAGE_WARNEMPTY,1);
//fall back to english if key nonexistant or empty
//define('LANGUAGE_FALLBACK',1);

/**
 * set all available memcached-server
 * format is: ip:port,ip,ip,ip
 * 
 */
define('MEMCACHED_SERVERS','localhost');

/**
 * tell cache-utility not to autoselect caching-method, but use the given one.
 * see cacheUtility-doku for possible values
 */
//define('CACHE_USEMETHOD','file');

/* // routes. can rewrite the current url to a new one
$routes = array(
        'foocontroller/fooaction' => 'bla/blubb',
        'bla/foo.php' => 'foo/index/',
        '' => 'notmain/index' // make notmain new default controller
);
*/

//----------YOUR STUFF----------------------------------------------------------

/*** GF_SPECIFIC ***/
//spezial-hack f�r nicht abgesprochene gameinstaller-�nderung:
//define('KATATMP','#+#DATAPATH#+#');
/*** /GF_SPECIFIC ***/

//include(LIB.'boot_firephp.php'); // fb.php must be available in include path
//include(LIB.'boot_coverage.php');
//include(LIB.'boot_profile.php');
//include(LIB.'boot_strict.php');
//include(LIB.'boot_dbug.php'); // dbug.php must be available in include path



//============ lib/boot.php =======================================================


/**
 * includes everything needed to call the dispatcher and start the whole mvc machinery
 * @package kata_internal
 */




/**
 * include base config
 */
//require_once ROOT."config".DS."core.php";

/**
 * in case we dont use ignorant installer software: simply use kata's builtin tmp-dir
 */
if (!defined('KATATMP')) {
	define('KATATMP',ROOT.'tmp'.DS);
}
if (!defined('CACHE_IDENTIFIER')) {
   define('CACHE_IDENTIFIER','kataDef');
}

/**
 * needed for the updater
 */
define('KATAVERSION','1.4');

/**
 * set default encodings to utf-8 (you don't want to use anything less, anyway)
 */
mb_internal_encoding( 'UTF-8' );
mb_regex_encoding('UTF-8');
if (defined('TZ')) {
	date_default_timezone_set(TZ);
} else {
	date_default_timezone_set('Europe/Berlin');
}

/**
 * do we have to turn on error messages and asserts?
 */
if (DEBUG>0) {
	error_reporting(E_ALL);
	
	assert_options(ASSERT_ACTIVE,true);
	assert_options(ASSERT_WARNING,1);
	assert_options(ASSERT_QUIET_EVAL,0);
	
	ini_set('display_errors',1);
}

/**
 * include all neccessary files to start up the framework
 */
//require LIB.'class_registry.php';
//require LIB.'kata_functions.php';
//require LIB."basics.php";
//require LIB."dispatcher.php";



//============ config/database.php =======================================================


/**
 * all database configs used by kata. you can use a different database-connection per model.
 * 
 * driver: name of the database to use (or in other words: name of the dbo to use to access the database), selected by connection-property of the model
 * subdriver: name of the subdriver, to tell the dbo which driver you want (e.g. if you use PDO or ADODB. can be left empty for mysql and mssql)
 * host: where the database runs (normally localhost) 
 * login: user used to access the database
 * password: password used to access database
 * prefix: you can use a fixed prefix for all tables (but you have to obey model->getPrefix() if you write your own queries)
 * encoding: LEAVE EMPTY if all works well, ONLY set this to the characterset of the client if you encouter encoding problems! (MySQL only!!!)
 * 
 * @package kata
 */
class DATABASE_CONFIG
{
  public static $default = array(
	'driver' => 'mysql',
	'host' => 'localhost',
	'login' => 'root',
	'password' => 'root',
	'database' => 'test',
	'prefix' => '',
	'encoding' => ''
  );
}




//============ lib/dispatcher.php =======================================================




/**
 * Contains the dispatcher-class. Here is where it all starts.
 *
 * Kata - Lightweight MVC Framework <http://www.codeninja.de/>
 * Copyright 2007-2009 mnt@codeninja.de, gameforge ag
 *
 * Licensed under The GPL License
 * Redistributions of files must retain the above copyright notice.
 * @package kata_internal
 */

/**
 * dispatcher. this is the first thing that is constructed.
 * the dispatcher then collects all parameters given via get/post and instanciates the right controller
 * @package kata_internal
 */
class dispatcher {
	/**
	 * placeholer-array for all relevant variables a class may need later on (e.g. controller)
	 * [isAjax] => false (boolean, tells you if view got called with /ajax/)
	 * [url] => Array (
	 *       [url] => locations
	 *       [foo] => bar (if url read ?foo=bar)
	 * )
	 * [form] => Array (
	 * 	  (all post-variables, automatically dequoted if needed)
	 * )
	 * [controller] => main (name of the controller of this request)
	 * [action] => index (name of the view of this request)
	 * @var array
	 */
	public $params;

	/**
	 * name of the current controller
	 * @var string
	 */
	public $controller;

	/**
	 * time in us the dispather started (used to calculate how long the framework took to completely render anything)
	 */
	private $starttime;

	/**
	 * constructor, just initializes starttime
	 */
	function __construct() {
		$this->starttime = microtime(true);
	}

	/**
	 * destructor, outputs Total Render time if DEBUG>0
	 */
	function __destruct() {
		if (DEBUG > 0) {
			kataDebugOutput('Total Render Time (including Models) ' . (microtime(true) - $this->starttime) . ' secs');
			kataDebugOutput('Memory used ' . number_format(memory_get_usage(true)) . ' bytes');
			kataDebugOutput('Parameters ' . print_R($this->params, true));
			if (function_exists('xdebug_get_profiler_filename')) {
				$fn = xdebug_get_profiler_filename();
				if (false !== $fn) {
					kataDebugOutput('profilefile:' . $fn);
				}
			}
			kataDebugOutput('Loaded classes: ' . implode(' ', array_keys(classRegistry :: getLoadedClasses())));
		}
	}

	private $forbiddenActions = array(
		'dispatch'=>1,
		'render'=>1,
		'renderView'=>1,
		'renderCachedHtml'=>1,
		'redirect'=>1,
		'set'=>1,
		'setRef'=>1,
		'setPageTitle'=>1,
		'log'=>1,
	);

	/**
	 * start the actual mvc-machinery
	 * 1. constructs all needed params by calling constructParams
	 * 2. loads the controller
	 * 3. sets all needed variables of the controller
	 * 4. calls constructClasses of the controller, which in turn constructs all needed models and components
	 * 5. render the actual view and layout (if autoRender is true)
	 * 6. return the output
	 * @param string $url raw url string passed to the array (eg. /main/index/foo/bar)
	 */
	final function dispatch($url, $routes = null) {
		$this->constructParams($url, $routes);

		try {
			$lowername = strtolower($this->params['controller']);

			if ('app' == $lowername) {
				$this->fourohfour();
				return;
			}

			//cache-controller? handle internally
			if (isset($this->params['action'][0]) && ($this->params['action'][0]=='_') && (substr($this->params['action'],0,6) == '_cache')) {
				$this->handleCachedFiles($lowername, substr($this->params['action'], 7));
			}
			
			// load controller and check if action exists
			require_once LIB . 'controllers' . DS . 'app_controller.php';
			if (file_exists(ROOT . 'controllers' . DS . $lowername . '_controller.php')) {
				require_once ROOT . 'controllers' . DS . $lowername . '_controller.php';
			} else {
				if (file_exists(LIB . 'controllers' . DS . $lowername . '_controller.php')) {
					require_once LIB . 'controllers' . DS . $lowername . '_controller.php';
				} else {
					if (empty ($this->params['action'])) {
						$this->params['action'] = 'index';
					}

					$c = $this->constructController('AppController');
					$c->beforeAction();
					if ($c->before404()) {
						$this->fourohfour();
					}
					return '';
				}
			}

			$classname = ucfirst($lowername) . 'Controller';
			$c = $this->constructController($classname);

			if (!empty ($this->params['isAjax'])) {
				$c->layout = null;
			}

			$c->beforeAction();

			if (!is_callable(array (
					$c,
					$this->params['action']
					)) || ($this->params['action'][0] == '_')
					|| isset($this->forbiddenActions[$this->params['action']])) {
				if ($c->before404()) {
					$this->fourohfour();
					return;
				}
			} else {
				$c->dispatch();

				if ($c->autoRender) {
					$c->render($this->params['action']);
				}
			}
		} catch (Exception $e) {
			$basePath = $this->constructBasePath();
			if (file_exists(ROOT . "views" . DS . "layouts" . DS . "error.php")) {
				include ROOT . "views" . DS . "layouts" . DS . "error.php";
			} else {
				include LIB . "views" . DS . "layouts" . DS . "error.php";
			}
			return '';
		}

		return $c->output;
	}

	function fourohfour() {
		$basePath = $this->constructBasePath();
		if (file_exists(ROOT . "views" . DS . "layouts" . DS . "404.php")) {
			include ROOT . "views" . DS . "layouts" . DS . "404.php";
		} else {
			include LIB . "views" . DS . "layouts" . DS . "404.php";
		}
	}


	private function constructController($classname) {
			$c = new $classname;
			$this->controller = $c;

			if (empty ($this->params['action'])) {
				$this->params['action'] = $c->defaultAction;
			}
			$c->basePath = $this->constructBasePath();
			$c->base = $this->constructBaseUrl();
			$c->webroot = $c->base . 'webroot/';
			$c->params = & $this->params;
			$c->action = $this->params['action'];

			$c->_constructClasses();

			return $c;
	}

	/**
	 * extract,clean and dequote any given get/post-parameters
	 * find out which controller and view we should use
	 * @param string $url raw url (see dispatch())
	 */
	private function constructParams($url, $routes = null) {
		//do we have routes?
		if (!empty ($routes) && is_array($routes)) {
			krsort($routes);
			if (!empty($routes[$url])) {
				$url = $routes[$url];
			} else {
				foreach ($routes as $old => $new) {
//					if (($old != '') && ($old.'/' == substr($url, 0, strlen($old.'/')))) {
					if (($old != '') && ($old == substr($url, 0, strlen($old)))) {
						$url = $new . substr($url, strlen($old));
						break;
					}
				}//foreach
			}//!empty

			// does route-target have a query-string? parse it
			$x = strpos($url,'?');
			if (false !== $x) {
				$result = array();
				parse_str(substr($url,$x+1),$result);
				$_GET = array_merge($_GET,$result);
				$url = substr($url,0,$x-1);
			}
		}

		$paramList = explode('/', $url);
		while ((count($paramList)>0) && ('' == $paramList[count($paramList)-1])) {
			array_pop($paramList);
		}

		if (isset ($paramList[0]) && ($paramList[0]) == 'ajax') {
			array_shift($paramList);
			$this->params['isAjax'] = 1;
		} else {
			$this->params['isAjax'] = 0;
		}

		$controller = "main";
		if (isset ($paramList[0]) && !empty ($paramList[0])) {
			$controller = strtolower(array_shift($paramList));
		}

		$action = '';
		if (isset ($paramList[0]) && !empty ($paramList[0])) {
			$action = strtolower(array_shift($paramList));
		} else {
			if (isset ($paramList[0])) {
				unset ($paramList[0]);
			}
		}

		$this->params['pass'] = $paramList;

		$kataUrl = is($_GET['kata'], '');
		unset ($_GET['kata']);
		if (!empty ($_GET)) {
			if (ini_get('magic_quotes_gpc') == 1) {
				$this->params['url'] = stripslashes_deep($_GET);
			} else {
				$this->params['url'] = $_GET;
			}
		}
		$this->params['callUrl'] = $kataUrl;

		if (!empty ($_POST)) {
			if (ini_get('magic_quotes_gpc') == 1) {
				$this->params['form'] = stripslashes_deep($_POST);
			} else {
				$this->params['form'] = $_POST;
			}
		}

		$this->params['controller'] = $controller;
		$this->params['action'] = $action;
	}

	/**
	 * construct the url path under which this framework can be called from the browser. adds / at the end
	 * @return string
	 */
	private function constructBasePath() {
		$base = dirname(dirname(env('PHP_SELF')));
		if (substr($base, -1, 1) == '\\') { //XAMMP
			$base = substr($base, 0, -1);
		}
		if (substr($base, -1, 1) != '/') {
			$base .= '/';
		}
		return $base;
	}

	/**
	 * tries to construct the base url under which this framework can be called from the browser. adds a "/" at the end
	 */
	private function constructBaseUrl() {
		return 'http' . (env('HTTPS') != '' ? 's' : '') . '://' .
				env('SERVER_NAME') . (env('SERVER_PORT') != '80' ? (':' . env('SERVER_PORT')) : '') .
				$this->constructBasePath();
	}

	private function handleCachedFiles($what, $version) {
		$filename = KATATMP . 'cache' . DS . $what. '.cache.' . basename($version);
		if (file_exists($filename)) {
			if (DEBUG>=3) {
				header('Expires: ' . gmdate('D, d M Y H:i:s', time() - HOUR). ' GMT');
			} else {
				header('Expires: ' . gmdate('D, d M Y H:i:s', time() + (60 * WEEK)) . ' GMT');
			}

			switch ($what) {
				case 'css' :
					header('Content-Type: text/css');
					break;
				case 'js' :
					header('Content-Type: text/javascript');
					break;
			}

			readfile($filename);
			die;
		}

		$this->fourohfour();
	}

} //class



//============ lib/helper.php =======================================================





/**
 * helper base-class. helpers are the classes you can access via $this->helpername inside a view
 * @package kata_helper
 */
abstract class Helper {

	/**
	 * name of the action of the current controller the dispatcher called
	 * @var string
	 */
	public $action;

	/**
	 * absolute filesystem path to the webroot folder
	 * @var string
	 * @deprecated 01.01.2010
	 */
	public $webroot;

	/**
	 * array which holds all relevant information for the current view:
	 * [isAjax] => false (boolean, tells you if view got called with /ajax/)
	 * [url] => Array (
	 *       [url] => locations
	 *       [foo] => bar (if url read ?foo=bar)
	 * )
	 * [form] => Array (
	 * 	  (all post-variables, automatically dequoted if needed)
	 * )
	 * [controller] => main (name of the controller of this request)
	 * [action] => index (name of the view of this request)
	 * @var array
	 */
	public $params;

	/**
	 * placeholder for the tag-templates inside the config folder
	 * @param array
	 */
	public $tags = array (
		'link' => '<a href="%s" %s>%s</a>',
		'image' => '<img src="%s" %s/>',
		'selectstart' => '<select name="%s" %s>',
		'selectmultiplestart' => '<select name="%s[]" %s>',
		'selectempty' => '<option value="" %s></option>',
		'selectoption' => '<option value="%s" %s>%s</option>',
		'selectend' => '</select>',
		'cssfile' => '<link rel="stylesheet" type="text/css" href="%s" />',
		'jsfile' => '<script type="text/javascript" src="%s"></script>',
		'formstart' => '<form method="%s" action="%s" %s>',
		'formend' => '</form>',
		'formerror' => '<div class="formError">%s</div>',
		'input' => '<input name="%s" value="%s" type="%s" %s />',
		'checkbox' => '<input type="hidden" name="%s" value="0" /><input type="checkbox" name="%s" value="1" %s %s />',
		'textarea' => '<textarea name="%s" %s >%s</textarea>',
		'button' => '<button type="button" name="%s" value="%s" %s>%s</button>',
		'submit' => '<input type="submit" value="%s" %s />',
		'reset' => '<input type="reset" value="%s" %s />'
	);

	/**
	 * @var string complete url (including http) to the base of our framework
	 */
	public $base = null;

	/**
	 * @var string path to the base of our framework, sans http
	 */
	public $basePath = null;

	/**
	 * @var array view-vars
	 */
	public $vars = null;

	/**
	 * constructor, loads tags-templates from config folder
	 */
	function __construct() {
		if (file_exists(ROOT . 'config' . DS . 'tags.php')) {
			$tags = array ();
			//require ROOT . 'config' . DS . 'tags.php';
			$this->tags = array_merge($this->tags, $tags);
		}
	}

	/**
	 * construct an relative url with the base url of the framework
	 * @param string $url url to expand
	 * @return string
	 */
	public function urlRel($url = null) {
		if (empty ($url)) {
			return $this->basePath;
		}
		if ($url[0] == '/') {
			return $this->basePath . substr($url, 1);
		}
		if (isset ($url[5]) && ($url[4] == ':' || $url[5] == ':')) {
			return $url;
		}

		if (defined('CDN_URL') && (DEBUG < 1)) {
			$ext = strtolower(substr($url, -4, 4));
			if (($ext == '.jpg') || ($ext == '.gif') || ($ext == '.png')) {
				return sprintf(CDN_URL, ord($url[0]) % 4) . $url;
			}
		}

		return $this->basePath . $url;
	}

	/**
	 * shortcut. this is what your are normally using everywhere inside a view
	 * @param string $url url to expand
	 * @return string
	 */
	public function url($url = null) {
		return $this->urlAbs($url);
	}

	/**
	 * construct an absolute url (including http(s)) with the base url of the framework. normally needed if you send a view via email and you need the http-part
	 * @param string $url url to expand
	 * @return string
	 */
	public function urlAbs($url = null) {
		if (empty ($url)) {
			return $this->base;
		}
		if ($url[0] == '/') {
			return $this->base . substr($url, 1);
		}
		if (isset ($url[5]) && ($url[4] == ':' || $url[5] == ':')) {
			return $url;
		}
		return $this->base . $url;
	}

	/**
	 * build an attribute-string of an html-tag out of an array
	 * @param array $options the name=>value pairs to append to the tag
	 * @param mixed $exlude null or array of attribute-names not to append (eg. when they are framework-parameters, not html-attributes)
	 * @param string $insertBefore string to prepand
	 * @param mixed $insertAfter string to append, or null if you want nothing appended
	 * @return string attributes as html
	 */
	public function parseAttributes($options, $exclude = null, $insertBefore = ' ', $insertAfter = '') {
		//maintain compatibility if options is string. ignore $exclude because it makes no sense in this case
		if (!is_array($options)) {
			return $options ? $insertBefore . $options . $insertAfter : '';
		}

		if (!is_array($exclude)) {
			//again, maintain compatibility
			if (is_string($exclude)) {
				$eclude = array($exclude);
			} else {
				$exclude = array();
			}
		}

		$escape = true;
		if (isset ($options['escape'])) {
			$escape = $options['escape'];
			unset ($options['escape']);
		}
		if (isset ($exclude['escape'])) {
			$escape = $exclude['escape'];
			unset ($exclude['escape']);
		}

		if (count($options) > 0) {
			$minimized = array (
				'compact' => 1,
				'checked' => 1,
				'declare' => 1,
				'readonly' => 1,
				'disabled' => 1,
				'selected' => 1,
				'defer' => 1,
				'ismap' => 1,
				'nohref' => 1,
				'noshade' => 1,
				'nowrap' => 1,
				'multiple' => 1,
				'noresize' => 1
			);
			$options = array_diff_key($options, array_flip($exclude));
			$optionsMinimized = array_intersect_key($options, $minimized);
			$options = array_diff_key($options, $optionsMinimized);
		} else {
			$optionsMinimized = array ();
		}

		$out = '';
		foreach ($options as $n => $v) {
			if ($escape) {
				$out .= $n . '="' . h($v) . '" ';
			} else {
				$out .= $n . '="' . $v . '" ';
			}
		}
		foreach ($optionsMinimized as $n => $v) {
			$out .= $n . '="' . $n . '" ';
		}

		return $out ? $insertBefore . $out . $insertAfter : '';
	}

	/**
	 * @deprecated 1.1 - 09.11.2008 not needed anymore. use url() instead
	 * @return string
	 */
	public function urlWebroot($url) {
		return $this->url($url);
	}

/**
 * output a url with __token get parameter appended. used for xsrf-detection
 *
 * @param string $url url to add __token to
 * @return string url with token appended
 */
	function tokenUrl($url) {
		$url = $this->url($url);
		$token = is($this->vars['__token'],'');
		if ('' == $token) {
			return $url;
		}

		$x = strpos($url,'?');
		if (false !== $x) {
			return substr($url,0,$x).'?__token='.$token.'&'.substr($url,$x+1);
		}

		$x = strpos($url,'#');
		if (false !== $x) {
			return substr($url,0,$x).'?__token='.$token.substr($url,$x+1);
		}

		return $url.'?__token='.$token;
	}

	/**
	 * returns initialized class of other helpers. you have to take care of initialization order!
	 *
	 * @param string $name name of helper you need access to
	 * @return object
	 */
	function getHelper($name) {
		$classname = ucfirst(strtolower($name)) . 'Helper';
		if (!classRegistry::hasObject($classname)) {
			throw new RuntimeException("Helper $name not initialized yet. Wrong initialization order?");
		}
		return classRegistry::getObject($classname);
	}

}



//============ lib/view.php =======================================================



/**
 * Contains the view-Class that is used to render view-templates (layout,views,elements)
 *
 * Kata - Lightweight MVC Framework <http://www.codeninja.de/>
 * Copyright 2007-2009 mnt@codeninja.de, gameforge ag
 *
 * Licensed under The GPL License
 * Redistributions of files must retain the above copyright notice.
 * @package kata_view
 */

/**
 * default view class. used to render the view (and layout) for the controller
 * @package kata_view
 */
class View {
	/**
	 * controller class that the dispatcher called
	 * @var object
	 */
	protected $controller;

	/**
	 * array which holds all relevant information for the current view:
	 * [isAjax] => false (boolean, tells you if view got called with /ajax/)
	 * [url] => Array (
	 *       [url] => locations
	 *       [foo] => bar (if url read ?foo=bar)
	 * )
	 * [form] => Array (
	 * 	  (all post-variables, automatically dequoted if needed)
	 * )
	 * [controller] => main (name of the controller of this request)
	 * [action] => index (name of the view of this request)
	 * @var array
	 */
	public $params;

	/**
	 * name of the action of the current controller the dispatcher called
	 * @var string
	 */
	public $action;

	/**
	 * absolute filesystem path to the webroot folder
	 * @var string
	 * @deprecated 01.01.2010
	 */
	public $webroot;

	/**
	 * base url of this framework
	 * @var string
	 */
	public $base;

	/**
	 * array of helpers you can access inside the view via $this->helpername
	 * @var array
	 */
	public $helpers = array (
		"Html"
	);

	/**
	 * array that holds the actual instanciated classes of all constructed helpers for this view
	 */
	protected $helperClasses = array ();

	/**
	 * name of the element that is currently rendered inside the view via $this->renderElement
	 * @var string
	 */
	protected $elementName = '';

	/**
	 * name of the layout we are rendering to
	 * @var string
	 */
	protected $layoutName = '';

	/**
	 * used to stop us from accidently contructing helpers twice
	 * @var bool
	 */
	protected $didConstructHelpers = false;

	/**
	 * constructor. copies all needed variables from the controller
	 * @param object controller that uses this view
	 */
	function __construct(& $controller) {
		$this->controller = $controller;
		$this->params =  $controller->params;
		$this->action =  $controller->action;
		$this->webroot =  $controller->webroot;
		$this->helpers =  $controller->helpers;
		$this->base =  $controller->base;
		$this->basePath =  $controller->basePath;
	}

	/**
	 * construct all helpers we found in our helpers property
	 */
	protected function constructHelpers() {
		if ($this->didConstructHelpers) {
			return;
		}
		//require LIB.'helper.php';

		foreach ($this->helpers as $name) {
			$name = strtolower($name);
			$classname = ucfirst(strtolower($name)) . 'Helper';
			$h = classRegistry :: getObject($classname);

			$h->webroot = $this->webroot;
			$h->action = $this->action;
			$h->params = $this->controller->params;
			$h->base = $this->base;
			$h->basePath = $this->basePath;
			$h->vars = $this->controller->viewVars;

			$this->helperClasses[$name] = $h;
		}

		$this->didConstructHelpers = true;
	}

	/**
	 * render the actual view and layout. normally done at the end of a action of a controller
	 * <code>
	 * 1. all helpers are constructed here, very late, so we dont accidently waste cpu cycles
	 * 2. all variables, helpers and params given from the controller are extracted to global namespace
	 * 3. the actual view-template is rendered
	 * 4. the content of the rendered view is rendered into the layout
	 * </code>
	 * @param string $action name of the view
	 * @param string $layout name of the layout
	 * @return string html of the view
	 */
	public function render($action, $layout) {
		$this->action = $action;
		$this->layoutName = $layout;

		$this->constructHelpers();
		extract(array (
			'params' => $this->controller->params
		));
		extract($this->controller->viewVars);
		extract($this->helperClasses);
		$GLOBALS['__THIS'] = $this;

		$viewfile = ROOT . 'views' . DS . strtolower(substr(get_class($this->controller), 0, -10)) . DS . $this->action . ".php";
		if ($this->action[0] == '.') {
			$viewfile = str_replace(DS,'/',$viewfile);
			do {
				$viewfile = preg_replace('/\w+\/\.\.\//', '', $viewfile, -1, $cnt);
			} while($cnt!=0);
			$viewfile = str_replace('/',DS,$viewfile);
		}
		
		ob_start();
		// well shirley... if you get a fatal error your view is missing ;)
		require $viewfile;
		return $this->renderLayout(ob_get_clean(), $this->layoutName);
	}

	/**
	 * renders the given string into the layout. normally called by renderView()
	 * <code>
	 * 1. title and content are extracted into global namespace
	 * 2. all variables, helpers and params given from the controller are extracted to global namespace
	 * 3. the given string is rendered into the layout
	 * 4. the html-output of this routine normally lands in the controllers output property
	 * </code>
	 * @param string $contentForLayout raw html to be rendered to the layout (normally the content of a view)
	 * @param string $layout name of the layout
	 */
	public function renderLayout($contentForLayout, $layout) {
		$this->layoutName = $layout; //in case $layout gets overwritten
		if ($this->layoutName !== null) {
			$this->constructHelpers();

			extract(array (
				'content_for_layout' => $contentForLayout,
				'title_for_layout' => $this->controller->pageTitle,
				'this' => $this
			));
			extract($this->controller->viewVars);
			extract($this->helperClasses);
			$GLOBALS['__THIS'] = $this;

			ob_start();

			$viewfile = ROOT . 'views' . DS . 'layouts' . DS . $this->layoutName . '.php';
			if ($this->layoutName[0] == '.') {
				$viewfile = str_replace(DS,'/',$viewfile);
				do {
					$viewfile = preg_replace('/\w+\/\.\.\//', '', $viewfile, -1, $cnt);
				} while($cnt!=0);
				$viewfile = str_replace('/',DS,$viewfile);
			} 


			// well shirley... if you get a fatal error your layout is missing ;)
			require $viewfile;
			return ob_get_clean();
		} else {
			return $contentForLayout;
		}
	}

	/**
	 * render a element and return the resulting html. an element is kind of like a mini-view you can use inside a view (via $this->renderElement()).
	 * it has (like a view) access to all variables a normal view has
	 * @param string $name name of the element (see views/elements/) without .php
	 * @param an array of variables the element can access in global namespace
	 */
	public function renderElement($name, $params = array ()) {
		$this->elementName = $name; //in case $name gets overwritten 
		extract($this->controller->viewVars);
		extract($params);
		extract($this->helperClasses);
		$GLOBALS['__THIS'] = $this;

		ob_start();
		require ROOT . 'views' . DS . 'elements' . DS . $this->elementName . '.php';
		return ob_get_clean();
	}

}



//============ lib/class_registry.php =======================================================


/**
 * Contains the class-registry
 *
 * Kata - Lightweight MVC Framework <http://www.codeninja.de/>
 * Copyright 2007-2009 mnt@codeninja.de, gameforge ag
 *
 * Licensed under The GPL License
 * Redistributions of files must retain the above copyright notice.
 * @package kata_internal
 */




/**
 * Class-registry, a pseudo-singleton wrapper. Used to memorize and return all classes we did already instanciate.
 * @package kata_internal
 */
class classRegistry {

	/**
	 * array to save objects of any classed created
	 * @var array
	 */
	static protected $objects = array();

	/**
	 * return an instance of the class given in $name. If the class does not exist yet, create it.
	 *
     * @param string $name name of the class to return an instance of
     * @param string $key to create the same class more than once. uses simple caching to return consistency
     */
	static function &getObject($name,$key='') {
		$objname = $name.(empty($key)?'':'/').$key;

		if (!isset(self::$objects[$objname])) {
		     self::$objects[$objname]=new $name;
		}
		return self::$objects[$objname];
	}

	/**
	 * check if we already registered given classname
	 *
	 * @param string $name name of the class to return an instance of
	 * @param string $key to create the same class more than once
	 * @return bool
	 */
	static function hasObject($name,$key='') {
		$objname = $name.(empty($key)?'':'/').$key;
		return isset(self::$objects[$objname]);
	}

	/**
	 * return all classes in the cache. used for debugging
	 * @return array cache-array
	 */
	static function getLoadedClasses() {
		return self::$objects;
	}

}



//============ lib/controller.php =======================================================




/**
 * contains the base controller. app_controller is derived from this class, your controllers from the app_controller.
 *
 * Kata - Lightweight MVC Framework <http://www.codeninja.de/>
 * Copyright 2007-2009 mnt@codeninja.de, gameforge ag
 *
 * Licensed under The GPL License
 * Redistributions of files must retain the above copyright notice.
 * @package kata_controller
 */

/**
 * The controller itself. Is initialized by the dispatcher.
 * @package kata_controller
 */
abstract class Controller {
	/**
	 * which models to use. Array of Modelnames in CamelCase, eg. array('User','Ship')
	 * 
	 * @var array
	 */
	public $uses = array ();

	/**
	 * which helpers to use inside the view. Array of Helpernames in CamelCase, eg. array('Js','My'). html is always included.
	 *  
	 * @var array
	 */
	public $helpers = array (
		'html'
	);

	/**
	 * which components to use. Array of componentnames in CamelCase, eg. array('Locale','Session')
	 * 
	 * @var array
	 */
	public $components = array ();

	/**
	 * which views to cache and how long (in seconds, 0=infinite)
	 * Example: public $cache = array('index'=>300,'second'=>0)
	 *
	 * @var array
	 */
	public $cache = null;

	/**
	 * which layout to use. null means an empty layout, otherwise kata will look for views/layouts/default.php
	 * 
	 * @var string
	 */
	public $layout = 'default';

	/**
	 * if true render the view automatically (just after the controllers appropriate action was called). if false you have to do $this->render('myview') yourself.
	 * 
	 * @var boolean
	 */
	public $autoRender = true;

	/**
	 * holds params (get,post,controller,action) of this request
	 * 
	 * @var array
	 */
	public $params = null;

	/**
	 * holds the contents of the view+layout after it has been rendered (via render() or automatically if autoRender is true)
	 * 
	 * @var string
	 */
	public $output = '';

	/**
	 * holds an array of variables that are extracted as global variables into the view. add variables via set() or setRef()
	 * 
	 * @var array
	 */
	public $viewVars = array ();

	/**
	 * action of the controller that was originally called
	 * 
	 * @var string
	 */
	public $action;

	/**
	 * the absolute filesystem-path to the webroot
	 * 
	 * @var string
	 * @deprecated 01.01.2010
	 */
	public $webroot;

	/**
	 * the absolute base-path of kata, slash. append controllername and actionname to this and you have a full url
	 * 
	 * @var string
	 */
	public $basePath;

	/**
	 * the absolute url of kata, including http(s),path and last slash. append controllername and actionname to this and you have a full url
	 * 
	 * @var string
	 */
	public $base;

	/**
	 * string with a pagetitle to render to the current layout ($title_for_layout inside the view)
	 * 
	 * @var string
	 */
	public $pageTitle = '';

	/**
	 * which view-class to use to render the view
	 * 
	 * @var string
	 */
	public $view = 'View';

	/**
	 * placeholder for the instanciated view-class
	 * 
	 * @var object
	 */
	protected $viewClass = null;

	/**
	 * The default action of the controller if none
	 * is given.
	 */
	public $defaultAction = 'index';

	/**
	 * constructor. builds an array of all models, components and helpers this controller (including the ones of the appController) needs.
	 */
	function __construct() {
		if (substr(get_class($this), -10) != 'Controller') {
			throw new InvalidArgumentException('controller::__construct my classname does not end with "Controller"');
		}

		if (is_subclass_of($this, 'AppController')) {
			$appVars = get_class_vars('AppController');
			$this->view = $appVars['view'];
			$uses = $appVars['uses'];
			$merge = array (
				'components',
				'helpers'
			);

			if (!empty ($this->uses) && ($uses == $this->uses)) {
				//array_unshift($this->uses, $this->modelClass);
			}
			elseif (!empty ($this->uses)) {
				$merge[] = 'uses';
			}

			foreach ($merge as $var) {
				if (isset ($appVars[$var]) && !empty ($appVars[$var]) && is_array($this-> {
					$var })) {
					$this-> {
						$var }
					= array_merge($this-> {
						$var }, array_diff($appVars[$var], $this-> {
						$var }));
				}
			}
		}
	}

	/*
		function __call($string,$args) {
			echo "calling $string with ".count($args)." args";
		}
	
		function __get($name) {
			if (isset($this->$name)) { return $this->$name; }
			echo "getting $name";
		}
	
		function __set($name,$value) {
			if (isset($this->$name)) { $this->$name=$value; }
			echo "setting $name	to $value";
		}
	*/

	/**
	 * loads all models this controller needs
	 */
	function _constructClasses() {
		if (!is_array($this->uses)) {
			throw new InvalidArgumentException('$uses must be an array');
		}
		//require_once LIB.'model.php';
		foreach ($this->uses as $u) {
			$this-> $u = getModel($u);
		}
		$this->_constructComponents($this);
	}

	/**
	 * loads all components this controller needs, even late
	 */
	function _constructComponents() {
			if (!is_array($this->components)) {
				throw new InvalidArgumentException('$components must be an array');
			}
			foreach ($this->components as $comname) {
				$classname = $comname . 'Component';
				if (!isset($this->$comname)) {
					$this-> $comname = classRegistry :: getObject($classname);
					$this-> $comname->startup($this);
				}
			}
	}

	/**
	 * call the actual action of this controller and render it
	 */
	function dispatch() {
		try {
			call_user_func_array(array (
				$this,
				$this->params['action']
			), $this->params['pass']);
		} catch (Exception $e) {
			$this->afterError($e);
		}
	}

	/**
	 * render the given view with the given layout and put the result in the output-property of this controller. this just calls renderView which does the real work.
	 * 
	 * @param string $action name of the view (without .php) to render
	 * @param string $layout optional: name of the layout(-view) to render the view into. if false the default layout of this controller is taken
	 */
	function render($action, $layout = false) {
		$this->output = $this->renderView($action, $layout);
	}

	/**
	 * include and instanciate view-class
	 */
	protected function initViewClass() {
		if ($this->viewClass === null) {
			$viewClassName = $this->view;
			//require LIB.strtolower($viewClassName).'.php';
			$this->viewClass = new $viewClassName ($this);
		}
	}

	/**
	 * render the given view with the given layout and put the result in the output-property of this controller.
	 * 
	 * @param string $action name of the view (without .php) to render
	 * @param string $layout optional: name of the layout(-view) to render the view into. if false the default layout of this controller is taken
	 */
	function & renderView($action, $layout = null) {
		$this->initViewClass();

		$this->autoRender = false;
		$this->beforeFilter();
		if ($layout === false) {
			$layout = $this->layout;
		}
		$html = $this->viewClass->render($action, $layout);
		$this->afterFilter();

		return $html;
	}

	/**
	 * render the given view with the given layout and put the result in the output-property of this controller.
	 * 
	 * @param string $html raw html
	 * @param string $layout optional: name of the layout(-view) to render the view into. if false the default layout of this controller is taken
	 */
	function & renderCachedHtml($html, $layout = null) {
		$this->initViewClass();

		if ($layout === false) {
			$layout = $this->layout;
		}
		$this->autoRender = false;
		// call no before/afterFilter because the whole view is cached and needs no data

		$html = $this->viewClass->renderLayout($html, $layout);
		return $html;
	}

	/**
	 * redirect to the given url. if relative the base-url to the framework is automatically added.
	 * 
	 * @param string $url to redirect to
	 * @param int $status http status-code to use for redirection (default 303=get the new url via GET even if this page was reached via POST)
	 * @param bool $die if we should die() after redirect (default: true);
	 */
	function redirect($url, $status = null, $die = true) {
		if (!is_numeric($status) || ($status < 100) || ($status > 505)) {
			$status = 303;
		}

		$this->autoRender = false;
		if (function_exists('session_write_close')) {
			session_write_close();
		}

		$pos = strpos($url, '://');
		if ($pos === false) { // is relative url, construct rest
			$url = $this->base . $url;
		}

		header('HTTP/1.1 ' . $status);
		header('Location: ' . $url);
		if ($die) {
			if ((DEBUG < 1) && headers_sent()) {
				echo '<html><head><title>Redirect</title>' .
				'<meta http-equiv="refresh" content="1; url=' . $url . '">' .
				'<meta name="robots" content="noindex" /><meta http-equiv="cache-control" content="no-cache" /><meta http-equiv="pragma" content="no-cache" />' .
				'</head>' .
				'<body>Redirect to <a href="' . $url . '">' . $url . '</a></body>' .
				'<script type="text/javascript">window.setTimeout(\'document.location.href="' . $url . '";\',1100);</script>';
				'</html>';
			}
			die;
		}
	}

	/**
	 * set the pagetitle for the current layout
	 * 
	 * @param string $n title
	 */
	function setPageTitle($n) {
		$this->pageTitle = $n;
	}

	/**
	 * get the pagetitle for the current layout
	 * 
	 * @return string current title
	 */
	function getPageTitle() {
		return $this->pageTitle;
	}

	/**
	 * set a variable to be available inside the view. the given name-string is the name of the global variable inside the view ('bla' => $bla)
	 * 
	 * @param string $name name of the variable that should be globally accessible inside the view
	 * @param mixed $value contents of the variable
	 **/
	function set($name, $value = null) {
		if ($name == 'title') {
			$this->setPageTitle($value);
		} else {
			$this->viewVars[$name] = $value;
		}
	}

	/**
	 * like set, but assignes the variable by reference
	 * 
	 * @param string $name name of the variable that should be globally accessible inside the view
	 * @param mixed $value contents of the variable
	 */
	function setRef($name, & $value) {
		if ($name == 'title') {
			$this->setPageTitle($value);
		} else {
			$this->viewVars[$name] = $value;
		}
	}

	/**
	 * Get a variable that's available inside the view.
	 * The given name-string is the name of the global variable
	 * inside the view.
	 *
	 * @param string $name name of the variable
	 * @return mixed
	 */
	function get($name) {
		if ($name == 'title') {
			return $this->getPageTitle();
		}
		if (isset ($this->viewVars[$name])) {
			return $this->viewVars[$name];
		}
		return null;
	}

	/**
	 * late-add a helper, just for this view. use if you dont want to add an 
	 * helper via $helpers because only 1 view uses the helper
	 * 
	 * @param string $name name of the helper ('Form')
	 */
	function addHelper($name) {
		if (!in_array($name, $this->helpers)) {
			$this->helpers[] = $name;
		}
	}

	/**
	 * late-load a component
	 * @param string $name name of the component to add (without 'Component')
	 */
	function addComponent($name) {
		if (!in_array($name,$this->components)) {
			$this->components[] = $name;
			$this->_constructComponents();
		}
	}

	/**
	 * shortcut to writeLog
	 * 
	 * @param string $what what text to log
	 * @param int $where where to log (KATA_DEBUG OR KATA_ERROR)
	 */
	function log($what, $where) {
		writeLog($what, $where);
	}

	/**
	 * deprecated. use beforeAction() everywhere
	 * 
	 * @deprecated 07.01.2009
	 */
	function beforeRender() {
	}

	/**
	 * call this after the controller has been initialized (read: models, components etc contructed) and we are about to call the myaction() method of the controller
	 */
	function beforeAction() {
		$this->beforeRender();
	}

	/**
	 * Called just before the view is rendered. Is never called if autoRender is false
	 */
	function beforeFilter() {
	}

	/**
	 * Called just after the view was rendered. Is never called if autoRender is false. Can be used to manipulate the views contents in the controllers output-property
	 */
	function afterFilter() {
	}

	/**
	 * renders 404-layout if you return true. you can also call+render a different action if you like
	 * @return bool render 404?
	 */
	function before404() {
		return true;
	}

	/**
	* default exception handler. if you dont overwrite it just rethrows the exception 
	* @param object $exception previously occured exception
	*/
	function afterError($exception) {
		throw $exception;
	}

	/**
	 * translate get/post or url/form to string suiteable for $this->params
	 * @access private
	 */
	private function normalizeMethod($method) {
		$method = strtoupper($method);
		if (($method == 'GET') || ($method == 'URL')) {
			return 'url';
		}
		if (($method == 'POST') || ($method == 'FORM')) {
			return 'form';
		}
		throw new InvalidArgumentException("'method' can only be 'get' or 'post'");
	}

	/**
	 * validate form-helper results, against model if needed
	 * 
	 * @param array $fields key(inputname)-value(validationparam) array of form-variables to validate
	 * @return bool true if everything validated successful
	 */
	function validate($method, $how) {
		$method = $this->normalizeMethod($method);
		if (empty ($this->params[$method])) {
			return array ();
		}

		$validateUtil = getUtil('Validate');
		$result = $validateUtil->checkAll($how, $this->params[$method]);
		$this->set('__validateErrors', $result);
		return $result;
	}

	function validateModel($method, $modelname) {
		$method = $this->normalizeMethod($method);

		$modelname = strtolower($modelname);
		if (empty ($this->params[$method][$modelname])) {
			return array ();
		}

		$validateUtil = getUtil('Validate');
		$result = $validateUtil->checkAllWithModel($modelname, $this->params[$method][$modelname]);
		$this->set('__validateErrors', array (
			$modelname => $result
		));
		return $result;
	}

}


/**
 * base component class
 * @package kata_component
 */
abstract class Component {

	/**
	 * startup method
	 * @param Controller $controller parent controller
	 */
	function startup($controller) {
		$this->controller = $controller;
	}

}



//============ lib/databaseconnectexception.php =======================================================


/**
 * model related exception
 *
 * Kata - Lightweight MVC Framework <http://www.codeninja.de/>
 * Copyright 2007-2009 mnt@codeninja.de, gameforge ag
 *
 * Licensed under The GPL License
 * Redistributions of files must retain the above copyright notice.
 * @package kata_model
 */



/**
 * Thrown on database connection problems
 * 
 * @package kata_model
 */
class DatabaseConnectException extends Exception {

}



//============ lib/databaseduplicateexception.php =======================================================


/**
 * model related exception
 *
 * Kata - Lightweight MVC Framework <http://www.codeninja.de/>
 * Copyright 2007-2009 mnt@codeninja.de, gameforge ag
 *
 * Licensed under The GPL License
 * Redistributions of files must retain the above copyright notice.
 * @package kata_model
 */



/**
 * Thrown if an sql-query generates an duplication error due du primary/unique constraints
 * 
 * @package kata_model
 */
class DatabaseDuplicateException extends DatabaseErrorException {

}



//============ lib/databaseerrorexception.php =======================================================


/**
 * model related exception
 *
 * Kata - Lightweight MVC Framework <http://www.codeninja.de/>
 * Copyright 2007-2009 mnt@codeninja.de, gameforge ag
 *
 * Licensed under The GPL License
 * Redistributions of files must retain the above copyright notice.
 * @package kata_model
 */



/**
 * Thrown if an sql-query generates an error
 * 
 * @package kata_model
 */
class DatabaseErrorException extends Exception {

	/**
	 * contains the query that generated the exception
	 * @var string
	 */
	protected $query = '';

	/**
	 *
	 * @param string $message informational message
	 * @param mixed $code query string
	 * @param Exception $previous previous Exception
	 */
	public function __construct($message = null, $code = 0, Exception $previous = null) {
		if ((0 !== $code) && is_String($code)) {
			$code = trim($code);
			if (strlen($code)>1024) {
				$code = substr($code,0,1023).' *snip*';
			}

			$this->query = $code;
			$message = $message." (SQL '".$code."')";
			$code = 0;
		}
		
		parent::__construct($message, $code);
	}

	/**
	 * return query that generated the exception
	 * @return string
	 */
	final public function getQueryString() {
		return $this->query;
	}


}



//============ lib/dbo_interface.php =======================================================



/**
 * Contains a wrapper-class for mssql, so models can access mssql
 *
 * Kata - Lightweight MVC Framework <http://www.codeninja.de/>
 * Copyright 2007-2009 mnt@codeninja.de, gameforge ag
 *
 * Licensed under The GPL License
 * Redistributions of files must retain the above copyright notice.
 * @package kata_model
 */

/**
 * interface 
 * @package kata_model
 **/
interface dbo_interface {

	/**
	 * REPLACE works like INSERT,
	 * except that if an old row in the table has the same value as a new row for a PRIMARY KEY or a UNIQUE  index,
	 * the old row is deleted before the new row is inserted
	 *
	 * @param string $tableName replace from this table
	 * @param array $fields name=>value pairs of new values
	 * @param string $pairs enquoted names to escaped pairs z.B.[name]='value'
	 * @return int modified rows.
	 */
	function replace($tableName, $fields, $pairs);

	/**
	 * a copy of the matching db-config entry in config/database.php
	 * @param $string $what spezifies what to get ... null=complete config array
	 * @return array|string
	 */
	function getConfig($what= null);

	/**
	 * set db-config entry
	 * @param $array $config
	 */
	function setConfig($config);

	/**
		 * checks if given operator is valid
		 * @param string $operator
		 * @return boolean
		 */
	function isValidOperator($operator);

	/**
	 * connect to the database
	 */
	function connect();

	/**
	 * are we connected?
	 */
	function isConnected();

	/**
	 * return the current link to the database, connect first if needed
	 */
	function getLink();

	/**
	 * inject dblink into dbo
	 */
	function setLink($l);

	/**
	 * unused right now, later possibly used by model to set right encoding
	 */
	function setEncoding($enc);

	/**
	 * execute query and return useful data depending on query-type
	 *
	 * @param string $s sql-statement
	 * @param array $idname which field-value to use as the key of the returned array (false=dont care)
	 * @return array
	 *
	 */
	function & query($s, $idnames= false);

	/**
	 * escape the given string so it can be safely appended to any sql
	 * @param string $sql string to escape
	 * @return string
	 */
	function escape($sql);

	/**
	* used to quote table and field names
	* @param string $s string to enquote;
	* @return string enquoted string
	*/
	function quoteName($s);

	/**
	 * output any queries made, how long it took, the result and any errors if DEBUG
	 * close the connection
	 */
	function __destruct();

	/**
	 * build a sql-string that returns first matching row
	 * @param string $sql SQL-String
	 * @param string $perpage expresion
	 * @return string finished query
	 */
	function getFirstRowQuery($sql, $perpage);

	/**
	 * build a sql-string that returns paged data
	 * @return string finished query
	 */
	function getPageQuery($sql, $page, $perPage);

	/**
	 * return the sql needed to convert a unix timestamp to datetime
	 * @param integer $t unixtime
	 * @return string
	 */
	function makeDateTime($t);

	/**
	 * try to reduce the fields of given table to the basic types bool, unixdate, int, string, float, date, enum
	 *
	 * <code>example:
	 *
	 * Array
	 * (
	 *     [table] => test
	 *     [primary] => array
	 *     [unique] => array
	 *     [cols] => Array
	 *         (
	 *             [a] => Array
	 *                 (
	 *                     [default] => CURRENT_TIMESTAMP
	 *                     [null] =>
	 *                     [length] => 0
	 *                     [type] => date
	 *                 )
	 *
	 *             [g] => Array
	 *                 (
	 *                     [default] =>
	 *                     [null] =>
	 *                     [length] => 0
	 *                     [type] => unsupported:time
	 *                 )
	 *         )
	 *
	 * )
	 * </code>
	 *
	 * @param string $tableName name of the table to analyze
	 * @return unknown
	 */
	function & describe($tableName);
}



//============ lib/dbo_mssql.php =======================================================




/**
 * Contains a wrapper-class for mssql, so models can access mssql
 *
 * Kata - Lightweight MVC Framework <http://www.codeninja.de/>
 * Copyright 2007-2009 mnt@codeninja.de, gameforge ag
 *
 * Licensed under The GPL License
 * Redistributions of files must retain the above copyright notice.
 * @package kata_model
 */

/**
 * this class is used by the model to access the database itself
 * @package kata_model 
 * @author mnt@codeninja.de
 * @author marcel.boessendoerfer@gameforge.de
 */

class dbo_mssql { //implements dbo_interface {

	/**
	 * a copy of the matching db-config entry in config/database.php
	 * @var array
	 */
	private $dbconfig = null;

	/**
	 * a placeholder for any result the database returned
	 * @var array
	 */
	private $result = null;

	/**
	 * a placeholder for the database link needed for this database
	 * @var int
	 */
	private $link = null;

	/**
	 * an array that holds all queries and some relevant information about them if DEBUG
	 * @var array
	 */
	private $queries = array ();

	/**
	 * constants used to quote table and field names
	 *
	 */
	private $quoteLeft = '[';
	private $quoteRight = ']';

	/**
	 * connect to the database
	 */
	function connect() {
		$this->link = mssql_connect($this->dbconfig['host'], $this->dbconfig['login'], $this->dbconfig['password']);
		if (!$this->link) {
			throw new DatabaseConnectException("Could not connect to server " . $this->dbconfig['host']);
		}
		if (!empty ($this->dbconfig['database'])) {
			$db = $this->dbconfig['database'];
			if ($db[0] != '[') {
				$db = '['.$db.']';
			}
			if (!mssql_select_db($db, $this->link)) {
				throw new DatabaseConnectException("Could not select Database " . $this->dbconfig['database']);
			}
		}

		if (!empty ($this->dbconfig['encoding'])) {
			$this->setEncoding($this->dbconfig['encoding']);
		}

		//freetds hack: freetds does not offer this function :(
		if (!function_exists("mssql_next_result")) {
			function mssql_next_result($res = null) {
				return false;
			}
		}
	}

	function isConnected() {
		return (bool) $this->link;
	}

	/**
	 * return the current link to the database, connect first if needed
	 */
	public function getLink() {
		if (!$this->link) {
			$this->connect();
		}
		return $this->link;
	}

	/**
		 * inject db link into dbo
		 */
	public function setLink($l) {
		$this->link = $l;
	}

	/**
	 * execute this query
	 * @return mixed
	 */
	private function execute($sql) {
		if (!$this->link) {
			$this->connect();
		}

		$start = microtime(true);
		$this->result = mssql_query($sql, $this->link);

		if (false === $this->result) {
			$msg = mssql_get_last_message();
			//TODO another way would be to check @@ERROR for errors 2601/2627 which is ALSO language dependend *facepalm*  
			if (stripos($msg,'duplicate') !== false) {
				DatabaseDuplicateException($msg);				
			} else {
				writeLog($msg . ': ' . $sql, KATA_ERROR);
				throw new DatabaseErrorException($msg,$sql);
			}
		}
		if (DEBUG > 0) {
			$this->queries[] = array (
				kataFunc::getLineInfo(),
				trim($sql),
				'',
				mssql_get_last_message(),
				 (microtime(true) - $start) . 'sec'
			);
		}
	}

	/**
	 * unused right now, later possibly used by model to set right encoding
	 */
	function setEncoding($enc) {
		//TODO
	}

	/**
	 * return numbers of rows affected by last query
	 * @return int
	 */
	private function lastAffected() {
		if ($this->link) {
			if (function_exists('mssql_rows_affected')) {
				return mssql_rows_affected($this->link);
			} else {
				$result = mssql_query("select @@rowcount as rows", $this->link);
				$rows = mssql_fetch_assoc($result);
				return $rows['rows'];
			}
		}
		$null = null;
		return $null;
	}

	/**
	 * return id of primary key of last insert
	 * @return int
	 */
	private function lastInsertId() {
		$this->execute("select SCOPE_IDENTITY() AS id");
		if ($this->result) {
			$res = mssql_fetch_assoc($this->result);
			if ($res) {
				$id = $res['id'];
				if ($this->result) {
					mssql_free_result($this->result);
				}
				return $id;
			}
		}
		return null;
	}

	/**
	 * return the result of the last query.
	 * @param mixed $idname if $idname is false keys are simply incrementing from 0, if $idname is string the key is the value of the column specified in the string
	 */
	private function & lastResult($idnames = false) {
		do {
			$result = array ();
			if (@mssql_num_rows($this->result) > 0) {
				if ($idnames === false) {
					while ($row = mssql_fetch_assoc($this->result)) {
						$result[] = $row;
					}
				} else {
					while ($row = mssql_fetch_assoc($this->result)) {
						$current = & $result;
						foreach ($idnames as $idname) {
							if (!array_key_exists($idname,$row)) {
								throw new InvalidArgumentException('Cant order result by a field thats not in the resultset (forgot to select it?)');
							}
							if ($row[$idname] === null) {
								$row[$idname] = 'null';
							}
							$current = & $current[$row[$idname]];
						}
						$current = $row;
					} //while
				} //idnames!=false
			} //num_rows>0
		} //do
		while (mssql_next_result($this->result));
		return $result;
	}
	/**
	 * REPLACE works like INSERT,
	 * except that if an old row in the table has the same value as a new row for a PRIMARY KEY or a UNIQUE index,
	 * the old row is deleted before the new row is inserted
	 *
	 * @param string $tableName replace from this table
	 * @param array $fields name=>value pairs of new values
	 * @param string $pairs enquoted names to escaped pairs z.B.[name]='value'
	 * @return int modified rows.
	 */
	function replace($tableName, $fields, $pairs) {
		throw new Exception('Not easily supportable on MSSQL. Direct your thanks for this to Microsoft.');
	}

	/**
	 * execute query and return useful data depending on query-type
	 *
	 * @param string $s sql-statement
	 * @param string $idname which field-value to use as the key of the returned array (false=dont care)
	 * @return array
	 */
	function & query($s, $idnames = false, $fields = false) {
		$result = null;
		switch ($this->getSqlCommand($s)) {
			case 'update' :
			case 'delete' :
			case 'alter':
				$this->execute($s);
				$result = $this->lastAffected();
				break;
			case 'insert' :
				$this->execute($s);
				$result = $this->lastInsertId();
				break;
			case 'select' :
			case 'exec':
			case 'execute':
			case 'show':
			case '/*page*/if' :
				if (is_string($idnames)) {
					$idnames = array (
						$idnames
					);
				}
				$this->execute($s);
				$result = $this->lastResult($idnames, $fields);
				break;
			default :
				$this->execute($s);
				$result = $this->result;
				break;
		}
		return $result;
	}

	/**
	 * escape the given string so it can be safely appended to any sql
	 * @param string $sql string to escape
	 * @return string
	 */
	function escape($sql) {
		return str_replace("'", "''", $sql); //seems odd but in mssql a single ' can be escaped by another
	}

	/**
	 * output any queries made, how long it took, the result and any errors if DEBUG
	 */
	function __destruct() {
		if (DEBUG > 0) {
			array_unshift($this->queries, array (
				'line',
				'',
				'affected',
				'error',
				'time'
			));
			kataDebugOutput($this->queries, true);
		}
		if ((bool) $this->link) {
			if (is_resource($this->link) && (get_resource_type($this->link) == 'mssql link')) {
				mssql_close($this->link);
			}
			$this->link = null;
		}
	}

	/**
	 * return the Sql-Command of given Query
	 * @param string $sql query
	 * @return string Sql-Command
	 */
	private function getSqlCommand($sql) {
		$sql = str_replace(array (
			"(",
			"\t",
			"\n"
		), " ", $sql);
		$Sqlparts = explode(" ", trim($sql));
		return strtolower($Sqlparts[0]);
	}

	/**
	 * build a sql-string that returns first matching row
	 * @param string $sql query
	 * @param string $perPage expression
	 * @return string (limited) Query
	 */
	function getFirstRowQuery($sql, $perPage) {
		//TODO UNION,EXCEPT,INTERSECT... not implemented, mostly not supported anyway
		$command = $this->getSqlCommand($sql);
		$validTopComands = array (
			'select' => 1,
			'insert' => 1,
			'update' => 1,
			'delete' => 1,
			'merge' => 1
		);
		//set TOP after first Command
		if (isset($validTopComands[$command])) {
			$first = mb_strpos(strtolower($sql), $command);
			$firstPart = mb_substr($sql, 0, $first);
			$secondPart = mb_substr($sql, ($first +strlen($command)));
			return $firstPart . $command . " TOP(" . $perPage . ")" . $secondPart;
		}
		return $sql;
	}

	/**
	 * build a sql-string that returns paged data
	 * every computed output has to be named !!! so 'max(x)' has to be 'max(x) as maxX' or something like that...
	 *
	 * some warnings/comments from dietmar riess:
	 * IF There is no IDENTITY FIELD we can numbering Rows with temp Table
	 * IF There is an IDENTITY FIELD we have to execute the much slower EXCEPT Query
	 * Also we know there is an IDENTITY FIELD we can't use it, because we do not! know which column it is !
	 *
	 * @see getPageQuery Interface
	 * @param boolean $orderd true is depreacated fliping TOPS
	 * @return string finished query
	 */
	function getPageQuery($sql, $page, $perPage) {
		$command = $this->getSqlCommand($sql);
		if ($command != "select") {
			throw new InvalidArgumentException('paging is not possible for given query');
			return $sql;
		}
		$fastQuery = $this->getFirstRowQuery($sql, 1);
		$fastInsertQuery = 'IF OBJECT_ID(\'tempdb..#temp\') IS NOT NULL DROP TABLE #temp;SELECT * INTO #temp FROM (' . $fastQuery . ') as a';
		$this->execute($fastInsertQuery);
		$ID = $this->lastInsertId();
		if ($ID === null) {
			$fastQuery = $this->getFirstRowQuery($sql, $page * $perPage);
			$tmptable = '/*PAGE*/IF OBJECT_ID(\'tempdb..#table\') IS NOT NULL DROP TABLE #table;SELECT IDENTITY(int,1,1) as tempRowNumID,* INTO #table FROM (' . $fastQuery . ') as a;';
			$tmptableAndQuery = $tmptable . 'SELECT * FROM #table where tempRowNumID between ' . (($page -1) * $perPage +1) . ' AND ' . (($page) * $perPage);
			return $tmptableAndQuery;
		} else {
			$topPages = $this->getFirstRowQuery($sql, $page * $perPage);
			$lastPages = $this->getFirstRowQuery($sql, ($page -1) * $perPage);
			$Query = 'SELECT * FROM (' . $topPages . ') as a EXCEPT SELECT * FROM (' . $lastPages . ') as a';
			return $Query;
		}
	}
	/**
	 * return the sql needed to convert a unix timestamp to datetime
	 * @param integer $t unixtime
	 * @return string
	 */
	function makeDateTime($t) {
		//may lie to you: mssql does not calculate summertime
		return "CONVERT(char(20),dateadd(ss," . $t . "+DATEDIFF(ss, GetUtcDate(), GetDate()),'1970-01-01 00:00:00'),120)";
	}

	/**
	 * try to reduce the fields of given table to the basic types bool, unixdate, int, string, float, date, enum
	 *
	 * <code>example:
	 *
	Array
	 * (
	 *     [table] => test
	 *     [primary] => a
	 *     [cols] => Array
	 *         (
	 *             [a] => Array
	 *                 (
	 *                     [default] => CURRENT_TIMESTAMP
	 *                     [null] =>
	 *                     [length] => 0
	 *                     [type] => date
	 *                 )
	 *
	 *             [g] => Array
	 *                 (
	 *                     [default] =>
	 *                     [null] =>
	 *                     [length] => 0
	 *                     [type] => unsupported:time
	 *                 )
	 *         )
	 *
	 * )
	 * </code>
	 *
	 * @param string $tableName name of the table to analyze
	 * @return unknown
	 */
	function & describe($tableName) {
		$primaryKey = array ();
		$identity = null;
		$desc = array ();
		$cols = array ();
		$tableName = $this->dequote($tableName);
		/*possibly incomplete*/
		$sql = "Select a.COLUMN_NAME,a.[IS_NULLABLE],a.[COLUMN_DEFAULT],a.[DATA_TYPE],a.[CHARACTER_MAXIMUM_LENGTH],a.[NUMERIC_PRECISION],b.[CONSTRAINT_NAME],COLUMNPROPERTY(OBJECT_ID('" . $tableName . "'),a.COLUMN_NAME, 'IsIdentity') as [identity]
										From INFORMATION_SCHEMA.COLUMNS a left join INFORMATION_SCHEMA.CONSTRAINT_COLUMN_USAGE as b on (a.COLUMN_NAME = b.COLUMN_NAME AND a.TABLE_NAME=b.TABLE_NAME)
										where a.TABLE_NAME='" . $tableName . "'";
		/*tested this do it ,cause CONSTRAINT_NAME can be defined by user!(fehlender Beweis für:"unkorrelierte Subqueries werden nur einmal ausgeführt")
		$sql = "Select a.COLUMN_NAME,[IS_NULLABLE],[COLUMN_DEFAULT],[DATA_TYPE],[CHARACTER_MAXIMUM_LENGTH],[NUMERIC_PRECISION],[CONSTRAINT_NAME],[CONSTRAINT_TYPE],a.COLUMN_NAME, 'IsIdentity') as [identity]
						from INFORMATION_SCHEMA.COLUMNS a left join
							(SELECT a.COLUMN_NAME,b.CONSTRAINT_TYPE,b.CONSTRAINT_NAME
							from INFORMATION_SCHEMA.CONSTRAINT_COLUMN_USAGE as a,INFORMATION_SCHEMA.TABLE_CONSTRAINTS as b
							where a.TABLE_NAME='".$tableName."' AND a.TABLE_CATALOG = db_name()
							AND b.TABLE_NAME='".$tableName."' AND b.TABLE_CATALOG = db_name()
							AND b.CONSTRAINT_NAME=a.CONSTRAINT_NAME
							)as b on (a.COLUMN_NAME = b.COLUMN_NAME)
						where a.TABLE_NAME='".$tableName."' AND a.TABLE_CATALOG =db_name()";
		*/
		$r = mssql_query($sql, $this->getLink());
		if (false === $r) {
			throw new Exception('model: cant describe, missing rights?');
		}
		$noResult = true;
		while ($row = mssql_fetch_assoc($r)) {
			$noResult = false;
			$data = array ();
			$data['default'] = empty ($row['COLUMN_DEFAULT']) ? false : $row['COLUMN_DEFAULT'];
			$data['null'] = 'NO' != $row['IS_NULLABLE'];
			$data['length'] = 0;
			if ($row['identity'] == 1) {
				$identity = $row['COLUMN_NAME'];
			}
			/*deprecated
			if('UNIQUE' == $row['CONSTRAINT_TYPE'] ){
				if(!isset($uniqueKeys[$row['CONSTRAINT_NAME']])){
					$uniqueKeys[$row['CONSTRAINT_NAME']] = array();
				}
				$uniqueKeys[$row['CONSTRAINT_NAME']][] = $row['COLUMN_NAME'];
			}
			if ('PRIMARY KEY' == $row['CONSTRAINT_TYPE']) {
				$primaryKey[] = $row['COLUMN_NAME'];
			}
			*/
			//keys
			$data['key'] = null;
			if (isset ($row['CONSTRAINT_NAME'])) {
				$key = substr($row['CONSTRAINT_NAME'], 0, 2);
				if ($key == 'PK') {
					$primaryKey[] = $row['COLUMN_NAME'];
					$data['key'] = 'PRI';
				} else
					if ($key == 'UQ') {
						$data['key'] = 'UNI';
					}
			}
			//types
			switch ($row['DATA_TYPE']) {
				case 'bit' :
					$data['type'] = 'bool';
					$data['length'] = $row['NUMERIC_PRECISION'];
					break;
				case 'bigint' :
				case 'int' :
				case 'smallint' :
				case 'tinyint' :
					$data['length'] = $row['NUMERIC_PRECISION'];
					$data['type'] = 'int';
					break;
				case 'char' :
				case 'varchar' :
					$data['length'] = $row['CHARACTER_MAXIMUM_LENGTH'];
					$data['type'] = 'string';
					break;
				case 'text' :
					$data['type'] = 'text';
					break;
				case 'float' :
				case 'real' :
					$data['length'] = $row['NUMERIC_PRECISION'];
					$data['type'] = 'float';
					break;
				case 'date' :
				case 'datetime' :
				case 'datetime2' :
				case 'smalldatetime' :
				case 'datetimeoffset' :
				case 'time' :
					$data['type'] = 'date';
			}
			$cols[$row['COLUMN_NAME']] = $data;
		}

		if ($noResult === true) {
			throw new Exception('table does not exists in selected Database');
		}
		$desc = array (
			'table' => str_replace(array (
				$this->quoteLeft,
				$this->quoteRight
			), '', $tableName),
			'primary' => $primaryKey,
			'identity' => $identity,
			'cols' => $cols
		);
		return $desc;
	}

	/**
	 * a copy of the matching db-config entry in config/database.php
	 * @param string $what spezifies what to get ... null=complete config array
	 * @return array|string
	 */
	function getConfig($what = null) {
		if (!empty ($what)) {
			return (isset ($this->dbconfig[$what])) ? $this->dbconfig[$what] : '';
		}
		return $this->dbconfig;
	}

	/**
	 * set db-config entry
	 * @param $array $config
	 */
	function setConfig($config) {
		if (empty ($this->dbconfig)) {
			$this->dbconfig = $config;
		}
	}

	/**
	 * used to quote table and field names
	 * @param string $s string to enquote;
	 * @return string enquoted string
	 */
	function quoteName($s) {
		return $this->quoteLeft . $s . $this->quoteRight;
	}

	/**
	 * checks if given operator is valid
	 * @param string $operator
	 * @return boolean
	 */
	function isValidOperator($operator) {
		if (empty ($operator)) {
			return false;
		}
		$ops = array (
			'=' => 1,
			'>' => 1,
			'<' => 1,
			'>=' => 1,
			'<=' => 1,
			'<>' => 1,
			'!=' => 1,
			'!<' => 1,
			'>!' => 1,
			'is null' => 1,
			'is not null' => 1,
			'between' => 1,
			'in' => 1,
			'not in' => 1,
			'like' => 1,
			'not like' => 1
		);
		return isset($ops[$operator]);
	}
}



//============ lib/dbo_mysql.php =======================================================




/**
 * Contains a wrapper-class for mysql, so models can access mysql
 *
 * Kata - Lightweight MVC Framework <http://www.codeninja.de/>
 * Copyright 2007-2009 mnt@codeninja.de, gameforge ag
 *
 * Licensed under The GPL License
 * Redistributions of files must retain the above copyright notice.
 * @package kata_model
 */

/**
 * this class is used by the model to access the database itself
 * @package kata_model
 */
class dbo_mysql { //implements dbo_interface {

	/**
	 * a copy of the matching db-config entry in config/database.php
	 * @var array
	 */
	private $dbconfig = null;

	/**
	 * a placeholder for any result the database returned
	 * @var array
	 */
	private $result = null;

	/**
	 * a placeholder for the database link needed for this database
	 * @var int
	 */
	private $link = null;

	/**
	 * an array that holds all queries and some relevant information about them if DEBUG>1
	 * @var array
	 */
	private $queries = array ();

	/**
	 * constants used to quote table and field names
	 */
	private $quoteLeft = '`';
	private $quoteRight = '`';

	/**
	 * connect to the database
	 */
	function connect() {
		$this->link = mysql_connect($this->dbconfig['host'], $this->dbconfig['login'], $this->dbconfig['password']);
		if (!$this->link) {
			throw new DatabaseConnectException("Could not connect to server " . $this->dbconfig['host']);
		}
		if (!mysql_select_db($this->dbconfig['database'], $this->link)) {
			throw new DatabaseConnectException("Could not select database " . $this->dbconfig['database']);
		}

		if (!empty ($this->dbconfig['encoding'])) {
			$this->setEncoding($this->dbconfig['encoding']);
		}
	}

	/**
	 * if we are already connected
	 * @return bool
	 */
	function isConnected() {
		return (bool) $this->link;
	}

	/**
	 * return the current link to the database, connect first if needed
	 */
	public function getLink() {
		if (!$this->link) {
			$this->connect();
		}
		return $this->link;
	}

	/**
	 * inject database link into dbo
	 */
	public function setLink($l) {
		$this->link = $l;
	}

	/**
	 * execute this query
	 * @return mixed
	 */
	private function execute($sql) {
		if (!$this->link) {
			$this->connect();
		}

		$start = microtime(true);
		$this->result = mysql_query($sql, $this->link);

		if (false === $this->result) {
			switch (mysql_errno($this->link)) {
				case 1062:
					throw new DatabaseDuplicateException(mysql_error($this->link));
					break;
				default:
					writeLog(mysql_error($this->link) . ': ' . $sql, KATA_ERROR);
					throw new DatabaseErrorException(mysql_error($this->link),$sql);
					break;
			}
		}
		if (DEBUG > 0) {
			$this->queries[] = array (
				kataFunc::getLineInfo(),
				trim($sql),
				mysql_affected_rows($this->link),
				mysql_error($this->link),
				 (microtime(true) - $start) . 'sec'
			);
		}
	}

	/**
	 * unused right now, later possibly used by model to set right encoding
	 */
	function setEncoding($enc) {
		$this->execute('SET NAMES ' . $enc);
		return $this->result;
	}

	/**
	 * return numbers of rows affected by last query
	 * @return int
	 */
	private function lastAffected() {
		if ($this->link) {
			return mysql_affected_rows($this->link);
		}
		$null = null;
		return $null;
	}

	/**
	 * return id of primary key of last insert
	 * @return int
	 */
	private function lastInsertId() {
		//may lie to you: http://bugs.mysql.com/bug.php?id=26921
		$id = mysql_insert_id($this->link);
		if ($id) {
			return $id;
		}
		//may lie to you: if you have an auto-increment-table and you supply a primary key LAST_INSERT_ID is unchanged
		$result = $this->query("SELECT LAST_INSERT_ID() as id");
		if (!empty ($result)) {
			return $result[0]['id'];
		}
		$null = null;
		return $null;
	}

	/**
	 * return the result of the last query.
	 * @param mixed $idname if $idname is false keys are simply incrementing from 0, if $idname is string the key is the value of the column specified in the string
	 */
	private function & lastResult($idnames = false) {
		$result = array ();
		if (@ mysql_num_rows($this->result) > 0) {
			if ($idnames === false) {
				while ($row = mysql_fetch_assoc($this->result)) {
					$result[] = $row;
				}
			} else {
				while ($row = mysql_fetch_assoc($this->result)) {
					$current = & $result;
					foreach ($idnames as $idname) {
						if (!array_key_exists($idname,$row)) {
							throw new InvalidArgumentException('Cant order result by a field thats not in the resultset (forgot to select it?)');
						}
						if ($row[$idname] === null) {
							$row[$idname] = 'null';
						}
						$current = & $current[$row[$idname]];
					} //foreach
					$current = $row;
				} //while fetch
			} //idnames
		} //rows>0
		return $result;
	}

	/**
	 * REPLACE works exactly like INSERT,
	 * except that if an old row in the table has the same value as a new row for a PRIMARY KEY or a UNIQUE  index,
	 * the old row is deleted before the new row is inserted
	 *
	 * @param string $tableName replace from this table
	 * @param array $fields name=>value pairs of new values
	 * @param string $pairs enquoted names to escaped pairs z.B.[name]='value'
	 * @return int modified rows.
	 */
	function replace($tableName, $fields, $pairs) {
		return $this->query('REPLACE INTO ' . $tableName . ' SET ' . $pairs);
	}

	/**
	 * execute query and return useful data depending on query-type
	 *
	 *  	SELECT / SHOW 											=> resultset array
	 * 		REPLACE / UPDATE / DELETE / ALTER 						=> affected rows (int)
	 * 		INSERT													=> last insert id (int)
	 * 		RENAME / LOCK / UNLOCK / TRUNCATE /SET / CREATE / DROP	=> Returns if the operation was successfull (boolean)
	 *
	 * @param string $s sql-statement
	 * @param string $idname which field-value to use as the key of the returned array (false=dont care)
	 * @return array
	 */
	function & query($s, $idnames = false, $fields = false) {
		$result = null;
		switch ($this->getSqlCommand($s)) {
			case 'replace' :
			case 'update' :
			case 'delete' :
			case 'alter' :
			case 'call':
				$this->execute($s);
				$result = $this->lastAffected();
				break;
			case 'insert' :
				$this->execute($s);
				$result = $this->lastInsertId();
				break;
			case 'select' :
			case 'show' :
				if (is_string($idnames)) {
					$idnames = array (
						$idnames
					);
				}
				$this->execute($s);
				$result = $this->lastResult($idnames, $fields);
				break;
			default :
				$this->execute($s);
				$result = $this->result;
				break;
		}
		return $result;
	}

	/**
	 * escape the given string so it can be safely appended to any sql
	 * @param string $sql string to escape
	 * @return string
	 */
	function escape($sql) {
		if (!$this->link) {
			$this->connect();
		}
		return mysql_real_escape_string($sql, $this->link);
	}

	/**
	 * return sql needed to convert unix timestamp to datetime
	 * @param integer $t unixtime
	 * @return string
	*/
	function makeDateTime($t) {
		return 'FROM_UNIXTIME(' . $t . ')';
	}

	/**
	 * output any queries made, how long it took, the result and any errors if DEBUG>1
	 */
	function __destruct() {
		if (DEBUG > 0) {
			array_unshift($this->queries, array (
				'line',
				'',
				'affected',
				'error',
				'time'
			));
			kataDebugOutput($this->queries, true);
		}
		if ((bool) $this->link) {
			//sometimes the resource FNORDs
			if (is_resource($this->link) && (get_resource_type($this->link) == 'mysql link')) {
				mysql_close($this->link);
			}
			$this->link = null;
		}
	}

	private function getFieldSize($str) {
		$x1 = strpos($str, '(');
		$x2 = strpos($str, ')');
		if ((false !== $x1) && (false !== $x2)) {
			return substr($str, $x1 +1, $x2 - $x1 -1);
		}
		return 0;
	}

	/**
	 * return the Sql-Command of given Query
	 * @param string $sql query
	 * @return string Sql-Command
	 */
	private function getSqlCommand($sql) {
		$sql = str_replace(array (
			"(",
			"\t",
			"\n"
		), " ", $sql);
		$Sqlparts = explode(" ", trim($sql));
		return strtolower($Sqlparts[0]);
	}

	/**
	 * build a sql-string that returns first matching row
	 * @param string $sql query
	 * @param string $perPage expression
	 * @return string (limited) Query
	 */
	function getFirstRowQuery($sql, $perPage) {
		return sprintf('%s LIMIT %d', $sql, $perPage);
	}

	/**
	 * build a sql-string that returns paged data
	 * @return string finished query
	 */
	function getPageQuery($sql, $page, $perPage) {
		return sprintf('%s LIMIT %d,%d', $sql, ($page -1) * $perPage, $perPage);
	}

	/**
	 * try to reduce the fields of given table to the basic types bool, unixdate, int, string, float, date, enum
	 *
	 * <code>example:
	 *
	 * Array
	 * (
	 *     [table] => test
	 *     [primary] => Array
	 * 	   [identity]=> a
	 *     [cols] => Array
	 *         (
	 *             [a] => Array
	 *                 (
	 *                     [default] => CURRENT_TIMESTAMP
	 *                     [null] =>
	 * 					   [key]	=> 'PRI'
	 *                     [length] => 0
	 *                     [type] => date
	 *                 )
	 *
	 *             [g] => Array
	 *                 (
	 *                     [default] =>
	 *                     [null] =>
	 * 					   [key]	=> 'UNI'
	 *                     [length] => 0
	 *                     [type] => unsupported:time
	 *                 )
	 *         )
	 *
	 * )
	 * </code>
	 *
	 * @param string $tableName name of the table to analyze
	 * @return unknown
	 */
	function & describe($tableName) {
		$primaryKey = array ();
		$identity = null;
		$desc = array ();
		$cols = array ();
		$sql = "SHOW COLUMNS FROM " . $tableName;
		$r = mysql_query($sql, $this->getLink());
		if (false == $r) {
			throw new Exception('model: cant describe, missing rights?');
		}
		$noResult = true;
		while ($row = mysql_fetch_assoc($r)) {
			$noResult = false;
			$data = array ();
			$data['default'] = $row['Default'];
			$data['null'] = 'NO' != $row['Null'];
			$data['length'] = 0;
			if ('auto_increment' == $row['Extra']) {
				$identity = $row['Field'];
			}
			//keys
			if ('PRI' == $row['Key']) {
				$primaryKey[] = $row['Field'];
			}
			$data['key'] = $row['Key'];

			//type
			$x = strpos($row['Type'], '(');
			$type = $x!==false ? substr($row['Type'], 0, $x) : $row['Type'];
			switch ($type) {
				case 'bit' :
					$data['type'] = 'bool';
					$data['length'] = 1;
					break;
				case 'bigint' :
				case 'int' :
				case 'smallint' :
				case 'tinyint' :
				case 'decimal':
					$data['length'] = $this->getFieldSize($row['Type']);
					$data['type'] = 'int';
					break;
				case 'char' :
				case 'varchar' :
					$data['length'] = $this->getFieldSize($row['Type']);
					$data['type'] = 'string';
					break;
				case 'text' :
					$data['type'] = 'string';
					break;
				case 'float' :
				case 'double' :
				case 'real' :
					$data['type'] = 'float';
					break;
				case 'date' :
				case 'datetime' :
				case 'time' :
				case 'timestamp' :
					$data['type'] = 'date';
					break;
				case 'set':
					$data['type'] = 'set';
					$data['values']  = 'foo';
				case 'blob':
					
					break;
			}
			$cols[$row['Field']] = $data;
		}

		if ($noResult === true) {
			throw new Exception('table does not exists in selected Database');
		}

		$desc = array (
			'table' => str_replace(array (
				$this->quoteLeft,
				$this->quoteRight
			), '', $tableName),
			'primary' => $primaryKey,
			'identity' => $identity,
			'cols' => $cols
		);
		return $desc;
	}

	/*deprecated(complex) tested. works fine
	function & describe($tableName) {
		$tableName = str_replace(array ($this->quoteLeft,$this->quoteRight), '', $tableName)
		$primaryKey= array();
		$desc= array ();
		$cols= array ();
		$sql = "Select a.COLUMN_NAME,IS_NULLABLE,COLUMN_DEFAULT,DATA_TYPE,CHARACTER_MAXIMUM_LENGTH,NUMERIC_PRECISION,NUMERIC_SCALE,CONSTRAINT_NAME,CONSTRAINT_TYPE
						from INFORMATION_SCHEMA.COLUMNS as a left join
							(SELECT a.COLUMN_NAME,b.CONSTRAINT_TYPE,b.CONSTRAINT_NAME
							from INFORMATION_SCHEMA.KEY_COLUMN_USAGE as a,INFORMATION_SCHEMA.TABLE_CONSTRAINTS as b
							where a.TABLE_NAME='".$tableName."' AND a.TABLE_SCHEMA = DATABASE()
							AND b.TABLE_NAME='".$tableName."' AND b.CONSTRAINT_SCHEMA = DATABASE()
							AND b.CONSTRAINT_NAME=a.CONSTRAINT_NAME
							)as b on (a.COLUMN_NAME = b.COLUMN_NAME)
						where a.TABLE_NAME='".$tableName."' AND a.TABLE_SCHEMA =DATABASE()";
	
		$r= mysql_query($sql,$this->getLink());
		if (false == $r) {
			throw new Exception('model: cant describe, missing rights?');
		}
		$noResult = true;
		while ($row= mysql_fetch_assoc($r)) {
			$noResult = false;
			$data= array ();
			$data['default']= empty ($row['COLUMN_DEFAULT']) ? false : $row['COLUMN_DEFAULT'];
			$data['null']= 'NO' != $row['IS_NULLABLE'];
			$data['length']= 0;
	
			if('UNIQUE' == $row['CONSTRAINT_TYPE'] ){
				if(!isset($uniqueKeys[$row['CONSTRAINT_NAME']])){
					$uniqueKeys[$row['CONSTRAINT_NAME']] = array();
				}
				$uniqueKeys[$row['CONSTRAINT_NAME']][] = $row['COLUMN_NAME'];
			}
	
			if ('PRIMARY KEY' == $row['CONSTRAINT_TYPE']) {
				$primaryKey[] = $row['COLUMN_NAME'];
			}
			switch ($row['DATA_TYPE']) {
				case 'bit' :
					$data['type']= 'bool';
					$data['length']= $row['NUMERIC_PRECISION'];
					break;
				case 'bigint':
				case 'int':
				case 'smallint':
				case 'tinyint':
					$data['length']= $row['NUMERIC_PRECISION'];
					$data['type']= 'int';
					break;
				case 'char':
				case 'varchar':
					$data['length']= $row['CHARACTER_MAXIMUM_LENGTH'];
					$data['type']= 'string';
					break;
				case 'text':
					$data['type']= 'text';
					break;
				case 'float':
				case 'double':
				case 'real':
					$data['type']= 'float';
					break;
				case 'date':
				case 'datetime':
				case 'time':
				case 'timestamp':
					$data['type']= 'date';
			}
			$cols[$row['COLUMN_NAME']]= $data;
		}
	
		if ($noResult === true) {
			throw new Exception('table does not exists in selected Database');
		}
		$unique = array();
		foreach ($uniqueKeys as $uniqueKey){
				$unique[]= $uniqueKey;
		}
		$desc= array (
			'table' => str_replace(array (
				$this->quoteLeft,
				$this->quoteRight
			), '', $tableName),
			'primary' => $primaryKey,
			'unique' => $unique,
			'cols' => $cols
		);
		return $desc;
	}
	*/
	/**
	 * a copy of the matching db-config entry in config/database.php
	 * @param $string $what spezifies what to get ... null=complete config array
	 * @return array|string
	 */
	function getConfig($what = null) {
		if (!empty ($what)) {
			return (isset ($this->dbconfig[$what])) ? $this->dbconfig[$what] : '';
		}
		return $this->dbconfig;
	}

	/**
	 * set db-config entry
	 * @param $array $config
	 */
	function setConfig($config) {
		if (empty ($this->dbconfig)) {
			$this->dbconfig = $config;
		}
	}

	/**
	* used to quote table and field names
	* @param string $s string to enquote;
	* @return string enquoted string
	*/
	function quoteName($s) {
		return $this->quoteLeft . $s . $this->quoteRight;
	}

	/**
	 * checks if given operator is valid
	 * @param string $operator
	 * @return boolean
	 */
	function isValidOperator($operator) {
		if (empty ($operator)) {
			return false;
		}
		$ops = array (
			'<=>'=>1,
			'='=>1,
			'>='=>1,
			'>'=>1,
			'<='=>1,
			'<'=>1,
			'<>'=>1,
			'!='=>1,
			'like'=>1,
			'not like'=>1,
			'is not null'=>1,
			'is null'=>1,
			'in'=>1,
			'not in'=>1,
			'between'=>1
		);
		return isset($ops[$operator]); 
	}
}



//============ lib/dbo_pdo.php =======================================================



/**
 * Contains a wrapper-class for pdo, so models can access pdo
 *
 * Kata - Lightweight MVC Framework <http://www.codeninja.de/>
 * Copyright 2007-2009 mnt@codeninja.de, gameforge ag
 *
 * Licensed under The GPL License
 * Redistributions of files must retain the above copyright notice.
 * @package kata_model
 */

/**
 * this class is used by the model to access the database itself
 * @package kata_model
 */
class dbo_pdo { //implements dbo_interface {

	/**
	 * a copy of the matching db-config entry in config/database.php
	 * @var array
	 */
	private $dbconfig = null;

	/**
	 * a placeholder for any result the database returned
	 * @var array
	 */
	private $result = null;

	/**
	 * a placeholder for the database link needed for this database
	 * @var int
	 */
	private $link = null;

	/**
	 * an array that holds all queries and some relevant information about them if DEBUG>1
	 * @var array
	 */
	private $queries = array ();

	/**
	 * constants used to quote table and field names
	 */
	private $quoteLeft = '`';
	private $quoteRight = '`';

	/**
	 * connect to the database
	 */
	function connect() {
		kataMakeTmpPath('db');

		$db = str_replace('KATATMP/',KATATMP.'db'.DS,$this->dbconfig['database']);
		$this->link = new PDO($db,null,null,!empty($this->dbconfig['options'])?$this->dbconfig['options']:null);
		if (!$this->link) {
			throw new DatabaseConnectException("Could not open database " . $db);
		}
		$this->link->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);

		if (!empty ($this->dbconfig['encoding'])) {
			$this->setEncoding($this->dbconfig['encoding']);
		}
	}

	/**
	 * if we are already connected
	 * @return bool
	 */
	function isConnected() {
		return (bool) $this->link;
	}

	/**
	 * return the current link to the database, connect first if needed
	 */
	public function getLink() {
		if (!$this->link) {
			$this->connect();
		}
		return $this->link;
	}

	/**
	 * inject database link into dbo
	 */
	public function setLink($l) {
		$this->link = $l;
	}

	/**
	 * execute this query
	 * @return mixed
	 */
	private function execute($sql) {
		if (!$this->link) {
			$this->connect();
		}

		$start = microtime(true);
		$error = '';
		$this->result = $this->link->query($sql);
		if (false === $this->result) {
			$error = implode(';',$this->link->errorInfo());
			writeLog($error . ': ' . $sql, KATA_ERROR);
			throw new DatabaseErrorException($error,$sql);
		}
		if (DEBUG > 0) {
			$this->queries[] = array (
				kataFunc::getLineInfo(),
				trim($sql),
				(false !== $this->result)?$this->result->rowCount():'',
				$error,
				 (microtime(true) - $start) . 'sec'
			);
		}
	}

	/**
	 * unused right now
	 */
	function setEncoding($enc) {
	}

	/**
	 * return numbers of rows affected by last query
	 * @return int
	 */
	private function lastAffected() {
		if (($this->link) && ($this->result instanceof PDOStatement)) {
			return $this->result->rowCount();
		}
		$null = null;
		return $null;
	}

	/**
	 * return id of primary key of last insert
	 * @return mixed
	 */
	private function lastInsertId($name=null) {
		$id = $this->link->lastInsertId($name);
		return $id;
	}

	/**
	 * return the result of the last query.
	 * @param mixed $idname if $idname is false keys are simply incrementing from 0, if $idname is string the key is the value of the column specified in the string
	 */
	private function & lastResult($idnames = false) {
		$result = array ();
		if ($this->result instanceof PDOStatement) {
			if ($idnames === false) {
				$result = $this->result->fetchAll(PDO::FETCH_ASSOC);
			} else {
				while (1) {
					$row = $this->result->fetch(PDO::FETCH_ASSOC);
					if (false === $row) {
						break;
					}
					
					$current = & $result;
					foreach ($idnames as $idname) {
						if (!array_key_exists($idname,$row)) {
							throw new InvalidArgumentException('Cant order result by field "'.$idname.'" because its nonexistant in resultset');
						}
						if ($row[$idname] === null) {
							$row[$idname] = 'null';
						}
						$current = & $current[$row[$idname]];
					} //foreach
					$current = $row;
				} //while fetch
			} //idnames
		} //rows>0
		return $result;
	}

	/**
	 * REPLACE works exactly like INSERT,
	 * except that if an old row in the table has the same value as a new row for a PRIMARY KEY or a UNIQUE  index,
	 * the old row is deleted before the new row is inserted
	 *
	 * @param string $tableName replace from this table
	 * @param array $fields name=>value pairs of new values
	 * @param string $pairs enquoted names to escaped pairs z.B.[name]='value'
	 * @return int modified rows.
	 */
	function replace($tableName, $fields, $pairs) {
		return $this->query('REPLACE INTO ' . $tableName . ' SET ' . $pairs);
	}

	/**
	 * execute query and return useful data depending on query-type
	 *
	 *  	SELECT / SHOW 											=> resultset array
	 * 		REPLACE / UPDATE / DELETE / ALTER 						=> affected rows (int)
	 * 		INSERT													=> last insert id (int)
	 * 		RENAME / LOCK / UNLOCK / TRUNCATE /SET / CREATE / DROP	=> Returns if the operation was successfull (boolean)
	 *
	 * @param string $s sql-statement
	 * @param string $idname which field-value to use as the key of the returned array (false=dont care)
	 * @return array
	 */
	function & query($s, $idnames = false, $fields = false) {
		$result = null;
		switch ($this->getSqlCommand($s)) {
			case 'replace' :
			case 'update' :
			case 'delete' :
			case 'alter' :
			case 'call':
				$this->execute($s);
				$result = $this->lastAffected();
				break;
			case 'insert' :
				$this->execute($s);
				$result = $this->lastInsertId();
				break;
			case 'select' :
			case 'show' :
				if (is_string($idnames)) {
					$idnames = array (
						$idnames
					);
				}
				$this->execute($s);
				$result = $this->lastResult($idnames, $fields);
				break;
			default :
				$this->execute($s);
				$result = $this->result;
				break;
		}
		return $result;
	}

	/**
	 * escape the given string so it can be safely appended to any sql
	 * @param string $sql string to escape
	 * @return string
	 */
	function escape($sql) {
		$sql = $this->link->quote($sql);
		return substr($sql,1,-1);
	}

	/**
	 * return sql needed to convert unix timestamp to datetime
	 * @param integer $t unixtime
	 * @return string
	*/
	function makeDateTime($t) {
		return 'datetime('.$t.', \'unixepoch\');';
	}

	/**
	 * output any queries made, how long it took, the result and any errors if DEBUG>1
	 */
	function __destruct() {
		if (DEBUG > 0) {
			array_unshift($this->queries, array (
				'line',
				'',
				'affected',
				'error',
				'time'
			));
			kataDebugOutput($this->queries, true);
		}
		if ($this->link) {
			unset($this->link);
		}
	}

	private function getFieldSize($str) {
		$x1 = strpos($str, '(');
		$x2 = strpos($str, ')');
		if ((false !== $x1) && (false !== $x2)) {
			return substr($str, $x1 +1, $x2 - $x1 -1);
		}
		return 0;
	}

	/**
	 * return the Sql-Command of given Query
	 * @param string $sql query
	 * @return string Sql-Command
	 */
	private function getSqlCommand($sql) {
		$sql = str_replace(array (
			"(",
			"\t",
			"\n"
		), " ", $sql);
		$Sqlparts = explode(" ", trim($sql));
		return strtolower($Sqlparts[0]);
	}

	/**
	 * build a sql-string that returns first matching row
	 * @param string $sql query
	 * @param string $perPage expression
	 * @return string (limited) Query
	 */
	function getFirstRowQuery($sql, $perPage) {
		return sprintf('%s LIMIT %d', $sql, $perPage);
	}

	/**
	 * build a sql-string that returns paged data
	 * @return string finished query
	 */
	function getPageQuery($sql, $page, $perPage) {
		return sprintf('%s LIMIT %d,%d', $sql, ($page -1) * $perPage, $perPage);
	}

	/**
	 * try to reduce the fields of given table to the basic types bool, unixdate, int, string, float, date, enum
	 *
	 * <code>example:
	 *
	 * Array
	 * (
	 *     [table] => test
	 *     [primary] => Array
	 * 	   [identity]=> a
	 *     [cols] => Array
	 *         (
	 *             [a] => Array
	 *                 (
	 *                     [default] => CURRENT_TIMESTAMP
	 *                     [null] =>
	 * 					   [key]	=> 'PRI'
	 *                     [length] => 0
	 *                     [type] => date
	 *                 )
	 *
	 *             [g] => Array
	 *                 (
	 *                     [default] =>
	 *                     [null] =>
	 * 					   [key]	=> 'UNI'
	 *                     [length] => 0
	 *                     [type] => unsupported:time
	 *                 )
	 *         )
	 *
	 * )
	 * </code>
	 *
	 * @param string $tableName name of the table to analyze
	 * @return unknown
	 */
	function & describe($tableName) {
		$primaryKey = array ();
		$identity = null;
		$desc = array ();
		$cols = array ();
		$sql = "SHOW COLUMNS FROM " . $tableName;
		$r = mysql_query($sql, $this->getLink());
		if (false == $r) {
			throw new Exception('model: cant describe, missing rights?');
		}
		$noResult = true;
		while ($row = mysql_fetch_assoc($r)) {
			$noResult = false;
			$data = array ();
			$data['default'] = $row['Default'];
			$data['null'] = 'NO' != $row['Null'];
			$data['length'] = 0;
			if ('auto_increment' == $row['Extra']) {
				$identity = $row['Field'];
			}
			//keys
			if ('PRI' == $row['Key']) {
				$primaryKey[] = $row['Field'];
			}
			$data['key'] = $row['Key'];

			//type
			$x = strpos($row['Type'], '(');
			$type = $x!==false ? substr($row['Type'], 0, $x) : $row['Type'];
			switch ($type) {
				case 'bit' :
					$data['type'] = 'bool';
					$data['length'] = 1;
					break;
				case 'bigint' :
				case 'int' :
				case 'smallint' :
				case 'tinyint' :
				case 'decimal':
					$data['length'] = $this->getFieldSize($row['Type']);
					$data['type'] = 'int';
					break;
				case 'char' :
				case 'varchar' :
					$data['length'] = $this->getFieldSize($row['Type']);
					$data['type'] = 'string';
					break;
				case 'text' :
					$data['type'] = 'string';
					break;
				case 'float' :
				case 'double' :
				case 'real' :
					$data['type'] = 'float';
					break;
				case 'date' :
				case 'datetime' :
				case 'time' :
				case 'timestamp' :
					$data['type'] = 'date';
					break;
				case 'set':
					$data['type'] = 'set';
					$data['values']  = 'foo';
				case 'blob':

					break;
			}
			$cols[$row['Field']] = $data;
		}

		if ($noResult === true) {
			throw new Exception('table does not exists in selected Database');
		}

		$desc = array (
			'table' => str_replace(array (
				$this->quoteLeft,
				$this->quoteRight
			), '', $tableName),
			'primary' => $primaryKey,
			'identity' => $identity,
			'cols' => $cols
		);
		return $desc;
	}

	/**
	 * a copy of the matching db-config entry in config/database.php
	 * @param $string $what spezifies what to get ... null=complete config array
	 * @return array|string
	 */
	function getConfig($what = null) {
		if (!empty ($what)) {
			return (isset ($this->dbconfig[$what])) ? $this->dbconfig[$what] : '';
		}
		return $this->dbconfig;
	}

	/**
	 * set db-config entry
	 * @param $array $config
	 */
	function setConfig($config) {
		if (empty ($this->dbconfig)) {
			$this->dbconfig = $config;
		}
	}

	/**
	* used to quote table and field names
	* @param string $s string to enquote;
	* @return string enquoted string
	*/
	function quoteName($s) {
		return $s;
	}

	/**
	 * checks if given operator is valid
	 * @param string $operator
	 * @return boolean
	 */
	function isValidOperator($operator) {
		if (empty ($operator)) {
			return false;
		}
		$ops = array (
			'<=>'=>1,
			'='=>1,
			'>='=>1,
			'>'=>1,
			'<='=>1,
			'<'=>1,
			'<>'=>1,
			'!='=>1,
			'like'=>1,
			'not like'=>1,
			'is not null'=>1,
			'is null'=>1,
			'in'=>1,
			'not in'=>1,
			'between'=>1
		);
		return isset($ops[$operator]);
	}
}



//============ lib/kata_functions.php =======================================================




/**
 * several functions needed by kata
 *
 * Kata - Lightweight MVC Framework <http://www.codeninja.de/>
 * Copyright 2007-2009 mnt@codeninja.de, gameforge ag
 *
 * Licensed under The GPL License
 * Redistributions of files must retain the above copyright notice.
 * @package kata_internal
 */

/**
 * internal function to send kata debug info to the browser. just define your own function if you want firebug or something like it
 */
if (!function_exists('kataDebugOutput')) {
	/**
	 * @ignore
	 * @param mixed $var variable to dump
	 * @param bool $isTable if variable is an array we use a table to display each line
	 */
	function kataDebugOutput($var = null, $isTable = false) {
		kataFunc :: debugOutput($var, $isTable);
	}
}

/**
 * create a directory in TMPPATH and check if its writable
 */
function kataMakeTmpPath($dirname) {
	if (!file_exists(KATATMP . $dirname . DS)) {
		if (!mkdir(KATATMP . $dirname, 0770, true)) {
			throw new Exception("makeTmpPath: cant create temporary path $dirname");
		}
	}
	if (!is_writable(KATATMP . $dirname)) {
		throw new Exception("makeTmpPath: " . KATATMP . "$dirname is not writeable");
	}
}

/**
 * static wrapper class so dont we pollute any namespace anymore
 */
class kataFunc {

	/**
	 * return the shortend path of the file currently begin executed
	 * 
	 * @return string
	 */
	function getLineInfo() {
		return;
		$nestLevel = -1;
		$bt = debug_backtrace();
		while ($nestLevel++ < count($bt)) {
			if (empty ($bt[$nestLevel]['file']))
				continue;
			foreach (array (
					LIB,
					ROOT . 'utilities' . DS
				) as $test) {
				if (substr($bt[$nestLevel]['file'], 0, strlen($test)) == $test)
					continue 2;
			}
			break;
		}
		return basename($bt[$nestLevel]['file']) . ':' . $bt[$nestLevel]['line'];
	}

	/**
	 * return stacktrace-like information about the given variable
	 * 
	 * @return string
	 */
	function getValueInfo($val) {
		if (is_null($val)) {
			return 'null';
		}
		if (is_array($val)) {
			return 'array[' . count($val) . ']';
		}
		if (is_bool($val)) {
			return ($val ? 'true' : 'false');
		}
		if (is_float($val) || is_int($val) || is_long($val) || is_real($val)) {
			return 'num:' . $val;
		}
		if (is_string($val)) {
			return 'string[' . strlen($val) . ']=' . substr($val, 0, 16);
		}
		if (is_resource($val)) {
			return 'resource' . get_resource_type($val);
		}
		if (is_object($val)) {
			return 'object';
		}
		return '?';
	}

	/**
	 * include files depending on name, if class is needed 
	 *
	 * @param string $cname classname
	 */
	static function autoloader($classname) {
		$cname = strtolower($classname);
		switch ($cname) {
			case 'appmodel' :
				if (file_exists(ROOT . 'models' . DS . 'app_model.php')) {
					//require ROOT . 'models' . DS . 'app_model.php';
				} else {
					//require LIB . 'models' . DS . 'app_model.php';
				}
				break;

			case 'appcontroller' :
				if (file_exists(ROOT . 'controllers' . DS . 'app_controller.php')) {
					//require ROOT . 'controllers' . DS . 'app_controller.php';
				} else {
					//require LIB . 'controllers' . DS . 'app_controller.php';
				}
				break;

				/*** GF_SPECIFIC ***/
			case substr($classname, 0, 3) == 'GF_' or substr($classname, 0, 5) == 'Zend_' or substr($classname, 0, 6) == 'ZendX_' :
				//require str_replace('_', '/', $classname) . '.php';
				break;
				/*** /GF_SPECIFIC ***/

			case substr($cname, -9, 9) == 'component' :
				$cname = substr($cname, 0, -9);
				if (file_exists(LIB . 'controllers' . DS . 'components' . DS . $cname . '.php')) {
					//require LIB . 'controllers' . DS . 'components' . DS . $cname . '.php';
					break;
				}
				//require ROOT . 'controllers' . DS . 'components' . DS . $cname . '.php';
				break;

			case substr($cname, -6, 6) == 'helper' :
				$cname = substr($cname, 0, -6);
				if (file_exists(LIB . 'views' . DS . 'helpers' . DS . $cname . '.php')) {
					//require LIB . 'views' . DS . 'helpers' . DS . $cname . '.php';
					break;
				}
				//require ROOT . 'views' . DS . 'helpers' . DS . $cname . '.php';
				break;

			case substr($cname, -7, 7) == 'utility' :
				if (file_exists(LIB . 'utilities' . DS . $cname . '.php')) {
					//require LIB . 'utilities' . DS . $cname . '.php';
					break;
				}
				//require ROOT . 'utilities' . DS . $cname . '.php';
				break;

			case file_exists(LIB . $cname . '.php') :
				//require LIB . $cname . '.php';
				break;

			case 'scaffoldcontroller' :
				//require LIB . 'controllers' . DS . 'scaffold_controller.php';
				break;

		}
	}

	/**
	 * the default debug output function. outputs print_r alike dump
	 * @param mixed $var variables to output
	 * @param bool $isTable if true variables are output in a table
	 */
	static function debugOutput($var = null, $isTable = false) {
		if (DEBUG < 2) {
			return;
		}
		if ($isTable) {
			echo '<table style="text-align:left;direction:ltr;border:1px solid red;color:black;background-color:#e8e8e8;border-collapse:collapse;text-align:left;direction:ltr;">';
			foreach ($var as $row) {
				echo '<tr>';
				foreach ($row as $col) {
					echo '<td style="border:1px solid red;padding:2px;">' . $col . '</td>';
				}
				echo '</tr>';
			}
			echo '</table>';
		} else {
			echo '<pre style="white-space:pre-wrap;text-align:left;direction:ltr;border:1px solid red;color:black;background-color:#e8e8e8;padding:3px;text-align:left;direction:ltr;">' . $var . '</pre>';
		}
	}

	/**
	 * shorthand function to read from serveral code caches
	 * @param string $id key to fetch
	 * @return bool success
	 */
	static function memoryRead($id) {
		if (function_exists('apc_fetch')) {
			return apc_fetch($id);
		}
		if (function_exists('eaccelerator_get')) {
			//eacc? bloody hell...
			$data = eaccelerator_get($id);
			if($data!==null) {
				$data = @unserialize($data); 
			}
			return $data;
		}
		if (function_exists('xcache_get')) {
			return xcache_get($id);
		}
		return false;
	}

	/**
	 * shorthand function to read from serveral code caches
	 * @param string $id key to fetch
	 * @param mixed $value value(s) to store. if FALSE key will be wiped from memory
	 * @return bool success
	 */
	static function memoryWrite($id, $value) {
		if (false !== $value) {
			if (function_exists('apc_store')) {
				return apc_store($id, $value,300);
			}
			if (function_exists('eaccelerator_put')) {
				return eaccelerator_put($id, serialize($value), 300);
			}
			if (function_exists('xcache_set')) {
				return xcache_set($id, $value, 300);
			}
			return false;
		}
		//yes, i'm checking for the wrong function
		if (function_exists('apc_store')) {
			return apc_delete($id);
		}
		if (function_exists('eaccelerator_put')) {
			return eaccelerator_rm($id);
		}
		if (function_exists('xcache_set')) {
			return xcache_unset($id);
		}
		return false;
	}

} //class

spl_autoload_register('kataFunc::autoloader');



//============ lib/kataext.php =======================================================


/**
 * Add normal callable methods to this class at runtime
 *
 * Kata - Lightweight MVC Framework <http://www.codeninja.de/>
 * Copyright 2007-2009 mnt@codeninja.de, gameforge ag
 *
 * Licensed under The GPL License
 * Redistributions of files must retain the above copyright notice.
 * @package kata_internal
 */

/**
 * you can freely add methods to this class at runtime
 *
 * <code>Example:
 * class Foo extends kataExt;
 * $foo = new Foo;
 * Foo->_('bla',function(){echo'bla';})->_('blubb',function(){echo'blubb'));
 * </code>
 *
 * @package kata_internal
 */
class kataExt {

	/**
	 * stores all added methods
	 * @var array
	 */
    private $__addedMethods = array();

    public function __call($name, $args) {
        $class = get_class($this);
        do {
            if (array_key_exists($class, $this->__addedMethods)
                    && array_key_exists($name, $this->__addedMethods[$class]))
                break;

            $class = get_parent_class($class);
        } while ($class !== false);

        if ($class === false)
            throw new Exception("Method not found");

        $func = $this->__addedMethods[$class][$name];
        array_unshift($args, $this);

        return call_user_func_array($func, $args);
    }

    public function _($methodName, $method) {
        $class = get_called_class();
        if (!array_key_exists($class, $this->__addedMethods))
            $this->__addedMethods[$class] = array();

        $this->__addedMethods[$class][$methodName] = $method;

		return $this;
    }

}



//============ lib/kataglob.php =======================================================



/**
 * Contains kataGlob
 *
 * Kata - Lightweight MVC Framework <http://www.codeninja.de/>
 * Copyright 2007-2009 mnt@codeninja.de, gameforge ag
 *
 * Licensed under The GPL License
 * Redistributions of files must retain the above copyright notice.
 * @package kata_internal
 */

/**
 * class for easy handling of global variables
 * @package kata_internal
 */
class kataGlob {

	/**
	 * storage array
	 * 
	 * @var array
	 * @access private
	 */
	private static $container = array ();

	/**
	 * get variable (if set) or return null
	 * 
	 * @param string $name name of the variable
	 */
	function get($name) {
		if (isset (self :: $container[$name])) {
			return self :: $container[$name];
		}
		return null;
	}

	/**
	 * set variable
	 * 
	 * @param string $name name of the variable
	 * @param mixed $value contents
	 */
	function set($name, $value) {
		self :: $container[$name] = $value;
	}

	/**
	 * unset variable
	 * 
	 * @param string $name name of the variable
	 */
	function remove($name) {
		unset(self :: $container[$name]);
	}

	/**
	 * find out if given variable exists (=is set)
	 * 
	 * @param string $name name of the variable
	 * @return bool true if variable is set 
	 */
	function exists($name) {
		return isset (self :: $container[$name]);
	}

}



//============ lib/katahardtyped.php =======================================================


/**
 * type-enforcement class. fast. hack. 
 * @package kata
 */


/**
 * type-enforcement class. just base your class on this class
 * - dont forget to call parent::__construct() if you override the c'tor
 * - member-variables are also protected automatically
 * 
 * @package kata_internal
 */
class kataHardtyped {
      protected $___hardTypes = array();
      protected $___hardVars = array();

      function __construct() {
         $vars = get_class_vars(get_class($this));
         foreach ($vars as $name=>$value) {
                 $this->_hardVars[$name] = $value;
                 $this->_hardTypes[$name] = gettype($value);
                 unset($this->$name);
         }
      }

      function __get($name) {
          if (isset($this->___hardVars[$name])) {
             return $this->___hardVars[$name];
          }
          throw new Exception("'$name' is no known property of class '".get_class($this)."'");
      }
      
      function __set($name,$value) {
          if (!isset($this->___hardVars[$name])) {
             $this->___hardVars[$name] = $value;
             $this->___hardTypes[$name] = gettype($value);
             return;
          }
          if (gettype($value) != $this->___hardTypes[$name]) {
             throw new InvalidArgumentException("setting '$name' to type '".gettype($value)."' disallowed, is of type '".$this->___hardTypes[$name]."'");
          }
          $this->___hardVars[$name] = $value;
      }
      
}



//============ lib/katareg.php =======================================================



/**
 * Contains the registry: can read/write configuration settings and persists them on disk
 *
 * Kata - Lightweight MVC Framework <http://www.codeninja.de/>
 * Copyright 2007-2009 mnt@codeninja.de, gameforge ag
 *
 * Licensed under The GPL License
 * Redistributions of files must retain the above copyright notice.
 * @package kata_internal
 */

/**
 * registry, a configuration object that persists itself on disk.
 * split keys into individial sections with a dot. if you read a section an array will be returned with all keys in that section
 *
 * <code>
 * kataReg::set('my.stuff',1);
 * kataReg::set('my.foo',2);
 * var_dump(kataReg::get('my.stuff'); // =1
 * var_dump(kataReg::get('my')); // =array('my'=>array('stuff'=>1,'foo'=>2))
 * </code>
 * @package kata_internal
 */
class kataReg {

	/**
	 * array to save objects of any classed created
	 * @var array
	 */
	static protected $dataArr= array ();

	/**
	 * array to save objects of any classed created
	 * @var array
	 */
	static protected $dataArrTemp = array ();

/**
 * did we already load data from disk?
 * @var boolean
 */
	static protected $didLoadData= false;

/**
 * load data from disk and put it into $dataArr
 */
	static protected function loadData() {
		if (self :: $didLoadData)
			return;

		$file= KATATMP.'cache'.DS.CACHE_IDENTIFIER.'-kataReg';
		if (file_exists($file)) {
			$data= array ();
			include $file;
			self :: $dataArr= $data;
			self :: $didLoadData= true;
		}
	}

/**
 * save data to disk. throws an exception if writing failed and DEBUG>0
 */
	static protected function saveData() {
		kataMakeTmpPath('cache');
		$data = '<? $data='.var_export(self :: $dataArr,true).';';

		if (false === file_put_contents(KATATMP.'cache'.DS.CACHE_IDENTIFIER.'-kataReg', $data)) {
			if (DEBUG > 0) {
				throw new Exception('katareg: cannot write data. wrong rights?');
			}
			return false;
		}
		return true;
	}

/**
 * get variable from registry. use a dot to split key into individual sections
 * 
 * <samp>get('showShop',false)</samp>
 * 
 * @param string $id key to read
 * @param mixed $default default value to use if key is not yet in the registr
 * @return mixed value of key or default value (which is null)
 */
	static public function get($id, $default= null) {
		self :: loadData();

		$temp= explode('.', $id);
		if (count($temp) == 1) {
			if (isset (self :: $dataArr[$id])) {
				return self :: $dataArr[$id];
			}
		} else {
			$start = & self::$dataArr;
			foreach ($temp as $keyname) {
				if (isset($start[$keyname])) {
					$start= & $start[$keyname];
				} else {
					return $default;
				}
			}
			return $start;
		}
		return $default;
	}

/**
 * set variable inside registry. use a dot to split key into individual sections
 * @param string $id key to write
 * @param mixed $vars any value
 */
	static public function set($id, $vars) {
		self :: loadData();

		$temp= explode('.', $id);
		if (count($temp) == 1) {
			self :: $dataArr[$id]= $vars;
		} else {
			$start= & self :: $dataArr;
			foreach ($temp as $keyname) {
				$start= & $start[$keyname];
			}
			$start= $vars;
		}

		return self :: saveData();
	}

/**
 * remove variable inside registry. use a dot to split key into individual sections
 * @param string $id key to write
 */	
	static public function delete($id) {
		self :: loadData();
		
		$temp= explode('.', $id);
		if (count($temp) == 1) {
			unset(self :: $dataArr[$id]);
		} else {
			$start= & self :: $dataArr;
			foreach ($temp as $keyname) {
				$start= & $start[$keyname];
			}
			unset($start);
		}

		return self :: saveData();
	}

}



//============ lib/model.php =======================================================




/**
 * The Base Model. used to access the database (via dbo_ objects)
 *
 * Kata - Lightweight MVC Framework <http://www.codeninja.de/>
 * Copyright 2007-2009 mnt@codeninja.de, gameforge ag
 *
 * Licensed under The GPL License
 * Redistributions of files must retain the above copyright notice.
 * @package kata_model
 */

/**
 * validation string define to check if string is not empty
 * @deprecated 31.04.2009
 */
define('VALID_NOT_EMPTY', 'VALID_NOT_EMPTY');
/**
 * @deprecated 31.04.2009
 * validation string define to check if string is numeric
 */
define('VALID_NUMBER', 'VALID_NUMBER');

/**
 * @deprecated 31.04.2009
 * validation string define to check if string is an email-address
 */
define('VALID_EMAIL', 'VALID_EMAIL');

/**
 * @deprecated 31.04.2009
 * validation string define to check if string is a numeric year
 */
define('VALID_YEAR', 'VALID_YEAR');

/**
 * The base model-class that all models derive from
 * @package kata_model
 * @todo review replace() (emulation in mssql vs. remove), query only supports basic Commands (but easily can support all)
 */
class Model {
	/**
	 * which connection to use of the ones defines inside config/database.php
	 * 
	 * @var string
	 */
	public $connection = 'default';

	/**
	 * whether to use a specific table for this model. false if not specific, otherwise the tablename
	 * 
	 * @var string
	 */
	public $useTable = false;

	/**
	 * which fieldname to use for primary key. is 'id' by default, override it in your
	 * model wo 'table_id' or 'tableId' as you like.
	 * 
	 * @var string 
	 */
	public $useIndex = 'id';

	/**
	 * containts the appropriate class used to access the database
	 * 
	 * @var object
	 */
	protected $dboClass = null;

	/**
	 * convenience method for writeLog
	 * 
	 * @param string $what what to log
	 * @param string $where where to log (KATA_DEBUG OR KATA_ERROR)
	 */
	function log($what, $where) {
		writeLog($what, $where);
	}

	/**
	 * lazy setup dbo the first time its used
	 * 
	 * @return object intialized dbo-class
	 */
	function dbo() {
		if (null === $this->dboClass) {
			$this->setupDbo($this->connection);
		}
		return $this->dboClass;
	}

	/**
	 * load dbo-class, give dbconfig to class
	 * 
	 * @param string $connName name of the connection to use
	 */
	protected function setupDbo($connName) {
		//require_once ROOT . 'config' . DS . 'database.php';
		if (!class_exists('DATABASE_CONFIG')) {
			throw new Exception('Incorrect config/database.php');
		}

		$dbvars = get_class_vars('DATABASE_CONFIG');
		if (empty ($dbvars[$connName])) {
			throw new DatabaseConnectException("Cant find configdata for database-connection '$connName'");
		}
		$dboname = 'dbo_' . $dbvars[$connName]['driver'];

		if (!class_exists($dboname)) {
			//require LIB.$dboname.'.php';
		}
		$this->dboClass = classRegistry :: getObject($dboname, $connName);
		$this->dboClass->setConfig($dbvars[$connName]);
	}

	/**
	 * allowes you to switch the current connection dynamically.
	 *
	 * @param string $connName name of the new connection to use
	 */
	function changeConnection($connName) {
		$this->connection = $connName;
		$this->setupDbo($connName);
	}

	/**
	 * return currently used connection name (see database.php)
	 *
	 * 	 * @return string
	 */
	function getConnectionName() {
		return $this->connection;
	}

	/**
	 * getter for the config options of the current model
	 * 
	 * @param string $what which part of the config you want returned. if null the whole config-array is returned
	 * @var string
	 */
	public function getConfig($what = null) {
		return $this->dbo()->getConfig($what);
	}

	/**
	 * getter for the database link of the current model. whats returned here depends greatly on the dbo-class
	 * 
	 * @var resource
	 */
	public function getLink() {
		return $this->dbo()->getLink();
	}

	/**
	 * utility function to generate correct tablename
	 *
	 * @param string $n tablename to use. if null uses $this->useTable. if that is also null uses modelname.
	 * @param bool $withPrefix if true adds prefix and adds the correct quote-signs to the name
	 * @return string
	 */
	public function getTableName($n = null, $withPrefix = true, $quoted = true) {
		$name = get_class($this);

		if ($withPrefix) {
			if (null !== $n) {
				return ($quoted ? $this->quoteName($this->getPrefix() . $n) : $this->getPrefix() . $n);
			}
			if ($this->useTable) {
				return ($quoted ? $this->quoteName($this->getPrefix() . $this->useTable) : $this->getPrefix() . $this->useTable);
			}
			return ($quoted ? $this->quoteName($this->getPrefix() . strtolower($name)) : $this->getPrefix() . strtolower($name));
		}

		if (null !== $n) {
			return $n;
		}
		if ($this->useTable) {
			return $this->useTable;
		}
		return strtolower($name);
	}

	/**
	 * return the prefix configured for this connection
	 *
	 * @return string
	 */
	public function getPrefix() {
		return $this->dbo()->getConfig('prefix');
	}

	/**
	 * execute an actual query on the database
	 * 
	 * @param string $s the sql to execute
	 * @param string $idnames can be used to have the keys of the returned array equal the value ob the column given here (instead of just heaving 0..x as keys)
	 * @return mixed returns array with results OR integer with insertid OR integer updated rows OR null
	 */
	function & query($s = null, $idnames = false) {
		if (empty ($s)) {
			throw new InvalidArgumentException('no query is specified');
		}
		return $this->dbo()->query($s, $idnames);
	}

	/**
	 * Do a query that is cached via the cacheUtility. caching is done 'dumb', so altering the database wont invalidate the cache
	 *
	 * A word of warning: If you dont supply an $idname, queries on different lines will result in different cachefiles
	 *
	 * @param string $s sql-string
	 * @param string $idname if set the key of the array is set to the value of this field of the result-array. So the result is not numbered from 0..x but for example the value of the primary key
	 * @param string $cacheid the id used to store this query in the cache. if ommited we try to build a suitable key
	 * @param int $ttl time to live in seconds (0=infinite)
	 */
	function & cachedQuery($s, $idname = false, $cacheid = false, $ttl = 0) {
		if (!$cacheid) {
			$bt = debug_backtrace();
			$cacheid = $bt[1]['class'] . '.' . $bt[1]['function'] . '.' . $bt[1]['line'];

			if (isset ($bt[1]['args']) && is_array($bt[1]['args'])) {
				foreach ($bt[1]['args'] as $arg) {
					if (null === $arg) {
						$cacheid .= '-null';
					}
					elseif (false === $arg) {
						$cacheid .= '-false';
					} else {
						$cacheid .= '-' . $arg;
					}
				}
			}
		}

		$cacheUtil = getUtil('Cache');

		$res = $cacheUtil->read($cacheid);
		if (false !== $res) {
			return $res;
		}

		$res = $this->query($s, $idname);
		$cacheUtil->write($cacheid, $res, $ttl);
		return $res;
	}

	/**
	 * escape possibly harmful strings so you can safely append them to an sql-string
	 * 
	 * @param string $s string to escape
	 * @return string escaped string
	 */
	function escape($s) {
		return $this->dbo()->escape($s);
	}

	/**
	 * enclose string in single-quotes AND escape it
	 * 
	 * @param string $s string to escape
	 * @return string quoted AND escaped string
	 */
	function quote($s) {
		return '\'' . $this->escape($s) . '\'';
	}

	/**
	 * enclose table- or fildname in whatever the database needs (depends on used dbo)
	 * 
	 * @param string $s field or tablename
	 * @return string escaped name 
	 */
	function quoteName($s) {
		return $this->dbo()->quoteName($s);
	}

	/**
	 * turn a unix timestamp into datetime-suitable SQL-function like FROM_UNIXTIME(timestamp) (depends on used dbo)
	 * 
	 * @param integer $t unix timestamp
	 * @return string sql-statement
	 */
	function makeDateTime($t) {
		return $this->dbo()->makeDateTime($t);
	}

	/**
	 * turn the given array into "name=value,name=value" pairs suitable for INSERT or UPDATE-sqls. strings are automatically quoted+escaped, fieldnames also
	 * 
	 * @param array $params the data
	 * @return string �foo�='bar',�baz�='ding'
	 */
	function pairs($params) {
		if (empty ($params)) {
			throw new InvalidArgumentException('no pairs are specified');
		}
		if (!is_array($params)) {
			throw new InvalidArgumentException('pairs: params must be an array');
		}
		$out = '';
		foreach ($params as $v => $k) {
			if (is_null($k)) {
				$out .= $this->quoteName($v) . "=NULL,";
			} else {
				$out .= $this->quoteName($v) . "=" . $this->quote($k) . ",";
			}
		}
		return substr($out, 0, strlen($out) - 1);
	}

	/**
	 * construct a suitable where-clause for a query from an array of conditions
	 * 
	 * @param mixed $id
	 * @param string $tableName needed to generate a primary key name
	 * @return string full 'WHERE x=' string
	 */
	function getWhereString($id, $tableName, $allowKey = false) {
		if (empty ($id)) {
			return '';
		}
		if (($tableName!==null) && !is_string($tableName)) {
			throw new InvalidArgumentException('tableName needs to be null or string');
		}

		if ($allowKey) {
			if (!is_array($id) && (is_numeric($id) || is_string($id))) {
				$id = array (
					$this->useIndex => $id
				);
			}
		}

		return ' WHERE ' . $this->getWhereStringHelper($id, $tableName);
	}

	/**
	 * do the actual work for getWhereString(). analyse strings and branch for arrays
	 * @param mixed $id
	 * @param string $tableName needed to generate a primary key name
	 * @return string 'x=y AND x=z' string without 'WHERE'
	 */
	private function getWhereStringHelper(& $id, $tableName) {
		if (!is_array($id)) {
			throw new InvalidArgumentException('condition needs to have array() as value');
		}

		$orMode = false;
		foreach ($id as $value) {
			if (is_string($value) && (strtolower($value) === 'or')) {
				$orMode = true;
				break;
			}
		}

		reset($id);
		$num = 0;
		$s = '';
		foreach ($id as $name => $value) {
			$name = trim($name);
			$num++;
			//dont count or || and
			if (!is_array($value) && (('or' == strtolower($value)) || ('and' == strtolower($value)))) {
				$num--;
				continue;
			}
			//place or/and between two conditions
			if ($num > 1) {
				$s .= ($orMode ? ' OR ' : ' AND ');
			}
			//throw Exception on key is numeric. Since we continued on value or || and, numeric key is only valid for new sub-condition=array
			if (is_numeric($name) && !is_array($value)) {
				throw new InvalidArgumentException('condition-array needs to have strings as keys');
			}
			//fieldName
			$fieldName = $this->quoteName($name);
			//operator
			$tempOperator = strpos($name, ' ');
			$operator = '=';

			if ($tempOperator !== false) {
				$fieldName = $this->quoteName(trim(substr($name, 0, $tempOperator)));
				$operator = strtolower(trim(substr($name, $tempOperator)));
			}
			//fieldValue depends on operator
			$fieldValue = '';
			if (!is_array($value)) {
				if ($value === false) {
					$value = '0';
				}
				//On fieldValue = null we use nullsensitive operators
				if (is_null($value)) {
					$fieldValue = 'null';
					if ($operator == '=' || $operator == 'is') {
						$operator = 'is null';
					} else
						if ($operator == '!=' || $operator == '<>' || $operator == 'is not') {
							$operator = 'is not null';
						}
				} else {
					$fieldValue = $this->quote($value);
				}
			}
			if ($operator == 'is null' || $operator == 'is not null') {
				$fieldValue = '';
			}
			if ($operator == 'in' || $operator == 'not in') {
				if (!is_array($value)) {
					throw new InvalidArgumentException($operator . ' operator needs to have array() as value');
				}
				// $value may be empty
				if (empty($value)) {
					if ($orMode) {
						$num--;
						continue;
					} else {
						return '0';
					}
				}

				$fieldValue = '( ';
				foreach ($value as $val) {
					if ($fieldValue != '( ') {
						$fieldValue .= ' , ';
					}
					$fieldValue .= $this->quote($val);
				}
				$fieldValue .= ' )';
			}
			if ($operator == 'between') {
				if (!is_array($value)) {
					throw new InvalidArgumentException($operator . 'operator needs to have array() as value');
				}
				$fieldValue = '';
				foreach ($value as $val) {
					if ($fieldValue != '') {
						$fieldValue .= ' and ';
					}
					$fieldValue .= $this->quote($val);
				}
			}
			//seperate subcondition
			if ($operator == '=' && $fieldValue == '' && is_array($value)) {
				$s .= ' ( ' . $this->getWhereStringHelper($value, $tableName) . ' ) ';
				continue;
			}
			if (!$this->dbo()->isValidOperator($operator)) {
				throw new InvalidArgumentException('operator:\'' . $operator . '\' is not supported');
			}

			$s .= $fieldName . ' ' . $operator . ' ' . $fieldValue;
		}
		return $s;
	}

	/**
	 * select rows using various methods (see $method for full list)
	 * 
	 * $method can be:
	 * 'all': return all matching rows as an array of results
	 * 'list': return matching rows as a multidimensional array that use keynames from your supplied 'listby' fields. similar to $idfields of query()  
	 * 'count': return number of rows matching
	 * 'first': return first matching row
	 * 
	 * $conditions is used to construct a suitable WHERE-clause. fieldnames are default AND connected, just add an 'or' somewhere to change that. 
	 * Example: 'conditions'=>array('field1'=>5',array('name LIKE'=>'%eter','or','name LIKE'=>'%eta'))
	 *
	 * $order: array of fieldnames used to construct ORDER BY clause
	 * 
	 * $group: array of fieldnames used to construct GROUP BY clause
	 * 
	 * $listby: see above
	 * 
	 * $limit: how many rows to return (per page)
	 * $page: which page to return (if $limit is set), first page is 1
	 *
	 * <code>
	 * $rows = $this->find('all',array(
	 * 	'conditions' => array( // WHERE conditions to use. default all elements are AND, just add 'or' to the condition-array to change this
	 * 		'field' => $thisValue,
	 * 		'or',
	 * 		'field2'=>$value2,
	 * 		'field3'=>$value3,
	 * 		'field4'=>$value4
	 * 	),
	 * 	'fields' => array( //array of field names that we should return. first field name is used as array-key if you use method 'list' and listby is unset
	 * 		'field1',
	 * 		'field2'
	 * 	),
	 *  'order' => array( //string or array defining order. you can add DESC or ASC
	 * 		'created',
	 * 		'field3 DESC'
	 * 	),
	 *  'group' => array( //fields to GROUP BY
	 * 		'field'
	 *	),
	 *  'listby' => array( //only if find('list'): fields to arrange result-array by
	 *		'field1','field2'
	 *  ),
	 *  'limit' => 50, //int, how many rows per page
	 *  'page' => 1, //int, which page, starting at 1
	 * ),'mytable');
	 * </code>
	 *
	 * @param string $method can be 'all','list','count','first','neighbors'
	 * @param array $params see example
	 * @param mixed $tableName string or null to use modelname
	 */
	function find($method = '', $params = array (), $tableName = null) {
		$orderBy = '';
		if (!empty ($params['order'])) {
			if (!is_array($params['order'])) {
				throw new InvalidArgumentException('order must be an array');
			}
			$orderBy = ' ORDER BY ' . implode(',', $params['order']);
		}
		$groupBy = '';
		if (!empty ($params['group'])) {
			if (!is_array($params['group'])) {
				throw new InvalidArgumentException('group must be an array');
			}
			$groupBy = ' GROUP BY ' . implode(',', $params['group']);
		}
		$fields = '*';
		if (!empty($params['fields']) && is_array($params['fields'])) {
			$fields = implode(',', $params['fields']);
		}
		$where = '';
		if (isset ($params['conditions'])) {
			$where = $this->getWhereString($params['conditions'], $tableName);
		}
		$indexFields = false;
		switch ($method) {
			case 'list' :
				if (empty ($params['listby'])) {
					if (empty ($params['fields'])) {
						$indexFields = array (
							$this->useIndex
						);
					} else {
						$indexFields = $params['fields'];
					}
				} else {
					if (!is_array($params['listby'])) {
						throw new InvalidArgumentException('listby must be an array');
					}
					if (strpos($fields, '*') === false) {
						foreach ($params['listby'] as $key => $value) {
							if (!in_array($value, $params['fields'])) {
								$fields = $fields . ',' . $value;
							}
						}
					}
					$indexFields = $params['listby'];
				}
			case 'all' :
				$sql = 'SELECT ' . $fields . ' FROM ' . $this->getTableName($tableName) . $where . $groupBy . $orderBy;
				if (!empty ($params['page']) && is_numeric($params['page'])) {
					$page = (int) $params['page'];
					$perPage = 50;
					if (!empty ($params['limit']) && is_numeric($params['limit'])) {
						$perPage = $params['limit'];
					}
					$sql = $this->dbo()->getPageQuery($sql, $page, $perPage);
				}
				return $this->query($sql, $indexFields);
				break;
				
			case 'count' :
				$sql = 'SELECT count(*) AS c FROM ' . $this->getTableName($tableName) . $where . $groupBy;
				$return = $this->query($sql);
				$count = isset ($return[0]['c']) ? $return[0]['c'] : 0;
				if (!empty ($params['page']) && is_numeric($params['page'])) {
					$page = (int) $params['page'];
					$perPage = 50;
					if (!empty ($params['limit']) && is_numeric($params['limit'])) {
						$perPage = $params['limit'];
					}
					$count = min($perPage, max(0, $count - ($page * $perPage)));
				}
				return $count;
				break;

			case 'first' :
				$sql = 'SELECT ' . $fields . ' FROM ' . $this->getTableName($tableName) . $where . $groupBy . $orderBy;
				$sql = $this->dbo()->getFirstRowQuery($sql, 1);
				return $this->query($sql);
				break;

			case 'neighbors' :
				die('not implemented yet');
				break;

			default :
				throw new InvalidArgumentException('model: find() doesnt know method ' . $method);
				break;
		}
	}

	/**
	 * read data from the database.
	 * 
	 * <code>
	 * $rows = $this->read(array('foobarId'=>5));
	 * $rows = $this->read(array(
	 * 	'foobarId'=>6,
	 *  'and',
	 *  'someId'=>2
	 * ));
	 * </code>
	 *
	 * @param mixed $id array of fieldnames used to construct WHERE clause
	 * @param array $fields return these colums (if null: all fields)
	 * @param string $tableName read from this table (if ommitted: use tablename of this model, including prefix)
	 * @param mixed $fieldName string or array to use as key for the returned result, see query()
	 */
	function read($id = null, $fields = null, $tableName = null, $fieldName = false) {
		if (is_array($fields) && (count($fields)==0)) {
			$fields = null;
		}
		return $this->query('SELECT ' .
		 ($fields === null ? '*' : implode(',', $fields)) .
		' FROM ' . $this->getTableName($tableName) .
		$this->getWhereString($id, $tableName, true), $fieldName);
	}

	/**
	 * mass insert function
	 * @param array $fields array of row-array to insert
	 * @param bool $ignore if we should do an INSERT INGORE
	 * @param mixed $tableName name of table to use or null for model default
	 * @return int number of successfully inserted rows
	 */
	function bulkcreate($fields = null, $ignore=false, $tableName = null) {
		if (!is_array($fields)) {
			throw new InvalidArgumentException('bulkinsert expects array');
		}
		$fieldNames = reset($fields);
		if (!is_array($fieldNames)) {
			throw new InvalidArgumentException('bulkinsert expects array of key-value-array');
		}
		$quotedFieldNames = array();
		foreach ($fieldNames as $fieldName=>$value) {
			if (is_numeric($fieldName)) {
				throw new InvalidArgumentException("rowname '$fieldName' is numeric, seems very odd");
			}
			$quotedFieldNames[] = $this->quoteName($fieldName);
		}

		$cntInner=0;
		$dntTotal=count($fields);
		$success=false;
		$sql = '';
		foreach ($fields as $rows) {
			foreach ($rows as &$row) {
				$row = $this->quote($row);
			}
			unset($row);
			$sql.='('.implode(',',$rows).'),';
			
			if (($cntInner++==100) || (--$dntTotal<=1)) {
				$result = $this->query('INSERT '.($ignore?' IGNORE ':'').'INTO '.
					$this->getTableName($tableName).' ('.
					implode($quotedFieldNames,',').') VALUES '.substr($sql,0,-1));
				$success = $success | (bool)$result;
				$cntInner=0;
				$sql='';
			}
		}
		
		return $success;
	}

	/**
	 * insert a record into the database.
	 *
	 * <code>
	 * $this->create(array('fooId'=>1,'int1'=>10,'int2'=>20));
	 * </code>
	 *
	 * @param array $fields name=>value pairs to be inserted into the table
	 * @param string $tableName insert into this table (if ommitted: use tablename of this model, including prefix)
	 */
	function create($fields = null, $tableName = null) {
		$fieldstr = '';
		$valuestr = '';
		if (empty ($fields)) {
			throw new InvalidArgumentException('insert without fields, seems odd');
		}
		foreach ($fields as $fieldname => $value) {
			$fieldstr .= $this->quoteName($fieldname) . ',';
			if (null === $value) {
				$valuestr .= 'null,';
			} else {
				$valuestr .= $this->quote($value) . ',';
			}
		}

		return $this->query('INSERT INTO ' . $this->getTableName($tableName) . ' (' . substr($fieldstr, 0, -1) . ') VALUES (' . substr($valuestr, 0, -1) . ')');
	}

	/**
	 * delete the row whose id is matching
	 *
	 * <code>
	 * $this->delete(array('rowId'=>10));
	 * $this->delete(array(
	 * 	'rowId'=>20,
	 * 	'and',
	 *  'parentId'=>10
	 * ));
	 * </code>
	 *
	 * @param mixed $id primary key of row to delete
	 * @param string $tableName delete from this table (if ommitted: use tablename of this model, including prefix)
	 */
	function delete($id = null, $tableName = null) {
		if (is_bool($id)) {
			throw new InvalidArgumentException('delete with bool condition, seems odd');
		}
		if (empty ($id)) {
			throw new InvalidArgumentException('delete with empty condition, seems odd');
		}
		$sql = 'DELETE FROM ' .
		$this->getTableName($tableName) .
		$this->getWhereString($id, $tableName, true);
		return $this->query($sql);
	}

	/**
	 * update a row whose id is matching. 
	 * 
	 * <code>
	 * $this->update(array(
	 * 	'fooId'=>10,
	 *  'data1'=>20
	 * ));
	 * </code>
	 *
	 * @param mixed $id primary array of fields suitable to construct a WHERE clause
	 * @param array $fields name=>value pairs of new values
	 * @param string $tableName update data in this table (if ommitted: use tablename of this model, including prefix)
	 */
	function update($id, $fields, $tableName = null) {
		if (empty ($id)) {
			throw new InvalidArgumentException('update with empty id, seems odd');
		}
		if (empty ($fields)) {
			throw new InvalidArgumentException('insert without fields, seems odd');
		}
		if (!is_array($fields)) {
			throw new InvalidArgumentException('fields must be an array');
		}
		return $this->query('UPDATE ' .
		$this->getTableName($tableName) .
		' SET ' . $this->pairs($fields) .
		$this->getWhereString($id, $tableName, true));
	}

	/**
	 * REPLACE works exactly like Insert, but removes previous entries.
	 *
	 * Warning: if an old row in the table has the same value as a new row for a PRIMARY KEY or a UNIQUE index,
	 * the old row is deleted before the new row is inserted. In short: It may be that more than 1 row is deleted.
	 *
	 * <code>
	 * $this->replace(array(
	 * 	'fooId'=>10,
	 *  'data1'=>20
	 * ));
	 * </code>
	 *
	 * @param mixed $id primary key of row to replace
	 * @param array $fields name=>value pairs of new values
	 * @param string $table replace from this table (if ommitted: use tablename of this model, including prefix)
	 */
	function replace($fields = null, $tableName = null) {
		if (empty ($fields)) {
			throw new InvalidArgumentException('insert without fields, seems odd');
		}
		if (!is_array($fields)) {
			throw new InvalidArgumentException('fields must be an array');
		}

		return $this->dbo()->replace($this->getTableName($tableName), $fields, $this->pairs($fields));
	}

	/**
	 * tries to reduce all fields of the given table to basic datatypes
	 *
	 * @param string $tableName optional tablename to use
	 * @return array
	 */
	function & describe($tableName = null) {
		$tableName = $this->getTableName($tableName);

		$cacheUtil = getUtil('Cache');
		$cacheId = 'describe.' . $this->connection . '.' . $tableName;
		$data = $cacheUtil->read($cacheId, CacheUtility :: CM_FILE);
		if (false !== $data) {
			return $data;
		}

		$data = $this->dbo()->describe($tableName);
		$cacheUtil->write($cacheId, $data, MINUTE, CacheUtility :: CM_FILE);
		return $data;
	}

	/**
	 * checks the given values of the array match certain criterias
	 * 
	 * @param array $params key/value pair. key is the name of the key inside the $what-array, value is a "VALID_" define (see above) OR the name of a (global) function that is given the string (should return bool wether the string validates) OR a regex string
	 * @param array $what the actual data, eg. GET/POST parameters
	 * @return bool true if everything is okay OR $params-array-key of the row that is not validating
	 * @deprecated 31.04.2009
	 */
	function validate($params = null, $what = null) {
		$validateUtil = getUtil('Validate');
		return $validateUtil->check($params, $what);
	}

	/**
	 * getModel() wrapper
	 */
	 function getModel($name) {
		 return $this->getModel($name);
	 }

}



//============ lib/modelloader.php =======================================================



/**
 * use other models inside models just like inside controllers. for use in standalone
 * classes (like rpc-handlers)
 *
 * Kata - Lightweight MVC Framework <http://www.codeninja.de/>
 * Copyright 2007-2009 mnt@codeninja.de, gameforge ag
 *
 * Licensed under The GPL License
 * Redistributions of files must retain the above copyright notice.
 * @package kata_controller
 */

/**
 * use other models inside models just like inside controllers
 * @package kata_model
 */
class ModelLoader {

	public $uses = array();

	public function __get($name) {
		if (!is_array($this->uses)) {
			throw new InvalidArgumentException('uses needs to be an array');
		}
		if (!in_array($name, $this->uses)) {
			throw new InvalidArgumentException("Model $name is not in uses class of this model");
		}

		$mdl = getModel($name);
		$this->$name = $mdl;
		return $mdl;
	}

}



//============ lib/modelloadermodel.php =======================================================



/**
 * use other models inside models just like inside controllers. extends appmodel.
 *
 * Kata - Lightweight MVC Framework <http://www.codeninja.de/>
 * Copyright 2007-2009 mnt@codeninja.de, gameforge ag
 *
 * Licensed under The GPL License
 * Redistributions of files must retain the above copyright notice.
 * @package kata_controller
 */

/**
 * use other models inside models just like inside controllers
 *
 * Example:
 * <code>
 * public $uses = 'Foo';
 *
 * function myfunction() {
 *    echo $this->Foo->read();
 * }
 * </code>
 * @package kata_model
 */

class ModelLoaderModel extends AppModel {

	public $uses = array();

	public function __get($name) {
		if (!is_array($this->uses)) {
			throw new InvalidArgumentException('uses needs to be an array');
		}
		if (!in_array($name, $this->uses)) {
			throw new InvalidArgumentException("Model $name is not in uses class of this model");
		}

		$mdl = getModel($name);
		$this->$name = $mdl;
		return $mdl;
	}
	
}



//============ lib/smartyview.php =======================================================




/**
 * Contains the class that is used to render views via smarty (layout,views,elements)
 *
 * Kata - Lightweight MVC Framework <http://www.codeninja.de/>
 * Copyright 2007-2009 mnt@codeninja.de, gameforge ag
 *
 * Licensed under The GPL License
 * Redistributions of files must retain the above copyright notice.
 * @package kata_view
 */

/**
 * smarty view class. used to render the view (and layout) for the controller. ALPHAish.
 * to use put the following in your controller:
 * <code>
 * public $view = 'SmartyView';
 * </code>
 *
 * @package kata_view
 */
class SmartyView extends View {

	/**
	 * hold smarty-object
	 *
	 * @var object
	 */
	protected $smarty = null;

	/**
	 * constructor. copies all needed variables from the controller
	 * @param object controller that uses this view
	 */
	function __construct(& $controller) {
		/**
		 * include smarty. a smarty-installation must be in your include path!
		 */
		//require_once 'Smarty.class.php';
		
		parent::__construct();

		$this->smarty = new Smarty;
		$this->smarty->register_function('helper', array (
				'SmartyView',
				'smarty_helper'
		));
	}

	/**
	 * allow smarty to use helpers
	 */
	function smarty_helper($params, & $smarty) {
		if (empty ($params['name'])) {
			throw new InvalidParameterException('smarty: {helper} needs name=');
		}
		if (!isset ($this->helperClasses[$params['name']])) {
			throw new InvalidParameterException('smarty: ' . $params['name'] . '-helper not found');
		}
		$helpername = $params['name'];
		unset ($params['name']);
		return call_user_func_array($this->helperClasses[$helpername], $params);
	}

	/**
	 * render the actual view and layout. normally done at the end of a action of a controller
	 * <code>
	 * 1. all helpers are constructed here, very late, so we dont accidently waste cpu cycles
	 * 2. all variables, helpers and params given from the controller are extracted to global namespace
	 * 3. the actual view-template is rendered
	 * 4. the content of the rendered view is rendered into the layout
	 * </code>
	 * @param string $action name of the view
	 * @param string $layout name of the layout
	 * @return string html of the view
	 */
	public function render($action, $layout) {
		$this->action = $action;

		$this->constructHelpers();
		$this->smarty->assign('params', $this->controller->params);
		$this->smarty->assign($this->passedVars);
		$this->smarty->assign($this->controller->viewVars);

		$viewfile = ROOT . 'views' . DS . strtolower(substr(get_class($this->controller), 0, -10)) . DS . $action . ".tpl";
		if (!file_exists($viewfile)) {
			throw new Exception('Cant find template [' . $this->action . '] of controller [' . get_class($this->controller) . '], Path is [' . $viewfile . ']');
		}
		return $this->smarty->fetch($viewfile);
	}

	/**
	 * renders the given string into the layout. normally called by renderView()
	 * <code>
	 * 1. title and content are extracted into global namespace
	 * 2. all variables, helpers and params given from the controller are extracted to global namespace
	 * 3. the given string is rendered into the layout
	 * 4. the html-output of this routine normally lands in the controllers output property
	 * </code>
	 * @param string $contentForLayout raw html to be rendered to the layout (normally the content of a view)
	 * @param string $layout name of the layout
	 */
	public function renderLayout($contentForLayout, $layout) {
		$this->layoutName = $layout;
		if ($this->layoutName !== null) {
			$this->constructHelpers();

			$this->smarty->assign('content_for_layout', $contentForLayout);
			$this->smarty->assign('title_for_layout', $this->controller->pageTitle);
			$this->smarty->assign('params', $this->controller->params);
			$this->smarty->assign($this->controller->viewVars);

			$layoutfile = ROOT . 'views' . DS . 'layouts' . DS . $this->layoutName . '.tpl';
			if (!file_exists($layoutfile)) {
				throw new Exception('Cant find layout template [' . $this->layoutName . ']');
			}
			return $this->smarty->fetch($layoutfile);
		} else {
			return $contentForLayout;
		}
	}

	/**
	 * render a element and return the resulting html. an element is kind of like a mini-view you can use inside a view (via $this->renderElement()).
	 * it has (like a view) access to all variables a normal view has
	 * @param string $name name of the element (see views/elements/) without .php
	 * @param an array of variables the element can access in global namespace
	 */
	public function renderElement($name, $params = array ()) {
		$this->elementName = $name;
		$this->smarty->assign($this->controller->viewVars);
		$this->smarty->assign($params);

		$elemfile = ROOT . 'views' . DS . 'elements' . DS . $this->elementName . ".php";
		if (!file_exists($elemfile)) {
			throw new Exception('Cant find element [' . $this->elementName . ']');
		}
		return $this->smarty->fetch($elemfile);
	}

}



//============ lib/controllers/components/locale.php =======================================================



/**
 * @package kata_component
 */
/**
 * locale-component. reads and caches an phpfile with language-strings
 * components are lightweight supportclasses for controllers
 * @package kata_component
 */
/**
 * global variable to cache the locale-class
 * @global object $GLOBALS['__cachedLocaleComponent']
 * @name __cachedLocaleComponent
 */
$GLOBALS['__cachedLocaleComponent'] = null;

/**
 * global function used to access language-strings. returns warning-string if key does not exist
 * @param string $msgId name of the language-string to return
 * @param array $msgArgs any parameters for printf if you have
 * @parma bool $safe true if you want no error to be thrown
 * @return string
 */
function __($msgId, $msgArgs = NULL, $safe=false) {
	if (null == $GLOBALS['__cachedLocaleComponent']) {
		$GLOBALS['__cachedLocaleComponent'] = classRegistry :: getObject('LocaleComponent');
	}
	if ($safe) {
		return $GLOBALS['__cachedLocaleComponent']->safeGetString($msgId, $msgArgs);
	}
	return $GLOBALS['__cachedLocaleComponent']->getString($msgId, $msgArgs);
}

/**
 * The Locale-Component Class
 * @package kata_component
 */
class LocaleComponent extends Component {

	/**
	 * placeholder for all languages
	 * @var array
	 */
	private $acceptedLanguages = null;
	/**
	 * which language-code is currently in use
	 * @var string
	 * @private
	 */
	private $code = false;
	/**
	 * the array with all locale-strings for the current language are cached here
	 * @var mixed
	 */
	private $messages = null;

	/**
	 * called by controller after the component was instanciated first
	 * @param object $controller the calling controller
	 */
	public function startup($controller) {
		parent::startup($controller);

		$this->setCode($this->findLanguage());

		if (!defined('LANGUAGE_FALLBACK')) {
			define('LANGUAGE_FALLBACK', false);
		}
		if (!defined('LANGUAGE_WARNEMPTY')) {
			define('LANGUAGE_WARNEMPTY', true);
		}
		if (!defined('LANGUAGE_PRINTF')) {
			define('LANGUAGE_PRINTF', false);
		}
		if (!defined('LANGUAGE_C2T')) {
			define('LANGUAGE_C2T', false);
		}
		if (!defined('LANGUAGE_ESCAPE')) {
			define('LANGUAGE_ESCAPE', false);
		}
	}

	/**
	 * returns html with h()ed entities and tags. entities are _not_ double-encoded, certain tags survive als html
	 *
	 * @param string $html raw html with umlauts
	 * @return string html with
	 */
	public function escapeHtml($html) {
		//[roman] da es die schöne double_encode Sache bei htmlentities erst ab der PHP 5.2.3 gibt hier ein fieser Mist...
		if (version_compare(PHP_VERSION, '5.2.3', '>=')) {
			$html = htmlentities($html, ENT_QUOTES, 'UTF-8', FALSE);
		} else {
			$html = html_entity_decode($html, ENT_QUOTES, 'UTF-8');
			$html = htmlentities($html, ENT_QUOTES, 'UTF-8');
		}
		return $html;
	}

	function getStringInternal($id, $messageArgs = null) {
		if (empty($this->code)) {
			throw new Exception('Locale: code not set yet');
		}

		if (empty($this->messages)) {
			$this->getMessages();
		}

		$ret = null;
		if (isset($this->messages[$id])) {
			$ret = $this->messages[$id];
		}

		if (empty($ret) && LANGUAGE_FALLBACK) {
			if (empty($this->enCache)) {
				$messages = array();
				include ROOT . 'controllers' . DS . 'lang' . DS . 'en.php';
				$this->enCache = $messages;
			}
			if (isset($this->enCache[$id])) {
				$ret = $this->enCache[$id];
			}
		}

		if (empty($ret)) { //null or ''
			return $ret;
		}

		if (count($messageArgs) > 0) {
			if (LANGUAGE_PRINTF) {
				$ret = vsprintf($ret, (null === $messageArgs ? array() : $messageArgs));
			} else {
				$replaced = 0;
				if (!empty($messageArgs)) {
					foreach ($messageArgs as $name => $value) {
						$ret = str_replace('%' . $name . '%', $value, $ret);
						$replaced++;
					}
				}
				if ((DEBUG > 0) && ($replaced != count($messageArgs))) {
					throw new Exception('locale: "' . $id . '" called with wrong number of arguments for language ' . $this->code . ' (i replaced:' . $replaced . ' i was given:' . count($messageArgs) . ') key value is "' . $this->messages[$id] . '"');
				}
			}
		}
		if (LANGUAGE_C2T && !empty($_GET['c2t'])) {
			$ret = '.<span class="c2t" ltlang=\'' . $this->getCode() . '\' ltname=\'' . $id . '\'>' . $ret . '</span>';
		}
		if (LANGUAGE_ESCAPE) {
			$ret = $this->escapeHtml($ret);
		}

		return $ret;
	}

	/**
	 * return the translation for the given string-identifier. throws expetions (if DEBUG>0) if key is missing or wrong parameters.
	 * @param string $id identifier to look up translation
	 * @param array $messageArgs optional parameters that will be formatted into the string with printf
	 */
	function getString($id, $messageArgs = null) {
		$ret = $this->getStringInternal($id, $messageArgs);
		if (null === $ret) {
			if (DEBUG > 0) {
				throw new exception('locale: cant find "' . $id . '" in language ' . $this->code);
			} else {
				writeLog("'$id' unset",'locale');
				return '---UNSET(' . $id . ')---';
			}
		}
		if (($ret === '') && LANGUAGE_WARNEMPTY) {
			writeLog("'$id' empty",'locale');
			return '---EMPTY(' . $id . ')---';
		}
		return $ret;
	}

	/**
	 * return given key or NULL if key is not found
	 * @param string $id
	 * @param array|null $messageArgs
	 * @return string|null
	 */
	function safeGetString($id, $messageArgs = null) {
		$ret = $this->getStringInternal($id, $messageArgs);
		if (empty($ret)) {
			return null;
		}
		return $ret;
	}

	/**
	 * sets a language-code. writes the code into the session of the user
	 * and sets Lang_Code for all views
	 * @param string $code short iso-code for language ("de" "en" "fr")
	 */
	function setCode($code) {
		if (empty($code)) {
			return;
		}

		if ($this->code != $code) {
			$this->messages = null;
			/*
			  if (isset ($this->Session)) {
			  $this->Session->write('Lang.Code', $code);
			  }
			 */
			$this->code = $code;
			if ($this->controller) {
				$this->controller->set('Lang_Code', $code);
			}
			if (function_exists('setlocale')) {
				$lang = $this->getLanguageFromTld($code);
				$loc = $lang . '.utf8';
				setlocale(LC_COLLATE, $loc, $lang, $code);
				setlocale(LC_CTYPE, $loc, $lang, $code);
				setlocale(LC_TIME, $loc, $lang, $code);
			}
		}
	}

	function getCode() {
		return $this->code;
	}

	private function fillAcceptedLanguages() {
		if ($this->acceptedLanguages !== null) {
			return;
		}

		$this->acceptedLanguages = array();
		if ($h = opendir(ROOT . 'controllers' . DS . 'lang' . DS)) {
			while (($file = readdir($h)) !== false) {
				if ($file {
					0 }
					== '.') {
					continue;
				}
				$temp = explode('.', $file);
				if (isset($temp[1]) && ('php' == $temp[1])) {
					$this->acceptedLanguages[$temp[0]] = $temp[0];
				}
			}
			closedir($h);
		}

		if (isset($this->acceptedLanguages['en'])) {
			unset($this->acceptedLanguages['en']);
			$this->acceptedLanguages['en'] = 'en';
		}

		if (isset($this->acceptedLanguages['de'])) {
			unset($this->acceptedLanguages['de']);
			$this->acceptedLanguages['de'] = 'de';
		}
	}

	function doesLanguageExist($lang) {
		$this->fillAcceptedLanguages();
		return!empty($this->acceptedLanguages[$lang]);
	}

	function getAcceptedLanguages() {
		$this->fillAcceptedLanguages();
		return $this->acceptedLanguages;
	}

	/**
	 * find the startup-language by looking at the LANGUAGE define in core/config.php
	 */
	function findLanguage() {
		if ((LANGUAGE == 'NULL') || (LANGUAGE === NULL)) {
			return null;
		}
		/*
		  if (isset ($this->Session)) {
		  $code= $this->Session->read('Lang.Code');
		  if (isset ($code) && !empty ($code)) {
		  return $code;
		  }
		  }
		 */
		if (LANGUAGE == 'VHOST') {
			$l = $this->getVhostLang();
			if (empty($l)) {
				$l = $this->getBrowserLang();
			}
			return $l;
		}
		if (LANGUAGE == 'BROWSER') {
			return $this->getBrowserLang();
		}
		return LANGUAGE;
	}

	/**
	 * try to find an language that we have as a file and that has a high priority in the users browser.
	 * returns EN if anything fails
	 * @return string short iso-code
	 */
	function getBrowserLang() {
		$this->fillAcceptedLanguages();

		$wanted = env('HTTP_ACCEPT_LANGUAGE');
		$key = '';
		if (isset($wanted)) {
			$Languages = explode(",", $wanted);
			$SLanguages = array();
			foreach ($Languages as $Key => $Language) {
				$Language = str_replace("-", "_", $Language);
				$Language = explode(";", $Language);
				if (isset($Language[1])) {
					$Priority = explode("q=", $Language[1]);
					$Priority = $Priority[1];
				} else {
					$Priority = "1.0";
				}
				$SLanguages[] = array(
					'priority' => $Priority,
					'language' => strtolower($Language[0])
				);
			}

			foreach ($SLanguages as $key => $row) {
				$priority[$key] = $row['priority'];
				$language[$key] = $row['language'];
			}

			array_multisort($priority, SORT_DESC, $language, SORT_ASC, $SLanguages);

			foreach ($SLanguages as $A) {
				// Check full codes first (xx_XX), then check 2digit-codes
				$key = $this->getTldFromLanguage($A['language']);
				if (empty($key)) {
					$GenericLanguage = explode("_", $A['language']);
					if (!empty($this->acceptedLanguages[$GenericLanguage[0]])) {
						$key = $this->getTldFromLanguage($GenericLanguage[0]);
					}
				}

				if (!empty($key)) {
					break;
				}
			}
		}

		return is($this->acceptedLanguages[$key], '');
	}

	/**
	 * try to find an language that we have as a file depending on the current domain name
	 * foo.example.tr -> "tr"
	 * [foo.bar.]tr.example.com -> "tr"
	 */
	function getVhostLang($useTld = false) {
		$this->fillAcceptedLanguages();

		$name = explode('.', env('SERVER_NAME'));
		if (count($name) < 2) {
			return '';
		}

		foreach ($this->acceptedLanguages as & $lang) {
			// www.DE.example.com DE.example.com
			if (($name[0] == $lang) || ($name[1] == $lang)) {
				return $this->getTldFromLanguage($lang);
			}
			if ($useTld) {
				// www.example.DE
				if (isset($name[count($name) - 1]) && ($name[count($name) - 1] == $lang)) {
					return $this->getTldFromLanguage($lang);
				}
			}
		}

		return '';
	}

	/**
	 * return the array containing all locale-codes by reference
	 */
	public function & getMessageArray() {
		if (null === $this->messages) {
			$this->getMessages();
		}
		return $this->messages;
	}

	/**
	 * load the message-array by loading controllers/lang/XX.php
	 */
	function getMessages() {
		if (empty($this->messages)) {
			$messages = array();
			include ROOT . 'controllers' . DS . 'lang' . DS . $this->code . '.php';
			$this->messages = & $messages;
		} //null
	}

	private $tldToLanguageArr = array(
		'ae' => 'ar_AR',
		'ar' => 'es_AR',
		'bg' => 'bg_BG',
		'br' => 'pt_BR',
		'by' => 'be_BY',
		'cl' => 'es_CL',
		'cn' => 'zh_CN',
		'co' => 'es_CO',
		'cz' => 'cs_CZ',
		'de' => 'de_DE',
		'dk' => 'da_DK',
		'ee' => 'et_EE',
		'eg' => 'ar_EG',
		'en' => 'en_UK',
		'es' => 'es_ES',
		'fi' => 'fi_FI',
		'fr' => 'fr_FR',
		'gr' => 'el_GR',
		'hk' => 'zh_HK',
		'hr' => 'hr_HR',
		'hu' => 'hu_HU',
		'id' => 'id_ID',
		'il' => 'he_IL',
		'in' => 'en_IN',
		'ir' => 'fa_IR',
		'it' => 'it_IT',
		'jp' => 'ja_JP',
		'kr' => 'ko_KR',
		'lt' => 'lt_LT',
		'lv' => 'lv_LV',
		'mx' => 'es_MX',
		'nl' => 'nl_NL',
		'no' => 'nb_NO',
		'pe' => 'es_PE',
		'ph' => 'tl_PH',
		'pk' => 'ur_PK',
		'pl' => 'pl_PL',
		'pt' => 'pt_PT',
		'ro' => 'ro_RO',
		'rs' => 'sr_RS',
		'ru' => 'ru_RU',
		'se' => 'sv_SE',
		'si' => 'sl_SI',
		'sk' => 'sk_SK',
		'th' => 'th_TH',
		'tr' => 'tr_TR',
		'tw' => 'zh_TW',
		'ua' => 'ru_UA',
		'us' => 'en_US',
		've' => 'es_VE',
		'vn' => 'vi_VN',
		'yu' => 'yu_YU',
		'com' => 'en_UK',
		'dev' => 'de_DE',
		'int' => 'en_US',
		'00' => '00_00',
	);

	/**
	 * map given language-tld-code to DINISO code for setLocale()
	 * @param string language-code
	 * @return string DINISO-code
	 */
	function getLanguageFromTld($lang) {
		return empty($this->tldToLanguageArr[$lang]) ? '' : $this->tldToLanguageArr[$lang];
	}

	/**
	 * map given DINISO to language-tld-code code for setLocale()
	 * @param string DINISO-code
	 * @return string language-code
	 */
	function getTldFromLanguage($langcode) {
		$langcode = strtolower($langcode);
		if (strlen($langcode) == 2) {
			if (isset($this->tldToLanguageArr[$langcode])) {
				return $langcode;
			}
			foreach ($this->tldToLanguageArr as $tld => $code) {
				if (substr($code, 0, 2) == $langcode) {
					return $tld;
				}
			}
		} else {
			foreach ($this->tldToLanguageArr as $tld => $code) {
				if (strtolower($code) == $langcode) {
					return $tld;
				}
			}
		}
		return '';
	}

}



//============ lib/controllers/components/memcached.session.php =======================================================



/**
 * sessions via memcache
 * 
 * @package kata_component
 */

/**
 * A component for object oriented session handling using memcached
 * needs PECL memcached-extension 2.1.2 or bigger
 *
 * @author feldkamp@gameforge.de
 * @package kata_component
 */
class SessionComponent extends baseSessionComponent {

	/**
	 * setting some ini-parameters and starting the actual session
	 */
	protected function startupSession() {
		$this->initCookie();
		$this->initSessionParams();

		$isMemcacheD = extension_loaded('memcached');

		$servers = explode(',', MEMCACHED_SERVERS);
		$path = '';
		foreach ($servers as $server) {
			$temp = explode(':', $server);
			$path .= ($isMemcacheD?'':'tcp://').$temp[0] . ':' . (empty ($temp[1]) ? 11211 : $temp[1]) . ',';
		}

		if ($isMemcacheD) {
			ini_set('session.save_handler', 'memcached');
			ini_set('session.save_path', substr($path, 0, -1));
		} else {
			if (version_compare(phpversion('memcache'), '2.1.2', '<')) {
				throw new Exception('You need at least PECL memcached 2.1.2 for session support');
			}

			ini_set('memcache.allow_failover', true);
			ini_set('memcache.hash_strategy', 'consistent');
			ini_set('memcache.hash_function', 'fnv');
			ini_set('session.save_path', substr($path, 0, -1));
			ini_set('session.save_handler', 'memcache');
		}

		@ session_start();
		$this->renewCookie();
	}

	/**
	 * read value(s) from the session container.
	 * returns all currently set values if called with null
	 * returns null when nothing could be found under the name you gave
	 * @param string $name name under which the value(s) are to find
	 */
	public function read($name = null) {
		if (CLI) {
			return false;
		}
		if ($this->initSession(true)) {
			if (empty ($name)) {
				return $_SESSION;
			}
			if (isset ($_SESSION[$name])) {
				return $_SESSION[$name];
			}
		}
		return null;
	}

	/**
	 * write mixed values to the session-component.
	 * @param string $name identifier, may contain alphanumeric characters or .-_
	 * @param mixed $value values to store
	 */
	public function write($name, $value) {
		if ($this->preamble($name, false)) {
			unset ($_SESSION[$name]);
			$_SESSION[$name] = $value;
			return true;
		}
		return false;
	}

	/**
	 * delete values stored under given name from the session-container
	 * @param string $name identifier
	 */
	public function delete($name) {
		if ($this->preamble($name, false)) {
			unset ($_SESSION[$name]);
			return true;
		}
		return false;
	}

	/**
	 * destroy any current session and all variables stored in the session-container with it
	 */
	public function destroy() {
		@session_destroy();
		$_SESSION = null;
		$this->clearCookie();
	}
}



//============ lib/controllers/components/session.php =======================================================



/**
 * @package kata_component
 */
/**
 * use igbinary for session serialization if loaded
 */
if (extension_loaded('igbinary')) {
	ini_set('session.serialize_handler', 'igbinary');
}

/**
 * base session class
 * 
 * @author feldkamp@gameforge.de
 * @author joachim.eckert@gameforge.de
 * @package kata_component
 */
class baseSessionComponent extends Component {

	/**
	 * path that we use when we set the cookie
	 * @var string
	 */
	protected $path;
	/**
	 * domain that we use when we set the cookie
	 * @var string
	 */
	protected $domain;
	/**
	 * useragent that we use when we set/check a session-cookie
	 * @var string
	 */
	protected $userAgent = null;
	/**
	 * time that we use when we check a session cookie
	 * @var int
	 */
	protected $time = null;
	/**
	 * time after that the session expires (normally time+SESSION_TIMEOUT, as set in config/core.php)
	 * @var int
	 */
	protected $sessionTime = 0;

	/**
	 * perform needed initialization and cache the controller that called us
	 */
	public function startup($controller) {
		parent::startup($controller);

		if (!defined('SESSION_UNSAFE')) {
			define('SESSION_UNSAFE', false);
		}

		if (CLI) {
			return;
		}
	}

	function constructParams() {
		//already constructed?
		if (null !== $this->time) {
			return;
		}

		$this->domain = env('SERVER_NAME');
		if (defined('SESSION_BASEDOMAIN') && (SESSION_BASEDOMAIN)) {
			$parts = explode('.', env('SERVER_NAME'));
			if (count($parts) > 1) {
				while (count($parts) > 2) {
					array_shift($parts);
				}
				$this->domain = '.' . implode('.', $parts);
			}
		}

		if (empty($this->controller->basePath)) {
			$this->path = '/';
		} else {
			$this->path = $this->controller->basePath;
		}

		$this->time = time();

		if (env('HTTP_USER_AGENT') != null) {
			$this->userAgent = md5(env('HTTP_USER_AGENT') . (!SESSION_UNSAFE ? $this->getIp() : '') . SESSION_STRING);
		} else {
			$this->userAgent = md5((!SESSION_UNSAFE ? $this->getIp() : '') . SESSION_STRING);
		}
	}

	/**
	 * did we already initialize the session?
	 * @var boolean
	 */
	private $didInitSession = false;

	/**
	 * setting some ini-parameters and starting the actual session. is done lazy (only when needed)
	 * @param $forRead boolean if true we dont initialize the session if no sessioncookie exists
	 */
	protected function initSession($forRead) {
		if ($this->didInitSession) {
			return true;
		}

		if ($forRead) {
			if (!isset($_COOKIE[SESSION_COOKIE]) || empty($_COOKIE[SESSION_COOKIE])) {
				return false;
			}
		}

		$this->constructParams();
		$this->startupSession();
		$this->didInitSession = true;
		$this->checkValid();
		return true;
	}

	protected function initSessionParams() {
		ini_set('url_rewriter.tags', '');
		ini_set('session.use_cookies', 1);
		ini_set('session.name', SESSION_COOKIE);
		ini_set('session.hash_bits_per_character', 6);
		ini_set('session_cache_limiter', 'nocache');
		ini_set('session.cookie_path', $this->path);
		ini_set('session.cookie_domain', $this->domain);
		ini_set('session.cookie_lifetime', SESSION_TIMEOUT);
		ini_set('session.gc_maxlifetime', SESSION_TIMEOUT);

		if (!defined('SESSION_SYSPATH') || !SESSION_SYSPATH) {
			kataMakeTmpPath('sessions');
			ini_set('session.save_path', KATATMP . 'sessions');
		}
	}

	protected function initCookie() {
		header('P3P: CP="NOI ADM DEV PSAi COM NAV OUR OTRo STP IND DEM"');
	}

	/**
	 * set cookie again to refresh session timeout
	 */
	protected function renewCookie() {
		$id = session_id();
		if (!empty($id)) {
			if ('localhost' === $this->domain) {
				setcookie(SESSION_COOKIE, $id, false, "/", false); // GRMBL!!!
			} else {
				setcookie(SESSION_COOKIE, $id, time() + SESSION_TIMEOUT, $this->path, $this->domain);
			}
		}
	}

	protected function clearCookie() {
		setcookie(SESSION_COOKIE, '', time() - DAY, $this->path, $this->domain);
	}

	/**
	 * check if the session expired, or something suspicious happend
	 */
	protected function checkValid() {
		if (!is_null($this->read('SessionConfig'))) {
			if ($this->userAgent != $this->read('SessionConfig.userAgent')) {
				// session hijacking
				$this->destroy();
			}
		} else {
			srand((double) microtime() * 1000000);
			$this->write('SessionConfig', 1);
			$this->write('SessionConfig.userAgent', $this->userAgent);
			$this->write('SessionConfig.rand', rand());
		}
	}

	/**
	 * checks if you used a valid string  as identifier
	 * @param string $name may contain a-z, A-Z, 0-9, ._-
	 */
	protected function validateKeyName($name) {
		if (is_string($name) && preg_match("/^[0-9a-zA-Z._-]+$/", $name)) {
			return;
		}
		throw new InvalidArgumentException("'$name' is not a valid session string identifier");
	}

	/**
	 * check obvious conditions for all operations
	 * 
	 * @param string $name name under which the value(s) are to find
	 * @param bool $forRead if we initialize for write (read: if we need to create a session if non-existing)
	 * @return bool success
	 */
	protected function preamble($name = null, $forRead = true) {
		if (CLI) {
			return false;
		}
		if (empty($name)) {
			return false;
		}
		$this->validateKeyName($name);
		return $this->initSession($forRead);
	}

	/**
	 * try to do an educated guess about the users real ip, even if he is behind proxies
	 * 
	 * @return string ip or '0.0.0.0' if failure
	 */
	public function getIp() {
		$ip = '0.0.0.0';

		if (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
			$ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
		} else {
			if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
				$ip = $_SERVER['HTTP_CLIENT_IP'];
			} else {
				$ip = $_SERVER['REMOTE_ADDR'];
			}
		}

		if (!empty($_SERVER['HTTP_CLIENTADDRESS'])) {
			$ip = $_SERVER['HTTP_CLIENTADDRESS'];
		}

		//TODO some proxies deliver comma seperated ip-lists *grmbl*
		return $ip;
	}

}

/**
 * included derived classes depending on storage-method
 */
if (!defined('SESSION_STORAGE')) {
	//require (LIB . 'controllers' . DS . 'components' . DS . 'file.session.php');
} else {
	//require (LIB . 'controllers' . DS . 'components' . DS . strtolower(SESSION_STORAGE) . '.session.php');
}



//============ lib/models/app_model.php =======================================================


/**
 * Contains an empty dummy App-Controller
 * @package kata_model
 */



/**
 * A dummy-class that is included if the user does not supply an appmodel.
 * @package kata_model
 * @author mnt@codeninja.de
 */
class AppModel extends Model{
}



//============ lib/utilities/cacheutility.php =======================================================


/**
 * contains Cache-class
 * @package kata
 */





/**
 * a universal caching class that can store data using several methods
 * @package kata_utility
 * @author mnt@codeninja.de
 * @author feldkamp@gameforge.de
 * @author jo@wurzelpilz.de
 */
class CacheUtility {

	/**
	 * which store/read-method to use
	 * @var string
	 */
	protected $method = null;

	protected $defaultMethod = null;

	protected $useRequestCache = true;
	protected $requestCache = array();

	/**
	 * some internal constants
	 */
	const CM_FILE = 'file';
	const CM_EACC = 'eacc'; //eaccelerator (please use apc)
	const CM_APC = 'apc';
	const CM_XCACHE = 'xcache';
	const CM_MEMCACHED = 'memcached'; //yeah. with a d
	const CM_MEMCACHE = 'memcache'; //without a d

	protected $isInitialized = false;
	/**
	 * set first store/read-method as default
	 */
	function initialize() {
		if ($this->isInitialized) {
			return;
		}
		$this->results = array ();
		kataMakeTmpPath('cache');

		if (defined('CACHE_USEMETHOD')) {
			$this->method = CACHE_USEMETHOD;
			$this->defaultMethod = CACHE_USEMETHOD;
			$this->isInitialized = true;
			return;
		}

		if (defined('MEMCACHED_SERVERS') && ('' != MEMCACHED_SERVERS) && class_exists('Memcached')) {
			$this->method = self :: CM_MEMCACHED;
			$this->defaultMethod = self :: CM_MEMCACHED;
			$this->initMemcached(true);
			$this->isInitialized = true;
			return;
		}
		if (defined('MEMCACHED_SERVERS') && ('' != MEMCACHED_SERVERS) && class_exists('Memcache')) {
			$this->method = self :: CM_MEMCACHE;
			$this->defaultMethod = self :: CM_MEMCACHE;
			$this->initMemcached();
			$this->isInitialized = true;
			return;
		}
		if (function_exists('apc_fetch')) {
			if (((CLI!=1) && ini_get('apc.enabled')) || ((CLI==1) && ini_get('apc.enable_cli'))) {
				$this->method = self :: CM_APC;
				$this->defaultMethod = self :: CM_APC;
				$this->isInitialized = true;
				return;
			}
		}
		if (function_exists('xcache_get')) {
			$this->method = self :: CM_XCACHE;
			$this->defaultMethod = self :: CM_XCACHE;
			$this->isInitialized = true;
			return;
		}
		if (function_exists('eaccelerator_get') && ini_get('eaccelerator.enable')) {
			$this->method = self :: CM_EACC;
			$this->defaultMethod = self :: CM_EACC;
			$this->isInitialized = true;
			return;
		}

		$this->method = self :: CM_FILE;
		$this->defaultMethod = self :: CM_FILE;
		$this->isInitialized = true;
	}

	/**
	 * @return int caching-method used for the last read/write
	 */
	function getMethodUsed() {
		return $this->method;
	}

	protected $memcachedClass = null;
	
	/**
	 * add all memcache-servers we know about, or just return if we already did it.
	 * uses all servers defined in MEMCACHED_SERVERS, see core.php
	 */
	protected function initMemcached($withD = false) {
		if (null !== $this->memcachedClass) {
			return;
		}

		if ($withD) {
			$this->memcachedClass = new Memcached;
		} else {
			ini_set('memcache.allow_failover', true);
			ini_set('memcache.hash_strategy', 'consistent');
			ini_set('memcache.hash_function', 'fnv');
			$this->memcachedClass = new Memcache;
		}

		$servers = explode(',', MEMCACHED_SERVERS);
		foreach ($servers as $server) {
			$temp = explode(':', $server);
			$this->memcachedClass->addServer($temp[0], empty ($temp[1]) ? 11211 : $temp[1], 1);
		}
	}

	function getMemcacheStats() {
		$this->initialize();

		if (is_a($this->memcachedClass,'Memcache')) {
			return $this->memcachedClass->getExtendedStats();
		}
		if (is_a($this->memcachedClass,'Memcached')) {
			return $this->memcachedClass->getStats();
		}

		return array();
	}


	/**
	 * holds data for debug-output
	 */
	protected $results=array();

	/**
	 * output debugging data if needed
	 */
	function __destruct() {
		if (DEBUG > 0) {
			array_unshift($this->results , array (
					'line',
					'op',
					'id',
					'data',
					'time'
			));
			kataDebugOutput($this->results, true);
		}
		unset($this->requestCache);
	}

	function add($id, $data, $ttl = 0, $forceMethod = false) {
		if (DEBUG > 2) {
			$this->results[] = array (
					kataFunc::getLineInfo(),
					'add',
					$id,
					'*caching off*',
					0
			);
			return false;
		}
		$startTime = microtime(true);
		$this->initialize();
		$r = $this->_add($id, $data, $ttl, $forceMethod);

		if ($r && $this->useRequestCache) {
			$this->requestCache[$id] = $data;
		}

		if (DEBUG > 0) {
			$this->results[] = array (
					kataFunc::getLineInfo(),
					'add',
					$id,
					kataFunc::getValueInfo($data),
					microtime(true) - $startTime
			);
		}
		return $r;
	}

	function write($id, $data, $ttl = 0, $forceMethod = false) {
		if (DEBUG > 2) {
			$this->results[] = array (
					kataFunc::getLineInfo(),
					'write',
					$id,
					'*caching off*',
					0
			);
			return false;
		}
		$startTime = microtime(true);
		$this->initialize();
		$r = $this->_write($id, $data, $ttl, $forceMethod);

		if ($r && $this->useRequestCache) {
			$this->requestCache[$id] = $data;
		}

		if (DEBUG > 0) {
			$this->results[] = array (
					kataFunc::getLineInfo(),
					'write',
					$id,
					kataFunc::getValueInfo($data),
					microtime(true) - $startTime
			);
		}
		return $r;
	}

	function read($id, $forceMethod = false) {
		if (DEBUG > 2) {
			$this->results[] = array (
					kataFunc::getLineInfo(),
					'read',
					$id,
					'*caching off*',
					0
			);
			return false;
		}

		if ($this->useRequestCache && isset($this->requestCache[$id])) {
			$data = $this->requestCache[$id];
			if (DEBUG > 0) {
				$this->results[] = array (
						kataFunc::getLineInfo(),
						'reqCache',
						$id,
						kataFunc::getValueInfo($data),
						0
				);
			}
			return $data;
		}

		$startTime = microtime(true);
		$this->initialize();
		$data = $this->_read($id, $forceMethod);
		if (DEBUG > 0) {
			$this->results[] = array (
					kataFunc::getLineInfo(),
					'read',
					$id,
					kataFunc::getValueInfo($data),
					microtime(true) - $startTime
			);
		}

		if ($this->useRequestCache) {
			$this->requestCache[$id] = $data;
		}

		return $data;
	}

	/**
	 * Disables the request cache
	 */
	public function disableRequestCache() {
		$this->useRequestCache = false;
	}

	/**
	 * Enables the request cache
	 */
	public function enableRequestCache() {
		$this->useRequestCache = true;
	}

	/**
	 * @var float|null
	 */
	protected $casToken=null;

	/**
	 * Read data from the cache
	 *
	 * @param string $id an unique-id of the data you want to read
	 * @param int $forceMethod which caching-method to use (only this time)
	 * @return mixed returns array or false if data could not be read
	 */
	protected function _read($id, $forceMethod = false) {
		$false = false;
		$id = CACHE_IDENTIFIER . '-' . $id;

		if (false === $forceMethod) {
			$this->method = $this->defaultMethod;
		} else {
			$this->method = $forceMethod;
		}

		if (self :: CM_MEMCACHED == $this->method) {
			$this->initMemcached(true);
			return $this->memcachedClass->get($id,null,$this->casToken);
		}

		if (self :: CM_MEMCACHE == $this->method) {
			$this->initMemcached();
			return $this->memcachedClass->get($id);
		}

		if (self :: CM_APC == $this->method) {
			return apc_fetch($id);
		}

		if (self :: CM_XCACHE == $this->method) {
			if (xcache_isset($id)) {
				return xcache_get($id);
			}
			return false;
		}

		if (self :: CM_EACC == $this->method) {
			$data = eaccelerator_get($id);
			if (null === $data) {
				return $false;
			}
			return @unserialize($data);
		}

		if (self :: CM_FILE == $this->method) {
			$fname = KATATMP . 'cache' . DS . urlencode($id);
			if (file_exists($fname)) {
				$temp = file_get_contents($fname);
				if ($temp !== false) {
					$temp = unserialize($temp);
					if ((0 == $temp['ttl']) || ($temp['ttl'] > time())) {
						return $temp['data'];
					}
				}
			}
			return $false;
		}

		throw new Exception('cacheUtil: unknown cache-method used');
	}

	/**
	 * write data to the cache. if data is false, the item will be purged from cache
	 *
	 * @param string $id  an unique-id of the data you want to write
	 * @param mixed $data data to write
	 * @param int $ttl time to live in seconds
	 * @param int $forceMethod which caching method to use (only this time)
	 * @return boolean true on success
	 */
	protected function _write($id, $data, $ttl = 0, $forceMethod = false) {
		$id = CACHE_IDENTIFIER . '-' . $id;

		if (false === $forceMethod) {
			$this->method = $this->defaultMethod;
		} else {
			$this->method = $forceMethod;
		}

		if (self :: CM_MEMCACHED == $this->method) {
			$this->initMemcached(true);
			if (false === $data) {
				return $this->memcachedClass->delete($id);
			}
			return $this->memcachedClass->set($id, $data, $ttl);
		}

		if (self :: CM_MEMCACHE == $this->method) {
			$this->initMemcached();
			if (false === $data) {
				return $this->memcachedClass->delete($id);
			}
			return $this->memcachedClass->set($id, $data, false, $ttl);
		}

		if (self :: CM_APC == $this->method) {
			if (false === $data) {
				return apc_delete($id);
			}
			return apc_store($id, $data, $ttl);
		}

		if (self :: CM_XCACHE == $this->method) {
			if (false === $data) {
				return xcache_unset($id);
			}
			return xcache_set($id, $data, $ttl);
		}

		if (self :: CM_FILE == $this->method) {
			$fname = KATATMP . 'cache' . DS . urlencode($id);
			if (false === $data) {
				return unlink($fname);
			}
			$temp = serialize(array (
					'ttl' => ($ttl > 0 ? time() + $ttl : 0),
					'data' => $data
			));
			return file_put_contents($fname, $temp);
		}

		if (self :: CM_EACC == $this->method) {
			if (false === $data) {
				return eaccelerator_rm($id);
			}
			return eaccelerator_put($id, serialize($data), $ttl);
		}

		throw new Exception('cacheUtil: unknown cache-method used');
	}

	/**
	 * write only data to the cache if item is nonexistant or expired
	 *
	 * @param string $id  an unique-id of the data you want to write
	 * @param mixed $data data to write
	 * @param int $ttl time to live in seconds
	 * @param int $forceMethod which caching method to use (only this time)
	 * @return boolean true on success
	 */
	protected function _add($id, $data, $ttl = 0, $forceMethod = false) {
		$id = CACHE_IDENTIFIER . '-' . $id;

		if (false === $forceMethod) {
			$this->method = $this->defaultMethod;
		} else {
			$this->method = $forceMethod;
		}

		if (self :: CM_MEMCACHED == $this->method) {
			$this->initMemcached(true);
			return $this->memcachedClass->add($id, $data, $ttl);
		}

		if (self :: CM_MEMCACHE == $this->method) {
			$this->initMemcached();
			return $this->memcachedClass->add($id, $data, false, $ttl);
		}

		if (self :: CM_APC == $this->method) {
			return apc_add($id, $data, $ttl);
		}

		if (self :: CM_XCACHE == $this->method) {
			if (!xcache_isset($id)) {
				return xcache_set($id, $data, $ttl);
			}
			return false;
		}

		if (self :: CM_FILE == $this->method) {
			$fname = KATATMP . 'cache' . DS . urlencode($id);
			if (file_exists($fname)) {
				return false;
			}

			// file not expired?
			$temp = file_get_contents($fname);
			if ($temp !== false) {
				$temp = unserialize($temp);
				if (($temp['ttl'] > 0) && ($temp['ttl'] < time())) {
					return false;
				}
			}

			$temp = serialize(array (
					'ttl' => ($ttl > 0 ? time() + $ttl : 0),
					'data' => $data
			));
			return file_put_contents($fname, $temp);
		}

		if (self :: CM_EACC == $this->method) {
			if (null !== eaccelerator_get($id)) {
				return false;
			}
			return eaccelerator_put($id, $data, $ttl);
		}

		throw new Exception('cacheUtil: unknown cache-method used');
	}

}



//============ lib/utilities/clusterlockutility.php =======================================================


/**
 * @package kata
 */




/**
 * CLUSTERwide locking mechanism with timeout for critical sections or eventhandlers
 * (needs memcached)
 *
 * @package kata_utility
 * @author feldkamp@gameforge.de
 */
class ClusterlockUtility {
	/**
	 * @var integer seconds to wait until we timeout
	 */
	private $timeout= 10;

	/**
	 * @var array holds lock-status
	 */
	private $locks= array ();

	/**
	 * placeholder for cache-utility
	 */
	private $cacheUtil = null;

	/**
	 * @param integer $timout how many seconds to wait for a lock before we fail
	 */
	public function setTimeout($timeout) {
		if (is_numeric($timeout) && ($timeout > 0)) {
			$this->timeout= $timeout;
			return true;
		}
		return false;
	}

	/**
	 * initialize internal structures
 	 */
	protected function initialize() {
		if (!defined('MEMCACHED_SERVERS') || (strlen(MEMCACHED_SERVERS)==0)) {
			throw new RuntimeException('clusterlockutil: no memcached-servers defined in config');
		}

		if (null === $this->cacheUtil) {
			$this->cacheUtil = getUtil('Cache');
		}
	}

	/**
	 * Lock a (user?) id
	 *
	 * @param int $id, id of the user to lock.
	 * @param bool $waitForTimeout, wait for time out
	 * @return bool, returns true if the user was locked
	 * @uses CacheUtility::add
	 */
	function lock($id, $waitForTimeout= true) {
		$this->initialize();

		$timeout= time() + $this->timeout;
		$lockId= CACHE_IDENTIFIER.'clusterLockUtilHandle'.urlencode($id);
		$success = false;

		while ((time() < $timeout) && $waitForTimeout) {
			if ($this->cacheUtil->add($lockId,1,$this->timeout,CacheUtility::CM_MEMCACHE)) {
				$success = true;
				break;
			}
			usleep(100000);
		}

		if ($success) {
			$this->locks[$id]= true;
			return true;
		}

		return false;
	}

	/**
	 * Unlock a (user?) id
	 *
	 * @param int $id, id of the user to lock
	 * @return true if the user was unlocked
	 * @uses CacheUtility::write
	 */
	function unlock($id) {
		if (!isset($this->locks[$id])) {
			if (DEBUG > 0) {
				throw new Exception("user $userid not locked");
			}
			return false;
		}

		$lockId = CACHE_IDENTIFIER.'clusterLockUtilHandle'.urlencode($id);
		$this->cacheUtil->write($lockId,false,1,CacheUtility::CM_MEMCACHE);
		unset ($this->locks[$id]);
		return true;
	}

	function __destruct() {
		if (count($this->locks) > 0) {
			if (DEBUG > 0) {
				throw new Exception("these locks have not been unlocked:".print_r($this->locks, true));
			}
		}
	}

}



//============ lib/utilities/extcacheutility.php =======================================================



/**
 * contains Extcache-class
 * @package kata
 */

/**
 * extends the normal cacheutility with more memcached functions
 * @package kata_utility
 * @author feldkamp@gameforge.de
 */
class ExtcacheUtility extends CacheUtility {

	/**
	 * increment given key. if nonexistant (or not numeric) key will be assumed as 0
	 * @param string $id key name
	 * @param string|bool $forceMethod which method to use instead of the autodetected one
	 * @return bool success
	 */
	public function increment($id, $forceMethod=false) {
		if (DEBUG > 2) {
			$this->results[] = array(
				kataFunc::getLineInfo(),
				'inc',
				$id,
				'*caching off*',
				0
			);
			return false;
		}

		$startTime = microtime(true);
		$this->initialize();
		$r = $this->_increment($id, $forceMethod);

		if ($r && $this->useRequestCache) {
			$this->requestCache[$id] = $r;
		}

		if (DEBUG > 0) {
			$this->results[] = array(
				kataFunc::getLineInfo(),
				'inc',
				$id,
				kataFunc::getValueInfo($r),
				microtime(true) - $startTime
			);
		}
		return $r;
	}

	/**
	 * do the actual incrementing
	 * @param string $id
	 * @param string|bool $forceMethod
	 * @return bool success
	 */
	protected function _increment($id, $forceMethod) {
		$id = CACHE_IDENTIFIER . '-' . $id;

		if (false === $forceMethod) {
			$this->method = $this->defaultMethod;
		} else {
			$this->method = $forceMethod;
		}

		if (self :: CM_MEMCACHED == $this->method) {
			$this->initMemcached(true);
			return $this->memcachedClass->increment($id);
		}

		if (self :: CM_MEMCACHE == $this->method) {
			$this->initMemcached();
			return $this->memcachedClass->increment($id);
		}

		throw new Exception('ExtCacheUtil: increment works only with memcache(d)');
	}

	/**
	 * decrement given key. if nonexistant (or not numeric) key will be assumed as 0
	 * @param string $id key name
	 * @param string|bool $forceMethod which method to use instead of the autodetected one
	 * @return bool success
	 */
	public function decrement($id, $forceMethod=false) {
		if (DEBUG > 2) {
			$this->results[] = array(
				kataFunc::getLineInfo(),
				'dec',
				$id,
				'*caching off*',
				0
			);
			return false;
		}

		$startTime = microtime(true);
		$this->initialize();
		$r = $this->_decrement($id, $forceMethod);

		if ($r && $this->useRequestCache) {
			$this->requestCache[$id] = $r;
		}

		if (DEBUG > 0) {
			$this->results[] = array(
				kataFunc::getLineInfo(),
				'inc',
				$id,
				kataFunc::getValueInfo($r),
				microtime(true) - $startTime
			);
		}
		return $r;
	}

	/**
	 * do the actual decrementing
	 * @param string $id
	 * @param string|bool $forceMethod
	 * @return bool success
	 */
	public function _decrement($id, $forceMethod=false) {
		$id = CACHE_IDENTIFIER . '-' . $id;

		if (false === $forceMethod) {
			$this->method = $this->defaultMethod;
		} else {
			$this->method = $forceMethod;
		}

		if (self :: CM_MEMCACHED == $this->method) {
			$this->initMemcached(true);
			return $this->memcachedClass->decrement($id);
		}

		if (self :: CM_MEMCACHE == $this->method) {
			$this->initMemcached();
			return $this->memcachedClass->decrement($id);
		}

		throw new Exception('ExtCacheUtil: decrement works only with memcache(d)');
	}

	/**
	 * read key and set comapareAndSet variable
	 * @param string $id keyname
	 * @param float $casVariable variable to put the cas-value into
	 * @param string|bool $forceMethod which method to use
	 * @return string
	 */
	public function readCas($id, &$casVariable, $forceMethod = false) {
		$r = $this->read($id, $forceMethod);
		if (self :: CM_MEMCACHED == $this->method) {
			$casVariable = $this->casToken;
		} else {
			throw new Exception('ExtCacheUtil: readCas works only with memcached');
		}
		return $r;
	}

	/**
	 * read multiple keys at once
	 * @param array $ids keynames
	 * @param string|bool $forceMethod which method to use
	 * @return array
	 */
	public function getMulti($ids, $forceMethod=false) {
		if (DEBUG > 2) {
			foreach ($ids as $id) {
				$this->results[] = array(
					kataFunc::getLineInfo(),
					'read',
					$ids,
					'*caching off*',
					0
				);
			}
			return false;
		}

		$startTime = microtime(true);
		$this->initialize();

		foreach ($ids as &$id) {
			$id = CACHE_IDENTIFIER . '-' . $id;
		}
		unset($id);

		if (false === $forceMethod) {
			$this->method = $this->defaultMethod;
		} else {
			$this->method = $forceMethod;
		}

		if (self :: CM_MEMCACHED == $this->method) {
			$this->initMemcached(true);

			$r = $this->memcachedClass->getMulti($ids);
			if (DEBUG > 0) {
				$endTime = microtime(true) - $startTime;
				foreach ($ids as $no => $id) {
					$this->results[] = array(
						kataFunc::getLineInfo(),
						'read',
						$id,
						kataFunc::getValueInfo($r[$no]),
						$endTime
					);
				}
			}

			if ($this->useRequestCache) {
				foreach ($ids as $id) {
					$this->requestCache[$id] = $r[$id];
				}
			}

			return $r;
		}

		throw new Exception('ExtCacheUtil: getMulti works only with memcache(d)');
	}

	/**
	 * set key only if the stored casToken equals our castoken (=key is unchanged)
	 * @param float $casToken castoken previously obtained by readCas
	 * @param string $id keyname
	 * @param string $value keyvalue
	 * @param integer $ttl time to live in seconds
	 * @param string|bool $forceMethod method to use
	 * @return boolean
	 */
	public function compareAndSwap($casToken, $id, $value, $ttl=0, $forceMethod=false) {
		if (DEBUG > 2) {
			$this->results[] = array(
				kataFunc::getLineInfo(),
				'read',
				$id,
				'*caching off*',
				0
			);
			return false;
		}

		$startTime = microtime(true);
		$this->initialize();

		$id = CACHE_IDENTIFIER . '-' . $id;

		if (false === $forceMethod) {
			$this->method = $this->defaultMethod;
		} else {
			$this->method = $forceMethod;
		}

		if (self :: CM_MEMCACHED == $this->method) {
			$this->initMemcached(true);
			$r = $this->memcachedClass->cas($casToken, $id, $value, $ttl);

			$done = true;
			if (($r) && ($this->memcachedClass->getResultCode() == Memcached::RES_SUCCESS)) {
				$done = true;
			}
			$done = false;

			if (DEBUG > 0) {
				$this->results[] = array(
					kataFunc::getLineInfo(),
					'read',
					$id,
					$done ? 'swapped' : 'my data is stale',
					microtime(true) - $startTime
				);
			}

			return $done;
		}

		throw new Exception('ExtCacheUtil: compareAndSwap works only with memcache(d)');
	}

}



//============ lib/utilities/httputility.php =======================================================



/**
 * @package kata
 */

/**
 * http-request class that does GET and POST (and even SSL) and has no dependencies
 *
 * @author mnt@codeninja.de
 * @package kata_utility
 *
 * Kata - Lightweight MVC Framework <http://www.codeninja.de/>
 * Copyright 2007-2009 mnt@codeninja.de, gameforge ag
 *
 * Licensed under The GPL License
 * Redistributions of files must retain the above copyright notice.
 */
class HttpUtility {

	/**
	 * @var array will hold all returned webserver-headers
	 */
	private $returnHeaders;
	/**
	 * @var bool true to automatically follow redirects
	 */
	private $followRedirects= true;
/**
 * send cookies we got on the last request?
 */
	private $rememberCookies=false;
/**
 * cookies from last request
 */
	private $lastCookies=array();

	const TYPE_GET= 1;
	const TYPE_POST= 2;
	const TYPE_HEAD= 3;

	/**
	 * do a GET request to given url. you dont have to encode get-parameters, just give it as array
	 *
	 * @param string $url base-url to request to eg. http://example.com/foo/bar
	 * @param array $getVars optional get-parameters to append to url
	 * @param array $headers optional request-headers to add to request
	 * @return mixed returns html or false if something went wrong (then ask lastError())
	 */
	public function get($url, $getVars= false, $headers= false) {
		return $this->doRequest(self :: TYPE_GET, $url, $getVars, false, $headers);
	}

	/**
	 * do a HEAD request to given url. you dont have to encode get-parameters, just give it as array
	 *
	 * @param string $url base-url to request to eg. http://example.com/foo/bar
	 * @param array $getVars optional get-parameters to append to url
	 * @param array $headers optional request-headers to add to request
	 * @return mixed returns html or false if something went wrong (then ask lastError())
	 */
	public function head($url, $getVars= false, $headers= false) {
		return $this->doRequest(self :: TYPE_HEAD, $url, $getVars, false, $headers);
	}

	/**
	 * do a GET request to given url. you dont have to encode get-parameters, just give it as array
	 *
	 * @param string $url base-url to request to eg. http://example.com/foo/bar
	 * @param array $getVars optional get-parameters to append to url
	 * @param array $postVars optional post-parameters to add to request
	 * @param array $headers optional request-headers to add to request
	 * @return mixed returns html or false if something went wrong (then ask lastError())
	 */
	public function post($url, $getVars= false, $postVars= false, $headers= false) {
		return $this->doRequest(self :: TYPE_POST, $url, $getVars, $postVars, $headers);
	}

	/**
	 * @param bool whether or not to follow redirects like 301,303 etc.
	 */
	public function setFollowRedirects($follow) {
		$this->followRedirects= $follow;
	}

	/**
	 * @param bool whether or not to remember cookies
	 */
	public function setRemembercookies($remember) {
		$this->rememberCookies= $remember;
	}

	/**
	 * actual wrapper
	 * @param int $type see TYPE_ consts. get/post/head
	 * @param string $url
	 * @param array $getVars
	 * @param array $postVars
	 * @param array $headers
	 * @return string html
	 */
	protected function doRequest($type, $url, $getVars, $postVars, $headers) {
		if (false === $headers) {
			$headers= array ();
		}

		$urlArr= parse_url($url);
		if (isset ($urlArr['user'], $urlArr['pass'])) {
			$url= str_replace($urlArr['user'].':'.$urlArr['pass'].'@', '', $url);
			$headers['Authorization']= 'Basic '.base64_encode($urlArr['user'].':'.$urlArr['pass']);
		}

		if ($type == self :: TYPE_GET) {
			$params= array (
				'http' => array (
					'method' => 'GET'
				)
			);
		}

		if ($type == self :: TYPE_HEAD) {
			$params= array (
				'http' => array (
					'method' => 'HEAD'
				)
			);
		}

		if ($type == self :: TYPE_POST) {
			$postStr= http_build_query($postVars);
			$params= array (
				'http' => array (
					'method' => 'POST',
					'content' => $postStr
				)
			);
			$headers['Content-Type']= 'application/x-www-form-urlencoded';
			$headers['Content-Length']= strlen($postStr);
		}

		if ($this->rememberCookies && !empty($this->lastCookies)) {
			$cookieStr = '';
			$idx = 0;
			foreach ($this->lastCookies as $n=>$v) {
				if ($idx++>0) { $cookieStr.=' $'; }
				$cookieStr .= $n.'='.$v.';';
				if ($idx+1<count($this->lastCookies)) { $cookieStr.=' '; }
			}
			$headers['Cookie'] = $cookieStr;
		}

		if (is_array($getVars)) {
			$url .= '?'.http_build_query($getVars);
		}

		$headers['User-Agent']= 'kata httpUtility - http://codeninja.de';

		$headerStr= '';
		foreach ($headers as $name => $value) {
			$headerStr .= $name.': '.$value."\r\n";
		}
		$params['http']['header']= $headerStr;
		$context= stream_context_create($params);
		$stream=  @fopen($url, 'rb', false, $context);

		if (!$stream) {
			if (isset($http_response_header)) {
				$http_response_header = array_reverse($http_response_header);
				$headArr = array();
				foreach ($http_response_header as $s) {
					if (substr($s,0,5) == 'HTTP/') {
						break;
					}
					$headArr[] = $s;
				}
				$headArr[] = $this->parseHeaders($headArr);
				return false;
			}

			$this->returnHeaders= array (
				'Status' => 0,
				'X-Kata-Error' => 'Stream init failed'
			);
			return false;
		}

		$html= @ stream_get_contents($stream);
		$headers= stream_get_meta_data($stream);
		if (isset ($headers['wrapper_data'])) {
			$this->returnHeaders= $this->parseHeaders($headers['wrapper_data']);
		} else {
			$this->returnHeaders= $this->parseHeaders($headers);
		}
		fclose($stream);

		if (false === $html) {
			return false;
		}

		if ($this->followRedirects) {
			$location= isset ($this->returnHeaders['Location']) ? $this->returnHeaders['Location'] : '';
			switch ($this->getStatus()) {
				case 301 :
				case 302 :
				case 307 :
					return $this->doRequest($type, $location, $getVars, $postVars, $headers);
					break;
				case 303 : //post->get
					return $this->doRequest($type == self :: TYPE_POST ? self :: TYPE_GET : $type, $location, $getVars, $postVars, $headers);
					break;
			}
		}

		return $html;
	}

	/**
	 * returns the http-status code of the last request. 0 if we could not figure out status-code
	 *
	 * @return int
	 */
	public function getStatus() {
		if (isset ($this->returnHeaders['Status'])) {
			return $this->returnHeaders['Status'];
		}
		if (!empty ($this->returnHeaders)) {
			$s= $this->returnHeaders[0];
			if (substr($s, 0, 4) == 'HTTP') {
				$temp= explode(' ', $s);
				return $temp[1];
			}
		}
		return 0;
	}

	/**
	 * return all webserver-headers from the last request
	 */
	public function getReturnHeaders() {
		return is($this->returnHeaders, array ());
	}

	protected function parseHeaders($headers) {
		$headOut= array ();
		foreach ($headers as $h) {
			$x= strpos($h, ':');
			if ($x === false) {
				$headOut[]= $h;
			} else {
				$headOut[substr($h, 0, $x)]= substr($h, $x +2);
			}
		}
		if (isset($headOut['Set-Cookie']) && $this->rememberCookies) {
			$this->lastCookies = $this->parseCookies($headOut['Set-Cookie']);
		}
		return $headOut;
	}

	protected function parseCookies($s) {
		$cookies = array();
		$sArr = explode(';',$s);
		foreach ($sArr as $s) {
			$s = trim($s);
			$name = '';
			$x = strpos($s,'=');
			if ($x>0) {
				$name = substr($s,0,$x);
				$s = substr($s,$x+1);
			}
			if (!empty($name)) {
				$cookies[$name] = $s;
			}
		}
		return $cookies;
	}
}



//============ lib/utilities/imageutility.php =======================================================


/**
 * @package kata
 */






/**
 * contains thumbnail utility class
 * @package kata
 */

/**
 * routines to resize images and handle downloads
 * @package kata_utility
 * @author mnt@codeninja.de
 */
class ImageUtility {

	const IMGERR_TOOBIG = 1;
	const IMGERR_TOOSMALL = 2;
	const IMGERR_UNKNOWNFORMAT = 4;

	const UPL_TOOBIG = 1; //file too big
	const UPL_INTERNAL = 2; //internal error
	const UPL_EXTENSION = 3; //file extension not allowed

	private $imageQuality = 95;

	/**
	 * read a gif,jpg,png from disk and return image
	 * @param string $filename
	 * @return image
	 */
	function read($filename) {
		if (!file_exists($filename)) {
			return false;
		}

		$img = false;
		$temp = getimagesize($filename);
		switch ($temp[2]) {
			case IMAGETYPE_GIF :
				$img = imagecreatefromgif($filename);
				break;
			case IMAGETYPE_JPEG :
				$img = imagecreatefromjpeg($filename);
				break;
			case IMAGETYPE_PNG :
				$img = imagecreatefrompng($filename);
				break;
		}

		return $img;
	}

	/**
	 * write the given image to disk
	 * @param image $img
	 * @param int $type imagetype, IMAGETYPE_GIF usw.
	 * @param string $filename
	 */
	function write($img, $type, $filename) {
		switch ($type) {
			case IMAGETYPE_GIF :
				return imagegif($img, $filename);
				break;
			case IMAGETYPE_JPEG :
				return imagejpeg($img, $filename, $this->imageQuality);
				break;
			case IMAGETYPE_PNG :
				return imagepng($img, $filename, 9, PNG_ALL_FILTERS);
				break;
			default :
				throw new Exception('write: Uknown image type');
		}
	}

	/**
	 * set image write quality. 0-100, regardless of imagetype
	 * @param int $quality 0=worst 100=best
	 */
	function setQuality($quality) {
		$this->imageQuality=max(0,min($quality,100));
	}


	/**
	 * simply create a thumbnail, dont make it proportional
	 * @param image $image
	 * @param int $width
	 * @param int $height
	 */
	function makeThumbnail($image, $width, $height) {
		$thumb = imagecreatetruecolor($width, $height);
		imagecopyresampled($thumb, $image, 0, 0, 0, 0, $width, $height, imagesx($image), imagesy($image));
		return $thumb;
	}

	/**
	 * enshure picture fits proportionally into the destionation size. this
	 * could possibly result in the picture being cropped
	 * @param image $image
	 * @param int $max_x target width
	 * @param int $max_y target height
	 */
	function makeThumbnailProportional($image, $max_x, $max_y) {
		$width = $max_x;
		$height = $max_y;

		$width_orig = imagesx($image);
		$height_orig = imagesy($image);

		if ($width_orig < $height_orig) {
			$height = ($max_x / $width_orig) * $height_orig;
		} else {
			$width = ($max_y / $height_orig) * $width_orig;
		}

		if ($width < $max_x) {
			$width = $max_x;
			$height = ($max_x / $width_orig) * $height_orig;
		}

		if ($height < $max_y) {
			$height = $max_y;
			$width = ($max_y / $height_orig) * $width_orig;
		}

		// first cutout the region we want to use, otherwise thumbs for different sizes look different
		$thumb = imagecreatetruecolor($width, $height);
		imagecopyresampled($thumb, $image, 0, 0, 0, 0, $width, $height, $width_orig, $height_orig);

		// then scale this region to the wanted size
		$thumb2 = imagecreatetruecolor($max_x, $max_y);
		$w1 = ($width / 2) - ($max_x / 2);
		$h1 = ($height / 2) - ($max_y / 2);
		imagecopyresampled($thumb2, $thumb, 0, 0, $w1, $h1, $max_x, $max_y, $max_x, $max_y);

		return $thumb2;
	}

/**
 * does the image conform to given parameters?
 * @param string $filename ...
 * @param int $maxwidth
 * @param int $maxheight
 * @param int minwidth
 * @param int minheight
 * @param mixed IMGERR_ codes
 */
	function isImageInvalid($filename, $maxwidth, $maxheight, $minwidth = 1, $minheight = 1) {
		list ($width, $height, $type, $attr) = getimagesize($filename);

		if (($type != IMAGETYPE_GIF) && ($type != IMAGETYPE_JPEG) && ($type != IMAGETYPE_PNG)) {
			return ImageUtility :: IMGERR_UNKNOWNFORMAT;
		}
		if ($width > $maxwidth) {
			return ImageUtility :: IMGERR_TOOBIG;
		}
		if ($height > $maxheight) {
			return ImageUtility :: IMGERR_TOOBIG;
		}
		if ((false !== $minheight) && ($height < $minheight)) {
			return ImageUtility :: IMGERR_TOOSMALL;
		}
		if ((false !== $minwidth) && ($width < $minwidth)) {
			return ImageUtility :: IMGERR_TOOSMALL;
		}

		return false;
	}


/**
 * reduce several upload-errors to 3 general ones
 * @param string $fieldname name="" of the upload-input-element
 * @return mixed true=success, false=no file given, UPLOAD_ERR_
 */
	function simpleUploadError($fieldname) {
		if (isset ($_FILES[$fieldname]['name']) && (empty ($_FILES[$fieldname]['error']))) {
			if (is_uploaded_file($_FILES[$fieldname]['tmp_name'])) {
				return true;
			}
		}


		switch ($_FILES[$fieldname]['error']) {
			case UPLOAD_ERR_FORM_SIZE :
			case UPLOAD_ERR_INI_SIZE :
				return ImageUtility :: UPL_TOOBIG;
				break;

			case UPLOAD_ERR_PARTIAL :
			case UPLOAD_ERR_NO_TMP_DIR :
			case UPLOAD_ERR_CANT_WRITE :
				return ImageUtility :: UPL_INTERNAL;
				break;

			case UPLOAD_ERR_EXTENSION :
				return ImageUtility :: UPL_EXTENSION;
				break;

			case UPLOAD_ERR_NO_FILE :
			default :
				return false;
				break;
		}
	}

        /**
         * @var white skin color tone
         */
        public $whiteSkin = 0x793B24;
        /**
         * @var black skin color tone
         */
        public $blackSkin = 0xFEC5BF;

/**
 * try to classify how likely the picture contains nudity.
 * warning: O(scary) (read: can take several seconds depending for big pictures)
 *
 * @param resource $img image-resource of the picture to classify
 * @return float 0=no nudity 100=full frontal nudity ^^
 */
    function getNudity($img)
    {
        if(!$img) return false;

        $x = imagesx($img)-1;
        $y = imagesy($img)-1;
        $score = 0;
	    $arA = array();
	    $arB = array();

        $arA['R'] = ($this->whiteSkin >> 16) & 0xFF;
        $arA['G'] = ($this->whiteSkin >> 8) & 0xFF;
        $arA['B'] = $this->whiteSkin & 0xFF;

        $arB['R'] = ($this->blackSkin >> 16) & 0xFF;
        $arB['G'] = ($this->blackSkin >> 8) & 0xFF;
        $arB['B'] = $$this->blackSkin & 0xFF;

        $xPoints = array($x/8, $x/4, ($x/8 + $x/4), $x-($x/8 + $x/4), $x-($x/4), $x-($x/8));
        $yPoints = array($y/8, $y/4, ($y/8 + $y/4), $y-($y/8 + $y/4), $y-($y/8), $y-($y/8));
        $zPoints = array($xPoints[2], $yPoints[1], $xPoints[3], $y);

        for($i=1; $i<=$x; $i++) {
            for($j=1; $j<=$y; $j++) {
                $color = imagecolorat($img, $i, $j);
                if($color >= $this->whiteSkin && $color <= $this->blackSkin) {
                    $color = array('R'=> ($color >> 16) & 0xFF, 'G'=> ($color >> 8) & 0xFF, 'B'=> $color & 0xFF);
                    if($color['G'] >= $arA['G'] && $color['G'] <= $arB['G'] && $color['B'] >= $arA['B'] && $color['B'] <= $arB['B']) {
                        if($i >= $zPoints[0] && $j >= $zPoints[1] && $i <= $zPoints[2] && $j <= $zPoints[3]) {
                            $score += 3;
                        } elseif($i <= $xPoints[0] || $i >=$xPoints[5] || $j <= $yPoints[0] || $j >= $yPoints[5]) {
                            $score += 0.10;
                        } elseif($i <= $xPoints[0] || $i >=$xPoints[4] || $j <= $yPoints[0] || $j >= $yPoints[4]) {
                            $score += 0.40;
                        } else {
                            $score += 1.50;
                        }
                    }//score
                }//colorinrange
            }//forj
        }//fori

        $score = min(100,round( ($score * 100) / ($x * $y) , 2));
        return $score;
    }

}



//============ lib/utilities/iputility.php =======================================================



/**
 * @package kata
 */
/**
 * contains IP-class
 * @package kata
 */

/**
 * some ip utility functions
 * @package kata_utility
 * @author mnt@codeninja.de
 */
class IpUtility {

	private $simpleHeaders = array(
		'REMOTE_ADDR',
		'HTTP_CLIENT_IP',
		'HTTP_X_FORWARDED_FOR',
		'HTTP_X_FORWARDED',
		'HTTP_FORWARDED_FOR',
		'HTTP_FORWARDED',
		'HTTP_X_COMING_FROM',
		'HTTP_COMING_FROM'
	);
	private $proxyHeaders = array(
		'HTTP_VIA',
		'HTTP_PROXY_CONNECTION',
		'HTTP_XROXY_CONNECTION',
		'HTTP_X_FORWARDED_FOR',
		'HTTP_X_FORWARDED',
		'HTTP_FORWARDED_FOR',
		'HTTP_FORWARDED',
		'HTTP_X_COMING_FROM',
		'HTTP_COMING_FROM',
		'HTTP_CLIENT_IP',
		'HTTP_PC_REMOTE_ADDR',
		'HTTP_CLIENTADDRESS',
		'HTTP_CLIENT_ADDRESS',
		'HTTP_SP_HOST',
		'HTTP_SP_CLIENT',
		'HTTP_X_ORIGINAL_HOST',
		'HTTP_X_ORIGINAL_REMOTE_ADDR',
		'HTTP_X_ORIG_CLIENT',
		'HTTP_X_CISCO_BBSM_CLIENTIP',
		'HTTP_X_AZC_REMOTE_ADDR',
		'HTTP_10_0_0_0',
		'HTTP_PROXY_AGENT',
		'HTTP_X_SINA_PROXYUSER',
		'HTTP_XXX_REAL_IP',
		'HTTP_X_REMOTE_ADDR',
		'HTTP_RLNCLIENTIPADDR',
		'HTTP_REMOTE_HOST_WP',
		'HTTP_X_HTX_AGENT',
		'HTTP_XONNECTION',
		'HTTP_X_LOCKING',
		'HTTP_PROXY_AUTHORIZATION',
		'HTTP_MAX_FORWARDS',
		'HTTP_X_IWPROXY_NESTING',
		'HTTP_X_TEAMSITE_PREREMAP',
		'HTTP_X_SERIAL_NUMBER',
		'HTTP_CACHE_INFO',
		'HTTP_X_BLUECOAT_VIA'
	);

	//see http://www.zytrax.com/tech/web/mobile_ids.html
	private $mobileAgents = array(
		'IPhone',
		'Android',
		'BlackBerry',
		'DoCoMo',
		'Maemo',
		'MeeGo',
		'NetFront',
		'Nokia',
		'PalmOS',
		'PalmSource',
		'SonyEricsson',
		'Symbian',
		'Windows CE',
		'IEMobile',
		'J2ME',
		'Minimo',
		'UP.Browser',
		'AvantGo'
	);

	/**
	 * simplified ip guess, so we don't end up with local ips
	 * @return string user-ip
	 */
	public function getIp() {
		$ip = '0.0.0.0';

		if (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
			$ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
		} else {
			if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
				$ip = $_SERVER['HTTP_CLIENT_IP'];
			} else {
				$ip = $_SERVER['REMOTE_ADDR'];
			}
		}

		if (!empty($_SERVER['HTTP_CLIENTADDRESS'])) {
			$ip = $_SERVER['HTTP_CLIENTADDRESS'];
		}

		return $ip;
	}

	/**
	 * try to do an educated guess about the users real ip, even if he is behind proxies
	 */
	public function getEndIp() {
		foreach ($this->simpleHeaders as $header) {
			$h = env($header);
			if (isset($h) && !empty($h)) {
				return $h;
			}
		}
		return '0.0.0.0';
	}

	/**
	 * is user using a proxy?
	 * @return bool
	 */
	public function isUsingProxy() {
		foreach ($this->proxyHeaders as $header) {
			$h = env($header);
			if (isset($h) && !empty($h)) {
				return true;
				break;
			}
		}
		return false;
	}

	/**
	 * is user using a handheld device to surf?
	 * @return bool
	 */
	public function isMobileDevice() {
		$agent = env('HTTP_USER_AGENT');
		if (empty($agent)) {
			return false;
		}

		foreach ($this->mobileAgents as $mobile) {
			if (false !== strpos($agent,$mobile)) {
				return true;
			}
		}
		return false;
	}

}



//============ lib/utilities/lockutility.php =======================================================


/**
 * @package kata
 */




/**
 * systemwide locking mechanism with timeout for critical sections or eventhandlers
 *
 * @package kata_utility
 * @author feldkamp@gameforge.de
 * @author sven.bender@gameforge.de
 */
class LockUtility {
	/**
	 * @var integer seconds to wait until we timeout
	 */
	private $timeout= 10;

	/**
	 * @var array holds lock-status
	 */
	private $locks= array ();

	/**
	 * @param integer $timout how many seconds to wait for a lock before we fail
	 */
	public function setTimeout($timeout) {
		if (is_numeric($timeout) && ($timeout > 0)) {
			$this->timeout= $timeout;
			return true;
		}
		return false;
	}

	/**
	 * setup up session directory
	 */
	function  __construct() {
		kataMakeTmpPath('sessions');
	}

	/**
	 * Lock a user id
	 *
	 * @param int $id, id of the user to lock.
	 * @param bool $waitForTimeout, wait for time out
	 * @return bool, returns true if the user was locked
	 */
	function lock($id, $waitForTimeout= true) {
		if (substr(PHP_OS, 0, 3) == 'WIN') {
			return true;
		}

		$this->garbageCollect();

		$timeout= time() + $this->timeout;
		$lockname= KATATMP.'sessions'.DS.'lockfile'.urlencode($id);
		$fplock= null;

		do {
			$fplock = fopen($lockname, "w+");
			if ($fplock) {
				if (flock($fplock, LOCK_EX | LOCK_NB)) {
					break;
				}
				if ($fplock) {
					fclose($fplock);
					$fplock= null;
				}
			}
			usleep(100000);
		} while ((time() < $timeout) && $waitForTimeout);

		if ($fplock) {
			$this->locks[$id]= $fplock;
			return true;
		}

		return false;
	}

	/**
	 * Unlock a user id
	 *
	 * @param int $id, id of the user to lock
	 * @return true if the user was unlocked
	 */
	function unlock($id) {
		if (substr(PHP_OS, 0, 3) == 'WIN') {
			return true;
		}

		if (!isset($this->locks[$id])) {
			if (DEBUG > 0) {
				throw new Exception("entity $id not locked");
			}
			return false;
		}

		$fplock= $this->locks[$id];
		flock($fplock, LOCK_UN);
		fclose($fplock);

		@ unlink(KATATMP.'sessions'.DS.'lockfile'.urlencode($id));
		unset ($this->locks[$id]);
		return true;
	}

	/**
	 * clean up leftover lockfiles
	 *
	 * @param bool $force collect now, even if propability is unmet
	 */
	function garbageCollect($force = false) {
		if (defined('LOCKUTIL_NOGC') && (LOCKUTIL_NOGC)) {
			return;
		}

		if (rand(0,100) > 5) {
			if (!$force) {
				return;
			}
		}
		
		$files = glob(KATATMP.'sessions'.DS.'lockfile*', GLOB_NOSORT);
		$maxAge = time()-100;
		foreach ($files as $file) {
			if (filemtime($file) < $maxAge) {
				@unlink($file);
			}
		}
	}

	/**
	 * output a word of warning if we forgot to unlock some ids
	 */
	function __destruct() {
		if (count($this->locks) > 0) {
			if (DEBUG > 0) {
				foreach ($this->locks as $id=>$fp) {
					$this->unlock($id);
				}
				writeLog("these locks have not been unlocked:".print_r($this->locks, true),KATA_ERROR);
			}
		}
	}

}



//============ lib/utilities/minifyutility.php =======================================================


/**
 * @package kata_utility
 */






/**
 * methods to compress css and javascript
 *
 * @author mnt@codeninja.de
 * @package kata_utility
 */
class MinifyUtility {

	public function js($js) {
		return JSMin::minify($js);
	}

	public function css($css) {
	 	$css = preg_replace('!/\*[^*]*\*+([^/][^*]*\*+)*/!', '', $css);
	 	$css = str_replace(array("\t","\r","\n"),' ',$css);
	 	$css = preg_replace('{[ \t]+}',' ',$css);
		return $css;
	}

}




/**
 * jsmin.php - PHP implementation of Douglas Crockford's JSMin.
 *
 * This is pretty much a direct port of jsmin.c to PHP with just a few
 * PHP-specific performance tweaks. Also, whereas jsmin.c reads from stdin and
 * outputs to stdout, this library accepts a string as input and returns another
 * string as output.
 *
 * PHP 5 or higher is required.
 *
 * Permission is hereby granted to use this version of the library under the
 * same terms as jsmin.c, which has the following license:
 *
 * --
 * Copyright (c) 2002 Douglas Crockford  (www.crockford.com)
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy of
 * this software and associated documentation files (the "Software"), to deal in
 * the Software without restriction, including without limitation the rights to
 * use, copy, modify, merge, publish, distribute, sublicense, and/or sell copies
 * of the Software, and to permit persons to whom the Software is furnished to do
 * so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in all
 * copies or substantial portions of the Software.
 *
 * The Software shall be used for Good, not Evil.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
 * SOFTWARE.
 * --
 *
 * @ignore
 * @package JSMin
 * @author Ryan Grove <ryan@wonko.com>
 * @copyright 2002 Douglas Crockford <douglas@crockford.com> (jsmin.c)
 * @copyright 2008 Ryan Grove <ryan@wonko.com> (PHP port)
 * @license http://opensource.org/licenses/mit-license.php MIT License
 * @version 1.1.1 (2008-03-02)
 * @link http://code.google.com/p/jsmin-php/
 */

class JSMin {
  const ORD_LF    = 10;
  const ORD_SPACE = 32;

  protected $a           = '';
  protected $b           = '';
  protected $input       = '';
  protected $inputIndex  = 0;
  protected $inputLength = 0;
  protected $lookAhead   = null;
  protected $output      = '';

  // -- Public Static Methods --------------------------------------------------

  public static function minify($js) {
    $jsmin = new JSMin($js);
    return $jsmin->min();
  }

  // -- Public Instance Methods ------------------------------------------------

  public function __construct($input) {
    $this->input       = str_replace("\r\n", "\n", $input);
    $this->inputLength = strlen($this->input);
  }

  // -- Protected Instance Methods ---------------------------------------------

  protected function action($d) {
    switch($d) {
      case 1:
        $this->output .= $this->a;

      case 2:
        $this->a = $this->b;

        if ($this->a === "'" || $this->a === '"') {
          for (;;) {
            $this->output .= $this->a;
            $this->a       = $this->get();

            if ($this->a === $this->b) {
              break;
            }

            if (ord($this->a) <= self::ORD_LF) {
              throw new JSMinException('Unterminated string literal.');
            }

            if ($this->a === '\\') {
              $this->output .= $this->a;
              $this->a       = $this->get();
            }
          }
        }

      case 3:
        $this->b = $this->next();

        if ($this->b === '/' && (
            $this->a === '(' || $this->a === ',' || $this->a === '=' ||
            $this->a === ':' || $this->a === '[' || $this->a === '!' ||
            $this->a === '&' || $this->a === '|' || $this->a === '?')) {

          $this->output .= $this->a . $this->b;

          for (;;) {
            $this->a = $this->get();

            if ($this->a === '/') {
              break;
            } elseif ($this->a === '\\') {
              $this->output .= $this->a;
              $this->a       = $this->get();
            } elseif (ord($this->a) <= self::ORD_LF) {
              throw new JSMinException('Unterminated regular expression '.
                  'literal.');
            }

            $this->output .= $this->a;
          }

          $this->b = $this->next();
        }
    }
  }

  protected function get() {
    $c = $this->lookAhead;
    $this->lookAhead = null;

    if ($c === null) {
      if ($this->inputIndex < $this->inputLength) {
        $c = $this->input[$this->inputIndex];
        $this->inputIndex += 1;
      } else {
        $c = null;
      }
    }

    if ($c === "\r") {
      return "\n";
    }

    if ($c === null || $c === "\n" || ord($c) >= self::ORD_SPACE) {
      return $c;
    }

    return ' ';
  }

  protected function isAlphaNum($c) {
    return ord($c) > 126 || $c === '\\' || preg_match('/^[\w\$]$/', $c) === 1;
  }

  protected function min() {
    $this->a = "\n";
    $this->action(3);

    while ($this->a !== null) {
      switch ($this->a) {
        case ' ':
          if ($this->isAlphaNum($this->b)) {
            $this->action(1);
          } else {
            $this->action(2);
          }
          break;

        case "\n":
          switch ($this->b) {
            case '{':
            case '[':
            case '(':
            case '+':
            case '-':
              $this->action(1);
              break;

            case ' ':
              $this->action(3);
              break;

            default:
              if ($this->isAlphaNum($this->b)) {
                $this->action(1);
              }
              else {
                $this->action(2);
              }
          }
          break;

        default:
          switch ($this->b) {
            case ' ':
              if ($this->isAlphaNum($this->a)) {
                $this->action(1);
                break;
              }

              $this->action(3);
              break;

            case "\n":
              switch ($this->a) {
                case '}':
                case ']':
                case ')':
                case '+':
                case '-':
                case '"':
                case "'":
                  $this->action(1);
                  break;

                default:
                  if ($this->isAlphaNum($this->a)) {
                    $this->action(1);
                  }
                  else {
                    $this->action(3);
                  }
              }
              break;

            default:
              $this->action(1);
              break;
          }
      }
    }

    return $this->output;
  }

  protected function next() {
    $c = $this->get();

    if ($c === '/') {
      switch($this->peek()) {
        case '/':
          for (;;) {
            $c = $this->get();

            if (ord($c) <= self::ORD_LF) {
              return $c;
            }
          }

        case '*':
          $this->get();

          for (;;) {
            switch($this->get()) {
              case '*':
                if ($this->peek() === '/') {
                  $this->get();
                  return ' ';
                }
                break;

              case null:
                throw new JSMinException('Unterminated comment.');
            }
          }

        default:
          return $c;
      }
    }

    return $c;
  }

  protected function peek() {
    $this->lookAhead = $this->get();
    return $this->lookAhead;
  }
}

/**
 * Exceptions ---------------------------------------------------------------
 * @package JSMin
 * @ignore
 **/
class JSMinException extends Exception {}



//============ lib/utilities/rsautility.php =======================================================



/**
 * contains Rsa-class
 * @package kata
 */

/**
 * rsa asymetric crypto related functions
 *
 * expects pem-style rsa-keys as 'vendors/rsakeys/pub.pem' and
 * 'vendors/rsakeys/priv.pem'. You can change the path by
 * setKeyPath()
 *
 *
 * @package kata_utility
 * @author joachim.eckert@gameforge.de
 * @author feldkamp@gameforge.de
 */
class RsaUtility {

	function __construct() {
		if (!function_exists('openssl_public_encrypt')) {
			throw new Exception('openssl extension not loaded :(');
		}

		$this->setKeyPath('vendors' . DS . 'rsakeys' . DS);
		$this->setEncoder('json');
	}

	////////////////////////////////////////////////////////////////////////////

	/**
	 * filesystem path to key storage
	 * @var string
	 */
	private $keyPath = '';

	/**
	 * read current filesystem path for key storage
	 * @return string
	 */
	public function getKeyPath() {
		return ROOT . $this->keyPath;
	}

	/**
	 * set filesystempath where to find keys
	 * @param string $path
	 */
	public function setKeyPath($path) {
		$this->keyPath = str_replace(ROOT, '', $path);
	}

	/**
	 * storage for priv/pub key
	 * @var array
	 */
	private $keys = array();

	/**
	 * read key from filesystem
	 * @param string $what can be 'pub' or 'priv'
	 * @return string
	 */
	public function getKey($what) {
		if (('pub' != $what) && ('priv' != $what)) {
			throw new Exception('$what can only be "pub" or "priv". typo?');
		}

		if (!empty($this->keys[$what])) {
			return $this->keys[$what];
		}

		if (!file_exists($this->getKeyPath() . $what . '.pem')) {
			throw new Exception('Cant find ' . $what . '.pem in ' . $this->getKeyPath());
		}
		$key = file_get_contents($this->getKeyPath() . $what . '.pem');
		if (!empty($key)) {
			$this->keys[$what] = $key;
			return $key;
		}
		throw new Exception('Cant read ' . $what . '.pem in ' . $this->getKeyPath());
	}

	/**
	 * return our public key
	 * @return string
	 */
	public function getPublicKey() {
		return $this->getKey('pub');
	}

	/**
	 * return our private key
	 * @return string
	 */
	public function getPrivateKey() {
		return $this->getKey('priv');
	}

	////////////////////////////////////////////////////////////////////////////

	/**
	 * which de/encoder function to use
	 * @var string
	 */
	private $encoder = 'json';

	/**
	 * set de/encoder function. can be (for examle) json,base64 etc
	 * @param string $encoder
	 */
	public function setEncoder($encoder) {
		if (null === $encoder) {
			$this->encoder = null;
			return;
		}

		$encoder = strtolower($encoder);
		if (!function_exists($encoder . '_encode') || !function_exists($encoder . '_decode')) {
			throw new Exception($encoder . '_encode/decode not found');
		}

		$this->encoder = $encoder;
	}

	/**
	 * read currently used de/encoder function
	 * @return string
	 */
	public function getEncoder() {
		return $this->encoder;
	}

	/**
	 * encode given string using currently set encoder function
	 * @param string $data
	 * @return string
	 */
	public function encode($data) {
		if (null === $this->encoder) {
			return $data;
		}

		$func = $this->encoder . '_encode';
		return $func($data);
	}

	/**
	 * decode given string using currently set decoder function
	 * @param string $data
	 * @return string
	 */
	public function decode($data) {
		if (null === $this->encoder) {
			return $data;
		}
		if ('json' == $this->encoder) {
			return json_decode($data, true);
		}

		$func = $this->encoder . '_decode';
		return $func($data);
	}

	////////////////////////////////////////////////////////////////////////////

	/**
	 * encrypts data asymetrically
	 *
	 * first serializes the data, then encrypts it and finally base64-encodes it
	 * best used to crypt data which can be decrypted with decryptAsymetric()
	 *
	 * @param mixed $data:           the data to encrypt
	 * @param mixed $receiverPubKey: the public key of the receiver
	 * @param mixed $ownPrivKey:     own private key, needed if data should also be signed (defaults to null)
	 * @param bool  $sign:           true if signature should be added (defaults to false)
	 *
	 * @return mixed: the encrypted data, false on error
	 */
	public function encrypt($data, $receiverPubKey, $sign = false) {
		$crypted = null;
		$signature = null;

		if (!openssl_public_encrypt(serialize($data), $crypted, $receiverPubKey)) {
			return false;
		}

		// get signature
		if ($sign) {
			$signature = $this->getSignature($crypted);
			if ($signature === false) {
				return false;
			}
		}

		$crypted = $this->encode($crypted);

		// add signature
		if (!empty($signature)) {
			$crypted .= "|$signature";
		}

		return $crypted;
	}

	/**
	 * decrypts asymetric crypted data
	 * first base64-decodes it, then decrypts it and finally unserializes it
	 * best used to decrypt data encrypted with encryptAsymetric()
	 *
	 * @param string $data:           the encrypted data
	 * @param mixed  $senderPubKey:   the public key of the sender, needed for signature check (defaults to null)
	 * @param bool   $checkSignature: true if signature should be checked (defaults to false)
	 *
	 * @return mixed: the decrypted data, false on error
	 */
	public function decrypt($data, $senderPubKey = null, $checkSignature = false) {
		$data = explode("|", $data);
		$data[0] = base64_decode($data[0]);
		if ($data[0] == false) {
			return false;
		}

		$ownPrivKey = $this->getPrivateKey();

		// verify signature
		if ($checkSignature) {
			if (empty($senderPubKey) || empty($data[1]) || !$this->verifySignature($data[0], $data[1], $senderPubKey)) {
				return false;
			}
		}

		$decrypted = null;
		if (!openssl_private_decrypt($data[0], $decrypted, $ownPrivKey)) {
			return false;
		}

		return unserialize($decrypted);
	}

	////////////////////////////////////////////////////////////////////////////

	/**
	 * generates an asymetric signature
	 *
	 * @param string $data:          the data to sign
	 * @param mixed  $ownPrivateKey: the own private key to sign the data with
	 *                               (as resource or pem representation)
	 *
	 * @return string: the signature (base64-encoded)
	 */
	private function getSignature($data) {
		$signature = null;
		$ownPrivateKey = $this->getPrivateKey();

		if (!openssl_sign($data, $signature, $ownPrivateKey, OPENSSL_ALGO_SHA1)) {
			return false;
		}

		return $this->encode($signature);
	}

	/**
	 * verifies an asymetric signature
	 *
	 * @param string $data:            the data which has been signed
	 * @param string $signature:       the signature to check (base64-encoded)
	 * @param mixed  $senderPublicKey: the public key of the signing party
	 *
	 * @return bool: true if signature was correct, false if incorrect or other error
	 */
	private function verifySignature($data, $signature, $senderPublicKey) {
		if (openssl_verify($data, base64_decode($signature), $senderPublicKey, OPENSSL_ALGO_SHA1) !== 1) {
			return false;
		}
		return true;
	}

}



//============ lib/utilities/simpleparseutility.php =======================================================


/**
 * @package kata
 */





/**
 * routines for super simple pattern parsing
 * @package kata_utility
 * @author feldkamp@gameforge.de
 */
 class SimpleparseUtility {

	/**
	 * cut out a text depending on a searchpattern.
	 * example: text is "abcdefghij",
	 * 			searchpattern is "ab*fg",
	 * 			then result is "cde"
	 *
	 * @param string $text text to search in
	 * @param string $pattern pattern to search in text
	 * @return mixed false when nothing is found, otherwise an array with text-results
	 */
	public function getPattern($text, $pattern, $casesensitive= true) {
		if ($text === false) {
			$text= $this->_http->getBody();
		}
		$pattern= explode("*", $pattern);

		if ($casesensitive) {
			$x1= strpos($text, $pattern[0]);
		} else {
			$x1= stripos($text, $pattern[0]);
		}
		if ($x1 === false) {
			return false;
		}

		if ($casesensitive) {
			$x2= strpos($text, $pattern[1], $x1);
		} else {
			$x2= stripos($text, $pattern[1], $x1);
		}
		if ($x2 === false) {
			return false;
		}

		return substr($text, $x1 +strlen($pattern[0]), $x2 - $x1 -strlen($pattern[0]));
	}

/**
 * cut out text depending on pattern, multiple wildcards allowed
 * example: text is "abcdefghij",
 * 			searchpattern is "ab*fg*ij",
 * 			then result is array("cde","h")
 *
 * @param string $text text to search in
 * @param string $pattern pattern to search in text
 * @return mixed false when nothing is found, otherwise an array with text-results
 */
	public function getPatterns($text, $pattern, $casesensitive= true) {
		$pattern= explode("*", $pattern);
		if (count($pattern)<2) {
			return false;
		}
		$out= array ();
		for ($i= 1; $i < count($pattern); $i++) {
			$temp= $this->getPattern($text, $pattern[$i -1].'*'.$pattern[$i], $casesensitive);
			if ($temp !== false) {
				$out[]= $temp;
			}
		}
		return $out;
	}


}



//============ lib/utilities/validateutility.php =======================================================



/**
 * @package kata
 */

/**
 * check if an array of parameters matches certain criterias.
 * you can still use the (deprecated) defines of model.php (until i murder you)
 *
 * @package kata_utility
 * @author feldkamp@gameforge.de
 */
class ValidateUtility {

	/**
	 * checks the given values of the array match certain criterias
	 *
	 * <code>
	 * check(array(
	 * 'email'=>'INT'
	 * ),$this->params['form'])
	 * </code>
	 *
	 * @param array $how key/value pair. key is the name of the key inside the $what-array, value is a rule, the name of a function that is given the string (should return bool wether the string validates)
	 * @param array $params the actual data as name=>value array (eg. $this->params['form'])
	 * @return true if data validates OR $params-key, for example 'EMAIL' if no correct email given OR false if $params is empty
	 */
	function check($how, $params) {
		$result = $this->checkAll($how, $params);

		if (!is_array($result))
			return $result;
		if (is_array($result) && (count($result) == 0))
			return false;
		return array_shift($result);
	}

	/**
	 * checks the given values of the array match certain criterias
	 *
	 * <code>
	 * check(array(
	 * 'email'=>'INT'
	 * ),$this->params['form'])
	 * </code>
	 *
	 * @param array $how key/value pair. key is the name of the key inside the $what-array, value is a rule, the name of a function that is given the string (should return bool wether the string validates)
	 * @param array $params the actual data as name=>value array (eg. $this->params['form'])
	 * @return true if data validates OR $params-key, for example 'EMAIL' if no correct email given OR array() if $params is empty
	 */
	function checkAll($how, $params) {
		if (!is_array($how)) {
			throw new InvalidArgumentException("validateutil: 'how' should be an array");
		}
		if (!is_array($params) || empty ($params)) {
			return array ();
		}

		$errors = array ();
		foreach ($how as $inpname => $howname) {
			// be compatible with old validate
			if (substr($howname, 0, 6) == 'VALID_') {
				$howname = substr($howname, 6);
			}

			$inpvalue = is($params[$inpname], '');
			switch (strtoupper($howname)) {
				case 'NOT_EMPTY' :
					if (empty ($inpvalue))
						$errors[$inpname] = $howname;
					break;
				case 'ALLOW_EMPTY' :
					break;
				case 'ALLOW_MULTI' :
					if (!is_array($inpvalue) || (count($inpvalue) == 0))
						$errors[$inpname] = $howname;
					break;
				case 'ALLOW_MULTI_EMPTY':
					break;
				case 'BOOL' :
					if ($inpvalue != '1' && $inpvalue != '0')
						$errors[$inpname] = $howname;
					break;
				case 'BOOL_LAZY' :
					if ($inpvalue != '1' && $inpvalue != '')
						$errors[$inpname] = $howname;
					break;
				case 'BOOL_TRUE' :
					if ($inpvalue != '1')
						$errors[$inpname] = $howname;
				break;
					case 'FLOAT' :
					if (!filter_var($inpvalue, FILTER_VALIDATE_FLOAT))
						$errors[$inpname] = $howname;
					break;
				case 'UINT' :
				case 'NUMBER' :
					if (!filter_var($inpvalue, FILTER_VALIDATE_INT, array (
							'options' => array (
								'min_range' => 0
							)
						)))
						$errors[$inpname] = $howname;
					break;
				case 'INT' :
					if (!filter_var($inpvalue, FILTER_VALIDATE_INT))
						$errors[$inpname] = $howname;
					break;
				case 'HEX' :
					if (!filter_var($inpvalue, FILTER_VALIDATE_INT || FILTER_FLAG_ALLOW_HEX))
						$errors[$inpname] = $howname;
					break;
				case 'EMAIL' :
					if (filter_var($inpvalue, FILTER_VALIDATE_EMAIL) === false)
						$errors[$inpname] = $howname;
					break;
				case 'IP' :
					if (!filter_var($inpvalue, FILTER_VALIDATE_IP))
						$errors[$inpname] = $howname;
					break;
				case 'URL' :
					if (!filter_var($inpvalue, FILTER_VALIDATE_URL))
						$errors[$inpname] = $howname;
					break;
				case 'YEAR' :
					if (!is_numeric($inpvalue) || ($inpvalue < 1900) || ($inpvalue > 2099))
						$errors[$inpname] = $howname;
				case function_exists($howname) :
					if (!$howname ($inpname, $inpvalue))
						$errors[$inpname] = $howname;
					break;
				default :
					if (false === strpos($howname, '/')) {
						throw new InvalidArgumentException("validateutil: '$howname' is no rule, function or regex. maybe a typo?");
					}
					if (!filter_var($inpvalue, FILTER_VALIDATE_REGEXP, array (
							'validate_regexp' => $howname
						)))
						$errors[$inpname] = $howname;
					break;
			} //switch
		} //foreach

		if (empty ($errors)) {
			return true;
		}

		return $errors;
	}

	/**
	 * check the given parameter-array against the rows of a model
	 * 
	 * @param string $modelname name of the model to validate against
	 * @param array $params the actual data as name=>value array (eg. $this->params['form'])
	 * @return true if data validates OR $params-key, for example 'EMAIL' if no correct email given OR array() if $params is empty
	 */
	function checkAllWithModel($modelname, $params) {
		$model = getModel($modelname);
		$describe = $model->describe($model->getTableName(null, true, false));
		$valArray = array ();

		//remove primary key 
		if (isset ($describe['identity'])) {
			unset ($describe['cols'][$describe['identity']]);
		}

		foreach ($describe['cols'] as $colname => $coldata) {
			switch ($coldata['type']) {
				case 'int' :
					$valArray[$colname] = 'INT';
					break;
				case 'string' :
				case 'text' :
					$valArray[$colname] = 'NOT_EMPTY';
					break;
				case 'bool' :
					$valArray[$colname] = 'BOOL';
					break;
				case 'float' :
					$valArray[$colname] = 'FLOAT';
					break;
				default :
					//TODO support more stuff
					throw new InvalidArgumentException("validateModel: no support (yet) for " . $coldata['type']);
			}
		}

		return $this->checkAll($valArray, $params);
	}

}



//============ lib/views/helpers/form.php =======================================================


/**
 * Contains the form helper
 *
 * Kata - Lightweight MVC Framework <http://www.codeninja.de/>
 * Copyright 2007-2009 mnt@codeninja.de, gameforge ag
 *
 * Licensed under The GPL License
 * Redistributions of files must retain the above copyright notice.
 * @package kata_helper
 */

/**
 * form helper for super-easy form handling
 * @package kata_helper
 */
class FormHelper extends Helper {

	/**
	 * which model to use. needed to format name-portion of tag accordingly
	 * 
	 * @var string
	 */
	private $modelName = '';

	/**
	 * which submit-method we use, used to access $this->params
	 */
	private $method = 'get';

	/**
	 * did we already open a form-tag?
	 * 
	 * @var bool
	 */
	private $formTagOpen = false;

	/**
	 * throw error if form still open
	 */
	function __destruct() {
		if ($this->formTagOpen) {
			//cant throw exception here, no stackframe
			trigger_error('formhelper: form not closed', E_USER_ERROR);
		}
	}

	/**
	 * throw error if form not open
	 */
	private function checkForOpenForm() {
		if (!$this->formTagOpen) {
			throw new Exception('formhelper: form not opened, cant close');
		}
	}

	/**
	 * create a opening form-tag for GET requests
	 * 
	 * @param string $url url to GET to
	 * @param mixed $htmlAttributes key-value array of html-attributes OR preformatted string ('readonly="true"') OR empty
	 * @param mixed $modelName string of model to use OR empty
	 * @return string html 
	 */
	function get($url=null, $htmlAttributes = null, $modelName = '') {
		if (empty($url)) {
			$url = $this->base.$this->params['controller'].'/'.$this->params['action'];
		}
		if ($this->formTagOpen) {
			throw new Exception('formhelper: form already opened, cant open again');
		}
		$this->formTagOpen = true;
		$this->method = 'url';
		$this->modelName = strtolower($modelName);
		return sprintf($this->tags['formstart'], 'get', $this->url($url), $this->parseAttributes($htmlAttributes));
	}

	/**
	 * create a opening form-tag for POST requests
	 * 
	 * @param string $url url to POST to
	 * @param mixed $htmlAttributes key-value array of html-attributes OR preformatted string ('readonly="true"') OR empty
	 * @param mixed $modelName string of model to use OR empty
	 * @return string html 
	 */
	function post($url=null, $htmlAttributes = null, $modelName = '') {
		if (empty($url)) {
			$url = $this->base.$this->params['controller'].'/'.$this->params['action'];
		}
		if ($this->formTagOpen) {
			throw new Exception('formhelper: form already opened, cant open again');
		}
		$this->formTagOpen = true;
		$this->method = 'form';
		$this->modelName = strtolower($modelName);
		return sprintf($this->tags['formstart'], 'post', $this->url($url), $this->parseAttributes($htmlAttributes));
	}

	/**
	 * close previously openend form-tag
	 * @return string html 
	 */
	function close() {
		$this->checkForOpenForm();
		$this->formTagOpen = false;
		return sprintf($this->tags['formend']);
	}

	/**
	 * return error message if the previously displayed form had any validation errors. obviously only works if you did a validate inside your models action.
	 * 
	 * @param string $name name-portion of input-tag
	 * @param string $errorString string to display on error
	 * @return string empty string if no error, otherwise errorstring inside configured error-tag-template
	 */
	function error($name, $errorString) {
		$this->checkForOpenForm();
		if (empty ($this->modelName)) {
			if (isset ($this->vars['__validateErrors'][$name])) {
				return sprintf($this->tags['formerror'], $errorString);
			}
			return '';
		}
		if (isset ($this->vars['__validateErrors'][$this->modelName][$name])) {
			return sprintf($this->tags['formerror'], $errorString);
		}
		return '';
	}

	/**
	 * create name-portion of tag, depends on wether we use a model or not:
	 * if no model is used simply returns name, otherwise returns 'model[name]'
	 * 
	 * @param mixed $name name-portion of the input tag
	 * @return string html 
	 */
	function fieldName($name) {
		if (empty ($this->modelName)) {
			return sprintf('%s', $name);
		}
		return sprintf('%s[%s]', $this->modelName, $name);
	}

	/**
	 * overwrite given (referenced) value with value from get/post, if existing
	 * 
	 * @param string $name name-portion of tag
	 * @param string $value value to manipulate (referenced) 
	 * @return string html 
	 */
	function setDefault($name, & $value) {
		if (empty ($this->modelName)) {
			if (isset ($this->params[$this->method][$this->fieldName($name)])) {
				$value = h($this->params[$this->method][$this->fieldName($name)]);
			}
			return;
		}
		if (isset ($this->params[$this->method][$this->modelName][$name])) {
			$value = h($this->params[$this->method][$this->modelName][$name]);
		}
	}

	/**
	 * generate input-tag
	 * 
	 * @param string $name name of the input-tag
	 * @param string $value default value of the input tag. will be overwritten if we land here again after the request  
	 * @param array $htmlAttributes tag-attributes
	 * @return string html 
	 */
	function input($name, $value = '', $htmlAttributes = null) {
		$this->checkForOpenForm();
		$type = 'text';
		if (!empty ($htmlAttributes['type'])) {
			$type = $htmlAttributes['type'];
			unset ($htmlAttributes['type']);
		}
		$this->setDefault($name, $value);

		return sprintf($this->tags['input'], $this->fieldName($name), $value, $type, $this->parseAttributes($htmlAttributes));
	}

	/**
	 * generate checkbox. will generate a value (0) even if unchecked, no more javascript fiddeling!
	 * 
	 * @param string $name name of the input-tag
	 * @param bool $checked if checkbox should be checked or not. will be overwritten if we land here again after the request  
	 * @param array $htmlAttributes tag-attributes
	 * @return string html 
	 */
	function checkbox($name, $checked = false, $htmlAttributes = null) {
		$this->checkForOpenForm();
		$this->setDefault($name, $checked);

		return sprintf($this->tags['checkbox'], $this->fieldName($name), $this->fieldName($name), $checked ? 'checked="checked"' : '', $this->parseAttributes($htmlAttributes));
	}

	/**
	 * generate textarea-tag
	 * 
	 * @param string $name name of the input-tag
	 * @param string $value default value of the textarea tag. will be overwritten if we land here again after the request  
	 * @param array $htmlAttributes tag-attributes
	 * @return string html 
	 */
	function textarea($name, $value = '', $htmlAttributes = null) {
		$this->checkForOpenForm();
		$this->setDefault($name, $value);

		return sprintf($this->tags['textarea'], $this->fieldName($name), $this->parseAttributes($htmlAttributes), $value);
	}

	/**
	 * build select/option tags
	 * 
	 * <samp>
	 * $arr = array('blue'=>'Blue color','red'=>'Red color');
	 * echo $html->selectTag('gameinput',$arr,'red');
	 * </samp>
	 * 
	 * @param string $fieldName name-part of the select-tag
	 * @param array $optionElements array with elements (key=option-tags value-part, name=between option tag)
	 * @param string $selected keyname of the element to be default selected
	 * @param array $selectAttr array of attributes of the select-tag, for example "class"=>"dontunderline"
	 * @param array $optionAttr array of attributes for each option-tag, for example "class"=>"dontunderline"
	 * @param boolean $showEmpty if we should display an empty option as the default selection so the user knows (s)he has to select something
	 * @return string html
	 */
	function select($name, $optionElements, $selected = null, $selectAttr = array (), $optionAttr = array (), $showEmpty = false) {
		$this->checkForOpenForm();
		$this->setDefault($name, $selected);

		$select = '';
		if (!is_array($optionElements)) {
			return '';
		}
		if (isset ($selectAttr) && array_key_exists("multiple", $selectAttr)) {
			$select .= sprintf($this->tags['selectmultiplestart'], $this->fieldName($name), $this->parseAttributes($selectAttr));
		} else {
			$select .= sprintf($this->tags['selectstart'], $this->fieldName($name), $this->parseAttributes($selectAttr));
		}
		if ($showEmpty == true) {
			$select .= sprintf($this->tags['selectempty'], $this->parseAttributes($optionAttr));
		}
		foreach ($optionElements as $optname => $title) {
			$optionsHere = $optionAttr;

			if (($selected != null) && ((string)$selected == (string)$optname)) {
				$optionsHere['selected'] = 'selected';
			}
			elseif (is_array($selected) && in_array($optname, $selected)) {
				$optionsHere['selected'] = 'selected';
			}

			$select .= sprintf($this->tags['selectoption'], $optname, $this->parseAttributes($optionsHere), h($title));
		}

		$select .= sprintf($this->tags['selectend']);
		return $select;
	}

	/**
	 * generate button-tag
	 * 
	 * @param string $name name of button
	 * @param string $value value of button
	 * @param array $htmlAttributes tag-attributes
	 * @param array $html html to insert inside button
	 * @return string html 
	 */
	function button($name, $value = '', $htmlAttributes = null, $html = '', $escapeHtml = false) {
		$this->checkForOpenForm();
		if ($escapeHtml) {
			$html = h($html);
		}
		return sprintf($this->tags['button'], $name, $value, $this->parseAttributes($htmlAttributes), $html);
	}

	/**
	 * generate submit-button
	 * 
	 * @param string $title title of button
	 * @param array $htmlAttributes tag-attributes
	 * @return string html 
	 */
	function submit($title, $htmlAttributes = null) {
		$this->checkForOpenForm();
		return sprintf($this->tags['submit'], $title, $this->parseAttributes($htmlAttributes));
	}

	/**
	 * generate reset-button
	 * 
	 * @param string $title title of button
	 * @param array $htmlAttributes tag-attributes
	 * @return string html 
	 */
	function reset($title, $htmlAttributes = null) {
		$this->checkForOpenForm();
		return sprintf($this->tags['reset'], $title, $this->parseAttributes($htmlAttributes));
	}

}



//============ lib/views/helpers/html.php =======================================================


/**
 * @package kata
 */




/**
 * the default html-helper, available in each view.
 * modify this helper, or build your own one.
 * @package kata_helper
 * @author mnt@codeninja.de
 */
class HtmlHelper extends Helper {

	/**
	 * build a href link
	 * 
	 * @param string $title what the link should say
	 * @param string url where the link should point (is automatically expanded if relative link)
	 * @param array $htmlAttributes array of attributes of the link, for example "class"=>"dontunderline"
	 * @param string an optional message that asks if you are really shure if you click on the link and aborts navigation if user clicks cancel, ignored if false
	 * @param boolean $escapeTitle if we should run htmlspecialchars over the links title
	 */
	function link($title, $url, $htmlAttributes = array (), $confirmMessage = false, $escapeTitle = true) {
		if ($escapeTitle) {
			$title = htmlspecialchars($title, ENT_QUOTES);
		}

		if ($confirmMessage) {
			$confirmMessage = $this->escape($confirmMessage, true);
			$htmlAttributes['onclick'] = 'return confirm(\'' . $confirmMessage . '\');';
		}

		if (((strpos($url, '://')) || (strpos($url, 'javascript:') === 0) || (strpos($url, 'mailto:') === 0) || substr($url, 0, 1) == '#')) {
			return sprintf($this->tags['link'], $url, $this->parseAttributes($htmlAttributes), $title);
		} else {
			return sprintf($this->tags['link'], $this->url($url, true), $this->parseAttributes($htmlAttributes), $title);
		}
	}

	/**
	 * builds a complete link to an image. if relative it is assumed the image can be found in "webroot/"
	 * 
	 * @param string $url filename of the image (is automatically expanded if relative link)
	 * @param array $htmlAttributes array of attributes of the link, for example "class"=>"dontunderline"
	 */
	function image($url, $htmlAttributes = array ()) {
		return sprintf($this->tags['image'], $this->url($url), $this->parseAttributes($htmlAttributes));
	}

	/**
	 * build select/option tags
	 * 
	 * <samp>
	 * $arr = array('blue'=>'Blue color','red'=>'Red color');
	 * echo $html->selectTag('gameinput',$arr,'red');
	 * </samp>
	 * 
	 * @param string $fieldName name-part of the select-tag
	 * @param array $optionElements array with key/value elements (key=option-tags value-part, name=between option tag)
	 * @param string $selected keyname of the element to be default selected
	 * @param array $selectAttr array of attributes of the select-tag, for example "class"=>"dontunderline"
	 * @param array $optionAttr array of attributes for each option-tag, for example "class"=>"dontunderline"
	 * @param boolean $showEmpty if we should display an empty option as the default selection so the user knows (s)he has to select something
	 */
	function selectTag($fieldName, $optionElements, $selected = null, $selectAttr = array (), $optionAttr = array (), $showEmpty = false) {
		$select = array ();
		if (!is_array($optionElements)) {
			return null;
		}
		if (isset ($selectAttr) && array_key_exists("multiple", $selectAttr)) {
			$select[] = sprintf($this->tags['selectmultiplestart'], $fieldName, $this->parseAttributes($selectAttr));
		} else {
			$select[] = sprintf($this->tags['selectstart'], $fieldName, $this->parseAttributes($selectAttr));
		}
		if ($showEmpty == true) {
			$select[] = sprintf($this->tags['selectempty'], $this->parseAttributes($optionAttr));
		}
		foreach ($optionElements as $name => $title) {

			$optionsHere = $optionAttr;

			if (($selected != null) && ((string)$selected == (string)$name)) {
				$optionsHere['selected'] = 'selected';
			}
			elseif (is_array($selected) && in_array($name, $selected)) {
				$optionsHere['selected'] = 'selected';
			}

			$select[] = sprintf($this->tags['selectoption'], $name, $this->parseAttributes($optionsHere), h($title));

		}

		$select[] = sprintf($this->tags['selectend']);
		return implode("\n", $select);
	}

	/**
	 * escape string stuitable for javascript output. can also be used to
	 * escape raw php code you want to output (for include or eval) but NOT
	 * to escape HTML for XSS-protection!
	 * 
	 * @param string $s string to escape
	 * @param bool $singleQuotes if single quotes should be escaped (true) or double-quotes (false)
	 */
	function escape($s, $singleQuotes = true) {
		if ($singleQuotes) {
			return str_replace(array (
				"'",
				"\n",
				"\r"
			), array (
				'\\\'',
				"\\n",
				""
			), $s);
		}
		return str_replace(array (
			'"',
			"\n",
			"\r"
		), array (
			'\\"',
			"\\n",
			""
		), $s);
	}


/**
 * generate individual tags if DEBUG>0 OR pack all files into a single one, chache the file,
 * replace with a single tag that points to a url that reads the cached+joined file
 *
 * Minifies only in DEBUG <= 0
 *
 * all .c.css files are computed
 * 
 * @param array $files individual files, relative to webroot
 * @param string $target js/css
 * @param string $tagFormat printf-able string of the individual tag
 * @param bool $cacheAndMinify if we should join+compress+cache given target
 * @param bool $rtl include rtl-stuff instead of ltr-stuff
 */
	private function joinFiles($files,$target,$tagFormat,$cacheAndMinify,$rtl=false) {
		// debugging? just return individual tags
		if (!$cacheAndMinify) {
			$html = '';
			foreach ($files as $file) {
				if ('.c.css'==substr($file,-6,6)) {
					$html .= $this->joinFiles(array($file),$target,$tagFormat,true,$rtl);
				} else {
					$html .= sprintf($tagFormat,$this->url($target.'/'.$file));
				}
			}
			return $html;
		}

		kataMakeTmpPath('cache');

		// cachefile exists and is young enough?
		$slug = md5(implode($files,',').(defined('VERSION')?VERSION:'').($rtl?'rtl':''));
		$cacheFile = KATATMP.'cache'.DS.$target.'.cache.'.$slug;
		if (file_exists($cacheFile) && (time()-filemtime($cacheFile)<HOUR) && DEBUG <= 0) {
			return sprintf($tagFormat,$this->url($target.'/_cache-'.$slug));
		}

		// build cachefile
		$content = '';
		foreach ($files as $file) {
			$x = strpos($file,'?');
			if ($x>0) { $file = substr($file,0,$x); }

			$txt = file_get_contents(WWW_ROOT.$target.DS.$file);
			if (false === $txt) {
				throw new Exception("html: cant find $target-file '$file'");
			}
			if ('.c.css'==substr($file,-6,6)) {
				$txt = $this->filterCss($txt,$rtl);
			}

			$content .= $txt."\n\n\n\n\n\n";
		}

		$ignoreMinify = (DEBUG > 0);

		if ('css' == $target && !$ignoreMinify) {
			$miniUtil = getUtil('Minify');
			$content = $miniUtil->css($content);
		}
		if ('js' == $target && !$ignoreMinify) {
			$miniUtil = getUtil('Minify');
			$content = $miniUtil->js($content);
		}

		file_put_contents($cacheFile,$content);

		return sprintf($tagFormat,$this->url($target.'/_cache-'.$slug.(DEBUG > 0 ? '?'.time() : '')));
	}

/**
 * return javascript-tags for all given files.
 * if DEBUG<=0 all files are joined into a single one and are sent compressed to the browser
 * otherweise individual javascript-src-tags are generated
 * 
 * @param array $files filename of script to include, relative to webroot/js/
 * @param bool $cacheAndMinify if we should join+compress+cache given target
 * @return string javascript-tag(s)
 */
	function javascriptTag($files,$cacheAndMinify=false) {
		if (is_string($files)) { $files = array($files); }
		return $this->joinFiles($files,'js',$this->tags['jsfile'],$cacheAndMinify);
	}

/**
 * return css-tags for all given files.
 * if DEBUG<=0 all files are joined into a single one and are sent compressed to the browser,
 * otherweise individual link-tags are generated
 * 
 * @param array $files filename of css to include, relative to webroot/css/
 * @param bool $rtl if we should include rtl-styles instead of ltr
 * @param bool $cacheAndMinify if we should join+compress+cache given target
 * @return string css-tag(s)
 */
	function cssTag($files,$cacheAndMinify=false,$rtl=false) {
		if (is_string($files)) { $files = array($files); }
		return $this->joinFiles($files,'css',$this->tags['cssfile'],$cacheAndMinify,$rtl);
	}

/**
 * output a url with __token get parameter appended. used for xsrf-detection
 * 
 * @param string $url url to add __token to
 * @return string url with token appended
 */
	function tokenUrl($url) {
		$url = $this->url($url);
		$token = is($this->vars['__token'],'');
		if ('' == $token) {
			return $url;
		}

		$x = strpos($url,'?');
		if (false !== $x) {
			return substr($url,0,$x).'?__token='.$token.'&'.substr($url,$x+1);
		}

		$x = strpos($url,'#');
		if (false !== $x) {
			return substr($url,0,$x).'?__token='.$token.substr($url,$x+1);
		}

		return $url.'?__token='.$token;
	}

/**
 * compress given css-string
 * 
 * @param string $css styles to compress
 * @return string the compressed css
 */
	function compressCss($css) {
		$minifyUtil = getUtil('Minify');
		return $minifyUtil->css($css);
	}

	function filterCss($css,$rtl=false) {
		$lines = explode('}',$css);
		unset($css);

		$output = '';
		foreach ($lines as $line) {
			$line = trim($line);
			if ('' != $line) {
				$parts = explode('{',$line);
				if (count($parts)<2) {
					throw new Exception('RTLCSS parse error: '.$line);
				}
				if (strtolower(substr($parts[0],0,7))!='/*rtl*/' && !$rtl) {
					$output.=$line."}\n";
				}
				if (strtolower(substr($parts[0],0,7))!='/*ltr*/' && $rtl) {
					$output.=$line."}\n";
				}
			}
		}

		return $output;
	}

}



//============ lib/views/helpers/js.php =======================================================


/**
 * @package kata
 */




/**
 * nano-helper for javascript. simply add() javascript in your view, and get() it inside the head-section of your layout
 * @author mnt@codeninja.de
 * @package kata_helper
 */

class JsHelper extends Helper {

	private $jsLines='';

/**
 * add javascript to buffer inside a view
 * 
 * @param string $js javascript
 */
	function add($js) {
		$this->jsLines = $this->jsLines."\n".$js;
	}

/**
 * return buffer. use inside your layout:
 * 
 * <code>
 * ...inside head-element inside your layout
 * [script type="application/javascript"]
 * echo $js->get();
 * [/script]
 * </code>
 * 
 * @return string joined javascript-strings you gave the helper via add()
 */
	function get() {
	   return $this->jsLines;
	}

/**
 * like get, but compresses the javascript on the fly
 *
 * @return string compressed javascript
 */
	function getCompressed() {
		return $this->compress($this->jsLines);
	}

/**
 * quote string for inclusion in javascript-strings
 *
 * @param string $s string to quote
 * @return string quoted string
 */
	function quote($s,$withDoubleQuotes=false) {
		if ($withDoubleQuotes) {
			return str_replace('"','\\"',$s);
		}
		return str_replace("'","\\'",$s);
	}

/**
 * return the given javascript-string in compressed form
 * 
 * @param string $js your javascript
 * @return string your javascript compressed
 */
	function compress($js) {
		$minifyUtil = getUtil('Minify');
		return $minifyUtil->js($js);
	}

}



//============ lib/basics.php =======================================================



/**
 * several convenience defines and functions
 *
 * Kata - Lightweight MVC Framework <http://www.codeninja.de/>
 * Copyright 2007-2009 mnt@codeninja.de, gameforge ag
 *
 * Licensed under The GPL License
 * Redistributions of files must retain the above copyright notice.
 * 
 * @author mnt
 * @package kata_internal
 */
/**
 * internal function to dump variables into the browser if DEBUG==2. just define your own function if you want firebug or something like it
 */
if (!function_exists('debug')) {

	/**
	 * print out type and content of the given variable if DEBUG-define (in config/core.php) > 0
	 * @param mixed $var     Variable to debug
	 * @param mixed $deprecated  deprecated
	 */
	function debug($var = false, $deprecated = false) {
		if (DEBUG < 1) {
			return;
		}

		ob_start();
		if (function_exists('xdebug_var_dump')) {
			xdebug_var_dump($var);
		} else {
			var_dump($var);
		}
		$var = ob_get_clean();
		
		kataDebugOutput($var);
	}

}

/**
 * Recursively strips slashes from all values in an array
 * @param mixed $value
 * @return mixed
 */
function stripslashes_deep($value) {
	if (is_array($value)) {
		return array_map('stripslashes_deep', $value);
	} else {
		return stripslashes($value);
	}
}

/**
 * Recursively urldecodes all values in an array
 * @param mixed $value
 * @return mixed
 */
function urldecode_deep($value) {
	if (is_array($value)) {
		return array_map('urldecode_deep', $value);
	} else {
		return urldecode($value);
	}
}

/**
 * write a string to the log in KATATMP/logs. if DEBUG<0 logentries that have KATA_DEBUG are not written.
 *
 * @param string $what string to write to the log
 * @param int $where log-level to log (default: KATA_DEBUG)
 */
function writeLog($what, $where = KATA_DEBUG) {
	$logname = 'error';
	if (is_numeric($where)) {
		if ((DEBUG < 0) && (KATA_DEBUG == $where)) {
			return;
		}
		if (KATA_DEBUG == $where) {
			$logname = 'debug';
		} elseif (KATA_PANIC == $where) {
			$logname = 'panic';
		}
	} else {
		$logname = basename($where);
	}

	kataMakeTmpPath('logs');
	$h = fopen(KATATMP . 'logs' . DS . $logname . '.log', 'a');
	if ($h) {
		fwrite($h, date('d.m.Y H:i:s ') . $what . "\n");
		fclose($h);
	}
}

/**
 * include all neccessary classes and the given model
 * 
 * @param string model name without .php - if null it just loads all needed classes
 * @package kata_model
 */
function loadModel($name) {
	if (file_exists(ROOT . 'models' . DS . strtolower($name) . '.php')) {
		//require ROOT . 'models' . DS . strtolower($name) . '.php';
		return;
	}
	throw new Exception('basics: loadModel: cant find Model [' . $name . ']');
}

/**
 * return a handle to the given model. loads and initializes the model if needed. 
 * You always get the same object, singleton-alike.
 * 
 * @param string $value model name (without .php)
 * @return object
 */
function getModel($name) {
	if (!class_exists($name)) {
		loadModel($name);
	}
	$o = classRegistry :: getObject($name);
	return $o;
}

/**
 * return class-handle of a utility-class
 * You always get the same object, singleton-alike.
 *
 * @param string $name name of the utility
 * @return object class-handle
 */
function getUtil($name) {
	$classname = $name . 'Utility';
	return classRegistry :: getObject($classname);
}

/**
 * Gets an environment variable from available sources.
 * env() knows what to do if $_SERVER/$_ENV are not available.
 *
 * @param  string $key Environment variable name.
 * @return string Environment variable setting.
 */
function env($key) {
	if ($key == 'HTTPS') {
		if (isset($_SERVER) && !empty($_SERVER)) {
			return (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on');
		} else {
			return (strpos(env('SCRIPT_URI'), 'https://') === 0);
		}
	}

	if (isset($_SERVER[$key])) {
		return $_SERVER[$key];
	} elseif (isset($_ENV[$key])) {
		return $_ENV[$key];
	} elseif (getenv($key) !== false) {
		return getenv($key);
	}

	// fallback
	if ($key == 'DOCUMENT_ROOT') {
		$offset = 0;
		if (!strpos(env('SCRIPT_NAME'), '.php')) {
			$offset = 4;
		}
		return substr(env('SCRIPT_FILENAME'), 0, strlen(env('SCRIPT_FILENAME')) - (strlen(env('SCRIPT_NAME')) + $offset));
	}

	// fallback
	if ($key == 'PHP_SELF') {
		return str_replace(env('DOCUMENT_ROOT'), '', env('SCRIPT_FILENAME'));
	}

	return null;
}

/**
 * merge any number of arrays
 * @param array first array
 * @param array second array and so on
 * @return array the merged array
 */
function am() {
	$result = array();
	foreach (func_get_args () as $arg) {
		if (!is_array($arg)) {
			$arg = array(
				$arg
			);
		}
		$result = array_merge($result, $arg);
	}
	return $result;
}

/**
 * loads the given files in the VENDORS directory if not already loaded
 * @param string $name Filename without the .php part.
 */
function vendor($name) {
	$args = func_get_args();
	foreach ($args as $arg) {
		//require_once (ROOT . 'vendors' . DS . $arg . '.php');
	}
}

/**
 * Convenience method for htmlspecialchars. you should use this instead of echo to avoid xss-exploits
 * @param string $text
 * @return string
 */
function h($text) {
	if (is_array($text)) {
		return array_map('h', $text);
	}
	return htmlspecialchars($text);
}

/**
 * convenience method to check if given value is set. if so, value is return, otherwise the default
 * @param mixed $arg value to check
 * @param mixed $default value returned if $value is unset
 */
function is(& $arg, $default = null) {
	if (isset($arg)) {
		return $arg;
	}
	return $default;
}

$dispatcher= new dispatcher();
echo $dispatcher->dispatch(isset ($_GET['kata']) ? $_GET['kata'] : '', isset ($routes) ? $routes : null);

