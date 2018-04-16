<?php
require_once(__DIR__ . '/vendor/autoload.php');
use BotMan\BotMan\Cache\DoctrineCache;
use BotMan\BotMan\BotMan;
use BotMan\BotMan\BotManFactory;
use BotMan\BotMan\Drivers\DriverManager;
use Doctrine\Common\Cache\FilesystemCache;
require_once(__DIR__ . '/conversations/mainConversation.php');
$config = [];
// Load the driver(s) you want to use
DriverManager::loadDriver(\BotMan\Drivers\Web\WebDriver::class);
$cache_path = __DIR__ . '/cache';
$doctrineCacheDriver = new FilesystemCache($cache_path);
// Create an instance
$botman = BotManFactory::create($config, new DoctrineCache($doctrineCacheDriver));

// Give the bot something to listen for.
$botman->hears('hi', function($bot) {
	$bot->startConversation(new OnboardingConversation);
});
// mặc định khi không hiểu message
$botman->fallback(function($bot) {
	$bot->reply('Xin lỗi, Ca này khó, chưa chém được nhé!!!');
});

// Start listening
$botman->listen();

