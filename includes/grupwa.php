<?php
function wpbc_links_form_meta_box_handler($item1)
{
    $table1 = new Custom_Table_Examplee_List_Table();
    $table1->prepare_items1();

    $message = '';
    if ('delete' === $table1->current_action()) {
        $message = '<div class="updated below-h2" id="message"><p>'
            . sprintf(__('Items deleted: %d', 'wpbc'), count((array)$_REQUEST['id_url'])) . '</p></div>';
    }

                if (!class_exists('WP_List_Table')) {
                    require_once(ABSPATH . 'wp-admin/includes/class-wp-list-table.php');
                }
                class Custom_Table_Examplee_List_Table extends WP_List_Table
                {
                    function __construct()
                    {
                        global $status, $page;
                        $o = " ";
                        parent::__construct(array(
                            'singular' =>  'url',
                            'plural'   =>  'urls',
                        ));
                        wpbc_page_handler_link($o);
                    }
                    function column_default($item, $column_id_url)
                    {
                        return $item[$column_id_url];
                    }

                    function column_id_url($item)
                    {

                        $actions = array(
                            'edit' => sprintf('<a href="?page=ubahs_form&phone=%s&id=%s&pilgrup=%s">%s</a>', $item['phone'], $item['id'], $item['pilgrup'], __('Edit', 'wpbc')),
                            'delete' => sprintf('<a href="?page=%s&action=delete&id_url=%s">%s</a>', $_REQUEST['page'], $item['id_url'], __('Delete', 'wpbc')),

                        );

                        return sprintf(
                            '%s %s',
                            $item['id_url'],
                            $this->row_actions($actions)
                        );
                    }

                    function get_columns()
                    {
                        $columns = array(

                            'id_url'      => __('id_url', 'wpbc'),
                            'url_ku'     => __('url_ku', 'wpbc'),

                        );
                        return $columns;
                    }

                    function get_sortable_columns()
                    {
                        $sortable_columns = array(
                            'id_url'        => array('id_url', true),
                            'url_ku'      => array('url_ku', true),

                        );
                        return $sortable_columns;
                    }

                    function get_bulk_actions()
                    {
                        $actions = array(
                            'delete' => 'Delete',
                        );
                        return $actions;
                    }


                    function process_bulk_action()
                    {
                        global $wpdb;
                        $table_name = $wpdb->prefix . 'urlku';

                        if ('delete' === $this->current_action()) {
                            $ids = isset($_REQUEST['id_url']) ? $_REQUEST['id_url'] : array();

                            $wpdb->query("DELETE FROM $table_name WHERE id_url IN($ids) ");
                        }
                    }
                    function prepare_items1()
                    {
                        global $wpdb;
                        $table_name2 = $wpdb->prefix . 'urlku';

                        $per_page = 10;

                        $columns = $this->get_columns();
                        $hidden = array();
                        $sortable = $this->get_sortable_columns();

                        $this->_column_headers = array($columns, $hidden, $sortable);

                        $this->process_bulk_action();

                        $total_items = $wpdb->get_var("SELECT COUNT(id) FROM $table_name2");

                        $paged = isset($_REQUEST['paged']) ? max(0, intval($_REQUEST['paged']) - 1) : 0;
                        $orderby = (isset($_REQUEST['orderby']) && in_array($_REQUEST['orderby'], array_keys($this->get_sortable_columns()))) ? $_REQUEST['orderby'] : 'name';
                        $order = (isset($_REQUEST['order']) && in_array($_REQUEST['order'], array('asc', 'desc'))) ? $_REQUEST['order'] : 'asc';

                        $this->items = $wpdb->get_results($wpdb->prepare("SELECT urlku.id_url, urlku.url_ku 
                                            FROM $table_name2 as urlku 
                                            ORDER BY $orderby $order LIMIT %d OFFSET %d", $per_page, $paged), ARRAY_A);

                        $this->set_pagination_args(array(
                            'total_items' => $total_items,
                            'per_page' => $per_page,
                            'total_pages' => ceil($total_items / $per_page)
                        ));
                    }
                }
