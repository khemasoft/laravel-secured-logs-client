<?php
namespace App\GDCEBlockchain;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class BlockchainClient
{
    protected $baseUrl;
    protected $clientId;
    protected $clientSecret;
    protected $clientUser;
    protected $clientPassword;
    protected $accessToken;

    protected $modelClass;
    protected $modelId;
    protected $data;

    public function __construct($modelClass, $modelId, $data)
    {
        $this->baseUrl = $this->getBaseUrl();
        $this->clientId = $this->getClientId();
        $this->clientSecret = $this->getClientSecret();
        $this->clientUser = $this->getClientUser();
        $this->clientPassword = $this->getClientPassword();
        $this->accessToken = $this->getAccessToken();

        $this->modelClass = $modelClass;
        $this->modelId = $modelId;
        $this->data = $data;
    }

    public function addChainBlock(){
        $response = Http::withHeaders(collect($this->accessToken)->toArray())
        ->post($this->baseUrl.'/api/blockchain/add-chain-block',[
            'client_id'=>$this->clientId,
            'causer' => Auth::user(),
            'model_class' => $this->modelClass,
            'model_id' => $this->modelId,
            'data'=>$this->data
        ]);

        if($response->ok()){
            Log::info($this->modelClass.'::'.$this->modelId.' successfully submitted to Blockchain server.');
        }else{
            Log::error($this->modelClass.'::'.$this->modelId.' encountered an error during submitting to Blockchain server.');
        }
    }

    public function getChain(){
        $response = Http::withHeaders(collect($this->accessToken)->toArray())
            ->post($this->baseUrl.'/api/blockchain/get-chain',[
                'client_id'=>$this->clientId,
                'model_class' => $this->modelClass,
                'model_id' => $this->modelId,
            ]);

        return json_decode($response->body());
    }

    public function isChainValid(){
        $response = Http::withHeaders(collect($this->accessToken)->toArray())
            ->post($this->baseUrl.'/api/blockchain/is-chain-valid',[
                'client_id'=>$this->clientId,
                'causer' => Auth::user(),
                'model_class' => $this->modelClass,
                'model_id' => $this->modelId,
                'data'=>$this->data
            ]);

        if($response->ok()){
            if(collect(json_decode($response->body()))['blockchain_status']){
                Log::info('Congratulation!  '.$this->modelClass.'::'.$this->modelId.' has a valid blockchain.');
            }else{
                Log::warning('Warning!  '.$this->modelClass.'::'.$this->modelId.' has an invalid blockchain.');
            }

            return collect(json_decode($response->body()))['blockchain_status'];
        }else{
            Log::error($this->modelClass.'::'.$this->modelId.' encountered an error during checking blockchain validity.');

            return ['error'=>'Unable to retrieve the data.'];
        }
    }
    private function getAccessToken(){
        $response = Http::asForm()->post($this->baseUrl.'/oauth/token', [
            'grant_type' => 'password',
            'client_id' => $this->clientId,
            'client_secret' => $this->clientSecret,
            'username' => $this->clientUser,
            'password' => $this->clientPassword,
            'scope' => '*',
        ]);

        return json_decode($response->body());
    }

    private function getBaseUrl(){
        return config('blockchain_client.base_url');
    }

    private function getClientId(){
        return config('blockchain_client.client_id');
    }

    private function getClientSecret(){
        return config('blockchain_client.client_secret');
    }

    private function getClientUser(){
        return config('blockchain_client.username');
    }

    private function getClientPassword(){
        return config('blockchain_client.password');
    }
}
