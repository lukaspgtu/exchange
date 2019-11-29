<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Jenssegers\Agent\Agent;

class Session extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'sessions_log';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id', 'ip', 'device', 'platform', 'browser'
    ];

    public static function init($user_id, $ip)
    {
        $agent = new Agent();

        $session = DB::table('sessions_log')
            ->where('user_id', $user_id)
            ->where('ip', $ip)
            ->where('device', $agent->device())
            ->where('platform', $agent->platform())
            ->where('browser', $agent->browser())
            ->first();

        if ($session == null) {

            DB::table('sessions_log')->insert([
                'user_id' => $user_id,
                'ip' => $ip,
                'device' => $agent->device(),
                'platform' => $agent->platform(),
                'browser' => $agent->browser(),
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ]);

        }

        else {

            DB::table('sessions_log')
                ->where('id', $session->id)
                ->update([
                    'updated_at' => date('Y-m-d H:i:s')
                ]);

        }
    }
}
