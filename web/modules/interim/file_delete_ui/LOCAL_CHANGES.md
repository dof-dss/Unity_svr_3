# Interim File delete module

Copied from Corp Lite's tracked interim fork of
https://git.drupalcode.org/project/file_delete_ui at commit
`56a1b3ac1676b2df5812f9d65911344b2dc7d75a`.

The upstream source is unchanged. Unity 3 has already removed the Composer
requirement and exported module enablement. This copy makes the module code
available for sites whose active configuration still enables it, so the existing
Unity Common retirement update can uninstall it through Drupal.

When returning to a suitable upstream Composer release, remove this interim
copy in the same change to avoid duplicate module discovery.
