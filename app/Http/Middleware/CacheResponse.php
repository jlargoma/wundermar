<?php

namespace App\Http\Middleware;

use Exception;
use Illuminate\Filesystem\Filesystem;
use Closure;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
//use Silber\PageCache\Middleware\CacheResponse as BaseCacheResponse;

class CacheResponse 
{
  
   /**
     * The filesystem instance.
     *
     * @var \Illuminate\Filesystem\Filesystem
     */
    protected $files;
   /**
     * Constructor.
     *
     * @var \Illuminate\Filesystem\Filesystem  $files
     */
    public function __construct(Filesystem $files)
    {
        $this->files = $files;
    }
    
    protected function shouldCache(Request $request, Response $response)
    {
        // In this example, we don't ever want to cache pages if the
        // URL contains a query string. So we first check for it,
        // then defer back up to the parent's default checks.
        if ($request->getQueryString()) {
            return false;
        }
//        return parent::shouldCache($request, $response);
        return $request->isMethod('GET') && $response->getStatusCode() == 200;
    }
    
     /**
     * Handle an incoming request.
     *
     * @param  \Symfony\Component\HttpFoundation\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);

        if ($this->shouldCache($request, $response)) {
          
          list($path, $file) = $this->getDirectoryAndFileNames($request);

          $this->files->makeDirectory($path, 0775, true, true);

          $this->files->put(
              $this->join([$path, $file]),
              $response->getContent(),
              true
          );
          
        }

        return $response;
    }
    
     /**
     * Get the names of the directory and file.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    protected function getDirectoryAndFileNames($request)
    {
        $segments = explode('/', ltrim($request->getPathInfo(), '/'));

        $file = $this->aliasFilename(array_pop($segments)).'.html';

        $dir = public_path().'/page-cache';
        return [$dir, $file];
    }
    
    /**
     * Alias the filename if necessary.
     *
     * @param  string  $filename
     * @return string
     */
    protected function aliasFilename($filename)
    {
      $default = 'home_'. urlencode(config('app.url'));
      if($filename=='create-cache') return $default;
        return $filename ?:$default;
        
//        return $filename ?: 'pc__index__pc';
    }

   
     /**
     * Join the given paths together by the system's separator.
     *
     * @param  string[] $paths
     * @return string
     */
    protected function join(array $paths)
    {
        $trimmed = array_map(function ($path) {
            return trim($path, '/');
        }, $paths);

        return $this->matchRelativity(
            $paths[0], implode('/', array_filter($trimmed))
        );
    }
    /**
     * Makes the target path absolute if the source path is also absolute.
     *
     * @param  string  $source
     * @param  string  $target
     * @return string
     */
    protected function matchRelativity($source, $target)
    {
        return $source[0] == '/' ? '/'.$target : $target;
    }
}