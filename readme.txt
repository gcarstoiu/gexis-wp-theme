=== gexis ===
Contributors: gcarstoiu
Requires at least: 6.6
Tested up to: 6.8
Requires PHP: 7.4
Stable tag: 1.1.0
License: GPLv3 or later
License URI: https://www.gnu.org/licenses/gpl-3.0.html

A block theme for gexis, a personal site in four parts whose letters are tags.

== Description ==

The four letters of the gexis mark are tags: gazette, engine room, inbox and
sound. Each gets its tile colour and lights its letter of the mark on its
archive. Dark and light modes, threaded comments with avatars, a CV-style
author page. See README.md.

== Copyright ==

gexis WordPress Theme, Copyright 2026 George Carstoiu
gexis is distributed under the terms of the GNU GPL v3 or later.

This program is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 3 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
GNU General Public License for more details.

== Resources ==

* Nunito Sans, Copyright 2016 The Nunito Sans Project Authors
  License: SIL Open Font License 1.1, assets/fonts/LICENSE-NunitoSans.txt
  Source: https://github.com/googlefonts/NunitoSans
* IBM Plex Mono, Copyright 2017 IBM Corp.
  License: SIL Open Font License 1.1, assets/fonts/LICENSE-IBMPlexMono.txt
  Source: https://github.com/IBM/plex
* Bricolage Grotesque, Copyright 2022 The Bricolage Grotesque Project Authors
  License: SIL Open Font License 1.1, assets/fonts/LICENSE-BricolageGrotesque.txt
  Source: https://github.com/ateliertriay/bricolage
* Parsedown 1.8.0, Copyright 2013-2018 Emanuil Rusev
  License: MIT, inc/vendor/LICENSE-Parsedown.txt
  Source: https://github.com/erusev/parsedown
* screenshot.jpg: the theme rendering its own development seed; the
  images in it are generated placeholders. GPLv3 or later.

== Changelog ==

= 1.1.0 =
* New layout on every page, from the "Front page" design (George, 2026-10-24). Each page opens with one large card: the post's picture with its title on it on the home page and on posts, a head card elsewhere.
* Home: the lead post as a wide picture, the four tags as a row of cards, then the next seven posts as a grid of picture and text tiles with the Guides beside them, and a link to the whole archive.
* Posts: On this page (the post's sections and its comments) and the post's tags beside the body; older and newer as two picture cards; the comments as cards with the form beside them.
* Archive: a row per month, the month's first post across the row.
* Tag: the tag's card (its letter lit, name, description, counts), its documents, then its posts as a grid, 14 a page.
* About me: the CV as cards; On the bench and Lately below it.
* Guides: the document's source and sections beside it, the other guides under it.
* Search: the field in a card, results as cards with the words searched for marked, and the results by tag beside them.
* This site: the four tags two by two; Comments and Elsewhere side by side (insert the This site pattern again to get them).
* 404: a card with the mark, Home, Search and the tags; the three latest posts below.
* Footer: the wordmark, the tags with their colours, About me, This site, RSS.
* New block: On this page (gexis/toc). New variants: tag cards as a strip or for one post; search state in parts; tag chips with counts.

= 1.0.6 =
* New screenshot: the current header, with About me and This site.

= 1.0.5 =
* Inbox is now for reader feedback and requests. Its default description reads: "Feedback and requests: what readers ask me to look into, fix or think about again." A description set in Posts → Tags still takes precedence.

= 1.0.4 =
* The home page's middle column is Guides: the pages that show a repository document (installation guide, manual, FAQ, hardware, release notes), in their page order. It replaces Most discussed.

= 1.0.3 =
* Pages take tags. A tag's archive lists its pages as Documents, a row of cards above its posts, and a tagged page shows its tag above the title. Post counts count posts only.

= 1.0.2 =
* New block, Repository document: shows a Markdown file from the Gexis Player repository (or another public GitHub repository) as a page, refreshed hourly, with links between the documents pointing at their pages on the site.

= 1.0.1 =
* Images in posts keep their own shape instead of being cropped to 16:10.
* About me in the header opens the owner's author page; About is now This site.

= 1.0.0 =
* First release.
