# Design notes

The handoff (*gexis WordPress theme*, Claude Design, October 2026) is high
fidelity: colours, type, spacing, radii and copy are final. This file lists
every place the theme departs from it, or had to choose where it was silent.

## Structure

- **1.1: the "Front page" direction on every page** (George chose it from
  the home-page mockups on 2026-10-24; the mockups are a canvas, not part of
  the handoff). What it changes from the handoff: every page opens with one
  large card - the lead post's or the post's own picture with its title on
  it, or a head card - and lists are cards on a 12-column grid with 210px
  rows. The ground (weave, blurred image, veil) stays behind it all.
- **Bento cells are sized in PHP** (`inc/bento.php`). Core's Post Template
  gives every item the same markup and cannot size one item differently from
  the next, so a filter on its output gives each `<li>` its step of a named
  cycle (`--span`, `--rows`, and a look: picture with the text on it, picture
  with the text under it, or a text card). A post with no image is always a
  text card, so the grid never shows an empty picture.

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

- **Navigation is About me · Archive · This site · Search**, not the
  handoff's Archive · About · Search (owner's request, after launch). About me
  opens the owner's author page, the CV, so the person behind the site is one
  tap from anywhere. The handoff's About page describes the site — its four
  tags, comments, feeds — so it is renamed This site (slug `site`). The
  footer carries both. The owner is the first administrator, or the user
  chosen under Customizer → gexis → Owner.

## Values

- **Two clamps differ between the handoff README and the prototype.** Home
  cover title: README `clamp(38px, 6vw, 58px)`, prototype
  `clamp(38px, 4.6vw, 58px)`. Single post H1: README `5.4vw`, prototype
  `4.2vw`. The theme used the prototype's, as the visual reference, until
  1.1; the picture headers now use `clamp(40px, 6.4vw, 88px)`, as in the
  Front page mockup.
- **The home page's lead title is an `<h1>`** (a `<span>` in the prototype), so the
  front page has a heading.
- **Images in a post keep their own shape.** The handoff's figure is 16:10,
  but cropping every body image to that cut wide screenshots (bar displays)
  down to their middle. 16:10 is still one click away on the image block.
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
- **Guides** (1.0.4, in place of *Most discussed*; George, 2026-10-19; a
  card in the home grid since 1.1): lists the pages that show a repository document
  (Installation guide, User manual, FAQ, Hardware, Release notes), in each
  page's Order, then by title, up to five, as text rows - title and excerpt,
  no thumbnail. With few comments,
  *Most discussed* was an arbitrary pick.
- **Tag archive**: 14 posts a page, two cycles of the tag grid (a wide
  picture, a tall one, two text cards, a picture, two text cards). The
  head's line is the tag's description, or the theme's line for it when the
  description is empty (a filter on core/term-description).
- **On this page** lists a post's level-2 headings when there are at least
  two; the headings get ids from their text as the post renders. Search
  marks the words searched for in result titles and excerpts, and *By tag*
  narrows the search with WordPress's own `tag` query variable.
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
