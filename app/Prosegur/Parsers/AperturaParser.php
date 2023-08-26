<?php

/**
 * 
 * Class to process incoming alarms from Prosegur mailbox. The body of the email is parsed and the alarms are saved in the database.
 * 
 * Example of an alarm email:
 * 
 * subject: Apertura y Cierre
 * body:
 * El sistema DNA INVERSIONES SAS ubicado en CARRERA 10 Nº 42 -84 MARAYA registró CIERRE partición 1 por KENGUAN, SANTIAGO May  6 2023  5:42PM
 */
namespace App\Prosegur\Parsers;


class AperturaParser extends Parser {
    /**
     * The subject of the message
     * 
     * @var string
     */
    const subject = 'Apertura y Cierre';

    /**
     * Create a new Parser instance
     * 
     * @param string $message
     */
    public function __construct($message) {
        $this->message = $message;
    }

    /**
     * Get the system name
     * 
     * @return string
     */
    public function get_system()
    {
        $system = null;
        $pattern = '/El sistema ([A-Z0-9 ]+) ubicado/';
        preg_match($pattern, $this->message, $matches);
        if (count($matches) > 1) {
            $system = $matches[1];
        }
        return $system;
    }

    /**
     * Get the location name
     * 
     * @return string
     */
    public function get_location()
    {
        $location = null;
        $pattern = '/(?<=ubicado en\s)(.*)(?=\sregistró)/';
        preg_match($pattern, $this->message, $matches);
        if (count($matches) > 1) {
            $location = $matches[1];
        }
        return $location;
    }

    /**
     * Get the event name
     * 
     * @return string
     */
    public function get_event()
    {
        $event = null;
        $pattern = '/registró ([A-Z0-9 ]+) partición/';
        preg_match($pattern, $this->message, $matches);
        if (count($matches) > 1) {
            $event = $matches[1];
        }
        return $event;
    }

    /**
     * Get the operator name
     * 
     * @return string
     */
    public function get_operator()
    {
        $operator = null;
        $pattern = '/por (.*)(?=\s(Jan(?:uary)?|Feb(?:ruary)?|Mar(?:ch)?|Apr(?:il)?|May|Jun(?:e)?|Jul(?:y)?|Aug(?:ust)?|Sep(?:tember)?|Oct(?:ober)?|Nov(?:ember)?|Dec(?:ember)?))/';
        preg_match($pattern, $this->message, $matches);
        if (count($matches) > 1) {
            $operator = $matches[1];
        }
        return $operator;
    }

    /**
     * Get the date of the event
     * 
     * @return string
     */
    public function get_date()
    {
        $date = null;
        $pattern = '/(?:(Jan(?:uary)?|Feb(?:ruary)?|Mar(?:ch)?|Apr(?:il)?|May|Jun(?:e)?|Jul(?:y)?|Aug(?:ust)?|Sep(?:tember)?|Oct(?:ober)?|Nov(?:ember)?|Dec(?:ember)?))(.*)(PM|AM)/';
        preg_match($pattern, $this->message, $matches);
        if (count($matches) > 1) {
            $date = $matches[1] . $matches[2] . $matches[3];
        }
        return $date;
    }
}