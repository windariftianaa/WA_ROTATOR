<?php
global $wpdb;
$table_name3 = $wpdb->prefix . 'urlku';
$id_url = $_GET['id_url'];

$sql =  $wpdb->query("DELETE FROM $table_name3 WHERE id_url IN($id_url) ");

if ($sql) {
    header("location:get_admin_url(get_current_blog_id(), 'admin.php?page=urls_form')");
} else {
    echo "Proses hapus gagal";
}