<?php

/**
 * Plugin Name: WA Rotator Grup by Pesan.link
 * Description: Plugins WA Rotator Grup Gratis
 * Version:     1.1.3
 * Plugin URI: https://jogjaitclinic.com/warotator-plugin
 * Author: Development Team Pesan.Link (Burhan-Rusdi)
 * Author URI:  https://pesan.link/
 * License:     GPLv2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: wpbc
 * Domain Path: /languages
 */

defined('ABSPATH') or die('¡Sin trampas!');
require plugin_dir_path(__FILE__) . 'includes/register_activation_plugin.php';
function rotatorinit()
{
    $f = "";
    foreach (ambil_data_url() as $row) {
        if (basename($_SERVER['REQUEST_URI']) === rawurlencode($row->url_ku)) {
            loading1();
            waUrl($row->id_url,$row->pixel_type);
            //wa_link_page($f,$row->pixel_type);
        }
    }
}

add_action('init', 'rotatorinit');
require plugin_dir_path(__FILE__) . 'includes/metabox-p1.php';
function wpbc_custom_admin_styles()
{
    wp_enqueue_style('custom-styles', plugins_url('/css/styles.css', __FILE__));
}
add_action('admin_enqueue_scripts', 'wpbc_custom_admin_styles');


function wpbc_plugin_load_textdomain()
{
    load_plugin_textdomain('wpbc', false, basename(dirname(__FILE__)) . '/languages');
}
add_action('plugins_loaded', 'wpbc_plugin_load_textdomain');


global $wpbc_db_version;
$wpbc_db_version = '1.1.0';


function wpbc_install()
{
    global $wpdb;
    global $wpbc_db_version;

    $table_name = $wpdb->prefix . 'datacs';

    $sql = "CREATE TABLE " . $table_name . " (
      id int(11) NOT NULL,
      name VARCHAR (50) NOT NULL,
      phone VARCHAR(15) NOT NULL,
      click int(11) NOT NULL,
      isdeeplink int(1) NOT NULL,
      pilgrup int (11) NOT NULL, 
      note text NOT NULL, 
      PRIMARY KEY  (id)
    );";
    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($sql);

    /*tam*/
    $table_name3 = $wpdb->prefix . 'urlku';
    $sql = "CREATE TABLE " . $table_name3 . " (
        id_url int(11) NOT NULL,
        url_ku VARCHAR (150) NOT NULL,
        pixel_type VARCHAR (100) NOT NULL,
        PRIMARY KEY  (id_url)
      );";
    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($sql);

    $table_name2 = $wpdb->prefix . 'data_performa';
    $sql = "CREATE TABLE " . $table_name2 . " (
        time DATE NOT NULL DEFAULT CURRENT_TIMESTAMP,
      phone VARCHAR(15) NOT NULL,
     // FOREIGN KEY (phone) REFERENCES " . $table_name . "(phone)
        INDEX `phone` (`phone`),
        CONSTRAINT {$table_name2} FOREIGN KEY (`phone`)
        REFERENCES {$table_name} (`id`) ON DELETE CASCADE ON UPDATE CASCADE
    );";
    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($sql);

    $sql = "CREATE TABLE ". $wpdb->prefix . "datawpfbpixel (id INT(1) NOT NULL, pixel_id VARCHAR(50) NOT NULL, enable int(1) NOT NULL, PRIMARY KEY(id));";
    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($sql);

    $sql2 = "INSERT INTO ". $wpdb->prefix . "datawpfbpixel VALUES ('1', '0', '0');";
    dbDelta($sql2);

    add_option('wpbc_db_version', $wpbc_db_version);
    $installed_ver = get_option('wpbc_db_version');
    if ($installed_ver != $wpbc_db_version) {
        $sql = "CREATE TABLE " . $table_name . " (
          id int(11) NOT NULL,
          name VARCHAR (50) NOT NULL,
          phone VARCHAR(15) NOT NULL,
          click int(11) NOT NULL,
          pilgrup int (11) NOT NULL,
          isdeeplink int(1) NOT NULL,
          PRIMARY KEY  (id), 
          ADD CONSTRAINT 'fkurl' FOREIGN KEY (`pilgrub`)
          REFERENCES {$table_name3} (`id_url`) ON DELETE CASCADE ON UPDATE CASCADE
        );";
        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql);

        $sql = "ALTER TABLE " . $table_name . "             
            ADD CONSTRAINT 'fkurl' FOREIGN KEY (`pilgrub`)
            REFERENCES {$table_name3} (`id_url`) ON DELETE CASCADE ON UPDATE CASCADE
          );";
        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql);

        $sql = "CREATE TABLE " . $table_name2 . " (
            time DATE NOT NULL DEFAULT CURRENT_TIMESTAMP,
          phone VARCHAR(15) NOT NULL,
          //FOREIGN KEY (phone) REFERENCES " . $table_name . "(phone)
            INDEX `phone` (`phone`),
            CONSTRAINT {$table_name2} FOREIGN KEY (`phone`)
            REFERENCES {$table_name} (`id`) ON DELETE CASCADE ON UPDATE CASCADE
        );";

        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql);

        $sql = "CREATE TABLE " . $table_name3 . " (
         id_url int(11) NOT NULL,
         url_ku VARCHAR (50) NOT NULL,
         PRIMARY KEY  (id_url)
         );";
        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql);

        update_option('wpbc_db_version', $wpbc_db_version);
    }
}

register_activation_hook(__FILE__, 'wpbc_install');


function wpbc_install_data()
{
    global $wpdb;

    $table_name = $wpdb->prefix . 'datacs';
    $table_name2 = $wpdb->prefix . 'data_performa';
    $table_name5 = $wpdb->prefix . 'urlku';
}

register_activation_hook(__FILE__, 'wpbc_install_data');


function wpbc_update_db_check()
{
    global $wpbc_db_version;
    if (get_site_option('wpbc_db_version') != $wpbc_db_version) {
        wpbc_install();
    }
}

add_action('plugins_loaded', 'wpbc_update_db_check');



if (!class_exists('WP_List_Table')) {
    require_once(ABSPATH . 'wp-admin/includes/class-wp-list-table.php');
}


class Custom_Table_Example_List_Table extends WP_List_Table
{
    function __construct()
    {
        global $status, $page;
        $o = " ";
        parent::__construct(array(
            'singular' => 'contact',
            'plural'   => 'contacts',
        ));
        wpbc_page_handler_link($o);
    }

    function column_default($item, $column_name)
    {
        return $item[$column_name];
    }


    function column_phone($item)
    {
        return '<em>' . $item['phone'] . '</em>';
    }

    function column_id($item)
    {
        return '</em>' . $item['id'] . '</em>';
    }

    function column_click($item)
    {
        return '</em>' . $item['click'] . '</em>';
    }

    function column_pilgrup($item)
    {

        return  '</em>' . $item['pilgrup'] . '</em>';
    }

    function column_default1($item, $column_urlgrup)
    {
        return $item[$column_urlgrup];
    }

    function column_urlgrup($item)
    {

        $actions = array(
            'pilih' => sprintf('
<a id="%s" class="copy_grup_text" href="#" onclick="copy_url_text()"> Copy URL</a> <script type="text/javascript">function copy_url_text(){navigator.clipboard.writeText(event.srcElement.id); alert("URL telah disalin keclipboard");}</script>', $item['urlgrup'], __('Copy Grup ', 'wpbc')),

        );

        return sprintf(
            '%s %s',
            $item['urlgrup'],
            $this->row_actions($actions)
        );
    }

    function column_name($item)
    {

        $actions = array(
            'edit' => sprintf('<a href="?page=ubahs_form&phone=%s&id=%s&pilgrup=%s">%s</a>', $item['phone'], $item['id'], $item['pilgrup'], __('Edit', 'wpbc')),
            'delete' => sprintf('<a href="?page=%s&action=delete&id=%s">%s</a>', $_REQUEST['page'], $item['id'], __('Delete', 'wpbc')),

        );

        return sprintf(
            '%s %s',
            $item['name'],
            $this->row_actions($actions)
        );
    }


    function column_cb($item)
    {
        return sprintf(
            '<input type="checkbox" name="id[]" value="%s" />',
            $item['id']
        );
    }

    function get_columns()
    {
        $columns = array(
            'cb' => '<input type="checkbox" />',
            'name'          => __('Name', 'wpbc'),
            'phone'         => __('Phone', 'wpbc'),
            'deeplinkstr'    => __('Tipe URL', 'wpbc'),
            'click'         => __('Hits', 'wpbc'),
            'urlgrup'       => __('URL Grub', 'wpbc'),
        );
        return $columns;
    }

    function get_sortable_columns()
    {
        $sortable_columns = array(
            'id'        => array('id', true),
            'name'          => __('Name', 'wpbc'),
            'phone'         => __('Phone', 'wpbc'),
            'deeplinkstr'    => __('Tipe URL', 'wpbc'),
            'click'         => __('Hits', 'wpbc'),
            'urlgrup'       => __('URL Grub', 'wpbc'),
        );
        return $sortable_columns;
    }

    function get_bulk_actions()
    {
        $actions = array(
            'delete' => 'Delete', 'pilih' => 'Pilih'
        );
        return $actions;
    }


    function process_bulk_action()
    {
        global $wpdb;
        $table_name = $wpdb->prefix . 'datacs';

        if ('delete' === $this->current_action()) {
            $ids = isset($_REQUEST['id']) ? $_REQUEST['id'] : array();

            $wpdb->query("DELETE FROM $table_name WHERE id IN($ids) ");
        }

        if ('pilih' === $this->current_action()) {
            $ids = isset($_REQUEST['urlgrup']) ? $_REQUEST['urlgrup'] : array();
?>
            <h4>Wa Rotator</h4>
            <input type="text" value="<?php echo $ids ?> " id="pilih" name="pilih" readonly />
            <button type="button" onclick="copy_text()">Copy</button>
            

    <?php

        }
    }

    #tampilan
    function prepare_items()
    {
        global $wpdb;
        $table_name = $wpdb->prefix . 'datacs';
        $table_name2 = $wpdb->prefix . 'urlku';

        $per_page = 10;

        $columns = $this->get_columns();
        $hidden = array();
        $sortable = $this->get_sortable_columns();

        $this->_column_headers = array($columns, $hidden, $sortable);

        $this->process_bulk_action();

        $total_items = $wpdb->get_var("SELECT COUNT(id) FROM $table_name");

        $paged = isset($_REQUEST['paged']) ? max(0, intval($_REQUEST['paged']) - 1) : 0;
        $orderby = (isset($_REQUEST['orderby']) && in_array($_REQUEST['orderby'], array_keys($this->get_sortable_columns()))) ? $_REQUEST['orderby'] : 'name';
        $order = (isset($_REQUEST['order']) && in_array($_REQUEST['order'], array('asc', 'desc'))) ? $_REQUEST['order'] : 'asc';


        $situs = get_site_url();
        $this->items = $wpdb->get_results($wpdb->prepare("SELECT datacs.id, datacs.name, datacs.phone,IF(datacs.isdeeplink<0, 'Whatsapp Deeplink', 'Whatsapp API URL') as deeplinkstr, datacs.click, datacs.pilgrup,  CONCAT('$situs','/', urlku.url_ku) as urlgrup 
                            FROM $table_name as datacs,  $table_name2 as urlku 
                            where datacs.pilgrup = urlku.id_url ORDER BY $orderby $order LIMIT %d OFFSET %d", $per_page, $paged), ARRAY_A);

        $this->set_pagination_args(array(
            'total_items' => $total_items,
            'per_page' => $per_page,
            'total_pages' => ceil($total_items / $per_page)
        ));
    }
}

//require plugin_dir_path(__FILE__) . 'includes/hapusgrup.php';

function wpbc_admin_menu()
{
    add_menu_page(__('WA Rotator', 'wpbc'), __('WA Rotator', 'wpbc'), 'activate_plugins', 'contacts', 'wpbc_contacts_page_handler');
    add_submenu_page('contacts', __('Daftar Kontak', 'wpbc'), __('Daftar Kontak ', 'wpbc'), 'activate_plugins', 'contacts', 'wpbc_contacts_page_handler');
    add_submenu_page('contacts', __('Tambah Kontak', 'wpbc'), __('New Contact', 'wpbc'), 'activate_plugins', 'contacts_form', 'wpbc_contacts_form_page_handler');
    add_submenu_page('contacts', __('Group Link', 'wpbc'), __('Group Link', 'wpbc'), 'activate_plugins', 'urls_form', 'wpbc_urls_form_page_handler');
    add_submenu_page('contacts', __('Setting', 'wpbc'), __('Setting', 'wpbc'), 'activate_plugins', 'setting_form', 'wpbc_setting_form_page_handler');
    add_submenu_page('contacts', __('Edit Kontak', 'wpbc'), __('', 'wpbc'), 'activate_plugins', 'ubahs_form', 'wpbc_ubahs_form_page_handler');

}


function wpbc_admin_menu_activation()
{
    add_menu_page(__('WA Rotator', 'wpbc'), __('WA Rotator', 'wpbc'), 'activate_plugins', 'activation_wa','wp_register_activation_register_wa');
    add_submenu_page('activation_wa', __('Url', 'wpbc'), __('Activation', 'wpbc'), 'activate_plugins', 'activation_wa', 'wp_register_activation_register_wa');
    add_submenu_page('activation_wa', __('Add new', 'wpbc'), __('', 'wpbc'), 'activate_plugins', 'activation_wa_register', 'wp_register_activation_register_wa');
}

function check_registration_status_wa(){
    if(wp_register_activation_check_wa()){
        wpbc_admin_menu();
    }else{
        wpbc_admin_menu_activation();
    }
}
add_action('admin_menu', 'check_registration_status_wa');


function wpbc_validate_contact($item)
{
    $messages = array();

    if (empty($item['name'])) $messages[] = __('Name is required', 'wpbc');
    if (!empty($item['phone']) && !absint(intval($item['phone'])))  $messages[] = __('Phone can not be less than zero');
    if (!empty($item['phone']) && !preg_match('/[0-9]+/', $item['phone'])) $messages[] = __('Phone must be number');


    if (empty($messages)) return true;
    return implode('<br />', $messages);
}

function wpbc_validate_link($item1)
{
    $messages = array();
    global $wpdb;
    $table_name3 = $wpdb->prefix . 'urlku';
    if (empty($item1['url_ku'])) $messages[] = __('Grup Name is required', 'wpbc');

    if (empty($messages)) return true;
    return implode('<br />', $messages);
}

function wpbc_validate_ubah($item1)
{
    $messages = array();

    if (empty($item1['phone'])) $messages[] = __('Phone is required', 'wpbc');

    if (empty($messages)) return true;
    return implode('<br />', $messages);
}

function wpbc_validate_setting($item1)
{
    $messages = array();
    if (empty($item1['pixel_id']) && $item1['enable']===1) $messages[] = __('Pixel ID is required', 'wpbc');
    if (empty($messages)) return true;
    return implode('<br />', $messages);
}

function wpbc_languages()
{
    load_plugin_textdomain('wpbc', false, dirname(plugin_basename(__FILE__)));
}

add_action('init', 'wpbc_languages');


function wpbc_page_handler_link($g)
{

    ?>
    <html>

    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width">
        <title>Copy Clipboard</title>
        <style type="text/css">
            h4 {
                font-size: 25px;
            }

            input[type="text"],
            button[type="button"] {
                padding: 10px 15px;
                font-size: 16px;
                border-radius: 5px;
            }

            input[type="text"] {
                width: 330px;
                border: 1px solid #bbb;
            }

            button[type="button"] {
                background: #A9A9A9;
                border: 1px solid #A9A9A9;
                color: #fff;
                cursor: pointer;
            }
        </style>
    </head>

    <body>

    </body>

    </html>
    <script type="text/javascript">
        function copy_text() {
            document.getElementById("pilih").select();
            document.execCommand("copy");
            alert("Text berhasil dicopy");
        }
    </script>
    <br> <br>
<?php
}

add_shortcode('whatsup-plugin', 'wa_link_page');

function wa_link_page($f,$pt,$e,$pix_id)
{
    global $wpdb;
    do_action( 'wp_head' );
    if($e==1){
        echo "<script>!function(f,b,e,v,n,t,s){if(f.fbq)return;n=f.fbq=function(){n.callMethod?n.callMethod.apply(n,arguments):n.queue.push(arguments)};if(!f._fbq)f._fbq=n;n.push=n;n.loaded=!0;n.version='2.0';n.queue=[];t=b.createElement(e);t.async=!0;t.src=v;s=b.getElementsByTagName(e)[0];s.parentNode.insertBefore(t,s)}(window, document,'script','https://connect.facebook.net/en_US/fbevents.js');fbq('init', '".$pix_id."');</script><noscript><img height='1' width='1' style='display:none'src='https://www.facebook.com/tr?id=".$pix_id."&ev=PageView&noscript=1'/></noscript>";
    }

    
    

    echo '<script type="text/javascript">fbq("track", "'.$pt.'");</script>';
    echo  "<meta content=1;url=" . $f . " http-equiv='refresh' />";
    exit;
}

add_shortcode('data_statistik', 'wpbc_page_handler_data');

function wpbc_page_handler_data()
{

    global $wpdb;

    $results = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}datacs as datacs, {$wpdb->prefix}data_performa as data_performa,
    {$wpdb->prefix}urlku as urlku
    ON datacs.id = data_performa.id AND urlku.id_url = datacs.pilgrup
    ORDER BY data_performa.time DESC");
    //  table_users($results);
    return $results;
}


function waUrl($f,$pt)
{
    global $konten;
    global $nilai;
    global $wpdb;

    $p = $f;

    $user_count = $wpdb->get_var(
        "SELECT COUNT(*) FROM {$wpdb->prefix}datacs where pilgrup = $p"
    );

    $mulai = $wpdb->get_var(
        "SELECT min(id) FROM {$wpdb->prefix}datacs where pilgrup = $p"
    );
    $selesai = $wpdb->get_var(
        "SELECT max(id) FROM {$wpdb->prefix}datacs where pilgrup = $p"
    );


    //jumlah total akun user
    $total = $user_count;

    //dataklik
    $namaFile = 'data1.txt';

    $nilai = file_get_contents($namaFile);

    if ($nilai >= $selesai) {
        $konten = $mulai;
    } else if ($nilai < $mulai) {
        $konten = $mulai;
    } else {
        $konten = $nilai + 1;
    }

    $file = fopen($namaFile, 'w');
    fwrite($file, $konten);
    fclose($file);


    $id = $konten;

    $kontak = $wpdb->get_var(
        $wpdb->prepare("SELECT phone from {$wpdb->prefix}datacs where pilgrup = $p AND id = %d", $id)
    );

    $pesan = $wpdb->get_var(
        $wpdb->prepare("SELECT note from {$wpdb->prefix}datacs where pilgrup = $p AND id = %d", $id)
    );

    $click = $wpdb->get_var(
        $wpdb->prepare("SELECT click from {$wpdb->prefix}datacs where pilgrup = $p AND id = %d", $id)
    );

    $isdeeplink = $wpdb->get_var(
        $wpdb->prepare("SELECT isdeeplink from {$wpdb->prefix}datacs where pilgrup = $p AND id = %d", $id)
    );

    $e = $wpdb->get_var(
        $wpdb->prepare("SELECT enable from {$wpdb->prefix}datawpfbpixel where id = '1'")
    );

    $pix_id = $wpdb->get_var(
        $wpdb->prepare("SELECT pixel_id from {$wpdb->prefix}datawpfbpixel where id = '1'")
    );

    $data_performa = $wpdb->prefix . 'data_performa';
    $item['phone'] = $kontak;
    $result = $wpdb->insert($data_performa, $item);

    $wpdb->query("UPDATE {$wpdb->prefix}datacs SET `click` = ($click + 1) WHERE `phone` = '$kontak' AND pilgrup = $p");

    $text_encode = urlencode("$pesan");
    

    if($isdeeplink){
        $r = 'whatsapp://send?phone=' . $kontak . '&text=' . $text_encode;
    }else{
        $r = 'https://api.whatsapp.com/send?phone=' . $kontak . '&text=' . $text_encode;
    }
    wa_link_page($r,$pt,$e,$pix_id);
}


function loading1()
{
?>

    <!DOCTYPE html>
    <html lang="en">

    <head>
        <meta charset="UTF-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Document</title>
    </head>

    <body>
        <style>
            .container {
                height: 200px;
                position: relative;
            }

            .vertical-center {
                margin: 0;
                position: absolute;
                top: 50%;
                -ms-transform: translateY(-50%);
                transform: translateY(-50%);
            }
        </style>

        <div class="container">
            <div class="vertical-center">

            </div>
        </div>

        <div style="margin:0 auto; justify-content: center; align-items: center; display: flex; ">

            <div class="loadingio-spinner-bars-aettok4qpsb">
                <div class="ldio-vrxw6qn0iq">
                    <div></div>
                    <div></div>
                    <div></div>
                    <div></div>
                </div>
            </div>
            <style type="text/css">
                @keyframes ldio-vrxw6qn0iq {
                    0% {
                        opacity: 1
                    }

                    50% {
                        opacity: .5
                    }

                    100% {
                        opacity: 1
                    }
                }

                .ldio-vrxw6qn0iq div {
                    position: absolute;
                    width: 10px;
                    height: 40px;
                    top: 60px;
                    animation: ldio-vrxw6qn0iq 1s cubic-bezier(0.5, 0, 0.5, 1) infinite;
                }

                .ldio-vrxw6qn0iq div:nth-child(1) {
                    transform: translate(30px, 0);
                    background: #157759;
                    animation-delay: -0.6s;
                }

                .ldio-vrxw6qn0iq div:nth-child(2) {
                    transform: translate(70px, 0);
                    background: #53ab8b;
                    animation-delay: -0.4s;
                }

                .ldio-vrxw6qn0iq div:nth-child(3) {
                    transform: translate(110px, 0);
                    background: #82dbb8;
                    animation-delay: -0.2s;
                }

                .ldio-vrxw6qn0iq div:nth-child(4) {
                    transform: translate(150px, 0);
                    background: #a2fdd9;
                    animation-delay: -1s;
                }

                .loadingio-spinner-bars-aettok4qpsb {
                    width: 200px;
                    height: 200px;
                    display: inline-block;
                    overflow: hidden;
                    background: none;
                }

                .ldio-vrxw6qn0iq {
                    width: 100%;
                    height: 100%;
                    position: relative;
                    transform: translateZ(0) scale(1);
                    backface-visibility: hidden;
                    transform-origin: 0 0;
                    /* see note above */
                }

                .ldio-vrxw6qn0iq div {
                    box-sizing: content-box;
                }

                /* generated by https://loading.io/ */
            </style>

        </div>

        <div style="margin:0 auto; justify-content: center; align-items: center; display: flex; ">
            <h3>loading...</h3>
        </div>



    </body>
    <?php

    ?>

    </html>

<?php
}
