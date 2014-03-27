<?php

use Silex\Application;
use Silex\Provider\DoctrineServiceProvider;
use Silex\Provider\FormServiceProvider;
use Silex\Provider\MonologServiceProvider;
use Silex\Provider\SecurityServiceProvider;
use Silex\Provider\ServiceControllerServiceProvider;
use Silex\Provider\SessionServiceProvider;
use Silex\Provider\TwigServiceProvider;
use Silex\Provider\UrlGeneratorServiceProvider;
use Silex\Provider\ValidatorServiceProvider;
use Silex\Provider\WebProfilerServiceProvider;
use SilexAssetic\AsseticServiceProvider;
use Silex\Provider\FacebookServiceProvider;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Silex\Provider\TranslationServiceProvider;
use Symfony\Component\Translation\Loader\YamlFileLoader;
use CHH\Silex\CacheServiceProvider;
use FHJ\Providers\FacebookUserProvider;
use FHJ\Events\EventIdentifiers;
use FHJ\Repositories\UserDbRepository;
use FHJ\Repositories\ProjectDbRepository;
use FHJ\Converters\ProjectConverter;
use FHJ\Converters\UserConverter;
use FHJ\Controllers\HomepageController;
use FHJ\Controllers\UserListController;
use FHJ\Controllers\UserDeleteController;
use FHJ\Controllers\UserEditController;
use FHJ\Controllers\ProjectListController;
use FHJ\Controllers\ProjectDeleteController;
use FHJ\Controllers\ProjectEditController;
use FHJ\Controllers\BuildStatusUpdateController;
use FHJ\Facebook\SocialEventListener;
use FHJ\Facebook\FacebookConfig;
use FHJ\Framework\AppFormExtensionLoader;

// this is needed for the secure() method to work. see controllers.php.
$app['route_class'] = 'FHJ\Framework\SecuredRoute';

$app->register(new SessionServiceProvider());
$app->register(new ValidatorServiceProvider());
$app->register(new UrlGeneratorServiceProvider());
$app->register(new ServiceControllerServiceProvider());

$app->register(new FormServiceProvider());
$app['form.extensions'] = $app->share($app->extend('form.extensions', function ($extensions) use ($app) {
	$extensions[] = new AppFormExtensionLoader();

	return $extensions;
}));

$app->register(new FacebookServiceProvider(), array(
	'facebook.config' => array(
		'appId'      => $app['facebook.appId'],
		'secret'     => $app['facebook.secret'],
		'appName'    => 'https://apps.facebook.com/' . $app['facebook.appNamespace'] . '/',
		// the $app->share function is resolved when the server_url is needed
		// at that time, $app['url_generator'] is already known by the system
		// (which is not the case at the moment)
		'server_url' => $app->share(function($app) {
				return $app['url_generator']->generate('homepage');
			}),
		'fileUpload' => false
	),
	'facebook.permissions' => array(
		'email',
		'basic_info',
		'user_groups',
		'publish_actions'
	)
));

$app->register(new SecurityServiceProvider(), array(
    'security.firewalls' => array(
	    'public' => array(
		    'pattern' => '^/$',
		    'anonymous' => true,
	    ),
        'secured_area'  => array(
	        'anonymous' => false,
            'pattern'   => '^/(login_check|projects|users)',
	        'facebook'  => array(
		        'check_path' => '/login_check'
	        ),
            'logout' => true,
	        'users' => $app->share(function () use ($app) {
				return new FacebookUserProvider($app['repository.users'], $app['facebook']);
	        }),
        ),
    ),
));

$app['security.role_hierarchy'] = array(
	'ROLE_ADMIN' => array('ROLE_USER'),
);

// Do not remove this component, as forms are not going to work anymore then
// (The trans function is then missing)
$app->register(new TranslationServiceProvider());
$app['translator'] = $app->share($app->extend('translator', function($translator, $app) {
	$translator->addLoader('yaml', new YamlFileLoader());
	$translator->addResource('yaml', __DIR__.'/../resources/locales/en.yml', 'en');

	return $translator;
}));


$app->register(new MonologServiceProvider(), array(
    'monolog.logfile' => __DIR__.'/../resources/log/app.log',
    'monolog.name'    => 'app',
    'monolog.level'   => $app['logging.loglevel']
));

$app->register(new TwigServiceProvider(), array(
    'twig.options'         => array(
        'cache'            => isset($app['twig.options.cache']) ? $app['twig.options.cache'] : false,
        'strict_variables' => true
    ),
    'twig.form.templates'  => array('form_div_layout.html.twig', 'common/form_div_layout.html.twig'),
    'twig.path'            => array(__DIR__ . '/../resources/views')
));

$app->register(new CacheServiceProvider(), array(
	'cache.options' => array(
		'default' => array(
			'driver'    => 'filesystem',
			'directory' => $app['data.cache'],
			'lifetime'  => 86400
		)
	)
));

if ($app['debug'] && isset($app['cache.path'])) {
    $app->register(new WebProfilerServiceProvider(), array(
        'profiler.cache_dir' => $app['cache.path'].'/profiler',
    ));
}

if (isset($app['assetic.enabled']) && $app['assetic.enabled']) {
    $app->register(new AsseticServiceProvider(), array(
        'assetic.options'      => array(
            'debug'            => $app['debug'],
            'auto_dump_assets' => $app['debug'],
        )
    ));

    $app['assetic.filter_manager'] = $app->share(
        $app->extend('assetic.filter_manager', function($fm, $app) {
            $fm->set('lessphp', new Assetic\Filter\LessphpFilter());

            return $fm;
        })
    );

    $app['assetic.asset_manager'] = $app->share(
        $app->extend('assetic.asset_manager', function($am, $app) {
            $am->set('styles', new Assetic\Asset\AssetCache(
                new Assetic\Asset\GlobAsset(
                    $app['assetic.input.path_to_css'],
                    array($app['assetic.filter_manager']->get('lessphp'))
                ),
                new Assetic\Cache\FilesystemCache($app['assetic.path_to_cache'])
            ));
            $am->get('styles')->setTargetPath($app['assetic.output.path_to_css']);

            $am->set('scripts', new Assetic\Asset\AssetCache(
                new Assetic\Asset\GlobAsset($app['assetic.input.path_to_js']),
                new Assetic\Cache\FilesystemCache($app['assetic.path_to_cache'])
            ));
            $am->get('scripts')->setTargetPath($app['assetic.output.path_to_js']);

            return $am;
        })
    );

}

$app->register(new DoctrineServiceProvider(), array(
	'dbs.options' => array (
		'db' => array(
			'driver'    => $dbDriver,
			'host'      => $dbHost,
			'dbname'    => $dbName,
			'user'      => $dbUser,
			'password'  => $dbPassword,
			'charset'   => 'utf8',
		)
	),
));

unset($dbDriver, $dbHost, $dbName, $dbUser, $dbPassword);

// Repositories
$app['repository.users'] = new UserDbRepository($app['dbs']['db'], $app['monolog']);
$app['repository.projects'] = new ProjectDbRepository($app['dbs']['db'], $app['monolog']);

// Social posting service
$app['postingService'] = new SocialEventListener($app['repository.users'], new FacebookConfig($app['facebook.appId'],
		$app['facebook.appNamespace'], $app['facebook.secret']), $app['monolog']);

// Event dispatcher
$app['socialEventDispatcher'] = new EventDispatcher();
$app['socialEventDispatcher']->addListener(EventIdentifiers::EVENT_BUILD_STATUS_UPDATE,
    array($app['postingService'], 'onProjectBuildStatusUpdate'));

// Converters
$app['converter.user'] = $app->protect(function ($value) use ($app) {
	$converter = new UserConverter($app['repository.users']);
	return $converter->convert($value);
});

$app['converter.project'] = $app->protect(function ($value) use ($app) {
	$converter = new ProjectConverter($app['repository.projects']);
	return $converter->convert($value);
});

// Controllers
$app['controller.homepage'] = $app->share(function(Application $app) {
	return new HomepageController($app);
});

$app['controller.userList'] = $app->share(function(Application $app) {
    return new UserListController($app);
});

$app['controller.userDelete'] = $app->share(function(Application $app) {
    return new UserDeleteController($app);
});

$app['controller.userEdit'] = $app->share(function(Application $app) {
    return new UserEditController($app);
});

$app['controller.projectList'] = $app->share(function(Application $app) {
    return new ProjectListController($app);
});

$app['controller.projectDelete'] = $app->share(function(Application $app) {
    return new ProjectDeleteController($app);
});

$app['controller.projectEdit'] = $app->share(function(Application $app) {
    return new ProjectEditController($app);
});

$app['controller.buildStatusUpdate'] = $app->share(function(Application $app) {
    return new BuildStatusUpdateController($app);
});

return $app;
