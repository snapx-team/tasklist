<?php

namespace Xguard\Tasklist\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\SlackMessage;
use Illuminate\Notifications\Notification;

class TaskSlackNotification extends Notification
{
    use Queueable;

    private $data;

    public function __construct($data)
    {
        $this->data = $data;
    }

    public function via($notifiable)
    {
        return ['slack'];
    }

    public function toSlack($notifiable)
    {
        $message = (new SlackMessage)
            ->content("INCOMPLETE TASKS:\n ------------------------");

        foreach ($this->data as $task) {
            $description = $task['description'];
            $contractIdentifier = $task['contractIdentifier'];

            $message->attachment(function ($attachment) use ($task) {
                $attachment->pretext('TASK: ' . $task['description'])
                    ->fields([
                        'DEADLINE:' => $task['time'],
                        'CONTRACT:' => $task['contractIdentifier'],
                    ]);

                if (empty($task['employees'])) {
                    $attachment->footer('No Employee Available', 'No one is currently checked into this shift to complete the task.')
                        ->color('#FFE000');
                } else {
                    $employeeList = "ON-SITE EMPLOYEES \n" ;
                    foreach ($task['employees'] as $employee) {
                        $employeeName = $employee['employeeName'];
                        $phone = $employee['phone'];

                        $employeeList .= "{$employeeName} - Phone: {$phone}\n";
                    }

                    $attachment->footer($employeeList)->color('#3498db');
                }
            });
        }

        return $message;
    }
}
