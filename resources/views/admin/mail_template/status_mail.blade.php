<?php
    $colorCode = '#17a2b8';
    if(isset($data->status_type) && $data->status_type == 'bdm_status'){
        if(strtolower($data->status) == 'accepted'){
            $colorCode = '#28a745';
        } elseif (strtolower($data->status) == 'rejected'){
            $colorCode = "#F3B083";
        } elseif (strtolower($data->status) == 'pending'){
            $colorCode = "#17a2b8";
        }
    }

    if(isset($data->status_type) && $data->status_type == 'pv_status'){
        if ($data->pv_status == 'submitted_to_end_client'){
            $colorCode = '#28a745';
        } else {
            $colorCode = "#F3B083";
        }
    }
?>
<!DOCTYPE html>
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <title></title>
    </head>
    <body>
        <div style="line-height:inherit;margin:0;background-color:#f5f5f5">
            <table cellpadding="0" cellspacing="0" role="presentation" width="100%" bgcolor="#f5f5f5" valign="top" style="line-height:inherit;table-layout:fixed;vertical-align:top;border-spacing:0;border-collapse:collapse;background-color:#f5f5f5;width:100%;text-align: center;">
            <tbody style="line-height:inherit">
            <tr valign="top">
                <td valign="top" style="line-height:inherit;border-collapse:collapse;word-break:break-word;vertical-align:top;text-align: center;padding: 60px 60px 0;">
                    {{ config('app.name') }}
                </td>
            </tr>
            <tr valign="top">
                <td valign="top" style="line-height:inherit;border-collapse:collapse;word-break:break-word;vertical-align:top;text-align: center;padding:30px 0 60px;">
                    <table cellpadding="0" cellspacing="0" role="presentation" width="100%" bgcolor="#FFFFFF" valign="top" style="line-height:inherit;table-layout:fixed;vertical-align:top;min-width:320px;max-width: 612px;border-spacing:0;border-collapse:collapse;background-color:#ffffff;width:100%;margin: 0 auto;">
                        <tbody style="line-height:inherit">
                        <tr valign="top" style="line-height:inherit;border-collapse:collapse;vertical-align:top">
                            <td valign="top" style="line-height:inherit;border-collapse:collapse;word-break:break-word;vertical-align:top;font-family: 'Poppins',Arial,sans-serif;font-style: normal;font-weight: 400;font-size: 16px;line-height: 26px;color: #495057;padding: 50px 60px;text-align: left;">
                                @if(isset($data->status_type) && in_array($data->status_type, ['bdm_status', 'pv_status']))
                                    <strong> Status:  <span style="color:{{$colorCode}};">{{$data->status_text}}</span></strong>
                                    <table style="width:100%; border-collapse: collapse; border-radius: 10px; overflow: hidden; box-shadow: 0 0 10px rgba(0,0,0,0.1);" bgcolor="{{$colorCode}}">
                                        <tr>
                                            <td style="border: 1px solid #dddddd; text-align: left; padding: 8px; color: black; border-radius: 10px;"><strong style="padding-left: 6px">Job Id</strong></td>
                                            <td style="border: 1px solid #dddddd; text-align: left; padding: 8px; color: black; border-radius: 10px;"><strong style="padding-left: 6px">{{$data->requirement->job_id}} ({{$data->requirement->job_title}})</strong></td>
                                        </tr>
                                        <tr>
                                            <td style="border: 1px solid #dddddd; text-align: left; padding: 8px; color: black; border-radius: 10px;"><strong style="padding-left: 6px">Consultant Name</strong></td>
                                            <td style="border: 1px solid #dddddd; text-align: left; padding: 8px; color: black; border-radius: 10px;"><strong style="padding-left: 6px">{{$data->name}}</strong></td>
                                        </tr>
                                        @if(isset($data->status_type) && $data->status_type == 'bdm_status')
                                            @if(in_array(strtolower($data->status), ['accepted', 'pending']))
                                                <tr>
                                                    <td style="border: 1px solid #dddddd; text-align: left; padding: 8px; color: black; border-radius: 10px;"><strong style="padding-left: 6px">Prime vendor Company</strong></td>
                                                    <td style="border: 1px solid #dddddd; text-align: left; padding: 8px; color: black; border-radius: 10px;"><strong style="padding-left: 6px">{{$data->requirement->pv_company_name}}</strong></td>
                                                </tr>
                                                <tr>
                                                    <td style="border: 1px solid #dddddd; text-align: left; padding: 8px; color: black; border-radius: 10px;"><strong style="padding-left: 6px">Point of Contact</strong></td>
                                                    <td style="border: 1px solid #dddddd; text-align: left; padding: 8px; color: black; border-radius: 10px;"><strong style="padding-left: 6px">{{$data->requirement->poc_name}}</strong></td>
                                                </tr>
                                            @elseif(strtolower($data->status) == 'rejected')
                                                <tr>
                                                    <td style="border: 1px solid #dddddd; text-align: left; padding: 8px; color: black; border-radius: 10px;"><strong style="padding-left: 6px">Rejection Reason</strong></td>
                                                    <td style="border: 1px solid #dddddd; text-align: left; padding: 8px; color: black; border-radius: 10px;"><strong style="padding-left: 6px">{{$data->reason}}</strong></td>
                                                </tr>

                                            @endif
                                        @endif
                                        @if(isset($data->status_type) && $data->status_type == 'pv_status')
                                            <tr>
                                                <td style="border: 1px solid #dddddd; text-align: left; padding: 8px; color: black; border-radius: 10px;"><strong style="padding-left: 6px">Location</strong></td>
                                                    <td style="border: 1px solid #dddddd; text-align: left; padding: 8px; color: black; border-radius: 10px;"><strong style="padding-left: 6px">{{$data->location}}</strong></td>
                                            </tr>
                                            <tr>
                                                <td style="border: 1px solid #dddddd; text-align: left; padding: 8px; color: black; border-radius: 10px;"><strong style="padding-left: 6px">Prime vendor Company</strong></td>
                                                <td style="border: 1px solid #dddddd; text-align: left; padding: 8px; color: black; border-radius: 10px;"><strong style="padding-left: 6px">{{$data->requirement->pv_company_name}}</strong></td>
                                            </tr>
                                            <tr>
                                                <td style="border: 1px solid #dddddd; text-align: left; padding: 8px; color: black; border-radius: 10px;"><strong style="padding-left: 6px">Client</strong></td>
                                                <td style="border: 1px solid #dddddd; text-align: left; padding: 8px; color: black; border-radius: 10px;"><strong style="padding-left: 6px">{{$data->requirement->client_name}}</strong></td>
                                            </tr>
                                            @if(in_array(strtolower($data->pv_status),['rejected_by_pv', 'rejected_by_end_client', 'no_response_from_pv', 'position_closed']))
                                                <tr>
                                                    <td style="border: 1px solid #dddddd; text-align: left; padding: 8px; color: black; border-radius: 10px;"><strong style="padding-left: 6px">Reason</strong></td>
                                                    <td style="border: 1px solid #dddddd; text-align: left; padding: 8px; color: black; border-radius: 10px;"><strong style="padding-left: 6px">{{$data->pv_reason}}</strong></td>
                                                </tr>
                                            @endif
                                        @endif
                                        <tr>
                                            <td style="border: 1px solid #dddddd; text-align: left; padding: 8px; color: black; border-radius: 10px;"><strong style="padding-left: 6px">Job Posted Date</strong></td>
                                            <td style="border: 1px solid #dddddd; text-align: left; padding: 8px; color: black; border-radius: 10px;"><strong style="padding-left: 6px">{{date('m-d-y' , strtotime($data->requirement->created_at))}}</strong></td>
                                        </tr>
                                    </table>
                                @elseif(isset($data->status_type) && $data->status_type == 'interview_status')
                                    {!! $data->content !!}
                                @elseif($data->type == 'submission_add')
                                    <p>
                                        Hi,
                                        Please find the attached resume for JID:{{$data->requirement->job_id}} ({{$data->requirement->job_title}}) along with details below for the <b>Location at Client: {{$data->location}}</b>
                                    </p>
                                    <p>Employer Company Name: {{$data->employer_name}}</p>
                                    <table style="width:100%; border-collapse: collapse; border-radius: 10px; overflow: hidden; box-shadow: 0 0 10px rgba(0,0,0,0.1);">
                                        <tr>
                                            <td style="border: 1px solid #dddddd; text-align: left; padding: 8px; color: black; border-radius: 10px;"><strong style="padding-left: 6px">Candidate’s Full Name</strong></td>
                                            <td style="border: 1px solid #dddddd; text-align: left; padding: 8px; color: black; border-radius: 10px;"><strong style="padding-left: 6px">{{$data->name}}</strong></td>
                                        </tr>
                                        <tr>
                                            <td style="border: 1px solid #dddddd; text-align: left; padding: 8px; color: black; border-radius: 10px;"><strong style="padding-left: 6px">Contact Number</strong></td>
                                            <td style="border: 1px solid #dddddd; text-align: left; padding: 8px; color: black; border-radius: 10px;"><strong style="padding-left: 6px">{{$data->phone}}</strong></td>
                                        </tr>
                                        <tr>
                                            <td style="border: 1px solid #dddddd; text-align: left; padding: 8px; color: black; border-radius: 10px;"><strong style="padding-left: 6px">Email ID</strong></td>
                                            <td style="border: 1px solid #dddddd; text-align: left; padding: 8px; color: black; border-radius: 10px;"><strong style="padding-left: 6px">{{$data->email}}</strong></td>
                                        </tr>
                                        <tr>
                                            <td style="border: 1px solid #dddddd; text-align: left; padding: 8px; color: black; border-radius: 10px;"><strong style="padding-left: 6px">Visa</strong></td>
                                            <td style="border: 1px solid #dddddd; text-align: left; padding: 8px; color: black; border-radius: 10px;"><strong style="padding-left: 6px">{{\App\Models\Requirement::getVisaNames($data->requirement->visa)}}</strong></td>
                                        </tr>
                                        <tr>
                                            <td style="border: 1px solid #dddddd; text-align: left; padding: 8px; color: black; border-radius: 10px;"><strong style="padding-left: 6px">Location</strong></td>
                                            <td style="border: 1px solid #dddddd; text-align: left; padding: 8px; color: black; border-radius: 10px;"><strong style="padding-left: 6px">{{$data->location}}</strong></td>
                                        </tr>
                                        <tr>
                                            <td style="border: 1px solid #dddddd; text-align: left; padding: 8px; color: black; border-radius: 10px;"><strong style="padding-left: 6px">LinkedIn</strong></td>
                                            <td style="border: 1px solid #dddddd; text-align: left; padding: 8px; color: black; border-radius: 10px;"><strong style="padding-left: 6px">{{$data->linkedin_id}}</strong></td>
                                        </tr>
                                        <tr>
                                            <td style="border: 1px solid #dddddd; text-align: left; padding: 8px; color: black; border-radius: 10px;"><strong style="padding-left: 6px">Education details</strong></td>
                                            <td style="border: 1px solid #dddddd; text-align: left; padding: 8px; color: black; border-radius: 10px;"><strong style="padding-left: 6px">{{$data->education_details}}</strong></td>
                                        </tr>
                                        <tr>
                                            <td style="border: 1px solid #dddddd; text-align: left; padding: 8px; color: black; border-radius: 10px;"><strong style="padding-left: 6px">Availability Date to Start</strong></td>
                                            <td style="border: 1px solid #dddddd; text-align: left; padding: 8px; color: black; border-radius: 10px;"><strong style="padding-left: 6px">Immediate</strong></td>
                                        </tr>
                                        <tr>
                                            <td style="border: 1px solid #dddddd; text-align: left; padding: 8px; color: black; border-radius: 10px;"><strong style="padding-left: 6px">Total Experience</strong></td>
                                            <td style="border: 1px solid #dddddd; text-align: left; padding: 8px; color: black; border-radius: 10px;"><strong style="padding-left: 6px">{{$data->resume_experience}}</strong></td>
                                        </tr>
                                        <tr>
                                            <td style="border: 1px solid #dddddd; text-align: left; padding: 8px; color: black; border-radius: 10px;"><strong style="padding-left: 6px">SSN</strong></td>
                                            <td style="border: 1px solid #dddddd; text-align: left; padding: 8px; color: black; border-radius: 10px;"><strong style="padding-left: 6px">{{$data->last_4_ssn}}</strong></td>
                                        </tr>
                                        <tr>
                                            <td style="border: 1px solid #dddddd; text-align: left; padding: 8px; color: black; border-radius: 10px;"><strong style="padding-left: 6px">Rate</strong></td>
                                            <td style="border: 1px solid #dddddd; text-align: left; padding: 8px; color: black; border-radius: 10px;"><strong style="padding-left: 6px">{{$data->recruiter_rate}}</strong></td>
                                        </tr>
                                    </table>
                                @endif
                                <br>
                                <br>
                                Thanks,<br>
                                {{ config('app.name') }}
                            </td>
                        </tr>
                        </tbody>
                    </table>
                </td>
            </tr>
            </tbody>
        </table>
        </div>
    </body>
</html>
