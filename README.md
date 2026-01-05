# roundcube-nextcloud-embed


Roundcube plugin for embedded operation in Nextcloud. This plugin accompanies the [Roundcube Mail Nextcloud app](https://github.com/rotdrop/nextcloud-roundcube/). When not running embedded, it does nothing.

## Operations

When the plugin detects that it runs embedded in Nextcloud (i.e. the top window is owned by Nextcloud), changes Roundcube's behaviour slightly:

- some styles are overwritten from Nextcloud's styles.
- when a session error occurs (e.g. timeout), Roundcube will not redirect to its own login page. Instead, the top window is reloaded, which triggers the Roundcube Mail App to re-authenticate.
- Some items declared as needless by the configuration are removed. The Roundcube Mail App already removes the Logout button (the showTopLine setting), but Dark and About buttons appear needless as well. YMMV.

IMHO adaption of Roundcube for embedded operation is much more flexible to implement as Roundcube plugin instead of handling this externally.


## Configuration

When embedded, the plugin will remove items as configured.
If the configuration `removeEmbeddedItem` is unset, the default `#taskmenu .special-buttons` is used, which removes the complete bottom-left special-buttons menu.


```
// Selector for items to be removed
$config['removeEmbeddedItem']="#taskmenu .special-buttons";
```