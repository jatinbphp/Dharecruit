<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMailTemplatesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('mail_templates', function (Blueprint $table) {
            $table->id();
            $table->string('template_name');
            $table->string('type');
            $table->longText('subject');
            $table->longText('content');
            $table->timestamps();
        });

        \Illuminate\Support\Facades\DB::table('mail_templates')->insert([
            'type' => 'scheduled',
            'template_name' => 'Interview Schedule',
            'subject' => 'Interview Schedule',
            'content' =>'<p>Hi Recruiter,</p><p>I am thrilled to inform you that your candidate&nbsp;<b>[candidate_name]&nbsp;</b>for&nbsp;<b>JID: [job_id]</b>&nbsp; was selected for the interview for the <b>[job_title]</b>&nbsp; Position on date <b>[interview_date]&nbsp;</b>&nbsp;at <b>[interview_time] , [ time_zone]</b>&nbsp;with the client: [client_name].</p><p><br></p><p>Hope to hear something good.</p>',
        ]);

        \Illuminate\Support\Facades\DB::table('mail_templates')->insert([
            'type' => 're_scheduled',
            'template_name' => 'Interview Reschedule',
            'subject' => 'Interview Reschedule',
            'content' =>'<p>Hi Recruiter,</p><p>I am thrilled to inform you that your candidate&nbsp;<span style=\"font-weight: bolder;\">[candidate_name]&nbsp;</span>for&nbsp;<span style=\"font-weight: bolder;\">JID: [job_id]</span>&nbsp; was selected for the interview for the&nbsp;<span style=\"font-weight: bolder;\">[job_title]</span>&nbsp; Position on date&nbsp;<span style=\"font-weight: bolder;\">[interview_date]&nbsp;</span>&nbsp;at&nbsp;<span style=\"font-weight: bolder;\">[interview_time] , [ time_zone]</span>&nbsp;with the client: [client_name].</p><p><br></p><p>Hope to hear something good.</p>',
        ]);

        \Illuminate\Support\Facades\DB::table('mail_templates')->insert([
            'type' => 'selected_for_next_round',
            'template_name' => 'Selected for Another Round',
            'subject' => 'Selected for Another Round',
            'content' =>'<p>Hi Recruiter,</p><p>I am thrilled to inform you that your candidate&nbsp; <b>[<span style=\"font-size: 1rem;\">candidate_name</span></b><span style=\"font-size: 1rem;\"><b>]</b> was selected for the Second round of interviews for the&nbsp;</span><span style=\"font-weight: bolder;\">[job_title]</span>&nbsp;<span style=\"font-size: 1rem;\">Position (JID:&nbsp;</span>[job_id]<span style=\"font-size: 1rem;\">).</span></p><p>The next round is scheduled on&nbsp;&nbsp;<span style=\"font-weight: bolder;\">[interview_date]&nbsp;</span>&nbsp;at&nbsp;<span style=\"font-weight: bolder;\">[interview_time], [ time_zone]</span>&nbsp;&nbsp;with the client: [client_name].</p><p>Hope to hear something good</p><p>JID: [job_id]</p>',
        ]);

        \Illuminate\Support\Facades\DB::table('mail_templates')->insert([
            'type' => 'rejected',
            'template_name' => 'Rejected by Client',
            'subject' => 'Rejected by Client: No Worries, We will try Again!',
            'content' =>'<p>Hi Recruiter,</p><p>This mail is just to share the feedback regarding the interview that was done by your candidate&nbsp;<span style=\"font-weight: bolder;\">[<span style=\"font-size: 1rem;\">candidate_name</span></span><span style=\"font-size: 1rem;\"><span style=\"font-weight: bolder;\">]</span></span>&nbsp;on&nbsp;<span style=\"font-weight: bolder;\">[interview_date]&nbsp;</span>&nbsp;at&nbsp;<span style=\"font-weight: bolder;\">[interview_time], [ time_zone]</span>&nbsp;&nbsp;was rejected by the client for the position of&nbsp;<span style=\"font-weight: 700;\">[job_title]</span>&nbsp;(JID: [job_id]) &lt;/strong&gt;.</p><p>Reason: <b>[feedback]</b></p><p>JID: <b>[job_id]</b></p>',
        ]);

        \Illuminate\Support\Facades\DB::table('mail_templates')->insert([
            'type' => 'confirmed_position',
            'template_name' => 'Confirmed Position',
            'subject' => 'Confirmed Position: Congratulations!!!',
            'content' =>'<p>Hi Recruiter,</p><p>Congratulations! The project is confirmed! Candidate&nbsp;<span style=\"font-size: 1rem; font-weight: bolder;\">[<span style=\"font-size: 1rem;\">candidate_name</span></span><span style=\"font-size: 1rem;\"><span style=\"font-weight: bolder;\">]</span></span><span style=\"font-size: 1rem;\">&nbsp;is confirmed by the client for the position of&nbsp;</span><span style=\"font-weight: 700; font-size: 1rem;\">[job_title]&nbsp;</span><span style=\"font-size: 1rem;\"><b>(JID: [job_id])</b>.</span></p>',
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('mail_templates');
    }
}
