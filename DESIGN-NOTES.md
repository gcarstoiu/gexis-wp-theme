# Design notes

The handoff (*gexis WordPress theme*, Claude Design, October 2026) is high
fidelity: colours, type, spacing, radii and copy are final. This file lists
every place the theme departs from it, or had to choose where it was silent.

## Structure

- **Block theme, not the classic theme the handoff suggested.** The owner's
  call. The suggested PHP files map as follows: `front-page.php` →
  `templates/front-page.html` + `patterns/home.php`; `single.php` +
  `comments.php` → `patterns/single.php` (core Comments blocks);
  `tag.php`, `archive.php`, `author.php`, `page.php`, `search.php`,
  `404.php` → the template and pattern of the same name;
  `template-parts/logo.php` → `blocks/mark`; `ground.php` → `blocks/ground`;
  `card-large.php`, `row.php` → `inc/markup.php`; the comment walker →
  core Comment Template plus two filters in `inc/comments.php`.
- **The prototype's Template bar** is a review aid and is not built.

## Values

- **Two clamps differ between the handoff README and the prototype.** Home
  cover title: README `clamp(38px, 6vw, 58px)`, prototype
  `clamp(38px, 4.6vw, 58px)`. Single post H1: README `5.4vw`, prototype
  `4.2vw`. The theme uses the prototype's, as the visual reference.
- **The home cover title is an `<h1>`** (a `<span>` in the prototype), so the
  front page has a heading.
- **Reading time** is words ÷ 220, rounded, never under 1 min. The handoff
  shows the figure but not the rate.

## Behaviour

- **Mobile menu.** Core's Navigation block provides the Menu button and its
  script (the handoff allows script for the menu toggle). Core collapses at
  600px; CSS moves that to the design's 760px. The open menu is a card under
  the header as designed, but it **overlays** the page rather than pushing it
  down, because core positions it. It is opaque (`--bg-well`, which follows the mode)
  rather than `--fill-card`, which is translucent and unreadable over content.
- **Colour-mode switch** without script: hidden, and the page follows the OS
  setting through `prefers-color-scheme`.
- **Replying.** WordPress's `comment-reply.js` moves the form under the
  comment being answered, with "Replying to {name}" and Cancel above the
  textarea. The prototype keeps the form above the list.
- **Thread depth is fixed at 2** (`pre_option_thread_comments_depth`), as
  designed: replies sit under the comment they answer, and Reply on a reply
  answers the same thread.
- **Most discussed**: posts published in the last 90 days, by comment count,
  excluding posts already on the page. If fewer than three qualify, the column
  is topped up from all time rather than left short.
- **Tag archive**: 15 posts a page. On page 1 the first three render as large
  cards and the rest as rows; later pages are rows only. One Query Loop,
  styled by position.
- **Toasts** are drawn by CSS (2.4s, past tense) from a `?commented=` flag the
  theme adds to WordPress's redirect, so they need no script. "PDF downloaded"
  is raised by the one front-end script.
- **Avatar fallback.** Gravatars are requested with `d=blank` and sit over the
  initials circle (tint hashed exactly as `ArtistAvatar.jsx`), so a missing
  Gravatar shows the initials without the server checking.
- **Author badge** text comes from CSS (`content: "Author"`), so it is not
  translated.
- **Page intro.** The 22px intro is the first paragraph of the page body, not
  the excerpt, so it can be edited in place.
- **CV fields** are plain profile fields, one entry per line, rather than ACF
  repeaters, so the theme needs no plugin. The CV PDF is a URL from the Media
  Library rather than a media picker.

## Assets and speed

- **Fonts.** Nunito Sans and IBM Plex Mono were re-subset from the upstream
  OFL sources to Latin plus Latin Extended (the handoff's files lacked ă, ș,
  ț). Nunito Sans italic was added so emphasis is not synthesised.
  Bricolage Grotesque is subset to the five glyphs the mark uses
  (`g e i s x`): 2.8 KB instead of 205 KB.
- **Critical CSS.** The token files and both modes are inlined in `<head>`;
  the component styles are one stylesheet. The header and ground are not
  split out separately.
- **No emoji script, no jQuery, no icon font.** `comment-reply.js` loads only
  on singular pages with comments open.
