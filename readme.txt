=== Tamago-DB Job board ===
Contributors: joanv
Tags: job-board,recruiting,job,recruitment,ats
Requires at least: 4.9
Tested up to: 6.6.2
Requires PHP: 7.3

Tamago-DB Job Board integrates directly into the Tamago-DB ATS platform.

== Description ==

The Tamago-DB Job Board WP Plugin integrates directly into the Tamago-DB ATS platform, enabling recruitment companies to create their own website job-board. It comes with a number of highly customizable templates that allow integration into the look and feel of the website.

It's compatible with Google for Jobs, English and Japanese language optimised and only available to Tamago-DB users.

If you want to know more about us, please contact us on <a href="https://www.tamago-db.com">https://www.tamago-db.com</a>.

== Installation ==

1. Upload the plugin files to the '/wp-content/plugins/wp-job-board' directory, or install the plugin through the WordPress plugins screen directly.
2. Activate the plugin through the 'Plugins' screen in WordPress
3. Click on 'TDB Job Board' on the left to go to the plugin settings for configuration.
4. Insert the api url and api key to link the plugin to your Tamago-DB instance. You can find how to generate an API key in the documentation of your Tamago-DB.
5. Use the shortcode to add the job board to a page in your website.

[jobsearch_form] - [tdb_job_board_form] / Include search and list

Parameters:

jobLanguage: filter jobs on the selected language
url: add this parameter if you use this shortcode on another page than the main job list page. The URL should be the URL of the job list page.
searchHidden: set to true to hide the search form on the result page
api: if you set up multiple APIs on the admin panel, specify the API number you want to use (by default it will be the first one)
companyId : limit the job search to jobs belonging to this company (use the company ID)
tag : limit the job search to jobs with this tag (use the tag ID)

[jobsearch_apply] - [tdb_job_board_apply] / Apply for candidate

Parameters:

redirect-link: redirect to a different page after submitting the registration form
api: if you set up multiple APIs on the admin panel, specify the API number you want to use (by default it will be the first one)

[jobsearch_last_job] - [tdb_job_board_last_job] / Widget who shown latest published jobs

Parameters:

url: add this parameter if you use this shortcode on another page than the main job list page. The URL should be the URL of the job list page.
api: if you set up multiple APIs on the admin panel, specify the API number you want to use (by default it will be the first one)

[jobsearch_category] - [tdb_job_board_category] / Widget who shown some specific jobs on category from Tamago-db

Parameters:

url: add this parameter if you use this shortcode on another page than the main job list page. The URL should be the URL of the job list page.
api: if you set up multiple APIs on the admin panel, specify the API number you want to use (by default it will be the first one)

[jobsearch_tag] - [tdb_job_board_tag] / Widget who shown some specific jobs on tag from Tamago-db

Parameters:

url: add this parameter if you use this shortcode on another page than the main job list page. The URL should be the URL of the job list page.
tag: tag ID
title: widget title
api: if you set up multiple APIs on the admin panel, specify the API number you want to use (by default it will be the first one)

[tdb_search_form] / Search form

Parameters:

jobLanguage: filter on the language of the job
url: add this parameter if you use this shortcode on another page than the main job list page. The URL should be the URL of the job list page.
api: if you set up multiple APIs on the admin panel, specify the API number you want to use (by default it will be the first one)

[tdb_list_form] / List form

Parameters:

jobLanguage: filter on the language of the job
company: ID of the company jobs will be listed.
featured: true or false to add featured job.
url: add this parameter if you use this shortcode on another page than the main job list page. The URL should be the URL of the job list page.
urllist: pages you need to redirect to go to the detail
api: if you set up multiple APIs on the admin panel, specify the API number you want to use (by default it will be the first one)

[jobsearch_featured] - [tdb_job_board_featured] / List featured jobs from a given Company ID

Parameters:

company: ID of the company jobs will be listed.
url: add this parameter if you use this shortcode on another page than the main job list page. The URL should be the URL of the job list page.
api: if you set up multiple APIs on the admin panel, specify the API number you want to use (by default it will be the first one)

[jobsearch_apply_btn] - [tdb_job_board_apply_btn] / Create a button who redirect to the apply pages and get the current page title

Parameters:

url: add this parameter to go to the apply/register page

== Screenshots ==

1. Job list and search form
2. Job list using another template
3. Job detail
