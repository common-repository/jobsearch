=== Tamago-DB Job board ===
Contributors: Tamago-DB
Tags: job-board,recruiting,api,job-search,job,recruitment,recruit,job,tamago,tamago-db,
Requires at least: 4.9
Tested up to: 5.2
Requires PHP: 7.1

Tamago-DB Job Board integrates directly into the Tamago-DB ATS platform,The standard free version provides candidates with the ability to search and view available jobs

== Description ==
Tamago-DB Job Board integrates directly into the Tamago-DB ATS platform, thereby enabling recruitment companies to create their own website job-board. 

The Tamago-DB Job-board WP plugin comes with a number of highly customizable templates that seamlessly integrated into the look and feel of the website for high quality user experience. 

The standard free version provides candidates with the ability to search and view available jobs, while the pro paid version enables online application. 

The Tamago-DB job board is compatible with Google for Jobs compliant, England and Japanese language optimised and only available to Tamago-DB licence users. 

If you want to know more about us, please contact us on <a href="https://www.tamago-db.com">https://www.tamago-db.com</a>.

== Installation ==
1. Upload the plugin files to the '/wp-content/plugins/wp-job-board' directory, or install the plugin through the WordPress plugins screen directly.
2. Activate the plugin through the 'Plugins' screen in WordPress
3. Use the Settings->Plugin Name screen to configure the plugin
4. Insert api link and api key provided by Tamago-DB then update it to set-up the plugin
5. Use the shortcode on the widget area or pages to make the plugin active

[tdb_job_board_form] in the page you want to make it work, to add job language filter, you should add the code jobLanguage.

[tdb_job_board_form Language='ja'] to show only the apply form, you should use [tdb_job_board_apply] or [tdb_job_board_apply redirect-link='http://website/thanks'] to redirect on thanks page when apply is done.

[tdb_job_board_last_job url=''] show the 10 last jobs for the number of the day you choose in the admin panel.

[tdb_job_board_category url='' title = ''] show 10 jobs for the category you choose in the admin panel, title is not mandatory.

== Screenshots ==
1. Job list and search form
2. Job list and search form with another template
3. Job detail
