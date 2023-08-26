<?php

/**
 * 
 * Class to process incoming alarms from Prosegur mailbox. The body of the email is parsed and the alarms are saved in the database.
 * 
 * Example of an alarm email:
 * 
 * subject: NOTIFICACION DE ALARMA
 * body:
 * PROSEGUR le informa que su sistema de alarma NAJERA ARCHILA DAVID RICARDO ubicado en CARRERA 76A Nº 49-48, ha presentado salto de
 * alarma, solicitamos la verificacion interna lo antes posible. mayor informacion 3183515058 Op. 2
 */
namespace App\Prosegur\Parsers;


class AlarmaParser extends Parser {

    /**
     * The subject of the message
     * 
     * @var string
     */
    const subject = 'NOTIFICACION DE ALARMA';

    /**
     * Create a new Parser instance
     * 
     * @param \Webklex\PHPIMAP\Message $email
     */
    public function __construct($message) {
        $this->message = $message;
    }

    /**
     * Get the system name
     * 
     * @return string
     */
    public function get_system() {
        $system = null;
        $pattern = '/sistema de alarma ([A-Z0-9 ]+) ubicado/';
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
    public function get_location(){
        $location = null;
        $pattern = '/(?<=ubicado en\s)(.*)(?=,\sha)/';
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
    public function get_event() {
        $event = null;
        $pattern = '/(?<=presentado\s)(.*)(?=,\ssolicitamos)/';
        preg_match($pattern, $this->message, $matches);
        if (count($matches) > 1) {
            $event = $matches[1];
        }
        return $event;
    }

    /**
     * Get the date of the event time
     * 
     * @return string
     */
    public function get_date() {
        return null;
    }
}
