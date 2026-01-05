<?php

/*
 * Inject modified session_error function
 * and modify CSS to visually fit into Nextcloud frame.
*/


class nextcloud_embed extends rcube_plugin
{
  public function init()
  {
    $rcube=rcube::get_instance();
    $removeEmbeddedItem=$rcube->config->get('removeEmbeddedItem', "#taskmenu .special-buttons");
    $rcube->output->set_env('removeEmbeddedItem', $removeEmbeddedItem);
    $this->include_script('nextcloud_embed.js');
  }
}