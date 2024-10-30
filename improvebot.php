<?php

/**
 * Plugin Name:       Improve Bot
 * Description:       Bot by Krino
 * Version:           0.0.1
 * Author:            Krino
 * Author URI:        https://www.krino.cl
 * Text Domain:       krino
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * GitHub Plugin URI: https://github.com/2Fwebd/improve_bot-wordpress
 */

if (!defined('IMPROVE_BOT_PLUGIN_VERSION'))
    define('IMPROVE_BOT_PLUGIN_VERSION', '1.1.0');
if (!defined('IMPROVE_BOT_URL'))
    define('IMPROVE_BOT_URL', plugin_dir_url(__FILE__));
if (!defined('IMPROVE_BOT_PATH'))
    define('IMPROVE_BOT_PATH', plugin_dir_path(__FILE__));
if (!defined('IMPROVE_BOT_ENDPOINT'))
    define('IMPROVE_BOT_ENDPOINT', 'improvebot.krino.ai');
if (!defined('IMPROVE_BOT_PROTOCOL'))
    define('IMPROVE_BOT_PROTOCOL', 'https');




class ImproveBot
{

    private $_nonce = 'improve_bot_admin';
    private $option_name = 'improve_bot_data';

    public function __construct()
    {
        add_action('admin_menu',                array($this, 'addAdminMenu'));
        add_action('wp_ajax_store_admin_data',  array($this, 'storeAdminData'));
        add_action('admin_enqueue_scripts',     array($this, 'addAdminScripts'));

        add_action('wp_head',                   array($this, 'addHeadCode'));
        add_action('wp_footer',                 array($this, 'addFooterCode'));
    }

    public function addAdminMenu()
    {
        add_menu_page(
            'Improve Bot',
            'Improve Bot',
            'manage_options',
            'improve_bot',
            array($this, 'adminLayout'),
            'dashicons-testimonial'
        );
    }

    private function getData()
    {
        return get_option($this->option_name, array());
    }

    public function addAdminScripts()
    {

        // wp_enqueue_style('improve_bot-admin', IMPROVE_BOT_URL. 'assets/css/admin.css', false, 1.0);

        wp_enqueue_script('improve_bot-admin', IMPROVE_BOT_URL . 'assets/js/admin.js', array(), 1.0);

        $admin_options = array(
            'ajax_url' => admin_url('admin-ajax.php'),
            '_nonce'   => wp_create_nonce($this->_nonce),
        );

        wp_localize_script('improve_bot-admin', 'improve_bot_exchanger', $admin_options);
    }

    public function storeAdminData()
    {

        if (wp_verify_nonce(sanitize_text_field($_POST['security']), $this->_nonce) === false)
            die('Invalid Request! Reload your page please.');

        $data = $this->getData();
        foreach ($_POST as $field => $value) {
            $value = sanitize_text_field($value);
            if (substr($field, 0, 12) !== "improve_bot_")
                continue;

            if (empty($value))
                unset($data[$field]);

            // We remove the feedier_ prefix to clean things up
            $field = substr($field, 12);

            $data[$field] = $value;
        }

        update_option($this->option_name, $data);

        echo __('Saved!', 'feedier');
        die();
    }

    public function adminLayout()
    {
        $data = $this->getData();
?>
        <form id="improvebot-admin-form" class="postbox">
            <table class="form-table">
                <tbody>
                    <tr>
                        <td scope="row">
                            <label>BOT API KEY</label>
                        </td>
                        <td>
                            <input name="improve_bot_api_key" id="improve_bot_api_key" class="regular-text" type="text" value="<?php echo (isset($data['api_key'])) ? $data['api_key'] : ''; ?>" />
                        </td>
                    </tr>
                </tbody>
            </table>
            <hr>

            <div class="inside">

                <button class="button button-primary" id="improvebot-admin-save" type="submit">
                    Guardar
                </button>

            </div>
        </form>
    <?php
    }

    public function addHeadCode()
    {
        wp_enqueue_style('improve_bot-style', IMPROVE_BOT_PROTOCOL . '://' . IMPROVE_BOT_ENDPOINT . '/e4d798d154509684de5b69af7cb4e31396263913e6b1b0f6a7e4f218007006ac', false, 1.0);
    }

    public function addFooterCode()
    {
        $data = $this->getData();
        $bot = $this->getBotData((isset($data['api_key'])) ? $data['api_key'] : '');
        wp_enqueue_script('improve_bot-script', IMPROVE_BOT_URL . 'assets/js/iframe.js', array(), 1.0, true);

        $admin_options = array(
            'bot' => $bot == false ? "hola" : json_decode($bot)
        );

        wp_localize_script('improve_bot-script', 'improve_bot_data', $admin_options);
    ?>
        <div id="improve-bot"></div>
<?php
    }

    private function getBotData($api_key)
    {
        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => IMPROVE_BOT_PROTOCOL . "://" . IMPROVE_BOT_ENDPOINT . "/cc0b11b2fe9f3825e73cae4ab2f2f69a6686fe34e38e5ad4fa71a81efba2a9b9/$api_key/",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST"
        ));

        $response = curl_exec($curl);

        curl_close($curl);
        return $response;
    }
}

new ImproveBot();
?>