
-- SUMMARY --

Add This module provides Drupal integration to addthis.com link sharing service.
Integration has been implemented as a field.

Description from addthis.com: 
The AddThis button spreads your content across the Web by making it easier for
your visitors to bookmark and share it with other people, again... and again...
and again. This simple yet powerful button is very easy to install and provides
valuable Analytics about the bookmarking and sharing activity of your users.
AddThis helps your visitors create a buzz for your site and increase its
popularity and ranking.

AddThis is already on hundreds of thousands of websites including SAP,
TIME Magazine, Oracle, Freewebs, Entertainment Weekly, Topix, Lonely Planet,
MapQuest, MySpace, PGA Tour, Tower Records, Squidoo, Zappos, Funny or Die, FOX,
ABC, CBS, Glamour, PostSecret, WebMD, American Idol, and ReadWriteWeb,
just to name a few. Each month our button is displayed 20 billion times.

-- REQUIREMENTS --

Field, Block

-- INSTALLATION --

Normal Drupal module installation, see http://drupal.org/node/70151 for further
information.

For link sharing statistics registration at http://addthis.com/ is required, but
the module will work even without registration.

-- INCLUDED MODULES --
1. **addthis** - Provides the base API to integrate with AddThis. Also creates RenderElements,
base Twig templates and global admin settings for AddThis. Does not provide any
rendering functionality on its own.
2. **addthis_block** - Provides a configurable block for displaying AddThis.
3. **addthis_fields** - Provides two field formatters for AddThis to allow for rendering on
entities.

-- CONFIGURATION --

Use the admin configuration page to configure settings and see http://drupal.org/node/1309922
for a walkthrough on how to configure the rest.

-- DEVELOPMENT --

Please see the addthis.api.php for implementation options of different displays
and altering configuration on rendering.

-- CONTACT --

Current D8 contributors
* John Doyle (doylejd) - https://www.drupal.org/u/doylejd

Current D7 maintainers:
* Vesa Palmu (wesku) - http://drupal.org/user/75070
* Jani Palsamäki (janip) - http://drupal.org/user/1356218
* Matthias Glastra (matglas86) - http://drupal.org/user/573464

Major contributions by:
Lesmana Lim (lesmana) - http://drupal.org/user/84263
