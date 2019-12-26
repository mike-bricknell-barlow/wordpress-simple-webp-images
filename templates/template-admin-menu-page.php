<div class="wrap">
    <h1>Simple Webp Images - Settings</h1>

    <form method="post" action="/wp-admin/admin-post.php"> 
        <?php settings_fields( 'simple-webp-images-options-group' ); ?>
        <input type="hidden" name="action" value="update_settings">
        <table class="form-table">
            <tbody>
                <?php 
                
                foreach ( $fields as $field ) {
                    echo '<tr>';
                        switch ( $field['type'] ) {
                            case 'text':
                                ?>

                                <th><label for="<?php echo $field['id'] ?>"><?php echo $field['label'] ?></label></th>
                                <td><input type="text" id="<?php echo $field['id'] ?>" name="<?php echo $field['id'] ?>" placeholder="80" value="<?php echo $field['value'] ?>" /></td>

                                <?php
                                break;
                        }
                    echo '</tr>';
                }
                
                ?>
            </tbody>
        </table>   
        <?php submit_button(); ?> 
    </form>
</div>