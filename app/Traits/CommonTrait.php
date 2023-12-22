<?php
namespace App\Traits;
trait CommonTrait {
    public function formatString($input): string
    {
        return ucwords(str_replace('_', ' ', $input));
    }

    public function getPercentage($value, $total)
    {
        if(!$total){
            return 0;
        }
        return  round((($value * 100) / $total), 2);
    }

    public function getDate($type, $request): array
    {
        $date = [];
        switch ($type){
            case 'today':
                $date['from'] = \Carbon\Carbon::now()->format('Y-m-d');
                $date['to']   = \Carbon\Carbon::now()->addDay()->format('Y-m-d');
                break;
            case 'this_week':
                $date['from'] = \Carbon\Carbon::now()->startOfWeek()->format('Y-m-d');
                $date['to']   = \Carbon\Carbon::now()->endOfWeek()->addDay()->format('Y-m-d');
                break;
            case 'last_week':
                $date['from'] = \Carbon\Carbon::now()->subWeek()->startOfWeek()->format('Y-m-d');;
                $date['to']   = \Carbon\Carbon::now()->subWeek()->endOfWeek()->addDay()->format('Y-m-d');;
                break;
            case 'this_month':
                $date['from'] = \Carbon\Carbon::now()->startOfMonth()->format('Y-m-d');
                $date['to']   = \Carbon\Carbon::now()->endOfMonth()->addDay()->format('Y-m-d');
                break;
            case 'last_month':
                $date['from'] = \Carbon\Carbon::now()->subMonth()->subMonth()->startOfMonth()->format('Y-m-d');
                $date['to']   = \Carbon\Carbon::now()->subMonth()->subMonth()->endOfMonth()->addDay()->format('Y-m-d');
                break;
            case 'time_frame':
                $date['from'] = \Carbon\Carbon::createFromFormat('m/d/Y', $request->fromDate)->format('Y-m-d');
                $date['to']   = \Carbon\Carbon::createFromFormat('m/d/Y', $request->toDate)->addDay()->format('Y-m-d');
                break;
            default:
                $date['from'] = \Carbon\Carbon::now()->format('Y-m-d');
                $date['to']   = \Carbon\Carbon::now()->addDay()->format('Y-m-d');
        }
        return  $date;
    }

    public function getKeyWiseClass()
    {
        return [
            'heading_type'                   => 'font-weight-bold',
            'total_req'                      => 'font-weight-bold',
            'total_uni_req'                  => '',
            'served'                         => '',
            'unserved'                       => '',
            'servable_per'                   => '',
            'submission_received'            => 'font-weight-bold',
            'bdm_accept'                     => 'text-success',
            'bdm_rejected'                   => 'text-danger',
            'bdm_unviewed'                   => 'text-primary',
            'bdm_pending'                    => 'text-primary',
            'vendor_no_responce'             => 'text-secondary',
            'vendor_rejected_by_pv'          => 'text-danger',
            'vendor_rejected_by_client'      => 'font-weight-bold text-danger',
            'vendor_submitted_to_end_client' => 'font-weight-bold text-success',
            'vendor_position_closed'         => 'text-secondary',
            'client_rescheduled'             => 'text-warning',
            'client_selected_for_next_round' => 'font-weight-bold text-warning',
            'client_waiting_feedback'        => '',
            'client_confirmed_position'      => 'font-weight-bold text-success',
            'client_rejected'                => 'font-weight-bold text-danger',
            'client_backout'                 => 'font-weight-bold text-dark',
        ];
    }
}
