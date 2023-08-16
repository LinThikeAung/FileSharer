<?php

namespace App\Providers;

use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use App\Repositories\FileUploadRepository;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(FileUploadRepository::class);
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        View::composer('*', function ($view) {
            $data = null ;
            if(auth()->check()){      
                //Disk 1       
                $output = shell_exec('df -h /media/dkmads-upload/');
                $lines  = explode("\n", $output);
                $parts  = preg_split('/\s+/', $lines[1]);

                //Disk 2
                $output2 =  shell_exec('df -h /media/dkmads-upload2/');
                $lines2  = explode("\n", $output2);
                $parts2  = preg_split('/\s+/', $lines2[1]);
                $data = [
                    'disktotal1'  => $parts[1],
                    'used1'       => $parts[2],
                    'available1'  => $parts[3],
                    'percent1'    => $parts[4],
                    'disktotal2'  => $parts2[1],
                    'used2'       => $parts2[2],
                    'available2'  => $parts2[3],
                    'percent2'    => $parts2[4],
                ];
                $view->with('data',$data);
            }
        });
    }
}
