=== Tamago-DB Job board ===
Contributors: Tamago-DB
Tags: job-board,recruiting,api,job-search,job,recruitment,recruit,job,tamago,tamago-db,
Requires at least: 4.9
Tested up to: 6.1
Requires PHP: 7.2

Tamago-DB Job Board integrates directly into the Tamago-DB ATS platform.

== Description ==

Tamago-DB Job Board integrates directly into the Tamago-DB ATS platform, thereby enabling recruitment companies to create their own website job-board.

The Tamago-DB Job-board WP plugin comes with a number of highly customizable templates that seamlessly integrated into the look and feel of the website for high quality user experience.

The Tamago-DB job board is compatible with Google for Jobs compliant, England and Japanese language optimised and only available to Tamago-DB licence users.

If you want to know more about us, please contact us on <a href="https://www.tamago-db.com">https://www.tamago-db.com</a>.

== Installation ==
1. Upload the plugin files to the '/wp-content/plugins/wp-job-board' directory, or install the plugin through the WordPress plugins screen directly.
2. Activate the plugin through the 'Plugins' screen in WordPress
3. Use the Settings->Plugin Name screen to configure the plugin
4. Insert api link and api key provided by Tamago-DB then update it to set up the plugin
5. Use the shortcode on the widget area or pages to make the plugin active

[jobsearch_form] - [tdb_job_board_form] / Include search and list
Parameters:
jobLanguage: filter on the language of the job
url: add this parameter if you use this shortcode on another page than the main job list page. The URL should be the URL of the job list page.
searchHidden: set to true to hide the search form on the result page
api: if you set up multiple api link on admin panel, you can give the number of the api you want to use(by default it will be the first one)

[jobsearch_apply] - [tdb_job_board_apply] / Apply for candidate
Parameters:
redirect-link: redirect to a different page after submitting the registration form
api: if you set up multiple api link on admin panel, you can give the number of the api you want to use(by default it will be the first one)

[jobsearch_last_job] - [tdb_job_board_last_job] / Widget showing the latest published jobs
Parameters:
url: add this parameter if you use this shortcode on another page than the main job list page. The URL should be the URL of the job list page.
api: if you set up multiple api link on admin panel, you can give the number of the api you want to use(by default it will be the first one)

[jobsearch_category] - [tdb_job_board_category] / Widget showing jobs of a specific category from Tamago-db
Parameters:
url: add this parameter if you use this shortcode on another page than the main job list page. The URL should be the URL of the job list page.
api: if you set up multiple api link on admin panel, you can give the number of the api you want to use(by default it will be the first one)

[jobsearch_tag] - [tdb_job_board_tag] / Widget showing jobs of a specific tag from Tamago-db
Parameters:
url: add this parameter if you use this shortcode on another page than the main job list page. The URL should be the URL of the job list page.
tag: tag ID
title: widget title
api: if you set up multiple api link on admin panel, you can give the number of the api you want to use(by default it will be the first one)

[tdb_search_form] / Search form
Parameters:
jobLanguage: filter on the language of the job
url: add this parameter if you use this shortcode on another page than the main job list page. The URL should be the URL of the job list page.
api: if you set up multiple api link on admin panel, you can give the number of the api you want to use(by default it will be the first one)

[tdb_list_form] / List form
Parameters:
jobLanguage: filter on the language of the job
company: ID of the company jobs will be listed.
featured: true or false to add featured job.
url: add this parameter if you use this shortcode on another page than the main job list page. The URL should be the URL of the job list page.
urllist: pages you need to redirect to go to the detail
api: if you set up multiple api link on admin panel, you can give the number of the api you want to use(by default it will be the first one)

[jobsearch_featured] - [tdb_job_board_featured] / List featured jobs from a given Company ID
Parameters:
company: ID of the company jobs will be listed.
url: add this parameter if you use this shortcode on another page than the main job list page. The URL should be the URL of the job list page.
api: if you set up multiple api link on admin panel, you can give the number of the api you want to use(by default it will be the first one)

[jobsearch_apply_btn] - [tdb_job_board_apply_btn] / Create a button who redirect to the apply pages and get the current page title
url: add this parameter to go to the apply/register page

== Screenshots ==
1. Job list and search form
2. Job list and search form with another template
3. Job detail
