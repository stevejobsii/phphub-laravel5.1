<?php 
namespace App\good\Core;

interface CreatorListener
{
    public function creatorFailed($errors);
    public function creatorSucceed($model);
}
