# gexis — a WordPress block theme

The theme for **gexis**, a personal site in four parts. The four letters of the
gexis mark are ordinary WordPress **tags**, not sections, and a post can carry
several:

| Letter | Tag slug | Tile | Covers |
|---|---|---|---|
| **g** | `gazette` | teal `#7ed6bc` | Articles on topics of interest |
| **e** | `engine-room` | amber `#e0a758` | Homelab and tech tinkering |
| **i** | `inbox` | periwinkle `#9fb4e8` | Email templates, email client styling |
| **s** | `sound` | coral `#f2a48f` | The gexis DAC player |

The theme recognises those four slugs and gives each its colour, and lights its
letter of the mark on its archive. Any other tag renders neutral.

Built from the gexis design system: a blurred diagonal weave behind every page,
Nunito Sans for anything spoken and IBM Plex Mono for anything measured, dark
and light modes, native threaded comments with avatars, and a CV-style author
page.

![The front page in dark mode](screenshot.jpg)

## Install

Clone this repository into `wp-content/themes/gexis`, or build a zip with
`dev/build-zip.sh` and upload `dist/gexis.zip` under **Appearance → Themes →
Add New → Upload Theme**. The folder must be named `gexis`.

Requires WordPress 6.6 and PHP 7.4 or later.

## Setting it up

1. **Tags.** Create the tags `gazette`, `engine-room`, `inbox` and `sound`
   (Posts → Tags). Their descriptions appear on the tag cards; if one is empty a
   default line is used.
2. **Featured images** drive the look: the cover, the cards, the rows, and the
   blurred ground behind each page all come from them.
3. **About page.** Create a page with the slug `about` and insert the
   *About: the four tags* pattern (category *gexis*).
4. **Author page.** Fill in *Author page (gexis)* at the bottom of your user
   profile: location, lede, now, summary, links, work, skills, facts and a CV
   PDF URL. Repeating fields take one entry per line; the help text under each
   field shows the format.
5. **Customizer → gexis.** Set the default background image (used behind pages
   with no image of their own) and, optionally, suggested searches.

The *Archive* link goes to the posts page if you have set one (Settings →
Reading); otherwise to `/archive/`, which the theme provides.

## How it is built

A block theme: templates, parts and patterns are block markup built from core
blocks, editable in the Site Editor. PHP is used only for what core blocks
cannot do, and each file under `inc/` opens by saying what that is.

```
theme.json             palette, type, spacing — the design tokens as presets
style.css              header + component styles
templates/  parts/     thin: header part, one pattern, footer part
patterns/              the markup of each template, translatable
blocks/                twelve small server-rendered gexis/* blocks
inc/                   tags, query roles, comments, routes, customizer, CV
assets/css/ds/         the design system's token files, verbatim
assets/fonts/          self-hosted woff2, SIL OFL
dev/                   a throwaway Docker WordPress and a seed (not shipped)
```

The `gexis/*` blocks exist because core has no equivalent: the mark, the
blurred ground, the mono meta line (`28 Sep 2026 · 7 min · 12 comments`),
older/newer rows with thumbnails, posts grouped by month, the post count, tag
chips and cards, the CV, the search states, the `Newer · 1 / 4 · Older` pager
and the colour-mode switch. They have no build step: one plain script gives
them all a server-side preview and sidebar controls in the editor.

See [DESIGN-NOTES.md](DESIGN-NOTES.md) for where the implementation departs
from the design handoff, and why.

## Development

```sh
dev/docker.sh up     # WordPress on http://localhost:8088 (admin / admin), seeded
dev/docker.sh wp …   # run WP-CLI against it
dev/docker.sh down   # remove it
```

It creates its own network, database and site, all named `gexis-dev-*`, and
mounts this directory read-only. The seed recreates the design prototype's
sample posts, comments and CV, with generated placeholder images.

Release zip: `dev/build-zip.sh` writes `dist/gexis.zip` (without `dev/`).

## Licence

GPL-3.0-or-later; see [LICENSE](LICENSE). Bundled fonts are under the SIL Open
Font License 1.1; see `assets/fonts/` and `readme.txt`.
