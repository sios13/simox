<?php
namespace Simox\Events;

interface EventsAwareInterface
{
    public function setEventsManager( Manager $events_manager );
    public function getEventsManager();
}
