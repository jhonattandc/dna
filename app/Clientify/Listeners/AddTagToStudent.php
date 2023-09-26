<?php

namespace App\Clientify\Listeners;

use App\Clientify\Services\ClientifyAPI;

use Exception;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class AddTagToStudent implements ShouldQueue
{
    use InteractsWithQueue;

    /**
     * The clientify api instance.
     */
    protected $client;

    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct(ClientifyAPI $clientify)
    {
        $this->client = $clientify;
    }

    /**
     * Handle the event.
     *
     * @param  object  $event
     * @return void
     */
    public function handle($event)
    {
        $contacts = $this->client->get_contacts($event->student->email);
        if ($contacts instanceof Exception) {
            return;
        }

        if ($contacts) {
            $contact = $contacts->first();
            $this->client->add_tag_to_contact($contact->id, $event->tag);
        }
    }
}
