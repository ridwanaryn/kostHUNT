<?php

declare(strict_types=1);

namespace App\Notifications;

use App\Models\MaintenanceRequest;
use Illuminate\Notifications\Notification;

class MaintenanceStatusChanged extends Notification
{
    public function __construct(private readonly MaintenanceRequest $maintenanceRequest) {}

    /**
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database'];
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'maintenance_request_id' => $this->maintenanceRequest->id,
            'title' => $this->maintenanceRequest->title,
            'status' => $this->maintenanceRequest->status->value,
            'message' => 'Your request "'.$this->maintenanceRequest->title.'" is now '
                .$this->maintenanceRequest->status->label().'.',
            'url' => route('tenant.maintenance-requests.show', $this->maintenanceRequest),
        ];
    }
}
