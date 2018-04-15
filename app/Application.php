<?php

namespace App;

/* 
  We override the default Laraval Application class to
  chage the default public folder to public_html -> myWeb requirment
  See bootstrap/app.php for use
 */

class Application extends \Illuminate\Foundation\Application
{
    public function publicPath()
    {
        return $this->basePath . DIRECTORY_SEPARATOR . 'public_html';
    }
}