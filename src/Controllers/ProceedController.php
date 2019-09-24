<?php

use GuzzleHttp\Psr7\Request;
use Hanoivip\Proceed\Serivces\ProceedService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

namespace \Hanoivip\Proceed\Controllers;

class ProceedController
{
    private $proceed;
    
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
        $link = env('APP_URL') . '/proc/' . $code;
        $count = $this->proceed->getCount($uid);
        return view('hanoivip::proceed-home', ['link' => $link, 'count' => $count]);
    }
    /**
     * Exchange the number of kick info web's coin
     * @param Request $request
     */
    public function exchange(Request $request)
    {
        $uid = Auth::user()->getAuthIdentifier();
    }
    /**
     * View user's proceed link
     * Note: crawler
     * @param Request $request
     * @param string $code
     */
    public function click(Request $request, $code)
    {
        return view('hanoivip::click', ['code' => $code]);
        
    }
    public function doClick(Request $request)
    {
        $rules = ['captcha' => 'required|captcha'];
        $code = $request->input('code');
        $validator = validator()->make(request()->all(), $rules);
        if ($validator->fails()) 
        {
            return view('hanoivip::proceed-click', ['code' => $code, 'error' => 'Captcha fail']);
        } 
        else 
        {
            try 
            {
                $result = $this->proceed->proceed($code);
                if ($result === true)
                {
                    return view('hanoivip::proceed-click-result', ['error' => __('proceed.success')]);
                }
                else 
                {
                    return view('hanoivip::proceed-click-result', ['message' => __('proceed.fail.' . $result)]);
                }
            } 
            catch (Exception $ex) 
            {
                Log::error('Proceed exception:' . $ex->getMessage());
                return view('hanoivip::proceed-click-result', ['code' => $code, 'error' => __('proceed.exception')]);
            }
            
        }
    }
    
}