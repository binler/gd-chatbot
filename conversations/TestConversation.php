<?php

use BotMan\BotMan\Messages\Conversations\Conversation;
use BotMan\BotMan\Messages\Outgoing\Actions\Button as Button;
use BotMan\BotMan\Messages\Outgoing\Question as Question;
use BotMan\BotMan\Messages\Incoming\Answer as Answer;
/**
 * 
 */
class TestConversation extends Conversation {
    
    protected $data = array();
    
    public function __construct() {
        $this->data = include_once './data.php';
    }
    
    /**
     * Method get data by column in message data.
     * 
     * @param type $data
     * @param type $column
     * @param type $value
     * @return type Null | Array.
     */
    public function getInfo($column, $value) {
        $key = array_search($value, array_column($this->data, $column));
        return $key === false ? NULL : $this->data[$key];
    }

    /**
     * Method get and create button from data.
     * 
     * @param type $button_list
     * @return array
     */
    public function getListButton($button_list) {
        $array_button = [];
        if (array_key_exists('item', $button_list)) {
            foreach ($button_list['item'] as $k => $v) {
                array_push($array_button, Button::create($v)->value($k));
            }
        }
        return $array_button;
    }

    /**
     * Method run first.
     * 
     * @callback
     */
    public function askMain() {
        $datamessage = $this->getInfo('name', 'default');
        $list_btn    = $this->getListButton($datamessage);
        $question    = Question::create($datamessage['question'])
                ->fallback('default')
                ->callbackId($datamessage['name'])
                ->addButtons($list_btn);

        $this->ask($question, function (Answer $answer) {
            // Detect if button was clicked:
            if ($answer->isInteractiveMessageReply()) {
                $selectedValue = $answer->getValue();
                $this->askAuto($selectedValue);
            }
        });
    }

    /**
     * Auto get event to run.
     * 
     * @param type $value key function to run
     * @callback 
     */
    public function askAuto($value) {
        $datamessage = $this->getInfo('name', $value);
        if (is_null($datamessage)) {
            $this->askContinue();
        }
        else {
            $list_btn = $this->getListButton($datamessage);
            $question = Question::create($datamessage['question'])
                    ->fallback('default')
                    ->callbackId($datamessage['name'])
                    ->addButtons($list_btn);

            $this->ask($question, function (Answer $answer) {
                // Detect if button was clicked:
                if ($answer->isInteractiveMessageReply()) {
                    $selectedValue = $answer->getValue();
                    $this->askAuto($selectedValue);
                }
            });
        }
    }

    /**
     * Run method when not find request
     * 
     * @callback
     */
    public function askContinue() {
        $datamessage = $this->getInfo('name', 'help');
        $list_btn    = $this->getListButton($datamessage);
        $question    = Question::create($datamessage['question'])
                ->fallback('default')
                ->callbackId($datamessage['name'])
                ->addButtons($list_btn);
        $this->ask($question, function (Answer $answer) {
            // Detect if button was clicked:
            if ($answer->isInteractiveMessageReply()) {
                $selectedValue = $answer->getValue();
                $this->askAuto($selectedValue);
            }
        });
    }
    
    /**
     * Run ask
     */
    public function run() {
        $this->askMain();
    }

}
