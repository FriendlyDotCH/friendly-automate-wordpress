=== Friendly Automate ===
Contributors: friendly,mautic,hideokamoto,shulard,escopecz,dbhurley,macbookandrew,bradycargle,gabcarvalhogama
Donate link: https://friendly.ch
Tags: marketing automation, crm, analytics, dynamic content, form, marketing, automation, email marketing, email marketing automation, sales,friendly, friendly automate
Requires at least: 4.7
Tested up to: 5.7.2
Stable tag: 1.0
Requires PHP: 7.0
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html
 
The fastest and easiest way to to automate your marketing with contact management (CRM), email marketing, forms, dynamic content, and analytics.


[Friendly Automate](https://friendly.ch) [Wordpress Plugin](https://wordpress.org/plugins/wp-mautic/) injects Friendly tracking script and image in your WordPress website. Your Friendly instance will be able to track information about your visitors. You can also insert Friendly content inside your website using different shortcodes, and integrate with Elementor.

## Key features
- You don't have to edit source code of your template to insert tracking code.
- Plugin adds additional information to tracking image URL so you get better results than using just plain HTML code of tracking image.
- You can use Friendly form embed with shortcode described below.
- You can choose where the script is injected (header / footer).
- Tracking image can be used as fallback when JavaScript is disabled.

## Configuration

Once installed, the plugin will appear in your plugin list:

1. Enable it.
2. Go to the Settings page and set your Friendly  URL.

And that's it!

## Usage

### Friendly Forms

To load a Friendly Form to your WP post, insert this shortcode to the place you want the form to appear:

```
[mautic type="form" id="1"]
```

Replace "1" with the form ID you want to load. To get the ID of the form, go to your Mautic, open the form detail and look at the URL. The ID is right there. For example in this URL: http://yourfriendlysite.com/s/forms/view/3 the ID is 3.

### Friendly Focus

To load a Friendly Focus to your post, insert this shortcode to the place you want the form to appear:

```
[mautic type="focus" id="1"]
```

Replace "1" with the focus ID you want to load. To get the ID of the focus, go to your Mautic, open the focus detail and look at the URL. The ID is right there. For example in this URL: http://yourmautic.com/s/focus/3.js the ID is 3.

### Friendly Dynamic Content

To load dynamic content into your WordPress content, insert this shortcode where you'd like it to appear:

```
[mautic type="content" slot="slot_name"]Default content to display in case of error or unknown contact.[/mautic]
```

Replace the "slot_name" with the slot name you'd like to load. This corresponds to the slot name you defined when building your campaign and adding the "Request Dynamic Content" contact decision.

### Friendly Gated Videos

Friendly supports gated videos with Youtube, Vimeo, and MP4 as sources.

To load gated videos into your WordPress content, insert this shortcode where you'd like it to appear:

```
[mautic type="video" gate-time="#" form-id="#" src="URL"]
[mautic type="video" src="URL"]
```

Replace the # signs with the appropriate number. For gate-time, enter the time (in seconds) where you want to pause the video and show the Friendly form. For form-id, enter the id of the Friendly form that you'd like to display as the gate. Replace URL with the browser URL to view the video. In the case of Youtube or Vimeo, you can simply use the URL as it appears in your address bar when viewing the video normally on the providing website. For MP4 videos, enter the full http URL to the MP4 file on the server.

### Friendly Tags

You can add or remove multiple lead tags on specific pages using commas. To remove an tag you have to use minus "-" signal before tag name:

```
[mautic type="tags" values="mytag,anothertag,-removetag"]
```

### Elementor Integration

Using Elementor Forms to collect your leads? Check out our video guide on how to integrate Friendly with Elementor: https://docs.friendly.ch/Integrate-Friendly-Automate-with-Elementor-Form-4cf8d07f78a140a5a4f246587e2525ee

### Friendly Tracking Script

Tracking script works right after you finish the configuration steps. That means it will insert the `mtc.js` script from your Friendly instance. You can check HTML source code (CTRL + U) of your WordPress website to make sure the plugin works. You should be able to find something like this:

```html
<script>
    (function(w,d,t,u,n,a,m){w['MauticTrackingObject']=n;
        w[n]=w[n]||function(){(w[n].q=w[n].q||[]).push(arguments)},a=d.createElement(t),
        m=d.getElementsByTagName(t)[0];a.async=1;a.src=u;m.parentNode.insertBefore(a,m)
    })(window,document,'script','http://yourfriendlysite.com/mtc.js','mt');

    wpmautic_send();
</script>
```

#### Custom attributes handling

If you need to send custom attributes within Mautic events, you can use the `wpmautic_tracking_attributes` filter.

```php
add_filter('wpmautic_tracking_attributes', function($attrs) {
    $attrs['preferred_locale'] = $customVar;
    return $attrs;
});
```

The returned attributes will be added to Friendly payload.
