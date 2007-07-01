=== More from Google ===
Contributors: tompahoward
Donate link: http://windyroad.org/software/wordpress/more-from-google-plugin/#donate
Tags: search, google, revenue, Windy Road
Requires at least: 2.0
Tested up to: 2.2
Stable tag: 0.0.2

Easily embed Google search results within your WordPress site, with the More from Google plugin.

== Description ==

Now you can easily add search results from Google within your WordPress search results page.

The More from Google plugin adds the required fields to your
search form and outputs the required XHTML and Javascript to display 
Google's results within your WordPress search results page.  This can be 
used:

* simply to provide your readers with related results from other sites.
* with google site search to provide the readers with supplemental results so they can search WordPress pages and non-WordPress pages on your site.
* encourage readers to stay at your site longer.
* increase your revenue through Google's AdSense for Search program.

The More from Google plugin, also allows you to specify a search term for each post. This search term is used to create a link at the bottom
of your post linking to search results for that term. This encourages
your readers to Google for related articles, without leaving your site.

= How it Works =
Google provides an option to have their results embedded within your page.  To achieve this, Google can provide you with code for a custom search form and code for displaying the results.

The More From Google plugin creates a merged search form, combining Google's search form, with the default WordPress search form. When this merged search form is submitted, it tells both WordPress and Google to perform the search.  Because Google's portion of the form
is designed to be destination independent (it doesn't care what what page your results are shown on) there form is still directed to the WordPress search results page.

Your search results page will display the WordPress search results as usual and with slight modification using the display code from Google, it will also show the search results from Google.

The More from Google link at the bottom of each post call a Javascript function that populates the search form and then submits it.  Because the embedded results from Google will only be displayed if Javascript is  enabled in your readers browser, the More from Google link is only displayed if Javascript is enabled.

== Installation ==
= Installing With Themes That Support More From Google =
1. copy the `more-from-google` directory to your `wp-contents/plugins` directory.
1. Activate the More from Google plugin in your plugins administration page.
1. When editing your posts, add a search term in the 'Google Search Term' field.

= Installing With Themes That Don't Support More From Google =

1. In the theme you are using, make sure the id of the search form is `searchform` and add the following code within the search form:

    	if( function_exists( 'mfg_search_inputs' ) ) {
        	mfg_search_inputs();
    	}

	for instance, in the default theme, you would edit `searchform.php` and change

		<form method="get" id="searchform" action="<?php bloginfo('home'); ?>/">
		<div><input type="text" value="<?php the_search_query(); ?>" name="s" id="s" />
		<input type="submit" id="searchsubmit" value="Search" />
		</div>
		</form>

	to

		<form method="get" id="searchform" action="<?php bloginfo('home'); ?>/">
		<?php if( function_exists( 'mfg_search_inputs' ) ) {
		    mfg_search_inputs();
		}?>
		<div><input type="text" value="<?php the_search_query(); ?>" name="s" id="s" />
		<input type="submit" id="searchsubmit" value="Search" />
		</div>
		</form>

1. In the theme you are using, add the following code to the end of the search page:

		if( function_exists('mfg_show_results') ) {
	    	mfg_show_results();
		}

	for instance, in the default theme, you would edit `search.php` and change

		<?php endif; ?>
			
		</div>
		
		<?php get_sidebar(); ?>
		
		<?php get_footer(); ?>

	to

		<?php endif; ?>
		<?php if( function_exists( 'mfg_show_results' ) ) {
			mfg_show_results();
		}?>	
		</div>
		
		<?php get_sidebar(); ?>
		
		<?php get_footer(); ?>

1. In the theme you are using, edit the search results page and the following condition to the `have_posts` if statement:

		(!function_exists('mfg_show_wordpress_search_results') 
			|| mfg_show_wordpress_search_results())

	for instance, in the default theme, you would edit `search.php` and change

		<?php if (have_posts()) : ?>

	to

		<?php if (have_posts()
	          && (!function_exists('mfg_show_wordpress_search_results') 
    	          || mfg_show_wordpress_search_results())) : ?>

1.  Perform the same steps in "Installing With Themes That Support More From Google"

== Screenshots ==

None yet.

== Frequently Asked Questions ==

Got any questions?

== Release Notes ==
* 0.0.2
	* Added support for old WordPress 2.0 installations
* 0.0.1
	* Better handing of middle clicks.
	* Added [BeNice](http://wordpress.org/extend/plugins/be-nice/ ) support.
	* Fixed Readme.txt
* 0.0.0 
	* Initial Release