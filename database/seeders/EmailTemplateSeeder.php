<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\EmailTemplate;

class EmailTemplateSeeder extends Seeder
{
    public function run()
    {
        
        EmailTemplate::create([
            'template_name' => 'Event Reminder',
            'subject' => 'Reminder: enter_event_name is Coming Up!',
            'type' => 'email',
            'message' => 'Dear {{name}},<br><br>This is a friendly reminder that the enter_event_name is coming up soon.<br>Event Details:<br>Date: enter_event_date<br>Time: enter_event_time<br>Location: enter_event_location<br><br>Don’t forget to bring your tickets!<br>Your QR code is <br>{{qr_code}}<br>Best regards,<br>{{site_name}} Team',
        ]);

        EmailTemplate::create([
            'template_name' => 'Welcome Email',
            'subject' => 'Welcome to {{site_name}}!',
            'type' => 'email',
            'message' => 'Dear {{name}},

                Welcome to {{site_name}}!  We are excited to have you join us.<br>
                Feel free to explore the site and get started with your first event registration.<br>

                If you have any questions, don’t hesitate to reach out.<br>
                Your QR code is 
                {{qr_code}}<br>
                Update Your Profile here {{profile_update_link}}<br>
                Best regards,
                {{site_name}} Team',
        ]);



        // Insert Event Reminder Notification Template
        EmailTemplate::create([
            'template_name' => 'Event Reminder Notification',
            'subject' => 'Reminder: enter_event_name is Approaching!',
            'type' => 'notifications',
            'message' => 'Hey {{name}}, just a reminder that enter_event_name is happening on enter_event_date at enter_event_location. Don’t miss out! <br> {{qr_code}}',
        ]);

        // Insert Welcome Notification Template
        EmailTemplate::create([
            'template_name' => 'Welcome Notification',
            'subject' => 'Welcome to {{site_name}}!',
            'type' => 'notifications',
            'message' => 'Hi {{name}}, welcome to {{site_name}}! We’re excited to have you with us. Get started by registering for an event today.',
        ]);


        // Insert Event Cancellation Notification Template
        EmailTemplate::create([
            'template_name' => 'Event Cancellation Notification',
            'subject' => 'We Are Sorry, enter_event_name Has Been Canceled',
            'type' => 'notifications',
            'message' => 'Dear {{name}}, we regret to inform you that enter_event_name scheduled for enter_event_date has been canceled. We apologize for any inconvenience.',
        ]);
    }
}

