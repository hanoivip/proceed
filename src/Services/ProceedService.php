<?php

namespace Hanoivip\Proceed\Services;

use Hanoivip\GateClient\Facades\BalanceFacade;
use Hanoivip\Proceed\Models\Proceed;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Exception;
use Hanoivip\Proceed\Models\ProceedHistory;

class ProceedService
{
    const KEY = "gunpowBembem8888";
    
    const IP_POOL = "ProceedIP@";
    
    const PROCEED = "Proceed";
    
    const PROCEED_LOCK = "ProceedLock";
    
    const PROCEED_LIST = "ProceedList";
    
    public function generateCode($uid)
    {
        $code = openssl_encrypt($uid, 'AES-128-ECB', self::KEY, OPENSSL_RAW_DATA);
        return base64_encode($code);
    }
    /**
     * Chuyển toàn bộ số điểm qua xu web
     * @param number $uid
     * @return number|true true when success, fail return error number
     */
    public function exchange($uid)
    {
        $record = Proceed::where('user_id', $uid)->get();
        $count = 0;
        if ($record->isNotEmpty())
        {
            $record = $record->first();
            $count = $record->proceed;
        }
        $result = 1;
        if (!empty($count))
        {
            if ($count < 10)
            {
                return 3;
            }
            $result = 2;
            $rate = config('proceed.webcoin-rate', 100);
            $coin = intval($rate * $count);
            $coin_type = intval(config('proceed.webcoin-type', 0));
            if (BalanceFacade::add($uid, $coin, "Proceed", $coin_type))
            {
                $record->proceed = 0;
                $record->save();
                // Save history
                $result = 0;
            }
            // Save history
            $history = new ProceedHistory();
            $history->user_id = $uid;
            $history->point = $count;
            $history->gain_coin = $coin;
            $history->result = $result;
            $history->save();
        }
        return $result == 0 ? true : $result;
    }
    
    public function getCount($uid)
    {
        $record = Proceed::where('user_id', $uid)->get();
        if ($record->isNotEmpty())
        {
            $record = $record->first();
            return $record->proceed;
        }
        else
        {
            return 0;
        }
    }
    
    private function getTarget($code)
    {
        $uid = openssl_decrypt($code, 'AES-128-ECB', self::KEY);
        //Log::debug("Proceed decrypted uid:" . $uid);
        return intval($uid);
    }
    /**
     * Proceed a code, just caching..
     * @param string $code
     * @return true|number true when success or error number code
     */
    public function proceed($remoteIp, $code)
    {
        if (Cache::has(self::IP_POOL . $remoteIp))
        {
            Log::debug("Proceed this IP need to cooldown..");
            return 0;
        }
        $uid = $this->getTarget($code);
        if (empty($uid))
            throw new Exception("Proceed target code is malform");
        // Processing
        $interval = config('proceed.interval-per-ip', 15);
        $expires = now()->addMinutes($interval);
        $lock =  Cache::lock(self::PROCEED_LOCK, 5);
        try 
        {
            if ($lock->get())
            {
                // Save current changes
                if (!Cache::has(self::PROCEED_LIST))
                    $list = [];
                else
                    $list = Cache::get(self::PROCEED_LIST);
                if (!isset($list[$uid]))
                    $list[$uid] = 1;
                else
                    $list[$uid]++;
                Cache::put(self::PROCEED_LIST, $list);
                // Save IP
                Cache::put(self::IP_POOL . $remoteIp, 1, $expires);
                // Remember
                return true;
            }
        }
        catch (Exception $ex)
        {
            Log::error($ex->getMessage());
            return 1;
        }
        finally
        {
            optional($lock)->release();
        }
        return 1;
    }
    /**
     * Persist number of proceed to database
     */
    public function persist()
    {
        $lock = Cache::lock(self::PROCEED_LOCK);
        try
        {
            if ($lock->get())
            {
                $list = [];
                if (Cache::has(self::PROCEED_LIST)) {
                    $list = Cache::get(self::PROCEED_LIST);
                }
                $errList = [];
                foreach ($list as $uid => $val)
                {
                    try 
                    {
                        $record = Proceed::where('user_id', $uid)->get();
                        if ($record->isNotEmpty())
                        {
                            $record = $record->first();
                        }
                        else 
                        {
                            $record = new Proceed();
                            $record->user_id = $uid;
                            $record->proceed = 0;
                        }
                        $record->proceed += $val;
                        $record->save();
                    }
                    catch (Exception $ex)
                    {
                        Log::error("Proceed persist {$uid}-{$val} error." .  $ex->getMessage());
                        $errList[$uid] = $val;
                    }
                }
                Cache::put(self::PROCEED_LIST, $errList);
            }
        }
        catch (Exception $ex)
        {
            Log::error($ex->getMessage());
        }
        finally 
        {
            optional($lock)->release();
        }
    }
}