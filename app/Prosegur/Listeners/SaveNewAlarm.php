<?php
 
namespace App\Prosegur\Listeners;
 
use Webklex\IMAP\Events\MessageNewEvent;
use App\Prosegur\Processors\EmailProcessor;
 

class SaveNewAlarm
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }
 
    /**
     * Handle the event.
     *
     * @param  Webklex\IMAP\Events\MessageNewEvent $event
     * @return void
     */
    public function handle(MessageNewEvent $event)
    {
        $processor = new EmailProcessor();
        $processor->process($event->message);
    }
}