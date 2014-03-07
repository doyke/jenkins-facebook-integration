<?php

use Silex\Provider\DoctrineServiceProvider;
use Silex\Provider\FormServiceProvider;
use Silex\Provider\MonologServiceProvider;
use Silex\Provider\SecurityServiceProvider;
use Silex\Provider\ServiceControllerServiceProvider;
use Silex\Provider\SessionServiceProvider;
use Silex\Provider\TranslationServiceProvider;
use Silex\Provider\TwigServiceProvider;
use Silex\Provider\UrlGeneratorServiceProvider;
use Silex\Provider\ValidatorServiceProvider;
use Silex\Provider\WebProfilerServiceProvider;
use SilexAssetic\AsseticServiceProvider;
use Symfony\Component\Security\Core\Encoder\PlaintextPasswordEncoder;
use Symfony\Component\Translation\Loader\YamlFileLoader;
use FHJ\ServiceProviders\FacebookServiceProvider;
use FHJ\UserProviders\FacebookUserProvider;
use FHJ\Repositories\DbRepository;
use FHJ\Repositories\SVNPlotRepository;

$app->register(new SessionServiceProvider());
$app->register(new ValidatorServiceProvider());
$app->register(new FormServiceProvider());
$app->register(new UrlGeneratorServiceProvider());

$app->register(new SecurityServiceProvider(), array(
    'security.firewalls' => array(
	    'public' => array(
		    'pattern' => '^/',
		    'anonymous' => true,
	    ),
        'admin' => array(
            'pattern' => '^/settings',
	        'facebook' => array(
		        'check_path' => '/login_check',
		        'login_path' => '/login',
	        ),
            'logout'    => true,
	        'users' => $app->share(function () use ($app) {
				return new FacebookUserProvider($app['repository.db']);
	        }),
        ),
    ),
));

$app['security.encoder.digest'] = $app->share(function ($app) {
    return new PlaintextPasswordEncoder();
});

$app->register(new TranslationServiceProvider());
$app['translator'] = $app->share($app->extend('translator', function($translator, $app) {
    $translator->addLoader('yaml', new YamlFileLoader());
    $translator->addResource('yaml', __DIR__.'/../resources/locales/en.yml', 'en');

    return $translator;
}));

$app->register(new MonologServiceProvider(), array(
    'monolog.logfile' => __DIR__.'/../resources/log/app.log',
    'monolog.name'    => 'app',
    'monolog.level'   => 300 // = Logger::WARNING
));

$app->register(new TwigServiceProvider(), array(
    'twig.options'        => array(
        'cache'            => isset($app['twig.options.cache']) ? $app['twig.options.cache'] : false,
        'strict_variables' => true
    ),
    'twig.form.templates' => array('form_div_layout.html.twig', 'common/form_div_layout.html.twig'),
    'twig.path'           => array(__DIR__ . '/../resources/views')
));

if ($app['debug'] && isset($app['cache.path'])) {
    $app->register(new ServiceControllerServiceProvider());
    $app->register(new WebProfilerServiceProvider(), array(
        'profiler.cache_dir' => $app['cache.path'].'/profiler',
    ));
}

if (isset($app['assetic.enabled']) && $app['assetic.enabled']) {
    $app->register(new AsseticServiceProvider(), array(
        'assetic.options' => array(
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
		),
		'svnplot' => array(
			'driver'    => 'pdo_sqlite',
			'path'      => __DIR__.'/app.db',
		),
	),
));

unset($dbDriver, $dbHost, $dbName, $dbUser, $dbPassword);

$app['repository.db'] = new DbRepository($app['dbs']['db'], $app['monolog']);
$app['repository.svnplot'] = new SVNPlotRepository($app['dbs']['svnplot'], $app['monolog']);

$app->register(new FacebookServiceProvider(), array(
	'facebook.config' => array(
		'appId'      => $app['facebook.appId'],
		'secret'     => $app['facebook.secret'],
		'fileUpload' => false, // optional
	),
	'facebook.permissions' => array('email', 'basic_info', 'user_groups', 'publish_actions'),
));

return $app;
