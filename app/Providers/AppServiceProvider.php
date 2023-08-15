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
                $disktotal       = disk_total_space('/media/dkmads-upload/');
                $disktotalsize   = $disktotal / 1073741824;
                $diskfree        = disk_free_space('/media/dkmads-upload/');
                $used            = $disktotal - $diskfree;
                $diskusedsize    = $used / 1073741824;
                $diskuse1        = round(100 - (($diskusedsize / $disktotalsize) * 100));
                $diskuse         = round(100 - ($diskuse1)) . '%';
                $disk_total      = disk_total_space('/media/dkmads-upload2/');
                $disk_total_size = $disk_total / 1073741824;
                $disk_free       = disk_free_space('/media/dkmads-upload2/');  
                $used2           = $disk_total - $disk_free;
                $disk_used_size  = $used2 / 1073741824;
                $diskuse2        = round(100 - (($disk_used_size / $disk_total_size) * 100));
                $diskuse2        = round(100 - ($diskuse2)) . '%';
                $data = [
                    'disktotal'  => $this->formatFileSize($disktotal),
                    'used'       => $this->formatFileSize($used),
                    'diskuse'    => $diskuse,
                    'disk_total' => $this->formatFileSize($disk_total),
                    'used2'      => $this->formatFileSize($used2),
                    'diskuse2'   => $diskuse2,
                ];
                $view->with('data',$data);
            }
        });
    }

    private function formatFileSize($bytes)
    {
        $sizes = ['Bytes', 'KB', 'MB', 'GB', 'TB'];
        if ($bytes === 0) return '0 Byte';
        $i = floor(log($bytes, 1024));
        return round($bytes / pow(1024, $i)).' '. $sizes[$i];
    }
}
