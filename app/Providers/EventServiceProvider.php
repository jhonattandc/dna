<?php

namespace App\Providers;

use App\Listeners\Q10StudentventSubscriber;

use App\Prosegur\Listeners\SaveNewAlarm;
use Webklex\IMAP\Events\MessageNewEvent;

use App\Q10\Models\Campus;
use JeroenNoten\LaravelAdminLte\Events\BuildingMenu;

use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;

use Illuminate\Support\Facades\Event;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array<class-string, array<int, class-string>>
     */
    protected $listen = [
        Registered::class => [
            SendEmailVerificationNotification::class,
        ],
        MessageNewEvent::class => [
            SaveNewAlarm::class,
        ],
    ];

    /**
     * The subscriber classes to register.
     *
     * @var array
     */
    protected $subscribe = [
        Q10StudentventSubscriber::class,
    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        Event::listen(BuildingMenu::class, function (BuildingMenu $event) {
            // Add some items to the menu...
            $event->menu->add('Q10');
            foreach (Campus::all() as $campus) {
                $event->menu->add([
                    'text' => $campus->Nombre,
                    'icon' => 'fas fa-fw fa-map-marker-alt',
                    'active' => ['q10/campus/'. $campus->id, 'q10/campus/'. $campus->id .'/*'],
                    'submenu' => [
                        [
                            'text' => 'Usuarios',
                            'icon' => 'fas fa-fw fa-users',
                            'shift' => 'ml-2',
                            'submenu' => [
                                [
                                    'text' => 'Administradores',
                                    'url'  => 'q10/campus/'. $campus->id .'/users/admins',
                                    'icon' => 'fas fa-fw fa-user-shield',
                                    'shift' => 'ml-4'
                                ],
                                [
                                    'text' => 'Docentes',
                                    'url'  => 'q10/campus/'. $campus->id .'/users/teachers',
                                    'icon' => 'fas fa-fw fa-user-tie',
                                    'shift' => 'ml-4'
                                ],
                                [
                                    'text' => 'Estudiantes',
                                    'url'  => 'q10/campus/'. $campus->id .'/users/students',
                                    'icon' => 'fas fa-fw fa-user-graduate',
                                    'shift' => 'ml-4'
                                ],
                            ],
                
                        ],
                        [
                            'text' => 'Periodos',
                            'url'  => 'q10/campus/'. $campus->id .'/periods',
                            'icon' => 'fas fa-fw fa-calendar-alt',
                            'shift' => 'ml-2'
                        ],
                        [
                            'text' => 'Programas',
                            'url'  => 'q10/campus/'. $campus->id .'/programs',
                            'icon' => 'fas fa-fw fa-graduation-cap',
                            'shift' => 'ml-2'
                        ],
                    ]
                ]);
            }
        });
    }
}
