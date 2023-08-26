<?php

namespace App\Prosegur\Processors;

use App\Prosegur\Models\Alarm;
use Webklex\PHPIMAP\Message;

class EmailProcessor {

    /**
     * The available parsers for the emails processor.
     * If the email subject matches the parser subject, the parser will be used.
     * The parsers are ordered by priority.
     * 
     * @var array of \App\Prosegur\Parsers\Parser classes
     */
    private $available_parsers = [
        \App\Prosegur\Parsers\AlarmaParser::class,
        \App\Prosegur\Parsers\AperturaParser::class,
    ];

    /**
     * Get the parser for the email
     * 
     * @param \Webklex\PHPIMAP\Message $email
     * @return \App\Prosegur\Parsers\Parser
     */
    public function getParser($email){
        foreach ($this->available_parsers as $parser) {
            if ($parser::subject == $email->subject) {
                return new $parser($email->getTextBody());
            }
        }
        return null;
    }

    /**
     * Get the triggered at date from email timestamp
     * 
     * @param \Webklex\PHPIMAP\Message $email
     * 
     * @return datetime
     */
    public function get_triggered_at($email) {
        return $email->getDate()->toDate();
    }

    /**
     * Process the email
     * 
     * @param \Webklex\PHPIMAP\Message $email
     *
     * @return void
     */
    public function process(Message $email) {
        $parser = $this->getParser($email);
        if (!$parser) {
            return;
        }
        $date = $parser->get_date($email);
        if ($date) {
            $date = strtotime($date);
            $date = date('Y-m-d H:i:s', $date);
        } else {
            $date = $this->get_triggered_at($email);
        }
        $alarm = Alarm::firstOrCreate([
            'email_id' => $email->getMessageId(),
        ], [
            'operator' => $parser->get_operator(),
            'event' => $parser->get_event(),
            'system' => $parser->get_system(),
            'location' => $parser->get_location(),
            'triggered_at' => $date,
        ]);
    }
}