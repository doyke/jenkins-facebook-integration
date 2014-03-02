<?php

namespace SKE\Composer;

class Script
{
    public static function install()
    {
        self::chmodOrMkdir('resources/cache', 0777);
	    self::chmodOrMkdir('resources/log', 0777);
	    self::chmodOrMkdir('web/assets', 0777);
        chmod('console', 0500);

	    self::checkProdFile();

        exec('php console assetic:dump');
    }

	private static function chmodOrMkdir($directory, $mode)
	{
		if (!is_dir($directory))
		{
			mkdir($directory, $mode, true);
			return;
		}

		chmod($directory, $mode);
	}

	private static function checkProdFile()
	{
		if (!file_exists('resources/config/prod.php'))
		{
			copy('resources/config/prod.php.dist', 'resources/config/prod.php');
		}
	}
}
