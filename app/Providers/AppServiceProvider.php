<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Classes\Mobile;
use App\Sites;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
      
      if (config('app.env') !== 'VIRTUAL' && isset($_SERVER["HTTP_HOST"])){
          if(!(isset($_SERVER["HTTPS"])) || $_SERVER["HTTPS"] != "on")
          {
              header("Location: https://" . $_SERVER["HTTP_HOST"] . $_SERVER["REQUEST_URI"]);
              exit();
          }
      }
      
      $this->getSiteData();
      
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
    
    private function getSiteData() {
      
      
      //Show in apartamentosierranevada
      $this->app['config']['show_ASN'] = true;
      //show in riadpuertasdelalbaicin
      $this->app['config']['show_RPA'] = false;
      
      if (! isset($_SERVER['HTTP_HOST'])) return;
        
      $mobile = new Mobile();
      config(['app.is_mobile'=>$mobile->isMobile()]);
      
      
      $host = $_SERVER['HTTP_HOST'];
      $site = Sites::where('url',$host)->first();
      if ($site){
        config(['app.site_id'=>$site->id]);
        config(['app.url'=>$host]);
        config(['app.site'=>$site->site]);
        config(['app.title'=>$site->title]);
        config(['app.contact'=>$site->mail_from]);
        config(['app.name'=>$site->name]);
        config(['mail.from.address'=>$site->mail_from]);
        config(['mail.from.name'=>$site->mail_name]);
        
      } else {
        config(['app.site_id'=>-1]);
      }
    }
    
  
}
