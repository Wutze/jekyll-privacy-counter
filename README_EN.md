# jekyll privacy counter

[![Made with Jekyll](https://img.shields.io/badge/Made%20with-Jekyll-b7173b?logo=jekyll&logoColor=white)](https://jekyllrb.com/)
[![Privacy Friendly](https://img.shields.io/badge/Privacy-Friendly-brightgreen)](#)
[![SQLite3](https://img.shields.io/badge/Database-SQLite3-blue)](https://www.sqlite.org/)
[![License: EUPL-1.2](https://img.shields.io/badge/License-EUPL--1.2-blue.svg)](https://joinup.ec.europa.eu/collection/eupl/eupl-text-eupl-12)
[![No Cookies](https://img.shields.io/badge/Cookies-None-orange)](#)
[![Zero Tracking](https://img.shields.io/badge/Tracking-Zero-critical)](#)

---

A minimalist, **privacy-friendly visitor counter** for **Jekyll**,  
with no cookies, external trackers, or JavaScript dependencies.  
Ideal for static blogs that still want to know which posts are being read.

---

## Intro

Jekyll is a framework that serves static websites. Since I don’t have server logs enabled — for privacy reasons, and because the site itself is behind a firewall in a DMZ — I needed a very simple solution to count how many times each page was viewed.  

I didn’t want to use cookies or JavaScript. Most analytics tools are too big, too complex, or privacy-invasive, and I didn’t want to rely on Google or others. Still, I wanted a minimal way to measure interest.  

This solution simply delivers a tiny CSS file from the HTML header that increments a value in a small SQLite3 database for each page view.  

That’s it.

Jekyll runs in two basic modes:
- **Development**
- **Production**

The counter is only active when the site runs in production.  
In development mode, statistics per article and a full stats table are shown instead.

---

## Start Development Environment

First, change to your Jekyll project directory and start your local instance:

```bash
bundle exec jekyll serve --unpublished --drafts --livereload
```
Depending on your _config.yml, this defines which port and IP the test site runs on.
You can open it in your browser and gradually integrate all scripts.

--livereload automatically refreshes your browser after each file save.

--unpublished includes posts marked with published: false.

--drafts includes files from the _drafts folder (not published until moved to _posts).

Integration
To build your production site, use:

``` JEKYLL_ENV=production bundle exec jekyll build ```

This ensures Jekyll recognizes the production environment and applies the right templates:

```
{% if jekyll.environment != "production" %}
{% include js-ministat.html %}
{% endif %}
```
Everything not in production includes the js-ministat.html, which enables JavaScript, queries the database, and displays the counters.

Make sure the environment variables are set correctly, otherwise counters might appear on your live site (which we don’t want).

```
{% if jekyll.environment != "production" %}
  {% if post.published == false %}
    <span class="mini-led-red views" data-page="{{ post.title | escape }}">&nbsp;</span>
  {% else %}
    <span class="mini-led-green views" data-page="{{ post.title | escape }}">&nbsp;</span>
  {% endif %}
{% endif %}
```

That’s basically it — and this is what it looks like:

Enjoy!

### Extra: start.sh
I work directly on the web server that also serves my live website.
My development environment resides there as well.

By connecting via SSH and running ./start.sh, the test site launches immediately.
This instance includes the statistics and view counters.
When I stop it using CTRL+C, the script asks whether to deploy the changes to production.

In your Jekyll post headers,

``` published: false ```
prevents the post from being published.
If omitted or set to true, the post is published during the next production build.

Configuration is simple:

```bash
SRC="361-grad-lokal/"
DEST="361-grad-webseite/"
GIT_REPO="http://gitlab1.home/micro/361neu"
```

Jekyll generates the _site folder containing the built static site.
Your web server should be configured to serve this directory automatically.

You don’t have to use this script to run the counter — it’s just a helper.
Maybe it inspires you to improve or customize it further.
For me, it saves a lot of time — no forgotten copies, no manual syncing, and automatic backups. 😄