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

    protected $services = [
    	"supports" => [

			"level1" => [
				"item" => [
					["title" => "Về thông tin tuyển dụng"],
					["title" => "Trao đổi dự án"],
					["title" => "Những câu hỏi khác"]
				]
			],

			"level2" => [
				"item" => [
					["title" => "Vị trí đang tuyển dụng"],
					["title" => "Về trợ cấp phúc lợi"],
					["title" => "Những câu hỏi khác"]
				]
			],

			"level3" => [
				"item" => [
					["title" => "Backend Engineer"],
					["title" => "Frontend Engineer"],
					["title" => "Bridge Engineer"]
				]
			],

			"level4" => [
				"item" => [
					["title" => "Kinh nghiệm PHP　trên 3 năm"],
					["title" => "Kinh nghiệm Javascript　trên 3 năm"],
					["title" => "Kinh nghiệm Python　trên 3 năm"]
				]
			]
	    ]
	];

	public function getListButton($array_list, $keyword) {
		$list_button = [];
		foreach ($array_list as $key => $val) {
			array_push($list_button, Button::create($val[$keyword])->value($val[$keyword]));
		}
		return $list_button;
	}

    public function askService($question)
    {
    	//$dataMessage = file_get_contents('./data.json');
		//$services = json_decode($dataMessage, true);
		$list_service = $this->getListButton($this->services["supports"]["level1"]["item"], 'title');
		$question = Question::create($question)
	        ->fallback('service')
	        ->callbackId('service_id')
	        ->addButtons($list_service);

	    $this->ask($question, function (Answer $answer) {
	        // Detect if button was clicked:
	        if ($answer->isInteractiveMessageReply()) {
	            $selectedText = $answer->getText();
	            if($selectedText == 'Về thông tin tuyển dụng') {
	            	$this->say('Vâng, bạn muốn tìm hiểu về thông tin tuyển dụng đúng không nhỉ!');
					$this->askRecruitment();
				}
	        }
	    });
	}
	
	public function askRecruitment() {
		$list_button = $this->getListButton($this->services["supports"]["level2"]["item"], "title");
		$question = Question::create('Các mục bên dưới được tìm thấy liên quan đến tuyển dụng.')
	        ->fallback('Recruitment')
	        ->callbackId('Recruitment_id')
	        ->addButtons($list_button);

	    $this->ask($question, function (Answer $answer) {
	        // Detect if button was clicked:
	        if ($answer->isInteractiveMessageReply()) {
	            $selectedText = $answer->getText();
	            if($selectedText == 'Vị trí đang tuyển dụng') {
	            	$this->say('Thắc mắc của bạn là về vị trí đang tuyển dụng nhỉ.');
					$this->askPositionRecruiment();
	            }
	        }
	    });
	}

	public function askPositionRecruiment() {
		$list_button = $this->getListButton($this->services["supports"]["level3"]["item"], "title");
		$question = Question::create('Các mục bên dưới được tìm thấy liên quan đến tuyển dụng.')
	        ->fallback('positionRecruitment')
	        ->callbackId('positionRecruitment_id')
	        ->addButtons($list_button);

	    $this->ask($question, function (Answer $answer) {
	        // Detect if button was clicked:
	        if ($answer->isInteractiveMessageReply()) {
	            $selectedText = $answer->getText();
	            if($selectedText == 'Backend Engineer') {
	            	$this->say("Mong muốn của bạn là $selectedText phải không nhỉ?");
					$this->askWorkExperient();
	            }
	        }
	    });
	}

	public function askWorkExperient() {
		$list_button = $this->getListButton($this->services["supports"]["level4"]["item"], "title");
		$question = Question::create('Trước khi đăng ký ứng tuyển thì bạn cho tôi biết skill của bạn được không?')
	        ->fallback('WorkExperient')
	        ->callbackId('WorkExperient_id')
	        ->addButtons($list_button);

	    $this->ask($question, function (Answer $answer) {
	        // Detect if button was clicked:
	        if ($answer->isInteractiveMessageReply()) {
	            $selectedText = $answer->getText();
	            if($selectedText == 'Kinh nghiệm PHP　trên 3 năm') {
	            	$this->say("Vậy là bạn đã có $selectedText nhỉ");
	            	$this->say("Nếu là người có $selectedText thì ở công ty tôi lương sẽ tầm xxx USD.");
	            	$this->say("Nhất định hãy gửi CV đến nhân sự công ty tôi nhé.");
					$this->askService('Bạn còn có thắc mắc nào khác không?');
	            }
	        }
	    });
	}

    public function run()
    {
        // This will be called immediately
        $this->askService('Hôm nay, bạn muốn hỏi liên quan đến vấn đề gì ạ. Vui lòng chọn các option sau nhé.');
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

