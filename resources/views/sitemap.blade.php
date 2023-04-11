<?php echo '<?xml version="1.0" encoding="UTF-8"?>'; ?>
<?php 

	if (!Request::isSecure())
	{
	    // $domain = substr (Request::root(), 7); // $domain is now 'www.example.com'
	    $domain = Request::root(); // $domain is now 'www.example.com'

	}else{
	    $domain = Request::root(); // $domain is now 'www.example.com'
	}

?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
    @foreach ($routes as $route)
        <url>
            <loc><?php echo $domain ?>/{{ $route }}</loc>
            <changefreq>weekly</changefreq>
            <priority>0.9</priority>
        </url>
    @endforeach
</urlset>