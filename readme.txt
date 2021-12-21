=== Friendly Automate ===
Contributors: friendlych,mautic,bradycargle,hideokamoto,shulard,escopecz,dbhurley,macbookandrew,gabcarvalhogama
Donate link: https://friendly.ch
Tags: marketing automation, crm, analytics, dynamic content, form, marketing, automation, email marketing, email marketing automation, sales, friendly, friendly automate
Requires at least: 4.7
Tested up to: 5.8.2
Stable tag: 1.1.0
Requires PHP: 7.0
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html
 
The fastest and easiest way to to automate your marketing with contact management (CRM), email marketing, forms, dynamic content, and analytics.


The [Friendly Automate](https://friendly.ch/automate) WordPress Plugin injects the Friendly Automate tracking script and images into your WordPress website. Your Friendly Automate instance will be able to track information about your visitors like what pages they visit and what content they're interested in. You can also insert Friendly Automate's dynamic content inside your website using different shortcodes, as well as integrate with Elementor.

== Key features ==
- Easy setup on WordPress. You don't have to edit your source code, go through complex tutorials, or hire an expensive expert. 
- Get detailed analytics. You can see the content that users are engaging with and interested in.
- Simple content embedding. Add your forms or dynamic content to your WordPress site with a single shortcode.

== Configuration ==

Once installed, the plugin will appear in your plugin list:

1. Enable it.
2. Go to the Settings -> Friendly Automate and set your Friendly Automate URL.

You can also customize where you want to insert the tracking script location on your website. Feel free to leave this as the default option.

== Usage ==

=== Friendly Automate Forms ===

To load a Friendly Automate Form to your WordPress page or post, insert this shortcode to the place you want the form to appear:

`
[friendlyautomate type="form" id="1"]
`

Replace "1" with the form ID you want to load. To get the ID of the form, go to your Friendly Automate account, open the form detail and look at the URL. The ID is right there. For example in this URL: http://yourfriendlysite.com/s/forms/view/3 the ID is 3.

=== Friendly Automate Dynamic Content ===

To load dynamic content into your WordPress content, insert this shortcode where you'd like it to appear:

`
[friendlyautomate type="content" slot="slot_name"]Default content to display in case of error or unknown contact.[/friendlyautomate]
`

Replace the "slot_name" with the slot name you'd like to load. This corresponds to the slot name you defined when building your campaign and adding the "Request Dynamic Content" contact decision.

=== Elementor Integration ===

Using Elementor Forms to collect your leads? Check out our video guide on how to [integrate Friendly Automate with Elementor](https://docs.friendly.ch/automate-elementor-en)

=== Friendly Automate Tracking Script ===

Tracking script works right after you finish the configuration steps. That means it will insert the `mtc.js` script from your Friendly Automate instance. You can check HTML source code (CTRL + U) of your WordPress website to make sure the plugin works. You should be able to find something like this:

`html
<script>
    (function(w,d,t,u,n,a,m){w['MauticTrackingObject']=n;
        w[n]=w[n]||function(){(w[n].q=w[n].q||[]).push(arguments)},a=d.createElement(t),
        m=d.getElementsByTagName(t)[0];a.async=1;a.src=u;m.parentNode.insertBefore(a,m)
    })(window,document,'script','http://yourfriendlysite.com/mtc.js','mt');

    friendlyautomate_send();
</script>
`

== Screenshots ==

1. The WordPress settings page for Friendly Automate

2. Adding a shortcode to a post or page

3. Integrating Friendly Automate with Elementor

== Changelog ==

= 1.1.0 =
* Added functionality to track WordPress tags and categories in Friendly Automate

= 1.0.1 =
* Improved readme for extra clarity and added better pictures

The Friendly Automate plugin is based on the WP Mautic plugin by [Mautic](https://www.mautic.org/). While we have added on to the plugin, removed some bugs, and made it more accessible to our clients, we could not be here without Mautic. Please consider getting involved and contributing to the Mautic project.