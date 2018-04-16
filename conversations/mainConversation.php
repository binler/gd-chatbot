<?php
use BotMan\BotMan\Messages\Conversations\Conversation;
use BotMan\BotMan\Messages\Outgoing\Actions\Button as Button;
use BotMan\BotMan\Messages\Outgoing\Question as Question;
use BotMan\BotMan\Messages\Incoming\Answer as Answer;

class OnboardingConversation extends Conversation
{

    protected $services = [
    	"supports" => [

			"level1" => [
				"item" => [
					["title" => "求人情報について"],
					["title" => "案件のご相談"],
					["title" => "その他ご質問"]
				]
			],

			"level2" => [
				"item" => [
					["title" => "募集中のポジションについて"],
					["title" => "福利厚生について"],
					["title" => "その他ご質問"]
				]
			],

			"level3" => [
				"item" => [
					["title" => "バックエンドエンジニア"],
					["title" => "フロントエンドエンジニア"],
					["title" => "ブリッジエンジニア"]
				]
			],

			"level4" => [
				"item" => [
					["title" => "PHP経験　3年以上"],
					["title" => "Javascript経験　3年以上"],
					["title" => "Python経験　3年以上"]
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
	            if($selectedText == '求人情報について') {
	            	$this->say('はい、求人情報についてですね');
					$this->askRecruitment();
				}
	        }
	    });
	}
	
	public function askRecruitment() {
		$list_button = $this->getListButton($this->services["supports"]["level2"]["item"], "title");
		$question = Question::create('次の項目が見つかりました')
	        ->fallback('Recruitment')
	        ->callbackId('Recruitment_id')
	        ->addButtons($list_button);

	    $this->ask($question, function (Answer $answer) {
	        // Detect if button was clicked:
	        if ($answer->isInteractiveMessageReply()) {
	            $selectedText = $answer->getText();
	            if($selectedText == '募集中のポジションについて') {
	            	$this->say('お問い合わせは、募集中のポジションについてですね');
					$this->askPositionRecruiment();
	            }
	        }
	    });
	}

	public function askPositionRecruiment() {
		$list_button = $this->getListButton($this->services["supports"]["level3"]["item"], "title");
		$question = Question::create('現在募集中のポジションはこちらです。')
	        ->fallback('positionRecruitment')
	        ->callbackId('positionRecruitment_id')
	        ->addButtons($list_button);

	    $this->ask($question, function (Answer $answer) {
	        // Detect if button was clicked:
	        if ($answer->isInteractiveMessageReply()) {
	            $selectedText = $answer->getText();
	            if($selectedText == 'バックエンドエンジニア') {
	            	$this->say("ご希望は、バックエンドエンジニアですね、");
					$this->askWorkExperient();
	            }
	        }
	    });
	}

	public function askWorkExperient() {
		$list_button = $this->getListButton($this->services["supports"]["level4"]["item"], "title");
		$question = Question::create('ご応募いただく前にあなたのスキルをお教えてください')
	        ->fallback('WorkExperient')
	        ->callbackId('WorkExperient_id')
	        ->addButtons($list_button);

	    $this->ask($question, function (Answer $answer) {
	        // Detect if button was clicked:
	        if ($answer->isInteractiveMessageReply()) {
	            $selectedText = $answer->getText();
	            if($selectedText == 'PHP経験　3年以上') {
	            	$this->say("あなたはPHP3年以上の経験をお持ちですね");
	            	$this->say("弊社のPHP経験者ですと、xxx USD～となります。");
	            	$this->say("是非弊社人事宛にCVをお送りください");
					$this->askService('その他お問い合わせはございますか');
	            }
	        }
	    });
	}

    public function run()
    {
        // This will be called immediately
        $this->askService("いらっしゃいませ。GDITのバーチャルデスクにようこそ\n本日どの様なご用件でしょうか下記よりお選びください");
    }
}