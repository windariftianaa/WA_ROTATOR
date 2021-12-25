<?php
function wpbc_contacts_page_handler()
{
    global $wpdb;

    $table = new Custom_Table_Example_List_Table();
    $table->prepare_items();

    $message = '';
    if ('delete' === $table->current_action()) {
        $message = '<div class="updated below-h2" id="message"><p>'
            . sprintf(__('Items deleted: %d', 'wpbc'), count((array)$_REQUEST['id'])) . '</p></div>';
    }
?>
    <div class="wrap">

        <div class="icon32 icon32-posts-post" id="icon-edit"><br></div>
        <h2>
            <?php _e('Contacts', 'wpbc') ?>
            <a class="add-new-h2" href="<?php echo get_admin_url(
                                            get_current_blog_id(),
                                            'admin.php?page=contacts_form'
                                        ); ?>">
                <?php _e('Add New Kontak', 'wpbc') ?>
            </a>

            <a class="add-new-h2" href="<?php echo get_admin_url(
                                            get_current_blog_id(),
                                            'admin.php?page=urls_form'
                                        ); ?>">
                <?php _e('Add New Grub', 'wpbc') ?>
            </a>
        </h2>

        <?php echo $message; ?>

        <form id="contacts-table" method="POST">
            <input type="hidden" name="page" value="<?php echo $_REQUEST['page'] ?>" />
            <?php $table->display() ?>

        </form>

    </div>
<?php
}

function wpbc_contacts_form_page_handler()
{
    global $wpdb;
    $table_name = $wpdb->prefix . 'datacs';

    $message = '';
    $notice = '';

    $default = array(
        'id'        => '',
        'name'      => '',
        'phone'     => '',
        'pilgrup'   => '',
        'note'      => '',
    );


    if (isset($_REQUEST['nonce']) && wp_verify_nonce($_REQUEST['nonce'], basename(__FILE__))) {

        $item = shortcode_atts($default, $_REQUEST);

        $item_valid = wpbc_validate_contact($item);

        if ($item_valid === true) {
            if ($item['id'] == '') {

                $user_count = $wpdb->get_var(
                    "SELECT max(id) FROM {$wpdb->prefix}datacs "
                );

                $id = $user_count + 1;
                $item['id'] = $id;

                $result = $wpdb->insert($table_name, $item);

                if ($result) {
                    $message = __('Item was successfully saved', 'wpbc');
                } else {
                    $notice = __('There was an error while saving item', 'wpbc');
                }
            } else {
                $result = $wpdb->update($table_name, $item, array('pilgrup' => $item['pilgrup'], 'id' => $item['id']));
                if ($result) {
                    $message = __('Item was successfully updated', 'wpbc');
                } else {
                    $notice = __('There was an error while updating item', 'wpbc');
                }
            }
        } else {
            $notice = $item_valid;
        }
    } else {

        $item = $default;
        if (isset($_REQUEST['id'])) {
            $item = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table_name WHERE 
            id = %s", $_REQUEST['id']), ARRAY_A);
            if (!$item) {
                $item = $default;
                $notice = __('Item not found', 'wpbc');
            }
        }
    }

    add_meta_box(
        'contacts_form_meta_box',
        __('Contact data', 'wpbc'),
        'wpbc_contacts_form_meta_box_handler',
        'contact',
        'normal',
        'default'
    );

?>
    <div class="wrap">
        <div class="icon32 icon32-posts-post" id="icon-edit"><br></div>
        <h2><?php _e('Contact', 'wpbc') ?> <a class="add-new-h2" href="<?php echo get_admin_url(get_current_blog_id(), 'admin.php?page=contacts'); ?>">
                <?php _e('back to list', 'wpbc') ?></a>
            <a class="add-new-h2" href="<?php echo get_admin_url(
                                            get_current_blog_id(),
                                            'admin.php?page=urls_form'
                                        ); ?>">
                <?php _e('Add New Grub', 'wpbc') ?>
            </a>
        </h2>

        <?php if (!empty($notice)) : ?>
            <div id="notice" class="error">
                <p><?php echo $notice ?></p>
            </div>
        <?php endif; ?>
        <?php if (!empty($message)) : ?>
            <div id="message" class="updated">
                <p><?php echo $message ?></p>
            </div>

        <?php endif; ?>
        <form id="form" method="POST">
            <input type="hidden" name="nonce" value="<?php echo wp_create_nonce(basename(__FILE__)) ?>" />

            <div class="metabox-holder" id="poststuff">
                <div id="post-body">
                    <div id="post-body-content">

                        <?php do_meta_boxes('contact', 'normal', $item); ?>
                        <input type="submit" value="<?php _e('Save', 'wpbc') ?>" id="submit" class="button-primary" name="submit">
                    </div>
                </div>
            </div>

        </form>
    </div>

<?php
}

function wpbc_contacts_form_meta_box_handler($item)
{
?>
    <tbody>

        <div class="formdatabc">

            <form>

                <div class="form2bc">
                    <p>
                        <label for="name"><?php _e('Name :', 'wpbc') ?></label>
                        <br>
                        <input id="name" name="name" type="text" placeholder="Name " value="<?php echo esc_attr($item['name']) ?>" required>
                    </p>
                </div>

                <div class="form2bc">
                    <p>
                        <label for="phone"><?php _e('Phone :', 'wpbc') ?></label>
                        <br>
                        <input id="phone" name="phone" type="tel" placeholder="6281225853777 " value="<?php echo esc_attr($item['phone']) ?>">
                    </p>
                </div>

                <div class="form2bc">
                    <p>
                        <label for="note"><?php _e('Note :', 'wpbc') ?></label>
                        <br>
                        <input id="note" name="note" type="tel" value="<?php echo esc_attr($item['note']) ?>" placeholder="Saya ingin memesan ">
                    </p>
                </div>
                <div class="form2bc">
                    <p>
                        <label for="pilgrup"><?php _e('Pilih Grub :', 'wpbc') ?></label>
                        <br>

                        <select id="pilgrup" name="pilgrup">
                            <?php foreach (ambil_data_url() as $row) { ?>
                                <option value="<?php echo $row->id_url ?>"><?php echo "$row->url_ku" ?></option>
                            <?php } ?>
                        </select>
                    </p>
                </div>
            </form>

        </div>
    </tbody>
<?php
}

function wpbc_urls_form_page_handler()
{
    global $wpdb;
    $table_name3 = $wpdb->prefix . 'urlku';


    $message = '';
    $notice = '';

    $default1 = array(
        'id_url'   => '',
        'url_ku'   => '',
    );


    if (isset($_REQUEST['nonce']) && wp_verify_nonce($_REQUEST['nonce'], basename(__FILE__))) {

        $item1 = shortcode_atts($default1, $_REQUEST);

        $item_valid1 = wpbc_validate_link($item1);


        if ($item_valid1 === true) {
            if ($item1['id_url'] == '') {
                $user_count = $wpdb->get_var(
                    "SELECT max(id_url) 
                    FROM {$wpdb->prefix}urlku"
                );
                $id_url_b = $user_count + 1;
                $item1['id_url'] = $id_url_b;

                $result = $wpdb->insert($table_name3, $item1);

                if ($result) {
                    $message = __('Item was successfully saved', 'wpbc');
                } else {
                    $notice = __('There was an error while saving item', 'wpbc');
                }
            } else {

                $result = $wpdb->update($table_name3, $item1, array('id_url' => $item1['id_url']));
                if ($result) {
                    $message = __('Item was successfully Updated', 'wpbc');
                } else {
                    $wpdb->delete($table_name3, array('id_url' => $item1['id_url']));
                    $message = __('Item was successfully delete', 'wpbc');
                    //$notice = __('There was an error while delete item', 'wpbc');
                }
            }
        } else {

            $notice = $item_valid1;
        }
    } else {

        $item1 = $default1;
        if (isset($_REQUEST['id_url'])) {
            $item1 = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table_name3 WHERE id_url = %s", $_REQUEST['id_url']), ARRAY_A);
            if (!$item1) {
                $item1 = $default1;
                $notice = __('Item not found', 'wpbc');
            }
        }
    }

    add_meta_box(
        'links_form_meta_box',
        __('Data URL', 'wpbc'),
        'wpbc_links_form_meta_box_handler',
        'link',
        'normal',
        'default'
    );

?>
    <div class="wrap">
        <div class="icon32 icon32-posts-post" id="icon-edit"><br></div>
        <h2><?php _e('Contact', 'wpbc') ?> <a class="add-new-h2" href="<?php echo get_admin_url(get_current_blog_id(), 'admin.php?page=contacts'); ?>"><?php _e('back to list', 'wpbc') ?></a>
            <a class="add-new-h2" href="<?php echo get_admin_url(get_current_blog_id(), 'admin.php?page=urls_form'); ?>"><?php _e('Add New Grub', 'wpbc') ?> </a>

            <a class="add-new-h2" href="<?php echo get_admin_url(
                                            get_current_blog_id(),
                                            'admin.php?page=contacts_form'
                                        ); ?>">
                <?php _e('Add New Kontak', 'wpbc') ?>
            </a>
        </h2>

        <?php if (!empty($notice)) : ?>
            <div id="notice" class="error">
                <p><?php echo $notice ?></p>
            </div>
        <?php endif; ?>
        <?php if (!empty($message)) : ?>
            <div id="message" class="updated">
                <p><?php echo $message ?></p>
            </div>
        <?php endif; ?>
        <form id="form" method="POST">
            <input type="hidden" name="nonce" value="<?php echo wp_create_nonce(basename(__FILE__)) ?>" />
            <div class="metabox-holder" id="poststuff">
                <div id="post-body">
                    <div id="post-body-content">

                        <?php do_meta_boxes('link', 'normal', $item1); ?>

                    </div>
                </div>
            </div>
        </form>
    </div>
<?php
}


function wpbc_links_form_meta_box_handler($item1)
{

?>
    <tbody>
        <div class="formdatabc">
            <form>


                <div class="form2bc">
                    <p>
                        <label for="url_ku"><?php _e('Nama Grup Baru :', 'wpbc') ?></label>
                        <br>
                        <input id="url_ku" name="url_ku" type="text" placeholder="WA Rotator" value="<?php echo esc_attr($item1['url_ku']) ?>" required>
                        <br><small style="color: #6da38f;">*untuk nambah data klik button Add new grup dan input nama grup,<br> jika edit grup klik pilih dan ubah nama grup, jika hapus klik pilih kemudian save</small>
                    </p>
                </div>

                <div class="form2bc">
                    <p>
                        <input type="submit" value="<?php _e('Save', 'wpbc') ?>" id="submit" class="button-primary" name="submit">
                    </p>
                </div>
            </form>
        </div>
    </tbody>

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/css/bootstrap.min.css" integrity="sha384-B0vP5xmATw1+K9KRQjQERJvTumQW0nPEzvF6L/Z6nronJ3oUOFUFpCjEUQouq2+l" crossorigin="anonymous">
    <table class="table">
        <thead class="thead-light">
            <tr>
                <th scope="col">Id Grup</th>
                <th scope="col">Nama Grup</th>
                <th scope="col">Pilihan</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach (ambil_data_url() as $row) {
            ?>
                <tr>
                    <td><?php echo $row->id_url ?></td>
                    <td><?php echo "$row->url_ku" ?></td>
                    <td> <a href="?page=urls_form&id_url=<?php echo "$row->id_url" ?>&url_ku=<?php echo "$row->url_ku" ?>">Pilih</a>
                    </td>
                </tr>
        </tbody>
    <?php } ?>
    </table>
<?php
}


function ambil_data_url()
{
    global $wpdb;
    $results = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}urlku", OBJECT);
    return $results;
}

function wpbc_ubahs_form_page_handler()
{
    global $wpdb;
    $table_name = $wpdb->prefix . 'datacs';

    $message = '';
    $notice = '';

    $default = array(

        'name'      => '',
        'phone'     => '',
        'pilgrup'   => '',
        'note'      => '',
    );


    if (isset($_REQUEST['nonce']) && wp_verify_nonce($_REQUEST['nonce'], basename(__FILE__))) {

        $item = shortcode_atts($default, $_REQUEST);

        $item_valid = wpbc_validate_ubah($item);

        if ($item_valid === true) {
            $result = $wpdb->update($table_name, $item, array('id' => $item['id']));
            if ($result) {
                $message = __('Item was successfully updated', 'wpbc');
            } else {
                $notice = __('There was an error while updating item', 'wpbc');
            }
        } else {
            $notice = $item_valid;
        }
    } else {

        $item = $default;
        if (isset($_REQUEST['id'])) {
            $item = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table_name WHERE 
            id = %s", $_REQUEST['id']), ARRAY_A);
            if (!$item) {
                $item = $default;
                $notice = __('Item not found', 'wpbc');
            }
        }
    }

    add_meta_box(
        'ubahs_form_meta_box',
        __('Edit data', 'wpbc'),
        'wpbc_ubahs_form_meta_box_handler',
        'ubah',
        'normal',
        'default'
    );

?>
    <div class="wrap">
        <div class="icon32 icon32-posts-post" id="icon-edit"><br></div>
        <h2><?php _e('Contact', 'wpbc') ?> <a class="add-new-h2" href="<?php echo get_admin_url(get_current_blog_id(), 'admin.php?page=contacts'); ?>">
                <?php _e('back to list', 'wpbc') ?></a>
        </h2>

        <?php if (!empty($notice)) : ?>
            <div id="notice" class="error">
                <p><?php echo $notice ?></p>
            </div>
        <?php endif; ?>
        <?php if (!empty($message)) : ?>
            <div id="message" class="updated">
                <p><?php echo $message ?></p>
            </div>

        <?php endif; ?>
        <form id="form" method="POST">
            <input type="hidden" name="nonce" value="<?php echo wp_create_nonce(basename(__FILE__)) ?>" />

            <div class="metabox-holder" id="poststuff">
                <div id="post-body">
                    <div id="post-body-content">

                        <?php do_meta_boxes('ubah', 'normal', $item); ?>
                        <input type="submit" value="<?php _e('Save', 'wpbc') ?>" id="submit" class="button-primary" name="submit">
                    </div>
                </div>
            </div>

        </form>
    </div>

<?php
}

function wpbc_ubahs_form_meta_box_handler($item)
{
?>
    <tbody>

        <div class="formdatabc">

            <form>

                <div class="form2bc">
                    <p>
                        <label for="name"><?php _e('Name :', 'wpbc') ?></label>
                        <br>
                        <input id="name" name="name" type="text" placeholder="Name " value="<?php echo esc_attr($item['name']) ?>" required>
                    </p>
                </div>

                <div class="form2bc">
                    <p>
                        <label for="phone"><?php _e('Phone :', 'wpbc') ?></label>
                        <br>
                        <input id="phone" name="phone" type="tel" value="<?php echo esc_attr($item['phone']) ?>" required>
                    </p>
                </div>

                <div class="form2bc">
                    <p>
                        <label for="note"><?php _e('Note :', 'wpbc') ?></label>
                        <br>
                        <input id="note" name="note" type="tel" value="<?php echo esc_attr($item['note']) ?>" placeholder="Saya ingin memesan ">
                    </p>
                </div>
                <div class="form2bc">
                    <p>
                        <label for="pilgrup"><?php _e('Pilih Grub :', 'wpbc') ?></label>
                        <br>

                        <select id="pilgrup" name="pilgrup">
                            <option value="<?php echo esc_attr($item['pilgrup']) ?>"><?php echo esc_attr($item['pilgrup']) ?></option>
                        </select>
                    </p>
                </div>
            </form>

        </div>
    </tbody>
<?php
}
