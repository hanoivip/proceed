<?php
namespace Hanoivip\Proceed\Controllers;

use Hanoivip\Proceed\Serivces\ProceedService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Exception;

class ProceedController
{
    private $proceed;
    
    const LOCK = "ProceedClient";
    
    public function __construct(ProceedService $proceed)
    {
        $this->proceed = $proceed;
    }
    /**
     * View user's info
     * + proceed link
     * + number of kick
     * + exchange button
     */
    public function home() 
    {
        $uid = Auth::user()->getAuthIdentifier();
        $code= $this->proceed->generateCode($uid);
        $link = url('/proc?code=' . $code);
        $count = $this->proceed->getCount($uid);
        Log::debug($link);
        return view('hanoivip::proceed-home', ['link' => $link, 'count' => $count]);
    }
    /**
     * Exchange the number of kick info web's coin
     * @param Request $request
     */
    public function exchange(Request $request)
    {
        $clientIp = $request->headers->get('X-Real-IP');//Cloudflare proxy
        $uid = Auth::user()->getAuthIdentifier();
        try
        {
            $lock = Cache::lock(self::LOCK . $clientIp);
            $result = -1;
            if ($lock->get())
            {
                $result = $this->proceed->exchange($uid);
                $lock->release();
            }
            if ($result === true)
            {
                return view('hanoivip::proceed-exchange-result', ['message' => __('hanoivip::proceed.exchange.success')]);
            }
            else 
            {
                return view('hanoivip::proceed-exchange-result', ['message' => __('hanoivip::proceed.exchange.fail' . $result)]);
            }
        }
        catch (Exception $ex)
        {
            Log::error("Proceed exchange exception: " . $ex->getMessage());
            return view('hanoivip::proceed-exchange-result', ['error' => __('proceed.exchange.exception')]);
        }
    }
    /**
     * View user's proceed link
     * Note: crawler
     * @param Request $request
     * @param string $code
     */
    public function click(Request $request, $code)
    {
        return view('hanoivip::proceed-click', ['code' => $code]);
        
    }
    public function click2(Request $request)
    {
        $code = $request->input('code');
        return view('hanoivip::proceed-click', ['code' => $code]);
        
    }
    public function doClick(Request $request)
    {
        $clientIp = $request->headers->get('X-Real-IP');//Cloudflare proxy
        $rules = ['captcha' => 'required|captcha', 'code' => 'required'];
        $code = $request->input('code');
        $validator = validator()->make(request()->all(), $rules);
        if ($validator->fails()) 
        {
            return view('hanoivip::proceed-click', ['code' => $code, 'error' => __('hanoivip::proceed.captcha')]);
        } 
        else 
        {
            try 
            {
                $lock = Cache::lock(self::LOCK . $clientIp);
                $result = -1;
                if ($lock->get())
                {
                    $result = $this->proceed->proceed($clientIp, $code);
                    $lock->release();
                }
                if ($result === true)
                {
                    return view('hanoivip::proceed-click-result', ['message' => __('hanoivip::proceed.success')]);
                }
                else
                {
                    return view('hanoivip::proceed-click-result', ['error' => __('hanoivip::proceed.fail.' . $result)]);
                }
            } 
            catch (Exception $ex) 
            {
                Log::error('Proceed exception:' . $ex->getMessage());
                return view('hanoivip::proceed-click-result', ['code' => $code, 'error' => __('hanoivip::proceed.exception')]);
            }
            
        }
    }
    
    public function history(Request $request)
    {
        
    }
}
