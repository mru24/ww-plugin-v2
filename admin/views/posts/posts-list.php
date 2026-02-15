<?php
/**
 * Admin View: Posts List
 *
 * @var array $posts
 */
?>

<div class="wrap">
  <h1 class="wp-heading-inline">Posts</h1>
  <a href="<?php echo esc_url( admin_url( 'admin.php?page=ww-plugin-v2-edit-post' ) ); ?>" class="page-title-action">Add New</a>
  <hr class="wp-header-end">

  <table class="wp-list-table widefat striped">
    <thead>
      <tr>
        <th>Post Title</th>
        <th>Post Excerpt</th>
        <th>Post Content</th>
        <th>Post Author</th>
        <th>Status</th>
        <th>Created</th>
        <th>Last updated</th>
        <th>Actions</th>
      </tr>
    </thead>
    <tbody>
      <?php if ( ! empty( $posts ) ) : ?>
        <?php foreach ( $posts as $post ) : ?>
          <tr>
            <td><?php echo esc_html( $post['post_title'] ); ?></td>
            <td><?php echo esc_html( $post['post_excerpt'] ); ?></td>
            <td><?php echo esc_html( $post['post_content'] ); ?></td>
            <td><?php echo esc_html( $post['post_author'] ); ?></td>
            <td>
              <span class="tag-<?php echo strtolower(esc_html($post['post_status'])); ?>">
                <?php echo esc_html( ucfirst( $post['post_status'] ) ); ?>
              </span>
            </td>
            <td><?php echo ww_format_datetime(esc_html( $post['created_at'] )); ?></td>
            <td><?php echo ww_format_datetime(esc_html( $post['updated_at'] )); ?></td>
            <td>
              <a href="<?php echo esc_url( admin_url( 'admin.php?page=ww-plugin-v2-edit-post&id=' . absint( $post['id'] ) ) ); ?>">Edit</a> |
              <a href="<?php echo wp_nonce_url( admin_url( 'admin-post.php?action=ww_plugin_v2_delete_post&id=' . absint( $post['id'] ) ), 'ww_plugin_v2_delete_post' ); ?>" onclick="return confirm('Are you sure you want to delete this post?');">Delete</a>
            </td>
          </tr>
        <?php endforeach; ?>
      <?php else : ?>
        <tr><td colspan="12">No posts found.</td></tr>
      <?php endif; ?>
    </tbody>
  </table>
  <style>
    .tag-published { background-color: #d4edda; color: #155724; padding: 4px 8px; border-radius: 4px; font-size: 0.9em; font-weight: bold; }
    .tag-draft { background-color: #fff3cd; color: #856404; padding: 4px 8px; border-radius: 4px; font-size: 0.9em; font-weight: bold; }
    .tag-cancelled { background-color: #8f2424; color: #f8c8c8; padding: 4px 8px; border-radius: 4px; font-size: 0.9em; font-weight: bold; }
    .wp-list-table ul { margin: 0; padding: 0 0 0 15px; }
    .wp-list-table li { margin-bottom: 3px; }
  </style>
</div>