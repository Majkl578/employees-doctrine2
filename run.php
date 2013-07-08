<?php

namespace Benchmark;

use Doctrine\Common\Cache\MemcacheCache;
use Doctrine\DBAL\Tools\Console\Helper\ConnectionHelper;
use Doctrine\DBAL\Types\Type;
use Doctrine\ORM\Tools\Console\Helper\EntityManagerHelper;
use Doctrine\ORM\Tools\Setup;
use Doctrine\ORM\EntityManager;
use Nette\Diagnostics\Debugger;
use Doctrine\ORM\Tools\Console\ConsoleRunner;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Helper\HelperSet;


require_once __DIR__ . '/vendor/autoload.php';


Debugger::$strictMode = TRUE;
Debugger::enable(Debugger::DEVELOPMENT);


// You may not want to use memcache? :)
$memcache = new \Memcache();
$memcache->connect('127.0.0.1');
$cache = new MemcacheCache();
$cache->setMemcache($memcache);

$config = Setup::createAnnotationMetadataConfiguration(
	[__DIR__ . '/Benchmark/Entities'],
	TRUE,
	__DIR__ . '/Benchmark/Entities/Proxies',
	$cache,
	FALSE
);
$config->setProxyNamespace('Benchmark\Entities\Proxies');
$config->setAutoGenerateProxyClasses(TRUE);


// we need __toString on DateTime, since UoW converts composite primary keys to string
// (who the hell invented composite PKs :P)
Type::overrideType(Type::DATE, 'Benchmark\Types\DateType');
Type::overrideType(Type::DATETIME, 'Benchmark\Types\DateTimeType');


// TODO you may want to change this? ;)
$em = EntityManager::create(
	[
		'driver'   => 'pdo_mysql',
		'user'     => 'root',
		'password' => '',
		'dbname'   => 'employees',
	],
	$config
);


$cli = new Application('Benchmark for Doctrine 2 ORM + Employees Sample Database', '1337-dev');
$cli->setCatchExceptions(TRUE);
$cli->setHelperSet(new HelperSet([
	'db' => new ConnectionHelper($em->getConnection()),
	'em' => new EntityManagerHelper($em),
]));
ConsoleRunner::addCommands($cli);
$cli->add(new BenchmarkCommand());
$cli->run();
