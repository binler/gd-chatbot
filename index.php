<?php
require_once 'vendor/autoload.php';
use BotMan\BotMan\Cache\DoctrineCache;
use BotMan\BotMan\BotMan;
use BotMan\BotMan\BotManFactory;
use BotMan\BotMan\Drivers\DriverManager;
use BotMan\BotMan\Messages\Outgoing\Question;
use BotMan\BotMan\Messages\Conversations\Conversation;
use BotMan\BotMan\Messages\Incoming\Answer;
use Doctrine\Common\Cache\FilesystemCache;
use BotMan\BotMan\Messages\Outgoing\Actions\Button;
$config = [];
// $dataMessage = file_get_contents('./data.json');
// $json = json_decode($dataMessage, true);
// echo '<pre>' . print_r($json, true) . '</pre>';
// die();
// Load the driver(s) you want to use
DriverManager::loadDriver(\BotMan\Drivers\Web\WebDriver::class);
$doctrineCacheDriver = new FilesystemCache(__DIR__);
// Create an instance
$botman = BotManFactory::create($config, new DoctrineCache($doctrineCacheDriver));

class OnboardingConversation extends Conversation
{
    protected $firstname;

    protected $email;

    public function askFirstname()
    {
        $this->ask('Xin chào! Bạn tên gì?', function(Answer $answer) {
            // Save result
            $this->firstname = $answer->getText();

            $this->say('Rất vui được gặp bạn '.$this->firstname);
            $this->askEmail();
        });
    }

    public function askEmail()
    {
        $this->ask('Xin cái email nữa đi được không?', function(Answer $answer) {
            // Save result
            $this->email = $answer->getText();

            $this->say('Great - Hết rồi đó, '.$this->firstname);
            $this->askForDatabase();
        });

    }

    public function askForDatabase()
	{
	    $question = Question::create('Bạn có muốn chúng tôi hổ trợ gì không?')
	        ->fallback('Unable to create a new database')
	        ->callbackId('create_database')
	        ->addButtons([
	            Button::create('Có')->value('yes'),
	            Button::create('Không')->value('no'),
	        ]);

	    $this->ask($question, function (Answer $answer) {
	        // Detect if button was clicked:
	        if ($answer->isInteractiveMessageReply()) {
	            $selectedValue = $answer->getValue(); // will be either 'yes' or 'no'
	            $selectedText = $answer->getText(); // will be either 'Of course' or 'Hell no!'
	        }
	    });
	}

    public function run()
    {
        // This will be called immediately
        $this->askFirstname();
    }
}

// Give the bot something to listen for.
$botman->hears('.*(hi|hello|xin chào|xin chao |chao|chào).*', function($bot) {
	$bot->startConversation(new OnboardingConversation);
});

// mặc định khi không hiểu message
$botman->fallback(function($bot) {
	$bot->reply('Xin lỗi, Ca này khó, chưa chém được nhé!!!');
});

// Start listening
$botman->listen();

