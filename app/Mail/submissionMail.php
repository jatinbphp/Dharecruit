<?php

namespace App\Mail;

use App\Models\MailTemplate;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class submissionMail extends Mailable
{
    use Queueable, SerializesModels;
    public $data;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($data)
    {
        $this->data = $data;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $subject = '';
        if(isset($this->data->status_type) && in_array($this->data->status_type, ['bdm_status', 'pv_status'])){
            $subject = $this->data->status_text;
        } else if(isset($this->data->status_type) && $this->data->status_type == 'interview_status') {
            $subject = isset($this->data->subject) ? $this->data->subject : $this->data->status->text;
        } else if($this->data->type == 'submission_add'){
            $subject = 'Submission Add';
        }
        return $this->markdown('admin.mail_template.status_mail')->subject($subject);
    }
}
