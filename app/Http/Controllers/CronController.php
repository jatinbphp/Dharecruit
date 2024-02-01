<?php

namespace App\Http\Controllers;

use App\Models\DataLog;
use App\Models\MailSent;
use App\Models\Requirement;
use App\Models\Setting;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Log;

class CronController extends Controller
{
    public function expireRequirement(){
        Log::info("Cron Expire Requirement Start ----> ".Carbon::now()->format('m-d-y h:i:s'));
        $settingRow =  Setting::where('name', 'no_of_hours_for_expire')->first();

        if(empty($settingRow) || !$settingRow->value){
            return '';
        }

        $expHours = $settingRow->value;
        $requirementObj = new Requirement();

        $requirementData = Requirement::
        whereNotIn('status', [$requirementObj::STATUS_EXP_HOLD, $requirementObj::STATUS_EXP_NEED])
            ->whereRaw('TIMESTAMPDIFF(HOUR, created_at, NOW()) >= ?', [$expHours])
            ->get();

        if(empty($requirementData)){
            return '';
        }

        $logData = [];
        $updatedRequirementIds = [];

        foreach ($requirementData as $requirement) {
            $data = [];

            $data['status'] = ($requirement->status == 'unhold') ? $requirementObj::STATUS_EXP_NEED : $requirementObj::STATUS_EXP_HOLD;
            $updatedRequirementIds[] = $requirement->id;
            $requirement->update($data);

            $data['requirement_id'] = $requirement->id;
            $data['created_at'] = $requirement->created_at;
            $logData[$requirement->id] = $data;
        }

        if($logData){
            $inputData['section_id'] = 0;
            $inputData['section']    = 'requirement';
            $inputData['data']       = json_encode($logData);

            DataLog::create($inputData);
        }

        Log::info("Updated Requirements Id ----> ". json_encode($updatedRequirementIds));
        Log::info("Cron Expire Requirement End ----> ".Carbon::now()->format('m-d-y h:i:s'));

        return '';
    }

    public function sendMails()
    {
//        Log::info("Cron SendMail Start ----> ".Carbon::now()->format('m-d-y h:i:s'));
//        $mailsData = MailSent::where('status', 0)->get();
//        if(!empty($mailsData)){
//            foreach ($mailsData as $mailData){
//                $data = json_decode($mailData->data);
//                $responce = $this->sendMail($data, $mailData->type);
//                if($responce){
//                    $mailData->status = 1;
//                    $mailData->save();
//                }
//            }
//        }
//        Log::info("Cron SendMail End ----> ".Carbon::now()->format('m-d-y h:i:s'));
//        return '';
    }

    public function clearMailData()
    {
//        Log::info("Cron SendMail Clean Start ----> ".Carbon::now()->format('m-d-y h:i:s'));
//        MailSent::where('status', 1)->delete();
//        Log::info("Cron SendMail Clean End ----> ".Carbon::now()->format('m-d-y h:i:s'));
//        return '';
    }
}
