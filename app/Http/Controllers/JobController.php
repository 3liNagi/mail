<?php

namespace App\Http\Controllers;

use App\Mail\EmailForQueuing;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Jobs\SendEmail;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Mail;

class JobController extends Controller
{
    /**
     * @param Request $request
     */
    public function enqueue()
    {
        $count = DB::table('users')->count();
        $limit = 20;
        $numberOFOffset = round(($count / $limit));
        if ($count > 20) {
            for ($offset = 0; $offset < $numberOFOffset; $offset++) {
                $details = DB::table('users')->offset((int)$offset * (int)$limit)->take((int)$limit)->select('email')->get();
                $emailJob = (new SendEmail($details))->delay(Carbon::now()->addMinutes(5));
                dispatch($emailJob);
            }
            return 'Send Done';
        }
        else {
            $details = DB::table('users')->select('email')->get();
//            $email_from = new EmailForQueuing();
//            Mail::to($details[0]->email)->send($email_from);
            SendEmail::dispatch($details);
            return 'Send Done';
        }
    }
}
