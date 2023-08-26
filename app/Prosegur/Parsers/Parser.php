<?php

namespace App\Prosegur\Parsers;


abstract class Parser {
    /**
     * The message string to parse
     * 
     * @var string
     */
    public $message;

    /**
     * The unique id of the message
     * 
     * @var string
     */
    public $uid;


    /**
     * Create a new Parser instance
     * 
     * @param string $message
     */
    public function __construct($message) {
        $this->message = $message;
    }

    /**
     * Get the unique id
     * 
     * @return string
     */
    public function get_uid() {
        return $this->uid;
    }

    /**
     * Set the unique id
     */
    public function set_uid($uid) {
        $this->uid = $uid;
    }

    /**
     * Get the operator name
     * 
     * @return string
     */
    public function get_operator() {
        return null;
    }

    /**
     * Get the event name
     * 
     * @return string
     */
    abstract public function get_event();

    /**
     * Get the system name
     * 
     * @return string
     */
    abstract public function get_system();

    /**
     * Get the location name
     * 
     * @return string
     */
    abstract public function get_location();

    /**
     * Get the date if the message has one
     */
    abstract public function get_date();
}