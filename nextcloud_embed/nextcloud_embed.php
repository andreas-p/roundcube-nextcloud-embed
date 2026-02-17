<?php

/*
 * Inject modified session_error function
 * and modify CSS to visually fit into Nextcloud frame.
*/


class nextcloud_embed extends rcube_plugin
{
    public const PLUGIN_VERSION = 'v1.6.0';
    const SESSION_ID="nc_session_id";
    
    public static function info(): array
    {
        return [
            'name' => 'navigator_identity',
            'vendor' => 'PSE Consulting Andreas Pflug',
            'version' => self::PLUGIN_VERSION,
            'license' => '© 2026 PSE Consulting',
        ];
    }
    public function init()
    {
        $rcube=rcube::get_instance();
        $removeEmbeddedItem=$rcube->config->get('removeEmbeddedItem', "#taskmenu .special-buttons");
        $rcube->output->set_env('removeEmbeddedItem', $removeEmbeddedItem);
        $this->include_script('nextcloud_embed.js');
        $this->include_stylesheet('nextcloud_embed.css');
        
        
        if ($rcube->task == 'login')
        {
            $this->add_hook('startup', [$this, 'startup']);
            $this->add_hook('authenticate', [$this, 'authenticate']);
        }
        elseif ($rcube->task == 'logout')
        {
            $this->add_hook('session_destroy', [$this, 'session_destroy']);
            $this->add_hook('logout_after', [$this, 'logout_after']);
        }
    }
  
    public function startup($args)
    {
        // Skip login form
        if (!$args['action']) $args['action'] = "login";
        return $args;
    }
    
    public function authenticate($args)
    {
        // Get 
        $config=rcube::get_instance()->config;

        $host= $_SERVER['REQUEST_SCHEME'] . "://" . $_SERVER['HTTP_HOST'] . $config->get('navigator_base', '');
        $client="Roundcube";
        
        $cookies=[];
        foreach ($_COOKIE as $key => $value)
            $cookies[] = "$key=$value";
        
        $ch=curl_init("$host/apps/pse/getMailAccess?clientName=$client");

        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_COOKIE, join('; ', $cookies));
        ob_start();
        $rc=curl_exec($ch);
        $result=ob_get_clean();
        if ($rc === true)
        {
            $data=json_decode($result, true);
            $args['user'] = $data['username'];
            $args['pass'] = $data['password'];
            if (isset($data['id']))
                $_SESSION[self::SESSION_ID]=$data['id'];
        }
        return $args;
    }
    
    public function session_destroy($args)
    {
        if (isset($_SESSION[self::SESSION_ID]))
        {
            $db=rcube::get_instance()->get_dbh();
            $db->query("DELETE FROM oc_authtoken WHERE id=?", $_SESSION[self::SESSION_ID]);
            unset($_SESSION[self::SESSION_ID]);
        }
        return $args;
    }
    public function logout_after($args)
    {
        $_SESSION['username'] = "DUMMY";
        return $args;
    }
}