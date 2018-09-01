<?php

namespace App\Processes;

use App\Website;

class ResolveIncident
{
    public function __invoke(Website $website, string $type) : void
    {
        $incident = $website->incidents()->where('type', $type)->first();
        if (!$incident) {
            return;
        }

        $incident->resolve();

        $message = "{$incident->type} incident resolved on ${$website->name} ({$website->url})";
        $phones = User::all()->pluck('phone_number')->filter();
        foreach ($phones as $phone) {
            $this->twilio->sendSMS($phone, $message);
        }
    }
}