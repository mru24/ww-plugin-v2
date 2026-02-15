<?php
/**
 * Admin View: Add/Edit Post
 *
 * @var array  $data
 * @var string $title
 */
?>

<div class="wrap">
  <h1><?php echo esc_html( $title ); ?></h1>

  <form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">

    <?php wp_nonce_field( 'ww_plugin_v2_post_nonce' ); ?>
    <input type="hidden" name="action" value="ww_plugin_v2_add_post">
    <input type="hidden" name="id" value="<?php echo isset( $data['id'] ) ? absint( $data['id'] ) : 0; ?>">

    <table class="form-table" role="presentation">
      <tr>
        <th scope="row"><label for="post_title">Post Title *</label></th>
        <td><input type="text" name="post_title" id="post_title" class="regular-text" required value="<?php echo isset( $data['post_title'] ) ? esc_attr( $data['post_title'] ) : ''; ?>"></td>
      </tr>
      <tr>
        <th scope="row"><label for="post_title">Post Excerpt *</label></th>
        <td><textarea name="post_excerpt" id="post_excerpt" class="regular-text" required><?php echo isset( $data['post_excerpt'] ) ? esc_attr( $data['post_excerpt'] ) : ''; ?></textarea></td>
      </tr>
      <tr>
        <th scope="row"><label for="post_content">Post Content *</label></th>
        <td><textarea name="post_content" id="post_content" class="regular-text" required><?php echo isset( $data['post_content'] ) ? esc_attr( $data['post_content'] ) : ''; ?></textarea></td>
      </tr>
      <tr>
        <th scope="row"><label for="post_author">Post Author *</label></th>
        <td><input type="text" name="post_author" id="post_author" class="regular-text" required value="<?php echo isset( $data['post_author'] ) ? esc_attr( $data['post_author'] ) : ''; ?>"></td>
      </tr>
      <tr>
        <th scope="row"><label for="status">Status</label></th>
        <td>
          <?php $status = isset( $data['post_status'] ) ? $data['post_status'] : 'active'; ?>
          <select name="post_status" id="post_status">
            <option value="published" <?php selected( $status, 'published' ); ?>>Published</option>
            <option value="draft" <?php selected( $status, 'draft' ); ?>>Draft</option>
            <option value="cancelled" <?php selected( $status, 'cancelled' ); ?>>Cancelled</option>
          </select>
        </td>
      </tr>
    </table>

    <p class="submit">
      <input type="submit" class="button button-primary button-large" value="<?php echo isset( $data['id'] ) ? 'Update Post' : 'Add Post'; ?>">
      <a href="<?php echo esc_url( admin_url( 'admin.php?page=ww-plugin-v2-posts' ) ); ?>" class="button button-secondary button-large">Cancel</a>
    </p>
  </form>
</div>