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
// Load the driver(s) you want to use
DriverManager::loadDriver(\BotMan\Drivers\Web\WebDriver::class);
$doctrineCacheDriver = new FilesystemCache(__DIR__);
// Create an instance
$botman = BotManFactory::create($config, new DoctrineCache($doctrineCacheDriver));

class OnboardingConversation extends Conversation
{
    protected $firstname;

    protected $email;

    protected $services = [
    	"supports" => [
	    	["title" => "Về thông tin tuyển dụng",
	    		"item" => [
	    			["title" => "Vị trí đang tuyển dụng"],
	    			["title" => "Về trợ cấp phúc lợi"],
	    			["title" => "Những câu hỏi khác"]
	    		]
	    	],

	    	["title" => "Trao đổi dự án"],
	    	["title" => "Những câu hỏi khác"],
	    ]
    ];

    public function askService()
    {
    	//$dataMessage = file_get_contents('./data.json');
		//$services = json_decode($dataMessage, true);
		$list_service = [];
		reset($this->services["supports"]);
		while (list($key, $val) = each($this->services["supports"])) {
    		array_push($list_service, Button::create($val["title"])->value($val["title"]) );
		};
		
		$question = Question::create('Hôm nay, bạn muốn hỏi liên quan đến vấn đề gì ạ. Vui lòng chọn các option sau nhé.')
	        ->fallback('service')
	        ->callbackId('service_id')
	        ->addButtons($list_service);

	    $this->ask($question, function (Answer $answer) {
	        // Detect if button was clicked:
	        if ($answer->isInteractiveMessageReply()) {
	            $selectedText = $answer->getText();
	            $item = array_search($selectedText, array_column($services['supports'], 'title'));
	            $this->say($selectedText);
	        }
	    });
        // $this->ask('Xin chào! Bạn tên gì?', function(Answer $answer) {
        //     // Save result
        //     $this->firstname = $answer->getText();

        //     $this->say('Rất vui được gặp bạn '.$this->firstname);
        //     $this->askEmail();
        // });
    }

    // public function askEmail()
    // {
    //     $this->ask('Xin cái email nữa đi được không?', function(Answer $answer) {
    //         // Save result
    //         $this->email = $answer->getText();

    //         $this->say('Vậy email là: , '.$this->email);
    //         $this->askForDatabase();
    //     });

    // }

 //    public function askForDatabase()
	// {
	//     $question = Question::create('Hôm nay, bạn muốn hỏi liên quan đến vấn đề gì ạ. Vui lòng chọn các option sau nhé.')
	//         ->fallback('help_me')
	//         ->callbackId('create_database')
	//         ->addButtons([
	//             Button::create('Có')->value('yes'),
	//             Button::create('Không')->value('no'),
	//         ]);

	//     $this->ask($question, function (Answer $answer) {
	//         // Detect if button was clicked:
	//         if ($answer->isInteractiveMessageReply()) {
	//             //$selectedValue = $answer->getValue(); // will be either 'yes' or 'no'
	//             $selectedText = $answer->getText(); // will be either 'Of course' or 'Hell no!'
	//             if($selectedText == "yes") {
	//             	$this->askService();
	//             } else {
	//             	$this->say("Bạn có muốn chúng tôi giúp đỡ gì nữa không?");
	//             }
	//         }
	//     });
	// }

    public function run()
    {
        // This will be called immediately
        $this->askService();
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

