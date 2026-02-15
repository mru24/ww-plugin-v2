<?php
/**
 * Handles POST/GET actions for Posts.
 */

if (! class_exists('WW_Post_Controller')) {

  class WW_Post_Controller {
    private $posts;

    public function __construct($posts_service) {
      $this->posts = $posts_service;
    }

    public function handle_save() {
      if (! current_user_can('manage_options')) {
        wp_die('Insufficient permissions.');
      }

      check_admin_referer('ww_plugin_v2_post_nonce');

      $post_id = isset($_POST['post_id']) ? intval($_POST['post_id']) : 0;

      $data = [
        'post_title'        => sanitize_text_field($_POST['post_title']),
        'post_excerpt'      => sanitize_text_field($_POST['post_excerpt']),
        'post_content'      => sanitize_text_field($_POST['post_content']),
        'post_author'       => sanitize_text_field($_POST['post_author']),
        'post_status'       => sanitize_text_field($_POST['post_status']),
      ];

      if ($post_id === 0) {
        $data['created_at'] = current_time('mysql', true);
      }

      $result  = $this->posts->save_post($data, $post_id);
      $message = $result ? 1 : 2;

      wp_redirect(admin_url('admin.php?page=ww-plugin-v2-posts&message=' . $message));
      exit;
    }

    public function handle_delete() {
      if (! current_user_can('manage_options')) {
        wp_die('Insufficient permissions.');
      }

      check_admin_referer('ww_plugin_v2_delete_post');

      $post_id      = isset($_GET['id']) ? intval($_GET['id']) : 0;
      $result       = $this->post->delete_post($post_id);
      $message      = $result ? 3 : 4;

      wp_redirect(admin_url('admin.php?page=ww-plugin-v2-posts&message=' . $message));
      exit;
    }
  }
}