<?php
/**
 * Handles POST/GET actions for Subscriptions.
 */

if (! class_exists('WW_Subscription_Controller')) {

    class WW_Subscription_Controller {

        private $subscriptions;

        public function __construct($subscriptions_service) {
            $this->subscriptions = $subscriptions_service;
        }

        /**
         * Logic for adding or updating a subscription
         */
        public function handle_save() {
          if (! current_user_can('manage_options')) {
              wp_die('Insufficient permissions.');
          }

          check_admin_referer('ww_plugin_v2_subscription_nonce');

          // 1. Determine if this is an Edit or a New entry
          $subscription_id = isset($_POST['subscription_id']) ? intval($_POST['subscription_id']) : 0;

          // 2. Build the base data (fields that always change)
          $data = [
              'plan_name'          => sanitize_text_field($_POST['plan_name']),
              'plan_id'            => sanitize_title($_POST['plan_name']),
              'status'             => sanitize_text_field($_POST['status']),
              'tenure'             => sanitize_text_field($_POST['tenure']),
              'price'              => floatval($_POST['price']),
              'payment_gateway_id' => sanitize_text_field($_POST['payment_gateway_id']),
          ];

          // 3. ONLY add created_at if it's a NEW record (ID is 0)
          if ($subscription_id === 0) {
              $data['created_at'] = current_time('mysql', true);
          }

          $result  = $this->subscriptions->save_subscription($data, $subscription_id);
          $message = $result ? 1 : 2;

          wp_redirect(admin_url('admin.php?page=ww-plugin-v2-subscriptions&message=' . $message));
          exit;
      }

        /**
         * Logic for deleting a subscription
         */
        public function handle_delete() {
            if (! current_user_can('manage_options')) {
                wp_die('Insufficient permissions.');
            }

            check_admin_referer('ww_plugin_v2_delete_subscription');

            $subscription_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
            $result          = $this->subscriptions->delete_subscription($subscription_id);
            $message         = $result ? 3 : 4;

            wp_redirect(admin_url('admin.php?page=ww-plugin-v2-subscriptions&message=' . $message));
            exit;
        }
    }
}