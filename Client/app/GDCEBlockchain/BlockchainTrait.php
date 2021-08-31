<?php

namespace App\GDCEBlockchain;

use Illuminate\Database\Eloquent\Model;

trait BlockchainTrait
{
    public function addChainBlock(){

        $query = self::where(self::getKeyName(),self::getKey());
        if(count(self::chainableRelationships)>0){
            $query->with(self::chainableRelationships);
        }

        $data = $query->first();

        $blockchain = new BlockchainClient(self::class,self::getKey(), $data);
        $blockchain->addChainBlock();

//        return $blockchain;
    }
    public function isChainValid()
    {
        $query = self::where(self::getKeyName(),self::getKey());
        if(count(self::chainableRelationships)>0){
            $query->with(self::chainableRelationships);
        }

        $data = $query->first();

        $blockchain = new BlockchainClient(self::class,self::getKey(), $data);
        return $blockchain->isChainValid();
    }

    public function getChain()
    {
        $query = self::where(self::getKeyName(),self::getKey());
        if(count(self::chainableRelationships)>0){
            $query->with(self::chainableRelationships);
        }

        $data = $query->first();

        $blockchain = new BlockchainClient(self::class,self::getKey(), $data);
        return $blockchain->getChain();
    }
}
